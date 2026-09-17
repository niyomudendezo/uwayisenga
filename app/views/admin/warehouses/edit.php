<?php $title = 'Edit Warehouse'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Warehouse</h4>
  <a href="<?= APP_URL ?>/admin/warehouses" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="card border-0 shadow-sm" style="max-width:600px">
  <div class="card-body">
    <form method="POST" action="<?= APP_URL ?>/admin/warehouses/<?= $warehouse['id'] ?>/update">
      <?= Auth::csrfField() ?>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Name <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" value="<?= Helper::e($warehouse['name']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Cooperative</label>
          <select name="cooperative_id" class="form-select">
            <option value="">— None —</option>
            <?php foreach ($cooperatives as $c): ?>
              <option value="<?= $c['id'] ?>" <?= $warehouse['cooperative_id']==$c['id'] ? 'selected':'' ?>>
                <?= Helper::e($c['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">District</label>
          <select name="district_id" class="form-select">
            <option value="">— None —</option>
            <?php foreach ($districts as $d): ?>
              <option value="<?= $d['id'] ?>" <?= $warehouse['district_id']==$d['id'] ? 'selected':'' ?>>
                <?= Helper::e($d['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Location</label>
          <input type="text" name="location" class="form-control" value="<?= Helper::e($warehouse['location'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Capacity</label>
          <input type="number" name="capacity" class="form-control" min="0" step="0.01" value="<?= $warehouse['capacity'] ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Unit</label>
          <select name="capacity_unit" class="form-select">
            <?php foreach (['kg','ton','bags'] as $u): ?>
              <option value="<?= $u ?>" <?= $warehouse['capacity_unit']===$u ? 'selected':'' ?>><?= $u ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="active"   <?= $warehouse['status']==='active'   ? 'selected':'' ?>>Active</option>
            <option value="inactive" <?= $warehouse['status']==='inactive' ? 'selected':'' ?>>Inactive</option>
          </select>
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <a href="<?= APP_URL ?>/admin/warehouses" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>
