<?php
class CooperativeController extends Controller {

    private CooperativeModel $coopModel;
    private int $coopId;

    public function __construct() {
        $this->requireRole('cooperative_manager');
        $this->coopModel = new CooperativeModel();
        $coop = $this->coopModel->getByManagerId(Auth::id());
        if (!$coop) { $this->flash('danger', 'No cooperative assigned to your account.'); $this->redirect('/login'); }
        $this->coopId = $coop['id'];
    }

    public function dashboard(): void {
        $invModel   = new InventoryModel();
        $orderModel = new OrderModel();
        $aiService  = new AIPredictionService();

        $coop  = $this->coopModel->findWithDetails($this->coopId);
        $stats = [
            'members'       => count($this->coopModel->getMembers($this->coopId)),
            'inventory_val' => $invModel->getSummaryStats($this->coopId)['total_value'] ?? 0,
            'active_orders' => count(array_filter($orderModel->getStatusCounts($this->coopId), fn($s) => in_array($s['status'], ['pending','approved']))),
            'revenue'       => $this->coopModel->getRevenueStats($this->coopId)['completed_revenue'] ?? 0,
        ];
        $inventory      = $invModel->getAllWithDetails(1, 5, '', $this->coopId)['data'];
        $recentOrders   = $orderModel->getAllWithDetails(1, 5, '', '', $this->coopId)['data'];
        $predictions    = $aiService->getLatestPredictions(5);
        $revenueByMonth = $orderModel->getRevenueByMonth($this->coopId);
        $invSummary     = $this->coopModel->getInventorySummary($this->coopId);

        $this->view('cooperative/dashboard', compact('coop','stats','inventory','recentOrders','predictions','revenueByMonth','invSummary'));
    }

    public function members(): void {
        $members = $this->coopModel->getMembers($this->coopId);
        $db      = Database::getInstance();
        $farmers = $db->query("SELECT f.id, u.first_name, u.last_name FROM farmers f JOIN users u ON f.user_id=u.id WHERE u.status='active'")->fetchAll();
        $this->view('cooperative/members', ['title' => 'Members', 'members' => $members, 'farmers' => $farmers, 'coopId' => $this->coopId]);
    }

    public function inventory(): void {
        $model  = new InventoryModel();
        $page   = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $cropId = (int)($_GET['crop'] ?? 0);
        $dateFrom = $this->validDate($_GET['date_from'] ?? '');
        $dateTo   = $this->validDate($_GET['date_to'] ?? '');
        if ($dateFrom && $dateTo && $dateFrom > $dateTo) { [$dateFrom, $dateTo] = [$dateTo, $dateFrom]; }
        $result = $model->getAllWithDetails($page, 15, $search, $this->coopId, $cropId, $dateFrom, $dateTo);
        $stats  = ($dateFrom || $dateTo)
            ? $model->getPeriodSummary($this->coopId, $cropId, $dateFrom, $dateTo)
            : $model->getSummaryStats($this->coopId, $cropId);
        $db     = Database::getInstance();
        $this->view('cooperative/inventory/index', [
            'title'      => 'Inventory',
            'result'     => $result,
            'stats'      => $stats,
            'search'     => $search,
            'crops'      => (new CropModel())->getAllWithCategory(),
            'warehouses' => $db->query("SELECT * FROM warehouses WHERE cooperative_id={$this->coopId}")->fetchAll(),
            'cropId'     => $cropId,
            'dateFrom'   => $dateFrom,
            'dateTo'     => $dateTo,
        ]);
    }

    private function validDate(string $date): string {
        $parsed = DateTime::createFromFormat('Y-m-d', $date);
        return $parsed && $parsed->format('Y-m-d') === $date ? $date : '';
    }

    public function createInventory(): void {
        $db = Database::getInstance();
        $this->view('cooperative/inventory/create', [
            'title'      => 'Add Inventory',
            'crops'      => (new CropModel())->getAllWithCategory(),
            'warehouses' => $db->query("SELECT * FROM warehouses WHERE cooperative_id={$this->coopId}")->fetchAll(),
            'harvests'   => $db->query(
                "SELECT h.id, c.name as crop_name, h.quantity, h.harvest_date,
                        u.first_name, u.last_name
                 FROM harvests h
                 JOIN crops c ON h.crop_id=c.id
                 JOIN farmers f ON h.farmer_id=f.id
                 JOIN users u ON f.user_id=u.id
                 WHERE f.cooperative_id={$this->coopId}
                 ORDER BY h.harvest_date DESC"
            )->fetchAll(),
        ]);
    }

    public function storeInventory(): void {
        if (!$this->isPost()) { $this->redirect('/cooperative/inventory'); return; }
        $this->validateCsrf();
        $data = [
            'cooperative_id' => $this->coopId,
            'warehouse_id'   => (int)($_POST['warehouse_id'] ?? 0) ?: null,
            'crop_id'        => (int)($_POST['crop_id'] ?? 0),
            'harvest_id'     => (int)($_POST['harvest_id'] ?? 0) ?: null,
            'qty_opening'    => (float)($_POST['qty_opening'] ?? $_POST['qty_available'] ?? 0),
            'qty_received'   => 0,
            'qty_available'  => (float)($_POST['qty_opening'] ?? $_POST['qty_available'] ?? 0),
            'grade'          => $_POST['grade'] ?? 'A',
            'buying_price'   => (float)($_POST['buying_price'] ?? 0),
            'asking_price'   => (float)($_POST['asking_price'] ?? 0),
            'harvest_date'   => $_POST['harvest_date'] ?? null,
            'expiry_date'    => $_POST['expiry_date'] ?? null,
            'status'         => 'available',
        ];
        if (!$data['crop_id'] || !$data['qty_opening']) { $this->flash('danger', 'Crop and opening stock are required.'); $this->redirect('/cooperative/inventory/create'); return; }
        $id = (new InventoryModel())->create($data);
        (new InventoryModel())->recordMovement($id, 'opening', $data['qty_opening'], 'inventory', $id, ($data['harvest_date'] ?: date('Y-m-d')) . ' 00:00:00');
        AuditLogger::log('inventory_created', 'inventory', $id, [], $data);
        NotificationService::sendToRole('buyer', 'new_stock', 'New Stock Available', 'New crop inventory has been added.', '/buyer/marketplace');
        $this->flash('success', 'Inventory added.');
        $this->redirect('/cooperative/inventory');
    }

    public function editInventory(string $id): void {
        $model = new InventoryModel();
        $item  = $model->find((int)$id);
        if (!$item || $item['cooperative_id'] != $this->coopId) { $this->flash('danger', 'Not found.'); $this->redirect('/cooperative/inventory'); return; }
        $db = Database::getInstance();
        $this->view('cooperative/inventory/edit', [
            'title'      => 'Edit Inventory',
            'item'       => $item,
            'crops'      => (new CropModel())->getAllWithCategory(),
            'warehouses' => $db->query("SELECT * FROM warehouses WHERE cooperative_id={$this->coopId}")->fetchAll(),
        ]);
    }

    public function updateInventory(string $id): void {
        if (!$this->isPost()) { $this->redirect('/cooperative/inventory'); return; }
        $this->validateCsrf();
        $model = new InventoryModel();
        $item  = $model->find((int)$id);
        if (!$item || $item['cooperative_id'] != $this->coopId) { $this->flash('danger', 'Not found.'); $this->redirect('/cooperative/inventory'); return; }
        $data = [
            'asking_price'  => (float)($_POST['asking_price'] ?? 0),
            'buying_price'  => (float)($_POST['buying_price'] ?? 0),
            'grade'         => $_POST['grade'] ?? 'A',
            'status'        => $_POST['status'] ?? 'available',
            'expiry_date'   => $_POST['expiry_date'] ?? null,
        ];
        $model->update((int)$id, $data);
        AuditLogger::log('inventory_updated', 'inventory', (int)$id, $item, $data);
        $this->flash('success', 'Inventory updated.');
        $this->redirect('/cooperative/inventory');
    }

    public function stockIn(string $id): void {
        if (!$this->isPost()) { $this->redirect('/cooperative/inventory'); return; }
        $this->validateCsrf();
        $model = new InventoryModel();
        $item = $model->find((int)$id);
        $qty = (float)($_POST['quantity'] ?? 0);
        if (!$item || (int)$item['cooperative_id'] !== $this->coopId || $qty <= 0) {
            $this->flash('danger', 'Enter a valid stock-in quantity.');
            $this->redirect('/cooperative/inventory'); return;
        }
        $model->stockIn((int)$id, $qty);
        AuditLogger::log('inventory_stock_in', 'inventory', (int)$id, ['qty_available'=>$item['qty_available']], ['quantity'=>$qty]);
        $this->flash('success', number_format($qty, 2) . ' units added to stock.');
        $this->redirect('/cooperative/inventory');
    }

    public function stockOut(string $id): void {
        if (!$this->isPost()) { $this->redirect('/cooperative/inventory'); return; }
        $this->validateCsrf();
        $model = new InventoryModel();
        $item = $model->find((int)$id);
        $qty = (float)($_POST['quantity'] ?? 0);
        if (!$item || (int)$item['cooperative_id'] !== $this->coopId || $qty <= 0 || $qty > (float)$item['qty_available']) {
            $this->flash('danger', 'Stock-out quantity must be greater than zero and cannot exceed available stock.');
            $this->redirect('/cooperative/inventory'); return;
        }
        $model->stockOut((int)$id, $qty);
        AuditLogger::log('inventory_stock_out', 'inventory', (int)$id, ['qty_available'=>$item['qty_available']], ['quantity'=>$qty]);
        $this->flash('success', number_format($qty, 2) . ' units removed from stock.');
        $this->redirect('/cooperative/inventory');
    }

    public function harvests(): void {
        $model    = new HarvestModel();
        $page     = (int)($_GET['page'] ?? 1);
        $search   = $_GET['search'] ?? '';
        $farmerId = (int)($_GET['farmer'] ?? 0);
        $cropId   = (int)($_GET['crop'] ?? 0);
        $result   = $model->getAllWithDetails($page, 15, $search, $farmerId, $this->coopId);
        // Filter by crop in PHP since HarvestModel doesn't have cropId param
        if ($cropId) {
            $filtered = array_filter($result['data'], fn($h) => $h['crop_id'] == $cropId);
            $result['data'] = array_values($filtered);
        }
        $this->view('cooperative/harvests', [
            'title'   => 'Harvests',
            'result'  => $result,
            'search'  => $search,
            'crops'   => (new CropModel())->getAllWithCategory(),
            'farmers' => $this->coopModel->getMembers($this->coopId),
        ]);
    }

    public function storeHarvest(): void {
        if (!$this->isPost()) { $this->redirect('/cooperative/harvests'); return; }
        $this->validateCsrf();
        $data = [
            'farmer_id'      => (int)($_POST['farmer_id'] ?? 0),
            'crop_id'        => (int)($_POST['crop_id'] ?? 0),
            'cooperative_id' => $this->coopId,
            'quantity'       => (float)($_POST['quantity'] ?? 0),
            'grade'          => $_POST['grade'] ?? 'A',
            'harvest_date'   => $_POST['harvest_date'] ?? date('Y-m-d'),
            'season'         => trim($_POST['season'] ?? ''),
            'notes'          => trim($_POST['notes'] ?? ''),
        ];
        if (!$data['farmer_id'] || !$data['crop_id'] || !$data['quantity']) { $this->flash('danger', 'Required fields missing.'); $this->redirect('/cooperative/harvests'); return; }
        $id = (new HarvestModel())->create($data);
        AuditLogger::log('harvest_recorded', 'harvests', $id, [], $data);
        $this->flash('success', 'Harvest recorded.');
        $this->redirect('/cooperative/harvests');
    }

    public function orders(): void {
        $model  = new OrderModel();
        $page   = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $result = $model->getAllWithDetails($page, 15, $search, $status, $this->coopId);
        $this->view('cooperative/orders/index', ['title' => 'Orders', 'result' => $result, 'search' => $search, 'status' => $status]);
    }

    public function orderDetail(string $id): void {
        $model = new OrderModel();
        $order = $model->findWithDetails((int)$id);
        if (!$order || $order['cooperative_id'] != $this->coopId) { $this->flash('danger', 'Order not found.'); $this->redirect('/cooperative/orders'); return; }
        $items = $model->getItems((int)$id);
        $this->view('cooperative/orders/detail', ['title' => 'Order #' . $order['order_no'], 'order' => $order, 'items' => $items]);
    }

    public function approveOrder(string $id): void {
        if (!$this->isPost()) { $this->redirect('/cooperative/orders'); return; }
        $this->validateCsrf();
        $model = new OrderModel();
        $order = $model->find((int)$id);
        if (!$order || $order['cooperative_id'] != $this->coopId) { $this->flash('danger', 'Not found.'); $this->redirect('/cooperative/orders'); return; }

        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $model->updateStatus((int)$id, 'approved', Auth::id());
            // Reserve inventory
            $items = $model->getItems((int)$id);
            $invModel = new InventoryModel();
            foreach ($items as $item) {
                $invModel->reserveStock($item['inventory_id'], $item['quantity']);
            }
            $db->commit();
            // Notify buyer
            $buyerUserId = $db->prepare("SELECT u.id FROM buyers b JOIN users u ON b.user_id=u.id WHERE b.id=?")->execute([$order['buyer_id']]) ? null : null;
            $stmt = $db->prepare("SELECT u.id FROM buyers b JOIN users u ON b.user_id=u.id WHERE b.id=?");
            $stmt->execute([$order['buyer_id']]);
            $buyerUser = $stmt->fetch();
            if ($buyerUser) {
                NotificationService::send($buyerUser['id'], 'order_approved', 'Order Approved', "Your order #{$order['order_no']} has been approved.", '/buyer/orders/' . $id);
            }
            AuditLogger::log('order_approved', 'orders', (int)$id);
            $this->flash('success', 'Order approved.');
        } catch (Exception $e) {
            $db->rollBack();
            $this->flash('danger', 'Failed to approve order.');
        }
        $this->redirect('/cooperative/orders/' . $id);
    }

    public function rejectOrder(string $id): void {
        if (!$this->isPost()) { $this->redirect('/cooperative/orders'); return; }
        $this->validateCsrf();
        $model = new OrderModel();
        $order = $model->find((int)$id);
        if (!$order || $order['cooperative_id'] != $this->coopId) { $this->flash('danger', 'Not found.'); $this->redirect('/cooperative/orders'); return; }
        $model->updateStatus((int)$id, 'rejected', Auth::id());
        AuditLogger::log('order_rejected', 'orders', (int)$id);
        $this->flash('success', 'Order rejected.');
        $this->redirect('/cooperative/orders/' . $id);
    }

    public function markDelivered(string $id): void {
        if (!$this->isPost()) { $this->redirect('/cooperative/orders'); return; }
        $this->validateCsrf();
        $model = new OrderModel();
        $order = $model->find((int)$id);
        if (!$order || $order['cooperative_id'] != $this->coopId) { $this->flash('danger', 'Not found.'); $this->redirect('/cooperative/orders'); return; }

        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $model->updateStatus((int)$id, 'completed');
            $items    = $model->getItems((int)$id);
            $invModel = new InventoryModel();
            foreach ($items as $item) {
                $invModel->confirmSale($item['inventory_id'], $item['quantity']);
            }
            $db->commit();
            AuditLogger::log('order_completed', 'orders', (int)$id);
            $this->flash('success', 'Order marked as completed.');
        } catch (Exception $e) {
            $db->rollBack();
            $this->flash('danger', 'Failed to update order.');
        }
        $this->redirect('/cooperative/orders/' . $id);
    }

    public function aiPredictions(): void {
        $aiService = new AIPredictionService();
        $db        = Database::getInstance();
        $this->view('cooperative/ai-predictions', [
            'title'       => 'AI Predictions',
            'predictions' => $aiService->getLatestPredictions(15),
            'crops'       => (new CropModel())->getAllWithCategory(),
            'districts'   => $db->query("SELECT * FROM districts ORDER BY name")->fetchAll(),
        ]);
    }

    public function runPrediction(): void {
        if (!$this->isPost()) { $this->redirect('/cooperative/ai-predictions'); return; }
        $this->validateCsrf();
        $cropId     = (int)($_POST['crop_id'] ?? 0);
        $districtId = (int)($_POST['district_id'] ?? 1);
        if (!$cropId) { $this->flash('danger', 'Select a crop.'); $this->redirect('/cooperative/ai-predictions'); return; }
        (new AIPredictionService())->predict($cropId, $districtId, $this->coopId);
        $this->flash('success', 'Prediction generated.');
        $this->redirect('/cooperative/ai-predictions');
    }

    public function reports(): void {
        $this->view('cooperative/reports', [
            'title'   => 'Reports',
            'crops'   => (new CropModel())->getAllWithCategory(),
            'coopId'  => $this->coopId,
        ]);
    }

    private function renderReport(string $title, array $columns, array $rows, string $format): void {
        if (in_array($format, ['csv', 'excel'], true)) {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . strtolower(str_replace(' ','_',$title)) . '_' . date('Y-m-d') . '.csv"');
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            foreach ($rows as $row) fputcsv($out, array_values($row));
            fclose($out); exit;
        }
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' . htmlspecialchars($title) . '</title>';
        echo '<style>body{font-family:Arial,sans-serif;font-size:12px;margin:20px}h2{color:#198754}table{width:100%;border-collapse:collapse;margin-top:15px}th{background:#198754;color:#fff;padding:8px;text-align:left}td{padding:6px 8px;border-bottom:1px solid #ddd}tr:nth-child(even){background:#f9f9f9}.meta{color:#666;font-size:11px;margin-bottom:10px}@media print{.no-print{display:none}}</style>';
        echo '</head><body><h2>' . htmlspecialchars($title) . '</h2>';
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
        echo '</tbody></table></body></html>'; exit;
    }

    public function reportMembers(): void {
        $format = $_GET['format'] ?? 'pdf';
        $rows = array_map(fn($m) => [
            $m['first_name'], $m['last_name'], $m['email'], $m['phone'] ?? '-',
            $m['farm_name'] ?? '-', $m['farm_size'] ?? '-', $m['status']
        ], $this->coopModel->getMembers($this->coopId));
        $this->renderReport('Members Report', ['First Name','Last Name','Email','Phone','Farm Name','Farm Size (ha)','Status'], $rows, $format);
    }

    public function reportHarvests(): void {
        $format = $_GET['format'] ?? 'pdf';
        $rows = (new HarvestModel())->getAllWithDetails(1, 10000, '', 0, $this->coopId)['data'];
        $out = array_map(fn($r) => [
            $r['first_name'] . ' ' . $r['last_name'], $r['crop_name'],
            $r['quantity'], $r['crop_unit'], $r['grade'], $r['season'] ?? '-', $r['harvest_date']
        ], $rows);
        $this->renderReport('Harvests Report', ['Farmer','Crop','Quantity','Unit','Grade','Season','Date'], $out, $format);
    }

    public function reportInventory(): void {
        $format = $_GET['format'] ?? 'pdf';
        $rows = (new InventoryModel())->getAllWithDetails(1, 10000, '', $this->coopId)['data'];
        $out = array_map(fn($r) => [
            $r['crop_name'], $r['warehouse_name'] ?? '-', $r['qty_available'],
            $r['qty_reserved'], $r['qty_sold'], $r['grade'], $r['asking_price'], $r['status']
        ], $rows);
        $this->renderReport('Inventory Report', ['Crop','Warehouse','Available','Reserved','Sold','Grade','Price (RWF)','Status'], $out, $format);
    }

    public function reportOrders(): void {
        $format = $_GET['format'] ?? 'pdf';
        $rows = (new OrderModel())->getAllWithDetails(1, 10000, '', '', $this->coopId)['data'];
        $out = array_map(fn($r) => [
            $r['order_no'],
            trim(($r['company_name'] ?? '') ?: ($r['first_name'] . ' ' . $r['last_name'])),
            $r['total_amount'], $r['status'], $r['created_at']
        ], $rows);
        $this->renderReport('Orders Report', ['Order No','Buyer','Amount (RWF)','Status','Date'], $out, $format);
    }

    public function reportAi(): void {
        $format = $_GET['format'] ?? 'pdf';
        $rows = (new AIPredictionService())->getLatestPredictions(10000);
        $out = array_map(fn($r) => [
            $r['crop_name'], $r['predicted_demand'], $r['predicted_price'],
            $r['confidence_score'] . '%', $r['best_selling_period'], $r['prediction_date']
        ], $rows);
        $this->renderReport('AI Predictions Report', ['Crop','Demand','Predicted Price (RWF)','Confidence','Best Period','Date'], $out, $format);
    }

    public function productionPlans(): void {
        $db    = Database::getInstance();
        $plans = $db->query("SELECT pp.*, c.name as crop_name FROM production_plans pp JOIN crops c ON pp.crop_id=c.id WHERE pp.cooperative_id={$this->coopId} ORDER BY pp.created_at DESC")->fetchAll();
        $this->view('cooperative/production-plans', [
            'title'  => 'Production Plans',
            'plans'  => $plans,
            'crops'  => (new CropModel())->getAllWithCategory(),
        ]);
    }

    public function storePlan(): void {
        if (!$this->isPost()) { $this->redirect('/cooperative/production-plans'); return; }
        $this->validateCsrf();
        $data = [
            'cooperative_id' => $this->coopId,
            'crop_id'        => (int)($_POST['crop_id'] ?? 0),
            'season'         => trim($_POST['season'] ?? ''),
            'planned_qty'    => (float)($_POST['planned_qty'] ?? 0),
            'planned_date'   => $_POST['planned_date'] ?? null,
            'notes'          => trim($_POST['notes'] ?? ''),
            'status'         => 'planned',
            'created_by'     => Auth::id(),
        ];
        Database::getInstance()->prepare(
            "INSERT INTO production_plans (cooperative_id,crop_id,season,planned_qty,planned_date,notes,status,created_by) VALUES (?,?,?,?,?,?,?,?)"
        )->execute(array_values($data));
        $this->flash('success', 'Production plan created.');
        $this->redirect('/cooperative/production-plans');
    }
}
