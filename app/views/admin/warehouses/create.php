<?php $title = 'Add Warehouse'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0"><i class="bi bi-building-fill me-2"></i>Add Warehouse</h4>
  <a href="<?= APP_URL ?>/admin/warehouses" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="card border-0 shadow-sm" style="max-width:600px">
  <div class="card-body">
    <form method="POST" action="<?= APP_URL ?>/admin/warehouses/store">
      <?= Auth::csrfField() ?>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Name <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" required autofocus>
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
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-success">Create Warehouse</button>
          <a href="<?= APP_URL ?>/admin/warehouses" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>
