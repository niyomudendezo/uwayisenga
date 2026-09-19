<?php
class BuyerModel extends Model {
    protected $table = 'buyers';

    public function getAllWithDetails($page = 1, $perPage = 15, $search = '', ?string $verified = null): array {
        $where = []; $params = [];
        if ($search)            { $where[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR b.company_name LIKE ? OR u.email LIKE ?)"; $params = ["%$search%","%$search%","%$search%","%$search%"]; }
        if ($verified !== null) { $where[] = "b.verified=?"; $params[] = (int)$verified; }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM buyers b JOIN users u ON b.user_id=u.id $whereStr");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare(
            "SELECT b.*, u.first_name, u.last_name, u.email, u.phone, u.status, u.avatar,
                    d.name as district_name
             FROM buyers b JOIN users u ON b.user_id=u.id
             LEFT JOIN districts d ON b.district_id=d.id
             $whereStr ORDER BY b.verified ASC, u.created_at DESC LIMIT $perPage OFFSET $offset"
        );
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total, 'per_page' => $perPage, 'current_page' => $page, 'last_page' => max(1,(int)ceil($total/$perPage))];
    }

    public function findByUserId($userId): array|false {
        $stmt = $this->db->prepare(
            "SELECT b.*, u.first_name, u.last_name, u.email, u.phone, u.avatar, u.status,
                    d.name as district_name
             FROM buyers b JOIN users u ON b.user_id=u.id
             LEFT JOIN districts d ON b.district_id=d.id
             WHERE b.user_id=?"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function getPurchaseHistory($buyerId): array {
        $stmt = $this->db->prepare(
            "SELECT o.*, co.name as cooperative_name,
                    GROUP_CONCAT(c.name SEPARATOR ', ') as crops
             FROM orders o
             JOIN cooperatives co ON o.cooperative_id=co.id
             JOIN order_items oi ON oi.order_id=o.id
             JOIN crops c ON oi.crop_id=c.id
             WHERE o.buyer_id=?
             GROUP BY o.id ORDER BY o.created_at DESC"
        );
        $stmt->execute([$buyerId]);
        return $stmt->fetchAll();
    }

    public function getOrderStats($buyerId): array {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as total_orders,
                    COALESCE(SUM(total_amount),0) as total_spent,
                    COALESCE(SUM(CASE WHEN status='completed' THEN total_amount ELSE 0 END),0) as completed_amount
             FROM orders WHERE buyer_id=?"
        );
        $stmt->execute([$buyerId]);
        return $stmt->fetch();
    }
}
