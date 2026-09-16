<?php $title = 'My Orders'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-cart3 text-success me-2"></i>My Orders</h5>
  <a href="<?= APP_URL ?>/buyer/marketplace" class="btn btn-success btn-sm">
    <i class="bi bi-shop me-1"></i>Browse Marketplace
  </a>
</div>

<!-- Filter by status -->
<div class="card border-0 shadow-sm rounded-3 mb-3">
  <div class="card-body py-2">
    <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
      <select name="status" class="form-select form-select-sm" style="width:160px">
        <option value="">All Statuses</option>
        <?php foreach (['pending','approved','paid','in_delivery','completed','cancelled','rejected'] as $s): ?>
        <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-success btn-sm">Filter</button>
      <?php if ($status): ?>
      <a href="<?= APP_URL ?>/buyer/orders" class="btn btn-outline-secondary btn-sm">Reset</a>
      <?php endif; ?>
    </form>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Order No</th>
            <th>Cooperative</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Delivery Date</th>
            <th>Date</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($result['data'] as $o): ?>
          <tr>
            <td class="fw-semibold"><?= Helper::e($o['order_no']) ?></td>
            <td><?= Helper::e($o['cooperative_name']) ?></td>
            <td class="fw-semibold text-success"><?= Helper::formatCurrency($o['total_amount']) ?></td>
            <td><?= Helper::statusBadge($o['status']) ?></td>
            <td class="small text-muted"><?= $o['delivery_date'] ? Helper::formatDate($o['delivery_date']) : '-' ?></td>
            <td class="small text-muted"><?= Helper::formatDate($o['created_at']) ?></td>
            <td>
              <a href="<?= APP_URL ?>/buyer/orders/<?= $o['id'] ?>" class="btn btn-xs btn-outline-success btn-sm">
                <i class="bi bi-eye"></i>
              </a>
              <?php if ($o['status'] === 'pending'): ?>
              <form method="POST" action="<?= APP_URL ?>/buyer/orders/<?= $o['id'] ?>/cancel" class="d-inline">
                <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <button type="submit" class="btn btn-xs btn-outline-danger btn-sm" onclick="return confirm('Cancel this order?')">
                  <i class="bi bi-x"></i>
                </button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($result['data'])): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">No orders found. <a href="<?= APP_URL ?>/buyer/marketplace">Browse marketplace</a></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php if ($result['last_page'] > 1): ?>
  <div class="card-footer bg-white d-flex justify-content-between align-items-center">
    <small class="text-muted">Showing <?= count($result['data']) ?> of <?= $result['total'] ?> orders</small>
    <?= Helper::paginate($result, APP_URL . '/buyer/orders') ?>
  </div>
  <?php endif; ?>
</div>
