<?php $title = 'Market Prices'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-graph-up text-success me-2"></i>Market Prices</h5>
</div>

<!-- Latest Prices -->
<h6 class="fw-semibold mb-3 text-muted">Latest Prices</h6>
<?php if (empty($prices)): ?>
<div class="alert alert-info">No market prices available at the moment.</div>
<?php else: ?>
<div class="row g-3 mb-4">
  <?php foreach ($prices as $p): ?>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3 h-100">
      <div class="card-body text-center py-3">
        <div class="fw-semibold"><?= Helper::e($p['crop_name']) ?></div>
        <div class="fs-4 fw-bold text-success"><?= Helper::formatCurrency($p['price']) ?></div>
        <div class="small text-muted">per <?= Helper::e($p['unit']) ?></div>
        <?php if (!empty($p['district_name'])): ?>
        <div class="small text-muted"><i class="bi bi-geo-alt"></i> <?= Helper::e($p['district_name']) ?></div>
        <?php endif; ?>
        <div class="small text-muted"><?= Helper::formatDate($p['price_date']) ?></div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- 3-Month Trends -->
<?php if (!empty($trends)): ?>
<h6 class="fw-semibold mb-3 text-muted">3-Month Price Trends</h6>
<div class="card border-0 shadow-sm rounded-3">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Crop</th>
          <th>Unit</th>
          <th>Latest</th>
          <th>Min</th>
          <th>Max</th>
          <th>Avg</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($trends as $t): ?>
        <tr>
          <td class="fw-semibold"><?= Helper::e($t['crop_name']) ?></td>
          <td><?= Helper::e($t['unit']) ?></td>
          <td class="text-success fw-bold"><?= Helper::formatCurrency($t['latest_price']) ?></td>
          <td><?= Helper::formatCurrency($t['min_price']) ?></td>
          <td><?= Helper::formatCurrency($t['max_price']) ?></td>
          <td><?= Helper::formatCurrency($t['avg_price']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
