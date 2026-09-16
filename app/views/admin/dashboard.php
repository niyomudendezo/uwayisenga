<?php $title = 'Admin Dashboard'; ?>

<div class="admin-dashboard">
<header class="dashboard-welcome">
  <div><span class="dashboard-eyebrow">Overview · <?= date('l, d F') ?></span><h1>Good day, <?= Helper::e(explode(' ', Auth::name())[0]) ?>.</h1><p>Here is what is happening across the AgruKrwanda network today.</p></div>
  <div class="dashboard-actions"><a href="<?= APP_URL ?>/admin/reports" class="btn dashboard-action-secondary"><i class="bi bi-file-earmark-bar-graph"></i> View reports</a><a href="<?= APP_URL ?>/admin/users/create" class="btn dashboard-action-primary"><i class="bi bi-plus-lg"></i> Add user</a></div>
</header>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card dashboard-stat stat-green h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-3 bg-success bg-opacity-10 p-3"><i class="bi bi-person-badge text-success fs-4"></i></div>
        <div>
          <div class="fw-bold fs-4"><?= number_format($stats['farmers']) ?></div>
          <div class="text-muted small">Farmers</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card dashboard-stat stat-blue h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-3 bg-primary bg-opacity-10 p-3"><i class="bi bi-building text-primary fs-4"></i></div>
        <div>
          <div class="fw-bold fs-4"><?= number_format($stats['cooperatives']) ?></div>
          <div class="text-muted small">Cooperatives</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card dashboard-stat stat-amber h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-3 bg-warning bg-opacity-10 p-3"><i class="bi bi-shop text-warning fs-4"></i></div>
        <div>
          <div class="fw-bold fs-4"><?= number_format($stats['buyers']) ?></div>
          <div class="text-muted small">Buyers</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card dashboard-stat stat-purple h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-3 bg-info bg-opacity-10 p-3"><i class="bi bi-cart3 text-info fs-4"></i></div>
        <div>
          <div class="fw-bold fs-4"><?= number_format($stats['active_orders']) ?></div>
          <div class="text-muted small">Active Orders</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Revenue & Inventory Value -->
<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="card value-card revenue-value-card text-white">
      <div class="card-body d-flex align-items-center gap-3">
        <i class="bi bi-currency-exchange fs-1 opacity-50"></i>
        <div>
          <div class="small opacity-75">Total Revenue (Completed Orders)</div>
          <div class="fw-bold fs-3"><?= Helper::formatCurrency($stats['total_revenue']) ?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card value-card inventory-value-card text-white">
      <div class="card-body d-flex align-items-center gap-3">
        <i class="bi bi-boxes fs-1 opacity-50"></i>
        <div>
          <div class="small opacity-75">Total Inventory Value</div>
          <div class="fw-bold fs-3"><?= Helper::formatCurrency($stats['inventory_val']) ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <!-- Revenue Chart -->
  <div class="col-md-8">
    <div class="card border-0 shadow-sm rounded-3 h-100">
      <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-graph-up-arrow text-success me-2"></i>Monthly Revenue (RWF)
      </div>
      <div class="card-body">
        <canvas id="revenueChart" height="100"></canvas>
      </div>
    </div>
  </div>

  <!-- AI Prediction Summary -->
  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-3 h-100">
      <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-robot text-success me-2"></i>AI Demand Forecast
      </div>
      <div class="card-body">
        <canvas id="demandChart" height="180"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <!-- Top Crops -->
  <div class="col-md-5">
    <div class="card border-0 shadow-sm rounded-3 h-100">
      <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-flower1 text-success me-2"></i>Top Crops by Production
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead class="table-light"><tr><th>Crop</th><th>Qty (kg)</th><th>Farmers</th></tr></thead>
            <tbody>
              <?php foreach ($cropProduction as $c): ?>
              <tr>
                <td><?= Helper::e($c['crop_name']) ?></td>
                <td><?= number_format($c['total_qty']) ?></td>
                <td><?= $c['farmer_count'] ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Orders -->
  <div class="col-md-7">
    <div class="card border-0 shadow-sm rounded-3 h-100">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-cart3 text-success me-2"></i>Recent Orders</span>
        <a href="<?= APP_URL ?>/admin/orders" class="btn btn-sm btn-outline-success">View All</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead class="table-light"><tr><th>Order #</th><th>Buyer</th><th>Cooperative</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
              <?php foreach ($recentOrders as $o): ?>
              <tr>
                <td><a href="<?= APP_URL ?>/admin/orders" class="text-success"><?= Helper::e($o['order_no']) ?></a></td>
                <td><?= Helper::e($o['company_name'] ?: $o['first_name'].' '.$o['last_name']) ?></td>
                <td><?= Helper::e($o['cooperative_name']) ?></td>
                <td><?= Helper::formatCurrency($o['total_amount']) ?></td>
                <td><?= Helper::statusBadge($o['status']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- AI Predictions & Market Prices -->
<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-robot text-success me-2"></i>Latest AI Predictions</span>
        <a href="<?= APP_URL ?>/admin/ai-predictions" class="btn btn-sm btn-outline-success">View All</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead class="table-light"><tr><th>Crop</th><th>Demand</th><th>Price/kg</th><th>Confidence</th></tr></thead>
            <tbody>
              <?php foreach ($aiPredictions as $p): ?>
              <tr>
                <td><?= Helper::e($p['crop_name']) ?></td>
                <td><?= Helper::statusBadge($p['predicted_demand']) ?></td>
                <td><?= Helper::formatCurrency($p['predicted_price']) ?></td>
                <td><span class="badge bg-secondary"><?= $p['confidence_score'] ?>%</span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-graph-up text-success me-2"></i>Current Market Prices</span>
        <a href="<?= APP_URL ?>/admin/market-prices" class="btn btn-sm btn-outline-success">Manage</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead class="table-light"><tr><th>Crop</th><th>Price/kg</th><th>District</th><th>Date</th></tr></thead>
            <tbody>
              <?php foreach (array_slice($latestPrices, 0, 6) as $p): ?>
              <tr>
                <td><?= Helper::e($p['crop_name']) ?></td>
                <td class="fw-semibold text-success"><?= Helper::formatCurrency($p['price']) ?></td>
                <td><?= Helper::e($p['district_name'] ?? 'National') ?></td>
                <td><?= Helper::formatDate($p['price_date']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Activity -->
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-header bg-white border-0 fw-semibold">
    <i class="bi bi-activity text-success me-2"></i>Recent Activity
  </div>
  <div class="card-body p-0">
    <ul class="list-group list-group-flush">
      <?php foreach ($recentActivity as $a): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center py-2">
        <div class="d-flex align-items-center gap-2">
          <div class="rounded-circle bg-success bg-opacity-10 p-1"><i class="bi bi-person text-success small"></i></div>
          <span class="small"><?= Helper::e($a['description']) ?></span>
        </div>
        <span class="text-muted small"><?= Helper::timeAgo($a['created_at']) ?></span>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

</div>

<script>
// Revenue Chart
const revenueData = <?= json_encode(array_reverse($revenueByMonth)) ?>;
new Chart(document.getElementById('revenueChart'), {
  type: 'bar',
  data: {
    labels: revenueData.map(r => r.month),
    datasets: [{
      label: 'Revenue (RWF)',
      data: revenueData.map(r => r.revenue),
      backgroundColor: 'rgba(25,135,84,0.7)',
      borderColor: '#198754',
      borderWidth: 1,
      borderRadius: 4,
    }]
  },
  options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

// Demand Pie Chart
const demandData = <?= json_encode($aiPredictions) ?>;
const demandCounts = { High: 0, Medium: 0, Low: 0 };
demandData.forEach(p => { if (demandCounts[p.predicted_demand] !== undefined) demandCounts[p.predicted_demand]++; });
new Chart(document.getElementById('demandChart'), {
  type: 'doughnut',
  data: {
    labels: ['High', 'Medium', 'Low'],
    datasets: [{ data: Object.values(demandCounts), backgroundColor: ['#198754','#ffc107','#dc3545'], borderWidth: 0 }]
  },
  options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>
