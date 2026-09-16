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
<div class="card border-0 shadow-sm rounded-3 mb-4">
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

<!-- Price History Chart -->
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-header bg-white border-bottom py-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <span class="fw-semibold"><i class="bi bi-bar-chart-line text-success me-2"></i>Price History</span>
      <div class="d-flex gap-2 flex-wrap">
        <select id="chartCrop" class="form-select form-select-sm" style="width:160px">
          <?php foreach ($crops as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $cropId == $c['id'] ? 'selected' : '' ?>><?= Helper::e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <select id="chartMonths" class="form-select form-select-sm" style="width:120px">
          <option value="6">6 Months</option>
          <option value="12" selected>12 Months</option>
          <option value="24">24 Months</option>
        </select>
      </div>
    </div>
  </div>
  <div class="card-body">
    <div id="chartEmpty" class="text-center py-5 text-muted d-none">
      <i class="bi bi-bar-chart fs-1 d-block mb-2 opacity-25"></i>No price history available for this crop.
    </div>
    <canvas id="priceChart" height="90"></canvas>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const appUrl = '<?= APP_URL ?>';
  const canvas = document.getElementById('priceChart');
  const empty  = document.getElementById('chartEmpty');
  let chart    = null;

  function loadChart() {
    const cropId = document.getElementById('chartCrop').value;
    const months = document.getElementById('chartMonths').value;

    fetch(`${appUrl}/api/price-chart/${cropId}?months=${months}`)
      .then(r => r.json())
      .then(data => {
        if (!data.labels || data.labels.length === 0) {
          canvas.classList.add('d-none');
          empty.classList.remove('d-none');
          return;
        }
        canvas.classList.remove('d-none');
        empty.classList.add('d-none');

        const labels = data.labels.map(d => {
          const dt = new Date(d + 'T00:00:00');
          return dt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        });

        if (chart) chart.destroy();
        chart = new Chart(canvas, {
          type: 'line',
          data: {
            labels,
            datasets: [{
              label: 'Price (RWF/kg)',
              data: data.prices,
              borderColor: '#198754',
              backgroundColor: 'rgba(25,135,84,0.08)',
              borderWidth: 2.5,
              pointBackgroundColor: '#198754',
              pointRadius: 4,
              pointHoverRadius: 6,
              fill: true,
              tension: 0.4
            }]
          },
          options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
              legend: { display: false },
              tooltip: { callbacks: { label: ctx => ' ' + Number(ctx.parsed.y).toLocaleString() + ' RWF/kg' } }
            },
            scales: {
              x: { grid: { display: false }, ticks: { maxTicksLimit: 12 } },
              y: {
                beginAtZero: false,
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: { callback: v => Number(v).toLocaleString() + ' RWF' }
              }
            }
          }
        });
      })
      .catch(() => { canvas.classList.add('d-none'); empty.classList.remove('d-none'); });
  }

  document.getElementById('chartCrop').addEventListener('change', loadChart);
  document.getElementById('chartMonths').addEventListener('change', loadChart);
  loadChart();
});
</script>
