<?php $title = 'Farmers Management'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-person-badge text-success me-2"></i>Farmers</h5>
</div>
<div class="card border-0 shadow-sm rounded-3 mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-5"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search farmers..." value="<?= Helper::e($search) ?>"></div>
      <div class="col-auto"><button type="submit" class="btn btn-success btn-sm">Search</button><a href="<?= APP_URL ?>/admin/farmers" class="btn btn-outline-secondary btn-sm ms-1">Reset</a></div>
    </form>
  </div>
</div>
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Name</th><th>Farm</th><th>Size (ha)</th><th>Cooperative</th><th>District</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach ($result['data'] as $f): ?>
          <tr>
            <td>
              <div class="fw-semibold"><?= Helper::e($f['first_name'].' '.$f['last_name']) ?></div>
              <div class="small text-muted"><?= Helper::e($f['email']) ?></div>
            </td>
            <td class="small"><?= Helper::e($f['farm_name'] ?? '-') ?></td>
            <td><?= $f['farm_size'] ?? '-' ?></td>
            <td class="small"><?= Helper::e($f['cooperative_name'] ?? 'Independent') ?></td>
            <td class="small"><?= Helper::e($f['district_name'] ?? '-') ?></td>
            <td><?= Helper::statusBadge($f['status']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($result['data'])): ?><tr><td colspan="6" class="text-center text-muted py-4">No farmers found.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php if ($result['last_page'] > 1): ?>
  <div class="card-footer bg-white d-flex justify-content-between align-items-center">
    <small class="text-muted">Showing <?= count($result['data']) ?> of <?= $result['total'] ?></small>
    <?= Helper::paginate($result, APP_URL . '/admin/farmers') ?>
  </div>
  <?php endif; ?>
</div>
