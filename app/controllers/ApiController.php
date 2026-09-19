<?php
class ApiController extends Controller {

    public function notifications(){
        $this->requireAuth();
        $notifications = NotificationService::getRecent(Auth::id(), 10);
        $unread        = NotificationService::unreadCount(Auth::id());
        $this->json(['notifications' => $notifications, 'unread' => $unread]);
    }

    public function markRead(){
        $this->requireAuth();
        $this->validateCsrf();
        NotificationService::markRead(Auth::id());
        $this->json(['success' => true]);
    }

    public function districts(){
        $stmt = Database::getInstance()->query("SELECT id, name FROM districts ORDER BY name");
        $this->json($stmt->fetchAll());
    }

    public function sectors($districtId){
        $stmt = Database::getInstance()->prepare("SELECT id, name FROM sectors WHERE district_id=? ORDER BY name");
        $stmt->execute([(int)$districtId]);
        $this->json($stmt->fetchAll());
    }

    public function cells($sectorId){
        $stmt = Database::getInstance()->prepare("SELECT id, name FROM cells WHERE sector_id=? ORDER BY name");
        $stmt->execute([(int) $sectorId]);
        $this->json($stmt->fetchAll());
    }

    public function villages($cellId){
        $stmt = Database::getInstance()->prepare("SELECT id, name FROM villages WHERE cell_id=? ORDER BY name");
        $stmt->execute([(int) $cellId]);
        $this->json($stmt->fetchAll());
    }

    public function crops(){
        $this->json((new CropModel())->getAllWithCategory());
    }

    public function priceChart($cropId){
        $months     = (int) ($_GET['months'] ?? 12);
        $districtId = (int) ($_GET['district'] ?? 0);
        $model      = new MarketPriceModel();
        if ((int)$cropId === 0) {
            $history = $model->getAllCropPriceHistory($districtId, $months);
            $labels = array_values(array_unique(array_column($history, 'price_date')));
            $series = [];
            foreach ($history as $row) {
                $series[$row['crop_id']]['label'] = $row['crop_name'];
                $series[$row['crop_id']]['unit'] = $row['unit'];
                $series[$row['crop_id']]['values'][$row['price_date']] = (float)$row['price'];
            }
            $datasets = [];
            foreach ($series as $cropSeries) {
                $datasets[] = [
                    'label' => $cropSeries['label'],
                    'unit' => $cropSeries['unit'],
                    'data' => array_map(
                        static fn($date) => $cropSeries['values'][$date] ?? null,
                        $labels
                    ),
                ];
            }
            $this->json(['labels' => $labels, 'datasets' => $datasets]);
            return;
        }
        $history = $model->getPriceHistory((int)$cropId, $districtId, $months);
        $this->json([
            'labels' => array_column($history, 'price_date'),
            'prices' => array_column($history, 'price'),
        ]);
    }
}
