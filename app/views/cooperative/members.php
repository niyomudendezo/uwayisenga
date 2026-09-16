<?php $title = 'Members'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-people text-success me-2"></i>Members</h5>
</div>

<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Farm Name</th>
            <th>Farm Size</th>
            <th>Joined</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($members as $m): ?>
          <tr>
            <td class="fw-semibold"><?= Helper::e($m['first_name'] . ' ' . $m['last_name']) ?></td>
            <td class="small"><?= Helper::e($m['email']) ?></td>
            <td class="small"><?= Helper::e($m['phone'] ?? '-') ?></td>
            <td><?= Helper::e($m['farm_name'] ?? '-') ?></td>
            <td><?= $m['farm_size'] ? $m['farm_size'] . ' ha' : '-' ?></td>
            <td class="small text-muted"><?= Helper::formatDate($m['joined_at'] ?? '') ?></td>
            <td><?= Helper::statusBadge($m['status'] ?? 'active') ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($members)): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">No members found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
