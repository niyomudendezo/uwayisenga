<?php $title = 'Cooperatives Management'; ?>
<div class="cooperatives-page">
<header class="coop-page-header">
  <div><span class="dashboard-eyebrow">Network administration</span><h1>Cooperatives</h1><p>Manage registered agricultural cooperatives, their managers and member communities.</p></div>
  <button class="btn coop-add-btn" data-bs-toggle="modal" data-bs-target="#addCoopModal"><i class="bi bi-plus-lg"></i><span>Add cooperative</span></button>
</header>

<section class="coop-stats" aria-label="Cooperative summary">
  <article><span class="coop-stat-icon coop-stat-green"><i class="bi bi-buildings"></i></span><div><strong><?= number_format((int)($stats['total'] ?? 0)) ?></strong><span>Total cooperatives</span></div><small>Registered network</small></article>
  <article><span class="coop-stat-icon coop-stat-blue"><i class="bi bi-people"></i></span><div><strong><?= number_format((int)($stats['members'] ?? 0)) ?></strong><span>Farmer members</span></div><small>Across all groups</small></article>
  <article><span class="coop-stat-icon coop-stat-amber"><i class="bi bi-person-badge"></i></span><div><strong><?= number_format(max(0, (int)($stats['total'] ?? 0) - (int)($stats['unassigned'] ?? 0))) ?></strong><span>Assigned managers</span></div><small><?= (int)($stats['unassigned'] ?? 0) ?> need assignment</small></article>
  <article><span class="coop-stat-icon coop-stat-purple"><i class="bi bi-activity"></i></span><div><strong><?= number_format((int)($stats['active'] ?? 0)) ?></strong><span>Active groups</span></div><small>Currently operating</small></article>
</section>

<div class="coop-toolbar">
  <div><h2>Cooperative directory</h2><p><?= number_format((int)$result['total']) ?> record<?= (int)$result['total'] === 1 ? '' : 's' ?> in the directory</p></div>
  <form method="GET" class="coop-search-form">
    <div class="coop-search-input"><i class="bi bi-search"></i><input type="search" name="search" placeholder="Search name or registration number" value="<?= Helper::e($search) ?>" aria-label="Search cooperatives"></div>
    <button type="submit" class="btn">Search</button>
    <?php if ($search): ?><a href="<?= APP_URL ?>/admin/cooperatives" class="coop-reset" title="Clear search"><i class="bi bi-x-lg"></i></a><?php endif; ?>
  </form>
</div>

<div class="card coop-directory-card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table coop-directory-table align-middle mb-0">
        <thead><tr><th>Cooperative</th><th>Registration</th><th>Manager</th><th>Location</th><th>Community</th><th>Status</th><th><span class="visually-hidden">Actions</span></th></tr></thead>
        <tbody>
          <?php foreach ($result['data'] as $index => $c): ?>
          <tr>
            <td><div class="coop-identity"><span class="coop-avatar coop-avatar-<?= ($index % 4) + 1 ?>"><?= strtoupper(substr($c['name'], 0, 1)) ?></span><div><strong><?= Helper::e($c['name']) ?></strong><small>Added <?= !empty($c['created_at']) ? date('d M Y', strtotime($c['created_at'])) : '—' ?></small></div></div></td>
            <td><span class="coop-registration"><?= Helper::e($c['registration_no'] ?? 'Not provided') ?></span></td>
            <td><?php if ($c['first_name']): ?><div class="coop-manager"><i class="bi bi-person-check"></i><span><?= Helper::e($c['first_name'].' '.$c['last_name']) ?></span></div><?php else: ?><span class="coop-unassigned"><i class="bi bi-exclamation-circle"></i> Unassigned</span><?php endif; ?></td>
            <td><span class="coop-location"><i class="bi bi-geo-alt"></i><?= Helper::e($c['district_name'] ?? 'Not specified') ?></span></td>
            <td><a href="<?= APP_URL ?>/admin/cooperatives/<?= (int)$c['id'] ?>/members" class="coop-members-link"><span><i class="bi bi-people"></i></span><strong><?= number_format((int)$c['member_count']) ?></strong> members</a></td>
            <td><span class="coop-status coop-status-<?= Helper::e($c['status']) ?>"><i></i><?= ucfirst(Helper::e($c['status'])) ?></span></td>
            <td><a href="<?= APP_URL ?>/admin/cooperatives/<?= (int)$c['id'] ?>/members" class="coop-view-btn" aria-label="View <?= Helper::e($c['name']) ?> members">View members <i class="bi bi-arrow-right"></i></a></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($result['data'])): ?><tr><td colspan="7"><div class="coop-empty"><span><i class="bi bi-buildings"></i></span><h3>No cooperatives found</h3><p><?= $search ? 'Try another name or clear your current search.' : 'Add your first cooperative to begin building the network.' ?></p><?php if ($search): ?><a href="<?= APP_URL ?>/admin/cooperatives" class="btn btn-outline-success btn-sm">Clear search</a><?php else: ?><button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCoopModal">Add cooperative</button><?php endif; ?></div></td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php if ($result['last_page'] > 1): ?>
  <div class="card-footer coop-directory-footer d-flex justify-content-between align-items-center">
    <small>Showing <?= count($result['data']) ?> of <?= number_format((int)$result['total']) ?> cooperatives</small>
    <?= Helper::paginate($result, APP_URL . '/admin/cooperatives') ?>
  </div>
  <?php endif; ?>
</div></div>

<!-- Add Cooperative Modal -->
<div class="modal fade" id="addCoopModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content coop-modal">
    <div class="modal-header"><div class="coop-modal-title"><span><i class="bi bi-building-add"></i></span><div><h5 class="modal-title">Add a new cooperative</h5><p>Create a profile and connect its manager and district.</p></div></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
    <form method="POST" action="<?= APP_URL ?>/admin/cooperatives/store">
      <?= Auth::csrfField() ?>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-8"><label class="form-label">Cooperative name <span>*</span></label><input type="text" name="name" class="form-control" placeholder="e.g. Green Hills Cooperative" required></div>
          <div class="col-md-4"><label class="form-label">Registration number</label><input type="text" name="registration_no" class="form-control" placeholder="e.g. RCA/0012"></div>
          <div class="col-md-6">
            <label class="form-label">Assigned manager</label>
            <select name="manager_id" class="form-select">
              <option value="">Select Manager</option>
              <?php foreach ($managers as $m): ?><option value="<?= $m['id'] ?>"><?= Helper::e($m['first_name'].' '.$m['last_name']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Operating district</label>
            <select name="district_id" class="form-select">
              <option value="">Select District</option>
              <?php foreach ($districts as $d): ?><option value="<?= $d['id'] ?>"><?= Helper::e($d['name']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6"><label class="form-label">Phone number</label><input type="tel" name="phone" class="form-control" placeholder="+250 7XX XXX XXX"></div>
          <div class="col-md-6"><label class="form-label">Email address</label><input type="email" name="email" class="form-control" placeholder="cooperative@example.rw"></div>
          <div class="col-12"><label class="form-label">About the cooperative</label><textarea name="description" class="form-control" rows="3" placeholder="Briefly describe its focus and activities"></textarea></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn coop-cancel-btn" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn coop-save-btn"><i class="bi bi-check-lg"></i>Create cooperative</button></div>
    </form>
  </div></div>
</div>
