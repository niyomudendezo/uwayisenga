<?php
class FarmerModel extends Model {
    protected $table = 'farmers';

    public function getAllWithDetails($page = 1, $perPage = 15, $search = '', $districtId = 0): array {
        $where = []; $params = [];
        if ($search)     { $where[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR f.farm_name LIKE ?)"; $params = array_merge($params, ["%$search%","%$search%","%$search%","%$search%"]); }
        if ($districtId) { $where[] = "f.district_id=?"; $params[] = $districtId; }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM farmers f JOIN users u ON f.user_id=u.id $whereStr");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare(
            "SELECT f.*, u.first_name, u.last_name, u.email, u.phone, u.status, u.avatar,
                    d.name as district_name, co.name as cooperative_name
             FROM farmers f
             JOIN users u ON f.user_id=u.id
             LEFT JOIN districts d ON f.district_id=d.id
             LEFT JOIN cooperatives co ON f.cooperative_id=co.id
             $whereStr ORDER BY u.created_at DESC LIMIT $perPage OFFSET $offset"
        );
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total, 'per_page' => $perPage, 'current_page' => $page, 'last_page' => max(1,(int)ceil($total/$perPage))];
    }

    public function findByUserId($userId): array|false {
        $stmt = $this->db->prepare(
            "SELECT f.*, u.first_name, u.last_name, u.email, u.phone, u.avatar, u.status,
                    d.name as district_name, co.name as cooperative_name
             FROM farmers f JOIN users u ON f.user_id=u.id
             LEFT JOIN districts d ON f.district_id=d.id
             LEFT JOIN cooperatives co ON f.cooperative_id=co.id
             WHERE f.user_id=?"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function getHarvestHistory($farmerId): array {
        $stmt = $this->db->prepare(
            "SELECT h.*, c.name as crop_name, c.unit FROM harvests h
             JOIN crops c ON h.crop_id=c.id WHERE h.farmer_id=? ORDER BY h.harvest_date DESC"
        );
        $stmt->execute([$farmerId]);
        return $stmt->fetchAll();
    }

    public function getTotalHarvest($farmerId): float {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(quantity),0) FROM harvests WHERE farmer_id=?");
        $stmt->execute([$farmerId]);
        return (float) $stmt->fetchColumn();
    }

    public function getTopCrops($farmerId): array {
        $stmt = $this->db->prepare(
            "SELECT c.name, SUM(h.quantity) as total_qty, c.unit
             FROM harvests h JOIN crops c ON h.crop_id=c.id
             WHERE h.farmer_id=? GROUP BY c.id ORDER BY total_qty DESC LIMIT 5"
        );
        $stmt->execute([$farmerId]);
        return $stmt->fetchAll();
    }
}
