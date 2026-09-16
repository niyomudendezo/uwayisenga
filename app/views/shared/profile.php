<?php $title = 'My Profile'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="fw-bold mb-0"><i class="bi bi-person-circle text-success me-2"></i>My Profile</h5>
</div>

<div class="row g-4">

  <!-- Profile Info -->
  <div class="col-md-8">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white fw-semibold border-bottom">Personal Information</div>
      <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/profile" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">First Name</label>
              <input type="text" name="first_name" class="form-control" value="<?= Helper::e($user['first_name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-control" value="<?= Helper::e($user['last_name'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control bg-light" value="<?= Helper::e($user['email'] ?? '') ?>" disabled>
              <div class="form-text">Email cannot be changed.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control" value="<?= Helper::e($user['phone'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">District</label>
              <select name="district_id" class="form-select">
                <option value="">— Select District —</option>
                <?php foreach ($districts as $d): ?>
                <option value="<?= $d['id'] ?>" <?= ($user['district_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                  <?= Helper::e($d['name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Role</label>
              <input type="text" class="form-control bg-light text-capitalize" value="<?= Helper::e(str_replace('_', ' ', $user['role_name'] ?? '')) ?>" disabled>
            </div>
            <div class="col-12">
              <label class="form-label">Profile Photo</label>
              <input type="file" name="avatar" class="form-control" accept="image/jpg,image/jpeg,image/png">
              <div class="form-text">Max 5MB. JPG or PNG only.</div>
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-success">
              <i class="bi bi-check-lg me-1"></i>Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Avatar & Account Info -->
  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-3 mb-3 text-center p-4">
      <?php if (!empty($user['avatar'])): ?>
        <img src="<?= APP_URL ?>/assets/uploads/<?= Helper::e($user['avatar']) ?>" class="rounded-circle mb-3 mx-auto d-block" width="90" height="90" style="object-fit:cover">
      <?php else: ?>
        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto mb-3 fw-bold fs-3" style="width:90px;height:90px">
          <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?>
        </div>
      <?php endif; ?>
      <div class="fw-bold"><?= Helper::e(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></div>
      <div class="text-muted small text-capitalize"><?= Helper::e(str_replace('_', ' ', $user['role_name'] ?? '')) ?></div>
      <div class="mt-2"><?= Helper::statusBadge($user['status'] ?? 'active') ?></div>
      <?php if (!empty($user['last_login'])): ?>
      <div class="text-muted small mt-2">Last login: <?= Helper::formatDate($user['last_login'], 'd M Y H:i') ?></div>
      <?php endif; ?>
    </div>

    <!-- Change Password -->
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white fw-semibold border-bottom">Change Password</div>
      <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/profile/password">
          <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
          <div class="mb-3">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control" minlength="8" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" minlength="8" required>
          </div>
          <button type="submit" class="btn btn-outline-success w-100">
            <i class="bi bi-lock me-1"></i>Update Password
          </button>
        </form>
      </div>
    </div>
  </div>

</div>
