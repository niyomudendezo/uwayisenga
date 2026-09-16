<?php $title = 'Sale #' . $order['order_no']; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-receipt text-success me-2"></i>Sale #<?= Helper::e($order['order_no']) ?></h5>
  <a href="<?= APP_URL ?>/farmer/sales-history" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Back
  </a>
</div>

<div class="row g-4">

  <!-- Items -->
  <div class="col-md-8">
    <div class="card border-0 shadow-sm rounded-3 mb-3">
      <div class="card-header bg-white fw-semibold border-bottom">Items Sold</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr><th>Crop</th><th>Quantity</th><th>Unit Price</th><th>Total</th></tr>
            </thead>
            <tbody>
              <?php foreach ($items as $item): ?>
              <tr>
                <td class="fw-semibold"><?= Helper::e($item['crop_name']) ?></td>
                <td><?= number_format($item['quantity']) ?> <?= Helper::e($item['unit']) ?></td>
                <td><?= Helper::formatCurrency($item['unit_price']) ?></td>
                <td class="fw-semibold text-success"><?= Helper::formatCurrency($item['quantity'] * $item['unit_price']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
              <tr>
                <td colspan="3" class="fw-bold text-end">Total</td>
                <td class="fw-bold text-success fs-5"><?= Helper::formatCurrency($order['total_amount']) ?></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <?php if (!empty($order['delivery_addr'])): ?>
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white fw-semibold border-bottom">Delivery Details</div>
      <div class="card-body">
        <p class="mb-1"><i class="bi bi-geo-alt text-success me-2"></i><?= Helper::e($order['delivery_addr']) ?></p>
        <?php if (!empty($order['delivery_date'])): ?>
        <p class="mb-0"><i class="bi bi-calendar text-success me-2"></i><?= Helper::formatDate($order['delivery_date']) ?></p>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Summary -->
  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-3 mb-3">
      <div class="card-header bg-white fw-semibold border-bottom">Sale Summary</div>
      <div class="card-body">
        <ul class="list-unstyled mb-0">
          <li class="mb-2 d-flex justify-content-between">
            <span class="text-muted small">Status</span>
            <?= Helper::statusBadge($order['status']) ?>
          </li>
          <li class="mb-2 d-flex justify-content-between">
            <span class="text-muted small">Sale Date</span>
            <span class="small"><?= Helper::formatDate($order['created_at']) ?></span>
          </li>
          <li class="mb-2 d-flex justify-content-between">
            <span class="text-muted small">Cooperative</span>
            <span class="small fw-semibold"><?= Helper::e($order['cooperative_name']) ?></span>
          </li>
          <li class="d-flex justify-content-between">
            <span class="text-muted small">Total Amount</span>
            <span class="fw-bold text-success"><?= Helper::formatCurrency($order['total_amount']) ?></span>
          </li>
        </ul>
      </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white fw-semibold border-bottom">Buyer Information</div>
      <div class="card-body">
        <ul class="list-unstyled mb-0">
          <li class="mb-2">
            <div class="text-muted small">Name</div>
            <div class="fw-semibold"><?= Helper::e(trim(($order['company_name'] ?? '') ?: ($order['first_name'] . ' ' . $order['last_name']))) ?></div>
          </li>
          <?php if (!empty($order['buyer_email'])): ?>
          <li class="mb-2">
            <div class="text-muted small">Email</div>
            <div><?= Helper::e($order['buyer_email']) ?></div>
          </li>
          <?php endif; ?>
          <?php if (!empty($order['buyer_phone'])): ?>
          <li>
            <div class="text-muted small">Phone</div>
            <div><?= Helper::e($order['buyer_phone']) ?></div>
          </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </div>

</div>
