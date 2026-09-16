<?php $title = 'Cooperatives Management'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-building text-success me-2"></i>Cooperatives</h5>
  <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCoopModal">
    <i class="bi bi-plus-lg me-1"></i>Add Cooperative
  </button>
</div>
<div class="card border-0 shadow-sm rounded-3 mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-5"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search cooperatives..." value="<?= Helper::e($search) ?>"></div>
      <div class="col-auto"><button type="submit" class="btn btn-success btn-sm">Search</button><a href="<?= APP_URL ?>/admin/cooperatives" class="btn btn-outline-secondary btn-sm ms-1">Reset</a></div>
    </form>
  </div>
</div>
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Name</th><th>Reg. No</th><th>Manager</th><th>District</th><th>Members</th><th>Status</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($result['data'] as $c): ?>
          <tr>
            <td class="fw-semibold"><?= Helper::e($c['name']) ?></td>
            <td class="small"><?= Helper::e($c['registration_no'] ?? '-') ?></td>
            <td class="small"><?= Helper::e($c['first_name'] ? $c['first_name'].' '.$c['last_name'] : 'Unassigned') ?></td>
            <td class="small"><?= Helper::e($c['district_name'] ?? '-') ?></td>
            <td>
              <a href="<?= APP_URL ?>/admin/cooperatives/<?= $c['id'] ?>/members" class="badge bg-primary text-decoration-none">
                <i class="bi bi-people me-1"></i><?= $c['member_count'] ?> Members
              </a>
            </td>
            <td><?= Helper::statusBadge($c['status']) ?></td>
            <td>
              <a href="<?= APP_URL ?>/admin/cooperatives/<?= $c['id'] ?>/members" class="btn btn-sm btn-outline-success">
                <i class="bi bi-eye me-1"></i>View Members
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($result['data'])): ?><tr><td colspan="7" class="text-center text-muted py-4">No cooperatives found.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php if ($result['last_page'] > 1): ?>
  <div class="card-footer bg-white d-flex justify-content-between align-items-center">
    <small class="text-muted">Showing <?= count($result['data']) ?> of <?= $result['total'] ?></small>
    <?= Helper::paginate($result, APP_URL . '/admin/cooperatives') ?>
  </div>
  <?php endif; ?>
</div>

<!-- Add Cooperative Modal -->
<div class="modal fade" id="addCoopModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Add Cooperative</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="<?= APP_URL ?>/admin/cooperatives/store">
      <?= Auth::csrfField() ?>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-8"><label class="form-label fw-semibold small">Name *</label><input type="text" name="name" class="form-control" required></div>
          <div class="col-md-4"><label class="form-label fw-semibold small">Reg. Number</label><input type="text" name="registration_no" class="form-control"></div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small">Manager</label>
            <select name="manager_id" class="form-select">
              <option value="">Select Manager</option>
              <?php foreach ($managers as $m): ?><option value="<?= $m['id'] ?>"><?= Helper::e($m['first_name'].' '.$m['last_name']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small">District</label>
            <select name="district_id" class="form-select">
              <option value="">Select District</option>
              <?php foreach ($districts as $d): ?><option value="<?= $d['id'] ?>"><?= Helper::e($d['name']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6"><label class="form-label fw-semibold small">Phone</label><input type="text" name="phone" class="form-control"></div>
          <div class="col-md-6"><label class="form-label fw-semibold small">Email</label><input type="email" name="email" class="form-control"></div>
          <div class="col-12"><label class="form-label fw-semibold small">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Save</button></div>
    </form>
  </div></div>
</div>
