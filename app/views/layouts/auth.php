<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= Helper::e($title ?? '') ?> — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body class="auth-bg">
<div class="auth-wrapper d-flex align-items-center justify-content-center min-vh-100">
  <div class="container auth-container">
    <?php
      $flash = Helper::flash();
      if ($flash):
    ?>
    <div class="row justify-content-center mb-3">
      <div class="col-lg-10">
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
          <?= Helper::e($flash['message']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <?php require $content; ?>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
