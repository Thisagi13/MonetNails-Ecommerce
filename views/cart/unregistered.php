<?php
/**
 * views/cart/unregistered.php — Shopping Cart (guest view)
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

<!-- ==================== TOP NAV ==================== -->
<?php
$activeNav = 'cart';
require __DIR__ . '/../../includes/header.php';
?>

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
    <!-- Empty cart state -->
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
          <article class="cart-item" id="cart-item-<?php echo (int)$item['cart_id']; ?>" data-cart-id="<?php echo (int)$item['cart_id']; ?>" data-price="<?php echo (float)$item['price']; ?>">
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

              <!-- Attribute tags -->
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

              <!-- Controls -->
              <div class="cart-controls">
                <div class="qty-stepper" data-cart-id="<?php echo (int)$item['cart_id']; ?>" data-price="<?php echo (float)$item['price']; ?>">
                  <button type="button" class="qty-dec" aria-label="Decrease quantity">
                    <svg width="9" height="2" viewBox="0 0 9 2" fill="none"><path d="M0 1.17V0h8.17v1.17H0Z" fill="#1C1B1B"/></svg>
                  </button>
                  <span class="qty-val"><?php echo (int)$item['quantity']; ?></span>
                  <button type="button" class="qty-inc" aria-label="Increase quantity">
                    <svg width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M3.5 4.67H0V3.5h3.5V0h1.17v3.5h3.5v1.17h-3.5v3.5H3.5V4.67Z" fill="#1C1B1B"/></svg>
                  </button>
                </div>

                <div class="cart-actions-row">
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
                <div class="summary-item-row" data-summary-cart-id="<?php echo (int)$it['cart_id']; ?>" data-price="<?php echo (float)$it['price']; ?>">
                  <span class="s-label"><?php echo htmlspecialchars($it['product_name']); ?></span>
                  <span class="s-val"><?php echo cartMoney((float)$it['price'] * (int)$it['quantity']); ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="summary-totals">
            <div class="s-row">
              <span class="s-label">Selected Items Subtotal</span>
              <span class="s-val" id="summary-subtotal"><?php echo cartMoney($data['subtotal']); ?></span>
            </div>
            <div class="s-row">
              <span class="s-label">Shipping Fee</span>
              <span class="s-val" id="summary-shipping"><?php echo cartMoney($data['shippingFee']); ?></span>
            </div>
          </div>

          <div class="summary-grand">
            <div class="s-row">
              <span class="s-label">TOTAL DUE</span>
              <span class="grand-amount" id="summary-total"><?php echo cartMoney($data['totalDue']); ?></span>
            </div>
            <div class="summary-note">LKR, VAT &amp; DUTIES INCLUDED</div>
          </div>

          <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=checkout" class="checkout-btn" id="checkout-btn">
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
      <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=about">About Us</a>
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
/* Qty steppers — dynamic price calculation and persistent DB update */
(function() {
  var updateTimers = {};

  function formatMoney(n) {
    n = Number(n) || 0;
    if (n % 1 === 0) {
      return 'Rs.' + n.toLocaleString('en-US');
    }
    return 'Rs.' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function recalculateTotals() {
    var subtotal = 0;
    var count = 0;
    document.querySelectorAll('.cart-item').forEach(function(item) {
      var chk = item.querySelector('.cart-item-check');
      if (chk && !chk.checked) return;
      var price = parseFloat(item.getAttribute('data-price')) || 0;
      var qtyEl = item.querySelector('.qty-val');
      var qty = qtyEl ? (parseInt(qtyEl.textContent, 10) || 1) : 1;
      subtotal += price * qty;
      count++;
    });

    var shipping = subtotal > 0 ? 150 : 0;
    var totalDue = subtotal + shipping;

    var countLabel = document.querySelector('.summary-selected-label');
    if (countLabel) countLabel.textContent = 'Selected Items (' + count + ')';

    var subtotalEl = document.getElementById('summary-subtotal');
    if (subtotalEl) subtotalEl.textContent = formatMoney(subtotal);

    var shippingEl = document.getElementById('summary-shipping');
    if (shippingEl) shippingEl.textContent = formatMoney(shipping);

    var totalEl = document.getElementById('summary-total');
    if (totalEl) totalEl.textContent = formatMoney(totalDue);
  }

  function updateQuantity(stepper, delta) {
    var qtyEl = stepper.querySelector('.qty-val');
    var cartId = stepper.getAttribute('data-cart-id');
    var currentQty = parseInt(qtyEl.textContent, 10) || 1;
    var newQty = currentQty + delta;
    if (newQty < 1) return;

    // 1. Immediately update quantity display
    qtyEl.textContent = newQty;

    // 2. Immediately update item price on the card
    var article = stepper.closest('.cart-item');
    var unitPrice = parseFloat((article && article.getAttribute('data-price')) || stepper.getAttribute('data-price') || 0);
    var itemTotal = unitPrice * newQty;

    if (article) {
      var priceEl = article.querySelector('.cart-item-price');
      if (priceEl) priceEl.textContent = formatMoney(itemTotal);
    }

    // 3. Immediately update order summary line for this item
    var summaryRow = document.querySelector('.summary-item-row[data-summary-cart-id="' + cartId + '"]');
    if (summaryRow) {
      var summaryVal = summaryRow.querySelector('.s-val');
      if (summaryVal) summaryVal.textContent = formatMoney(itemTotal);
    }

    // 4. Immediately recalculate subtotal & total due
    recalculateTotals();

    // 5. Debounce the AJAX call to persist to server / database
    if (updateTimers[cartId]) {
      clearTimeout(updateTimers[cartId]);
    }

    updateTimers[cartId] = setTimeout(function() {
      var body = new URLSearchParams();
      body.append('action', 'update_qty');
      body.append('cart_id', cartId);
      body.append('quantity', newQty);
      body.append('ajax', '1');

      fetch('index.php?page=cart', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: body.toString()
      })
      .then(function(res) {
        return res.json();
      })
      .then(function(resData) {
        if (resData && resData.success) {
          if (article) {
            var pEl = article.querySelector('.cart-item-price');
            if (pEl && resData.item_total_formatted) pEl.textContent = resData.item_total_formatted;
          }
          if (summaryRow) {
            var sVal = summaryRow.querySelector('.s-val');
            if (sVal && resData.item_total_formatted) sVal.textContent = resData.item_total_formatted;
          }
          var subEl = document.getElementById('summary-subtotal');
          if (subEl && resData.subtotal_formatted) subEl.textContent = resData.subtotal_formatted;

          var totEl = document.getElementById('summary-total');
          if (totEl && resData.total_due_formatted) totEl.textContent = resData.total_due_formatted;
        }
      })
      .catch(function(err) {
        console.error('Error persisting cart quantity:', err);
      });
    }, 200);
  }

  document.querySelectorAll('.qty-stepper').forEach(function(stepper) {
    var dec = stepper.querySelector('.qty-dec');
    var inc = stepper.querySelector('.qty-inc');

    if (dec) {
      dec.addEventListener('click', function(e) {
        e.preventDefault();
        updateQuantity(stepper, -1);
      });
    }

    if (inc) {
      inc.addEventListener('click', function(e) {
        e.preventDefault();
        updateQuantity(stepper, 1);
      });
    }
  });

  // Checkbox toggle recalculation
  document.querySelectorAll('.cart-item-check').forEach(function(chk) {
    chk.addEventListener('change', function() {
      var article = chk.closest('.cart-item');
      var cartId = article ? article.getAttribute('data-cart-id') : null;
      var summaryRow = cartId ? document.querySelector('.summary-item-row[data-summary-cart-id="' + cartId + '"]') : null;
      if (summaryRow) {
        summaryRow.style.display = chk.checked ? '' : 'none';
      }
      recalculateTotals();
    });
  });
})();
</script>
</body>
</html>
