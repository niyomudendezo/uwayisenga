<?php
class WarehouseModel extends Model {
    protected $table = 'warehouses';

    public function getAllWithDetails($search = '', $status = ''): array {
        $where = []; $params = [];
        if ($search) { $where[] = "(w.name LIKE ? OR w.location LIKE ?)"; $params = array_merge($params, ["%$search%", "%$search%"]); }
        if ($status) { $where[] = "w.status=?"; $params[] = $status; }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $stmt = $this->db->prepare(
            "SELECT w.*, co.name as cooperative_name, d.name as district_name,
                    COALESCE(SUM(i.qty_available),0) as stock_qty,
                    COUNT(DISTINCT i.id) as inventory_count
             FROM warehouses w
             LEFT JOIN cooperatives co ON w.cooperative_id=co.id
             LEFT JOIN districts d ON w.district_id=d.id
             LEFT JOIN inventories i ON i.warehouse_id=w.id
             $whereStr
             GROUP BY w.id ORDER BY w.created_at DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id): ?array {
        $stmt = $this->db->prepare(
            "SELECT w.*, co.name as cooperative_name, d.name as district_name
             FROM warehouses w
             LEFT JOIN cooperatives co ON w.cooperative_id=co.id
             LEFT JOIN districts d ON w.district_id=d.id
             WHERE w.id=?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getInventory($warehouseId): array {
        $stmt = $this->db->prepare(
            "SELECT i.*, c.name as crop_name, c.unit as crop_unit, co.name as cooperative_name
             FROM inventories i
             JOIN crops c ON i.crop_id=c.id
             JOIN cooperatives co ON i.cooperative_id=co.id
             WHERE i.warehouse_id=? ORDER BY i.updated_at DESC"
        );
        $stmt->execute([$warehouseId]);
        return $stmt->fetchAll();
    }

    public function getStats(): array {
        $stmt = $this->db->query(
            "SELECT COUNT(*) as total,
                    SUM(status='active') as active,
                    SUM(status='inactive') as inactive,
                    COALESCE(SUM(capacity),0) as total_capacity
             FROM warehouses"
        );
        return $stmt->fetch();
    }

    public function create($data): int {
        $this->db->prepare(
            "INSERT INTO warehouses (cooperative_id,name,location,district_id,capacity,capacity_unit,status)
             VALUES (?,?,?,?,?,?,?)"
        )->execute([
            $data['cooperative_id'] ?: null,
            $data['name'],
            $data['location'] ?? '',
            $data['district_id'] ?: null,
            $data['capacity'] ?? null,
            $data['capacity_unit'] ?? 'kg',
            $data['status'] ?? 'active',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update($id, $data): bool {
        return $this->rawExecute(
            "UPDATE warehouses SET cooperative_id=?,name=?,location=?,district_id=?,capacity=?,capacity_unit=?,status=? WHERE id=?",
            [
                $data['cooperative_id'] ?: null,
                $data['name'],
                $data['location'],
                $data['district_id'] ?: null,
                $data['capacity'] ?: null,
                $data['capacity_unit'] ?? 'kg',
                $data['status'] ?? 'active',
                $id,
            ]
        );
    }

    public function delete($id): bool {
        return $this->rawExecute("DELETE FROM warehouses WHERE id=?", [$id]);
    }
}
