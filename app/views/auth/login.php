<div class="login-shell login-shell-single">
  <section class="login-form-panel">
    <div class="login-mobile-brand"><a href="<?= APP_URL ?>/"><i class="bi bi-tree-fill"></i> <?= APP_NAME ?></a></div>
    <div class="login-form-wrap">
      <div class="login-heading">
        <span class="login-welcome-icon"><i class="bi bi-person"></i></span>
        <h2>Sign in to your account</h2>
        <p>Enter your details to continue to your dashboard.</p>
      </div>

      <form method="POST" action="<?= APP_URL ?>/login" class="login-form">
        <?= Auth::csrfField() ?>
        <div class="mb-3">
          <label for="loginEmail" class="form-label">Email address</label>
          <div class="login-input-wrap">
            <i class="bi bi-envelope"></i>
            <input id="loginEmail" type="email" name="email" class="form-control" placeholder="name@example.com" autocomplete="email" required autofocus>
            <div class="invalid-feedback">Please enter a valid email address.</div>
          </div>
        </div>
        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center">
            <label for="loginPassword" class="form-label">Password</label>
            <a href="<?= APP_URL ?>/forgot-password" class="forgot-link">Forgot password?</a>
          </div>
          <div class="login-input-wrap password-input-wrap">
            <i class="bi bi-lock"></i>
            <input id="loginPassword" type="password" name="password" class="form-control" placeholder="Enter your password" autocomplete="current-password" required>
            <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false"><i class="bi bi-eye"></i></button>
            <div class="invalid-feedback">Please enter your password.</div>
          </div>
        </div>
        <div class="form-check login-remember mb-4">
          <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
          <label class="form-check-label" for="remember">Keep me signed in on this device</label>
        </div>
        <button type="submit" class="btn login-submit w-100">Sign in <i class="bi bi-arrow-right"></i></button>
      </form>

      <div class="login-register">New to AgruKrwanda? <a href="<?= APP_URL ?>/register">Create a free account</a></div>

      <details class="demo-access">
        <summary><span><i class="bi bi-key"></i> Demo access</span><i class="bi bi-chevron-down"></i></summary>
        <div class="demo-content">
          <div><span>Administrator</span><code>admin@agrukrwanda.rw</code><small>Password: Admin@1234</small></div>
          <div><span>Cooperative manager</span><code>manager@kigaligrains.rw</code><small>Password: Admin@1234</small></div>
        </div>
      </details>
    </div>
    <p class="login-security-note"><i class="bi bi-lock-fill"></i> Your information is securely protected</p>
  </section>
</div>

<script>
document.querySelector('.password-toggle')?.addEventListener('click', function () {
  const input = document.getElementById('loginPassword');
  const showing = input.type === 'text';
  input.type = showing ? 'password' : 'text';
  this.setAttribute('aria-pressed', showing ? 'false' : 'true');
  this.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
  this.querySelector('i').className = showing ? 'bi bi-eye' : 'bi bi-eye-slash';
});
</script>
