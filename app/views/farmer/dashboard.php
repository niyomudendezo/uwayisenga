<?php
$title = 'Farmer Dashboard';
$cropCount = count($topCrops);
$recentHarvestCount = count($harvests);
$farmName = $farmer['farm_name'] ?? 'My Farm';
?>
<div class="farmer-dashboard-page">
  <section class="farmer-welcome">
    <div class="farmer-welcome-pattern" aria-hidden="true"></div>
    <div class="farmer-welcome-copy"><span class="farmer-greeting"><i class="bi bi-sun"></i><?= date('H') < 12 ? 'Good morning' : (date('H') < 18 ? 'Good afternoon' : 'Good evening') ?></span><h1>Welcome back, <?= Helper::e($farmer['first_name']) ?>.</h1><p>Here is what is happening across your farm and the market today.</p><div class="farmer-profile-meta"><span><i class="bi bi-house-heart"></i><?= Helper::e($farmName) ?></span><span><i class="bi bi-geo-alt"></i><?= Helper::e($farmer['district_name'] ?? 'Location not set') ?></span><?php if ($farmer['cooperative_name']): ?><span><i class="bi bi-buildings"></i><?= Helper::e($farmer['cooperative_name']) ?></span><?php endif; ?></div></div>
    <div class="farmer-welcome-actions"><a href="<?= APP_URL ?>/farmer/harvests" class="farmer-primary-action"><i class="bi bi-plus-lg"></i> Record harvest</a><a href="<?= APP_URL ?>/farmer/market-prices" class="farmer-secondary-action"><i class="bi bi-graph-up-arrow"></i> Check prices</a></div>
  </section>

  <section class="farmer-stat-grid">
    <article><span class="farmer-stat-icon stat-harvest"><i class="bi bi-basket2"></i></span><div><strong><?= number_format($totalHarv) ?> <em>kg</em></strong><span>Total harvest</span></div><small>All recorded production</small></article>
    <article><span class="farmer-stat-icon stat-crops"><i class="bi bi-flower1"></i></span><div><strong><?= $cropCount ?></strong><span>Crop varieties</span></div><small>Produced on your farm</small></article>
    <article><span class="farmer-stat-icon stat-land"><i class="bi bi-bounding-box"></i></span><div><strong><?= $farmer['farm_size'] ? number_format((float)$farmer['farm_size'],1) : '—' ?> <em>ha</em></strong><span>Farm size</span></div><small><?= Helper::e($farmer['soil_type'] ?? 'Soil type not set') ?></small></article>
    <article><span class="farmer-stat-icon stat-coop"><i class="bi bi-buildings"></i></span><div><strong><?= $farmer['cooperative_name'] ? 'Active' : 'None' ?></strong><span>Cooperative</span></div><small><?= Helper::e($farmer['cooperative_name'] ?? 'Not yet connected') ?></small></article>
  </section>

  <section class="farmer-quick-actions"><div><h2>Quick actions</h2><p>Common farm activities</p></div><div class="farmer-action-links"><a href="<?= APP_URL ?>/farmer/harvests"><span><i class="bi bi-basket"></i></span><div><strong>Record harvest</strong><small>Add new production</small></div><i class="bi bi-chevron-right"></i></a><a href="<?= APP_URL ?>/farmer/ai-recommendations"><span><i class="bi bi-stars"></i></span><div><strong>View recommendations</strong><small>Review market guidance</small></div><i class="bi bi-chevron-right"></i></a><a href="<?= APP_URL ?>/farmer/sales-history"><span><i class="bi bi-receipt"></i></span><div><strong>Sales history</strong><small>Track completed sales</small></div><i class="bi bi-chevron-right"></i></a></div></section>

  <div class="row g-4 farmer-insight-row">
    <div class="col-xl-6"><section class="farmer-panel h-100"><header><div class="farmer-panel-title"><span><i class="bi bi-stars"></i></span><div><h2>Market recommendations</h2><p>Latest data-supported guidance</p></div></div><a href="<?= APP_URL ?>/farmer/ai-recommendations">View all <i class="bi bi-arrow-right"></i></a></header><div class="farmer-recommendation-list">
      <?php foreach ($predictions as $index => $prediction): $demand=strtolower($prediction['predicted_demand']); $advice=preg_replace('/Best buyer:.*?\.\s*/','',$prediction['recommendation_text']); ?>
      <article><span class="farmer-crop-symbol crop-symbol-<?= ($index%3)+1 ?>"><i class="bi bi-flower1"></i></span><div><div class="farmer-recommendation-top"><strong><?= Helper::e($prediction['crop_name']) ?></strong><span class="farmer-demand demand-<?= $demand ?>"><?= Helper::e($prediction['predicted_demand']) ?> demand</span></div><p><?= Helper::e(Helper::truncate($advice,110)) ?></p><small><i class="bi bi-tag"></i><?= Helper::formatCurrency($prediction['predicted_price']) ?> predicted</small></div></article>
      <?php endforeach; ?>
      <?php if (empty($predictions)): ?><div class="farmer-panel-empty"><i class="bi bi-stars"></i><strong>No recommendations yet</strong><p>Record harvests and market activity to build useful guidance.</p></div><?php endif; ?>
    </div></section></div>

    <div class="col-xl-6"><section class="farmer-panel h-100"><header><div class="farmer-panel-title"><span><i class="bi bi-graph-up"></i></span><div><h2>Latest market prices</h2><p>Recent prices across Rwanda</p></div></div><a href="<?= APP_URL ?>/farmer/market-prices">All prices <i class="bi bi-arrow-right"></i></a></header><div class="farmer-price-list">
      <?php foreach (array_slice($prices,0,6) as $index => $price): ?><article><span class="farmer-crop-symbol crop-symbol-<?= ($index%3)+1 ?>"><i class="bi bi-flower1"></i></span><div><strong><?= Helper::e($price['crop_name']) ?></strong><small><i class="bi bi-geo-alt"></i><?= Helper::e($price['district_name'] ?? 'National') ?></small></div><div class="farmer-market-value"><strong><?= Helper::formatCurrency($price['price']) ?></strong><small>per <?= Helper::e($price['unit']) ?></small></div></article><?php endforeach; ?>
      <?php if (empty($prices)): ?><div class="farmer-panel-empty"><i class="bi bi-graph-up"></i><strong>No prices available</strong><p>Market prices will appear here after they are published.</p></div><?php endif; ?>
    </div></section></div>
  </div>

  <section class="farmer-harvest-panel"><header><div><h2>Recent harvests</h2><p>Your latest recorded farm production</p></div><a href="<?= APP_URL ?>/farmer/harvests">Manage harvests <i class="bi bi-arrow-right"></i></a></header><div class="table-responsive"><table class="table farmer-harvest-table align-middle mb-0"><thead><tr><th>Crop</th><th>Quantity</th><th>Quality grade</th><th>Season</th><th>Harvest date</th></tr></thead><tbody>
    <?php foreach ($harvests as $index => $harvest): ?><tr><td><div class="farmer-harvest-crop"><span class="farmer-crop-symbol crop-symbol-<?= ($index%3)+1 ?>"><i class="bi bi-flower1"></i></span><strong><?= Helper::e($harvest['crop_name']) ?></strong></div></td><td><strong class="farmer-quantity"><?= number_format($harvest['quantity']) ?> <small><?= Helper::e($harvest['unit'] ?? $harvest['crop_unit'] ?? 'kg') ?></small></strong></td><td><span class="farmer-grade grade-<?= strtolower($harvest['grade']) ?>">Grade <?= Helper::e($harvest['grade']) ?></span></td><td><span class="farmer-season"><i class="bi bi-cloud-sun"></i><?= Helper::e($harvest['season'] ?? 'Not specified') ?></span></td><td><span class="farmer-date"><i class="bi bi-calendar3"></i><?= Helper::formatDate($harvest['harvest_date']) ?></span></td></tr><?php endforeach; ?>
    <?php if (empty($harvests)): ?><tr><td colspan="5"><div class="farmer-panel-empty"><i class="bi bi-basket"></i><strong>No harvests recorded</strong><p>Record your first harvest to start tracking farm production.</p><a href="<?= APP_URL ?>/farmer/harvests" class="btn btn-success btn-sm">Record harvest</a></div></td></tr><?php endif; ?>
  </tbody></table></div><?php if ($recentHarvestCount): ?><footer><i class="bi bi-info-circle"></i>Showing your <?= $recentHarvestCount ?> most recent harvest record<?= $recentHarvestCount===1?'':'s' ?>.</footer><?php endif; ?></section>
</div>
