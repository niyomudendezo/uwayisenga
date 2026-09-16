<?php $title = 'About Us'; ?>

<section class="py-5 bg-success text-white text-center">
  <div class="container">
    <h1 class="fw-bold mb-2">About <?= APP_NAME ?></h1>
    <p class="lead opacity-90 mb-0">Transforming Rwanda's agricultural market through technology</p>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <div class="row g-5 align-items-center mb-5">
      <div class="col-md-6">
        <h2 class="fw-bold mb-3">Our Mission</h2>
        <p class="text-muted">
          <?= APP_NAME ?> is an AI-powered agricultural market linkage platform designed to connect Rwanda's farmers, cooperatives, and buyers. We eliminate middlemen, provide real-time market prices, and use artificial intelligence to forecast demand and optimize trade.
        </p>
        <p class="text-muted">
          Our platform empowers smallholder farmers with data-driven insights, helping them make informed decisions about when and where to sell their produce for maximum profit.
        </p>
      </div>
      <div class="col-md-6">
        <div class="row g-3 text-center">
          <?php $values = [
            ['bi-robot','AI-Powered','Smart predictions and recommendations'],
            ['bi-people','Community','Connecting farmers across Rwanda'],
            ['bi-shield-check','Trusted','Verified buyers and secure transactions'],
            ['bi-graph-up','Growth','Maximizing agricultural income'],
          ]; ?>
          <?php foreach ($values as [$icon, $title, $desc]): ?>
          <div class="col-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100">
              <i class="bi <?= $icon ?> text-success fs-2 mb-2"></i>
              <div class="fw-bold"><?= $title ?></div>
              <div class="small text-muted"><?= $desc ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="text-center py-4">
      <a href="<?= APP_URL ?>/register" class="btn btn-success btn-lg me-3">
        <i class="bi bi-person-plus me-2"></i>Join Us Today
      </a>
      <a href="<?= APP_URL ?>/contact" class="btn btn-outline-success btn-lg">
        <i class="bi bi-envelope me-2"></i>Contact Us
      </a>
    </div>
  </div>
</section>
