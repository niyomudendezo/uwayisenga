<?php $title = 'AI Predictions'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-robot text-success me-2"></i>AI Predictions</h5>
  <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#runPredictionModal">
    <i class="bi bi-play-fill me-1"></i>Run Prediction
  </button>
</div>

<?php if (empty($predictions)): ?>
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body text-center py-5">
    <i class="bi bi-robot text-muted fs-1 d-block mb-3"></i>
    <h5 class="text-muted">No Predictions Yet</h5>
    <p class="text-muted small">Run a prediction to get AI-powered market insights.</p>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#runPredictionModal">Run Prediction</button>
  </div>
</div>
<?php else: ?>
<div class="row g-3">
  <?php foreach ($predictions as $p): ?>
  <div class="col-md-6">
    <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-<?= $p['predicted_demand'] === 'High' ? 'success' : ($p['predicted_demand'] === 'Medium' ? 'warning' : 'danger') ?> border-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h6 class="fw-bold mb-0"><?= Helper::e($p['crop_name']) ?></h6>
            <small class="text-muted"><?= Helper::formatDate($p['prediction_date']) ?></small>
          </div>
          <?= Helper::statusBadge($p['predicted_demand']) ?>
        </div>
        <div class="row g-2 mb-3">
          <div class="col-6">
            <div class="bg-light rounded-2 p-2 text-center">
              <div class="small text-muted">Predicted Price</div>
              <div class="fw-bold text-success"><?= Helper::formatCurrency($p['predicted_price']) ?>/kg</div>
            </div>
          </div>
          <div class="col-6">
            <div class="bg-light rounded-2 p-2 text-center">
              <div class="small text-muted">Confidence</div>
              <div class="fw-bold"><?= $p['confidence_score'] ?>%</div>
            </div>
          </div>
        </div>
        <div class="small text-muted mb-1">Best Selling Period</div>
        <div class="small fw-semibold mb-2"><i class="bi bi-calendar-check text-success me-1"></i><?= Helper::e($p['best_selling_period']) ?></div>
        <div class="alert alert-<?= $p['predicted_demand'] === 'High' ? 'success' : ($p['predicted_demand'] === 'Medium' ? 'warning' : 'danger') ?> py-2 small mb-0">
          <i class="bi bi-lightbulb me-1"></i><?= Helper::e($p['recommendation_text']) ?>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Run Prediction Modal -->
<div class="modal fade" id="runPredictionModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Run AI Prediction</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="<?= APP_URL ?>/cooperative/ai-predictions/run">
        <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Crop <span class="text-danger">*</span></label>
            <select name="crop_id" class="form-select" required>
              <option value="">— Select Crop —</option>
              <?php foreach ($crops as $c): ?>
              <option value="<?= $c['id'] ?>"><?= Helper::e($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">District</label>
            <select name="district_id" class="form-select">
              <?php foreach ($districts as $d): ?>
              <option value="<?= $d['id'] ?>"><?= Helper::e($d['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="bi bi-play-fill me-1"></i>Run</button>
        </div>
      </form>
    </div>
  </div>
</div>
