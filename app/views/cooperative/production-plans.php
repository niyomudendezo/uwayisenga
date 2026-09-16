<?php $title = 'Production Plans'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-calendar-check text-success me-2"></i>Production Plans</h5>
  <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addPlanModal">
    <i class="bi bi-plus-lg me-1"></i>Add Plan
  </button>
</div>

<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Crop</th>
            <th>Season</th>
            <th>Planned Qty</th>
            <th>Planned Date</th>
            <th>Status</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($plans as $p): ?>
          <tr>
            <td class="fw-semibold"><?= Helper::e($p['crop_name']) ?></td>
            <td><?= Helper::e($p['season'] ?? '-') ?></td>
            <td><?= number_format($p['planned_qty']) ?> kg</td>
            <td><?= $p['planned_date'] ? Helper::formatDate($p['planned_date']) : '-' ?></td>
            <td><?= Helper::statusBadge($p['status'] ?? 'planned') ?></td>
            <td class="small text-muted"><?= Helper::e($p['notes'] ?? '-') ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($plans)): ?>
          <tr><td colspan="6" class="text-center text-muted py-4">No production plans yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add Plan Modal -->
<div class="modal fade" id="addPlanModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Add Production Plan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="<?= APP_URL ?>/cooperative/production-plans/store">
        <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Crop <span class="text-danger">*</span></label>
              <select name="crop_id" class="form-select" required>
                <option value="">— Select —</option>
                <?php foreach ($crops as $c): ?>
                <option value="<?= $c['id'] ?>"><?= Helper::e($c['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Season</label>
              <input type="text" name="season" class="form-control" placeholder="e.g. Season A 2025">
            </div>
            <div class="col-md-6">
              <label class="form-label">Planned Quantity (kg) <span class="text-danger">*</span></label>
              <input type="number" name="planned_qty" class="form-control" min="1" step="0.1" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Planned Date</label>
              <input type="date" name="planned_date" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">Notes</label>
              <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Save Plan</button>
        </div>
      </form>
    </div>
  </div>
</div>
