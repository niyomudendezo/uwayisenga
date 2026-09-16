<?php $title = 'Users Management'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-people text-success me-2"></i>Users</h5>
  <a href="<?= APP_URL ?>/admin/users/create" class="btn btn-success btn-sm">
    <i class="bi bi-plus-lg me-1"></i>Add User
  </a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-3 mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-5">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, email..." value="<?= Helper::e($search) ?>">
      </div>
      <div class="col-md-3">
        <select name="role" class="form-select form-select-sm">
          <option value="">All Roles</option>
          <?php foreach ($roles as $r): ?>
            <option value="<?= $r['name'] ?>" <?= $role === $r['name'] ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$r['name'])) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-success btn-sm">Filter</button>
        <a href="<?= APP_URL ?>/admin/users" class="btn btn-outline-secondary btn-sm">Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>District</th><th>Status</th><th>Last Login</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($result['data'] as $i => $u): ?>
          <tr>
            <td class="text-muted small"><?= ($result['current_page']-1)*$result['per_page']+$i+1 ?></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:.75rem;font-weight:700;flex-shrink:0">
                  <?= strtoupper(substr($u['first_name'],0,1)) ?>
                </div>
                <div>
                  <div class="fw-semibold small"><?= Helper::e($u['first_name'].' '.$u['last_name']) ?></div>
                </div>
              </div>
            </td>
            <td class="small"><?= Helper::e($u['email']) ?></td>
            <td class="small"><?= Helper::e($u['phone'] ?? '-') ?></td>
            <td><span class="badge bg-success bg-opacity-10 text-success"><?= ucfirst(str_replace('_',' ',$u['role_name'])) ?></span></td>
            <td class="small"><?= Helper::e($u['district_name'] ?? '-') ?></td>
            <td><?= Helper::statusBadge($u['status']) ?></td>
            <td class="small text-muted"><?= $u['last_login'] ? Helper::timeAgo($u['last_login']) : 'Never' ?></td>
            <td>
              <div class="d-flex gap-1">
                <a href="<?= APP_URL ?>/admin/users/<?= $u['id'] ?>/edit" class="btn btn-xs btn-outline-primary" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                <?php if ($u['id'] != Auth::id()): ?>
                <form method="POST" action="<?= APP_URL ?>/admin/users/<?= $u['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Deactivate this user?')">
                  <?= Auth::csrfField() ?>
                  <button type="submit" class="btn btn-xs btn-outline-danger" title="Deactivate">
                    <i class="bi bi-person-x"></i>
                  </button>
                </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($result['data'])): ?>
          <tr><td colspan="9" class="text-center text-muted py-4">No users found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php if ($result['last_page'] > 1): ?>
  <div class="card-footer bg-white d-flex justify-content-between align-items-center">
    <small class="text-muted">Showing <?= count($result['data']) ?> of <?= $result['total'] ?> users</small>
    <?= Helper::paginate($result, APP_URL . '/admin/users') ?>
  </div>
  <?php endif; ?>
</div>
