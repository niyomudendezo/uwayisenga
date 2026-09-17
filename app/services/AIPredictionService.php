<?php
/**
 * AI Prediction Service
 * Uses statistical analysis on historical market data to generate predictions.
 * Can optionally call a Python FastAPI microservice if configured.
 */
class AIPredictionService {
    private PDO $db;
    private string $aiServiceUrl;
    private bool $useExternalAI;

    public function __construct() {
        $this->db            = Database::getInstance();
        $this->aiServiceUrl  = $this->getSetting('ai_service_url', 'http://localhost:8000');
        $this->useExternalAI = (bool) $this->getSetting('ai_enabled', '0');
    }

    private function getSetting(string $key, string $default = ''): string {
        $stmt = $this->db->prepare("SELECT value FROM settings WHERE key_name=?");
        $stmt->execute([$key]);
        return $stmt->fetchColumn() ?: $default;
    }

    /**
     * Generate prediction for a crop in a district
     */
    public function predict(int $cropId, int $districtId, int $cooperativeId = 0): array {
        if ($this->useExternalAI) {
            $result = $this->callExternalAI($cropId, $districtId);
            if ($result) return $result;
        }
        return $this->localPredict($cropId, $districtId, $cooperativeId);
    }

    /**
     * Local statistical prediction engine
     */
    private function localPredict(int $cropId, int $districtId, int $cooperativeId): array {
        $prices    = $this->getHistoricalPrices($cropId, $districtId);
        $sales     = $this->getSalesHistory($cropId, $cooperativeId);
        $inventory = $this->getInventoryLevel($cropId, $cooperativeId);
        $bestBuyer = $this->findBestBuyer($cropId, $districtId);

        // Price trend analysis using linear regression
        $predictedPrice = $this->linearRegression($prices);
        $avgPrice       = count($prices) ? array_sum(array_column($prices, 'price')) / count($prices) : 0;
        $latestPrice    = $prices[0]['price'] ?? $avgPrice;

        // Demand classification
        $demand = $this->classifyDemand($prices, $sales);

        // Confidence score based on data availability
        $confidence = $this->calculateConfidence(count($prices), count($sales));

        // Best selling period
        $bestPeriod = $this->getBestSellingPeriod($prices);

        // Suggested quantity
        $suggestedQty = $this->suggestQuantity($sales, $inventory);

        // Revenue estimate
        $estimatedRevenue = $suggestedQty * $predictedPrice;

        // Recommendation text
        $recommendation = $this->buildRecommendation($demand, $predictedPrice, $latestPrice, $bestPeriod, $bestBuyer);

        $cropName = $this->db->prepare("SELECT name FROM crops WHERE id=?");
        $cropName->execute([$cropId]);
        $cropName = $cropName->fetchColumn() ?: '';

        $result = [
            'crop_id'             => $cropId,
            'crop_name'           => $cropName,
            'district_id'         => $districtId ?: null,
            'cooperative_id'      => $cooperativeId ?: null,
            'predicted_demand'    => $demand,
            'predicted_price'     => round($predictedPrice, 2),
            'best_buyer_id'       => $bestBuyer['id'] ?? null,
            'best_buyer_name'     => $bestBuyer['company_name'] ?? ($bestBuyer['name'] ?? 'N/A'),
            'best_selling_period' => $bestPeriod,
            'estimated_revenue'   => round($estimatedRevenue, 2),
            'confidence_score'    => $confidence,
            'suggested_qty'       => round($suggestedQty, 2),
            'recommendation_text' => $recommendation,
            'model_version'       => '1.1',
            'prediction_date'     => date('Y-m-d'),
        ];

        $this->savePrediction($result);
        return $result;
    }

    private function getHistoricalPrices(int $cropId, int $districtId): array {
        if ($districtId) {
            $stmt = $this->db->prepare(
                "SELECT AVG(price) AS price, price_date FROM market_prices
                 WHERE crop_id=? AND (district_id=? OR district_id IS NULL)
                 GROUP BY price_date ORDER BY price_date DESC LIMIT 24"
            );
            $stmt->execute([$cropId, $districtId]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT AVG(price) AS price, price_date FROM market_prices
                 WHERE crop_id=?
                 GROUP BY price_date ORDER BY price_date DESC LIMIT 24"
            );
            $stmt->execute([$cropId]);
        }
        $observations = $stmt->fetchAll();

        $salesSql = "SELECT oi.unit_price AS price, DATE(o.created_at) AS price_date
                     FROM order_items oi
                     JOIN orders o ON o.id=oi.order_id
                     JOIN cooperatives co ON co.id=o.cooperative_id
                     WHERE oi.crop_id=? AND o.status='completed'";
        $salesParams = [$cropId];
        if ($districtId) {
            $salesSql .= " AND co.district_id=?";
            $salesParams[] = $districtId;
        }
        $salesSql .= " ORDER BY o.created_at DESC LIMIT 24";
        $salesStmt = $this->db->prepare($salesSql);
        $salesStmt->execute($salesParams);
        $observations = array_merge($observations, $salesStmt->fetchAll());

        $daily = [];
        foreach ($observations as $observation) {
            $daily[$observation['price_date']][] = (float)$observation['price'];
        }
        $combined = [];
        foreach ($daily as $date => $values) {
            $combined[] = ['price_date' => $date, 'price' => array_sum($values) / count($values)];
        }
        usort($combined, static fn($a, $b) => strcmp($b['price_date'], $a['price_date']));
        return array_slice($combined, 0, 24);
    }

    public function getPredictionEvidence(int $cropId, int $districtId = 0): array {
        $marketSql = "SELECT COUNT(*) FROM market_prices WHERE crop_id=?";
        $marketParams = [$cropId];
        if ($districtId) {
            $marketSql .= " AND (district_id=? OR district_id IS NULL)";
            $marketParams[] = $districtId;
        }
        $stmt = $this->db->prepare($marketSql);
        $stmt->execute($marketParams);

        $salesSql = "SELECT COUNT(*) FROM order_items oi JOIN orders o ON o.id=oi.order_id
                     JOIN cooperatives co ON co.id=o.cooperative_id
                     WHERE oi.crop_id=? AND o.status='completed'";
        $salesParams = [$cropId];
        if ($districtId) {
            $salesSql .= " AND co.district_id=?";
            $salesParams[] = $districtId;
        }
        $salesStmt = $this->db->prepare($salesSql);
        $salesStmt->execute($salesParams);
        return ['prices' => (int)$stmt->fetchColumn(), 'sales' => (int)$salesStmt->fetchColumn()];
    }

    private function getSalesHistory(int $cropId, int $cooperativeId): array {
        $sql = "SELECT oi.quantity, oi.unit_price, o.created_at
                FROM order_items oi
                JOIN orders o ON oi.order_id=o.id
                WHERE oi.crop_id=? AND o.status='completed'";
        $params = [$cropId];
        if ($cooperativeId) { $sql .= " AND o.cooperative_id=?"; $params[] = $cooperativeId; }
        $sql .= " ORDER BY o.created_at DESC LIMIT 20";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function getInventoryLevel(int $cropId, int $cooperativeId): float {
        $sql = "SELECT COALESCE(SUM(qty_available),0) FROM inventories WHERE crop_id=? AND status='available'";
        $params = [$cropId];
        if ($cooperativeId) { $sql .= " AND cooperative_id=?"; $params[] = $cooperativeId; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (float) $stmt->fetchColumn();
    }

    private function findBestBuyer(int $cropId, int $districtId): array {
        // Find buyer with highest offer or most purchase history
        $stmt = $this->db->prepare(
            "SELECT b.id, b.company_name, u.first_name, u.last_name,
                    COALESCE(bo.price, 0) as offer_price,
                    COUNT(oi.id) as order_count
             FROM buyers b
             JOIN users u ON b.user_id=u.id
             LEFT JOIN buyer_offers bo ON bo.buyer_id=b.id AND bo.crop_id=? AND bo.status='active'
             LEFT JOIN orders o ON o.buyer_id=b.id AND o.status='completed'
             LEFT JOIN order_items oi ON oi.order_id=o.id AND oi.crop_id=?
             WHERE b.verified=1
             GROUP BY b.id
             ORDER BY offer_price DESC, order_count DESC
             LIMIT 1"
        );
        $stmt->execute([$cropId, $cropId]);
        $buyer = $stmt->fetch();
        if ($buyer && empty($buyer['company_name'])) {
            $buyer['company_name'] = $buyer['first_name'] . ' ' . $buyer['last_name'];
        }
        return $buyer ?: [];
    }

    /**
     * Simple linear regression on price time series
     */
    private function linearRegression(array $prices): float {
        if (empty($prices)) return 0;
        if (count($prices) === 1) return (float) $prices[0]['price'];

        $n = count($prices);
        $prices = array_reverse($prices); // oldest first
        $x = range(1, $n);
        $y = array_column($prices, 'price');

        $sumX  = array_sum($x);
        $sumY  = array_sum($y);
        $sumXY = 0; $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumX2 += $x[$i] * $x[$i];
        }

        $slope     = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        // Predict next period
        return max(0, $intercept + $slope * ($n + 1));
    }

    private function classifyDemand(array $prices, array $sales): string {
        if (empty($prices)) return 'Medium';

        $recentPrices = array_slice($prices, 0, 3);
        $olderPrices  = array_slice($prices, 3, 3);

        if (empty($olderPrices)) return 'Medium';

        $recentAvg = array_sum(array_column($recentPrices, 'price')) / count($recentPrices);
        $olderAvg  = array_sum(array_column($olderPrices, 'price')) / count($olderPrices);

        $change = $olderAvg > 0 ? (($recentAvg - $olderAvg) / $olderAvg) * 100 : 0;

        if ($change > 5)  return 'High';
        if ($change < -5) return 'Low';
        return 'Medium';
    }

    private function calculateConfidence(int $priceCount, int $salesCount): float {
        if ($priceCount === 0 && $salesCount === 0) return 0.0;
        $priceCoverage = min(1, $priceCount / 24);
        $salesCoverage = min(1, $salesCount / 20);
        return round(min(95, ($priceCoverage * 70) + ($salesCoverage * 25)), 1);
    }

    private function getBestSellingPeriod(array $prices): string {
        if (count($prices) < 2) return 'Immediate';

        $latest = (float) ($prices[0]['price'] ?? 0);
        $prev   = (float) ($prices[1]['price'] ?? 0);

        if ($latest > $prev) return 'Sell now — prices rising';
        if ($latest < $prev) return 'Wait 7-14 days — prices may recover';
        return 'Next 3-7 days';
    }

    private function suggestQuantity(array $sales, float $inventory): float {
        if (empty($sales)) return $inventory * 0.5;
        $avgSale = array_sum(array_column($sales, 'quantity')) / count($sales);
        return min($inventory, $avgSale * 1.2);
    }

    private function buildRecommendation(string $demand, float $predicted, float $latest, string $period, array $buyer): string {
        $buyerName = $buyer['company_name'] ?? 'local market';
        $priceDiff = $predicted - $latest;
        $direction = $priceDiff >= 0 ? 'increase' : 'decrease';

        $text = "Demand forecast: {$demand}. ";
        $text .= "Predicted price: " . number_format($predicted, 0) . " RWF/kg ";
        $text .= "(expected to {$direction} by " . abs(round($priceDiff, 0)) . " RWF). ";
        $text .= "Recommended action: {$period}.";

        return $text;
    }

    private function savePrediction(array $data): void {
        try {
            $this->db->prepare(
                "INSERT INTO ai_predictions
                 (crop_id, district_id, cooperative_id, predicted_demand, predicted_price,
                  best_buyer_id, best_selling_period, estimated_revenue, confidence_score,
                  suggested_qty, recommendation_text, model_version, prediction_date)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)"
            )->execute([
                $data['crop_id'], $data['district_id'], $data['cooperative_id'],
                $data['predicted_demand'], $data['predicted_price'], $data['best_buyer_id'],
                $data['best_selling_period'], $data['estimated_revenue'], $data['confidence_score'],
                $data['suggested_qty'], $data['recommendation_text'], $data['model_version'],
                $data['prediction_date'],
            ]);
        } catch (Exception $e) {}
    }

    private function callExternalAI(int $cropId, int $districtId): ?array {
        $url = $this->aiServiceUrl . '/predict';
        $payload = json_encode(['crop_id' => $cropId, 'district_id' => $districtId]);

        $ctx = stream_context_create(['http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\nContent-Length: " . strlen($payload),
            'content' => $payload,
            'timeout' => 5,
        ]]);

        $response = @file_get_contents($url, false, $ctx);
        return $response ? json_decode($response, true) : null;
    }

    public function getLatestPredictions(int $limit = 10): array {
        $stmt = $this->db->prepare(
            "SELECT ap.*, c.name as crop_name, c.unit, d.name as district_name,
                    co.name as cooperative_name,
                    COALESCE(NULLIF(b.company_name, ''), CONCAT(u.first_name, ' ', u.last_name)) AS best_buyer_name
             FROM ai_predictions ap
             JOIN crops c ON ap.crop_id=c.id
             LEFT JOIN districts d ON ap.district_id=d.id
             LEFT JOIN cooperatives co ON ap.cooperative_id=co.id
             LEFT JOIN buyers b ON ap.best_buyer_id=b.id
             LEFT JOIN users u ON b.user_id=u.id
             ORDER BY ap.created_at DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getPredictionSummary(): array {
        $stmt = $this->db->query(
            "SELECT predicted_demand, COUNT(*) as count
             FROM ai_predictions
             WHERE prediction_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY predicted_demand"
        );
        return $stmt->fetchAll();
    }
}
