<?php $title = 'Reports'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-bar-graph text-success me-2"></i>Reports</h5>
</div>

<div class="row g-3">
  <?php $reports = [
    ['Members Report',    'bi-people',        'List of all cooperative members with farm details.',       'success', '/cooperative/reports/members'],
    ['Harvests Report',   'bi-basket',        'All harvest records submitted by members.',                'primary', '/cooperative/reports/harvests'],
    ['Inventory Report',  'bi-boxes',         'Current inventory levels and valuation.',                  'warning', '/cooperative/reports/inventory'],
    ['Orders Report',     'bi-cart3',         'Order history with status and revenue breakdown.',         'info',    '/cooperative/reports/orders'],
    ['AI Predictions',    'bi-robot',         'Summary of AI predictions and recommendations.',           'danger',  '/cooperative/reports/ai'],
  ]; ?>
  <?php foreach ($reports as [$label, $icon, $desc, $color, $url]): ?>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-3 h-100">
      <div class="card-body text-center p-4">
        <div class="rounded-circle bg-<?= $color ?> bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px">
          <i class="bi <?= $icon ?> text-<?= $color ?> fs-3"></i>
        </div>
        <h6 class="fw-bold"><?= $label ?></h6>
        <p class="text-muted small"><?= $desc ?></p>
        <div class="d-flex gap-2 justify-content-center">
          <a href="<?= APP_URL . $url ?>?format=pdf" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-file-pdf me-1"></i>PDF
          </a>
          <a href="<?= APP_URL . $url ?>?format=csv" class="btn btn-sm btn-outline-success">
            <i class="bi bi-filetype-csv me-1"></i>CSV
          </a>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
