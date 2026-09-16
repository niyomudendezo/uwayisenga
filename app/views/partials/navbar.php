<nav class="navbar public-navbar navbar-expand-lg navbar-dark sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= APP_URL ?>/">
      <i class="bi bi-tree-fill fs-4"></i><?= APP_NAME ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/market-prices">Market Prices</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/contact">Contact</a></li>
      </ul>
      <div class="d-flex gap-2">
        <?php if (Auth::check()): ?>
          <a href="<?= APP_URL . Auth::dashboardUrl() ?>" class="btn btn-light btn-sm">
            <i class="bi bi-speedometer2 me-1"></i>Dashboard
          </a>
        <?php else: ?>
          <a href="<?= APP_URL ?>/login" class="btn btn-link text-white text-decoration-none btn-sm px-3">Log in</a>
          <a href="<?= APP_URL ?>/register" class="btn btn-light btn-sm text-success fw-semibold px-3">Get started</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
