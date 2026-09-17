<?php
class InventoryModel extends Model {
    protected string $table = 'inventories';

    public function getAllWithDetails(int $page = 1, int $perPage = 15, string $search = '', int $cooperativeId = 0, int $cropId = 0, string $dateFrom = '', string $dateTo = ''): array {
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
        $rows = $stmt->fetchAll();
        if ($dateFrom || $dateTo) { $rows = $this->applyPeriodBalances($rows, $dateFrom, $dateTo); }
        return ['data' => $rows, 'total' => $total, 'per_page' => $perPage, 'current_page' => $page, 'last_page' => max(1,(int)ceil($total/$perPage))];
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

    public function getSummaryStats(int $cooperativeId = 0, int $cropId = 0, string $dateFrom = '', string $dateTo = ''): array {
        $where = []; $params = [];
        if ($cooperativeId) { $where[] = 'cooperative_id=?'; $params[] = $cooperativeId; }
        if ($cropId)        { $where[] = 'crop_id=?'; $params[] = $cropId; }
        if ($dateFrom)      { $where[] = 'harvest_date>=?'; $params[] = $dateFrom; }
        if ($dateTo)        { $where[] = 'harvest_date<=?'; $params[] = $dateTo; }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(qty_opening),0) as total_opening,
                    COALESCE(SUM(qty_received),0) as total_received,
                    COALESCE(SUM(qty_available),0) as total_available,
                    COALESCE(SUM(qty_reserved),0) as total_reserved,
                    COALESCE(SUM(qty_sold),0) as total_sold,
                    COALESCE(SUM(qty_damaged),0) as total_damaged,
                    COALESCE(SUM(qty_sold + qty_damaged),0) as total_out,
                    COALESCE(SUM(qty_available * asking_price),0) as total_value
             FROM inventories $whereStr"
        );
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function reserveStock(int $id, float $qty): bool {
        return $this->rawExecute(
            "UPDATE inventories SET qty_available=qty_available-?, qty_reserved=qty_reserved+? WHERE id=? AND qty_available>=?",
            [$qty, $qty, $id, $qty]
        );
    }

    public function confirmSale(int $id, float $qty): bool {
        $updated = $this->rawExecute(
            "UPDATE inventories SET qty_reserved=qty_reserved-?, qty_sold=qty_sold+? WHERE id=?",
            [$qty, $qty, $id]
        );
        if ($updated) { $this->recordMovement($id, 'out', $qty, 'order'); }
        return $updated;
    }

    public function getPeriodSummary(int $cooperativeId, int $cropId, string $dateFrom, string $dateTo): array {
        $result = $this->getAllWithDetails(1, 100000, '', $cooperativeId, $cropId, $dateFrom, $dateTo);
        $summary = ['total_opening'=>0,'total_received'=>0,'total_out'=>0,'total_available'=>0,'total_reserved'=>0,'total_value'=>0];
        foreach ($result['data'] as $row) {
            $summary['total_opening'] += (float)$row['qty_opening'];
            $summary['total_received'] += (float)$row['qty_received'];
            $summary['total_out'] += (float)$row['qty_sold'] + (float)$row['qty_damaged'];
            $summary['total_available'] += (float)$row['qty_available'];
            $summary['total_reserved'] += (float)$row['qty_reserved'];
            $summary['total_value'] += (float)$row['qty_available'] * (float)$row['asking_price'];
        }
        return $summary;
    }

    public function stockIn(int $id, float $qty): bool {
        $updated = $this->rawExecute(
            "UPDATE inventories
             SET qty_received=qty_received+?, qty_available=qty_available+?, status='available'
             WHERE id=?",
            [$qty, $qty, $id]
        );
        if ($updated) { $this->recordMovement($id, 'in', $qty); }
        return $updated;
    }

    public function stockOut(int $id, float $qty): bool {
        $updated = $this->rawExecute(
            "UPDATE inventories
             SET qty_available=qty_available-?, qty_sold=qty_sold+?,
                 status=CASE WHEN qty_available-? <= 0 THEN 'sold' ELSE status END
             WHERE id=? AND qty_available>=?",
            [$qty, $qty, $qty, $id, $qty]
        );
        if ($updated) { $this->recordMovement($id, 'out', $qty); }
        return $updated;
    }

    public function recordMovement(int $inventoryId, string $type, float $qty, ?string $referenceType = null, ?int $referenceId = null, ?string $date = null): bool {
        return $this->rawExecute(
            "INSERT INTO inventory_movements (inventory_id,movement_type,quantity,movement_date,reference_type,reference_id) VALUES (?,?,?,?,?,?)",
            [$inventoryId, $type, $qty, $date ?: date('Y-m-d H:i:s'), $referenceType, $referenceId]
        );
    }

    private function applyPeriodBalances(array $rows, string $dateFrom, string $dateTo): array {
        if (!$rows) { return $rows; }
        $ids = array_map(static fn($row) => (int)$row['id'], $rows);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $from = $dateFrom ?: '1000-01-01';
        $to = $dateTo ? $dateTo . ' 23:59:59' : '9999-12-31 23:59:59';
        $sql = "SELECT inventory_id,
                       SUM(CASE WHEN movement_date < ? THEN IF(movement_type='out',-quantity,quantity) ELSE 0 END) opening_balance,
                       SUM(CASE WHEN movement_date >= ? AND movement_date <= ? AND movement_type IN ('opening','in') THEN quantity ELSE 0 END) period_in,
                       SUM(CASE WHEN movement_date >= ? AND movement_date <= ? AND movement_type='out' THEN quantity ELSE 0 END) period_out
                FROM inventory_movements WHERE inventory_id IN ($placeholders) GROUP BY inventory_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$from, $from, $to, $from, $to, ...$ids]);
        $balances = [];
        foreach ($stmt->fetchAll() as $balance) { $balances[(int)$balance['inventory_id']] = $balance; }
        foreach ($rows as &$row) {
            $balance = $balances[(int)$row['id']] ?? ['opening_balance'=>0,'period_in'=>0,'period_out'=>0];
            $row['qty_opening'] = (float)$balance['opening_balance'];
            $row['qty_received'] = (float)$balance['period_in'];
            $row['qty_sold'] = (float)$balance['period_out'];
            $row['qty_damaged'] = 0;
            $row['qty_available'] = $row['qty_opening'] + $row['qty_received'] - $row['qty_sold'];
            $row['status'] = $row['qty_available'] > 0 ? 'available' : 'sold';
        }
        unset($row);
        return $rows;
    }
}
