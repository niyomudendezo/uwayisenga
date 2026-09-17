<?php $title = 'My Harvests'; ?>
<div class="farmer-harvests-page">
  <header class="harvest-page-header"><div><span class="dashboard-eyebrow">Farm production</span><h1>My harvests</h1><p>Record, review and track everything produced on your farm.</p></div><button class="btn harvest-add-btn" data-bs-toggle="modal" data-bs-target="#addHarvestModal"><i class="bi bi-plus-lg"></i> Record harvest</button></header>

  <section class="harvest-summary-grid">
    <article><span class="harvest-summary-icon harvest-summary-green"><i class="bi bi-basket2"></i></span><div><strong><?= number_format((float)($stats['total_qty'] ?? 0)) ?> <em>kg</em></strong><span>Total production</span></div><small>All recorded harvests</small></article>
    <article><span class="harvest-summary-icon harvest-summary-blue"><i class="bi bi-journal-check"></i></span><div><strong><?= number_format((int)($stats['records'] ?? 0)) ?></strong><span>Harvest records</span></div><small>Production entries</small></article>
    <article><span class="harvest-summary-icon harvest-summary-amber"><i class="bi bi-flower1"></i></span><div><strong><?= number_format((int)($stats['crop_count'] ?? 0)) ?></strong><span>Crop varieties</span></div><small>Different crops grown</small></article>
    <article><span class="harvest-summary-icon harvest-summary-purple"><i class="bi bi-calendar-check"></i></span><div><strong><?= !empty($stats['latest_date']) ? date('d M',strtotime($stats['latest_date'])) : '—' ?></strong><span>Latest harvest</span></div><small><?= !empty($stats['latest_date']) ? date('Y',strtotime($stats['latest_date'])) : 'No record yet' ?></small></article>
  </section>

  <section class="harvest-log-card">
    <div class="harvest-log-head"><div><h2>Harvest history</h2><p>A complete record of your farm production</p></div><div class="harvest-log-meta"><span><i class="bi bi-database"></i><?= number_format((int)$result['total']) ?> record<?= (int)$result['total']===1?'':'s' ?></span><button class="btn" data-bs-toggle="modal" data-bs-target="#addHarvestModal"><i class="bi bi-plus"></i>Add new</button></div></div>
    <div class="table-responsive"><table class="table harvest-log-table align-middle mb-0"><thead><tr><th>Crop</th><th>Quantity</th><th>Quality</th><th>Season</th><th>Harvest date</th><th>Cooperative</th><th>Notes</th></tr></thead><tbody>
      <?php foreach ($result['data'] as $index => $harvest): ?><tr>
        <td><div class="harvest-crop-cell"><span class="harvest-crop-icon harvest-crop-icon-<?= ($index%4)+1 ?>"><i class="bi bi-flower1"></i></span><div><strong><?= Helper::e($harvest['crop_name']) ?></strong><small>Farm produce</small></div></div></td>
        <td><strong class="harvest-quantity"><?= number_format($harvest['quantity']) ?> <small><?= Helper::e($harvest['crop_unit']) ?></small></strong></td>
        <td><span class="harvest-grade harvest-grade-<?= strtolower($harvest['grade']) ?>"><i class="bi bi-patch-check"></i>Grade <?= Helper::e($harvest['grade']) ?></span></td>
        <td><span class="harvest-season"><i class="bi bi-cloud-sun"></i><?= Helper::e($harvest['season'] ?? 'Not specified') ?></span></td>
        <td><span class="harvest-date"><i class="bi bi-calendar3"></i><?= Helper::formatDate($harvest['harvest_date']) ?></span></td>
        <td><span class="harvest-cooperative"><i class="bi bi-buildings"></i><?= Helper::e($harvest['cooperative_name'] ?? 'Independent') ?></span></td>
        <td><span class="harvest-note" title="<?= Helper::e($harvest['notes'] ?? '') ?>"><?= Helper::e(Helper::truncate($harvest['notes'] ?? 'No notes',45)) ?></span></td>
      </tr><?php endforeach; ?>
      <?php if (empty($result['data'])): ?><tr><td colspan="7"><div class="harvest-empty"><span><i class="bi bi-basket"></i></span><h3>No harvests recorded yet</h3><p>Add your first harvest to begin tracking farm production.</p><button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addHarvestModal">Record first harvest</button></div></td></tr><?php endif; ?>
    </tbody></table></div>
    <?php if ($result['last_page']>1): ?><footer class="harvest-log-footer"><small>Showing <?= count($result['data']) ?> of <?= number_format((int)$result['total']) ?> records</small><?= Helper::paginate($result,APP_URL.'/farmer/harvests') ?></footer><?php endif; ?>
  </section>
</div>

<div class="modal fade" id="addHarvestModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content harvest-entry-modal">
  <form method="POST" action="<?= APP_URL ?>/farmer/harvests/store" data-validate><?= Auth::csrfField() ?>
    <div class="modal-header"><div class="harvest-modal-title"><span><i class="bi bi-basket2"></i></span><div><h5>Record a new harvest</h5><p>Add production details to your farm history.</p></div></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
    <div class="modal-body"><div class="row g-3">
      <div class="col-md-7"><label class="form-label">Crop <span>*</span></label><select name="crop_id" class="form-select" required><option value="">Choose a crop</option><?php foreach ($crops as $crop): ?><option value="<?= (int)$crop['id'] ?>"><?= Helper::e($crop['name']) ?> — <?= Helper::e($crop['category_name']) ?></option><?php endforeach; ?></select></div>
      <div class="col-md-5"><label class="form-label">Quantity <span>*</span></label><div class="harvest-quantity-input"><input type="number" name="quantity" class="form-control" min="0.1" step="0.1" placeholder="0.0" required><span>kg</span></div></div>
      <div class="col-md-6"><label class="form-label">Quality grade</label><div class="harvest-grade-options"><label><input type="radio" name="grade" value="A" checked><span><b>A</b> Premium</span></label><label><input type="radio" name="grade" value="B"><span><b>B</b> Standard</span></label><label><input type="radio" name="grade" value="C"><span><b>C</b> Basic</span></label></div></div>
      <div class="col-md-6"><label class="form-label">Harvest date <span>*</span></label><input type="date" name="harvest_date" class="form-control" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required></div>
      <div class="col-md-6"><label class="form-label">Growing season</label><input type="text" name="season" class="form-control" placeholder="e.g. Season A 2026"></div>
      <div class="col-md-6"><label class="form-label">Cooperative</label><div class="harvest-readonly-field"><i class="bi bi-buildings"></i><span><?= Helper::e($farmer['cooperative_name'] ?? 'Independent farmer') ?></span></div></div>
      <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="3" placeholder="Add optional notes about quality, weather or storage"></textarea></div>
    </div></div>
    <div class="modal-footer"><button type="button" class="btn harvest-modal-cancel" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn harvest-modal-save"><i class="bi bi-check-lg"></i> Save harvest</button></div>
  </form>
</div></div></div>
