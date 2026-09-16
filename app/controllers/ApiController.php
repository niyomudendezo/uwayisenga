<?php
class ApiController extends Controller {

    public function notifications(): void {
        $this->requireAuth();
        $notifications = NotificationService::getRecent(Auth::id(), 10);
        $unread        = NotificationService::unreadCount(Auth::id());
        $this->json(['notifications' => $notifications, 'unread' => $unread]);
    }

    public function markRead(): void {
        $this->requireAuth();
        $this->validateCsrf();
        NotificationService::markRead(Auth::id());
        $this->json(['success' => true]);
    }

    public function districts(): void {
        $stmt = Database::getInstance()->query("SELECT id, name FROM districts ORDER BY name");
        $this->json($stmt->fetchAll());
    }

    public function sectors(string $districtId): void {
        $stmt = Database::getInstance()->prepare("SELECT id, name FROM sectors WHERE district_id=? ORDER BY name");
        $stmt->execute([(int)$districtId]);
        $this->json($stmt->fetchAll());
    }

    public function cells(string $sectorId): void {
        $stmt = Database::getInstance()->prepare("SELECT id, name FROM cells WHERE sector_id=? ORDER BY name");
        $stmt->execute([(int) $sectorId]);
        $this->json($stmt->fetchAll());
    }

    public function villages(string $cellId): void {
        $stmt = Database::getInstance()->prepare("SELECT id, name FROM villages WHERE cell_id=? ORDER BY name");
        $stmt->execute([(int) $cellId]);
        $this->json($stmt->fetchAll());
    }

    public function crops(): void {
        $this->json((new CropModel())->getAllWithCategory());
    }

    public function priceChart(string $cropId): void {
        $months     = (int) ($_GET['months'] ?? 12);
        $districtId = (int) ($_GET['district'] ?? 0);
        $history    = (new MarketPriceModel())->getPriceHistory((int)$cropId, $districtId, $months);
        $this->json([
            'labels' => array_column($history, 'price_date'),
            'prices' => array_column($history, 'price'),
        ]);
    }
}
