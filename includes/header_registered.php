<?php
/**
 * includes/header_registered.php — Registered Customer Header
 * Monet Nails E-Commerce Application
 *
 * Renders the top navigation header for logged-in / registered customers.
 * Includes cart icon with live badge, profile avatar, and dropdown popup.
 */

$base_url  = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';
$activeNav = $activeNav ?? ($currentPage ?? ($_GET['page'] ?? 'home'));

// Ensure avatar html is available
if (empty($avatar_html)) {
    $initials = strtoupper(substr($customer_name ?? 'Client', 0, 1));
    if ($initials === '') {
        $initials = 'M';
    }
    $avatar_html = '<span class="avatar-initials">' . htmlspecialchars($initials) . '</span>';
}
?>
<!-- Stylesheet for Header if not already loaded in <head> -->
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/header.css">

<!-- Ensure profile button is always interactive and above layers -->
<style>
  .top-nav { position: sticky !important; top: 0 !important; z-index: 200 !important; }
  .top-nav .nav-actions { position: relative !important; }
  .top-nav .profile-btn {
    cursor: pointer !important;
    z-index: 205 !important;
    position: relative !important;
    pointer-events: auto !important;
  }
  .top-nav .profile-btn * { pointer-events: none !important; }
  .profile-popup { position: absolute !important; z-index: 300 !important; }
  .popup-overlay { position: fixed !important; inset: 0 !important; z-index: 90 !important; }
  .popup-item svg, .popup-item img { pointer-events: none !important; }
</style>

<!-- ============ TOP NAV (Registered Customer) ============ -->
<nav class="top-nav" id="top-nav">
  <div class="nav-inner">
    <a href="<?php echo $base_url; ?>/" id="nav-logo" title="Monet Nails Home">
      <img src="<?php echo $base_url; ?>/assets/images/home/node-210.png" alt="Monet Nails" class="logo-img">
    </a>

    <div class="nav-links">
      <a href="<?php echo $base_url; ?>/" class="<?php echo ($activeNav === 'home') ? 'active' : ''; ?>" id="nav-home">Home</a>
      <a href="<?php echo $base_url; ?>/index.php?page=about" class="<?php echo ($activeNav === 'about') ? 'active' : ''; ?>" id="nav-about">About</a>
      <a href="<?php echo $base_url; ?>/index.php?page=services" class="<?php echo ($activeNav === 'services') ? 'active' : ''; ?>" id="nav-services">Services</a>
      <a href="<?php echo $base_url; ?>/index.php?page=gallery" class="<?php echo ($activeNav === 'gallery') ? 'active' : ''; ?>" id="nav-gallery">Gallery</a>
      <a href="<?php echo $base_url; ?>/index.php?page=contact" class="<?php echo ($activeNav === 'contact') ? 'active' : ''; ?>" id="nav-contact">Contact</a>
    </div>

    <div class="nav-actions">
      <!-- Cart Icon with badge -->
      <a href="<?php echo $base_url; ?>/index.php?page=cart" class="nav-cart-btn <?php echo ($activeNav === 'cart') ? 'active' : ''; ?>" title="Cart" id="nav-cart-icon">
        <img src="<?php echo $base_url; ?>/assets/images/home_registered/vector-226.svg" alt="Cart">
        <?php if (!empty($headerCartCount) && $headerCartCount > 0): ?>
          <span class="cart-badge"><?php echo (int)$headerCartCount; ?></span>
        <?php endif; ?>
      </a>

      <!-- Profile Avatar Button -->
      <div class="profile-btn" id="profileBtn" title="Profile" role="button" aria-haspopup="true" aria-expanded="false" tabindex="0">
        <?php echo $avatar_html; ?>
      </div>

      <!-- Profile Dropdown Popup -->
      <div class="profile-popup" id="profilePopup">
        <div class="popup-header">
          <div class="popup-avatar">
            <?php echo $avatar_html; ?>
          </div>
          <div>
            <div class="popup-name"><?php echo htmlspecialchars($customer_name ?? 'Client'); ?></div>
            <div class="popup-email"><?php echo htmlspecialchars($customer_email ?? ''); ?></div>
          </div>
        </div>

        <div class="popup-menu">
          <a href="<?php echo $base_url; ?>/index.php?page=my_appointments" class="popup-item <?php echo ($activeNav === 'my_appointments') ? 'active' : ''; ?>" id="popup-appointments">
            <!-- calendar icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="#A9275E" stroke-width="1.8">
              <rect x="3" y="5" width="18" height="16" rx="2"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
              <line x1="8" y1="3" x2="8" y2="7"/>
              <line x1="16" y1="3" x2="16" y2="7"/>
            </svg>
            My Appointments
          </a>
          <a href="<?php echo $base_url; ?>/index.php?page=order_history" class="popup-item" id="popup-orders">
            <!-- shopping bag icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="#A9275E" stroke-width="1.8">
              <path d="M6 8h12l-1 12H7L6 8z"/>
              <path d="M9 8V6a3 3 0 0 1 6 0v2"/>
            </svg>
            Order History &amp; Custom Sets
          </a>
          <a href="<?php echo $base_url; ?>/index.php?page=account_settings" class="popup-item <?php echo ($activeNav === 'account_settings') ? 'active' : ''; ?>" id="popup-settings">
            <!-- gear icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="#A9275E" stroke-width="1.8">
              <circle cx="12" cy="12" r="3"/>
              <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
            Account Settings
          </a>
        </div>

        <div class="popup-footer">
          <a href="<?php echo $base_url; ?>/index.php?page=logout" class="popup-item" id="popup-logout">
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
<div class="popup-overlay" id="popupOverlay" onclick="window.closeMonetProfilePopup()"></div>

<script>
  /* Robust global profile popup functions */
  window.toggleMonetProfilePopup = function (e) {
    if (e) {
      if (typeof e.stopPropagation === 'function') e.stopPropagation();
    }
    var btn     = document.getElementById('profileBtn');
    var popup   = document.getElementById('profilePopup');
    var overlay = document.getElementById('popupOverlay');
    if (!popup) return;

    var isOpen = popup.classList.toggle('show');
    if (overlay) overlay.classList.toggle('show', isOpen);
    if (btn) btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  };

  window.closeMonetProfilePopup = function () {
    var btn     = document.getElementById('profileBtn');
    var popup   = document.getElementById('profilePopup');
    var overlay = document.getElementById('popupOverlay');
    if (popup)   popup.classList.remove('show');
    if (overlay) overlay.classList.remove('show');
    if (btn)     btn.setAttribute('aria-expanded', 'false');
  };

  (function () {
    if (window._monetProfilePopupInit) return;
    window._monetProfilePopupInit = true;

    function initPopup() {
      var btn   = document.getElementById('profileBtn');
      var popup = document.getElementById('profilePopup');

      /* Attach click directly on the button */
      if (btn) {
        btn.addEventListener('click', function (e) {
          e.stopPropagation();
          window.toggleMonetProfilePopup(e);
        });
        /* Keyboard: Enter or Space opens/closes the popup */
        btn.addEventListener('keydown', function (e) {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            window.toggleMonetProfilePopup(e);
          }
        });
      }

      if (popup) {
        popup.addEventListener('click', function (e) { e.stopPropagation(); });
      }

      document.addEventListener('click', function (e) {
        var p = document.getElementById('profilePopup');
        var b = document.getElementById('profileBtn');
        if (!p || !p.classList.contains('show')) return;
        if (b && (b === e.target || b.contains(e.target))) return;
        if (p.contains(e.target)) return;
        window.closeMonetProfilePopup();
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.closeMonetProfilePopup();
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initPopup);
    } else {
      initPopup();
    }
  })();
</script>
