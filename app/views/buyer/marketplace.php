<?php $title = 'Marketplace'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-shop text-success me-2"></i>Marketplace</h5>
  <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#offerModal">
    <i class="bi bi-tag me-1"></i>Post Offer
  </button>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search crops, cooperatives..." value="<?= Helper::e($search) ?>">
      </div>
      <div class="col-md-2">
        <select name="crop" class="form-select form-select-sm">
          <option value="">All Crops</option>
          <?php foreach ($crops as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $cropId == $c['id'] ? 'selected' : '' ?>><?= Helper::e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <select name="district" class="form-select form-select-sm">
          <option value="">All Districts</option>
          <?php foreach ($districts as $d): ?>
            <option value="<?= $d['id'] ?>" <?= $districtId == $d['id'] ? 'selected' : '' ?>><?= Helper::e($d['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <input type="number" name="min_qty" class="form-control form-control-sm" placeholder="Min Qty (kg)" value="<?= $minQty ?: '' ?>">
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-success btn-sm">Search</button>
        <a href="<?= APP_URL ?>/buyer/marketplace" class="btn btn-outline-secondary btn-sm">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- Results -->
<?php if (empty($items)): ?>
<div class="text-center py-5 text-muted">
  <i class="bi bi-inbox fs-1 d-block mb-2"></i>
  No crops available matching your criteria.
</div>
<?php else: ?>
<div class="row g-3">
  <?php foreach ($items as $item): ?>
  <div class="col-md-4 col-lg-3">
    <div class="card border-0 shadow-sm rounded-3 h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <h6 class="fw-bold mb-0"><?= Helper::e($item['crop_name']) ?></h6>
            <?php if ($item['variety']): ?>
            <small class="text-muted"><?= Helper::e($item['variety']) ?></small>
            <?php endif; ?>
          </div>
          <span class="badge bg-success">Grade <?= $item['grade'] ?></span>
        </div>

        <div class="fs-4 fw-bold text-success mb-1">
          <?= Helper::formatCurrency($item['asking_price']) ?><small class="fs-6 text-muted">/kg</small>
        </div>

        <div class="small text-muted mb-2">
          <i class="bi bi-boxes me-1"></i><?= number_format($item['qty_available']) ?> kg available<br>
          <i class="bi bi-building me-1"></i><?= Helper::e($item['cooperative_name']) ?><br>
          <i class="bi bi-geo-alt me-1"></i><?= Helper::e($item['district_name'] ?? 'N/A') ?>
        </div>

        <?php if ($item['harvest_date']): ?>
        <div class="small text-muted mb-3">
          <i class="bi bi-calendar me-1"></i>Harvested: <?= Helper::formatDate($item['harvest_date']) ?>
        </div>
        <?php endif; ?>

        <button class="btn btn-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#orderModal"
          data-id="<?= $item['id'] ?>"
          data-crop="<?= Helper::e($item['crop_name']) ?>"
          data-price="<?= $item['asking_price'] ?>"
          data-available="<?= $item['qty_available'] ?>"
          data-coop="<?= Helper::e($item['cooperative_name']) ?>">
          <i class="bi bi-cart-plus me-1"></i>Place Order
        </button>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Order Modal -->
<div class="modal fade" id="orderModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-cart-plus me-2"></i>Place Order</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="<?= APP_URL ?>/buyer/orders/place">
        <?= Auth::csrfField() ?>
        <input type="hidden" name="inventory_id" id="orderInventoryId">
        <div class="modal-body">
          <div class="alert alert-success py-2 small" id="orderCropInfo"></div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Quantity (kg) *</label>
            <input type="number" name="quantity" id="orderQty" class="form-control" step="0.01" min="1" required>
            <div class="form-text" id="orderAvailable"></div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Estimated Total</label>
            <div class="fs-5 fw-bold text-success" id="orderTotal">—</div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Delivery Date</label>
            <input type="date" name="delivery_date" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Delivery Address *</label>
            <textarea name="delivery_address" class="form-control" rows="2" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="bi bi-cart-check me-1"></i>Confirm Order</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Post Offer Modal -->
<div class="modal fade" id="offerModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Post Buying Offer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="<?= APP_URL ?>/buyer/offers/store">
        <?= Auth::csrfField() ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold small">Crop *</label>
            <select name="crop_id" class="form-select" required>
              <option value="">Select crop...</option>
              <?php foreach ($crops as $c): ?>
                <option value="<?= $c['id'] ?>"><?= Helper::e($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Offer Price (RWF/kg) *</label>
              <input type="number" name="price" class="form-control" step="0.01" min="0" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Quantity Needed (kg)</label>
              <input type="number" name="quantity" class="form-control" step="0.01" min="0">
            </div>
          </div>
          <div class="row g-2 mt-1">
            <div class="col-md-6">
              <label class="form-label fw-semibold small">District</label>
              <select name="district_id" class="form-select">
                <option value="">Any District</option>
                <?php foreach ($districts as $d): ?>
                  <option value="<?= $d['id'] ?>"><?= Helper::e($d['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Valid Until</label>
              <input type="date" name="valid_until" class="form-control">
            </div>
          </div>
          <div class="mt-2">
            <label class="form-label fw-semibold small">Notes</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Post Offer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('orderModal').addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  const id        = btn.dataset.id;
  const crop      = btn.dataset.crop;
  const price     = parseFloat(btn.dataset.price);
  const available = parseFloat(btn.dataset.available);
  const coop      = btn.dataset.coop;

  document.getElementById('orderInventoryId').value = id;
  document.getElementById('orderCropInfo').textContent = `${crop} from ${coop} — ${price.toLocaleString()} RWF/kg`;
  document.getElementById('orderAvailable').textContent = `Available: ${available.toLocaleString()} kg`;
  document.getElementById('orderQty').max = available;

  document.getElementById('orderQty').addEventListener('input', function() {
    const total = this.value * price;
    document.getElementById('orderTotal').textContent = total > 0 ? total.toLocaleString() + ' RWF' : '—';
  });
});
</script>
