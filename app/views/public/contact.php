<?php $title = 'Contact Us'; ?>

<section class="py-5 bg-success text-white text-center">
  <div class="container">
    <h1 class="fw-bold mb-2">Contact Us</h1>
    <p class="lead opacity-90 mb-0">We'd love to hear from you</p>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <div class="row g-5 justify-content-center">

      <div class="col-md-5">
        <h4 class="fw-bold mb-4">Get in Touch</h4>
        <ul class="list-unstyled">
          <li class="mb-3"><i class="bi bi-geo-alt-fill text-success me-2"></i>Kigali, Rwanda</li>
          <li class="mb-3"><i class="bi bi-envelope-fill text-success me-2"></i>info@agrukrwanda.rw</li>
          <li class="mb-3"><i class="bi bi-telephone-fill text-success me-2"></i>+250 700 000 000</li>
        </ul>
      </div>

      <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-4">Send a Message</h5>
            <form method="POST" action="<?= APP_URL ?>/contact">
              <?= Auth::csrfField() ?>
              <div class="mb-3">
                <label class="form-label">Your Name</label>
                <input type="text" name="name" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="5" required></textarea>
              </div>
              <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-send me-2"></i>Send Message
              </button>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
