<?php
class InventoryModel extends Model {
    protected string $table = 'inventories';

    public function getAllWithDetails(int $page = 1, int $perPage = 15, string $search = '', int $cooperativeId = 0, int $cropId = 0): array {
        $where = []; $params = [];
        if ($search)       { $where[] = "(c.name LIKE ? OR co.name LIKE ?)"; $params = array_merge($params, ["%$search%","%$search%"]); }
        if ($cooperativeId){ $where[] = "i.cooperative_id=?"; $params[] = $cooperativeId; }
        if ($cropId)       { $where[] = "i.crop_id=?"; $params[] = $cropId; }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM inventories i JOIN crops c ON i.crop_id=c.id JOIN cooperatives co ON i.cooperative_id=co.id $whereStr");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare(
            "SELECT i.*, c.name as crop_name, c.unit as crop_unit, co.name as cooperative_name,
                    w.name as warehouse_name
             FROM inventories i
             JOIN crops c ON i.crop_id=c.id
             JOIN cooperatives co ON i.cooperative_id=co.id
             LEFT JOIN warehouses w ON i.warehouse_id=w.id
             $whereStr ORDER BY i.updated_at DESC LIMIT $perPage OFFSET $offset"
        );
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total, 'per_page' => $perPage, 'current_page' => $page, 'last_page' => max(1,(int)ceil($total/$perPage))];
    }

    public function getAvailableForBuyers(string $search = '', int $districtId = 0, int $cropId = 0, float $minQty = 0): array {
        $where = ["i.status='available'", "i.qty_available > 0"]; $params = [];
        if ($search)    { $where[] = "(c.name LIKE ? OR co.name LIKE ?)"; $params = array_merge($params, ["%$search%","%$search%"]); }
        if ($districtId){ $where[] = "co.district_id=?"; $params[] = $districtId; }
        if ($cropId)    { $where[] = "i.crop_id=?"; $params[] = $cropId; }
        if ($minQty)    { $where[] = "i.qty_available >= ?"; $params[] = $minQty; }
        $whereStr = 'WHERE ' . implode(' AND ', $where);

        $stmt = $this->db->prepare(
            "SELECT i.*, c.name as crop_name, c.unit as crop_unit, c.variety,
                    co.name as cooperative_name, co.phone as coop_phone,
                    d.name as district_name
             FROM inventories i
             JOIN crops c ON i.crop_id=c.id
             JOIN cooperatives co ON i.cooperative_id=co.id
             LEFT JOIN districts d ON co.district_id=d.id
             $whereStr ORDER BY i.asking_price ASC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getSummaryStats(int $cooperativeId = 0): array {
        $where = $cooperativeId ? "WHERE cooperative_id=$cooperativeId" : '';
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(qty_available),0) as total_available,
                    COALESCE(SUM(qty_reserved),0) as total_reserved,
                    COALESCE(SUM(qty_sold),0) as total_sold,
                    COALESCE(SUM(qty_damaged),0) as total_damaged,
                    COALESCE(SUM(qty_available * asking_price),0) as total_value
             FROM inventories $where"
        );
        return $stmt->fetch();
    }

    public function reserveStock(int $id, float $qty): bool {
        return $this->rawExecute(
            "UPDATE inventories SET qty_available=qty_available-?, qty_reserved=qty_reserved+? WHERE id=? AND qty_available>=?",
            [$qty, $qty, $id, $qty]
        );
    }

    public function confirmSale(int $id, float $qty): bool {
        return $this->rawExecute(
            "UPDATE inventories SET qty_reserved=qty_reserved-?, qty_sold=qty_sold+? WHERE id=?",
            [$qty, $qty, $id]
        );
    }
}
