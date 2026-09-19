<?php
class CooperativeModel extends Model {
    protected $table = 'cooperatives';

    public function getAllWithDetails($page = 1, $perPage = 15, $search = ''): array {
        $where = ''; $params = [];
        if ($search) { $where = "WHERE c.name LIKE ? OR c.registration_no LIKE ?"; $params = ["%$search%","%$search%"]; }

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM cooperatives c $where");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare(
            "SELECT c.*, d.name as district_name, u.first_name, u.last_name,
                    (SELECT COUNT(*) FROM farmers f WHERE f.cooperative_id=c.id) as member_count
             FROM cooperatives c
             LEFT JOIN districts d ON c.district_id=d.id
             LEFT JOIN users u ON c.manager_id=u.id
             $where ORDER BY c.created_at DESC LIMIT $perPage OFFSET $offset"
        );
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total, 'per_page' => $perPage, 'current_page' => $page, 'last_page' => max(1,(int)ceil($total/$perPage))];
    }

    public function findWithDetails($id): array|false {
        $stmt = $this->db->prepare(
            "SELECT c.*, d.name as district_name, u.first_name, u.last_name, u.email as manager_email
             FROM cooperatives c
             LEFT JOIN districts d ON c.district_id=d.id
             LEFT JOIN users u ON c.manager_id=u.id
             WHERE c.id=?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getMembers($cooperativeId): array {
        $stmt = $this->db->prepare(
            "SELECT f.id as farmer_id, f.cooperative_id, f.farm_name, f.farm_size,
                    f.soil_type, f.irrigation,
                    u.first_name, u.last_name, u.email, u.phone, u.status,
                    u.created_at as joined_at
             FROM farmers f
             JOIN users u ON f.user_id=u.id
             WHERE f.cooperative_id=? AND u.status='active'
             ORDER BY u.first_name"
        );
        $stmt->execute([$cooperativeId]);
        return $stmt->fetchAll();
    }

    public function getInventorySummary($cooperativeId): array {
        $stmt = $this->db->prepare(
            "SELECT c.name as crop_name, c.unit,
                    SUM(i.qty_available) as available,
                    SUM(i.qty_reserved) as reserved,
                    SUM(i.qty_sold) as sold,
                    i.asking_price
             FROM inventories i JOIN crops c ON i.crop_id=c.id
             WHERE i.cooperative_id=? AND i.status='available'
             GROUP BY c.id ORDER BY available DESC"
        );
        $stmt->execute([$cooperativeId]);
        return $stmt->fetchAll();
    }

    public function getByManagerId($userId): array|false {
        $stmt = $this->db->prepare("SELECT * FROM cooperatives WHERE manager_id=? LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function getRevenueStats($cooperativeId): array {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(o.total_amount),0) as total_revenue,
                    COUNT(o.id) as total_orders,
                    COALESCE(SUM(CASE WHEN o.status='completed' THEN o.total_amount ELSE 0 END),0) as completed_revenue
             FROM orders o WHERE o.cooperative_id=?"
        );
        $stmt->execute([$cooperativeId]);
        return $stmt->fetch();
    }
}
