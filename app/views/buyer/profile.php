<?php $title = 'My Profile'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="fw-bold mb-0"><i class="bi bi-person-circle text-success me-2"></i>My Profile</h5>
</div>

<div class="row g-4">

  <!-- Profile Form -->
  <div class="col-md-8">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white fw-semibold border-bottom">Personal & Business Information</div>
      <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/buyer/profile">
          <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

          <h6 class="text-muted fw-semibold mb-3">Personal Details</h6>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label">First Name</label>
              <input type="text" name="first_name" class="form-control" value="<?= Helper::e($buyer['first_name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-control" value="<?= Helper::e($buyer['last_name'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control bg-light" value="<?= Helper::e($buyer['email'] ?? '') ?>" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control" value="<?= Helper::e($buyer['phone'] ?? '') ?>">
            </div>
          </div>

          <h6 class="text-muted fw-semibold mb-3">Business Details</h6>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Company Name</label>
              <input type="text" name="company_name" class="form-control" value="<?= Helper::e($buyer['company_name'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Business Type</label>
              <select name="business_type" class="form-select">
                <option value="">— Select —</option>
                <?php foreach (['Retailer','Wholesaler','Exporter','Processor','Restaurant','Other'] as $bt): ?>
                <option value="<?= $bt ?>" <?= ($buyer['business_type'] ?? '') === $bt ? 'selected' : '' ?>><?= $bt ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">TIN Number</label>
              <input type="text" name="tin_number" class="form-control" value="<?= Helper::e($buyer['tin_number'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">District</label>
              <select name="district_id" class="form-select">
                <option value="">— Select District —</option>
                <?php foreach ($districts as $d): ?>
                <option value="<?= $d['id'] ?>" <?= ($buyer['district_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                  <?= Helper::e($d['name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Address</label>
              <textarea name="address" class="form-control" rows="2"><?= Helper::e($buyer['address'] ?? '') ?></textarea>
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

  <!-- Summary Card -->
  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-3 text-center p-4 mb-3">
      <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto mb-3 fw-bold fs-3" style="width:80px;height:80px">
        <?= strtoupper(substr($buyer['first_name'] ?? 'B', 0, 1)) ?>
      </div>
      <div class="fw-bold fs-5"><?= Helper::e(($buyer['first_name'] ?? '') . ' ' . ($buyer['last_name'] ?? '')) ?></div>
      <?php if (!empty($buyer['company_name'])): ?>
      <div class="text-muted small"><?= Helper::e($buyer['company_name']) ?></div>
      <?php endif; ?>
      <div class="mt-2 d-flex gap-2 justify-content-center">
        <?= Helper::statusBadge($buyer['status'] ?? 'active') ?>
        <?php if (!empty($buyer['verified'])): ?>
        <span class="badge bg-success"><i class="bi bi-patch-check me-1"></i>Verified</span>
        <?php else: ?>
        <span class="badge bg-warning text-dark">Pending Verification</span>
        <?php endif; ?>
      </div>
      <hr>
      <ul class="list-unstyled text-start small">
        <li class="mb-2"><i class="bi bi-envelope text-success me-2"></i><?= Helper::e($buyer['email'] ?? '') ?></li>
        <li class="mb-2"><i class="bi bi-telephone text-success me-2"></i><?= Helper::e($buyer['phone'] ?? 'Not set') ?></li>
        <li class="mb-2"><i class="bi bi-geo-alt text-success me-2"></i><?= Helper::e($buyer['district_name'] ?? 'No district set') ?></li>
        <li><i class="bi bi-briefcase text-success me-2"></i><?= Helper::e($buyer['business_type'] ?? 'Not set') ?></li>
      </ul>
    </div>
  </div>

</div>
