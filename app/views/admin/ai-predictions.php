<?php $title = 'AI Predictions'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-robot text-success me-2"></i>AI Predictions</h5>
  <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#runModal">
    <i class="bi bi-play-fill me-1"></i>Run New Prediction
  </button>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
  <?php
  $demandMap = ['High' => ['success','bi-arrow-up-circle'], 'Medium' => ['warning','bi-dash-circle'], 'Low' => ['danger','bi-arrow-down-circle']];
  foreach ($summary as $s):
    [$color, $icon] = $demandMap[$s['predicted_demand']] ?? ['secondary','bi-circle'];
  ?>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-3 border-start border-<?= $color ?> border-3">
      <div class="card-body d-flex align-items-center gap-3">
        <i class="bi <?= $icon ?> text-<?= $color ?> fs-2"></i>
        <div>
          <div class="fw-bold fs-3"><?= $s['count'] ?></div>
          <div class="text-muted small"><?= $s['predicted_demand'] ?> Demand Crops</div>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Predictions Table -->
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-header bg-white border-0 fw-semibold">Latest Predictions</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr><th>Crop</th><th>District</th><th>Demand</th><th>Predicted Price</th><th>Best Buyer</th><th>Revenue Est.</th><th>Confidence</th><th>Recommendation</th><th>Date</th></tr>
        </thead>
        <tbody>
          <?php foreach ($predictions as $p): ?>
          <tr>
            <td class="fw-semibold"><?= Helper::e($p['crop_name']) ?></td>
            <td class="small"><?= Helper::e($p['district_name'] ?? 'National') ?></td>
            <td><?= Helper::statusBadge($p['predicted_demand']) ?></td>
            <td class="fw-semibold text-success"><?= Helper::formatCurrency($p['predicted_price']) ?>/kg</td>
            <td class="small"><?= Helper::e($p['cooperative_name'] ?? 'N/A') ?></td>
            <td><?= Helper::formatCurrency($p['estimated_revenue']) ?></td>
            <td>
              <div class="d-flex align-items-center gap-1">
                <div class="progress flex-grow-1" style="height:6px;width:60px">
                  <div class="progress-bar bg-success" style="width:<?= $p['confidence_score'] ?>%"></div>
                </div>
                <span class="small"><?= $p['confidence_score'] ?>%</span>
              </div>
            </td>
            <td class="small text-muted" style="max-width:200px"><?= Helper::truncate($p['recommendation_text'], 80) ?></td>
            <td class="small text-muted"><?= Helper::formatDate($p['prediction_date']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($predictions)): ?>
          <tr><td colspan="9" class="text-center text-muted py-4">No predictions yet. Run your first prediction.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Run Prediction Modal -->
<div class="modal fade" id="runModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-robot me-2"></i>Run AI Prediction</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="<?= APP_URL ?>/admin/ai-predictions/run">
        <?= Auth::csrfField() ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Select Crop *</label>
            <select name="crop_id" class="form-select" required>
              <option value="">Choose crop...</option>
              <?php foreach ($crops as $c): ?>
                <option value="<?= $c['id'] ?>"><?= Helper::e($c['name']) ?> (<?= Helper::e($c['category_name']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">District</label>
            <select name="district_id" class="form-select">
              <option value="1">Kigali City (Default)</option>
              <?php foreach ($districts as $d): ?>
                <option value="<?= $d['id'] ?>"><?= Helper::e($d['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="alert alert-info small">
            <i class="bi bi-info-circle me-1"></i>
            The AI engine analyzes historical prices, sales data, and seasonal trends to generate predictions.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="bi bi-play-fill me-1"></i>Generate Prediction</button>
        </div>
      </form>
    </div>
  </div>
</div>
