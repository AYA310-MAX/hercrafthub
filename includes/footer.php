<?php
if (!function_exists('url_for')) {
    require_once __DIR__ . '/helpers.php';
}
include __DIR__ . '/logout_modal.php';
echo render_goodbye_modal();
?>
<footer style="background: var(--footer-bg, #4a154b); color: #fff; padding: 60px 0 20px;">
  <div class="container">
    <div class="row">
      <div class="col-md-3 mb-4">
        <h5 style="font-weight: 700; letter-spacing: 1px; margin-bottom: 20px;">HerCraft<span style="color: #f7b731;">Hub</span></h5>
        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.6;">
          A premium, empowering marketplace for women selling handmade goods, tech crafts, and digital products.
        </p>
      </div>
      <div class="col-md-3 mb-4">
        <h6 style="font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; margin-bottom: 20px; color: rgba(255,255,255,0.9);">Quick Links</h6>
        <ul class="list-unstyled" style="font-size: 0.9rem;">
          <li class="mb-2"><a href="<?= htmlspecialchars(url_for('index.php')) ?>" style="color: #ddd; text-decoration: none; transition: color 0.3s;"><i class="ti ti-home" style="margin-right: 8px;"></i> Home</a></li>
          <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'seller'): ?>
          <li class="mb-2"><a href="<?= htmlspecialchars(url_for('browse.php')) ?>" style="color: #ddd; text-decoration: none; transition: color 0.3s;"><i class="ti ti-search" style="margin-right: 8px;"></i> Browse Listings</a></li>
          <?php endif; ?>
          <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'buyer'): ?>
          <li class="mb-2"><a href="<?= htmlspecialchars(url_for('sell.php')) ?>" style="color: #ddd; text-decoration: none; transition: color 0.3s;"><i class="ti ti-tag" style="margin-right: 8px;"></i> Sell an Item</a></li>
          <?php endif; ?>
          <?php if (!isset($_SESSION['user_id'])): ?>
          <li class="mb-2"><a href="<?= htmlspecialchars(url_for('register.php')) ?>" style="color: #ddd; text-decoration: none; transition: color 0.3s;"><i class="ti ti-user-plus" style="margin-right: 8px;"></i> Join Free</a></li>
          <?php endif; ?>
        </ul>
      </div>
      <div class="col-md-3 mb-4">
        <h6 style="font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; margin-bottom: 20px; color: rgba(255,255,255,0.9);">Company Info</h6>
        <ul class="list-unstyled" style="font-size: 0.9rem;">
          <li class="mb-2"><a href="<?= htmlspecialchars(url_for('about.php')) ?>" style="color: #ddd; text-decoration: none; transition: color 0.3s;"><i class="ti ti-info-circle" style="margin-right: 8px;"></i> About Us</a></li>
          <li class="mb-2"><a href="<?= htmlspecialchars(url_for('demo.php')) ?>" style="color: #ddd; text-decoration: none; transition: color 0.3s;"><i class="ti ti-device-desktop" style="margin-right: 8px;"></i> App Demo</a></li>
          <li class="mb-2"><a href="<?= htmlspecialchars(url_for('demo.php#faq')) ?>" style="color: #ddd; text-decoration: none; transition: color 0.3s;"><i class="ti ti-help" style="margin-right: 8px;"></i> FAQ</a></li>
          <li class="mb-2"><a href="<?= htmlspecialchars(url_for('contact.php')) ?>" style="color: #ddd; text-decoration: none; transition: color 0.3s;"><i class="ti ti-messages" style="margin-right: 8px;"></i> Get In Touch</a></li>
        </ul>
      </div>
      <div class="col-md-3 mb-4">
        <h6 style="font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; margin-bottom: 20px; color: rgba(255,255,255,0.9);">Contact</h6>
        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.6;">
          <i class="ti ti-mail" style="margin-right: 8px;"></i> <a href="mailto:hello@hercrafthub.co.za" style="color: #fff; text-decoration: none;">hello@hercrafthub.co.za</a><br><br>
          Built in South Africa
        </p>
      </div>
    </div>
    <hr style="border-color: rgba(255,255,255,0.1); margin: 30px 0 20px;">
    <div class="d-flex justify-content-between align-items-center flex-wrap" style="font-size: 0.85rem; opacity: 0.7;">
      <p class="mb-0">
        &copy; <?= date('Y') ?> HerCraft Hub. All rights reserved.
      </p>
      <div class="d-flex gap-3 mt-2 mt-md-0">
        <a href="#" style="color: #fff;"><i class="ti ti-brand-facebook fs-5"></i></a>
        <a href="#" style="color: #fff;"><i class="ti ti-brand-instagram fs-5"></i></a>
        <a href="#" style="color: #fff;"><i class="ti ti-brand-twitter fs-5"></i></a>
      </div>
    </div>
  </div>
</footer>
