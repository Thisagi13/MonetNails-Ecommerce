<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout — Monet Nails</title>
  <meta name="description" content="Complete your purchase with Monet Nails. Secure checkout with PayHere and Cash on Delivery.">

  <!-- Fonts: Bodoni Moda + Manrope -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@0,400..900;1,400..900&family=Manrope:wght@300..800&display=swap" rel="stylesheet">

  <!-- Header Stylesheet -->
  <link rel="stylesheet" href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/css/header.css">

  <!-- Checkout Stylesheet -->
  <link rel="stylesheet" href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/css/checkout.css">

  <!-- PayHere JavaScript SDK -->
  <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>
</head>
<body class="checkout-page">

  <!-- ==================== SITE HEADER ==================== -->
  <?php
  $activeNav = 'checkout';
  require __DIR__ . '/../../includes/header.php';
  ?>

  <!-- ==================== MAIN CHECKOUT ==================== -->
  <main class="checkout-main-wrap">
    <div class="checkout-container">

      <div class="checkout-heading">
        <h1>Checkout</h1>
        <p>Complete your delivery and payment details below. Your bespoke nail sets are reserved for you.</p>
      </div>

      <!-- Sign In Requirement Notice for Unsigned Users -->
      <?php if (!$isLoggedIn): ?>
        <div class="signin-prompt-card" id="signinPrompt">
          <div class="prompt-text">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <div>
              <strong>Already have an account, or need to place this order?</strong>
              <p style="margin:2px 0 0;font-size:13px;color:var(--muted);">
                You are currently viewing checkout as a guest. Please sign in or create an account to finalize your order.
              </p>
            </div>
          </div>
          <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin&redirect=checkout" class="signin-prompt-btn" id="btn-signin-notice">
            Sign In First
          </a>
        </div>
      <?php endif; ?>

      <!-- Errors Banner -->
      <?php if (!empty($errors)): ?>
        <div class="alert-error" id="checkoutErrors">
          <strong>Please resolve the following before placing your order:</strong>
          <ul>
            <?php foreach ($errors as $err): ?>
              <li><?php echo htmlspecialchars($err); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- Checkout Form -->
      <form method="POST" action="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=checkout" id="checkoutForm" class="checkout-columns">

        <!-- Left Column: Shipping & Delivery -->
        <div class="checkout-form-col">
          <div class="checkout-card">
            <h2>Shipping &amp; Delivery</h2>

            <div class="fields-grid">
              <div class="form-row-2">
                <div class="form-group">
                  <label for="first_name">First Name *</label>
                  <input type="text" id="first_name" name="first_name" required
                         value="<?php echo htmlspecialchars($ship['first_name']); ?>"
                         placeholder="e.g. Jane">
                </div>
                <div class="form-group">
                  <label for="last_name">Last Name *</label>
                  <input type="text" id="last_name" name="last_name" required
                         value="<?php echo htmlspecialchars($ship['last_name']); ?>"
                         placeholder="e.g. Doe">
                </div>
              </div>

              <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required
                       value="<?php echo htmlspecialchars($ship['email']); ?>"
                       placeholder="e.g. jane.doe@example.com">
              </div>

              <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" required
                       value="<?php echo htmlspecialchars($ship['phone']); ?>"
                       placeholder="e.g. +94 77 123 4567">
              </div>

              <div class="form-group">
                <label for="street_address">Street Address *</label>
                <input type="text" id="street_address" name="street_address" required
                       value="<?php echo htmlspecialchars($ship['street_address']); ?>"
                       placeholder="e.g. No 90/1, 4th Lane, Werellawatta">
              </div>

              <div class="form-row-2">
                <div class="form-group">
                  <label for="city">City *</label>
                  <input type="text" id="city" name="city" required
                         value="<?php echo htmlspecialchars($ship['city']); ?>"
                         placeholder="e.g. Yakkala">
                </div>
                <div class="form-group">
                  <label for="postal_code">Postal Code *</label>
                  <input type="text" id="postal_code" name="postal_code" required
                         value="<?php echo htmlspecialchars($ship['postal_code']); ?>"
                         placeholder="e.g. 11870">
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Options Card -->
          <div class="checkout-card">
            <h2>Payment Method</h2>

            <div class="payment-methods">
              <!-- PayHere Online -->
              <label class="payment-method-option <?php echo ($ship['payment_method'] === 'payhere') ? 'selected' : ''; ?>" id="opt-payhere">
                <div class="payment-method-left">
                  <input type="radio" name="payment_method" value="payhere" <?php echo ($ship['payment_method'] === 'payhere') ? 'checked' : ''; ?>>
                  <div>
                    <div class="payment-method-title">PayHere Online Gateway</div>
                    <div class="payment-method-desc">Pay securely via Visa, MasterCard, Amex, Genie, eZ Cash, mCash</div>
                  </div>
                </div>
                <div class="payment-badges">
                  <span class="payhere-badge">PAYHERE</span>
                </div>
              </label>

              <!-- Cash on Delivery -->
              <label class="payment-method-option <?php echo ($ship['payment_method'] === 'cod') ? 'selected' : ''; ?>" id="opt-cod">
                <div class="payment-method-left">
                  <input type="radio" name="payment_method" value="cod" <?php echo ($ship['payment_method'] === 'cod') ? 'checked' : ''; ?>>
                  <div>
                    <div class="payment-method-title">Cash on Delivery (COD)</div>
                    <div class="payment-method-desc">Pay with cash upon package arrival</div>
                  </div>
                </div>
              </label>
            </div>
          </div>
        </div>

        <!-- Right Column: Order Summary -->
        <div class="checkout-summary-col">
          <div class="checkout-card">
            <div class="summary-head">
              <h2>Order Summary</h2>
              <span class="items-count"><?php echo (int)$itemCount; ?> SETS</span>
            </div>

            <?php if (empty($cart)): ?>
              <p style="font-size:14px;color:var(--muted);padding:16px 0;">Your cart is currently empty.</p>
            <?php else: ?>
              <div class="order-items-list">
                <?php foreach ($cart as $item): ?>
                  <div class="order-item-row">
                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>"
                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                         class="order-item-img">
                    <div class="order-item-info">
                      <div class="order-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                      <?php if (!empty($item['variant'])): ?>
                        <div class="order-item-meta"><?php echo htmlspecialchars($item['variant']); ?></div>
                      <?php endif; ?>
                      <div class="order-item-meta">Qty: <?php echo (int)$item['quantity']; ?></div>
                    </div>
                    <div class="order-item-price">Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <div class="summary-divider"></div>

            <div class="totals-group">
              <div class="totals-row">
                <span class="label">Selected Items Subtotal</span>
                <span class="val">Rs. <?php echo number_format($subtotal, 2); ?></span>
              </div>
              <div class="totals-row">
                <span class="label">Shipping Fee</span>
                <span class="val">Rs. <?php echo number_format($shippingFee, 2); ?></span>
              </div>
            </div>

            <div class="summary-divider"></div>

            <div class="total-due-row">
              <span class="label">Total Due</span>
              <span class="total-amount">Rs. <?php echo number_format($total, 2); ?></span>
            </div>

            <?php if (!empty($cart)): ?>
              <?php if ($isLoggedIn): ?>
                <button type="submit" class="btn-place-order" id="btnPlaceOrder">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                  </svg>
                  <span id="btnText">Place Order &amp; Pay</span>
                </button>
              <?php else: ?>
                <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin&redirect=checkout" class="btn-place-order" id="btnPlaceOrder" style="background:var(--dark);text-decoration:none;display:flex;align-items:center;justify-content:center;">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                    <polyline points="10 17 15 12 10 7"></polyline>
                    <line x1="15" y1="12" x2="3" y2="12"></line>
                  </svg>
                  <span>Sign In to Proceed Checkout</span>
                </a>
              <?php endif; ?>
            <?php endif; ?>

            <p class="checkout-security-note">
              &#128274; Secure 256-bit encrypted checkout &middot; Your payment and delivery information remain completely private.
            </p>
          </div>
        </div>

      </form>
    </div>
  </main>

  <!-- ==================== PAYHERE MODAL TRIGGER (IF ACTIVE) ==================== -->
  <?php if (!empty($payhereData)): ?>
    <?php $payhereEndpoint = !empty($payhereData['sandbox']) ? 'https://sandbox.payhere.lk/pay/checkout' : 'https://www.payhere.lk/pay/checkout'; ?>
    <div id="payhereRedirectOverlay" style="position:fixed;inset:0;background:rgba(28,27,27,0.75);backdrop-filter:blur(6px);z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#fff;">
      <div style="background:#fff;color:#1C1B1B;padding:40px 48px;border-radius:12px;text-align:center;box-shadow:0 12px 36px rgba(0,0,0,0.3);max-width:480px;width:90%;">
        <div style="width:48px;height:48px;border:3px solid #E8DDE0;border-top-color:#A9275E;border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto 20px;"></div>
        <h3 style="font-family:'Bodoni Moda',serif;font-size:24px;margin-bottom:8px;font-weight:600;">Connecting to PayHere...</h3>
        <p style="font-size:14px;color:#574147;margin-bottom:24px;line-height:1.5;">Preparing your secure payment session for Order #<?php echo htmlspecialchars($payhereData['order_id']); ?>.</p>
        <form id="payhereDirectForm" method="post" action="<?php echo $payhereEndpoint; ?>">
          <?php foreach ($payhereData as $param => $val): ?>
            <input type="hidden" name="<?php echo htmlspecialchars($param); ?>" value="<?php echo htmlspecialchars((string)$val); ?>">
          <?php endforeach; ?>
          <button type="submit" class="btn-place-order" style="margin-top:0;width:100%;font-size:13px;padding:12px 16px;">
            Click Here If PayHere Window Does Not Open
          </button>
        </form>
      </div>
    </div>
    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var payment = {
          sandbox: <?php echo $payhereData['sandbox'] ? 'true' : 'false'; ?>,
          merchant_id: "<?php echo htmlspecialchars($payhereData['merchant_id']); ?>",
          return_url: "<?php echo htmlspecialchars($payhereData['return_url']); ?>",
          cancel_url: "<?php echo htmlspecialchars($payhereData['cancel_url']); ?>",
          notify_url: "<?php echo htmlspecialchars($payhereData['notify_url']); ?>",
          order_id: "<?php echo htmlspecialchars($payhereData['order_id']); ?>",
          items: "<?php echo htmlspecialchars($payhereData['items']); ?>",
          amount: "<?php echo htmlspecialchars($payhereData['amount']); ?>",
          currency: "<?php echo htmlspecialchars($payhereData['currency']); ?>",
          hash: "<?php echo htmlspecialchars($payhereData['hash']); ?>",
          first_name: "<?php echo htmlspecialchars($payhereData['first_name']); ?>",
          last_name: "<?php echo htmlspecialchars($payhereData['last_name']); ?>",
          email: "<?php echo htmlspecialchars($payhereData['email']); ?>",
          phone: "<?php echo htmlspecialchars($payhereData['phone']); ?>",
          address: "<?php echo htmlspecialchars($payhereData['address']); ?>",
          city: "<?php echo htmlspecialchars($payhereData['city']); ?>",
          country: "<?php echo htmlspecialchars($payhereData['country']); ?>"
        };

        if (typeof payhere !== 'undefined') {
          payhere.onCompleted = function onCompleted(orderId) {
            window.location.href = "<?php echo $payhereData['return_url']; ?>";
          };

          payhere.onDismissed = function onDismissed() {
            var overlay = document.getElementById('payhereRedirectOverlay');
            if (overlay) overlay.style.display = 'none';
          };

          payhere.onError = function onError(error) {
            console.error("PayHere Error:", error);
            document.getElementById('payhereDirectForm').submit();
          };

          // Launch PayHere popup
          payhere.startPayment(payment);
        } else {
          // If JS SDK is blocked, submit form directly
          document.getElementById('payhereDirectForm').submit();
        }
      });
    </script>
  <?php endif; ?>

  <script>
    // Radio toggle styling
    document.querySelectorAll('.payment-method-option input[type="radio"]').forEach(function (radio) {
      radio.addEventListener('change', function () {
        document.querySelectorAll('.payment-method-option').forEach(function (el) {
          el.classList.remove('selected');
        });
        if (this.checked) {
          this.closest('.payment-method-option').classList.add('selected');
          var btnText = document.getElementById('btnText');
          if (btnText) {
            if (this.value === 'cod') {
              btnText.textContent = 'Place Order (Cash on Delivery)';
            } else {
              btnText.textContent = 'Place Order & Pay with PayHere';
            }
          }
        }
      });
    });
  </script>

  <!-- ==================== SITE FOOTER ==================== -->
  <?php require __DIR__ . '/../../includes/footer.php'; ?>


</body>
</html>
