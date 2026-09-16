<?php $title = 'My Harvests'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-basket text-success me-2"></i>My Harvests</h5>
  <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addHarvestModal">
    <i class="bi bi-plus-lg me-1"></i>Record Harvest
  </button>
</div>

<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Crop</th>
            <th>Quantity</th>
            <th>Grade</th>
            <th>Season</th>
            <th>Harvest Date</th>
            <th>Cooperative</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($result['data'] as $h): ?>
          <tr>
            <td class="fw-semibold"><?= Helper::e($h['crop_name']) ?></td>
            <td><?= number_format($h['quantity']) ?> <?= Helper::e($h['crop_unit']) ?></td>
            <td><span class="badge bg-success">Grade <?= Helper::e($h['grade']) ?></span></td>
            <td><?= Helper::e($h['season'] ?? '-') ?></td>
            <td><?= Helper::formatDate($h['harvest_date']) ?></td>
            <td class="small"><?= Helper::e($h['cooperative_name'] ?? '-') ?></td>
            <td class="small text-muted"><?= Helper::e($h['notes'] ?? '-') ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($result['data'])): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">No harvests recorded yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php if ($result['last_page'] > 1): ?>
  <div class="card-footer bg-white d-flex justify-content-between align-items-center">
    <small class="text-muted">Showing <?= count($result['data']) ?> of <?= $result['total'] ?> records</small>
    <?= Helper::paginate($result, APP_URL . '/farmer/harvests') ?>
  </div>
  <?php endif; ?>
</div>

<!-- Add Harvest Modal -->
<div class="modal fade" id="addHarvestModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="<?= APP_URL ?>/farmer/harvests/store">
        <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Record Harvest</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
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
              <label class="form-label">Quantity (kg) <span class="text-danger">*</span></label>
              <input type="number" name="quantity" class="form-control" min="0.1" step="0.1" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Grade</label>
              <select name="grade" class="form-select">
                <option value="A">Grade A</option>
                <option value="B">Grade B</option>
                <option value="C">Grade C</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Harvest Date</label>
              <input type="date" name="harvest_date" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-12">
              <label class="form-label">Season</label>
              <input type="text" name="season" class="form-control" placeholder="e.g. Season A 2025">
            </div>
            <div class="col-12">
              <label class="form-label">Notes</label>
              <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Save Harvest</button>
        </div>
      </form>
    </div>
  </div>
</div>
