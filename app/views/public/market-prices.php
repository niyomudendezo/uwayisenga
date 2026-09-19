<?php
$title = 'Market Prices';
$priceCount = count($prices);
$cropCount = count(array_unique(array_column($prices, 'crop_name')));
$districtCount = count(array_filter(array_unique(array_column($prices, 'district_name'))));
$latestDate = !empty($prices) ? max(array_map(function($price) { return strtotime($price['price_date']); }, $prices)) : null;
$selectedCropName = 'All crops';
foreach ($crops as $crop) {
    if ((int) $selectedCrop === (int) $crop['id']) { $selectedCropName = $crop['name']; break; }
}
?>
<div class="market-page">
  <section class="market-hero">
    <div class="market-hero-pattern" aria-hidden="true"></div>
    <div class="container position-relative"><div class="row align-items-center g-5">
      <div class="col-lg-7">
        <span class="market-eyebrow"><i class="bi bi-broadcast-pin"></i> Rwanda market intelligence</span>
        <h1>Know the market.<br><span>Sell with confidence.</span></h1>
        <p>Compare recent crop prices across Rwanda and use real market data to make smarter buying and selling decisions.</p>
        <div class="market-hero-actions"><a href="#latest-prices" class="btn market-primary-btn">Explore prices <i class="bi bi-arrow-down-short"></i></a><a href="<?= APP_URL ?>/register" class="btn market-ghost-btn">Join the marketplace</a></div>
      </div>
      <div class="col-lg-5"><div class="market-pulse-card">
        <div class="market-pulse-head"><div><span>Market overview</span><strong><?= Helper::e($selectedCropName) ?></strong></div><span class="market-live"><i></i> Updated</span></div>
        <div class="market-pulse-grid">
          <div><i class="bi bi-basket2"></i><strong><?= $cropCount ?></strong><span>Crops listed</span></div>
          <div><i class="bi bi-geo-alt"></i><strong><?= $districtCount ?></strong><span>Districts</span></div>
          <div><i class="bi bi-tags"></i><strong><?= $priceCount ?></strong><span>Price points</span></div>
          <div><i class="bi bi-calendar3"></i><strong><?= $latestDate ? date('d M', $latestDate) : '—' ?></strong><span>Latest update</span></div>
        </div>
        <p><i class="bi bi-shield-check"></i> Prices are compiled from verified market records.</p>
      </div></div>
    </div></div>
  </section>

  <section class="market-content" id="latest-prices"><div class="container">
    <div class="market-filter-panel">
      <div class="market-filter-copy"><span class="market-filter-icon"><i class="bi bi-sliders2"></i></span><div><strong>Find the price you need</strong><span>Select a crop to narrow the market results.</span></div></div>
      <form method="GET" action="<?= APP_URL ?>/market-prices" class="market-filter-form">
        <label for="marketCrop" class="visually-hidden">Filter by crop</label>
        <div class="market-select-wrap"><i class="bi bi-search"></i><select id="marketCrop" name="crop_id" class="form-select" onchange="this.form.submit()">
          <option value="">Browse all crops</option>
          <?php foreach ($crops as $crop): ?><option value="<?= (int) $crop['id'] ?>" <?= ((int) $selectedCrop === (int) $crop['id']) ? 'selected' : '' ?>><?= Helper::e($crop['name']) ?></option><?php endforeach; ?>
        </select></div>
        <button class="btn market-filter-btn" type="submit">Apply filter</button>
        <?php if ($selectedCrop): ?><a href="<?= APP_URL ?>/market-prices#latest-prices" class="market-clear-link"><i class="bi bi-x-circle"></i> Clear</a><?php endif; ?>
      </form>
    </div>

    <div class="market-section-heading"><div><span class="section-eyebrow">Latest market snapshot</span><h2><?= $selectedCrop ? Helper::e($selectedCropName) . ' prices' : 'Today’s crop prices' ?></h2><p>Current recorded price per standard crop unit.</p></div><span class="market-result-count"><?= $priceCount ?> result<?= $priceCount === 1 ? '' : 's' ?></span></div>
    <?php if (empty($prices)): ?>
    <div class="market-empty-state"><span><i class="bi bi-bar-chart-line"></i></span><h3>No prices found</h3><p>There are no market prices available for this crop yet. Try viewing all crops.</p><a href="<?= APP_URL ?>/market-prices" class="btn btn-success">View all crops</a></div>
    <?php else: ?>
    <div class="market-price-grid">
      <?php foreach ($prices as $index => $price): ?>
      <article class="market-price-card">
        <div class="market-card-top"><span class="market-crop-icon market-crop-icon-<?= ($index % 4) + 1 ?>"><i class="bi bi-flower1"></i></span><span class="market-date"><i class="bi bi-clock"></i> <?= date('d M Y', strtotime($price['price_date'])) ?></span></div>
        <div class="market-card-body"><h3><?= Helper::e($price['crop_name']) ?></h3><div class="market-card-price"><?= Helper::formatCurrency($price['price']) ?></div><span class="market-unit">per <?= Helper::e($price['unit']) ?></span></div>
        <div class="market-card-footer"><span><i class="bi bi-geo-alt-fill"></i><?= !empty($price['district_name']) ? Helper::e($price['district_name']) : 'National average' ?></span><span class="market-available"><i></i> Current</span></div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($trends)): ?>
    <section class="market-trends">
      <div class="market-section-heading"><div><span class="section-eyebrow">Price movement</span><h2>3-month market range</h2><p>Compare the latest, lowest, highest and average recorded prices.</p></div><span class="market-period"><i class="bi bi-calendar-range"></i> Last 90 days</span></div>
      <div class="market-table-card"><div class="table-responsive"><table class="table market-table align-middle mb-0">
        <thead><tr><th>Crop</th><th>Latest price</th><th>Lowest</th><th>Highest</th><th>Average</th><th>Market range</th></tr></thead><tbody>
        <?php foreach ($trends as $index => $trend): $min=(float)$trend['min_price']; $max=(float)$trend['max_price']; $latest=(float)$trend['latest_price']; $position=$max>$min ? max(4,min(96,(($latest-$min)/($max-$min))*100)) : 50; ?>
        <tr><td><div class="market-table-crop"><span class="market-crop-icon market-crop-icon-<?= ($index % 4) + 1 ?>"><i class="bi bi-flower1"></i></span><div><strong><?= Helper::e($trend['crop_name']) ?></strong><small>per <?= Helper::e($trend['unit']) ?></small></div></div></td><td><strong class="market-latest-price"><?= Helper::formatCurrency($trend['latest_price']) ?></strong></td><td><?= Helper::formatCurrency($trend['min_price']) ?></td><td><?= Helper::formatCurrency($trend['max_price']) ?></td><td><?= Helper::formatCurrency($trend['avg_price']) ?></td><td><div class="market-range" title="Latest price position within the 3-month range"><span style="left:<?= round($position,1) ?>%"></span></div></td></tr>
        <?php endforeach; ?>
        </tbody></table></div><div class="market-table-note"><i class="bi bi-info-circle"></i> Price ranges help show market movement; they are not a guarantee of future prices.</div></div>
    </section>
    <?php endif; ?>

    <aside class="market-cta"><div><span class="market-cta-icon"><i class="bi bi-shop"></i></span><div><h3>Ready to access the marketplace?</h3><p>Connect with cooperatives, discover available produce and make informed offers.</p></div></div><a href="<?= APP_URL ?>/register" class="btn">Create a free account <i class="bi bi-arrow-right"></i></a></aside>
  </div></section>
</div>
