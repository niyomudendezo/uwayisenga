<?php $title = 'Orders'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-cart3 text-success me-2"></i>All Orders</h5>
</div>
<div class="card border-0 shadow-sm rounded-3 mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="<?= Helper::e($search) ?>"></div>
      <div class="col-md-3">
        <select name="status" class="form-select form-select-sm">
          <option value="">All Statuses</option>
          <?php foreach (['pending','approved','rejected','paid','in_delivery','completed','cancelled'] as $s): ?>
            <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-success btn-sm">Filter</button>
        <a href="<?= APP_URL ?>/admin/orders" class="btn btn-outline-secondary btn-sm">Reset</a>
      </div>
    </form>
  </div>
</div>
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Order #</th><th>Buyer</th><th>Cooperative</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
          <?php foreach ($result['data'] as $o): ?>
          <tr>
            <td class="fw-semibold"><?= Helper::e($o['order_no']) ?></td>
            <td><?= Helper::e($o['company_name'] ?: $o['first_name'].' '.$o['last_name']) ?></td>
            <td><?= Helper::e($o['cooperative_name']) ?></td>
            <td><?= Helper::formatCurrency($o['total_amount']) ?></td>
            <td><?= Helper::statusBadge($o['status']) ?></td>
            <td class="small text-muted"><?= Helper::timeAgo($o['created_at']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($result['data'])): ?><tr><td colspan="6" class="text-center text-muted py-4">No orders found.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php if ($result['last_page'] > 1): ?>
  <div class="card-footer bg-white d-flex justify-content-between align-items-center">
    <small class="text-muted">Showing <?= count($result['data']) ?> of <?= $result['total'] ?></small>
    <?= Helper::paginate($result, APP_URL . '/admin/orders') ?>
  </div>
  <?php endif; ?>
</div>
