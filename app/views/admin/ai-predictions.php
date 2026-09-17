<?php
$title = 'AI Predictions';
$demandCounts = ['High' => 0, 'Medium' => 0, 'Low' => 0];
foreach ($summary as $item) $demandCounts[$item['predicted_demand']] = (int)$item['count'];
$totalPredictions = count($predictions);
?>
<div class="ai-predictions-page">
  <header class="ai-page-header">
    <div><span class="dashboard-eyebrow">Statistical decision support</span><h1>AI predictions</h1><p>Forecast crop prices and demand using historical market and completed-sales data.</p></div>
    <button class="btn ai-run-btn" data-bs-toggle="modal" data-bs-target="#runModal"><i class="bi bi-stars"></i> Generate prediction</button>
  </header>

  <section class="ai-overview">
    <article class="ai-overview-primary"><div><span>Prediction activity</span><strong><?= number_format($totalPredictions) ?></strong><small>Latest stored forecasts</small></div><i class="bi bi-cpu"></i></article>
    <article><span class="ai-metric-icon ai-high"><i class="bi bi-arrow-up-right"></i></span><div><strong><?= $demandCounts['High'] ?></strong><span>High demand</span><small>Above +5% trend</small></div></article>
    <article><span class="ai-metric-icon ai-medium"><i class="bi bi-dash-lg"></i></span><div><strong><?= $demandCounts['Medium'] ?></strong><span>Stable demand</span><small>Within ±5% trend</small></div></article>
  </section>

  <section class="ai-method-card">
    <div class="ai-method-intro"><span><i class="bi bi-diagram-3"></i></span><div><strong>How this prediction works</strong><p>Transparent statistical forecasting aligned with the documented AgruKrwanda methodology.</p></div></div>
    <div class="ai-method-steps">
      <div><b>01</b><span><strong>Historical inputs</strong><small>Up to 24 price observations and 20 completed sales</small></span></div>
      <i class="bi bi-arrow-right"></i>
      <div><b>02</b><span><strong>Linear regression</strong><small>Least-squares forecast for the next period</small></span></div>
      <i class="bi bi-arrow-right"></i>
      <div><b>03</b><span><strong>Decision support</strong><small>Demand, selling period and recommendation</small></span></div>
    </div>
  </section>

  <section class="ai-results-card">
    <div class="ai-results-head"><div><h2>Latest prediction results</h2><p>Review forecasts, evidence strength and recommended action</p></div><span><i class="bi bi-clock-history"></i> Most recent first</span></div>
    <div class="table-responsive"><table class="table ai-results-table align-middle mb-0"><thead><tr><th>Crop & market</th><th>Demand forecast</th><th>Predicted price</th><th>Recommendation</th><th>Run details</th></tr></thead><tbody>
      <?php foreach ($predictions as $index => $prediction):
        $demand = ucfirst(strtolower($prediction['predicted_demand']));
      ?>
      <tr>
        <td><div class="ai-crop-cell"><span class="ai-crop-icon ai-crop-<?= ($index % 4) + 1 ?>"><i class="bi bi-flower1"></i></span><div><strong><?= Helper::e($prediction['crop_name']) ?></strong><small><i class="bi bi-geo-alt"></i><?= Helper::e($prediction['district_name'] ?? 'All districts') ?></small></div></div></td>
        <td><span class="ai-demand ai-demand-<?= strtolower($demand) ?>"><i class="bi bi-<?= $demand === 'High' ? 'arrow-up-right' : ($demand === 'Low' ? 'arrow-down-right' : 'dash') ?>"></i><?= Helper::e($demand) ?></span></td>
        <td><strong class="ai-price"><?= Helper::formatCurrency($prediction['predicted_price']) ?></strong><small class="ai-unit">per <?= Helper::e($prediction['unit'] ?? 'kg') ?></small></td>
        <td><button type="button" class="ai-recommendation-btn" data-bs-toggle="modal" data-bs-target="#recommendationModal<?= (int)$prediction['id'] ?>"><i class="bi bi-lightbulb"></i> View advice</button></td>
        <td><div class="ai-run-detail"><strong><?= Helper::formatDate($prediction['prediction_date']) ?></strong><small>Model <?= Helper::e($prediction['model_version'] ?? '1.0') ?></small></div></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($predictions)): ?><tr><td colspan="5"><div class="ai-empty"><span><i class="bi bi-stars"></i></span><h3>No predictions generated</h3><p>Select a crop and district to create the first evidence-based forecast.</p><button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#runModal">Generate prediction</button></div></td></tr><?php endif; ?>
    </tbody></table></div>
    <footer class="ai-results-footer"><i class="bi bi-info-circle"></i> Predictions support decisions and do not guarantee future prices or demand.</footer>
  </section>
</div>

<?php foreach ($predictions as $prediction): $displayAdvice = preg_replace('/Best buyer:.*?\.\s*/', '', $prediction['recommendation_text']); ?>
<div class="modal fade" id="recommendationModal<?= (int)$prediction['id'] ?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content ai-advice-modal">
  <div class="modal-header"><div><span><i class="bi bi-lightbulb"></i></span><div><h5><?= Helper::e($prediction['crop_name']) ?> recommendation</h5><p><?= Helper::e($prediction['district_name'] ?? 'All districts') ?> · <?= Helper::formatDate($prediction['prediction_date']) ?></p></div></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body"><div class="ai-advice-highlight"><i class="bi bi-quote"></i><p><?= Helper::e($displayAdvice) ?></p></div><div class="ai-advice-grid"><div><span>Best selling period</span><strong><?= Helper::e($prediction['best_selling_period']) ?></strong></div></div></div>
  <div class="modal-footer"><button type="button" class="btn btn-success" data-bs-dismiss="modal">Understood</button></div>
</div></div></div>
<?php endforeach; ?>

<div class="modal fade" id="runModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content ai-run-modal">
  <div class="modal-header"><div class="ai-run-title"><span><i class="bi bi-stars"></i></span><div><h5>Generate prediction</h5><p>Choose a market scope for the statistical forecast.</p></div></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form method="POST" action="<?= APP_URL ?>/admin/ai-predictions/run" data-validate><?= Auth::csrfField() ?><div class="modal-body">
    <label class="form-label">Crop <span>*</span></label><select name="crop_id" class="form-select mb-3" required><option value="">Choose a crop with available evidence</option><?php foreach ($crops as $crop): $evidence=$predictionEvidence[$crop['id']] ?? ['prices'=>0,'sales'=>0]; $evidenceTotal=$evidence['prices']+$evidence['sales']; ?><option value="<?= (int)$crop['id'] ?>" <?= !$evidenceTotal ? 'disabled' : '' ?>><?= Helper::e($crop['name']) ?> — <?= Helper::e($crop['category_name']) ?> (<?= $evidenceTotal ? $evidence['prices'].' prices, '.$evidence['sales'].' sales' : 'no prediction data' ?>)</option><?php endforeach; ?></select>
    <label class="form-label">Market area <span>*</span></label><select name="district_id" class="form-select" required><option value="0">All districts — national average</option><?php foreach ($districts as $district): ?><option value="<?= (int)$district['id'] ?>"><?= Helper::e($district['name']) ?></option><?php endforeach; ?></select>
    <div class="ai-data-notice"><i class="bi bi-database-check"></i><div><strong>Evidence used</strong><p>Historical market prices and completed sales are analyzed for the selected crop and market area.</p></div></div>
  </div><div class="modal-footer"><button type="button" class="btn ai-modal-cancel" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn ai-modal-run"><i class="bi bi-play-fill"></i> Run prediction</button></div></form>
</div></div></div>
