<?php $title = 'Warehouses'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0"><i class="bi bi-building-fill me-2"></i>Warehouses</h4>
  <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
    <i class="bi bi-plus-lg"></i> Add Warehouse
  </button>
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
          <tr><td colspan="9" class="text-center text-muted py-4">No warehouses found.</td></tr>
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
                <?= Helper::csrfField() ?>
                <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="<?= APP_URL ?>/admin/warehouses/store" class="modal-content">
      <?= Helper::csrfField() ?>
      <div class="modal-header">
        <h5 class="modal-title">Add Warehouse</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-12">
          <label class="form-label">Name <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Cooperative</label>
          <select name="cooperative_id" class="form-select">
            <option value="">— None —</option>
            <?php foreach ($cooperatives as $c): ?>
              <option value="<?= $c['id'] ?>"><?= Helper::e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">District</label>
          <select name="district_id" class="form-select">
            <option value="">— None —</option>
            <?php foreach ($districts as $d): ?>
              <option value="<?= $d['id'] ?>"><?= Helper::e($d['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Location</label>
          <input type="text" name="location" class="form-control" placeholder="e.g. Gasabo, Kigali">
        </div>
        <div class="col-md-6">
          <label class="form-label">Capacity</label>
          <input type="number" name="capacity" class="form-control" min="0" step="0.01">
        </div>
        <div class="col-md-6">
          <label class="form-label">Unit</label>
          <select name="capacity_unit" class="form-select">
            <option value="kg">kg</option>
            <option value="ton">ton</option>
            <option value="bags">bags</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Create</button>
      </div>
    </form>
  </div>
</div>
