<?php $title = 'Warehouses'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0"><i class="bi bi-building-fill me-2"></i>Warehouses</h4>
  <a href="<?= APP_URL ?>/admin/warehouses/create" class="btn btn-success btn-sm">
    <i class="bi bi-plus-lg"></i> Add Warehouse
  </a>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card text-center border-0 shadow-sm">
      <div class="card-body py-3">
        <div class="fs-3 fw-bold text-primary"><?= $stats['total'] ?></div>
        <div class="small text-muted">Total</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center border-0 shadow-sm">
      <div class="card-body py-3">
        <div class="fs-3 fw-bold text-success"><?= $stats['active'] ?></div>
        <div class="small text-muted">Active</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center border-0 shadow-sm">
      <div class="card-body py-3">
        <div class="fs-3 fw-bold text-secondary"><?= $stats['inactive'] ?></div>
        <div class="small text-muted">Inactive</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center border-0 shadow-sm">
      <div class="card-body py-3">
        <div class="fs-3 fw-bold text-info"><?= number_format($stats['total_capacity']) ?></div>
        <div class="small text-muted">Total Capacity (kg)</div>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<form class="row g-2 mb-3" method="GET">
  <div class="col-md-5">
    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name or location…" value="<?= Helper::e($search) ?>">
  </div>
  <div class="col-md-3">
    <select name="status" class="form-select form-select-sm">
      <option value="">All Status</option>
      <option value="active"   <?= $status==='active'   ? 'selected':'' ?>>Active</option>
      <option value="inactive" <?= $status==='inactive' ? 'selected':'' ?>>Inactive</option>
    </select>
  </div>
  <div class="col-auto">
    <button class="btn btn-primary btn-sm">Filter</button>
    <a href="<?= APP_URL ?>/admin/warehouses" class="btn btn-outline-secondary btn-sm">Reset</a>
  </div>
</form>

<!-- Table -->
<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Cooperative</th>
          <th>District</th>
          <th>Location</th>
          <th>Capacity</th>
          <th>Stock (kg)</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$warehouses): ?>
          <tr><td colspan="9" class="text-center text-muted py-4">No warehouses found. <a href="<?= APP_URL ?>/admin/warehouses/create">Add one</a>.</td></tr>
        <?php endif; ?>
        <?php foreach ($warehouses as $i => $w): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td class="fw-semibold"><?= Helper::e($w['name']) ?></td>
            <td><?= Helper::e($w['cooperative_name'] ?? '—') ?></td>
            <td><?= Helper::e($w['district_name'] ?? '—') ?></td>
            <td class="text-muted small"><?= Helper::e($w['location'] ?? '—') ?></td>
            <td><?= $w['capacity'] ? number_format($w['capacity']) . ' ' . $w['capacity_unit'] : '—' ?></td>
            <td><?= number_format($w['stock_qty']) ?> kg</td>
            <td>
              <span class="badge bg-<?= $w['status']==='active' ? 'success' : 'secondary' ?>">
                <?= ucfirst($w['status']) ?>
              </span>
            </td>
            <td>
              <a href="<?= APP_URL ?>/admin/warehouses/<?= $w['id'] ?>" class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
              <a href="<?= APP_URL ?>/admin/warehouses/<?= $w['id'] ?>/edit" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
              <form method="POST" action="<?= APP_URL ?>/admin/warehouses/<?= $w['id'] ?>/delete" class="d-inline"
                    onsubmit="return confirm('Delete this warehouse?')">
                <?= Auth::csrfField() ?>
                <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
