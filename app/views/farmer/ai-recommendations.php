<?php $title = 'Market Recommendations'; ?>
<div class="farmer-advice-page">
  <header class="farmer-advice-header">
    <div><span class="dashboard-eyebrow">Market decision support</span><h1>Market recommendations</h1><p>Simple selling guidance based on your crops and available market evidence.</p></div>
    <div class="farmer-advice-scope"><span><i class="bi bi-geo-alt"></i>Market area</span><strong><?= Helper::e($farmer['district_name'] ?? 'All districts') ?></strong></div>
  </header>

  <section class="farmer-advice-note"><span><i class="bi bi-stars"></i></span><div><strong>How recommendations are prepared</strong><p>Historical market prices and completed sales are analyzed using statistical linear regression. Recommendations support your decision but do not guarantee future prices.</p></div><a href="<?= APP_URL ?>/farmer/market-prices">View market prices <i class="bi bi-arrow-right"></i></a></section>

  <?php if (empty($predictions)): ?>
  <section class="farmer-advice-empty"><span><i class="bi bi-flower1"></i></span><h2>No recommendations available yet</h2><p>Record harvests for your crops and wait for sufficient market-price evidence to receive useful guidance.</p><div><a href="<?= APP_URL ?>/farmer/harvests" class="btn btn-success"><i class="bi bi-plus-lg"></i> Record harvest</a><a href="<?= APP_URL ?>/farmer/market-prices" class="btn btn-outline-success">Check market prices</a></div></section>
  <?php else: ?>
  <div class="farmer-advice-heading"><div><h2>Recommendations for your crops</h2><p><?= count($predictions) ?> crop recommendation<?= count($predictions)===1?'':'s' ?> available</p></div><span><i class="bi bi-clock-history"></i> Updated <?= date('d M Y') ?></span></div>
  <section class="farmer-advice-grid">
    <?php foreach ($predictions as $index => $prediction):
      $demand = strtolower($prediction['predicted_demand']);
      $advice = preg_replace('/Best buyer:.*?\.\s*/','',$prediction['recommendation_text']);
    ?>
    <article class="farmer-advice-card demand-card-<?= $demand ?>">
      <div class="farmer-advice-card-top"><div class="farmer-advice-crop"><span class="farmer-advice-crop-icon crop-symbol-<?= ($index%3)+1 ?>"><i class="bi bi-flower1"></i></span><div><strong><?= Helper::e($prediction['crop_name']) ?></strong><small><i class="bi bi-geo-alt"></i><?= $prediction['district_id'] ? Helper::e($farmer['district_name'] ?? 'Selected district') : 'All districts' ?></small></div></div><span class="farmer-advice-demand advice-demand-<?= $demand ?>"><i class="bi bi-<?= $demand==='high'?'arrow-up-right':($demand==='low'?'arrow-down-right':'dash') ?>"></i><?= Helper::e($prediction['predicted_demand']) ?> demand</span></div>
      <div class="farmer-advice-price"><span>Predicted market price</span><strong><?= Helper::formatCurrency($prediction['predicted_price']) ?><small>/ kg</small></strong><p>Forecast for the next market period</p></div>
      <div class="farmer-selling-window"><span><i class="bi bi-calendar2-check"></i></span><div><small>Recommended selling period</small><strong><?= Helper::e($prediction['best_selling_period'] ?: 'Not determined') ?></strong></div></div>
      <div class="farmer-advice-message"><i class="bi bi-lightbulb"></i><div><span>Recommendation</span><p><?= Helper::e($advice) ?></p></div></div>
      <footer><span><i class="bi bi-cpu"></i>Statistical model <?= Helper::e($prediction['model_version'] ?? '1.1') ?></span><span><i class="bi bi-calendar3"></i><?= Helper::formatDate($prediction['prediction_date']) ?></span></footer>
    </article>
    <?php endforeach; ?>
  </section>
  <?php endif; ?>

  <section class="farmer-advice-help"><div><span><i class="bi bi-question-circle"></i></span><div><strong>What should I do next?</strong><p>Compare the predicted price with today’s market prices before deciding when to sell.</p></div></div><a href="<?= APP_URL ?>/farmer/market-prices" class="btn">Compare prices <i class="bi bi-arrow-right"></i></a></section>
</div>
