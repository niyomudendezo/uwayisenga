<footer class="site-footer bg-dark text-white py-4">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <h6 class="fw-bold text-success"><i class="bi bi-tree-fill me-2"></i><?= APP_NAME ?></h6>
        <p class="small text-white-50">AI-Based Agriculture Cooperative and Market Linkage System for Rwanda. Empowering farmers with data-driven insights.</p>
      </div>
      <div class="col-md-2">
        <h6 class="fw-semibold">Quick Links</h6>
        <ul class="list-unstyled small text-white-50">
          <li><a href="<?= APP_URL ?>/" class="text-white-50 text-decoration-none">Home</a></li>
          <li><a href="<?= APP_URL ?>/market-prices" class="text-white-50 text-decoration-none">Market Prices</a></li>
          <li><a href="<?= APP_URL ?>/about" class="text-white-50 text-decoration-none">About</a></li>
          <li><a href="<?= APP_URL ?>/contact" class="text-white-50 text-decoration-none">Contact</a></li>
        </ul>
      </div>
      <div class="col-md-3">
        <h6 class="fw-semibold">Contact</h6>
        <ul class="list-unstyled small text-white-50">
          <li><i class="bi bi-geo-alt me-1"></i>Kigali, Rwanda</li>
          <li><i class="bi bi-telephone me-1"></i>+250 788 000 000</li>
          <li><i class="bi bi-envelope me-1"></i>info@agrukrwanda.rw</li>
        </ul>
      </div>
      <div class="col-md-3">
        <h6 class="fw-semibold">For Farmers & Buyers</h6>
        <a href="<?= APP_URL ?>/register" class="btn btn-success btn-sm me-2">Register</a>
        <a href="<?= APP_URL ?>/login" class="btn btn-outline-light btn-sm">Login</a>
      </div>
    </div>
    <hr class="border-secondary">
    <div class="text-center small text-white-50">
      &copy; <?= date('Y') ?> <?= APP_NAME ?> — Final Year Project, Bachelor of Information Technology, Rwanda
    </div>
  </div>
</footer>
