<?php
$title = 'Inventory Management';
$filterParams = array_filter([
    'search' => $search,
    'crop' => $cropId ?: null,
    'date_from' => $dateFrom,
    'date_to' => $dateTo,
], static fn($value) => $value !== null && $value !== '');
$paginationUrl = APP_URL . '/cooperative/inventory' . ($filterParams ? '?' . http_build_query($filterParams) : '');
?>
<div class="stock-page">
  <header class="stock-page-header"><div><span class="dashboard-eyebrow">Warehouse control</span><h1>Stock inventory</h1><p>Track opening balances, stock received, stock issued and quantities currently available.</p></div><a href="<?= APP_URL ?>/cooperative/inventory/create" class="btn stock-add-btn"><i class="bi bi-plus-lg"></i> Add inventory item</a></header>

  <section class="stock-summary-grid">
    <article><span class="stock-summary-icon stock-opening-icon"><i class="bi bi-box-seam"></i></span><div><strong><?= number_format((float)($stats['total_opening']??0)) ?> <em>kg</em></strong><span>Opening stock</span></div><small>Initial balances</small></article>
    <article><span class="stock-summary-icon stock-in-icon"><i class="bi bi-box-arrow-in-down"></i></span><div><strong><?= number_format((float)($stats['total_received']??0)) ?> <em>kg</em></strong><span>Stock in</span></div><small>Additional receipts</small></article>
    <article><span class="stock-summary-icon stock-out-icon"><i class="bi bi-box-arrow-up"></i></span><div><strong><?= number_format((float)($stats['total_out']??0)) ?> <em>kg</em></strong><span>Stock out</span></div><small>Issued, sold or damaged</small></article>
    <article><span class="stock-summary-icon stock-available-icon"><i class="bi bi-box2-heart"></i></span><div><strong><?= number_format((float)($stats['total_available']??0)) ?> <em>kg</em></strong><span>Available stock</span></div><small>Ready for market</small></article>
  </section>

  <section class="stock-ledger-card">
    <div class="stock-ledger-head"><div><h2>Inventory ledger</h2><p><?php if($dateFrom||$dateTo): ?><i class="bi bi-calendar3"></i> Balances for <?= $dateFrom ? Helper::e(date('d M Y', strtotime($dateFrom))) : 'the beginning' ?> to <?= $dateTo ? Helper::e(date('d M Y', strtotime($dateTo))) : 'today' ?><?php else: ?>Live stock balances by crop and warehouse<?php endif; ?></p></div><form method="GET" class="stock-filter-form"><label class="stock-search-filter"><i class="bi bi-search"></i><input type="search" name="search" placeholder="Search crop or cooperative" value="<?= Helper::e($search) ?>"></label><select name="crop"><option value="">All crops</option><?php foreach($crops as $crop): ?><option value="<?= (int)$crop['id'] ?>" <?= $cropId==$crop['id']?'selected':'' ?>><?= Helper::e($crop['name']) ?></option><?php endforeach; ?></select><label class="stock-date-filter"><span>From</span><input type="date" name="date_from" value="<?= Helper::e($dateFrom) ?>" max="<?= date('Y-m-d') ?>" aria-label="From date"></label><label class="stock-date-filter"><span>To</span><input type="date" name="date_to" value="<?= Helper::e($dateTo) ?>" max="<?= date('Y-m-d') ?>" aria-label="To date"></label><button class="btn" type="submit"><i class="bi bi-funnel"></i> Filter</button><?php if($search||$cropId||$dateFrom||$dateTo): ?><a href="<?= APP_URL ?>/cooperative/inventory" title="Clear filters"><i class="bi bi-x-lg"></i></a><?php endif; ?></form></div>
    <div class="table-responsive"><table class="table stock-ledger-table align-middle mb-0"><thead><tr><th>Inventory item</th><th>Opening</th><th>Stock in</th><th>Stock out</th><th>Reserved</th><th>Available</th><th>Price</th><th>Status</th></tr></thead><tbody>
      <?php foreach($result['data'] as $index=>$item): $unit=$item['crop_unit']??'kg'; ?>
      <tr>
        <td><div class="stock-item-cell"><span class="stock-crop-icon stock-crop-<?= ($index%4)+1 ?>"><i class="bi bi-flower1"></i></span><div><strong><?= Helper::e($item['crop_name']) ?></strong><small><i class="bi bi-building"></i><?= Helper::e($item['warehouse_name']??'No warehouse') ?> · Grade <?= Helper::e($item['grade']) ?></small></div></div></td>
        <td><span class="stock-number opening"><?= number_format((float)$item['qty_opening']) ?><small><?= Helper::e($unit) ?></small></span></td>
        <td><span class="stock-number in">+<?= number_format((float)$item['qty_received']) ?><small><?= Helper::e($unit) ?></small></span></td>
        <td><span class="stock-number out">−<?= number_format((float)$item['qty_sold'] + (float)$item['qty_damaged']) ?><small><?= Helper::e($unit) ?></small></span></td>
        <td><span class="stock-number reserved"><?= number_format((float)$item['qty_reserved']) ?><small><?= Helper::e($unit) ?></small></span></td>
        <td><span class="stock-available-value"><?= number_format((float)$item['qty_available']) ?><small><?= Helper::e($unit) ?></small></span></td>
        <td><strong class="stock-price"><?= Helper::formatCurrency($item['asking_price']) ?></strong><small class="stock-price-unit">per <?= Helper::e($unit) ?></small></td>
        <td><?= Helper::statusBadge($item['status']) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if(empty($result['data'])): ?><tr><td colspan="8"><div class="stock-empty"><span><i class="bi bi-boxes"></i></span><h3>No inventory records</h3><p>Add an opening balance to start managing cooperative stock.</p><a href="<?= APP_URL ?>/cooperative/inventory/create" class="btn btn-success btn-sm">Add inventory</a></div></td></tr><?php endif; ?>
    </tbody></table></div>
    <?php if($result['last_page']>1): ?><footer class="stock-ledger-footer"><small>Showing <?= count($result['data']) ?> of <?= number_format((int)$result['total']) ?> items</small><?= Helper::paginate($result,$paginationUrl) ?></footer><?php endif; ?>
  </section>
</div>
