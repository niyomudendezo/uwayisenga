<?php $title = 'Add Inventory'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-boxes text-success me-2"></i>Add Stock</h5>
  <a href="<?= APP_URL ?>/cooperative/inventory" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Back
  </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body">
    <form method="POST" action="<?= APP_URL ?>/cooperative/inventory/store">
      <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Crop <span class="text-danger">*</span></label>
          <select name="crop_id" class="form-select" required>
            <option value="">— Select Crop —</option>
            <?php foreach ($crops as $c): ?>
            <option value="<?= $c['id'] ?>"><?= Helper::e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Warehouse</label>
          <select name="warehouse_id" class="form-select">
            <option value="">— None —</option>
            <?php foreach ($warehouses as $w): ?>
            <option value="<?= $w['id'] ?>"><?= Helper::e($w['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Harvest (optional)</label>
          <select name="harvest_id" class="form-select">
            <option value="">— None —</option>
            <?php foreach ($harvests as $h): ?>
            <option value="<?= $h['id'] ?>"><?= Helper::e($h['first_name'].' '.$h['last_name']) ?> — <?= Helper::e($h['crop_name']) ?> — <?= number_format($h['quantity']) ?> kg (<?= Helper::formatDate($h['harvest_date']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Grade</label>
          <select name="grade" class="form-select">
            <option value="A">Grade A</option>
            <option value="B">Grade B</option>
            <option value="C">Grade C</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Opening Stock (kg) <span class="text-danger">*</span></label>
          <input type="number" name="qty_opening" class="form-control" min="0.1" step="0.1" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Buying Price (RWF/kg)</label>
          <input type="number" name="buying_price" class="form-control" min="0" step="0.01">
        </div>
        <div class="col-md-4">
          <label class="form-label">Asking Price (RWF/kg) <span class="text-danger">*</span></label>
          <input type="number" name="asking_price" class="form-control" min="0" step="0.01" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Harvest Date</label>
          <input type="date" name="harvest_date" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">Expiry Date</label>
          <input type="date" name="expiry_date" class="form-control">
        </div>
      </div>
      <div class="mt-4">
        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Save Stock</button>
        <a href="<?= APP_URL ?>/cooperative/inventory" class="btn btn-outline-secondary ms-2">Cancel</a>
      </div>
    </form>
  </div>
</div>
