<?php
class BuyerController extends Controller {

    private BuyerModel $buyerModel;
    private $buyer;

    public function __construct() {
        $this->requireRole('buyer');
        $this->buyerModel = new BuyerModel();
        $buyer = $this->buyerModel->findByUserId(Auth::id());
        if (!$buyer) { $this->flash('danger', 'Buyer profile not found.'); $this->redirect('/login'); }
        $this->buyer = $buyer;
    }

    public function dashboard(){
        $stats   = $this->buyerModel->getOrderStats($this->buyer['id']);
        $orders  = $this->buyerModel->getPurchaseHistory($this->buyer['id']);
        $prices  = (new MarketPriceModel())->getLatestPrices();
        $db      = Database::getInstance();
        $offers  = $db->prepare("SELECT bo.*, c.name as crop_name FROM buyer_offers bo JOIN crops c ON bo.crop_id=c.id WHERE bo.buyer_id=? AND bo.status='active'")->execute([$this->buyer['id']]) ? [] : [];
        $stmt    = $db->prepare("SELECT bo.*, c.name as crop_name FROM buyer_offers bo JOIN crops c ON bo.crop_id=c.id WHERE bo.buyer_id=? AND bo.status='active'");
        $stmt->execute([$this->buyer['id']]);
        $offers  = $stmt->fetchAll();

        $this->view('buyer/dashboard', [
            'buyer'  => $this->buyer,
            'stats'  => $stats,
            'orders' => array_slice($orders, 0, 5),
            'prices' => $prices,
            'offers' => $offers,
        ]);
    }

    public function marketplace(){
        $invModel   = new InventoryModel();
        $search     = $_GET['search'] ?? '';
        $districtId = (int)($_GET['district'] ?? 0);
        $cropId     = (int)($_GET['crop'] ?? 0);
        $minQty     = (float)($_GET['min_qty'] ?? 0);
        $db         = Database::getInstance();

        $this->view('buyer/marketplace', [
            'title'      => 'Marketplace',
            'items'      => $invModel->getAvailableForBuyers($search, $districtId, $cropId, $minQty),
            'crops'      => (new CropModel())->getAllWithCategory(),
            'districts'  => $db->query("SELECT * FROM districts ORDER BY name")->fetchAll(),
            'search'     => $search,
            'districtId' => $districtId,
            'cropId'     => $cropId,
            'minQty'     => $minQty,
        ]);
    }

    public function orders(){
        $model  = new OrderModel();
        $page   = (int)($_GET['page'] ?? 1);
        $status = $_GET['status'] ?? '';
        $result = $model->getAllWithDetails($page, 15, '', $status, 0, $this->buyer['id']);
        $this->view('buyer/orders/index', ['title' => 'My Orders', 'result' => $result, 'status' => $status]);
    }

    public function orderDetail($id){
        $model = new OrderModel();
        $order = $model->findWithDetails((int)$id);
        if (!$order || $order['buyer_id'] != $this->buyer['id']) { $this->flash('danger', 'Order not found.'); $this->redirect('/buyer/orders'); return; }
        $items = $model->getItems((int)$id);
        $this->view('buyer/orders/detail', ['title' => 'Order #' . $order['order_no'], 'order' => $order, 'items' => $items]);
    }

    public function placeOrder(){
        if (!$this->isPost()) { $this->redirect('/buyer/marketplace'); return; }
        $this->validateCsrf();

        $inventoryId   = (int)($_POST['inventory_id'] ?? 0);
        $quantity      = (float)($_POST['quantity'] ?? 0);
        $deliveryDate  = $_POST['delivery_date'] ?? null;
        $deliveryAddr  = trim($_POST['delivery_address'] ?? '');

        if (!$inventoryId || !$quantity) { $this->flash('danger', 'Invalid order data.'); $this->redirect('/buyer/marketplace'); return; }

        $invModel = new InventoryModel();
        $item     = $invModel->find($inventoryId);
        if (!$item || $item['qty_available'] < $quantity) { $this->flash('danger', 'Insufficient stock.'); $this->redirect('/buyer/marketplace'); return; }

        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $orderNo = Helper::generateOrderNo();
            $total   = $quantity * $item['asking_price'];

            $orderModel = new OrderModel();
            $orderId = $orderModel->create([
                'order_no'       => $orderNo,
                'buyer_id'       => $this->buyer['id'],
                'cooperative_id' => $item['cooperative_id'],
                'total_amount'   => $total,
                'status'         => 'pending',
                'delivery_date'  => $deliveryDate,
                'delivery_addr'  => $deliveryAddr,
            ]);

            $db->prepare(
                "INSERT INTO order_items (order_id, inventory_id, crop_id, quantity, unit, unit_price) VALUES (?,?,?,?,?,?)"
            )->execute([$orderId, $inventoryId, $item['crop_id'], $quantity, $item['unit'] ?? 'kg', $item['asking_price']]);

            $db->commit();

            // Notify cooperative manager
            $managerStmt = $db->prepare("SELECT manager_id FROM cooperatives WHERE id=?");
            $managerStmt->execute([$item['cooperative_id']]);
            $coop = $managerStmt->fetch();
            if ($coop && $coop['manager_id']) {
                NotificationService::send($coop['manager_id'], 'new_order', 'New Order Received', "Order #{$orderNo} has been placed.", '/cooperative/orders/' . $orderId);
            }

            AuditLogger::log('order_placed', 'orders', $orderId, [], ['order_no' => $orderNo, 'total' => $total]);
            $this->flash('success', "Order #{$orderNo} placed successfully!");
            $this->redirect('/buyer/orders/' . $orderId);

        } catch (Exception $e) {
            $db->rollBack();
            $this->flash('danger', 'Failed to place order. Please try again.');
            $this->redirect('/buyer/marketplace');
        }
    }

    public function cancelOrder($id){
        if (!$this->isPost()) { $this->redirect('/buyer/orders'); return; }
        $this->validateCsrf();
        $model = new OrderModel();
        $order = $model->find((int)$id);
        if (!$order || $order['buyer_id'] != $this->buyer['id'] || !in_array($order['status'], ['pending'])) {
            $this->flash('danger', 'Cannot cancel this order.');
            $this->redirect('/buyer/orders');
            return;
        }
        $model->updateStatus((int)$id, 'cancelled');
        AuditLogger::log('order_cancelled', 'orders', (int)$id);
        $this->flash('success', 'Order cancelled.');
        $this->redirect('/buyer/orders');
    }

    public function profile(){
        $db = Database::getInstance();
        $this->view('buyer/profile', [
            'title'     => 'My Profile',
            'buyer'     => $this->buyer,
            'districts' => $db->query("SELECT * FROM districts ORDER BY name")->fetchAll(),
        ]);
    }

    public function updateProfile(){
        if (!$this->isPost()) { $this->redirect('/buyer/profile'); return; }
        $this->validateCsrf();
        (new UserModel())->update(Auth::id(), [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name'] ?? ''),
            'phone'      => trim($_POST['phone'] ?? ''),
        ]);
        $this->buyerModel->update($this->buyer['id'], [
            'company_name'  => trim($_POST['company_name'] ?? ''),
            'business_type' => trim($_POST['business_type'] ?? ''),
            'tin_number'    => trim($_POST['tin_number'] ?? ''),
            'district_id'   => (int)($_POST['district_id'] ?? 0) ?: null,
            'address'       => trim($_POST['address'] ?? ''),
        ]);
        $this->flash('success', 'Profile updated.');
        $this->redirect('/buyer/profile');
    }

    public function marketPrices(){
        $priceModel = new MarketPriceModel();
        $cropId     = (int)($_GET['crop'] ?? 0);
        $this->view('buyer/market-prices', [
            'title'   => 'Market Prices',
            'prices'  => $priceModel->getLatestPrices(),
            'trends'  => $priceModel->getPriceTrends(),
            'crops'   => (new CropModel())->getAllWithCategory(),
            'history' => $cropId ? $priceModel->getPriceHistory($cropId) : [],
            'cropId'  => $cropId,
        ]);
    }

    public function storeOffer(){
        if (!$this->isPost()) { $this->redirect('/buyer/marketplace'); return; }
        $this->validateCsrf();
        $data = [
            'buyer_id'    => $this->buyer['id'],
            'crop_id'     => (int)($_POST['crop_id'] ?? 0),
            'district_id' => (int)($_POST['district_id'] ?? 0) ?: null,
            'price'       => (float)($_POST['price'] ?? 0),
            'quantity'    => (float)($_POST['quantity'] ?? 0),
            'unit'        => 'kg',
            'valid_until' => $_POST['valid_until'] ?? null,
            'notes'       => trim($_POST['notes'] ?? ''),
            'status'      => 'active',
        ];
        if (!$data['crop_id'] || !$data['price']) { $this->flash('danger', 'Crop and price required.'); $this->redirect('/buyer/marketplace'); return; }
        Database::getInstance()->prepare(
            "INSERT INTO buyer_offers (buyer_id,crop_id,district_id,price,quantity,unit,valid_until,notes,status) VALUES (?,?,?,?,?,?,?,?,?)"
        )->execute(array_values($data));
        $this->flash('success', 'Offer posted.');
        $this->redirect('/buyer/marketplace');
    }
}
