<?php
class CropModel extends Model {
    protected string $table = 'crops';

    public function getAllWithCategory(): array {
        return $this->rawQuery(
            "SELECT c.*, cc.name as category_name FROM crops c
             JOIN crop_categories cc ON c.category_id=cc.id
             WHERE c.status='active' ORDER BY cc.name, c.name"
        );
    }

    public function getCategories(): array {
        return $this->db->query("SELECT * FROM crop_categories ORDER BY name")->fetchAll();
    }

    public function getWithCategory(int $id): array|false {
        return $this->rawQueryOne(
            "SELECT c.*, cc.name as category_name FROM crops c
             JOIN crop_categories cc ON c.category_id=cc.id WHERE c.id=?", [$id]
        );
    }

    public function paginated(int $page, int $perPage, string $search = '', int $categoryId = 0): array {
        $where = []; $params = [];
        if ($search)    { $where[] = "(c.name LIKE ? OR c.variety LIKE ?)"; $params = array_merge($params, ["%$search%","%$search%"]); }
        if ($categoryId){ $where[] = "c.category_id=?"; $params[] = $categoryId; }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM crops c $whereStr");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare(
            "SELECT c.*, cc.name as category_name FROM crops c
             JOIN crop_categories cc ON c.category_id=cc.id
             $whereStr ORDER BY c.name LIMIT $perPage OFFSET $offset"
        );
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total, 'per_page' => $perPage, 'current_page' => $page, 'last_page' => max(1,(int)ceil($total/$perPage))];
    }
}
