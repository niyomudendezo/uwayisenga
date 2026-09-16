<?php
class MarketPriceModel extends Model {
    protected string $table = 'market_prices';

    public function getLatestPrices(int $districtId = 0, int $cropId = 0): array {
        $where = $districtId ? "AND mp.district_id=$districtId" : '';
        $cropWhere = $cropId ? "AND mp.crop_id=$cropId" : '';
        return $this->rawQuery(
            "SELECT mp.*, c.name as crop_name, c.unit, d.name as district_name
             FROM market_prices mp
             JOIN crops c ON mp.crop_id=c.id
             LEFT JOIN districts d ON mp.district_id=d.id
             WHERE mp.price_date = (SELECT MAX(price_date) FROM market_prices mp2 WHERE mp2.crop_id=mp.crop_id $where)
             $where $cropWhere ORDER BY c.name"
        );
    }

    public function getPriceHistory(int $cropId, int $districtId = 0, int $months = 12): array {
        $params = [$cropId];
        if ($districtId) {
            $where = "WHERE mp.crop_id=? AND mp.district_id=? AND mp.price_date >= DATE_SUB((SELECT MAX(price_date) FROM market_prices WHERE crop_id=?), INTERVAL ? MONTH)";
            $params[] = $districtId;
            $params[] = $cropId;
        } else {
            $where = "WHERE mp.crop_id=? AND mp.price_date >= DATE_SUB((SELECT MAX(price_date) FROM market_prices WHERE crop_id=?), INTERVAL ? MONTH)";
            $params[] = $cropId;
        }
        $params[] = $months;
        return $this->rawQuery(
            "SELECT mp.price_date, ROUND(AVG(mp.price), 2) as price
             FROM market_prices mp
             $where
             GROUP BY mp.price_date
             ORDER BY mp.price_date ASC", $params
        );
    }

    public function getPriceTrends(int $cropId = 0): array {
        $cropWhere = $cropId ? "AND c.id=$cropId" : '';
        return $this->rawQuery(
            "SELECT c.name as crop_name, c.unit,
                    MAX(mp.price) as max_price, MIN(mp.price) as min_price,
                    AVG(mp.price) as avg_price,
                    (SELECT price FROM market_prices mp2 WHERE mp2.crop_id=c.id ORDER BY price_date DESC LIMIT 1) as latest_price
             FROM market_prices mp JOIN crops c ON mp.crop_id=c.id
             WHERE mp.price_date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) $cropWhere
             GROUP BY c.id ORDER BY c.name"
        );
    }

    public function getAllPaginated(int $page = 1, int $perPage = 15, int $cropId = 0, int $districtId = 0): array {
        $where = []; $params = [];
        if ($cropId)    { $where[] = "mp.crop_id=?"; $params[] = $cropId; }
        if ($districtId){ $where[] = "mp.district_id=?"; $params[] = $districtId; }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM market_prices mp $whereStr");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare(
            "SELECT mp.*, c.name as crop_name, c.unit, d.name as district_name
             FROM market_prices mp JOIN crops c ON mp.crop_id=c.id
             LEFT JOIN districts d ON mp.district_id=d.id
             $whereStr ORDER BY mp.price_date DESC LIMIT $perPage OFFSET $offset"
        );
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total, 'per_page' => $perPage, 'current_page' => $page, 'last_page' => max(1,(int)ceil($total/$perPage))];
    }
}
