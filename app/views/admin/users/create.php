<?php $title = 'Create User'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-person-plus text-success me-2"></i>Create User</h5>
  <a href="<?= APP_URL ?>/admin/users" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body">
    <form method="POST" action="<?= APP_URL ?>/admin/users/create">
      <?= Auth::csrfField() ?>
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label fw-semibold small">First Name *</label><input type="text" name="first_name" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold small">Last Name *</label><input type="text" name="last_name" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold small">Email *</label><input type="email" name="email" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold small">Phone</label><input type="text" name="phone" class="form-control"></div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small">Role *</label>
          <select name="role_id" class="form-select" required>
            <?php foreach ($roles as $r): ?><option value="<?= $r['id'] ?>"><?= ucfirst(str_replace('_',' ',$r['name'])) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small">District</label>
          <select name="district_id" class="form-select">
            <option value="">Select District</option>
            <?php foreach ($districts as $d): ?><option value="<?= $d['id'] ?>"><?= Helper::e($d['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small">Status</label>
          <select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select>
        </div>
        <div class="col-md-6"><label class="form-label fw-semibold small">Password *</label><input type="password" name="password" class="form-control" value="Admin@1234" required></div>
        <div class="col-12"><button type="submit" class="btn btn-success"><i class="bi bi-save me-2"></i>Create User</button></div>
      </div>
    </form>
  </div>
</div>
