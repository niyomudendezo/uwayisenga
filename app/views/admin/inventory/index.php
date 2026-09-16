<?php $title = 'Inventory'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-boxes text-success me-2"></i>Inventory</h5>
</div>

<!-- Summary Stats -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <div class="text-success fw-bold fs-4"><?= number_format($stats['total_available']) ?> kg</div>
        <div class="text-muted small">Available</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <div class="text-warning fw-bold fs-4"><?= number_format($stats['total_reserved']) ?> kg</div>
        <div class="text-muted small">Reserved</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <div class="text-info fw-bold fs-4"><?= number_format($stats['total_sold']) ?> kg</div>
        <div class="text-muted small">Sold</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <div class="text-success fw-bold fs-4"><?= Helper::formatCurrency($stats['total_value']) ?></div>
        <div class="text-muted small">Total Value</div>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-3 mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="<?= Helper::e($search) ?>">
      </div>
      <div class="col-md-3">
        <select name="crop" class="form-select form-select-sm">
          <option value="">All Crops</option>
          <?php foreach ($crops as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $cropId == $c['id'] ? 'selected' : '' ?>><?= Helper::e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <select name="cooperative" class="form-select form-select-sm">
          <option value="">All Cooperatives</option>
          <?php foreach ($cooperatives as $co): ?>
          <option value="<?= $co['id'] ?>" <?= $coopId == $co['id'] ? 'selected' : '' ?>><?= Helper::e($co['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-success btn-sm">Filter</button>
        <a href="<?= APP_URL ?>/admin/inventory" class="btn btn-outline-secondary btn-sm">Reset</a>
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
            <th>Crop</th>
            <th>Cooperative</th>
            <th>Warehouse</th>
            <th>Available</th>
            <th>Reserved</th>
            <th>Sold</th>
            <th>Grade</th>
            <th>Asking Price</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($result['data'] as $item): ?>
          <tr>
            <td class="fw-semibold"><?= Helper::e($item['crop_name']) ?></td>
            <td class="small"><?= Helper::e($item['cooperative_name']) ?></td>
            <td class="small"><?= Helper::e($item['warehouse_name'] ?? 'N/A') ?></td>
            <td class="text-success fw-semibold"><?= number_format($item['qty_available']) ?> <?= $item['crop_unit'] ?></td>
            <td class="text-warning"><?= number_format($item['qty_reserved']) ?></td>
            <td class="text-info"><?= number_format($item['qty_sold']) ?></td>
            <td><span class="badge bg-success">Grade <?= Helper::e($item['grade']) ?></span></td>
            <td class="fw-semibold"><?= Helper::formatCurrency($item['asking_price']) ?>/kg</td>
            <td><?= Helper::statusBadge($item['status']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($result['data'])): ?>
          <tr><td colspan="9" class="text-center text-muted py-4">No inventory records found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php if ($result['last_page'] > 1): ?>
  <div class="card-footer bg-white d-flex justify-content-between align-items-center">
    <small class="text-muted">Showing <?= count($result['data']) ?> of <?= $result['total'] ?> items</small>
    <?= Helper::paginate($result, APP_URL . '/admin/inventory') ?>
  </div>
  <?php endif; ?>
</div>
