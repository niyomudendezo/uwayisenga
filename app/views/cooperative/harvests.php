<?php $title = 'Harvests'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-basket text-success me-2"></i>Harvests</h5>
  <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addHarvestModal">
    <i class="bi bi-plus-lg me-1"></i>Record Harvest
  </button>
</div>

<!-- Summary Stats -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <div class="fw-bold fs-4 text-success"><?= number_format($result['total']) ?></div>
        <div class="text-muted small">Total Records</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <div class="fw-bold fs-4 text-primary"><?= count(array_unique(array_column($result['data'], 'first_name'))) ?></div>
        <div class="text-muted small">Members with Harvests</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <div class="fw-bold fs-4 text-warning"><?= number_format(array_sum(array_column($result['data'], 'quantity'))) ?> kg</div>
        <div class="text-muted small">Total Quantity (this page)</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <div class="fw-bold fs-4 text-info"><?= count(array_unique(array_column($result['data'], 'crop_name'))) ?></div>
        <div class="text-muted small">Crop Types</div>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-3 mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search farmer or crop..." value="<?= Helper::e($search) ?>">
      </div>
      <div class="col-md-3">
        <select name="farmer" class="form-select form-select-sm">
          <option value="">All Members</option>
          <?php foreach ($farmers as $f): ?>
          <option value="<?= $f['farmer_id'] ?>" <?= ($_GET['farmer'] ?? '') == $f['farmer_id'] ? 'selected' : '' ?>>
            <?= Helper::e($f['first_name'] . ' ' . $f['last_name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <select name="crop" class="form-select form-select-sm">
          <option value="">All Crops</option>
          <?php foreach ($crops as $c): ?>
          <option value="<?= $c['id'] ?>" <?= ($_GET['crop'] ?? '') == $c['id'] ? 'selected' : '' ?>>
            <?= Helper::e($c['name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-success btn-sm">Filter</button>
        <a href="<?= APP_URL ?>/cooperative/harvests" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- Harvests Table -->
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Farmer</th>
            <th>Crop</th>
            <th>Quantity</th>
            <th>Grade</th>
            <th>Season</th>
            <th>Date</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($result['data'] as $h): ?>
          <tr>
            <td class="fw-semibold"><?= Helper::e($h['first_name'] . ' ' . $h['last_name']) ?></td>
            <td><?= Helper::e($h['crop_name']) ?></td>
            <td><?= number_format($h['quantity']) ?> <?= Helper::e($h['crop_unit']) ?></td>
            <td><span class="badge bg-success">Grade <?= Helper::e($h['grade']) ?></span></td>
            <td class="small"><?= Helper::e($h['season'] ?? '-') ?></td>
            <td class="small text-muted"><?= Helper::formatDate($h['harvest_date']) ?></td>
            <td class="small text-muted"><?= Helper::e($h['notes'] ?? '-') ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($result['data'])): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">No harvests found for this cooperative.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php if ($result['last_page'] > 1): ?>
  <div class="card-footer bg-white d-flex justify-content-between align-items-center">
    <small class="text-muted">Showing <?= count($result['data']) ?> of <?= $result['total'] ?> records</small>
    <?= Helper::paginate($result, APP_URL . '/cooperative/harvests') ?>
  </div>
  <?php endif; ?>
</div>

<!-- Add Harvest Modal -->
<div class="modal fade" id="addHarvestModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title fw-bold">Record Harvest</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <form method="POST" action="<?= APP_URL ?>/cooperative/harvests/store">
      <?= Auth::csrfField() ?>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-semibold small">Member (Farmer) <span class="text-danger">*</span></label>
            <select name="farmer_id" class="form-select" required>
              <option value="">— Select Member —</option>
              <?php foreach ($farmers as $f): ?>
              <option value="<?= $f['farmer_id'] ?>"><?= Helper::e($f['first_name'] . ' ' . $f['last_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold small">Crop <span class="text-danger">*</span></label>
            <select name="crop_id" class="form-select" required>
              <option value="">— Select Crop —</option>
              <?php foreach ($crops as $c): ?>
              <option value="<?= $c['id'] ?>"><?= Helper::e($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small">Quantity (kg) <span class="text-danger">*</span></label>
            <input type="number" name="quantity" class="form-control" step="0.01" min="0.1" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small">Grade</label>
            <select name="grade" class="form-select">
              <option value="A">Grade A</option>
              <option value="B">Grade B</option>
              <option value="C">Grade C</option>
              <option value="mixed">Mixed</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small">Harvest Date <span class="text-danger">*</span></label>
            <input type="date" name="harvest_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small">Season</label>
            <input type="text" name="season" class="form-control" placeholder="e.g. Season A 2025">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold small">Notes</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Save Harvest</button>
      </div>
    </form>
  </div></div>
</div>
