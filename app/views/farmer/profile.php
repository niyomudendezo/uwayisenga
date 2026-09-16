<?php $title = 'My Profile'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="fw-bold mb-0"><i class="bi bi-person-circle text-success me-2"></i>My Profile</h5>
</div>

<div class="row g-4">

  <!-- Profile Form -->
  <div class="col-md-8">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white fw-semibold border-bottom">Personal & Farm Information</div>
      <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/farmer/profile">
          <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

          <h6 class="text-muted fw-semibold mb-3">Personal Details</h6>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label">First Name</label>
              <input type="text" name="first_name" class="form-control" value="<?= Helper::e($farmer['first_name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-control" value="<?= Helper::e($farmer['last_name'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control bg-light" value="<?= Helper::e($farmer['email'] ?? '') ?>" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control" value="<?= Helper::e($farmer['phone'] ?? '') ?>">
            </div>
          </div>

          <h6 class="text-muted fw-semibold mb-3">Farm Details</h6>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Farm Name</label>
              <input type="text" name="farm_name" class="form-control" value="<?= Helper::e($farmer['farm_name'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Farm Size (hectares)</label>
              <input type="number" name="farm_size" class="form-control" step="0.1" min="0" value="<?= $farmer['farm_size'] ?? '' ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">District</label>
              <select name="district_id" class="form-select">
                <option value="">— Select District —</option>
                <?php foreach ($districts as $d): ?>
                <option value="<?= $d['id'] ?>" <?= ($farmer['district_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                  <?= Helper::e($d['name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Cooperative</label>
              <select name="cooperative_id" class="form-select">
                <option value="">— None —</option>
                <?php foreach ($cooperatives as $co): ?>
                <option value="<?= $co['id'] ?>" <?= ($farmer['cooperative_id'] ?? '') == $co['id'] ? 'selected' : '' ?>>
                  <?= Helper::e($co['name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Soil Type</label>
              <select name="soil_type" class="form-select">
                <option value="">— Select —</option>
                <?php foreach (['Clay','Sandy','Loam','Silt','Peat','Chalk'] as $s): ?>
                <option value="<?= $s ?>" <?= ($farmer['soil_type'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <div class="form-check">
                <input type="checkbox" name="irrigation" id="irrigation" class="form-check-input" value="1" <?= !empty($farmer['irrigation']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="irrigation">Has Irrigation System</label>
              </div>
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
    <div class="card border-0 shadow-sm rounded-3 text-center p-4">
      <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto mb-3 fw-bold fs-3" style="width:80px;height:80px">
        <?= strtoupper(substr($farmer['first_name'] ?? 'F', 0, 1)) ?>
      </div>
      <div class="fw-bold fs-5"><?= Helper::e(($farmer['first_name'] ?? '') . ' ' . ($farmer['last_name'] ?? '')) ?></div>
      <div class="text-muted small mb-2"><?= Helper::e($farmer['email'] ?? '') ?></div>
      <?= Helper::statusBadge($farmer['status'] ?? 'active') ?>

      <hr>
      <ul class="list-unstyled text-start small">
        <li class="mb-2"><i class="bi bi-geo-alt text-success me-2"></i><?= Helper::e($farmer['district_name'] ?? 'No district set') ?></li>
        <li class="mb-2"><i class="bi bi-building text-success me-2"></i><?= Helper::e($farmer['cooperative_name'] ?? 'No cooperative') ?></li>
        <li class="mb-2"><i class="bi bi-rulers text-success me-2"></i><?= $farmer['farm_size'] ? $farmer['farm_size'] . ' ha' : 'Farm size not set' ?></li>
        <li><i class="bi bi-droplet text-success me-2"></i><?= !empty($farmer['irrigation']) ? 'Has irrigation' : 'No irrigation' ?></li>
      </ul>
    </div>
  </div>

</div>
