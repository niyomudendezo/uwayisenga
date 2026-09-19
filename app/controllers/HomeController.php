<?php
class HomeController extends Controller {

    public function index(){
        $db = Database::getInstance();
        $stats = [
            'farmers'      => (int) $db->query("SELECT COUNT(*) FROM farmers")->fetchColumn(),
            'cooperatives' => (int) $db->query("SELECT COUNT(*) FROM cooperatives WHERE status='active'")->fetchColumn(),
            'buyers'       => (int) $db->query("SELECT COUNT(*) FROM buyers WHERE verified=1")->fetchColumn(),
            'crops'        => (int) $db->query("SELECT COUNT(*) FROM crops WHERE status='active'")->fetchColumn(),
        ];
        $latestPrices = (new MarketPriceModel())->getLatestPrices();
        $this->view('public/home', ['title' => 'Home', 'stats' => $stats, 'latestPrices' => $latestPrices], 'public');
    }

    public function about(){
        $this->view('public/about', ['title' => 'About Us'], 'public');
    }

    public function contact(){
        $this->view('public/contact', ['title' => 'Contact Us'], 'public');
    }

    public function sendContact(){
        if (!$this->isPost()) { $this->redirect('/contact'); return; }
        $this->validateCsrf();

        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$subject || !$message) {
            $this->flash('danger', 'Please complete all fields with a valid email address.');
            $this->redirect('/contact');
            return;
        }
        $this->flash('success', 'Your message has been sent. We will get back to you shortly.');
        $this->redirect('/contact');
    }

    public function unauthorized(){
        http_response_code(403);
        $this->view('errors/403', ['title' => 'Access Denied'], Auth::check() ? 'main' : 'public');
    }

    public function marketPrices(){
        $priceModel = new MarketPriceModel();
        $cropModel  = new CropModel();
        $cropId     = (int) ($_GET['crop_id'] ?? 0);
        $this->view('public/market-prices', [
            'title'       => 'Market Prices',
            'prices'      => $priceModel->getLatestPrices(0, $cropId),
            'trends'      => $priceModel->getPriceTrends($cropId),
            'crops'       => $cropModel->getAllWithCategory(),
            'selectedCrop'=> $cropId,
        ], 'public');
    }
}
