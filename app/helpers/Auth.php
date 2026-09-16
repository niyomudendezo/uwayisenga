<?php
class Auth {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public static function login(array $user): void {
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['role']      = $user['role_name'];
        $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['email']     = $user['email'];
        $_SESSION['avatar']    = $user['avatar'] ?? null;
    }

    public static function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool    { return !empty($_SESSION['user_id']); }
    public static function id(): ?int       { return $_SESSION['user_id'] ?? null; }
    public static function role(): ?string  { return $_SESSION['role'] ?? null; }
    public static function name(): string   { return $_SESSION['full_name'] ?? 'Guest'; }
    public static function email(): string  { return $_SESSION['email'] ?? ''; }
    public static function avatar(): ?string { return $_SESSION['avatar'] ?? null; }

    public static function is(string ...$roles): bool {
        return in_array(self::role(), $roles);
    }

    public static function csrfToken(): string {
        return $_SESSION['csrf_token'] ?? '';
    }

    public static function csrfField(): string {
        return '<input type="hidden" name="_token" value="' . self::csrfToken() . '">';
    }

    public static function verifyCsrf(string $token): bool {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    public static function dashboardUrl(): string {
        return match(self::role()) {
            'admin'                => '/admin/dashboard',
            'cooperative_manager'  => '/cooperative/dashboard',
            'farmer'               => '/farmer/dashboard',
            'buyer'                => '/buyer/dashboard',
            default                => '/',
        };
    }
}
