<?php $title = 'System Settings'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-gear text-success me-2"></i>Settings</h5>
</div>
<form method="POST" action="<?= APP_URL ?>/admin/settings">
  <?= Auth::csrfField() ?>
  <div class="row g-3">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 fw-semibold">General Settings</div>
        <div class="card-body">
          <div class="mb-3"><label class="form-label fw-semibold small">Site Name</label><input type="text" name="site_name" class="form-control" value="<?= Helper::e($settings['site_name'] ?? '') ?>"></div>
          <div class="mb-3"><label class="form-label fw-semibold small">Site Email</label><input type="email" name="site_email" class="form-control" value="<?= Helper::e($settings['site_email'] ?? '') ?>"></div>
          <div class="mb-3"><label class="form-label fw-semibold small">Site Phone</label><input type="text" name="site_phone" class="form-control" value="<?= Helper::e($settings['site_phone'] ?? '') ?>"></div>
          <div class="mb-3"><label class="form-label fw-semibold small">Currency</label><input type="text" name="currency" class="form-control" value="<?= Helper::e($settings['currency'] ?? 'RWF') ?>"></div>
          <div class="mb-3"><label class="form-label fw-semibold small">Items Per Page</label><input type="number" name="items_per_page" class="form-control" value="<?= Helper::e($settings['items_per_page'] ?? '15') ?>"></div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card border-0 shadow-sm rounded-3 mb-3">
        <div class="card-header bg-white border-0 fw-semibold">AI Settings</div>
        <div class="card-body">
          <div class="mb-3"><label class="form-label fw-semibold small">AI Service URL</label><input type="text" name="ai_service_url" class="form-control" value="<?= Helper::e($settings['ai_service_url'] ?? 'http://localhost:8000') ?>"></div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">AI Enabled</label>
            <select name="ai_enabled" class="form-select">
              <option value="0" <?= ($settings['ai_enabled'] ?? '0') == '0' ? 'selected' : '' ?>>Disabled (Use Local Engine)</option>
              <option value="1" <?= ($settings['ai_enabled'] ?? '0') == '1' ? 'selected' : '' ?>>Enabled (Use External API)</option>
            </select>
          </div>
        </div>
      </div>
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 fw-semibold">Inventory Settings</div>
        <div class="card-body">
          <div class="mb-3"><label class="form-label fw-semibold small">Low Stock Threshold (kg)</label><input type="number" name="low_stock_threshold" class="form-control" value="<?= Helper::e($settings['low_stock_threshold'] ?? '100') ?>"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="mt-3">
    <button type="submit" class="btn btn-success"><i class="bi bi-save me-2"></i>Save Settings</button>
  </div>
</form>
