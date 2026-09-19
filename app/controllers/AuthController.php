<?php
class AuthController extends Controller {

    public function loginForm(){
        if (Auth::check()) $this->redirect(Auth::dashboardUrl());
        $this->view('auth/login', ['title' => 'Login'], 'auth');
    }

    public function login(){
        if (!$this->isPost()) { $this->redirect('/login'); return; }
        $this->validateCsrf();

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $this->flash('danger', 'Email and password are required.');
            $this->redirect('/login');
            return;
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            AuditLogger::log('login_failed', 'auth', 0, [], ['email' => $email]);
            $this->flash('danger', 'Invalid email or password.');
            $this->redirect('/login');
            return;
        }

        if ($user['status'] !== 'active') {
            $this->flash('danger', 'Your account is suspended. Contact administrator.');
            $this->redirect('/login');
            return;
        }

        Auth::login($user);
        $userModel->updateLastLogin($user['id']);
        AuditLogger::log('login_success', 'auth', $user['id']);
        NotificationService::send($user['id'], 'login', 'Login Successful', 'You logged in successfully.');

        $this->redirect(Auth::dashboardUrl());
    }

    public function registerForm(){
        if (Auth::check()) $this->redirect(Auth::dashboardUrl());
        $userModel = new UserModel();
        $this->view('auth/register', [
            'title'     => 'Register',
            'roles'     => array_filter($userModel->getRoles(), function($r) { return in_array($r['name'], ['farmer','buyer']); }),
            'districts' => (new Database())->getInstance()->query("SELECT * FROM districts ORDER BY name")->fetchAll(),
        ], 'auth');
    }

    public function register(){
        if (!$this->isPost()) { $this->redirect('/register'); return; }
        $this->validateCsrf();

        $data = [
            'first_name'  => trim($_POST['first_name'] ?? ''),
            'last_name'   => trim($_POST['last_name'] ?? ''),
            'email'       => trim($_POST['email'] ?? ''),
            'phone'       => trim($_POST['phone'] ?? ''),
            'password'    => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'role'        => $_POST['role'] ?? 'farmer',
            'district_id' => (int)($_POST['district_id'] ?? 0),
        ];

        if (!$data['first_name'] || !$data['last_name'] || !$data['email'] || !$data['password']) {
            $this->flash('danger', 'All required fields must be filled.');
            $this->redirect('/register');
            return;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->flash('danger', 'Invalid email address.');
            $this->redirect('/register');
            return;
        }

        if (strlen($data['password']) < 8) {
            $this->flash('danger', 'Password must be at least 8 characters.');
            $this->redirect('/register');
            return;
        }

        if (!hash_equals($data['password'], $data['password_confirm'])) {
            $this->flash('danger', 'Password confirmation does not match.');
            $this->redirect('/register');
            return;
        }

        if ($data['phone'] && !preg_match('/^\+?[0-9\s-]{9,20}$/', $data['phone'])) {
            $this->flash('danger', 'Please enter a valid phone number.');
            $this->redirect('/register');
            return;
        }

        $userModel = new UserModel();
        if ($userModel->findByEmail($data['email'])) {
            $this->flash('danger', 'Email already registered.');
            $this->redirect('/register');
            return;
        }

        $db = Database::getInstance();
        $roleStmt = $db->prepare("SELECT id FROM roles WHERE name=?");
        $roleStmt->execute([$data['role']]);
        $role = $roleStmt->fetch();

        if (!$role || !in_array($data['role'], ['farmer','buyer'])) {
            $this->flash('danger', 'Invalid role selected.');
            $this->redirect('/register');
            return;
        }

        try {
            $db->beginTransaction();

            $userId = $userModel->create([
                'role_id'           => $role['id'],
                'first_name'        => $data['first_name'],
                'last_name'         => $data['last_name'],
                'email'             => $data['email'],
                'phone'             => $data['phone'],
                'password'          => password_hash($data['password'], PASSWORD_BCRYPT),
                'district_id'       => $data['district_id'] ?: null,
                'status'            => 'active',
                'email_verified_at' => date('Y-m-d H:i:s'),
            ]);

            if ($data['role'] === 'farmer') {
                $farmerModel = new FarmerModel();
                $farmerModel->create(['user_id' => $userId, 'district_id' => $data['district_id'] ?: null]);
            } elseif ($data['role'] === 'buyer') {
                $buyerModel = new BuyerModel();
                $buyerModel->create(['user_id' => $userId, 'district_id' => $data['district_id'] ?: null]);
            }

            $db->commit();
            AuditLogger::log('user_registered', 'auth', $userId, [], ['email' => $data['email'], 'role' => $data['role']]);
            $this->flash('success', 'Registration successful! Please login.');
            $this->redirect('/login');

        } catch (Exception $e) {
            $db->rollBack();
            $this->flash('danger', 'Registration failed. Please try again.');
            $this->redirect('/register');
        }
    }

    public function logout(){
        AuditLogger::log('logout', 'auth', Auth::id() ?? 0);
        Auth::logout();
        $this->redirect('/login');
    }

    public function forgotForm(){
        $this->view('auth/forgot', ['title' => 'Forgot Password'], 'auth');
    }

    public function forgotPassword(){
        if (!$this->isPost()) { $this->redirect('/forgot-password'); return; }
        $this->validateCsrf();

        $email = trim($_POST['email'] ?? '');
        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        // Always show success to prevent email enumeration
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $userModel->setResetToken($email, $token);
            // In production: send email with reset link
            // For demo: store token in session
            $_SESSION['reset_link'] = APP_URL . '/reset-password/' . $token;
        }

        $this->flash('success', 'If that email exists, a reset link has been sent.');
        $this->redirect('/forgot-password');
    }

    public function resetForm($token){
        $userModel = new UserModel();
        $user = $userModel->findByResetToken($token);
        if (!$user) {
            $this->flash('danger', 'Invalid or expired reset token.');
            $this->redirect('/forgot-password');
            return;
        }
        $this->view('auth/reset', ['title' => 'Reset Password', 'token' => $token], 'auth');
    }

    public function resetPassword(){
        if (!$this->isPost()) { $this->redirect('/login'); return; }
        $this->validateCsrf();

        $token    = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        if ($password !== $confirm || strlen($password) < 8) {
            $this->flash('danger', 'Passwords do not match or are too short.');
            $this->redirect('/reset-password/' . $token);
            return;
        }

        $userModel = new UserModel();
        $user = $userModel->findByResetToken($token);
        if (!$user) {
            $this->flash('danger', 'Invalid or expired token.');
            $this->redirect('/forgot-password');
            return;
        }

        $userModel->update($user['id'], [
            'password'          => password_hash($password, PASSWORD_BCRYPT),
            'reset_token'       => null,
            'reset_token_expires' => null,
        ]);

        $this->flash('success', 'Password reset successfully. Please login.');
        $this->redirect('/login');
    }
}
