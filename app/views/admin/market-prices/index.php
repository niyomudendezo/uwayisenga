<?php $title = 'Market Prices'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-graph-up text-success me-2"></i>Market Prices</h5>
  <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addPriceModal">
    <i class="bi bi-plus-lg me-1"></i>Add Price
  </button>
</div>

<!-- Trend Summary Cards -->
<?php if (!empty($trends)): ?>
<div class="row g-3 mb-4">
  <?php foreach (array_slice($trends, 0, 4) as $t): ?>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3 h-100">
      <div class="card-body">
        <div class="fw-semibold text-truncate"><?= Helper::e($t['crop_name']) ?></div>
        <div class="fs-4 fw-bold text-success mt-1">
          <?= Helper::formatCurrency($t['latest_price']) ?>
          <small class="fs-6 text-muted fw-normal">/<?= Helper::e($t['unit']) ?></small>
        </div>
        <div class="d-flex justify-content-between small text-muted mt-2">
          <span><i class="bi bi-arrow-down text-danger"></i> <?= Helper::formatCurrency($t['min_price']) ?></span>
          <span><i class="bi bi-arrow-up text-success"></i> <?= Helper::formatCurrency($t['max_price']) ?></span>
        </div>
        <div class="small text-muted mt-1">Avg: <?= Helper::formatCurrency($t['avg_price']) ?></div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Price History Chart -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
  <div class="card-header bg-white border-bottom py-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <span class="fw-semibold"><i class="bi bi-bar-chart-line text-success me-2"></i>Price History Chart</span>
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <select id="chartCropSelect" class="form-select form-select-sm" style="width:160px">
          <?php foreach ($crops as $c): ?>
          <option value="<?= $c['id'] ?>"><?= Helper::e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <select id="chartDistrict" class="form-select form-select-sm" style="width:160px">
          <option value="0">All Districts (avg)</option>
          <?php foreach ($districts as $d): ?>
          <option value="<?= $d['id'] ?>"><?= Helper::e($d['name']) ?></option>
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
    <div id="chartLoading" class="text-center py-4 text-muted small d-none">
      <div class="spinner-border spinner-border-sm text-success me-2"></div>Loading chart...
    </div>
    <div id="chartEmpty" class="text-center py-5 text-muted d-none">
      <i class="bi bi-bar-chart fs-1 d-block mb-2 opacity-25"></i>No price history available for this crop.
    </div>
    <canvas id="priceChart" height="90"></canvas>
  </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-3 mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <select name="crop" class="form-select form-select-sm">
          <option value="">All Crops</option>
          <?php foreach ($crops as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $cropId == $c['id'] ? 'selected' : '' ?>><?= Helper::e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <select name="district" class="form-select form-select-sm">
          <option value="">All Districts</option>
          <?php foreach ($districts as $d): ?>
          <option value="<?= $d['id'] ?>" <?= $distId == $d['id'] ? 'selected' : '' ?>><?= Helper::e($d['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-success btn-sm">Filter</button>
        <a href="<?= APP_URL ?>/admin/market-prices" class="btn btn-outline-secondary btn-sm">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- Prices Table -->
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Crop</th>
            <th>Price / kg</th>
            <th>District</th>
            <th>Date</th>
            <th>Source</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($result['data'] as $p): ?>
          <tr>
            <td class="fw-semibold"><?= Helper::e($p['crop_name']) ?></td>
            <td class="text-success fw-bold"><?= Helper::formatCurrency($p['price']) ?></td>
            <td><?= Helper::e($p['district_name'] ?? 'National') ?></td>
            <td><?= Helper::formatDate($p['price_date']) ?></td>
            <td class="small text-muted"><?= Helper::e($p['source'] ?? '-') ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($result['data'])): ?>
          <tr><td colspan="5" class="text-center text-muted py-4">No price records found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php if ($result['last_page'] > 1): ?>
  <div class="card-footer bg-white d-flex justify-content-between align-items-center">
    <small class="text-muted">Showing <?= count($result['data']) ?> of <?= $result['total'] ?> records</small>
    <?= Helper::paginate($result, APP_URL . '/admin/market-prices') ?>
  </div>
  <?php endif; ?>
</div>

<!-- Add Price Modal -->
<div class="modal fade" id="addPriceModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Add Market Price</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="<?= APP_URL ?>/admin/market-prices/store">
        <?= Auth::csrfField() ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold small">Crop <span class="text-danger">*</span></label>
            <select name="crop_id" class="form-select" required>
              <option value="">Select crop...</option>
              <?php foreach ($crops as $c): ?>
              <option value="<?= $c['id'] ?>"><?= Helper::e($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Price (RWF/kg) <span class="text-danger">*</span></label>
              <input type="number" name="price" class="form-control" step="0.01" min="0" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Date <span class="text-danger">*</span></label>
              <input type="date" name="price_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
          </div>
          <div class="mb-3 mt-2">
            <label class="form-label fw-semibold small">District</label>
            <select name="district_id" class="form-select">
              <option value="">National (All Districts)</option>
              <?php foreach ($districts as $d): ?>
              <option value="<?= $d['id'] ?>"><?= Helper::e($d['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Source</label>
            <input type="text" name="source" class="form-control" value="RAB Market Survey">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Save Price</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const appUrl  = '<?= APP_URL ?>';
  const canvas  = document.getElementById('priceChart');
  const loading = document.getElementById('chartLoading');
  const empty   = document.getElementById('chartEmpty');
  let chart     = null;

  function loadChart() {
    const cropId     = document.getElementById('chartCropSelect').value;
    const districtId = document.getElementById('chartDistrict').value;
    const months     = document.getElementById('chartMonths').value;
    if (!cropId) return;

    loading.classList.remove('d-none');
    canvas.classList.add('d-none');
    empty.classList.add('d-none');

    fetch(`${appUrl}/api/price-chart/${cropId}?months=${months}&district=${districtId}`)
      .then(r => r.json())
      .then(data => {
        loading.classList.add('d-none');

        if (!data.labels || data.labels.length === 0) {
          empty.classList.remove('d-none');
          return;
        }

        canvas.classList.remove('d-none');

        const labels = data.labels.map(d => {
          const dt = new Date(d + 'T00:00:00');
          return dt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        });

        if (chart) chart.destroy();
        chart = new Chart(canvas, {
          type: 'line',
          data: {
            labels: labels,
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
              tooltip: {
                callbacks: {
                  label: ctx => ' ' + Number(ctx.parsed.y).toLocaleString() + ' RWF/kg'
                }
              }
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
      .catch(() => {
        loading.classList.add('d-none');
        empty.classList.remove('d-none');
      });
  }

  document.getElementById('chartCropSelect').addEventListener('change', loadChart);
  document.getElementById('chartDistrict').addEventListener('change', loadChart);
  document.getElementById('chartMonths').addEventListener('change', loadChart);
  loadChart();
});
</script>
