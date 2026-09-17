<?php $title = 'Edit Inventory'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-boxes text-success me-2"></i>Edit Stock</h5>
  <a href="<?= APP_URL ?>/cooperative/inventory" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Back
  </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body">
    <form method="POST" action="<?= APP_URL ?>/cooperative/inventory/<?= $item['id'] ?>/update">
      <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Crop</label>
          <select name="crop_id" class="form-select" disabled>
            <?php foreach ($crops as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $item['crop_id'] == $c['id'] ? 'selected' : '' ?>><?= Helper::e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Warehouse</label>
          <select name="warehouse_id" class="form-select">
            <option value="">— None —</option>
            <?php foreach ($warehouses as $w): ?>
            <option value="<?= $w['id'] ?>" <?= ($item['warehouse_id'] ?? '') == $w['id'] ? 'selected' : '' ?>><?= Helper::e($w['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Available Quantity (kg)</label>
          <input type="number" class="form-control bg-light" value="<?= $item['qty_available'] ?>" readonly>
          <small class="text-muted">Use Stock In or Stock Out from the inventory page to change this balance.</small>
        </div>
        <div class="col-md-4">
          <label class="form-label">Buying Price (RWF/kg)</label>
          <input type="number" name="buying_price" class="form-control" min="0" step="0.01" value="<?= $item['buying_price'] ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Asking Price (RWF/kg)</label>
          <input type="number" name="asking_price" class="form-control" min="0" step="0.01" value="<?= $item['asking_price'] ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Grade</label>
          <select name="grade" class="form-select">
            <?php foreach (['A','B','C'] as $g): ?>
            <option value="<?= $g ?>" <?= $item['grade'] === $g ? 'selected' : '' ?>>Grade <?= $g ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <?php foreach (['available','reserved','sold','expired'] as $s): ?>
            <option value="<?= $s ?>" <?= $item['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Expiry Date</label>
          <input type="date" name="expiry_date" class="form-control" value="<?= $item['expiry_date'] ?? '' ?>">
        </div>
      </div>
      <div class="mt-4">
        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Update Stock</button>
        <a href="<?= APP_URL ?>/cooperative/inventory" class="btn btn-outline-secondary ms-2">Cancel</a>
      </div>
    </form>
  </div>
</div>
