<?php
/**
 * views/cart/registered.php — Shopping Cart (logged-in user view)
 */
function cartMoney($n): string {
    $n = (float)$n;
    return 'Rs.' . (fmod($n, 1.0) == 0 ? number_format($n, 0) : number_format($n, 2, '.', ''));
}
function cartItemImg($url): string {
    if (empty($url)) return '/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/gallery/signature-blossom.png';
    if (strpos($url, '/') === 0 || strpos($url, 'http') === 0) {
        return $url;
    }
    return '/MonetNails-Ecommerce2/MonetNails-Ecommerce/' . $url;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopping Cart | Monet Nails</title>
  <meta name="description" content="Review and manage your Monet Nails shopping cart before checkout.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400;0,6..96,500;1,6..96,400&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/css/gallery.css">
  <link rel="stylesheet" href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/css/cart.css">
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
      <a href="index.php?page=gallery" id="nav-gallery">Gallery</a>
      <a href="index.php?page=cart" class="active" id="nav-cart">Cart</a>
      <a href="contact.php" id="nav-contact">Contact</a>
    </div>
    <div class="nav-actions">
      <!-- Cart Icon with badge -->
      <a href="index.php?page=cart" class="nav-cart-btn" title="Cart" id="nav-cart-icon">
        <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home_registered/vector-226.svg" alt="Cart">
        <?php if ($data['itemCount'] > 0): ?>
          <span class="cart-badge"><?php echo $data['itemCount']; ?></span>
        <?php endif; ?>
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
<div class="popup-overlay" id="popupOverlay"></div>

<!-- ==================== BREADCRUMB ==================== -->
<div class="breadcrumb-bar">
  <nav class="breadcrumb-nav" aria-label="breadcrumb">
    <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/">HOME</a>
    <span class="bc-sep">
      <svg width="5" height="8" viewBox="0 0 5 8" fill="none"><path d="M3.07 4L0 .93.93 0l4 4-4 4L0 7.07 3.07 4Z" fill="#574147"/></svg>
    </span>
    <a href="index.php?page=gallery">GALLERY &amp; SHOP</a>
    <span class="bc-sep">
      <svg width="5" height="8" viewBox="0 0 5 8" fill="none"><path d="M3.07 4L0 .93.93 0l4 4-4 4L0 7.07 3.07 4Z" fill="#574147"/></svg>
    </span>
    <span class="bc-current">
      SHOPPING CART
      <?php if ($data['itemCount'] > 0): ?>
        <span class="bc-badge"><?php echo $data['itemCount']; ?></span>
      <?php endif; ?>
    </span>
  </nav>
</div>

<!-- ==================== MAIN CONTENT ==================== -->
<main class="cart-main" id="cart-main">

  <?php if (empty($data['cartItems'])): ?>
    <div class="cart-empty">
      <h2>Your Shopping Bag is Empty</h2>
      <p>Looks like you haven't added anything yet. Explore our gallery to find your perfect set.</p>
      <a href="index.php?page=gallery" class="btn-shop">Browse Gallery</a>
    </div>

  <?php else: ?>
    <div class="cart-title-row">
      <h1>Your Shopping Bag</h1>
      <span class="cart-count">(<?php echo $data['itemCount']; ?> BESPOKE SET<?php echo $data['itemCount'] !== 1 ? 'S' : ''; ?>)</span>
    </div>

    <div class="cart-grid">

      <!-- LEFT: Cart Items -->
      <section class="cart-items-col" id="cart-items-col">
        <?php foreach ($data['cartItems'] as $item): ?>
          <article class="cart-item" id="cart-item-<?php echo (int)$item['cart_id']; ?>" data-cart-id="<?php echo (int)$item['cart_id']; ?>">
            <input type="checkbox" class="cart-item-check" checked
                   aria-label="Select <?php echo htmlspecialchars($item['product_name']); ?>">

            <div class="cart-item-thumb">
              <img src="<?php echo htmlspecialchars(cartItemImg($item['image_url'])); ?>"
                   alt="<?php echo htmlspecialchars($item['product_name']); ?>">
            </div>

            <div class="cart-item-info">
              <div class="cart-name-row">
                <span class="cart-item-name"><?php echo htmlspecialchars($item['product_name']); ?></span>
                <span class="cart-item-price"><?php echo cartMoney((float)$item['price'] * (int)$item['quantity']); ?></span>
              </div>

              <?php if (!empty($item['shape']) || !empty($item['finish']) || !empty($item['size_preset']) || !empty($item['custom_sizing_code']) || !empty($item['special_features'])): ?>
              <div class="cart-item-tags">
                <?php if (!empty($item['shape'])): ?>
                  <span class="cart-tag">Shape: <?php echo htmlspecialchars($item['shape']); ?></span>
                <?php endif; ?>
                <?php if (!empty($item['finish'])): ?>
                  <span class="cart-tag">Finish: <?php echo htmlspecialchars($item['finish']); ?></span>
                <?php endif; ?>
                <?php if (!empty($item['size_preset'])): ?>
                  <span class="cart-tag">Size: <?php echo htmlspecialchars($item['size_preset']); ?></span>
                <?php endif; ?>
                <?php if (!empty($item['custom_sizing_code'])): ?>
                  <span class="cart-tag custom">Custom Sizing: <?php echo htmlspecialchars($item['custom_sizing_code']); ?></span>
                <?php endif; ?>
                <?php if (!empty($item['special_features'])): ?>
                  <span class="cart-tag custom"><?php echo htmlspecialchars($item['special_features']); ?></span>
                <?php endif; ?>
              </div>
              <?php endif; ?>

              <div class="cart-controls">
                <div class="qty-stepper" data-cart-id="<?php echo (int)$item['cart_id']; ?>">
                  <button type="button" class="qty-dec" aria-label="Decrease quantity">
                    <svg width="9" height="2" viewBox="0 0 9 2" fill="none"><path d="M0 1.17V0h8.17v1.17H0Z" fill="#1C1B1B"/></svg>
                  </button>
                  <span class="qty-val"><?php echo (int)$item['quantity']; ?></span>
                  <button type="button" class="qty-inc" aria-label="Increase quantity">
                    <svg width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M3.5 4.67H0V3.5h3.5V0h1.17v3.5h3.5v1.17h-3.5v3.5H3.5V4.67Z" fill="#1C1B1B"/></svg>
                  </button>
                </div>

                <div class="cart-actions-row">
                  <button type="button" class="cart-action-btn">
                    <svg width="14" height="8" viewBox="0 0 14 8" fill="none"><path d="M1.33 8C.97 8 .65 7.87.39 7.61.13 7.35 0 7.03 0 6.67V1.33C0 .97.13.65.39.39.65.13.97 0 1.33 0H12c.37 0 .68.13.94.39.26.26.39.58.39.94v5.34c0 .36-.13.68-.39.94-.26.26-.57.39-.94.39H1.33Zm0-1.33H12V1.33h-2v2.67H8.67V1.33H7.33v2.67H6V1.33H4.67v2.67H3.33V1.33h-2v5.34Z" fill="#574147"/></svg>
                    <span>Edit Sizing</span>
                  </button>
                  <span class="cart-action-sep">/</span>
                  <button type="button" class="cart-action-btn">
                    <svg width="10" height="12" viewBox="0 0 10 12" fill="none"><path d="M0 12V1.33C0 .97.13.65.39.39.65.13.97 0 1.33 0H8c.37 0 .68.13.94.39.26.26.39.58.39.94V12L4.67 10 0 12Zm1.33-2.03L4.67 8.53 8 9.97V1.33H1.33v8.64Z" fill="#574147"/></svg>
                    <span>Save for Later</span>
                  </button>
                  <span class="cart-action-sep">/</span>
                  <form method="POST" action="index.php?page=cart" style="display:inline;">
                    <input type="hidden" name="page" value="cart">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="cart_id" value="<?php echo (int)$item['cart_id']; ?>">
                    <button type="submit" class="cart-action-btn remove-btn">
                      <svg width="11" height="12" viewBox="0 0 11 12" fill="none"><path d="M2 12c-.37 0-.68-.13-.94-.39-.26-.26-.39-.57-.39-.94V2H0V.67h3.33V0h3.34v.67H10V2h-.67v8.67c0 .36-.13.67-.39.93-.26.27-.57.4-.94.4H2Zm6.67-10H2v8.67h6.67V2ZM3.33 9.33H4.6V3.33H3.33v6Zm2.34 0H7V3.33H5.67v6Z" fill="#574147"/></svg>
                      <span>Remove</span>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </section>

      <!-- RIGHT: Order Summary -->
      <aside class="cart-summary-col" id="order-summary-col">
        <div class="order-summary">
          <h2>Order Summary</h2>

          <div>
            <div class="summary-selected-label">Selected Items (<?php echo $data['itemCount']; ?>)</div>
            <div class="summary-item-list">
              <?php foreach ($data['cartItems'] as $it): ?>
                <div class="summary-item-row">
                  <span class="s-label"><?php echo htmlspecialchars($it['product_name']); ?></span>
                  <span class="s-val"><?php echo cartMoney((float)$it['price'] * (int)$it['quantity']); ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="summary-totals">
            <div class="s-row">
              <span class="s-label">Selected Items Subtotal</span>
              <span class="s-val"><?php echo cartMoney($data['subtotal']); ?></span>
            </div>
            <div class="s-row">
              <span class="s-label">Shipping Fee</span>
              <span class="s-val"><?php echo cartMoney($data['shippingFee']); ?></span>
            </div>
          </div>

          <div class="summary-grand">
            <div class="s-row">
              <span class="s-label">TOTAL DUE</span>
              <span class="grand-amount"><?php echo cartMoney($data['totalDue']); ?></span>
            </div>
            <div class="summary-note">LKR, VAT &amp; DUTIES INCLUDED</div>
          </div>

          <a href="checkout.php" class="checkout-btn" id="checkout-btn">
            <svg width="12" height="16" viewBox="0 0 12 16" fill="none"><path d="M1.5 15.75c-.41 0-.77-.15-1.06-.44C.15 15.02 0 14.66 0 14.25v-7.5c0-.41.15-.77.44-1.06.29-.29.65-.44 1.06-.44h.75V3.75C2.25 2.71 2.62 1.83 3.35 1.1 4.08.37 4.96 0 6 0s1.92.37 2.65 1.1c.73.73 1.1 1.61 1.1 2.65v1.5h.75c.41 0 .77.15 1.06.44.29.29.44.65.44 1.06v7.5c0 .41-.15.77-.44 1.06-.29.29-.65.44-1.06.44h-9Zm0-1.5h9v-7.5h-9v7.5ZM6 12c.41 0 .77-.15 1.06-.44.29-.29.44-.65.44-1.06 0-.41-.15-.77-.44-1.06A1.45 1.45 0 0 0 6 9c-.41 0-.77.15-1.06.44-.29.29-.44.65-.44 1.06 0 .41.15.77.44 1.06.29.29.65.44 1.06.44ZM3.75 5.25h4.5v-1.5c0-.625-.22-1.156-.66-1.594C7.16 1.72 6.63 1.5 6 1.5s-1.16.22-1.59.66c-.44.44-.66.97-.66 1.59v1.5Z" fill="#fff"/></svg>
            <span>PROCEED TO CHECKOUT</span>
          </a>
        </div>
      </aside>

    </div>
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

<script>
/* Profile popup */
(function() {
  var btn     = document.getElementById('profileBtn');
  var popup   = document.getElementById('profilePopup');
  var overlay = document.getElementById('popupOverlay');
  function togglePopup(e) {
    e.stopPropagation();
    var isOpen = popup.classList.toggle('show');
    overlay.classList.toggle('show', isOpen);
  }
  function closePopup() {
    popup.classList.remove('show');
    overlay.classList.remove('show');
  }
  if (btn && popup) {
    btn.addEventListener('click', togglePopup);
    popup.addEventListener('click', function(e) { e.stopPropagation(); });
    overlay.addEventListener('click', closePopup);
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closePopup(); });
  }
})();

/* Qty steppers */
document.querySelectorAll('.qty-stepper').forEach(function(stepper) {
  var qtyEl = stepper.querySelector('.qty-val');
  var dec   = stepper.querySelector('.qty-dec');
  var inc   = stepper.querySelector('.qty-inc');
  dec.addEventListener('click', function() {
    var q = parseInt(qtyEl.textContent, 10);
    if (q > 1) qtyEl.textContent = q - 1;
  });
  inc.addEventListener('click', function() {
    qtyEl.textContent = parseInt(qtyEl.textContent, 10) + 1;
  });
});
</script>
</body>
</html>
