<?php $title = 'Order #' . $order['order_no']; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h5 class="fw-bold mb-0">Order #<?= Helper::e($order['order_no']) ?></h5>
    <small class="text-muted">Placed <?= Helper::timeAgo($order['created_at']) ?></small>
  </div>
  <div class="d-flex gap-2 align-items-center">
    <?= Helper::statusBadge($order['status']) ?>
    <a href="<?= APP_URL ?>/cooperative/orders" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Back
    </a>
  </div>
</div>

<div class="row g-3">
  <!-- Order Info -->
  <div class="col-md-8">
    <div class="card border-0 shadow-sm rounded-3 mb-3">
      <div class="card-header bg-white border-0 fw-semibold">Order Items</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr><th>Crop</th><th>Quantity</th><th>Unit Price</th><th>Total</th></tr>
            </thead>
            <tbody>
              <?php $grandTotal = 0; foreach ($items as $item): $grandTotal += $item['total_price']; ?>
              <tr>
                <td class="fw-semibold"><?= Helper::e($item['crop_name']) ?></td>
                <td><?= number_format($item['quantity']) ?> <?= $item['unit'] ?></td>
                <td><?= Helper::formatCurrency($item['unit_price']) ?></td>
                <td class="fw-semibold text-success"><?= Helper::formatCurrency($item['total_price']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
              <tr>
                <td colspan="3" class="text-end fw-bold">Grand Total:</td>
                <td class="fw-bold text-success fs-5"><?= Helper::formatCurrency($grandTotal) ?></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <!-- Delivery Info -->
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white border-0 fw-semibold">Delivery Information</div>
      <div class="card-body">
        <div class="row g-2">
          <div class="col-md-6">
            <div class="small text-muted">Delivery Date</div>
            <div class="fw-semibold"><?= $order['delivery_date'] ? Helper::formatDate($order['delivery_date']) : 'Not specified' ?></div>
          </div>
          <div class="col-md-6">
            <div class="small text-muted">Delivery Address</div>
            <div class="fw-semibold"><?= Helper::e($order['delivery_addr'] ?? 'Not specified') ?></div>
          </div>
          <?php if ($order['notes']): ?>
          <div class="col-12">
            <div class="small text-muted">Notes</div>
            <div><?= Helper::e($order['notes']) ?></div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Buyer Info & Actions -->
  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-3 mb-3">
      <div class="card-header bg-white border-0 fw-semibold">Buyer Information</div>
      <div class="card-body">
        <div class="fw-semibold"><?= Helper::e($order['company_name'] ?: $order['first_name'].' '.$order['last_name']) ?></div>
        <div class="small text-muted"><?= Helper::e($order['buyer_email']) ?></div>
        <div class="small text-muted"><?= Helper::e($order['buyer_phone'] ?? '') ?></div>
      </div>
    </div>

    <!-- Order Timeline -->
    <div class="card border-0 shadow-sm rounded-3 mb-3">
      <div class="card-header bg-white border-0 fw-semibold">Order Status</div>
      <div class="card-body">
        <?php
        $steps = ['pending' => 'Order Placed', 'approved' => 'Approved', 'paid' => 'Payment Received', 'in_delivery' => 'In Delivery', 'completed' => 'Completed'];
        $statusOrder = array_keys($steps);
        $currentIdx  = array_search($order['status'], $statusOrder);
        ?>
        <?php foreach ($steps as $key => $label): ?>
          <?php $idx = array_search($key, $statusOrder); ?>
          <div class="d-flex align-items-center gap-2 mb-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:24px;height:24px;flex-shrink:0;background:<?= $idx <= $currentIdx ? '#198754' : '#dee2e6' ?>">
              <i class="bi bi-check text-white small"></i>
            </div>
            <span class="small <?= $idx <= $currentIdx ? 'fw-semibold' : 'text-muted' ?>"><?= $label ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Actions -->
    <?php if ($order['status'] === 'pending'): ?>
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white border-0 fw-semibold">Actions</div>
      <div class="card-body d-grid gap-2">
        <form method="POST" action="<?= APP_URL ?>/cooperative/orders/<?= $order['id'] ?>/approve">
          <?= Auth::csrfField() ?>
          <button type="submit" class="btn btn-success w-100" onclick="return confirm('Approve this order?')">
            <i class="bi bi-check-circle me-1"></i>Approve Order
          </button>
        </form>
        <form method="POST" action="<?= APP_URL ?>/cooperative/orders/<?= $order['id'] ?>/reject">
          <?= Auth::csrfField() ?>
          <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Reject this order?')">
            <i class="bi bi-x-circle me-1"></i>Reject Order
          </button>
        </form>
      </div>
    </div>
    <?php elseif (in_array($order['status'], ['approved','paid','in_delivery'])): ?>
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white border-0 fw-semibold">Actions</div>
      <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/cooperative/orders/<?= $order['id'] ?>/deliver">
          <?= Auth::csrfField() ?>
          <button type="submit" class="btn btn-success w-100" onclick="return confirm('Mark as completed/delivered?')">
            <i class="bi bi-truck me-1"></i>Mark as Delivered
          </button>
        </form>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
