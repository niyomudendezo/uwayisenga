<?php $title = 'Market Prices'; ?>

<section class="py-5 bg-success text-white text-center">
  <div class="container">
    <h1 class="fw-bold mb-2"><i class="bi bi-graph-up me-2"></i>Market Prices</h1>
    <p class="lead opacity-90 mb-0">Live agricultural market prices across Rwanda</p>
  </div>
</section>

<section class="py-5 bg-light">
  <div class="container">

    <!-- Filter -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
      <div class="card-body">
        <form method="GET" action="<?= APP_URL ?>/market-prices" class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Filter by Crop</label>
            <select name="crop_id" class="form-select">
              <option value="">All Crops</option>
              <?php foreach ($crops as $c): ?>
              <option value="<?= $c['id'] ?>" <?= ($selectedCrop == $c['id']) ? 'selected' : '' ?>>
                <?= Helper::e($c['name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-auto">
            <button class="btn btn-success">Filter</button>
            <?php if ($selectedCrop): ?>
            <a href="<?= APP_URL ?>/market-prices" class="btn btn-outline-secondary ms-2">Clear</a>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>

    <!-- Latest Prices -->
    <h4 class="fw-bold mb-3">Latest Prices</h4>
    <?php if (empty($prices)): ?>
    <div class="alert alert-info">No market prices available at the moment.</div>
    <?php else: ?>
    <div class="row g-3 mb-5">
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
            <div class="small text-muted"><?= date('d M Y', strtotime($p['price_date'])) ?></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Price Trends -->
    <?php if (!empty($trends)): ?>
    <h4 class="fw-bold mb-3">3-Month Price Trends</h4>
    <div class="card border-0 shadow-sm rounded-3">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-success">
            <tr>
              <th>Crop</th>
              <th>Unit</th>
              <th>Latest Price</th>
              <th>Min (3 mo)</th>
              <th>Max (3 mo)</th>
              <th>Avg (3 mo)</th>
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

  </div>
</section>
