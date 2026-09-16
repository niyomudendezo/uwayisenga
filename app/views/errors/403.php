<section class="error-state-page">
  <div class="error-state-card">
    <span class="error-code">403</span>
    <div class="error-icon"><i class="bi bi-shield-lock"></i></div>
    <h1>Access denied</h1>
    <p>Your account does not have permission to open this area. Use your role dashboard or return to the homepage.</p>
    <div class="d-flex justify-content-center gap-2 flex-wrap">
      <?php if (Auth::check()): ?><a href="<?= APP_URL . Auth::dashboardUrl() ?>" class="btn btn-success">Go to my dashboard</a><?php endif; ?>
      <a href="<?= APP_URL ?>/" class="btn btn-outline-success">Return home</a>
    </div>
  </div>
</section>
