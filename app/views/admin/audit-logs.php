<?php $title = 'Audit Logs'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-shield-check text-success me-2"></i>Audit Logs</h5>
  <small class="text-muted"><?= number_format($total) ?> total records</small>
</div>
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>User</th><th>Action</th><th>Module</th><th>IP Address</th><th>Time</th></tr></thead>
        <tbody>
          <?php foreach ($logs as $log): ?>
          <tr>
            <td class="small"><?= Helper::e($log['first_name'] ? $log['first_name'].' '.$log['last_name'] : 'System') ?></td>
            <td><span class="badge bg-secondary"><?= Helper::e($log['action']) ?></span></td>
            <td class="small"><?= Helper::e($log['module'] ?? '-') ?></td>
            <td class="small text-muted"><?= Helper::e($log['ip_address'] ?? '-') ?></td>
            <td class="small text-muted"><?= Helper::timeAgo($log['created_at']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php if ($last > 1): ?>
  <div class="card-footer bg-white d-flex justify-content-between align-items-center">
    <small class="text-muted">Page <?= $page ?> of <?= $last ?></small>
    <?= Helper::paginate(['total'=>$total,'per_page'=>$perPage,'current_page'=>$page,'last_page'=>$last,'data'=>$logs], APP_URL.'/admin/audit-logs') ?>
  </div>
  <?php endif; ?>
</div>
