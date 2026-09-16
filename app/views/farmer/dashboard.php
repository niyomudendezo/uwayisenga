<?php $title = 'Farmer Dashboard'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h5 class="fw-bold mb-0">Welcome, <?= Helper::e($farmer['first_name']) ?>!</h5>
    <small class="text-muted"><?= Helper::e($farmer['farm_name'] ?? 'My Farm') ?> | <?= Helper::e($farmer['district_name'] ?? '') ?></small>
  </div>
  <a href="<?= APP_URL ?>/farmer/harvests" class="btn btn-success btn-sm">
    <i class="bi bi-plus-lg me-1"></i>Record Harvest
  </a>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <i class="bi bi-basket text-success fs-2"></i>
        <div class="fw-bold fs-3 mt-1"><?= number_format($totalHarv) ?></div>
        <div class="text-muted small">Total Harvest (kg)</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <i class="bi bi-flower1 text-primary fs-2"></i>
        <div class="fw-bold fs-3 mt-1"><?= count($topCrops) ?></div>
        <div class="text-muted small">Crop Types</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <i class="bi bi-building text-warning fs-2"></i>
        <div class="fw-bold fs-3 mt-1"><?= $farmer['cooperative_name'] ? '✓' : '—' ?></div>
        <div class="text-muted small"><?= Helper::e($farmer['cooperative_name'] ?? 'No Cooperative') ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <i class="bi bi-rulers text-info fs-2"></i>
        <div class="fw-bold fs-3 mt-1"><?= $farmer['farm_size'] ?? '—' ?></div>
        <div class="text-muted small">Farm Size (ha)</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <!-- AI Recommendations -->
  <div class="col-md-6">
    <div class="card border-0 shadow-sm rounded-3 h-100">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-robot text-success me-2"></i>AI Recommendations</span>
        <a href="<?= APP_URL ?>/farmer/ai-recommendations" class="btn btn-sm btn-outline-success">View All</a>
      </div>
      <div class="card-body p-0">
        <?php foreach ($predictions as $p): ?>
        <div class="p-3 border-bottom">
          <div class="d-flex justify-content-between">
            <span class="fw-semibold small"><?= Helper::e($p['crop_name']) ?></span>
            <?= Helper::statusBadge($p['predicted_demand']) ?>
          </div>
          <div class="small text-success fw-semibold"><?= Helper::formatCurrency($p['predicted_price']) ?>/kg predicted</div>
          <div class="small text-muted mt-1"><?= Helper::truncate($p['recommendation_text'], 90) ?></div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($predictions)): ?>
        <div class="text-center text-muted py-4 small">
          <i class="bi bi-robot fs-2 d-block mb-2"></i>
          No AI recommendations yet. Record harvests to get started.
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Market Prices -->
  <div class="col-md-6">
    <div class="card border-0 shadow-sm rounded-3 h-100">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-graph-up text-success me-2"></i>Today's Prices</span>
        <a href="<?= APP_URL ?>/farmer/market-prices" class="btn btn-sm btn-outline-success">All Prices</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead class="table-light"><tr><th>Crop</th><th>Price/kg</th><th>Date</th></tr></thead>
            <tbody>
              <?php foreach (array_slice($prices, 0, 8) as $p): ?>
              <tr>
                <td><?= Helper::e($p['crop_name']) ?></td>
                <td class="fw-semibold text-success"><?= Helper::formatCurrency($p['price']) ?></td>
                <td class="small text-muted"><?= Helper::formatDate($p['price_date']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Harvests -->
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
    <span class="fw-semibold"><i class="bi bi-basket text-success me-2"></i>Recent Harvests</span>
    <a href="<?= APP_URL ?>/farmer/harvests" class="btn btn-sm btn-outline-success">View All</a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm table-hover mb-0">
        <thead class="table-light"><tr><th>Crop</th><th>Quantity</th><th>Grade</th><th>Season</th><th>Date</th></tr></thead>
        <tbody>
          <?php foreach ($harvests as $h): ?>
          <tr>
            <td><?= Helper::e($h['crop_name']) ?></td>
            <td><?= number_format($h['quantity']) ?> <?= Helper::e($h['unit'] ?? $h['crop_unit'] ?? 'kg') ?></td>
            <td><span class="badge bg-success">Grade <?= $h['grade'] ?></span></td>
            <td class="small"><?= Helper::e($h['season'] ?? '-') ?></td>
            <td class="small text-muted"><?= Helper::formatDate($h['harvest_date']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($harvests)): ?>
          <tr><td colspan="5" class="text-center text-muted py-3">No harvests recorded yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
