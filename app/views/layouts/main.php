<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= Helper::e($title ?? 'Dashboard') ?> — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body class="dashboard-body">

<!-- Sidebar -->
<div class="d-flex">
<?php require BASE_PATH . '/app/views/partials/sidebar.php'; ?>

<!-- Main Content -->
<div class="flex-grow-1 main-content">
  <!-- Topbar -->
  <?php require BASE_PATH . '/app/views/partials/topbar.php'; ?>

  <!-- Page Content -->
  <div class="container-fluid dashboard-content px-4 py-4">
    <?php
      $flash = Helper::flash();
      if ($flash):
    ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
      <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill me-2"></i>
      <?= Helper::e($flash['message']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php require $content; ?>
  </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
