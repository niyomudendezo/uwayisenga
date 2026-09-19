<?php
class ProfileController extends Controller {

    public function index(){
        $this->requireAuth();
        $userModel = new UserModel();
        $user      = $userModel->findWithRole(Auth::id());
        $db        = Database::getInstance();
        $this->view('shared/profile', [
            'title'     => 'My Profile',
            'user'      => $user,
            'districts' => $db->query("SELECT * FROM districts ORDER BY name")->fetchAll(),
        ]);
    }

    public function update(){
        $this->requireAuth();
        if (!$this->isPost()) { $this->redirect('/profile'); return; }
        $this->validateCsrf();

        $data = [
            'first_name'  => trim($_POST['first_name'] ?? ''),
            'last_name'   => trim($_POST['last_name'] ?? ''),
            'phone'       => trim($_POST['phone'] ?? ''),
            'district_id' => (int)($_POST['district_id'] ?? 0) ?: null,
        ];

        // Handle avatar upload
        if (!empty($_FILES['avatar']['name'])) {
            $path = Helper::uploadFile($_FILES['avatar'], 'profiles', ['jpg','jpeg','png']);
            if ($path) $data['avatar'] = $path;
        }

        (new UserModel())->update(Auth::id(), $data);
        $_SESSION['full_name'] = $data['first_name'] . ' ' . $data['last_name'];
        AuditLogger::log('profile_updated', 'profile', Auth::id());
        $this->flash('success', 'Profile updated.');
        $this->redirect('/profile');
    }

    public function changePassword(){
        $this->requireAuth();
        if (!$this->isPost()) { $this->redirect('/profile'); return; }
        $this->validateCsrf();

        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $userModel = new UserModel();
        $user      = $userModel->find(Auth::id());

        if (!password_verify($current, $user['password'])) {
            $this->flash('danger', 'Current password is incorrect.');
            $this->redirect('/profile');
            return;
        }

        if ($new !== $confirm || strlen($new) < 8) {
            $this->flash('danger', 'New passwords do not match or are too short.');
            $this->redirect('/profile');
            return;
        }

        $userModel->update(Auth::id(), ['password' => password_hash($new, PASSWORD_BCRYPT)]);
        AuditLogger::log('password_changed', 'profile', Auth::id());
        $this->flash('success', 'Password changed successfully.');
        $this->redirect('/profile');
    }
}
