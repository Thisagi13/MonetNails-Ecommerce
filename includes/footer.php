<?php
/**
 * includes/footer.php — Unified Master Footer Section
 * Monet Nails E-Commerce Application
 *
 * Renders the common site footer across all customer-end pages.
 *
 * Usage:
 *   <?php require __DIR__ . '/../../includes/footer.php'; ?>
 *   or:
 *   <?php require 'includes/footer.php'; ?>
 */

$base_url = $base_url ?? '/MonetNails-Ecommerce2/MonetNails-Ecommerce';
?>
<!-- Common Footer Stylesheet -->
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/footer.css">

<!-- ============ SITE FOOTER ============ -->
<footer class="site-footer" id="site-footer">
  <div class="footer-grid">

    <!-- Column 1: Brand -->
    <div class="footer-brand">
      <a href="<?php echo $base_url; ?>/" class="footer-logo" id="footer-logo">
        <img src="<?php echo $base_url; ?>/assets/images/home/node-161.png" alt="Monet Nails">
      </a>
      <p class="brand-tagline">Artistry in every touch. Elevating nail care to a fine art.</p>
      <p class="brand-copy">&copy; <?php echo date('Y'); ?> Monet Nails. Artistry in every touch.</p>
    </div>

    <!-- Column 2: Explore -->
    <div class="footer-col">
      <h4>Explore</h4>
      <a href="<?php echo $base_url; ?>/" id="footer-home">Home</a>
      <a href="<?php echo $base_url; ?>/index.php?page=about" id="footer-about">About Us</a>
      <a href="<?php echo $base_url; ?>/index.php?page=services" id="footer-services">Services</a>
      <a href="<?php echo $base_url; ?>/index.php?page=booking" id="footer-booking">Booking</a>
      <a href="<?php echo $base_url; ?>/index.php?page=gallery" id="footer-gallery">Gallery</a>
      <a href="<?php echo $base_url; ?>/index.php?page=contact" id="footer-contact">Contact</a>
      <a href="<?php echo $base_url; ?>/privacy.php" id="footer-privacy">Privacy Policy</a>
    </div>

    <!-- Column 3: Contact -->
    <div class="footer-col">
      <h4>Contact</h4>
      <div class="contact-row">
        <img src="<?php echo $base_url; ?>/assets/images/home/icon-184.svg" alt="Address icon">
        <span>No 90/1, 4th Lane, Werellawatta, Yakkala</span>
      </div>
      <div class="contact-row">
        <img src="<?php echo $base_url; ?>/assets/images/home/icon-188.svg" alt="Phone icon">
        <span>+94 77 210 5954</span>
      </div>
      <div class="contact-row">
        <img src="<?php echo $base_url; ?>/assets/images/home/icon-192.svg" alt="Email icon">
        <span>hello@monetnails.com</span>
      </div>
    </div>

    <!-- Column 4: Follow Us -->
    <div class="footer-col">
      <h4>Follow Us</h4>
      <div class="social-row">
        <a href="https://www.instagram.com/_monet.nails_?igsh=MXdjd2Z0aGhqNGdiZA%3D%3D&igsi=MXdjd2Z0aGhqNGdiZA%3D%3D&utm_source=qr" id="social-instagram" aria-label="Instagram">
          <img src="<?php echo $base_url; ?>/assets/images/home/icon-204.svg" alt="Instagram">
        </a>
        <a href="https://www.facebook.com/share/1C2fWPxDuY/?mibextid=wwXIfr" id="social-facebook" aria-label="Facebook">
          <img src="<?php echo $base_url; ?>/assets/images/home/icon-207.svg" alt="Facebook">
        </a>
        <a href="https://www.tiktok.com/@monet.nails" id="social-Tiktok" aria-label="Tiktok">
          <img src="<?php echo $base_url; ?>/assets/images/home/icon-201.svg" alt="Tiktok">
        </a>
      </div>
    </div>

  </div>
</footer>
