<?php $role = Auth::role(); $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
<nav class="sidebar app-sidebar text-white" id="sidebar">
  <div class="sidebar-header">
    <a href="<?= APP_URL ?>/" class="text-white text-decoration-none d-flex align-items-center gap-2">
      <span class="sidebar-logo"><i class="bi bi-tree-fill"></i></span>
      <span class="sidebar-brand"><b><?= APP_NAME ?></b><small>Agriculture network</small></span>
    </a>
  </div>

  <div class="sidebar-user">
    <div class="d-flex align-items-center gap-2">
      <?php if (Auth::avatar()): ?>
        <img src="<?= APP_URL ?>/assets/uploads/<?= Auth::avatar() ?>" class="rounded-circle" width="36" height="36" style="object-fit:cover">
      <?php else: ?>
        <div class="avatar-placeholder sidebar-avatar">
          <?= strtoupper(substr(Auth::name(), 0, 1)) ?>
        </div>
      <?php endif; ?>
      <div class="overflow-hidden">
        <div class="fw-semibold text-truncate small"><?= Helper::e(Auth::name()) ?></div>
        <div class="sidebar-role text-capitalize"><?= str_replace('_',' ',$role) ?></div>
      </div>
    </div>
  </div>

  <ul class="nav flex-column sidebar-nav">
    <?php if ($role === 'admin'): ?>
      <?php $navItems = [
        ['/admin/dashboard','bi-speedometer2','Dashboard'],
        ['/admin/users','bi-people','Users'],
        ['/admin/cooperatives','bi-building','Cooperatives'],
        ['/admin/farmers','bi-person-badge','Farmers'],
        ['/admin/buyers','bi-shop','Buyers'],
        ['/admin/crops','bi-flower1','Crops'],
        ['/admin/inventory','bi-boxes','Inventory'],
        ['/admin/warehouses','bi-building-fill','Warehouses'],
        ['/admin/market-prices','bi-graph-up','Market Prices'],
        ['/admin/orders','bi-cart3','Orders'],
        ['/admin/ai-predictions','bi-robot','AI Predictions'],
        ['/admin/reports','bi-file-earmark-bar-graph','Reports'],
        ['/admin/audit-logs','bi-shield-check','Audit Logs'],
        ['/admin/settings','bi-gear','Settings'],
      ]; ?>
    <?php elseif ($role === 'cooperative_manager'): ?>
      <?php $navItems = [
        ['/cooperative/dashboard','bi-speedometer2','Dashboard'],
        ['/cooperative/members','bi-people','Members'],
        ['/cooperative/harvests','bi-basket','Harvests'],
        ['/cooperative/inventory','bi-boxes','Inventory'],
        ['/cooperative/orders','bi-cart3','Orders'],
        ['/cooperative/production-plans','bi-calendar-check','Production Plans'],
        ['/cooperative/ai-predictions','bi-robot','AI Predictions'],
        ['/cooperative/reports','bi-file-earmark-bar-graph','Reports'],
      ]; ?>
    <?php elseif ($role === 'farmer'): ?>
      <?php $navItems = [
        ['/farmer/dashboard','bi-speedometer2','Dashboard'],
        ['/farmer/harvests','bi-basket','My Harvests'],
        ['/farmer/market-prices','bi-graph-up','Market Prices'],
        ['/farmer/ai-recommendations','bi-robot','AI Recommendations'],
        ['/farmer/sales-history','bi-receipt','Sales History'],
        ['/farmer/profile','bi-person-circle','My Profile'],
      ]; ?>
    <?php elseif ($role === 'buyer'): ?>
      <?php $navItems = [
        ['/buyer/dashboard','bi-speedometer2','Dashboard'],
        ['/buyer/marketplace','bi-shop','Marketplace'],
        ['/buyer/orders','bi-cart3','My Orders'],
        ['/buyer/market-prices','bi-graph-up','Market Prices'],
        ['/buyer/profile','bi-person-circle','My Profile'],
      ]; ?>
    <?php else: $navItems = []; ?>
    <?php endif; ?>

    <li class="sidebar-section-label"><span>Management</span></li>
    <?php foreach ($navItems as [$path, $icon, $label]): ?>
      <?php $active = str_starts_with($uri, dirname($_SERVER['SCRIPT_NAME']) . $path) ? 'active' : ''; ?>
      <li class="nav-item">
        <a href="<?= APP_URL . $path ?>" class="nav-link text-white <?= $active ?>">
          <i class="bi <?= $icon ?>"></i><span><?= $label ?></span>
        </a>
      </li>
    <?php endforeach; ?>

    <li class="sidebar-section-label sidebar-account-label"><span>Account</span></li>
    <li class="nav-item">
      <a href="<?= APP_URL ?>/profile" class="nav-link text-white">
        <i class="bi bi-person-circle"></i><span>Profile</span>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= APP_URL ?>/logout" class="nav-link text-white-50">
        <i class="bi bi-box-arrow-left"></i><span>Logout</span>
      </a>
    </li>
  </ul>
</nav>
