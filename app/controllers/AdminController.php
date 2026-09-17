<?php
class AdminController extends Controller {

    public function __construct() {
        $this->requireRole('admin');
    }

    public function dashboard(): void {
        $db = Database::getInstance();
        $userModel  = new UserModel();
        $orderModel = new OrderModel();
        $priceModel = new MarketPriceModel();
        $aiService  = new AIPredictionService();

        $stats = [
            'farmers'       => $userModel->countByRole('farmer'),
            'cooperatives'  => (int) $db->query("SELECT COUNT(*) FROM cooperatives WHERE status='active'")->fetchColumn(),
            'buyers'        => (int) $db->query("SELECT COUNT(*) FROM buyers WHERE verified=1")->fetchColumn(),
            'crops'         => (int) $db->query("SELECT COUNT(*) FROM crops WHERE status='active'")->fetchColumn(),
            'active_orders' => (int) $db->query("SELECT COUNT(*) FROM orders WHERE status IN ('pending','approved','paid','in_delivery')")->fetchColumn(),
            'total_revenue' => (float) $db->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status='completed'")->fetchColumn(),
            'inventory_val' => (float) $db->query("SELECT COALESCE(SUM(qty_available*asking_price),0) FROM inventories WHERE status='available'")->fetchColumn(),
        ];

        $recentOrders    = $orderModel->getAllWithDetails(1, 5)['data'];
        $revenueByMonth  = $orderModel->getRevenueByMonth();
        $cropProduction  = (new HarvestModel())->getCropProductionStats();
        $latestPrices    = $priceModel->getLatestPrices();
        $aiPredictions   = $aiService->getLatestPredictions(5);
        $recentActivity  = $db->query("SELECT al.*, u.first_name, u.last_name FROM activity_logs al LEFT JOIN users u ON al.user_id=u.id ORDER BY al.created_at DESC LIMIT 10")->fetchAll();

        $this->view('admin/dashboard', array_merge(
            compact('stats','recentOrders','revenueByMonth','cropProduction','latestPrices','aiPredictions','recentActivity'),
            ['title' => 'Admin Dashboard']
        ));
    }

    public function users(): void {
        $userModel = new UserModel();
        $page   = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $role   = $_GET['role'] ?? '';
        $result = $userModel->getAllWithRoles($page, 15, $search, $role);
        $this->view('admin/users/index', ['title' => 'Users', 'result' => $result, 'search' => $search, 'role' => $role, 'roles' => $userModel->getRoles()]);
    }

    public function createUser(): void {
        $userModel = new UserModel();
        $db = Database::getInstance();
        $this->view('admin/users/create', [
            'title'     => 'Create User',
            'roles'     => $userModel->getRoles(),
            'districts' => $db->query("SELECT * FROM districts ORDER BY name")->fetchAll(),
        ]);
    }

    public function storeUser(): void {
        if (!$this->isPost()) { $this->redirect('/admin/users'); return; }
        $this->validateCsrf();

        $data = [
            'role_id'           => (int)($_POST['role_id'] ?? 0),
            'first_name'        => trim($_POST['first_name'] ?? ''),
            'last_name'         => trim($_POST['last_name'] ?? ''),
            'email'             => trim($_POST['email'] ?? ''),
            'phone'             => trim($_POST['phone'] ?? ''),
            'district_id'       => (int)($_POST['district_id'] ?? 0) ?: null,
            'status'            => $_POST['status'] ?? 'active',
            'password'          => password_hash($_POST['password'] ?? 'Admin@1234', PASSWORD_BCRYPT),
            'email_verified_at' => date('Y-m-d H:i:s'),
        ];

        if (!$data['first_name'] || !$data['email'] || !$data['role_id']) {
            $this->flash('danger', 'Required fields missing.');
            $this->redirect('/admin/users/create');
            return;
        }

        $userModel = new UserModel();
        if ($userModel->findByEmail($data['email'])) {
            $this->flash('danger', 'Email already exists.');
            $this->redirect('/admin/users/create');
            return;
        }

        $userId = $userModel->create($data);
        AuditLogger::log('user_created', 'users', $userId, [], $data);
        $this->flash('success', 'User created successfully.');
        $this->redirect('/admin/users');
    }

    public function editUser(string $id): void {
        $userModel = new UserModel();
        $user = $userModel->findWithRole((int)$id);
        if (!$user) { $this->flash('danger', 'User not found.'); $this->redirect('/admin/users'); return; }
        $db = Database::getInstance();
        $this->view('admin/users/edit', [
            'title'     => 'Edit User',
            'user'      => $user,
            'roles'     => $userModel->getRoles(),
            'districts' => $db->query("SELECT * FROM districts ORDER BY name")->fetchAll(),
        ]);
    }

    public function updateUser(string $id): void {
        if (!$this->isPost()) { $this->redirect('/admin/users'); return; }
        $this->validateCsrf();

        $userModel = new UserModel();
        $data = [
            'role_id'     => (int)($_POST['role_id'] ?? 0),
            'first_name'  => trim($_POST['first_name'] ?? ''),
            'last_name'   => trim($_POST['last_name'] ?? ''),
            'phone'       => trim($_POST['phone'] ?? ''),
            'district_id' => (int)($_POST['district_id'] ?? 0) ?: null,
            'status'      => $_POST['status'] ?? 'active',
        ];

        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        }

        $old = $userModel->find((int)$id);
        $userModel->update((int)$id, $data);
        AuditLogger::log('user_updated', 'users', (int)$id, $old, $data);
        $this->flash('success', 'User updated successfully.');
        $this->redirect('/admin/users');
    }

    public function deleteUser(string $id): void {
        if (!$this->isPost()) { $this->redirect('/admin/users'); return; }
        $this->validateCsrf();
        if ((int)$id === Auth::id()) { $this->flash('danger', 'Cannot delete your own account.'); $this->redirect('/admin/users'); return; }
        (new UserModel())->update((int)$id, ['status' => 'inactive']);
        AuditLogger::log('user_deleted', 'users', (int)$id);
        $this->flash('success', 'User deactivated.');
        $this->redirect('/admin/users');
    }

    public function cooperatives(): void {
        $model  = new CooperativeModel();
        $page   = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $result = $model->getAllWithDetails($page, 15, $search);
        $db     = Database::getInstance();
        $cooperativeStats = $db->query(
            "SELECT COUNT(*) AS total,
                    SUM(status='active') AS active,
                    SUM(manager_id IS NULL) AS unassigned,
                    (SELECT COUNT(*) FROM farmers WHERE cooperative_id IS NOT NULL) AS members
             FROM cooperatives"
        )->fetch();
        $this->view('admin/cooperatives/index', [
            'title'     => 'Cooperatives',
            'result'    => $result,
            'search'    => $search,
            'districts' => $db->query("SELECT * FROM districts ORDER BY name")->fetchAll(),
            'managers'  => $db->query("SELECT u.id, u.first_name, u.last_name FROM users u JOIN roles r ON u.role_id=r.id WHERE r.name='cooperative_manager' AND u.status='active'")->fetchAll(),
            'stats'     => $cooperativeStats,
        ]);
    }

    public function cooperativeMembers(string $id): void {
        $coopModel = new CooperativeModel();
        $coop      = $coopModel->findWithDetails((int)$id);
        if (!$coop) { $this->flash('danger', 'Cooperative not found.'); $this->redirect('/admin/cooperatives'); return; }
        $members   = $coopModel->getMembers((int)$id);
        $db        = Database::getInstance();
        $harvests  = $db->prepare(
            "SELECT h.farmer_id, COUNT(*) as harvest_count, SUM(h.quantity) as total_qty
             FROM harvests h
             JOIN farmers f ON h.farmer_id=f.id
             WHERE f.cooperative_id=?
             GROUP BY h.farmer_id"
        );
        $harvests->execute([(int)$id]);
        $harvestStats = [];
        foreach ($harvests->fetchAll() as $row) {
            $harvestStats[$row['farmer_id']] = $row;
        }
        $this->view('admin/cooperatives/members', [
            'title'        => $coop['name'] . ' — Members',
            'coop'         => $coop,
            'members'      => $members,
            'harvestStats' => $harvestStats,
        ]);
    }

    public function storeCooperative(): void {
        if (!$this->isPost()) { $this->redirect('/admin/cooperatives'); return; }
        $this->validateCsrf();
        $data = [
            'name'            => trim($_POST['name'] ?? ''),
            'registration_no' => trim($_POST['registration_no'] ?? '') ?: null,
            'manager_id'      => (int)($_POST['manager_id'] ?? 0) ?: null,
            'district_id'     => (int)($_POST['district_id'] ?? 0) ?: null,
            'phone'           => trim($_POST['phone'] ?? '') ?: null,
            'email'           => trim($_POST['email'] ?? '') ?: null,
            'description'     => trim($_POST['description'] ?? '') ?: null,
            'status'          => 'active',
        ];
        if (!$data['name']) { $this->flash('danger', 'Cooperative name is required.'); $this->redirect('/admin/cooperatives'); return; }
        $id = (new CooperativeModel())->create($data);
        AuditLogger::log('cooperative_created', 'cooperatives', $id, [], $data);
        $this->flash('success', 'Cooperative created successfully.');
        $this->redirect('/admin/cooperatives');
    }

    public function farmers(): void {
        $model  = new FarmerModel();
        $page   = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $result = $model->getAllWithDetails($page, 15, $search);
        $this->view('admin/farmers/index', ['title' => 'Farmers', 'result' => $result, 'search' => $search]);
    }

    public function buyers(): void {
        $model    = new BuyerModel();
        $page     = (int)($_GET['page'] ?? 1);
        $search   = $_GET['search'] ?? '';
        $verified = $_GET['verified'] ?? null;
        $result   = $model->getAllWithDetails($page, 15, $search, $verified);
        $db       = Database::getInstance();
        $pendingUnverified = (int) $db->query("SELECT COUNT(*) FROM buyers WHERE verified=0")->fetchColumn();
        $this->view('admin/buyers/index', [
            'title'            => 'Buyers',
            'result'           => $result,
            'search'           => $search,
            'pendingUnverified'=> $pendingUnverified,
        ]);
    }

    public function verifyBuyer(string $id): void {
        if (!$this->isPost()) { $this->redirect('/admin/buyers'); return; }
        $this->validateCsrf();
        (new BuyerModel())->update((int)$id, ['verified' => 1, 'verified_at' => date('Y-m-d H:i:s')]);
        AuditLogger::log('buyer_verified', 'buyers', (int)$id);
        $this->flash('success', 'Buyer verified.');
        $this->redirect('/admin/buyers');
    }

    public function crops(): void {
        $cropModel = new CropModel();
        $page      = (int)($_GET['page'] ?? 1);
        $search    = $_GET['search'] ?? '';
        $catId     = (int)($_GET['category'] ?? 0);
        $result    = $cropModel->paginated($page, 15, $search, $catId);
        $this->view('admin/crops/index', [
            'title'      => 'Crops',
            'result'     => $result,
            'search'     => $search,
            'categories' => $cropModel->getCategories(),
            'catId'      => $catId,
        ]);
    }

    public function storeCrop(): void {
        if (!$this->isPost()) { $this->redirect('/admin/crops'); return; }
        $this->validateCsrf();
        $data = [
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'name'        => trim($_POST['name'] ?? ''),
            'variety'     => trim($_POST['variety'] ?? ''),
            'unit'        => trim($_POST['unit'] ?? 'kg'),
            'description' => trim($_POST['description'] ?? ''),
            'status'      => 'active',
        ];
        if (!$data['name'] || !$data['category_id']) { $this->flash('danger', 'Name and category required.'); $this->redirect('/admin/crops'); return; }
        $id = (new CropModel())->create($data);
        AuditLogger::log('crop_created', 'crops', $id, [], $data);
        $this->flash('success', 'Crop added.');
        $this->redirect('/admin/crops');
    }

    public function updateCrop(string $id): void {
        if (!$this->isPost()) { $this->redirect('/admin/crops'); return; }
        $this->validateCsrf();
        $data = [
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'name'        => trim($_POST['name'] ?? ''),
            'variety'     => trim($_POST['variety'] ?? ''),
            'unit'        => trim($_POST['unit'] ?? 'kg'),
            'description' => trim($_POST['description'] ?? ''),
            'status'      => $_POST['status'] ?? 'active',
        ];
        (new CropModel())->update((int)$id, $data);
        AuditLogger::log('crop_updated', 'crops', (int)$id, [], $data);
        $this->flash('success', 'Crop updated.');
        $this->redirect('/admin/crops');
    }

    public function marketPrices(): void {
        $model  = new MarketPriceModel();
        $page   = (int)($_GET['page'] ?? 1);
        $cropId = (int)($_GET['crop'] ?? 0);
        $distId = (int)($_GET['district'] ?? 0);
        $result = $model->getAllPaginated($page, 15, $cropId, $distId);
        $db     = Database::getInstance();
        $defaultChartCropId = $cropId ?: (int) $db->query(
            "SELECT crop_id FROM market_prices ORDER BY price_date DESC, id DESC LIMIT 1"
        )->fetchColumn();
        $this->view('admin/market-prices/index', [
            'title'     => 'Market Prices',
            'result'    => $result,
            'crops'     => (new CropModel())->getAllWithCategory(),
            'districts' => $db->query("SELECT * FROM districts ORDER BY name")->fetchAll(),
            'trends'    => $model->getPriceTrends(),
            'cropId'    => $cropId,
            'distId'    => $distId,
            'defaultChartCropId' => $defaultChartCropId,
        ]);
    }

    public function storePrice(): void {
        if (!$this->isPost()) { $this->redirect('/admin/market-prices'); return; }
        $this->validateCsrf();
        $data = [
            'crop_id'     => (int)($_POST['crop_id'] ?? 0),
            'district_id' => (int)($_POST['district_id'] ?? 0) ?: null,
            'price'       => (float)($_POST['price'] ?? 0),
            'price_date'  => $_POST['price_date'] ?? date('Y-m-d'),
            'source'      => trim($_POST['source'] ?? 'Manual Entry'),
            'created_by'  => Auth::id(),
        ];
        if (!$data['crop_id'] || !$data['price']) { $this->flash('danger', 'Crop and price required.'); $this->redirect('/admin/market-prices'); return; }
        (new MarketPriceModel())->create($data);
        NotificationService::sendToRole('farmer', 'price_update', 'Market Price Updated', 'New market prices have been posted.', '/farmer/market-prices');
        $this->flash('success', 'Price recorded.');
        $this->redirect('/admin/market-prices');
    }

    public function orders(): void {
        $model  = new OrderModel();
        $page   = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $result = $model->getAllWithDetails($page, 15, $search, $status);
        $this->view('admin/orders/index', ['title' => 'Orders', 'result' => $result, 'search' => $search, 'status' => $status]);
    }

    public function reports(): void {
        $db = Database::getInstance();
        $this->view('admin/reports/index', [
            'title'        => 'Reports',
            'crops'        => (new CropModel())->getAllWithCategory(),
            'cooperatives' => $db->query("SELECT id, name FROM cooperatives WHERE status='active'")->fetchAll(),
            'districts'    => $db->query("SELECT * FROM districts ORDER BY name")->fetchAll(),
        ]);
    }

    private function renderReport(string $title, array $columns, array $rows, string $format): void {
        if (in_array($format, ['csv', 'excel'], true)) {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . strtolower(str_replace(' ', '_', $title)) . '_' . date('Y-m-d') . '.csv"');
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            foreach ($rows as $row) fputcsv($out, array_values($row));
            fclose($out);
            exit;
        }
        // PDF = printable HTML
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8">';
        echo '<title>' . htmlspecialchars($title) . '</title>';
        echo '<style>body{font-family:Arial,sans-serif;font-size:12px;margin:20px}h2{color:#198754}table{width:100%;border-collapse:collapse;margin-top:15px}th{background:#198754;color:#fff;padding:8px;text-align:left}td{padding:6px 8px;border-bottom:1px solid #ddd}tr:nth-child(even){background:#f9f9f9}.meta{color:#666;font-size:11px;margin-bottom:10px}@media print{.no-print{display:none}}</style>';
        echo '</head><body>';
        echo '<h2>' . htmlspecialchars($title) . '</h2>';
        echo '<div class="meta">Generated: ' . date('d M Y H:i') . ' &nbsp;|&nbsp; Total records: ' . count($rows) . '</div>';
        echo '<button class="no-print" onclick="window.print()" style="background:#198754;color:#fff;border:none;padding:6px 16px;border-radius:4px;cursor:pointer;margin-bottom:10px">&#128438; Print / Save as PDF</button>';
        echo '<table><thead><tr>';
        foreach ($columns as $col) echo '<th>' . htmlspecialchars($col) . '</th>';
        echo '</tr></thead><tbody>';
        foreach ($rows as $row) {
            echo '<tr>';
            foreach (array_values($row) as $val) echo '<td>' . htmlspecialchars((string)($val ?? '-')) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></body></html>';
        exit;
    }

    public function reportFarmers(): void {
        $format = $_GET['format'] ?? 'pdf';
        $db = Database::getInstance();
        $rows = $db->query(
            "SELECT u.first_name, u.last_name, u.email, u.phone, f.farm_name, f.farm_size,
                    d.name as district, co.name as cooperative, f.soil_type,
                    IF(f.irrigation,'Yes','No') as irrigation, u.status, u.created_at
             FROM farmers f JOIN users u ON f.user_id=u.id
             LEFT JOIN districts d ON f.district_id=d.id
             LEFT JOIN cooperatives co ON f.cooperative_id=co.id
             ORDER BY u.first_name"
        )->fetchAll(PDO::FETCH_ASSOC);
        $this->renderReport('Farmers Report', ['First Name','Last Name','Email','Phone','Farm Name','Farm Size (ha)','District','Cooperative','Soil Type','Irrigation','Status','Registered'], $rows, $format);
    }

    public function reportCooperatives(): void {
        $format = $_GET['format'] ?? 'pdf';
        $db = Database::getInstance();
        $rows = $db->query(
            "SELECT co.name, co.registration_no, d.name as district, co.phone, co.email,
                    co.status, COUNT(DISTINCT f.id) as members,
                    COALESCE(SUM(i.qty_available),0) as inventory_kg
             FROM cooperatives co
             LEFT JOIN districts d ON co.district_id=d.id
             LEFT JOIN farmers f ON f.cooperative_id=co.id
             LEFT JOIN inventories i ON i.cooperative_id=co.id AND i.status='available'
             GROUP BY co.id ORDER BY co.name"
        )->fetchAll(PDO::FETCH_ASSOC);
        $this->renderReport('Cooperatives Report', ['Name','Reg No','District','Phone','Email','Status','Members','Inventory (kg)'], $rows, $format);
    }

    public function reportPrices(): void {
        $format = $_GET['format'] ?? 'pdf';
        $rows = (new MarketPriceModel())->getAllPaginated(1, 10000)['data'];
        $out = array_map(fn($r) => [
            $r['crop_name'], $r['price'], $r['unit'] ?? 'kg',
            $r['district_name'] ?? 'National', $r['price_date'], $r['source'] ?? '-'
        ], $rows);
        $this->renderReport('Market Prices Report', ['Crop','Price (RWF)','Unit','District','Date','Source'], $out, $format);
    }

    public function reportOrders(): void {
        $format = $_GET['format'] ?? 'pdf';
        $rows = (new OrderModel())->getAllWithDetails(1, 10000)['data'];
        $out = array_map(fn($r) => [
            $r['order_no'],
            trim(($r['company_name'] ?? '') ?: ($r['first_name'] . ' ' . $r['last_name'])),
            $r['cooperative_name'] ?? '-',
            $r['total_amount'], $r['status'], $r['created_at']
        ], $rows);
        $this->renderReport('Orders Report', ['Order No','Buyer','Cooperative','Amount (RWF)','Status','Date'], $out, $format);
    }

    public function reportInventory(): void {
        $format = $_GET['format'] ?? 'pdf';
        $rows = (new InventoryModel())->getAllWithDetails(1, 10000)['data'];
        $out = array_map(fn($r) => [
            $r['crop_name'], $r['cooperative_name'], $r['warehouse_name'] ?? '-',
            $r['qty_available'], $r['qty_reserved'], $r['qty_sold'],
            $r['grade'], $r['asking_price'], $r['status']
        ], $rows);
        $this->renderReport('Inventory Report', ['Crop','Cooperative','Warehouse','Available','Reserved','Sold','Grade','Price (RWF)','Status'], $out, $format);
    }

    public function reportAi(): void {
        $format = $_GET['format'] ?? 'pdf';
        $rows = (new AIPredictionService())->getLatestPredictions(10000);
        $out = array_map(fn($r) => [
            $r['crop_name'], $r['district_name'] ?? '-', $r['predicted_demand'],
            $r['predicted_price'], $r['confidence_score'] . '%',
            $r['best_selling_period'], $r['prediction_date']
        ], $rows);
        $this->renderReport('AI Predictions Report', ['Crop','District','Demand','Predicted Price (RWF)','Confidence','Best Period','Date'], $out, $format);
    }

    public function auditLogs(): void {
        $db     = Database::getInstance();
        $page   = (int)($_GET['page'] ?? 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $total  = (int) $db->query("SELECT COUNT(*) FROM audit_logs")->fetchColumn();
        $logs   = $db->query("SELECT al.*, u.first_name, u.last_name FROM audit_logs al LEFT JOIN users u ON al.user_id=u.id ORDER BY al.created_at DESC LIMIT $perPage OFFSET $offset")->fetchAll();
        $this->view('admin/audit-logs', [
            'title'  => 'Audit Logs',
            'logs'   => $logs,
            'total'  => $total,
            'page'   => $page,
            'perPage'=> $perPage,
            'last'   => max(1,(int)ceil($total/$perPage)),
        ]);
    }

    public function settings(): void {
        $db = Database::getInstance();
        $settings = [];
        foreach ($db->query("SELECT key_name, value FROM settings")->fetchAll() as $s) {
            $settings[$s['key_name']] = $s['value'];
        }
        $this->view('admin/settings', ['title' => 'Settings', 'settings' => $settings]);
    }

    public function saveSettings(): void {
        if (!$this->isPost()) { $this->redirect('/admin/settings'); return; }
        $this->validateCsrf();
        $db = Database::getInstance();
        foreach ($_POST as $key => $value) {
            if ($key === '_token') continue;
            $db->prepare("UPDATE settings SET value=? WHERE key_name=?")->execute([$value, $key]);
        }
        AuditLogger::log('settings_updated', 'settings');
        $this->flash('success', 'Settings saved.');
        $this->redirect('/admin/settings');
    }

    public function inventory(): void {
        $model  = new InventoryModel();
        $page   = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $cropId = (int)($_GET['crop'] ?? 0);
        $coopId = (int)($_GET['cooperative'] ?? 0);
        $result = $model->getAllWithDetails($page, 15, $search, $coopId, $cropId);
        $stats  = $model->getSummaryStats();
        $db     = Database::getInstance();
        $this->view('admin/inventory/index', [
            'title'        => 'Inventory',
            'result'       => $result,
            'stats'        => $stats,
            'search'       => $search,
            'cropId'       => $cropId,
            'coopId'       => $coopId,
            'crops'        => (new CropModel())->getAllWithCategory(),
            'cooperatives' => $db->query("SELECT id, name FROM cooperatives WHERE status='active' ORDER BY name")->fetchAll(),
        ]);
    }

    public function aiPredictions(): void {
        $aiService = new AIPredictionService();
        $db        = Database::getInstance();
        $crops     = (new CropModel())->getAllWithCategory();
        $predictionEvidence = [];
        foreach ($crops as $crop) {
            $predictionEvidence[$crop['id']] = $aiService->getPredictionEvidence((int)$crop['id']);
        }
        $this->view('admin/ai-predictions', [
            'title'       => 'AI Predictions',
            'predictions' => $aiService->getLatestPredictions(20),
            'summary'     => $aiService->getPredictionSummary(),
            'crops'       => $crops,
            'districts'   => $db->query("SELECT * FROM districts ORDER BY name")->fetchAll(),
            'predictionEvidence' => $predictionEvidence,
        ]);
    }

    public function runPrediction(): void {
        if (!$this->isPost()) { $this->redirect('/admin/ai-predictions'); return; }
        $this->validateCsrf();
        $cropId     = (int)($_POST['crop_id'] ?? 0);
        $districtId = (int)($_POST['district_id'] ?? 0);
        if (!$cropId || !array_key_exists('district_id', $_POST)) { $this->flash('danger', 'Select a crop and market area.'); $this->redirect('/admin/ai-predictions'); return; }
        $service = new AIPredictionService();
        $evidence = $service->getPredictionEvidence($cropId, $districtId);
        if (($evidence['prices'] + $evidence['sales']) === 0) {
            $this->flash('danger', 'Prediction unavailable: this crop has no market-price or completed-sale evidence for the selected area. Add verified market data first.');
            $this->redirect('/admin/ai-predictions');
            return;
        }
        $result = $service->predict($cropId, $districtId);
        AuditLogger::log('ai_prediction_run', 'ai', $cropId, [], $result);
        $this->flash('success', 'Prediction generated successfully.');
        $this->redirect('/admin/ai-predictions');
    }

    // ── WAREHOUSES ────────────────────────────────────────────────────────────
    public function warehouses(): void {
        $model  = new WarehouseModel();
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $this->view('admin/warehouses/index', [
            'title'        => 'Warehouses',
            'warehouses'   => $model->getAllWithDetails($search, $status),
            'stats'        => $model->getStats(),
            'search'       => $search,
            'status'       => $status,
            'cooperatives' => Database::getInstance()->query("SELECT id,name FROM cooperatives WHERE status='active' ORDER BY name")->fetchAll(),
            'districts'    => Database::getInstance()->query("SELECT id,name FROM districts ORDER BY name")->fetchAll(),
        ]);
    }

    public function createWarehouse(): void {
        $this->view('admin/warehouses/create', [
            'title'        => 'Add Warehouse',
            'cooperatives' => Database::getInstance()->query("SELECT id,name FROM cooperatives WHERE status='active' ORDER BY name")->fetchAll(),
            'districts'    => Database::getInstance()->query("SELECT id,name FROM districts ORDER BY name")->fetchAll(),
        ]);
    }

    public function storeWarehouse(): void {
        if (!$this->isPost()) { $this->redirect('/admin/warehouses'); return; }
        $this->validateCsrf();
        $id = (new WarehouseModel())->create($_POST);
        AuditLogger::log('warehouse_created', 'warehouse', $id, [], $_POST);
        $this->flash('success', 'Warehouse created successfully.');
        $this->redirect('/admin/warehouses');
    }

    public function editWarehouse(string $id): void {
        $model = new WarehouseModel();
        $warehouse = $model->getById((int)$id);
        if (!$warehouse) { $this->redirect('/admin/warehouses'); return; }
        $this->view('admin/warehouses/edit', [
            'title'        => 'Edit Warehouse',
            'warehouse'    => $warehouse,
            'cooperatives' => Database::getInstance()->query("SELECT id,name FROM cooperatives WHERE status='active' ORDER BY name")->fetchAll(),
            'districts'    => Database::getInstance()->query("SELECT id,name FROM districts ORDER BY name")->fetchAll(),
        ]);
    }

    public function updateWarehouse(string $id): void {
        if (!$this->isPost()) { $this->redirect('/admin/warehouses'); return; }
        $this->validateCsrf();
        (new WarehouseModel())->update((int)$id, $_POST);
        AuditLogger::log('warehouse_updated', 'warehouse', (int)$id, [], $_POST);
        $this->flash('success', 'Warehouse updated successfully.');
        $this->redirect('/admin/warehouses');
    }

    public function deleteWarehouse(string $id): void {
        if (!$this->isPost()) { $this->redirect('/admin/warehouses'); return; }
        $this->validateCsrf();
        (new WarehouseModel())->delete((int)$id);
        AuditLogger::log('warehouse_deleted', 'warehouse', (int)$id);
        $this->flash('success', 'Warehouse deleted.');
        $this->redirect('/admin/warehouses');
    }

    public function warehouseDetail(string $id): void {
        $model = new WarehouseModel();
        $warehouse = $model->getById((int)$id);
        if (!$warehouse) { $this->redirect('/admin/warehouses'); return; }
        $this->view('admin/warehouses/detail', [
            'title'     => $warehouse['name'],
            'warehouse' => $warehouse,
            'inventory' => $model->getInventory((int)$id),
        ]);
    }
}
