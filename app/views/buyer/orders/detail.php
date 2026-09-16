<?php $title = 'Order #' . $order['order_no']; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-receipt text-success me-2"></i>Order #<?= Helper::e($order['order_no']) ?></h5>
  <a href="<?= APP_URL ?>/buyer/orders" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Back to Orders
  </a>
</div>

<div class="row g-4">

  <!-- Order Info -->
  <div class="col-md-8">
    <div class="card border-0 shadow-sm rounded-3 mb-3">
      <div class="card-header bg-white fw-semibold border-bottom">Order Items</div>
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
                <td colspan="3" class="fw-bold text-end">Total Amount</td>
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
      <div class="card-header bg-white fw-semibold border-bottom">Order Summary</div>
      <div class="card-body">
        <ul class="list-unstyled mb-0">
          <li class="mb-2 d-flex justify-content-between">
            <span class="text-muted small">Status</span>
            <?= Helper::statusBadge($order['status']) ?>
          </li>
          <li class="mb-2 d-flex justify-content-between">
            <span class="text-muted small">Cooperative</span>
            <span class="small fw-semibold"><?= Helper::e($order['cooperative_name']) ?></span>
          </li>
          <li class="mb-2 d-flex justify-content-between">
            <span class="text-muted small">Order Date</span>
            <span class="small"><?= Helper::formatDate($order['created_at']) ?></span>
          </li>
          <li class="d-flex justify-content-between">
            <span class="text-muted small">Total</span>
            <span class="fw-bold text-success"><?= Helper::formatCurrency($order['total_amount']) ?></span>
          </li>
        </ul>
      </div>
    </div>

    <?php if ($order['status'] === 'pending'): ?>
    <form method="POST" action="<?= APP_URL ?>/buyer/orders/<?= $order['id'] ?>/cancel">
      <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
      <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Cancel this order?')">
        <i class="bi bi-x-circle me-1"></i>Cancel Order
      </button>
    </form>
    <?php endif; ?>
  </div>

</div>
