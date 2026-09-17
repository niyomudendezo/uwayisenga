<?php $title = 'Contact Us'; ?>

<div class="public-info-page"><section class="public-info-hero">
  <div class="container">
    <span>Talk to our team</span><h1>Contact us</h1>
    <p>We'd love to hear from you</p>
  </div>
</section>

<section class="public-info-content contact-content">
  <div class="container">
    <div class="row g-5 justify-content-center">

      <div class="col-md-5">
        <span class="section-eyebrow">Contact details</span><h2 class="fw-bold mb-4">Get in touch</h2>
        <ul class="list-unstyled">
          <li class="mb-3"><i class="bi bi-geo-alt-fill text-success me-2"></i>Kigali, Rwanda</li>
          <li class="mb-3"><i class="bi bi-envelope-fill text-success me-2"></i>info@agrukrwanda.rw</li>
          <li class="mb-3"><i class="bi bi-telephone-fill text-success me-2"></i>+250 700 000 000</li>
        </ul>
      </div>

      <div class="col-md-6">
        <div class="card public-contact-card">
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
</section></div>
