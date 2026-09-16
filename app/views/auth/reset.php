<div class="row justify-content-center">
  <div class="col-md-5 col-lg-4">
    <div class="card shadow-lg border-0 rounded-4">
      <div class="card-body p-4">
        <div class="text-center mb-4">
          <i class="bi bi-shield-lock-fill text-success fs-1"></i>
          <h5 class="fw-bold mt-2">Reset Password</h5>
        </div>
        <form method="POST" action="<?= APP_URL ?>/reset-password">
          <?= Auth::csrfField() ?>
          <input type="hidden" name="token" value="<?= Helper::e($token) ?>">
          <div class="mb-3">
            <label class="form-label fw-semibold small">New Password</label>
            <input type="password" name="password" class="form-control" minlength="8" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Confirm Password</label>
            <input type="password" name="password_confirm" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-success w-100">Reset Password</button>
        </form>
      </div>
    </div>
  </div>
</div>
