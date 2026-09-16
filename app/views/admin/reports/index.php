<?php $title = 'Reports'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-bar-graph text-success me-2"></i>Reports</h5>
</div>
<div class="row g-3">
  <?php $reportTypes = [
    ['Farmers Report','bi-person-badge','Generate a complete list of all registered farmers with their farm details.','success','/admin/reports/farmers'],
    ['Cooperatives Report','bi-building','Overview of all cooperatives, members, and inventory.','primary','/admin/reports/cooperatives'],
    ['Market Prices Report','bi-graph-up','Historical market price data for all crops.','warning','/admin/reports/prices'],
    ['Orders Report','bi-cart3','Complete order history with status and revenue breakdown.','info','/admin/reports/orders'],
    ['Inventory Report','bi-boxes','Current inventory levels across all cooperatives.','success','/admin/reports/inventory'],
    ['AI Predictions Report','bi-robot','Summary of all AI predictions and recommendations.','danger','/admin/reports/ai'],
  ]; ?>
  <?php foreach ($reportTypes as [$title2, $icon, $desc, $color, $url]): ?>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-3 h-100">
      <div class="card-body text-center p-4">
        <div class="rounded-circle bg-<?= $color ?> bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px">
          <i class="bi <?= $icon ?> text-<?= $color ?> fs-3"></i>
        </div>
        <h6 class="fw-bold"><?= $title2 ?></h6>
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
