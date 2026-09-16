<?php $title = 'AI Recommendations'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-robot text-success me-2"></i>AI Recommendations</h5>
</div>

<?php if (empty($predictions)): ?>
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body text-center py-5">
    <i class="bi bi-robot text-muted fs-1 d-block mb-3"></i>
    <h5 class="text-muted">No AI Recommendations Yet</h5>
    <p class="text-muted small">Record your harvests to receive personalized AI-powered market recommendations.</p>
    <a href="<?= APP_URL ?>/farmer/harvests" class="btn btn-success">Record Harvest</a>
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
            <h5 class="fw-bold mb-0"><?= Helper::e($p['crop_name']) ?></h5>
            <small class="text-muted">Prediction Date: <?= Helper::formatDate($p['prediction_date']) ?></small>
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
              <div class="small text-muted">Est. Revenue</div>
              <div class="fw-bold text-primary"><?= Helper::formatCurrency($p['estimated_revenue']) ?></div>
            </div>
          </div>
          <div class="col-6">
            <div class="bg-light rounded-2 p-2 text-center">
              <div class="small text-muted">Suggested Qty</div>
              <div class="fw-bold"><?= number_format($p['suggested_qty']) ?> kg</div>
            </div>
          </div>
          <div class="col-6">
            <div class="bg-light rounded-2 p-2 text-center">
              <div class="small text-muted">Confidence</div>
              <div class="fw-bold"><?= $p['confidence_score'] ?>%</div>
            </div>
          </div>
        </div>

        <div class="mb-2">
          <div class="small text-muted mb-1">Best Selling Period</div>
          <div class="small fw-semibold"><i class="bi bi-calendar-check text-success me-1"></i><?= Helper::e($p['best_selling_period']) ?></div>
        </div>

        <div class="alert alert-<?= $p['predicted_demand'] === 'High' ? 'success' : ($p['predicted_demand'] === 'Medium' ? 'warning' : 'danger') ?> py-2 small mb-0">
          <i class="bi bi-lightbulb me-1"></i><?= Helper::e($p['recommendation_text']) ?>
        </div>

        <!-- Confidence Bar -->
        <div class="mt-3">
          <div class="d-flex justify-content-between small text-muted mb-1">
            <span>AI Confidence</span><span><?= $p['confidence_score'] ?>%</span>
          </div>
          <div class="progress" style="height:6px">
            <div class="progress-bar bg-success" style="width:<?= $p['confidence_score'] ?>%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
