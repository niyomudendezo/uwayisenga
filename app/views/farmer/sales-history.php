<?php $title = 'Sales History'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-receipt text-success me-2"></i>Sales History</h5>
</div>

<?php if (!empty($sales)): ?>
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <div class="fw-bold fs-3 text-success"><?= count($sales) ?></div>
        <div class="text-muted small">Total Sales</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <div class="fw-bold fs-3 text-primary"><?= Helper::formatCurrency(array_sum(array_column($sales, 'total_amount'))) ?></div>
        <div class="text-muted small">Total Revenue</div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Order No</th>
            <th>Buyer</th>
            <th>Cooperative</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($sales as $s): ?>
          <tr>
            <td class="fw-semibold"><?= Helper::e($s['order_no']) ?></td>
            <td><?= Helper::e(trim(($s['company_name'] ?? '') ?: ($s['first_name'] . ' ' . $s['last_name']))) ?></td>
            <td class="small"><?= Helper::e($s['cooperative_name']) ?></td>
            <td class="fw-semibold text-success"><?= Helper::formatCurrency($s['total_amount']) ?></td>
            <td><?= Helper::statusBadge($s['status']) ?></td>
            <td class="small text-muted"><?= Helper::formatDate($s['created_at']) ?></td>
            <td>
              <a href="<?= APP_URL ?>/farmer/sales-history/<?= $s['id'] ?>" class="btn btn-sm btn-outline-success">
                <i class="bi bi-eye me-1"></i>Details
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($sales)): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">No sales history found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
