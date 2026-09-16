<div class="login-shell register-shell register-shell-single">
  <section class="login-form-panel register-form-panel">
    <div class="login-mobile-brand"><a href="<?= APP_URL ?>/"><i class="bi bi-tree-fill"></i> <?= APP_NAME ?></a></div>
    <div class="login-form-wrap register-form-wrap">
      <div class="register-heading-row">
        <div class="login-heading mb-0"><span class="login-welcome-icon"><i class="bi bi-person-plus"></i></span><h2>Create your account</h2><p>It only takes a minute to get started.</p></div>
        <span class="form-step-label">Account setup</span>
      </div>

      <form id="registerForm" method="POST" action="<?= APP_URL ?>/register" class="login-form register-form">
        <?= Auth::csrfField() ?>

        <fieldset class="role-fieldset">
          <legend>I want to join as</legend>
          <div class="role-options">
            <?php foreach ($roles as $index => $r): ?>
              <?php $isBuyer = $r['name'] === 'buyer'; ?>
              <label class="role-option">
                <input type="radio" name="role" value="<?= Helper::e($r['name']) ?>" <?= $index === 0 ? 'checked' : '' ?> required>
                <span class="role-option-box"><i class="bi <?= $isBuyer ? 'bi-shop' : 'bi-flower1' ?>"></i><span><strong><?= $isBuyer ? 'Buyer' : 'Farmer' ?></strong><small><?= $isBuyer ? 'Source quality produce' : 'Sell and grow smarter' ?></small></span><i class="bi bi-check-circle-fill role-check"></i></span>
              </label>
            <?php endforeach; ?>
          </div>
        </fieldset>

        <div class="row g-3">
          <div class="col-sm-6">
            <label for="firstName" class="form-label">First name <span>*</span></label>
            <div class="login-input-wrap"><i class="bi bi-person"></i><input id="firstName" type="text" name="first_name" class="form-control" placeholder="Your first name" autocomplete="given-name" required></div>
          </div>
          <div class="col-sm-6">
            <label for="lastName" class="form-label">Last name <span>*</span></label>
            <div class="login-input-wrap"><i class="bi bi-person"></i><input id="lastName" type="text" name="last_name" class="form-control" placeholder="Your last name" autocomplete="family-name" required></div>
          </div>
          <div class="col-12">
            <label for="registerEmail" class="form-label">Email address <span>*</span></label>
            <div class="login-input-wrap"><i class="bi bi-envelope"></i><input id="registerEmail" type="email" name="email" class="form-control" placeholder="name@example.com" autocomplete="email" required></div>
          </div>
          <div class="col-sm-6">
            <label for="registerPhone" class="form-label">Phone number</label>
            <div class="login-input-wrap"><i class="bi bi-telephone"></i><input id="registerPhone" type="tel" name="phone" class="form-control" placeholder="+250 7XX XXX XXX" autocomplete="tel"></div>
          </div>
          <div class="col-sm-6">
            <label for="registerDistrict" class="form-label">District</label>
            <div class="login-input-wrap select-input-wrap"><i class="bi bi-geo-alt"></i><select id="registerDistrict" name="district_id" class="form-select"><option value="">Select your district</option><?php foreach ($districts as $d): ?><option value="<?= (int) $d['id'] ?>"><?= Helper::e($d['name']) ?></option><?php endforeach; ?></select></div>
          </div>
          <div class="col-sm-6">
            <label for="registerPassword" class="form-label">Password <span>*</span></label>
            <div class="login-input-wrap password-input-wrap"><i class="bi bi-lock"></i><input id="registerPassword" type="password" name="password" class="form-control" placeholder="At least 8 characters" minlength="8" autocomplete="new-password" required><button class="password-toggle" type="button" data-target="registerPassword" aria-label="Show password"><i class="bi bi-eye"></i></button></div>
          </div>
          <div class="col-sm-6">
            <label for="confirmPassword" class="form-label">Confirm password <span>*</span></label>
            <div class="login-input-wrap password-input-wrap"><i class="bi bi-shield-lock"></i><input id="confirmPassword" type="password" name="password_confirm" class="form-control" placeholder="Repeat your password" minlength="8" autocomplete="new-password" required><button class="password-toggle" type="button" data-target="confirmPassword" aria-label="Show password"><i class="bi bi-eye"></i></button></div>
            <small id="passwordMatchError" class="password-match-error">Passwords do not match.</small>
          </div>
        </div>

        <p class="register-terms">By creating an account, you agree to use AgruKrwanda responsibly and provide accurate information.</p>
        <button type="submit" class="btn login-submit w-100">Create my account <i class="bi bi-arrow-right"></i></button>
      </form>

      <div class="login-register register-login-link">Already have an account? <a href="<?= APP_URL ?>/login">Sign in instead</a></div>
    </div>
  </section>
</div>

<script>
document.querySelectorAll('.password-toggle').forEach(function (button) {
  button.addEventListener('click', function () {
    const input = document.getElementById(this.dataset.target);
    const showing = input.type === 'text';
    input.type = showing ? 'password' : 'text';
    this.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    this.querySelector('i').className = showing ? 'bi bi-eye' : 'bi bi-eye-slash';
  });
});

document.getElementById('registerForm')?.addEventListener('submit', function (event) {
  const password = document.getElementById('registerPassword');
  const confirmation = document.getElementById('confirmPassword');
  const error = document.getElementById('passwordMatchError');
  const matches = password.value === confirmation.value;
  confirmation.setCustomValidity(matches ? '' : 'Passwords do not match');
  error.classList.toggle('show', !matches);
  if (!matches) { event.preventDefault(); confirmation.focus(); }
});
