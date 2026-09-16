<?php $title = 'Cooperative Dashboard'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h5 class="fw-bold mb-0"><?= Helper::e($coop['name']) ?></h5>
    <small class="text-muted"><?= Helper::e($coop['district_name'] ?? '') ?> | Reg: <?= Helper::e($coop['registration_no'] ?? 'N/A') ?></small>
  </div>
  <span class="badge bg-success">Active Cooperative</span>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <i class="bi bi-people text-success fs-2"></i>
        <div class="fw-bold fs-3 mt-1"><?= $stats['members'] ?></div>
        <div class="text-muted small">Members</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <i class="bi bi-boxes text-primary fs-2"></i>
        <div class="fw-bold fs-3 mt-1"><?= Helper::formatCurrency($stats['inventory_val']) ?></div>
        <div class="text-muted small">Inventory Value</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <i class="bi bi-cart3 text-warning fs-2"></i>
        <div class="fw-bold fs-3 mt-1"><?= $stats['active_orders'] ?></div>
        <div class="text-muted small">Active Orders</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <i class="bi bi-currency-exchange text-success fs-2"></i>
        <div class="fw-bold fs-3 mt-1"><?= Helper::formatCurrency($stats['revenue']) ?></div>
        <div class="text-muted small">Total Revenue</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <!-- Revenue Chart -->
  <div class="col-md-7">
    <div class="card border-0 shadow-sm rounded-3 h-100">
      <div class="card-header bg-white border-0 fw-semibold">Monthly Revenue</div>
      <div class="card-body"><canvas id="revenueChart" height="120"></canvas></div>
    </div>
  </div>

  <!-- Inventory Summary -->
  <div class="col-md-5">
    <div class="card border-0 shadow-sm rounded-3 h-100">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Inventory Summary</span>
        <a href="<?= APP_URL ?>/cooperative/inventory" class="btn btn-sm btn-outline-success">Manage</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead class="table-light"><tr><th>Crop</th><th>Available</th><th>Price/kg</th></tr></thead>
            <tbody>
              <?php foreach ($invSummary as $inv): ?>
              <tr>
                <td><?= Helper::e($inv['crop_name']) ?></td>
                <td><?= number_format($inv['available']) ?> <?= $inv['unit'] ?></td>
                <td class="text-success"><?= Helper::formatCurrency($inv['asking_price']) ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($invSummary)): ?>
              <tr><td colspan="3" class="text-center text-muted py-3">No inventory</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <!-- Recent Orders -->
  <div class="col-md-7">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-cart3 text-success me-2"></i>Recent Orders</span>
        <a href="<?= APP_URL ?>/cooperative/orders" class="btn btn-sm btn-outline-success">View All</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead class="table-light"><tr><th>Order #</th><th>Buyer</th><th>Amount</th><th>Status</th><th></th></tr></thead>
            <tbody>
              <?php foreach ($recentOrders as $o): ?>
              <tr>
                <td><?= Helper::e($o['order_no']) ?></td>
                <td><?= Helper::e($o['company_name'] ?: $o['first_name'].' '.$o['last_name']) ?></td>
                <td><?= Helper::formatCurrency($o['total_amount']) ?></td>
                <td><?= Helper::statusBadge($o['status']) ?></td>
                <td><a href="<?= APP_URL ?>/cooperative/orders/<?= $o['id'] ?>" class="btn btn-xs btn-outline-success">View</a></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- AI Predictions -->
  <div class="col-md-5">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-robot text-success me-2"></i>AI Insights</span>
        <a href="<?= APP_URL ?>/cooperative/ai-predictions" class="btn btn-sm btn-outline-success">More</a>
      </div>
      <div class="card-body p-0">
        <?php foreach ($predictions as $p): ?>
        <div class="p-3 border-bottom">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="fw-semibold small"><?= Helper::e($p['crop_name']) ?></div>
              <div class="text-muted" style="font-size:.75rem"><?= Helper::truncate($p['recommendation_text'], 70) ?></div>
            </div>
            <?= Helper::statusBadge($p['predicted_demand']) ?>
          </div>
          <div class="mt-1 small">
            <span class="text-success fw-semibold"><?= Helper::formatCurrency($p['predicted_price']) ?>/kg</span>
            <span class="text-muted ms-2">Confidence: <?= $p['confidence_score'] ?>%</span>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($predictions)): ?>
        <div class="text-center text-muted py-3 small">No predictions yet.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
const revenueData = <?= json_encode(array_reverse($revenueByMonth)) ?>;
new Chart(document.getElementById('revenueChart'), {
  type: 'line',
  data: {
    labels: revenueData.map(r => r.month),
    datasets: [{ label: 'Revenue (RWF)', data: revenueData.map(r => r.revenue), borderColor: '#198754', backgroundColor: 'rgba(25,135,84,0.1)', fill: true, tension: 0.4 }]
  },
  options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
