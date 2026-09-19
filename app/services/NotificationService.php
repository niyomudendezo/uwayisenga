<?php
class NotificationService {
    private static $db = null;

    private static function db() {
        if (!self::$db) self::$db = Database::getInstance();
        return self::$db;
    }

    public static function send($userId, $type, $title, $message, $link = ''){
        try {
            self::db()->prepare(
                "INSERT INTO notifications (user_id, type, title, message, link) VALUES (?,?,?,?,?)"
            )->execute([$userId, $type, $title, $message, $link]);
        } catch (Exception $e) {}
    }

    public static function sendToRole($role, $type, $title, $message, $link = ''){
        try {
            $users = self::db()->prepare(
                "SELECT u.id FROM users u JOIN roles r ON u.role_id=r.id WHERE r.name=? AND u.status='active'"
            );
            $users->execute([$role]);
            foreach ($users->fetchAll() as $user) {
                self::send($user['id'], $type, $title, $message, $link);
            }
        } catch (Exception $e) {}
    }

    public static function unreadCount($userId): int {
        $stmt = self::db()->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function getRecent($userId, $limit = 10): array {
        $stmt = self::db()->prepare(
            "SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    public static function markRead($userId){
        self::db()->prepare("UPDATE notifications SET is_read=1 WHERE user_id=?")->execute([$userId]);
    }
}
