<div class="row justify-content-center">
  <div class="col-md-5 col-lg-4">
    <div class="card shadow-lg border-0 rounded-4">
      <div class="card-body p-4">
        <div class="text-center mb-4">
          <i class="bi bi-key-fill text-success fs-1"></i>
          <h5 class="fw-bold mt-2">Forgot Password?</h5>
          <p class="text-muted small">Enter your email to receive a reset link.</p>
        </div>
        <form method="POST" action="<?= APP_URL ?>/forgot-password">
          <?= Auth::csrfField() ?>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Email Address</label>
            <input type="email" name="email" class="form-control" required autofocus>
          </div>
          <button type="submit" class="btn btn-success w-100">Send Reset Link</button>
        </form>
        <?php if (!empty($_SESSION['reset_link'])): ?>
          <div class="alert alert-info mt-3 small">
            <strong>Demo:</strong> <a href="<?= $_SESSION['reset_link'] ?>"><?= $_SESSION['reset_link'] ?></a>
          </div>
          <?php unset($_SESSION['reset_link']); ?>
        <?php endif; ?>
        <div class="text-center mt-3">
          <a href="<?= APP_URL ?>/login" class="small text-success">Back to Login</a>
        </div>
      </div>
    </div>
  </div>
</div>
