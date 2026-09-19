<?php
class FarmerController extends Controller {

    private FarmerModel $farmerModel;
    private $farmer;

    public function __construct() {
        $this->requireRole('farmer');
        $this->farmerModel = new FarmerModel();
        $farmer = $this->farmerModel->findByUserId(Auth::id());
        if (!$farmer) { $this->flash('danger', 'Farmer profile not found.'); $this->redirect('/login'); }
        $this->farmer = $farmer;
    }

    public function dashboard(){
        $farmerId  = $this->farmer['id'];
        $harvests  = $this->farmerModel->getHarvestHistory($farmerId);
        $topCrops  = $this->farmerModel->getTopCrops($farmerId);
        $totalHarv = $this->farmerModel->getTotalHarvest($farmerId);
        $prices    = (new MarketPriceModel())->getLatestPrices();
        $aiService = new AIPredictionService();
        $predictions = $aiService->getLatestPredictions(3);

        $this->view('farmer/dashboard', [
            'farmer'      => $this->farmer,
            'harvests'    => array_slice($harvests, 0, 5),
            'topCrops'    => $topCrops,
            'totalHarv'   => $totalHarv,
            'prices'      => $prices,
            'predictions' => $predictions,
        ]);
    }

    public function profile(){
        $db = Database::getInstance();
        $this->view('farmer/profile', [
            'title'        => 'My Profile',
            'farmer'       => $this->farmer,
            'districts'    => $db->query("SELECT * FROM districts ORDER BY name")->fetchAll(),
            'cooperatives' => $db->query("SELECT id, name FROM cooperatives WHERE status='active'")->fetchAll(),
        ]);
    }

    public function updateProfile(){
        if (!$this->isPost()) { $this->redirect('/farmer/profile'); return; }
        $this->validateCsrf();

        $userModel = new UserModel();
        $userModel->update(Auth::id(), [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name'] ?? ''),
            'phone'      => trim($_POST['phone'] ?? ''),
        ]);

        $this->farmerModel->update($this->farmer['id'], [
            'farm_name'      => trim($_POST['farm_name'] ?? ''),
            'farm_size'      => (float)($_POST['farm_size'] ?? 0),
            'district_id'    => (int)($_POST['district_id'] ?? 0) ?: null,
            'cooperative_id' => (int)($_POST['cooperative_id'] ?? 0) ?: null,
            'soil_type'      => trim($_POST['soil_type'] ?? ''),
            'irrigation'     => isset($_POST['irrigation']) ? 1 : 0,
        ]);

        $this->flash('success', 'Profile updated.');
        $this->redirect('/farmer/profile');
    }

    public function harvests(){
        $model  = new HarvestModel();
        $page   = (int)($_GET['page'] ?? 1);
        $result = $model->getAllWithDetails($page, 15, '', $this->farmer['id']);
        $db = Database::getInstance();
        $statsStmt = $db->prepare(
            "SELECT COUNT(*) AS records, COALESCE(SUM(quantity),0) AS total_qty,
                    COUNT(DISTINCT crop_id) AS crop_count, MAX(harvest_date) AS latest_date
             FROM harvests WHERE farmer_id=?"
        );
        $statsStmt->execute([$this->farmer['id']]);
        $this->view('farmer/harvests', [
            'title'  => 'My Harvests',
            'result' => $result,
            'crops'  => (new CropModel())->getAllWithCategory(),
            'farmer' => $this->farmer,
            'stats'   => $statsStmt->fetch(),
        ]);
    }

    public function storeHarvest(){
        if (!$this->isPost()) { $this->redirect('/farmer/harvests'); return; }
        $this->validateCsrf();
        $data = [
            'farmer_id'      => $this->farmer['id'],
            'crop_id'        => (int)($_POST['crop_id'] ?? 0),
            'cooperative_id' => $this->farmer['cooperative_id'] ?? null,
            'quantity'       => (float)($_POST['quantity'] ?? 0),
            'grade'          => $_POST['grade'] ?? 'A',
            'harvest_date'   => $_POST['harvest_date'] ?? date('Y-m-d'),
            'season'         => trim($_POST['season'] ?? ''),
            'notes'          => trim($_POST['notes'] ?? ''),
        ];
        if (!$data['crop_id'] || !$data['quantity']) { $this->flash('danger', 'Crop and quantity required.'); $this->redirect('/farmer/harvests'); return; }
        (new HarvestModel())->create($data);
        $this->flash('success', 'Harvest recorded.');
        $this->redirect('/farmer/harvests');
    }

    public function marketPrices(){
        $priceModel = new MarketPriceModel();
        $cropModel  = new CropModel();
        $districtId = $this->farmer['district_id'] ?? 0;
        $this->view('farmer/market-prices', [
            'title'   => 'Market Prices',
            'prices'  => $priceModel->getLatestPrices($districtId),
            'trends'  => $priceModel->getPriceTrends(),
            'crops'   => $cropModel->getAllWithCategory(),
        ]);
    }

    public function aiRecommendations(){
        $aiService  = new AIPredictionService();
        $topCrops   = $this->farmerModel->getTopCrops($this->farmer['id']);
        $predictions = [];

        $db = Database::getInstance();
        foreach (array_slice($topCrops, 0, 5) as $crop) {
            $stmt = $db->prepare("SELECT id FROM crops WHERE name=?");
            $stmt->execute([$crop['name']]);
            $cropRow = $stmt->fetch();
            if ($cropRow) {
                $districtId = (int)($this->farmer['district_id'] ?? 0);
                $evidence = $aiService->getPredictionEvidence((int)$cropRow['id'], $districtId);
                if (($evidence['prices'] + $evidence['sales']) === 0 && $districtId) {
                    $districtId = 0;
                    $evidence = $aiService->getPredictionEvidence((int)$cropRow['id'], 0);
                }
                if (($evidence['prices'] + $evidence['sales']) > 0) {
                    $predictions[] = $aiService->predict((int)$cropRow['id'], $districtId, $this->farmer['cooperative_id'] ?? 0);
                }
            }
        }

        $this->view('farmer/ai-recommendations', [
            'title'       => 'AI Recommendations',
            'predictions' => $predictions,
            'farmer'      => $this->farmer,
        ]);
    }

    public function salesHistory(){
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT o.id, o.order_no, o.created_at, o.status, o.total_amount,
                    o.delivery_date, o.delivery_addr,
                    co.name as cooperative_name, b_user.first_name, b_user.last_name,
                    b.company_name
             FROM orders o
             JOIN cooperatives co ON o.cooperative_id=co.id
             JOIN buyers b ON o.buyer_id=b.id
             JOIN users b_user ON b.user_id=b_user.id
             WHERE o.cooperative_id=? AND o.status='completed'
             ORDER BY o.created_at DESC"
        );
        $stmt->execute([$this->farmer['cooperative_id'] ?? 0]);
        $this->view('farmer/sales-history', ['title' => 'Sales History', 'sales' => $stmt->fetchAll()]);
    }

    public function saleDetail($id){
        $db    = Database::getInstance();
        $stmt  = $db->prepare(
            "SELECT o.*, co.name as cooperative_name, b_user.first_name, b_user.last_name,
                    b_user.phone as buyer_phone, b_user.email as buyer_email, b.company_name
             FROM orders o
             JOIN cooperatives co ON o.cooperative_id=co.id
             JOIN buyers b ON o.buyer_id=b.id
             JOIN users b_user ON b.user_id=b_user.id
             WHERE o.id=? AND o.cooperative_id=?"
        );
        $stmt->execute([(int)$id, $this->farmer['cooperative_id'] ?? 0]);
        $order = $stmt->fetch();
        if (!$order) { $this->flash('danger', 'Sale not found.'); $this->redirect('/farmer/sales-history'); return; }
        $items = (new OrderModel())->getItems((int)$id);
        $this->view('farmer/sale-detail', ['title' => 'Sale #' . $order['order_no'], 'order' => $order, 'items' => $items]);
    }
}
