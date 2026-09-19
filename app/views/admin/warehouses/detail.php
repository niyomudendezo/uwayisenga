<?php $title = $warehouse['name']; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0"><i class="bi bi-building-fill me-2"></i><?= Helper::e($warehouse['name']) ?></h4>
  <div>
    <a href="<?= APP_URL ?>/admin/warehouses/<?= $warehouse['id'] ?>/edit" class="btn btn-primary btn-sm">
      <i class="bi bi-pencil"></i> Edit
    </a>
    <a href="<?= APP_URL ?>/admin/warehouses" class="btn btn-outline-secondary btn-sm">Back</a>
  </div>
</div>

<!-- Info Card -->
<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <h6 class="card-title text-muted mb-3">Warehouse Info</h6>
        <table class="table table-sm table-borderless mb-0">
          <tr><th class="text-muted" width="140">Name</th><td><?= Helper::e($warehouse['name']) ?></td></tr>
          <tr><th class="text-muted">Cooperative</th><td><?= Helper::e($warehouse['cooperative_name'] ?? '—') ?></td></tr>
          <tr><th class="text-muted">District</th><td><?= Helper::e($warehouse['district_name'] ?? '—') ?></td></tr>
          <tr><th class="text-muted">Location</th><td><?= Helper::e($warehouse['location'] ?? '—') ?></td></tr>
          <tr><th class="text-muted">Capacity</th><td><?= $warehouse['capacity'] ? number_format($warehouse['capacity']) . ' ' . $warehouse['capacity_unit'] : '—' ?></td></tr>
          <tr><th class="text-muted">Status</th>
            <td><span class="badge bg-<?= $warehouse['status']==='active' ? 'success' : 'secondary' ?>"><?= ucfirst($warehouse['status']) ?></span></td>
          </tr>
          <tr><th class="text-muted">Created</th><td><?= date('d M Y', strtotime($warehouse['created_at'])) ?></td></tr>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <h6 class="card-title text-muted mb-3">Stock Summary</h6>
        <?php
          $totalAvail = array_sum(array_column($inventory, 'qty_available'));
          $totalReserved = array_sum(array_column($inventory, 'qty_reserved'));
          $totalSold = array_sum(array_column($inventory, 'qty_sold'));
          $totalValue = array_sum(array_map(function($r) { return $r['qty_available'] * $r['asking_price']; }, $inventory));
        ?>
        <div class="row g-2 text-center">
          <div class="col-6">
            <div class="bg-light rounded p-3">
              <div class="fs-4 fw-bold text-success"><?= number_format($totalAvail) ?> kg</div>
              <div class="small text-muted">Available</div>
            </div>
          </div>
          <div class="col-6">
            <div class="bg-light rounded p-3">
              <div class="fs-4 fw-bold text-warning"><?= number_format($totalReserved) ?> kg</div>
              <div class="small text-muted">Reserved</div>
            </div>
          </div>
          <div class="col-6">
            <div class="bg-light rounded p-3">
              <div class="fs-4 fw-bold text-secondary"><?= number_format($totalSold) ?> kg</div>
              <div class="small text-muted">Sold</div>
            </div>
          </div>
          <div class="col-6">
            <div class="bg-light rounded p-3">
              <div class="fs-4 fw-bold text-primary"><?= number_format($totalValue) ?> RWF</div>
              <div class="small text-muted">Stock Value</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Inventory Table -->
<div class="card border-0 shadow-sm">
  <div class="card-header bg-white fw-semibold">Stored Inventory (<?= count($inventory) ?> items)</div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Crop</th>
          <th>Cooperative</th>
          <th>Grade</th>
          <th>Available</th>
          <th>Reserved</th>
          <th>Sold</th>
          <th>Asking Price</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$inventory): ?>
          <tr><td colspan="8" class="text-center text-muted py-4">No inventory stored in this warehouse.</td></tr>
        <?php endif; ?>
        <?php foreach ($inventory as $item): ?>
          <tr>
            <td class="fw-semibold"><?= Helper::e($item['crop_name']) ?></td>
            <td><?= Helper::e($item['cooperative_name']) ?></td>
            <td><span class="badge bg-secondary"><?= $item['grade'] ?></span></td>
            <td><?= number_format($item['qty_available']) ?> <?= $item['crop_unit'] ?></td>
            <td><?= number_format($item['qty_reserved']) ?></td>
            <td><?= number_format($item['qty_sold']) ?></td>
            <td><?= number_format($item['asking_price']) ?> RWF</td>
            <td><span class="badge bg-<?= $item['status']==='available' ? 'success' : 'secondary' ?>"><?= ucfirst($item['status']) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
