<?php $title = 'Buyer Dashboard'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h5 class="fw-bold mb-0">Welcome, <?= Helper::e($buyer['first_name']) ?>!</h5>
    <small class="text-muted"><?= Helper::e($buyer['company_name'] ?? 'Individual Buyer') ?></small>
  </div>
  <a href="<?= APP_URL ?>/buyer/marketplace" class="btn btn-success btn-sm">
    <i class="bi bi-shop me-1"></i>Browse Marketplace
  </a>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <i class="bi bi-cart3 text-success fs-2"></i>
        <div class="fw-bold fs-3 mt-1"><?= $stats['total_orders'] ?></div>
        <div class="text-muted small">Total Orders</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <i class="bi bi-currency-exchange text-primary fs-2"></i>
        <div class="fw-bold fs-3 mt-1"><?= Helper::formatCurrency($stats['total_spent']) ?></div>
        <div class="text-muted small">Total Spent</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <i class="bi bi-check-circle text-success fs-2"></i>
        <div class="fw-bold fs-3 mt-1"><?= Helper::formatCurrency($stats['completed_amount']) ?></div>
        <div class="text-muted small">Completed Purchases</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <i class="bi bi-shield-check text-<?= $buyer['verified'] ? 'success' : 'warning' ?> fs-2"></i>
        <div class="fw-bold fs-3 mt-1"><?= $buyer['verified'] ? 'Verified' : 'Pending' ?></div>
        <div class="text-muted small">Account Status</div>
      </div>
    </div>
  </div>
</div>

<?php if (!$buyer['verified']): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
  <i class="bi bi-exclamation-triangle-fill fs-5"></i>
  <div>
    Your account is <strong>pending verification</strong>. An administrator will verify your account shortly.
    You can still browse the marketplace.
    <br><small class="text-muted">If this takes too long, please contact the administrator.</small>
  </div>
</div>
<?php endif; ?>

<div class="row g-3">
  <!-- Recent Orders -->
  <div class="col-md-7">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-cart3 text-success me-2"></i>Recent Orders</span>
        <a href="<?= APP_URL ?>/buyer/orders" class="btn btn-sm btn-outline-success">View All</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead class="table-light"><tr><th>Order #</th><th>Crops</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
              <?php foreach ($orders as $o): ?>
              <tr>
                <td><a href="<?= APP_URL ?>/buyer/orders/<?= $o['id'] ?>" class="text-success"><?= Helper::e($o['order_no']) ?></a></td>
                <td class="small"><?= Helper::e(Helper::truncate($o['crops'] ?? '', 30)) ?></td>
                <td><?= Helper::formatCurrency($o['total_amount']) ?></td>
                <td><?= Helper::statusBadge($o['status']) ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($orders)): ?>
              <tr><td colspan="4" class="text-center text-muted py-3">No orders yet. <a href="<?= APP_URL ?>/buyer/marketplace">Browse marketplace</a></td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Market Prices -->
  <div class="col-md-5">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-graph-up text-success me-2"></i>Market Prices</span>
        <a href="<?= APP_URL ?>/buyer/market-prices" class="btn btn-sm btn-outline-success">Details</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead class="table-light"><tr><th>Crop</th><th>Price/kg</th></tr></thead>
            <tbody>
              <?php foreach (array_slice($prices, 0, 8) as $p): ?>
              <tr>
                <td><?= Helper::e($p['crop_name']) ?></td>
                <td class="fw-semibold text-success"><?= Helper::formatCurrency($p['price']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
