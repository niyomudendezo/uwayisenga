<?php
abstract class Controller {
    protected function view(string $view, array $data = [], string $layout = 'main'): void {
        extract($data);
        $viewFile = BASE_PATH . '/app/views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            die("View not found: {$view}");
        }
        if ($layout) {
            $content = $viewFile;
            require BASE_PATH . '/app/views/layouts/' . $layout . '.php';
        } else {
            require $viewFile;
        }
    }

    protected function json(mixed $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void {
        header("Location: " . APP_URL . $url);
        exit;
    }

    protected function back(): void {
        $ref = $_SERVER['HTTP_REFERER'] ?? APP_URL . '/';
        header("Location: {$ref}");
        exit;
    }

    protected function isPost(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
    protected function isGet(): bool  { return $_SERVER['REQUEST_METHOD'] === 'GET'; }
    protected function isAjax(): bool { return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'; }

    protected function input(string $key, mixed $default = null): mixed {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function sanitize(string $value): string {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    protected function validateCsrf(): void {
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
        }
    }

    protected function requireAuth(): void {
        if (empty($_SESSION['user_id'])) {
            if ($this->isAjax()) $this->json(['error' => 'Unauthenticated'], 401);
            $this->redirect('/login');
        }
    }

    protected function requireRole(string ...$roles): void {
        $this->requireAuth();
        if (!in_array($_SESSION['role'] ?? '', $roles)) {
            if ($this->isAjax()) $this->json(['error' => 'Forbidden'], 403);
            $this->redirect('/unauthorized');
        }
    }

    protected function flash(string $type, string $message): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function setFlash(string $type, string $msg): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $msg];
    }
}
