<?php $title = 'Crops Management'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-flower1 text-success me-2"></i>Crops</h5>
  <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCropModal">
    <i class="bi bi-plus-lg me-1"></i>Add Crop
  </button>
</div>
<div class="card border-0 shadow-sm rounded-3 mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search crops..." value="<?= Helper::e($search) ?>"></div>
      <div class="col-md-3">
        <select name="category" class="form-select form-select-sm">
          <option value="">All Categories</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $catId == $c['id'] ? 'selected' : '' ?>><?= Helper::e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-success btn-sm">Filter</button>
        <a href="<?= APP_URL ?>/admin/crops" class="btn btn-outline-secondary btn-sm">Reset</a>
      </div>
    </form>
  </div>
</div>
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Name</th><th>Category</th><th>Variety</th><th>Unit</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($result['data'] as $c): ?>
          <tr>
            <td class="fw-semibold"><?= Helper::e($c['name']) ?></td>
            <td><span class="badge bg-success bg-opacity-10 text-success"><?= Helper::e($c['category_name']) ?></span></td>
            <td class="small"><?= Helper::e($c['variety'] ?? '-') ?></td>
            <td><?= Helper::e($c['unit']) ?></td>
            <td><?= Helper::statusBadge($c['status']) ?></td>
            <td>
              <button class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCropModal"
                data-id="<?= $c['id'] ?>" data-name="<?= Helper::e($c['name']) ?>" data-category="<?= $c['category_id'] ?>"
                data-variety="<?= Helper::e($c['variety'] ?? '') ?>" data-unit="<?= $c['unit'] ?>" data-status="<?= $c['status'] ?>">
                <i class="bi bi-pencil"></i>
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php if ($result['last_page'] > 1): ?>
  <div class="card-footer bg-white d-flex justify-content-between align-items-center">
    <small class="text-muted">Showing <?= count($result['data']) ?> of <?= $result['total'] ?></small>
    <?= Helper::paginate($result, APP_URL . '/admin/crops') ?>
  </div>
  <?php endif; ?>
</div>

<!-- Add Crop Modal -->
<div class="modal fade" id="addCropModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Add Crop</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="<?= APP_URL ?>/admin/crops/store">
      <?= Auth::csrfField() ?>
      <div class="modal-body">
        <div class="mb-3"><label class="form-label fw-semibold small">Category *</label>
          <select name="category_id" class="form-select" required>
            <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"><?= Helper::e($c['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="row g-2">
          <div class="col-md-6"><label class="form-label fw-semibold small">Name *</label><input type="text" name="name" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label fw-semibold small">Variety</label><input type="text" name="variety" class="form-control"></div>
          <div class="col-md-6"><label class="form-label fw-semibold small">Unit</label><input type="text" name="unit" class="form-control" value="kg"></div>
        </div>
        <div class="mt-2"><label class="form-label fw-semibold small">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Save</button></div>
    </form>
  </div></div>
</div>

<!-- Edit Crop Modal -->
<div class="modal fade" id="editCropModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Edit Crop</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" id="editCropForm">
      <?= Auth::csrfField() ?>
      <div class="modal-body">
        <div class="mb-3"><label class="form-label fw-semibold small">Category *</label>
          <select name="category_id" id="editCategory" class="form-select" required>
            <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"><?= Helper::e($c['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="row g-2">
          <div class="col-md-6"><label class="form-label fw-semibold small">Name *</label><input type="text" name="name" id="editName" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label fw-semibold small">Variety</label><input type="text" name="variety" id="editVariety" class="form-control"></div>
          <div class="col-md-6"><label class="form-label fw-semibold small">Unit</label><input type="text" name="unit" id="editUnit" class="form-control"></div>
          <div class="col-md-6"><label class="form-label fw-semibold small">Status</label>
            <select name="status" id="editStatus" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select>
          </div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Update</button></div>
    </form>
  </div></div>
</div>
<script>
document.getElementById('editCropModal').addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  document.getElementById('editCropForm').action = '<?= APP_URL ?>/admin/crops/' + btn.dataset.id + '/update';
  document.getElementById('editName').value = btn.dataset.name;
  document.getElementById('editCategory').value = btn.dataset.category;
  document.getElementById('editVariety').value = btn.dataset.variety;
  document.getElementById('editUnit').value = btn.dataset.unit;
  document.getElementById('editStatus').value = btn.dataset.status;
});
</script>
