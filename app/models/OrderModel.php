<?php
class OrderModel extends Model {
    protected $table = 'orders';

    public function getAllWithDetails($page = 1, $perPage = 15, $search = '', $status = '', $cooperativeId = 0, $buyerId = 0): array {
        $where = []; $params = [];
        if ($search)       { $where[] = "(o.order_no LIKE ? OR b_user.first_name LIKE ? OR co.name LIKE ?)"; $params = array_merge($params, ["%$search%","%$search%","%$search%"]); }
        if ($status)       { $where[] = "o.status=?"; $params[] = $status; }
        if ($cooperativeId){ $where[] = "o.cooperative_id=?"; $params[] = $cooperativeId; }
        if ($buyerId)      { $where[] = "o.buyer_id=?"; $params[] = $buyerId; }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM orders o JOIN buyers b ON o.buyer_id=b.id JOIN users b_user ON b.user_id=b_user.id JOIN cooperatives co ON o.cooperative_id=co.id $whereStr");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare(
            "SELECT o.*, b_user.first_name, b_user.last_name, b.company_name,
                    co.name as cooperative_name
             FROM orders o
             JOIN buyers b ON o.buyer_id=b.id
             JOIN users b_user ON b.user_id=b_user.id
             JOIN cooperatives co ON o.cooperative_id=co.id
             $whereStr ORDER BY o.created_at DESC LIMIT $perPage OFFSET $offset"
        );
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total, 'per_page' => $perPage, 'current_page' => $page, 'last_page' => max(1,(int)ceil($total/$perPage))];
    }

    public function findWithDetails($id): array|false {
        return $this->rawQueryOne(
            "SELECT o.*, b_user.first_name, b_user.last_name, b_user.email as buyer_email,
                    b_user.phone as buyer_phone, b.company_name, co.name as cooperative_name,
                    co.phone as coop_phone, co.email as coop_email
             FROM orders o
             JOIN buyers b ON o.buyer_id=b.id
             JOIN users b_user ON b.user_id=b_user.id
             JOIN cooperatives co ON o.cooperative_id=co.id
             WHERE o.id=?", [$id]
        );
    }

    public function getItems($orderId): array {
        return $this->rawQuery(
            "SELECT oi.*, c.name as crop_name, c.unit FROM order_items oi
             JOIN crops c ON oi.crop_id=c.id WHERE oi.order_id=?", [$orderId]
        );
    }

    public function updateStatus($id, $status, $reviewedBy = 0): bool {
        $sql = "UPDATE orders SET status=?, updated_at=NOW()";
        $params = [$status];
        if ($reviewedBy) { $sql .= ", reviewed_by=?, reviewed_at=NOW()"; $params[] = $reviewedBy; }
        $sql .= " WHERE id=?"; $params[] = $id;
        return $this->rawExecute($sql, $params);
    }

    public function getRevenueByMonth($cooperativeId = 0): array {
        $where = $cooperativeId ? "AND cooperative_id=$cooperativeId" : '';
        return $this->rawQuery(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') as month,
                    SUM(total_amount) as revenue, COUNT(*) as orders
             FROM orders WHERE status='completed' $where
             GROUP BY month ORDER BY month DESC LIMIT 12"
        );
    }

    public function getStatusCounts($cooperativeId = 0): array {
        $where = $cooperativeId ? "WHERE cooperative_id=$cooperativeId" : '';
        return $this->rawQuery("SELECT status, COUNT(*) as count FROM orders $where GROUP BY status");
    }
}
