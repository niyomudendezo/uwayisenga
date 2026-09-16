<?php $title = 'Buyers Management';
$pendingCount = count(array_filter($result['data'], fn($b) => !$b['verified']));
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-shop text-success me-2"></i>Buyers</h5>
  <?php
    $totalPending = 0;
    foreach ($result['data'] as $b) { if (!$b['verified']) $totalPending++; }
  ?>
</div>

<!-- Filter Tabs -->
<ul class="nav nav-tabs mb-3">
  <li class="nav-item">
    <a class="nav-link <?= !isset($_GET['verified']) ? 'active' : '' ?>" href="<?= APP_URL ?>/admin/buyers">
      All Buyers
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?= ($_GET['verified'] ?? '') === '0' ? 'active' : '' ?>" href="<?= APP_URL ?>/admin/buyers?verified=0">
      Pending Verification
      <?php if ($pendingUnverified ?? 0): ?>
      <span class="badge bg-warning text-dark ms-1"><?= $pendingUnverified ?></span>
      <?php endif; ?>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?= ($_GET['verified'] ?? '') === '1' ? 'active' : '' ?>" href="<?= APP_URL ?>/admin/buyers?verified=1">
      Verified
    </a>
  </li>
</ul>

<!-- Search -->
<div class="card border-0 shadow-sm rounded-3 mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <?php if (isset($_GET['verified'])): ?>
      <input type="hidden" name="verified" value="<?= Helper::e($_GET['verified']) ?>">
      <?php endif; ?>
      <div class="col-md-5">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search buyers..." value="<?= Helper::e($search) ?>">
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-success btn-sm">Search</button>
        <a href="<?= APP_URL ?>/admin/buyers<?= isset($_GET['verified']) ? '?verified='.$_GET['verified'] : '' ?>" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <th>Company</th>
            <th>Email</th>
            <th>Phone</th>
            <th>District</th>
            <th>Verified</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($result['data'] as $b): ?>
          <tr class="<?= !$b['verified'] ? 'table-warning' : '' ?>">
            <td class="fw-semibold"><?= Helper::e($b['first_name'] . ' ' . $b['last_name']) ?></td>
            <td><?= Helper::e($b['company_name'] ?? '-') ?></td>
            <td class="small"><?= Helper::e($b['email']) ?></td>
            <td class="small"><?= Helper::e($b['phone'] ?? '-') ?></td>
            <td class="small"><?= Helper::e($b['district_name'] ?? '-') ?></td>
            <td>
              <?php if ($b['verified']): ?>
              <span class="badge bg-success"><i class="bi bi-patch-check me-1"></i>Verified</span>
              <?php if (!empty($b['verified_at'])): ?>
              <div class="small text-muted"><?= Helper::formatDate($b['verified_at']) ?></div>
              <?php endif; ?>
              <?php else: ?>
              <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Pending</span>
              <?php endif; ?>
            </td>
            <td><?= Helper::statusBadge($b['status']) ?></td>
            <td>
              <?php if (!$b['verified']): ?>
              <form method="POST" action="<?= APP_URL ?>/admin/buyers/<?= $b['id'] ?>/verify" class="d-inline">
                <?= Auth::csrfField() ?>
                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Verify <?= Helper::e($b['first_name']) ?>\'s account?')">
                  <i class="bi bi-shield-check me-1"></i>Verify
                </button>
              </form>
              <?php else: ?>
              <span class="text-muted small"><i class="bi bi-check-circle text-success me-1"></i>Done</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($result['data'])): ?>
          <tr><td colspan="8" class="text-center text-muted py-4">No buyers found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php if ($result['last_page'] > 1): ?>
  <div class="card-footer bg-white d-flex justify-content-between align-items-center">
    <small class="text-muted">Showing <?= count($result['data']) ?> of <?= $result['total'] ?></small>
    <?= Helper::paginate($result, APP_URL . '/admin/buyers') ?>
  </div>
  <?php endif; ?>
</div>
