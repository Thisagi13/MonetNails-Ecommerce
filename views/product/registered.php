<?php
/**
 * views/product/registered.php
 * Product Details page — registered customer view
 */
function e($t) { return htmlspecialchars($t, ENT_QUOTES); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo e($data['product']['product_name']); ?> | Monet Nails</title>
  <meta name="description" content="<?php echo e(mb_substr($data['product']['description'] ?? '', 0, 160)); ?>">

  <!-- Google Fonts: Bodoni Moda (headings) + Manrope (body) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400;0,6..96,500;1,6..96,400&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Shared & Product Stylesheets with Cache Busting -->
  <link rel="stylesheet" href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/css/gallery.css?v=2">
  <link rel="stylesheet" href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/css/product.css?v=2">
  <style>
    .btn-buy, #btn-buy {
      background: #1C1B1B !important;
      background-color: #1C1B1B !important;
      color: #ffffff !important;
      border: 1px solid #1C1B1B !important;
      opacity: 1 !important;
      visibility: visible !important;
      display: block !important;
    }
    .btn-buy:hover, #btn-buy:hover {
      background: #A9275E !important;
      background-color: #A9275E !important;
      border-color: #A9275E !important;
      color: #ffffff !important;
      opacity: 0.95 !important;
    }
    .btn-cart, #btn-cart {
      background: transparent !important;
      color: #A9275E !important;
      border: 1px solid #A9275E !important;
      display: block !important;
    }
    .btn-cart:hover, #btn-cart:hover {
      background: #FDF2F6 !important;
      border-color: #A9275E !important;
      color: #A9275E !important;
    }
  </style>
</head>
<body>

<!-- ==================== TOP NAV (Registered) ==================== -->
<nav class="top-nav" id="top-nav">
  <div class="nav-inner">
    <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/" id="nav-logo">
      <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/node-210.png" alt="Monet Nails" class="logo-img">
    </a>
    <div class="nav-links">
      <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/" id="nav-home">Home</a>
      <a href="about.php" id="nav-about">About</a>
      <a href="services.php" id="nav-services">Services</a>
      <a href="index.php?page=gallery" class="active" id="nav-gallery">Gallery</a>
      <a href="contact.php" id="nav-contact">Contact</a>
    </div>
    <div class="nav-actions">
      <!-- Cart Icon -->
      <a href="index.php?page=cart" class="nav-cart-btn" title="Cart" id="nav-cart-icon">
        <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home_registered/vector-226.svg" alt="Cart">
      </a>

      <!-- Profile Avatar Button -->
      <div class="profile-btn" id="profileBtn" title="Profile">
        <?php echo $avatar_html ?? '<span class="avatar-initials">M</span>'; ?>
      </div>

      <!-- Profile Dropdown Popup -->
      <div class="profile-popup" id="profilePopup">
        <div class="popup-header">
          <div class="popup-avatar">
            <?php echo $avatar_html ?? '<span class="avatar-initials">M</span>'; ?>
          </div>
          <div>
            <div class="popup-name"><?php echo htmlspecialchars($customer_name ?? 'Client'); ?></div>
            <div class="popup-email"><?php echo htmlspecialchars($customer_email ?? ''); ?></div>
          </div>
        </div>

        <div class="popup-menu">
          <a href="my_appointments.php" class="popup-item" id="popup-appointments">
            <svg viewBox="0 0 24 24" fill="none" stroke="#A9275E" stroke-width="1.8">
              <rect x="3" y="5" width="18" height="16" rx="2"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
              <line x1="8" y1="3" x2="8" y2="7"/>
              <line x1="16" y1="3" x2="16" y2="7"/>
            </svg>
            My Appointments
          </a>
          <a href="order_history.php" class="popup-item" id="popup-orders">
            <svg viewBox="0 0 24 24" fill="none" stroke="#A9275E" stroke-width="1.8">
              <path d="M6 8h12l-1 12H7L6 8z"/>
              <path d="M9 8V6a3 3 0 0 1 6 0v2"/>
            </svg>
            Order History &amp; Custom Sets
          </a>
          <a href="account_settings.php" class="popup-item" id="popup-settings">
            <svg viewBox="0 0 24 24" fill="none" stroke="#A9275E" stroke-width="1.8">
              <circle cx="12" cy="12" r="3"/>
              <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
            Account Settings
          </a>
        </div>

        <div class="popup-footer">
          <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=logout" class="popup-item" id="popup-logout">
            <svg viewBox="0 0 24 24" fill="none" stroke="#A9275E" stroke-width="1.8">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Sign Out
          </a>
        </div>
      </div>
    </div>
  </div>
</nav>

<!-- popup backdrop -->
<div class="popup-overlay" id="popupOverlay"></div>

<!-- ==================== PRODUCT MAIN ==================== -->
<main class="main" id="product-main">
  <section class="product">

    <!-- Left: product image -->
    <div class="product-image" id="product-image-box">
      <img src="<?php echo e($data['productImage']); ?>"
           alt="<?php echo e($data['product']['product_name']); ?>"
           id="product-img">
    </div>

    <!-- Right: product info -->
    <div class="product-info" id="product-info">

      <!-- Breadcrumb -->
      <nav class="breadcrumb" aria-label="breadcrumb">
        <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/">Home</a>
        <span>/</span>
        <a href="index.php?page=gallery">Gallery</a>
        <span>/</span>
        <span class="here"><?php echo e($data['product']['product_name']); ?></span>
      </nav>

      <h1 class="product-title" id="product-title"><?php echo e($data['product']['product_name']); ?></h1>
      <p class="product-price" id="product-price"><?php echo e($data['priceText']); ?></p>
      <p class="product-description" id="product-desc"><?php echo e($data['product']['description'] ?? ''); ?></p>

      <?php if ($data['error'] !== ""): ?>
        <p class="alert alert-error" id="alert-error"><?php echo e($data['error']); ?></p>
      <?php endif; ?>

      <!-- Action Buttons -->
      <form class="product-actions" method="POST"
            action="index.php?page=product&id=<?php echo (int)$data['product']['product_id']; ?>"
            onsubmit="return lockButtons();">
        <input type="hidden" name="page" value="product">
        <button type="submit" name="action" value="buy_now" class="btn btn-buy" id="btn-buy" style="background-color: #1C1B1B !important; color: #ffffff !important; border: 1px solid #1C1B1B !important; opacity: 1 !important; visibility: visible !important;">Buy Now</button>
        <button type="submit" name="action" value="add_to_cart" class="btn btn-cart" id="btn-cart">Add to Cart</button>
      </form>

      <!-- Details -->
      <div class="product-details" id="product-details">
        <?php if (!empty($data['product']['material_info'])): ?>
        <div class="detail-block">
          <h3 class="detail-title">Material</h3>
          <p class="detail-text"><?php echo e($data['product']['material_info']); ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($data['product']['includes_info'])): ?>
        <div class="detail-block">
          <h3 class="detail-title">Includes</h3>
          <p class="detail-text"><?php echo e($data['product']['includes_info']); ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($data['product']['shipping_info'])): ?>
        <div class="detail-block">
          <h3 class="detail-title">Shipping</h3>
          <p class="detail-text"><?php echo e($data['product']['shipping_info']); ?></p>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </section>
</main>

<!-- ==================== SITE FOOTER ==================== -->
<footer class="site-footer" id="site-footer">
  <div class="footer-grid">
    <div class="footer-brand">
      <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/" class="footer-logo">
        <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/node-161.png" alt="Monet Nails">
      </a>
      <p class="footer-tagline">Artistry in every touch. Elevating nail care to a fine art.</p>
      <p class="footer-copy">&copy; 2024 Monet Nails. Artistry in every touch.</p>
    </div>

    <div class="footer-col">
      <h4>Explore</h4>
      <a href="services.php">Services</a>
      <a href="booking.php">Booking</a>
      <a href="about.php">About Us</a>
      <a href="privacy.php">Privacy Policy</a>
    </div>

    <div class="footer-col">
      <h4>Contact</h4>
      <div class="contact-row">
        <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-184.svg" alt="Address">
        <span>No 90/1, 4th Lane, Werellawatta, Yakkala</span>
      </div>
      <div class="contact-row">
        <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-188.svg" alt="Phone">
        <span>+94 77 210 5954</span>
      </div>
      <div class="contact-row">
        <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-192.svg" alt="Email">
        <span>hello@monetnails.com</span>
      </div>
    </div>

    <div class="footer-col">
      <h4>Follow Us</h4>
      <div class="social-row">
        <a href="https://tiktok.com" aria-label="TikTok">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/gallery/icon-152.svg" alt="TikTok">
        </a>
        <a href="https://instagram.com" aria-label="Instagram">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/gallery/icon-155.svg" alt="Instagram">
        </a>
        <a href="https://facebook.com" aria-label="Facebook">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/gallery/icon-158.svg" alt="Facebook">
        </a>
      </div>
    </div>
  </div>
</footer>

<script>
/* Profile Popup */
(function () {
  var btn     = document.getElementById('profileBtn');
  var popup   = document.getElementById('profilePopup');
  var overlay = document.getElementById('popupOverlay');
  var logoutBtn = document.getElementById('popup-logout');

  function togglePopup(e) {
    e.stopPropagation();
    var isOpen = popup.classList.toggle('show');
    if (overlay) overlay.classList.toggle('show', isOpen);
  }

  function closePopup() {
    popup.classList.remove('show');
    if (overlay) overlay.classList.remove('show');
  }

  if (btn && popup) {
    btn.addEventListener('click', togglePopup);
    popup.addEventListener('click', function (e) { e.stopPropagation(); });
    if (overlay) overlay.addEventListener('click', closePopup);
    document.addEventListener('click', function (e) {
      if (popup.classList.contains('show') && !popup.contains(e.target) && e.target !== btn && !btn.contains(e.target)) {
        closePopup();
      }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closePopup();
    });
  }

  if (logoutBtn) {
    logoutBtn.addEventListener('click', function(e) {
      window.location.href = this.href;
    });
  }
})();

/* Double-click guard on form buttons */
var alreadySent = false;
function lockButtons() {
  if (alreadySent) return false;
  alreadySent = true;
  return true;
}
</script>
</body>
</html>
