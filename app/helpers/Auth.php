<?php
class Auth {
    public static function start(){
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public static function login($user){
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['role']      = $user['role_name'];
        $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['email']     = $user['email'];
        $_SESSION['avatar']    = $user['avatar'] ?? null;
    }

    public static function logout(){
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function check()    { return !empty($_SESSION['user_id']); }
    public static function id()       { return $_SESSION['user_id'] ?? null; }
    public static function role()     { return $_SESSION['role'] ?? null; }
    public static function name()     { return $_SESSION['full_name'] ?? 'Guest'; }
    public static function email()    { return $_SESSION['email'] ?? ''; }
    public static function avatar()   { return $_SESSION['avatar'] ?? null; }

    public static function is(...$roles) {
        return in_array(self::role(), $roles);
    }

    public static function csrfToken() {
        return $_SESSION['csrf_token'] ?? '';
    }

    public static function csrfField() {
        return '<input type="hidden" name="_token" value="' . self::csrfToken() . '">';
    }

    public static function verifyCsrf($token) {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    public static function dashboardUrl() {
        $role = self::role();
        if ($role === 'admin')               return '/admin/dashboard';
        if ($role === 'cooperative_manager') return '/cooperative/dashboard';
        if ($role === 'farmer')              return '/farmer/dashboard';
        if ($role === 'buyer')               return '/buyer/dashboard';
        return '/';
    }
}
