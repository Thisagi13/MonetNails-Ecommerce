<?php
function galleryImgUrl($url): string {
    if (empty($url)) return '/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/gallery/signature-blossom.png';
    if (strpos($url, '/') === 0 || strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
        return $url;
    }
    return '/MonetNails-Ecommerce2/MonetNails-Ecommerce/' . ltrim($url, '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>The Gallery &amp; Shop | Monet Nails</title>
  <meta name="description" content="Browse and shop our handcrafted signature press-on nail sets at Monet Nails Bar.">

  <!-- Google Fonts: Bodoni Moda (headings) + Manrope (body) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400;0,6..96,500;1,6..96,400&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Gallery Stylesheet -->
  <link rel="stylesheet" href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/css/gallery.css?v=2">
  <style>
    .top-nav { z-index: 200 !important; }
    .popup-overlay { z-index: 90 !important; }
    .profile-popup { z-index: 300 !important; }
    .popup-item svg, .popup-item img { pointer-events: none !important; }
  </style>
</head>
<body>

<!-- ==================== TOP NAV (Registered Customer) ==================== -->
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
            <!-- calendar icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="#A9275E" stroke-width="1.8">
              <rect x="3" y="5" width="18" height="16" rx="2"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
              <line x1="8" y1="3" x2="8" y2="7"/>
              <line x1="16" y1="3" x2="16" y2="7"/>
            </svg>
            My Appointments
          </a>
          <a href="order_history.php" class="popup-item" id="popup-orders">
            <!-- shopping bag icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="#A9275E" stroke-width="1.8">
              <path d="M6 8h12l-1 12H7L6 8z"/>
              <path d="M9 8V6a3 3 0 0 1 6 0v2"/>
            </svg>
            Order History &amp; Custom Sets
          </a>
          <a href="account_settings.php" class="popup-item" id="popup-settings">
            <!-- gear icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="#A9275E" stroke-width="1.8">
              <circle cx="12" cy="12" r="3"/>
              <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
            Account Settings
          </a>
        </div>

        <div class="popup-footer">
          <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=logout" class="popup-item" id="popup-logout">
            <!-- sign out icon -->
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

<!-- ==================== HERO ==================== -->
<header class="hero" id="gallery-hero">
  <h1>The Gallery &amp; Shop</h1>
  <p>Artistry you can wear. Browse and purchase our signature handcrafted press-on sets.</p>
</header>

<!-- ==================== CATEGORY PILLS + FILTER BUTTON ==================== -->
<div class="container">
  <div class="toolbar">
    <!-- Quick category pills -->
    <div class="pills">
      <a class="pill <?php echo (count($selected_categories) === 0) ? 'active' : ''; ?>"
         href="index.php?page=gallery" id="pill-all">All</a>

      <?php foreach ($categories as $cat): ?>
        <?php $isSelected = in_array((int)$cat['category_id'], $selected_categories, true); ?>
        <a class="pill <?php echo $isSelected ? 'active' : ''; ?>"
           href="index.php?page=gallery&category[]=<?php echo (int)$cat['category_id']; ?>">
          <?php echo htmlspecialchars($cat['category_name']); ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Filter modal toggle button -->
    <button type="button" class="filter-btn" id="filterBtn" onclick="openFilters()">
      <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/gallery/icon-25.svg" alt="Filter">
      <span>Filter<?php echo (count($selected_categories) > 0) ? ' (' . count($selected_categories) . ')' : ''; ?></span>
    </button>
  </div>
</div>

<!-- ==================== PRODUCT GRID ==================== -->
<main class="container product-section" id="product-section">
  <div class="product-grid">
    <?php foreach ($products as $product): ?>
      <article class="product-card" id="product-<?php echo (int)$product['product_id']; ?>">
        <a href="index.php?page=product&id=<?php echo (int)$product['product_id']; ?>" class="card-img-link">
          <div class="card-img">
            <img src="<?php echo htmlspecialchars(galleryImgUrl($product['image'])); ?>"
                 alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                 loading="lazy">
          </div>
        </a>
        <h3 class="card-title">
          <a href="index.php?page=product&id=<?php echo (int)$product['product_id']; ?>"><?php echo htmlspecialchars($product['product_name']); ?></a>
        </h3>
        <p class="card-price">Rs. <?php echo number_format((float)$product['price'], 0); ?></p>
        <div class="card-actions">
          <a class="btn-buy" href="index.php?page=product&id=<?php echo (int)$product['product_id']; ?>">Buy Now</a>
          <a class="btn-cart" href="index.php?page=cart&action=add&product_id=<?php echo (int)$product['product_id']; ?>">Add to Cart</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

  <?php if (empty($products)): ?>
    <p class="empty-note">No products found for this selection.</p>
  <?php endif; ?>
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

<!-- ==================== FILTER POP-UP MODAL ==================== -->
<div class="filter-overlay" id="filterOverlay" onclick="closeOnOverlay(event)">
  <div class="filter-modal">
    <div class="filter-header">
      <img class="filter-icon" src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/gallery/icon-25.svg" alt="">
      <h2 class="filter-title">Filters</h2>
      <button type="button" class="filter-close" onclick="closeFilters()">&times;</button>
    </div>

    <form method="GET" action="index.php">
      <input type="hidden" name="page" value="gallery">
      <p class="filter-label">CATEGORY</p>
      <?php foreach ($categories as $cat): ?>
        <?php $isSelected = in_array((int)$cat['category_id'], $selected_categories, true); ?>
        <label class="filter-option">
          <input type="checkbox"
                 name="category[]"
                 value="<?php echo (int)$cat['category_id']; ?>"
                 <?php echo $isSelected ? 'checked' : ''; ?>>
          <span class="checkbox-box"></span>
          <span class="option-name"><?php echo htmlspecialchars($cat['category_name']); ?></span>
          <span class="option-count"><?php echo (int)$cat['product_count']; ?></span>
        </label>
      <?php endforeach; ?>

      <div class="filter-actions">
        <a class="clear-link" href="index.php?page=gallery">CLEAR ALL</a>
        <button type="submit" class="btn-apply">Apply Filters</button>
      </div>
    </form>
  </div>
</div>

<!-- ==================== JAVASCRIPT ==================== -->
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

  /* Filter Modal */
  function openFilters() {
    document.getElementById("filterOverlay").classList.add("show");
  }

  function closeFilters() {
    document.getElementById("filterOverlay").classList.remove("show");
  }

  function closeOnOverlay(event) {
    if (event.target.id === "filterOverlay") {
      closeFilters();
    }
  }

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeFilters();
  });
</script>

</body>
</html>
