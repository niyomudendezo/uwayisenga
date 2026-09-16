<?php $title = 'Edit User'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-pencil text-success me-2"></i>Edit User</h5>
  <a href="<?= APP_URL ?>/admin/users" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body">
    <form method="POST" action="<?= APP_URL ?>/admin/users/<?= $user['id'] ?>/edit">
      <?= Auth::csrfField() ?>
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label fw-semibold small">First Name *</label><input type="text" name="first_name" class="form-control" value="<?= Helper::e($user['first_name']) ?>" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold small">Last Name *</label><input type="text" name="last_name" class="form-control" value="<?= Helper::e($user['last_name']) ?>" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold small">Phone</label><input type="text" name="phone" class="form-control" value="<?= Helper::e($user['phone'] ?? '') ?>"></div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small">Role *</label>
          <select name="role_id" class="form-select" required>
            <?php foreach ($roles as $r): ?><option value="<?= $r['id'] ?>" <?= $user['role_id'] == $r['id'] ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$r['name'])) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small">District</label>
          <select name="district_id" class="form-select">
            <option value="">Select District</option>
            <?php foreach ($districts as $d): ?><option value="<?= $d['id'] ?>" <?= $user['district_id'] == $d['id'] ? 'selected' : '' ?>><?= Helper::e($d['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small">Status</label>
          <select name="status" class="form-select">
            <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            <option value="suspended" <?= $user['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
          </select>
        </div>
        <div class="col-md-6"><label class="form-label fw-semibold small">New Password <small class="text-muted">(leave blank to keep current)</small></label><input type="password" name="password" class="form-control"></div>
        <div class="col-12"><button type="submit" class="btn btn-success"><i class="bi bi-save me-2"></i>Update User</button></div>
      </div>
    </form>
  </div>
</div>
