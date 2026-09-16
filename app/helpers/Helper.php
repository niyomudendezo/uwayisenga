<?php
class Helper {
    public static function formatCurrency(float $amount): string {
        return number_format($amount, 0, '.', ',') . ' ' . CURRENCY;
    }

    public static function formatDate(string $date, string $format = 'd M Y'): string {
        return $date ? date($format, strtotime($date)) : '-';
    }

    public static function timeAgo(string $datetime): string {
        $diff = time() - strtotime($datetime);
        if ($diff < 60)     return 'Just now';
        if ($diff < 3600)   return (int)($diff/60) . ' min ago';
        if ($diff < 86400)  return (int)($diff/3600) . ' hr ago';
        if ($diff < 604800) return (int)($diff/86400) . ' days ago';
        return date('d M Y', strtotime($datetime));
    }

    public static function statusBadge(string $status): string {
        $map = [
            'active'      => 'success', 'inactive'   => 'secondary',
            'pending'     => 'warning', 'approved'   => 'success',
            'rejected'    => 'danger',  'paid'       => 'info',
            'completed'   => 'success', 'cancelled'  => 'danger',
            'in_delivery' => 'primary', 'available'  => 'success',
            'reserved'    => 'warning', 'sold'       => 'info',
            'expired'     => 'danger',  'suspended'  => 'danger',
            'High'        => 'success', 'Medium'     => 'warning',
            'Low'         => 'danger',  'confirmed'  => 'success',
            'failed'      => 'danger',  'dispatched' => 'primary',
            'delivered'   => 'success',
        ];
        $color = $map[$status] ?? 'secondary';
        return "<span class=\"badge bg-{$color}\">" . ucfirst(str_replace('_', ' ', $status)) . "</span>";
    }

    public static function generateOrderNo(): string {
        return 'ORD-' . date('Y') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    public static function uploadFile(array $file, string $dir, array $allowed = ['jpg','jpeg','png','pdf']): string|false {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) return false;
        if ($file['size'] > 5 * 1024 * 1024) return false;

        $uploadDir = UPLOAD_PATH . '/' . $dir . '/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = uniqid() . '_' . time() . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return $dir . '/' . $filename;
        }
        return false;
    }

    public static function paginate(array $result, string $baseUrl): string {
        $total   = $result['total'];
        $perPage = $result['per_page'];
        $current = $result['current_page'];
        $last    = $result['last_page'];

        if ($last <= 1) return '';

        $html = '<nav><ul class="pagination pagination-sm mb-0">';
        $html .= '<li class="page-item' . ($current <= 1 ? ' disabled' : '') . '">';
        $html .= '<a class="page-link" href="' . $baseUrl . '?page=' . ($current - 1) . '">«</a></li>';

        for ($i = max(1, $current - 2); $i <= min($last, $current + 2); $i++) {
            $html .= '<li class="page-item' . ($i === $current ? ' active' : '') . '">';
            $html .= '<a class="page-link" href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a></li>';
        }

        $html .= '<li class="page-item' . ($current >= $last ? ' disabled' : '') . '">';
        $html .= '<a class="page-link" href="' . $baseUrl . '?page=' . ($current + 1) . '">»</a></li>';
        $html .= '</ul></nav>';
        return $html;
    }

    public static function e(string $str): string {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    public static function flash(): ?array {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    public static function truncate(string $text, int $length = 80): string {
        return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
    }

    public static function numberFormat(float $n): string {
        return number_format($n, 2);
    }

    public static function percentChange(float $old, float $new): string {
        if ($old == 0) return '0%';
        $pct = (($new - $old) / $old) * 100;
        $sign = $pct >= 0 ? '+' : '';
        $color = $pct >= 0 ? 'text-success' : 'text-danger';
        return "<span class=\"{$color}\">{$sign}" . round($pct, 1) . "%</span>";
    }
}
