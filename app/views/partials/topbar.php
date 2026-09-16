<nav class="navbar navbar-expand-lg dashboard-topbar px-4 topbar">
  <button class="topbar-icon-btn me-3" id="sidebarToggle" aria-label="Toggle sidebar">
    <i class="bi bi-list fs-5"></i>
  </button>

  <nav aria-label="breadcrumb" class="d-none d-md-block topbar-context">
    <span>Admin workspace</span><strong><?= Helper::e($title ?? 'Dashboard') ?></strong>
  </nav>

  <div class="ms-auto d-flex align-items-center gap-3">
    <!-- Notifications -->
    <div class="dropdown">
      <button class="topbar-icon-btn position-relative" id="notifBtn" data-bs-toggle="dropdown" aria-label="Notifications">
        <i class="bi bi-bell"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notifBadge" style="display:none"></span>
      </button>
      <div class="dropdown-menu dropdown-menu-end shadow" style="width:320px;max-height:400px;overflow-y:auto" id="notifDropdown">
        <div class="dropdown-header d-flex justify-content-between align-items-center">
          <span class="fw-semibold">Notifications</span>
          <a href="#" class="small text-success" id="markAllRead">Mark all read</a>
        </div>
        <div id="notifList"><div class="text-center py-3 text-muted small">Loading...</div></div>
      </div>
    </div>

    <!-- User Menu -->
    <div class="dropdown">
      <button class="btn btn-sm d-flex align-items-center gap-2" data-bs-toggle="dropdown">
        <?php if (Auth::avatar()): ?>
          <img src="<?= APP_URL ?>/assets/uploads/<?= Auth::avatar() ?>" class="rounded-circle" width="28" height="28" style="object-fit:cover">
        <?php else: ?>
          <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width:28px;height:28px;font-size:.75rem;font-weight:700">
            <?= strtoupper(substr(Auth::name(), 0, 1)) ?>
          </div>
        <?php endif; ?>
        <span class="d-none d-md-inline small fw-semibold"><?= Helper::e(Auth::name()) ?></span>
        <i class="bi bi-chevron-down small"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow">
        <li><a class="dropdown-item" href="<?= APP_URL ?>/profile"><i class="bi bi-person me-2"></i>Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="<?= APP_URL ?>/logout"><i class="bi bi-box-arrow-left me-2"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
