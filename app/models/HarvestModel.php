<?php
class HarvestModel extends Model {
    protected string $table = 'harvests';

    public function getAllWithDetails(int $page = 1, int $perPage = 15, string $search = '', int $farmerId = 0, int $cooperativeId = 0): array {
        $where = []; $params = [];
        if ($search)       { $where[] = "(c.name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)"; $params = array_merge($params, ["%$search%","%$search%","%$search%"]); }
        if ($farmerId)     { $where[] = "h.farmer_id=?"; $params[] = $farmerId; }
        if ($cooperativeId){ $where[] = "(h.cooperative_id=? OR f.cooperative_id=?)"; $params[] = $cooperativeId; $params[] = $cooperativeId; }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM harvests h JOIN crops c ON h.crop_id=c.id JOIN farmers f ON h.farmer_id=f.id JOIN users u ON f.user_id=u.id $whereStr");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare(
            "SELECT h.*, c.name as crop_name, c.unit as crop_unit,
                    u.first_name, u.last_name, co.name as cooperative_name
             FROM harvests h
             JOIN crops c ON h.crop_id=c.id
             JOIN farmers f ON h.farmer_id=f.id
             JOIN users u ON f.user_id=u.id
             LEFT JOIN cooperatives co ON COALESCE(h.cooperative_id, f.cooperative_id)=co.id
             $whereStr ORDER BY h.harvest_date DESC LIMIT $perPage OFFSET $offset"
        );
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total, 'per_page' => $perPage, 'current_page' => $page, 'last_page' => max(1,(int)ceil($total/$perPage))];
    }

    public function getSeasonalStats(): array {
        return $this->rawQuery(
            "SELECT season, SUM(quantity) as total_qty, COUNT(*) as harvest_count,
                    COUNT(DISTINCT farmer_id) as farmer_count
             FROM harvests GROUP BY season ORDER BY season DESC LIMIT 8"
        );
    }

    public function getCropProductionStats(): array {
        return $this->rawQuery(
            "SELECT c.name as crop_name, c.unit, SUM(h.quantity) as total_qty,
                    COUNT(DISTINCT h.farmer_id) as farmer_count
             FROM harvests h JOIN crops c ON h.crop_id=c.id
             WHERE h.harvest_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY c.id ORDER BY total_qty DESC LIMIT 10"
        );
    }
}
