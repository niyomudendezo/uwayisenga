<?php $title = $coop['name'] . ' — Members'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h5 class="fw-bold mb-0"><i class="bi bi-people text-success me-2"></i><?= Helper::e($coop['name']) ?> — Members</h5>
    <small class="text-muted">
      <?= Helper::e($coop['district_name'] ?? '') ?>
      <?php if ($coop['registration_no']): ?> &nbsp;|&nbsp; Reg: <?= Helper::e($coop['registration_no']) ?><?php endif; ?>
      <?php if ($coop['first_name']): ?> &nbsp;|&nbsp; Manager: <?= Helper::e($coop['first_name'] . ' ' . $coop['last_name']) ?><?php endif; ?>
    </small>
  </div>
  <a href="<?= APP_URL ?>/admin/cooperatives" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Back
  </a>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <div class="fw-bold fs-3 text-success"><?= count($members) ?></div>
        <div class="text-muted small">Total Members</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <div class="fw-bold fs-3 text-primary"><?= count(array_filter($harvestStats)) ?></div>
        <div class="text-muted small">Members with Harvests</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <div class="fw-bold fs-3 text-warning"><?= number_format(array_sum(array_column($harvestStats, 'total_qty'))) ?> kg</div>
        <div class="text-muted small">Total Harvest</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body text-center">
        <div class="fw-bold fs-3 text-info"><?= number_format(array_sum(array_column($members, 'farm_size'))) ?> ha</div>
        <div class="text-muted small">Total Farm Area</div>
      </div>
    </div>
  </div>
</div>

<!-- Members Table -->
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
    <span class="fw-semibold">Member Details</span>
    <span class="badge bg-success"><?= count($members) ?> members</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Farm Name</th>
            <th>Farm Size</th>
            <th>Soil Type</th>
            <th>Irrigation</th>
            <th>Harvests</th>
            <th>Total Qty</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($members as $i => $m): ?>
          <?php $hs = $harvestStats[$m['farmer_id']] ?? null; ?>
          <tr>
            <td class="text-muted small"><?= $i + 1 ?></td>
            <td class="fw-semibold"><?= Helper::e($m['first_name'] . ' ' . $m['last_name']) ?></td>
            <td class="small"><?= Helper::e($m['email']) ?></td>
            <td class="small"><?= Helper::e($m['phone'] ?? '-') ?></td>
            <td><?= Helper::e($m['farm_name'] ?? '-') ?></td>
            <td><?= $m['farm_size'] ? number_format($m['farm_size']) . ' ha' : '-' ?></td>
            <td class="small"><?= Helper::e($m['soil_type'] ?? '-') ?></td>
            <td class="text-center">
              <?php if (!empty($m['irrigation'])): ?>
              <i class="bi bi-droplet-fill text-info" title="Has irrigation"></i>
              <?php else: ?>
              <i class="bi bi-droplet text-muted" title="No irrigation"></i>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?php if ($hs): ?>
              <span class="badge bg-success"><?= $hs['harvest_count'] ?></span>
              <?php else: ?>
              <span class="badge bg-light text-muted">0</span>
              <?php endif; ?>
            </td>
            <td class="fw-semibold text-success">
              <?= $hs ? number_format($hs['total_qty']) . ' kg' : '-' ?>
            </td>
            <td><?= Helper::statusBadge($m['status'] ?? 'active') ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($members)): ?>
          <tr><td colspan="11" class="text-center text-muted py-4">No members found for this cooperative.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
