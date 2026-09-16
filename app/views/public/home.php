<?php $title = 'Home'; ?>

<div class="home-page">
  <section class="hero-section position-relative overflow-hidden">
    <video class="hero-background-video" autoplay muted loop playsinline preload="metadata" aria-hidden="true">
      <source src="<?= APP_URL ?>/assets/videos/rwanda-agriculture-progress.mp4" type="video/mp4">
    </video>
    <div class="hero-video-overlay"></div>
    <div class="hero-orb hero-orb-one"></div><div class="hero-orb hero-orb-two"></div>
    <div class="container position-relative">
      <div class="row align-items-center g-5">
        <div class="col-lg-7">
          <div class="hero-kicker mb-4"><i class="bi bi-stars"></i> Smarter agriculture for Rwanda</div>
          <h1 class="hero-title mb-4">Grow more. Sell smarter.<br><span>Prosper together.</span></h1>
          <p class="hero-copy mb-4">One digital marketplace connecting farmers, cooperatives, and trusted buyers with live prices and AI-powered insights.</p>
          <div class="d-flex gap-3 flex-wrap mb-4">
            <a href="<?= APP_URL ?>/register" class="btn btn-light btn-lg hero-primary-btn">Create free account <i class="bi bi-arrow-right ms-2"></i></a>
            <a href="<?= APP_URL ?>/market-prices" class="btn btn-outline-light btn-lg hero-secondary-btn"><i class="bi bi-graph-up-arrow me-2"></i>Explore market prices</a>
          </div>
          <div class="hero-trust d-flex align-items-center flex-wrap gap-3"><span><i class="bi bi-check-circle-fill"></i> Free to join</span><span><i class="bi bi-check-circle-fill"></i> Verified buyers</span><span><i class="bi bi-check-circle-fill"></i> Rwanda-wide</span></div>
        </div>
        <div class="col-lg-5">
          <div class="hero-dashboard">
            <div class="d-flex align-items-center justify-content-between mb-4"><div><span class="dashboard-label">Market snapshot</span><h2 class="h5 mb-0 mt-1">Today in Rwanda</h2></div><span class="live-badge"><span></span> Live</span></div>
            <?php if (!empty($latestPrices)): ?>
              <?php foreach (array_slice($latestPrices, 0, 3) as $index => $p): ?>
                <div class="snapshot-row">
                  <div class="crop-symbol crop-symbol-<?= ($index % 3) + 1 ?>"><i class="bi <?= ['bi-flower1', 'bi-basket2-fill', 'bi-circle-fill'][$index % 3] ?>"></i></div>
                  <div class="flex-grow-1"><strong><?= Helper::e($p['crop_name']) ?></strong><small>per <?= Helper::e($p['unit']) ?></small></div>
                  <div class="text-end"><strong><?= Helper::formatCurrency($p['price']) ?></strong><small class="price-up"><i class="bi bi-arrow-up-short"></i> Market price</small></div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="snapshot-empty"><i class="bi bi-bar-chart-line"></i><span>Fresh market prices will appear here.</span></div>
            <?php endif; ?>
            <a href="<?= APP_URL ?>/market-prices" class="dashboard-link">See all market insights <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>
      </div>
      <div class="hero-stats">
        <?php $heroStats = [['bi-people-fill',$stats['farmers'],'Registered farmers'],['bi-buildings-fill',$stats['cooperatives'],'Active cooperatives'],['bi-patch-check-fill',$stats['buyers'],'Verified buyers'],['bi-grid-fill',$stats['crops'],'Crop varieties']]; ?>
        <?php foreach ($heroStats as [$icon, $value, $label]): ?><div class="hero-stat"><i class="bi <?= $icon ?>"></i><div><strong><?= number_format($value) ?>+</strong><span><?= $label ?></span></div></div><?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="home-section feature-section"><div class="container">
    <div class="row align-items-end mb-5 g-3"><div class="col-lg-7"><span class="section-eyebrow">Everything you need</span><h2 class="section-title">Built to help agriculture thrive</h2></div><div class="col-lg-5"><p class="section-intro mb-0">From harvest planning to final delivery, AgruKrwanda brings every part of the agricultural value chain into one simple platform.</p></div></div>
    <div class="row g-4">
      <?php $features = [['bi-robot','AI price intelligence','Plan confidently with forecasts based on historical prices and seasonal market trends.','green'],['bi-shop-window','Direct market access','Connect with verified buyers across Rwanda and build stronger trading relationships.','amber'],['bi-people','Cooperative tools','Manage members, inventory, harvests, and production from one organized workspace.','blue'],['bi-graph-up-arrow','Demand forecasting','Understand what the market needs, where demand is growing, and when to sell.','purple'],['bi-shield-check','Secure order tracking','Follow every order from placement and approval through payment and delivery.','teal'],['bi-file-earmark-bar-graph','Actionable reports','Turn your activity into clear printable PDF and CSV reports that support better decisions.','red']]; ?>
      <?php foreach ($features as [$icon,$name,$desc,$tone]): ?><div class="col-md-6 col-lg-4"><article class="feature-card h-100"><div class="feature-icon feature-icon-<?= $tone ?>"><i class="bi <?= $icon ?>"></i></div><h3><?= $name ?></h3><p><?= $desc ?></p><span class="feature-arrow"><i class="bi bi-arrow-up-right"></i></span></article></div><?php endforeach; ?>
    </div>
  </div></section>

  <section class="home-section prices-section"><div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4"><div><span class="section-eyebrow">Fresh from the market</span><h2 class="section-title mb-0">Latest crop prices</h2></div><a href="<?= APP_URL ?>/market-prices" class="text-link">Browse all prices <i class="bi bi-arrow-right"></i></a></div>
    <div class="price-grid">
      <?php if (!empty($latestPrices)): ?><?php foreach (array_slice($latestPrices, 0, 8) as $p): ?><article class="price-card"><div class="d-flex align-items-start justify-content-between gap-2"><div class="mini-crop-icon"><i class="bi bi-flower1"></i></div><span class="price-status">Updated</span></div><h3><?= Helper::e($p['crop_name']) ?></h3><div class="price-value"><?= Helper::formatCurrency($p['price']) ?></div><div class="price-unit">per <?= Helper::e($p['unit']) ?></div></article><?php endforeach; ?>
      <?php else: ?><div class="empty-prices"><i class="bi bi-basket2"></i><h3>Market prices are being updated</h3><p>Check back soon for the latest crop prices.</p></div><?php endif; ?>
    </div>
  </div></section>

  <section class="home-section steps-section"><div class="container">
    <div class="text-center mx-auto steps-heading"><span class="section-eyebrow">Simple by design</span><h2 class="section-title">From field to market in four steps</h2><p class="section-intro">Get started quickly, access useful insights, and reach the right buyers.</p></div>
    <div class="row g-4 steps-row">
      <?php $steps = [['01','bi-person-plus','Create your profile','Join as a farmer, cooperative manager, or buyer.'],['02','bi-basket2','Record your harvest','Add produce and keep inventory information current.'],['03','bi-stars','Use smart insights','See price predictions and demand forecasts.'],['04','bi-hand-thumbs-up','Trade with confidence','Connect, order, and track delivery directly.']]; ?>
      <?php foreach ($steps as [$num,$icon,$name,$desc]): ?><div class="col-sm-6 col-lg-3"><article class="step-card"><span class="step-number"><?= $num ?></span><div class="step-icon"><i class="bi <?= $icon ?>"></i></div><h3><?= $name ?></h3><p><?= $desc ?></p></article></div><?php endforeach; ?>
    </div>
  </div></section>

  <section class="cta-wrap"><div class="container"><div class="home-cta"><div class="cta-pattern"></div><div class="row align-items-center g-4 position-relative"><div class="col-lg-8"><span class="cta-kicker">Your next harvest starts here</span><h2>Ready to grow your agricultural business?</h2><p>Join Rwanda's connected agriculture community and make every decision count.</p></div><div class="col-lg-4 text-lg-end"><a href="<?= APP_URL ?>/register" class="btn btn-light btn-lg hero-primary-btn">Get started for free <i class="bi bi-arrow-right ms-2"></i></a><a href="<?= APP_URL ?>/contact" class="cta-contact">Questions? Talk to our team</a></div></div></div></div></section>
</div>
