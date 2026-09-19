<?php
class AuditLogger {
    private static ?PDO $db = null;

    private static function db(): PDO {
        if (!self::$db) self::$db = Database::getInstance();
        return self::$db;
    }

    public static function log($action, $module = '', $recordId = 0, $old = [], $new = []){
        try {
            $old = self::redactSensitive($old);
            $new = self::redactSensitive($new);
            $stmt = self::db()->prepare(
                "INSERT INTO audit_logs (user_id, action, module, record_id, old_values, new_values, ip_address, user_agent)
                 VALUES (?,?,?,?,?,?,?,?)"
            );
            $stmt->execute([
                Auth::id(),
                $action,
                $module,
                $recordId ?: null,
                $old ? json_encode($old) : null,
                $new ? json_encode($new) : null,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]);
        } catch (Exception $e) {
            // Silent fail — never break the app for logging
        }
    }

    private static function redactSensitive($values): array {
        $sensitive = ['password', 'password_confirm', 'current_password', 'new_password', 'token', '_token', 'reset_token'];
        foreach ($values as $key => $value) {
            if (in_array(strtolower((string) $key), $sensitive, true)) {
                $values[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $values[$key] = self::redactSensitive($value);
            }
        }
        return $values;
    }

    public static function activity($description){
        try {
            self::db()->prepare(
                "INSERT INTO activity_logs (user_id, description, ip_address) VALUES (?,?,?)"
            )->execute([Auth::id(), $description, $_SERVER['REMOTE_ADDR'] ?? null]);
        } catch (Exception $e) {}
    }
}
