<?php
class UserModel extends Model {
    protected $table = 'users';

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id=r.id WHERE u.email=?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findWithRole($id) {
        $stmt = $this->db->prepare("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id=r.id WHERE u.id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAllWithRoles($page = 1, $perPage = 15, $search = '', $role = ''): array {
        $where = []; $params = [];
        if ($search) { $where[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)"; $params = array_merge($params, ["%$search%","%$search%","%$search%"]); }
        if ($role)   { $where[] = "r.name=?"; $params[] = $role; }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total  = (int) $this->db->prepare("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id=r.id $whereStr")->execute($params) ? $this->db->prepare("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id=r.id $whereStr")->execute($params) : 0;

        // Re-execute for count
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id=r.id $whereStr");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("SELECT u.*, r.name as role_name, d.name as district_name FROM users u JOIN roles r ON u.role_id=r.id LEFT JOIN districts d ON u.district_id=d.id $whereStr ORDER BY u.created_at DESC LIMIT $perPage OFFSET $offset");
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total, 'per_page' => $perPage, 'current_page' => $page, 'last_page' => max(1,(int)ceil($total/$perPage))];
    }

    public function setResetToken($email, $token): bool {
        return $this->db->prepare("UPDATE users SET reset_token=?, reset_token_expires=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email=?")->execute([$token, $email]);
    }

    public function findByResetToken($token) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE reset_token=? AND reset_token_expires > NOW()");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public function updateLastLogin($id){
        $this->db->prepare("UPDATE users SET last_login=NOW() WHERE id=?")->execute([$id]);
    }

    public function countByRole($role): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id=r.id WHERE r.name=?");
        $stmt->execute([$role]);
        return (int) $stmt->fetchColumn();
    }

    public function getRoles(): array {
        return $this->db->query("SELECT * FROM roles ORDER BY id")->fetchAll();
    }
}
