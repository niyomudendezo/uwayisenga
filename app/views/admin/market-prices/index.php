<?php $title = 'Market Prices'; ?>
<div class="admin-prices-page">
<header class="price-admin-header">
  <div><span class="dashboard-eyebrow">Market intelligence</span><h1>Market prices</h1><p>Monitor crop price movement, compare districts and maintain reliable market records.</p></div>
  <button class="btn price-add-btn" data-bs-toggle="modal" data-bs-target="#addPriceModal"><i class="bi bi-plus-lg"></i> Record new price</button>
</header>

<!-- Trend Summary Cards -->
<?php if (!empty($trends)): ?>
<div class="price-admin-stats">
  <?php foreach (array_slice($trends, 0, 4) as $index => $t): ?>
    <article class="price-admin-stat">
      <div class="price-stat-top"><span class="price-stat-icon price-stat-icon-<?= ($index % 4) + 1 ?>"><i class="bi bi-flower1"></i></span><span class="price-current"><i></i> Current</span></div>
      <div class="card-body">
        <div class="price-stat-crop"><?= Helper::e($t['crop_name']) ?></div>
        <div class="price-stat-value">
          <?= Helper::formatCurrency($t['latest_price']) ?>
          <small>/<?= Helper::e($t['unit']) ?></small>
        </div>
        <div class="price-stat-range">
          <span><small>3-mo low</small><strong><?= Helper::formatCurrency($t['min_price']) ?></strong></span>
          <span><small>3-mo high</small><strong><?= Helper::formatCurrency($t['max_price']) ?></strong></span>
        </div>
        <div class="price-stat-average">Market average <strong><?= Helper::formatCurrency($t['avg_price']) ?></strong></div>
      </div>
    </article>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Price History Chart -->
<div class="card price-chart-card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div class="price-card-title"><span><i class="bi bi-bar-chart-fill"></i></span><div><strong>Market price diagram</strong><small>Historical prices compared by recorded date</small></div></div>
      <div class="price-chart-controls">
        <label><span>Crop</span><select id="chartCropSelect" class="form-select">
          <option value="0" <?= !$cropId ? 'selected' : '' ?>>All crops — comparison</option>
          <?php foreach ($crops as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $cropId == $c['id'] ? 'selected' : '' ?>><?= Helper::e($c['name']) ?></option>
          <?php endforeach; ?>
        </select></label>
        <label><span>Market area</span><select id="chartDistrict" class="form-select">
          <option value="0">All districts — average</option>
          <?php foreach ($districts as $d): ?>
          <option value="<?= $d['id'] ?>" <?= $distId == $d['id'] ? 'selected' : '' ?>><?= Helper::e($d['name']) ?></option>
          <?php endforeach; ?>
        </select></label>
        <label><span>Period</span><select id="chartMonths" class="form-select">
          <option value="6">6 Months</option>
          <option value="12" selected>12 Months</option>
          <option value="24">24 Months</option>
        </select></label>
      </div>
    </div>
  </div>
  <div class="card-body price-chart-body">
    <div class="price-chart-summary" id="priceChartSummary">
      <div><span>Selected market</span><strong id="chartSelectionLabel">—</strong></div>
      <div><span>Lowest</span><strong id="chartMinPrice">—</strong></div>
      <div><span>Average</span><strong id="chartAvgPrice">—</strong></div>
      <div><span>Highest</span><strong id="chartMaxPrice">—</strong></div>
    </div>
    <div id="chartLoading" class="text-center py-4 text-muted small d-none">
      <div class="spinner-border spinner-border-sm text-success me-2"></div>Loading chart...
    </div>
    <div id="chartEmpty" class="text-center py-5 text-muted d-none">
      <i class="bi bi-bar-chart fs-1 d-block mb-2 opacity-25"></i>No price history available for this crop.
    </div>
    <div class="price-chart-canvas"><canvas id="priceChart"></canvas></div>
  </div>
</div>

<!-- Filters -->
<div class="price-ledger-head"><div><h2>Price records</h2><p>Complete market price ledger</p></div><span><?= number_format((int)$result['total']) ?> total records</span></div>
<div class="price-filter-panel">
    <form method="GET" class="price-filter-form">
      <label><span>Crop</span><select name="crop" class="form-select">
          <option value="">All Crops</option>
          <?php foreach ($crops as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $cropId == $c['id'] ? 'selected' : '' ?>><?= Helper::e($c['name']) ?></option>
          <?php endforeach; ?>
        </select></label>
      <label><span>District</span><select name="district" class="form-select">
          <option value="">All Districts</option>
          <?php foreach ($districts as $d): ?>
          <option value="<?= $d['id'] ?>" <?= $distId == $d['id'] ? 'selected' : '' ?>><?= Helper::e($d['name']) ?></option>
          <?php endforeach; ?>
        </select></label>
      <div class="price-filter-actions"><button type="submit" class="btn"><i class="bi bi-funnel"></i> Apply filters</button><?php if ($cropId || $distId): ?><a href="<?= APP_URL ?>/admin/market-prices"><i class="bi bi-x-circle"></i> Clear</a><?php endif; ?></div>
    </form>
</div>

<!-- Prices Table -->
<div class="card price-ledger-card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table price-ledger-table align-middle mb-0">
        <thead>
          <tr>
            <th>Crop</th>
            <th>Market price</th>
            <th>District</th>
            <th>Date</th>
            <th>Source</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($result['data'] as $index => $p): ?>
          <tr>
            <td><div class="price-crop-cell"><span class="price-stat-icon price-stat-icon-<?= ($index % 4) + 1 ?>"><i class="bi bi-flower1"></i></span><div><strong><?= Helper::e($p['crop_name']) ?></strong><small>per <?= Helper::e($p['unit']) ?></small></div></div></td>
            <td><strong class="ledger-price"><?= Helper::formatCurrency($p['price']) ?></strong></td>
            <td><span class="ledger-location"><i class="bi bi-geo-alt"></i><?= Helper::e($p['district_name'] ?? 'National average') ?></span></td>
            <td><span class="ledger-date"><i class="bi bi-calendar3"></i><?= Helper::formatDate($p['price_date']) ?></span></td>
            <td><span class="ledger-source"><?= Helper::e($p['source'] ?? 'Not specified') ?></span></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($result['data'])): ?>
          <tr><td colspan="5"><div class="price-empty-state"><i class="bi bi-receipt"></i><strong>No price records found</strong><span>Try changing the filters or add a new market price.</span></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php if ($result['last_page'] > 1): ?>
  <div class="card-footer price-ledger-footer d-flex justify-content-between align-items-center">
    <small>Showing <?= count($result['data']) ?> of <?= number_format((int)$result['total']) ?> records</small>
    <?= Helper::paginate($result, APP_URL . '/admin/market-prices') ?>
  </div>
  <?php endif; ?>
</div>

<!-- Add Price Modal -->
<div class="modal fade" id="addPriceModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content price-entry-modal">
      <div class="modal-header">
        <div class="price-modal-title"><span><i class="bi bi-tags"></i></span><div><h5 class="modal-title">Record market price</h5><p>Add a verified crop price to the market ledger.</p></div></div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="<?= APP_URL ?>/admin/market-prices/store">
        <?= Auth::csrfField() ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Crop <span>*</span></label>
            <select name="crop_id" class="form-select" required>
              <option value="">Select crop...</option>
              <?php foreach ($crops as $c): ?>
              <option value="<?= $c['id'] ?>"><?= Helper::e($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">Price (RWF) <span>*</span></label><div class="price-input-group"><i>RWF</i><input type="number" name="price" class="form-control" step="0.01" min="0" placeholder="0.00" required></div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Price date <span>*</span></label>
              <input type="date" name="price_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
          </div>
          <div class="mb-3 mt-2">
            <label class="form-label">Market district</label>
            <select name="district_id" class="form-select">
              <option value="">National (All Districts)</option>
              <?php foreach ($districts as $d): ?>
              <option value="<?= $d['id'] ?>"><?= Helper::e($d['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Information source</label>
            <input type="text" name="source" class="form-control" value="RAB Market Survey">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn price-modal-cancel" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn price-modal-save"><i class="bi bi-check-lg"></i> Save price</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const appUrl  = '<?= APP_URL ?>';
  const canvas  = document.getElementById('priceChart');
  const loading = document.getElementById('chartLoading');
  const empty   = document.getElementById('chartEmpty');
  const chartWrap = document.querySelector('.price-chart-canvas');
  const summary = document.getElementById('priceChartSummary');
  let chart     = null;

  function loadChart() {
    const cropId     = document.getElementById('chartCropSelect').value;
    const districtId = document.getElementById('chartDistrict').value;
    const months     = document.getElementById('chartMonths').value;
    if (!cropId) return;

    loading.classList.remove('d-none');
    chartWrap.classList.add('d-none');
    summary.classList.add('d-none');
    empty.classList.add('d-none');

    fetch(`${appUrl}/api/price-chart/${cropId}?months=${months}&district=${districtId}`)
      .then(r => r.json())
      .then(data => {
        loading.classList.add('d-none');

        if (!data.labels || data.labels.length === 0) {
          empty.classList.remove('d-none');
          return;
        }

        chartWrap.classList.remove('d-none');
        summary.classList.remove('d-none');

        const palette = ['#168452','#e3ad24','#7456c7','#e05c4f','#3184bd','#df64b0','#46a5a0','#ed8438','#66758f','#89a93b'];
        const rawDatasets = data.datasets || [{
          label: document.getElementById('chartCropSelect').options[document.getElementById('chartCropSelect').selectedIndex].text,
          data: data.prices || []
        }];
        const numericPrices = rawDatasets.flatMap(series => series.data)
          .filter(price => price !== null && price !== '').map(Number);
        const formatPrice = value => Number(value).toLocaleString() + ' RWF';
        const cropSelect = document.getElementById('chartCropSelect');
        const districtSelect = document.getElementById('chartDistrict');
        document.getElementById('chartSelectionLabel').textContent = cropSelect.options[cropSelect.selectedIndex].text + ' · ' + districtSelect.options[districtSelect.selectedIndex].text;
        document.getElementById('chartMinPrice').textContent = formatPrice(Math.min(...numericPrices));
        document.getElementById('chartAvgPrice').textContent = formatPrice(numericPrices.reduce((sum, price) => sum + price, 0) / numericPrices.length);
        document.getElementById('chartMaxPrice').textContent = formatPrice(Math.max(...numericPrices));

        const labels = data.labels.map(d => {
          const dt = new Date(d + 'T00:00:00');
          return dt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        });

        if (chart) chart.destroy();
        const chartDatasets = rawDatasets.map((series, index) => ({
          label: series.label,
          data: series.data,
          backgroundColor: palette[index % palette.length],
          hoverBackgroundColor: palette[index % palette.length],
          borderColor: palette[index % palette.length],
          borderWidth: 1,
          borderRadius: 5,
          borderSkipped: false,
          maxBarThickness: cropId === '0' ? 34 : 58,
          minBarLength: 3,
          categoryPercentage: cropId === '0' ? .82 : .72,
          barPercentage: cropId === '0' ? .88 : .82
        }));
        const chartAreaBackground = {
          id: 'chartAreaBackground',
          beforeDraw(chart) {
            const {ctx, chartArea} = chart;
            if (!chartArea) return;
            ctx.save();
            ctx.fillStyle = '#f2f8f4';
            ctx.fillRect(chartArea.left, chartArea.top, chartArea.width, chartArea.height);
            ctx.restore();
          }
        };
        chart = new Chart(canvas, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: chartDatasets
          },
          plugins: [chartAreaBackground],
          options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
              legend: {
                display: true,
                position: 'top',
                align: 'center',
                labels: { usePointStyle: true, pointStyle: 'rectRounded', boxWidth: 10, boxHeight: 10, color: '#405248', font: { size: 12, weight: '600' } }
              },
              tooltip: {
                callbacks: {
                  title: items => items[0].label,
                  label: ctx => ' ' + ctx.dataset.label + ': ' + Number(ctx.parsed.y).toLocaleString() + ' RWF'
                }
              }
            },
            scales: {
              x: { title: { display: true, text: 'Recorded date', color: '#14794c', font: { size: 12, weight: '700' }, padding: { top: 10 } }, grid: { display: false }, border: { color: '#aac4b3' }, ticks: { maxTicksLimit: 8, color: '#56675d', font: { size: 11 }, maxRotation: 40, minRotation: 0 } },
              y: {
                beginAtZero: true,
                grid: { color: 'rgba(44,112,73,.18)', borderDash: [4, 4] },
                border: { color: '#aac4b3' },
                title: { display: true, text: 'Market value (RWF)', color: '#14794c', font: { size: 12, weight: '700' } },
                ticks: { color: '#56675d', font: { size: 11 }, padding: 6, callback: v => Number(v).toLocaleString() + ' RWF' }
              }
            }
          }
        });
      })
      .catch(() => {
        loading.classList.add('d-none');
        chartWrap.classList.add('d-none');
        summary.classList.add('d-none');
        empty.classList.remove('d-none');
      });
  }

  document.getElementById('chartCropSelect').addEventListener('change', loadChart);
  document.getElementById('chartDistrict').addEventListener('change', loadChart);
  document.getElementById('chartMonths').addEventListener('change', loadChart);
  loadChart();
});
</script>
