<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Confirmed — Monet Nails</title>
  <meta name="description" content="Thank you for your order at Monet Nails. Your handcrafted manicure sets are on the way.">

  <!-- Fonts: Bodoni Moda + Manrope -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@0,400..900;1,400..900&family=Manrope:wght@300..800&display=swap" rel="stylesheet">

  <!-- Header Stylesheet -->
  <link rel="stylesheet" href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/css/header.css">

  <!-- Checkout Stylesheet -->
  <link rel="stylesheet" href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/css/checkout.css">
</head>
<body class="checkout-page">

  <!-- Header -->
  <?php
  $activeNav = 'checkout';
  require __DIR__ . '/../../includes/header.php';
  ?>

  <!-- Success Content -->
  <main class="checkout-main-wrap">
    <div class="checkout-container">
      <div class="checkout-card success-card">
        <div class="success-icon-wrap">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>

        <h1>Thank You for Your Order!</h1>
        <p class="success-order-num">
          Order #<?php echo htmlspecialchars((string)($order['order_id'] ?? 'Confirmed')); ?>
        </p>
        <p style="font-size:15px;color:var(--muted);max-width:480px;margin:0 auto 20px;line-height:1.6;">
          Your order has been placed successfully. We are preparing your bespoke manicure set with artisanal care.
        </p>

        <?php if (!empty($order)): ?>
          <div style="background:var(--boxgrey);border-radius:10px;padding:20px;text-align:left;margin:24px 0;font-size:14px;">
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
              <span style="color:var(--muted);">Delivery Recipient:</span>
              <strong><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
              <span style="color:var(--muted);">Destination:</span>
              <span><?php echo htmlspecialchars($order['street_address'] . ', ' . $order['city']); ?></span>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
              <span style="color:var(--muted);">Payment Method:</span>
              <strong><?php echo htmlspecialchars($order['payment_method']); ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
              <span style="color:var(--muted);">Order Status:</span>
              <strong><span style="padding:3px 10px;border-radius:12px;background:#EFF7F2;color:#42806A;font-size:12px;font-weight:600;"><?php echo htmlspecialchars(ucfirst($order['status'] ?? 'Pending')); ?></span></strong>
            </div>
            <?php if (!empty($order['tracking_number'])): ?>
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
              <span style="color:var(--muted);">Tracking Reference:</span>
              <strong style="color:#BE536D;font-family:monospace;"><?php echo htmlspecialchars($order['tracking_number']); ?></strong>
            </div>
            <?php endif; ?>
            <div style="display:flex;justify-content:space-between;border-top:1px solid var(--border);padding-top:10px;margin-top:10px;">
              <span style="color:var(--muted);">Total Amount:</span>
              <strong style="color:var(--pink);font-size:16px;">Rs. <?php echo number_format((float)$order['total_amount'], 2); ?></strong>
            </div>
          </div>
        <?php endif; ?>

        <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=gallery" class="btn-back-home">
          Continue Shopping
        </a>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="site-footer">
    <div class="checkout-container footer-inner">
      <div class="footer-brand">
        <div class="footer-brand-title">Monet Nails</div>
        <p class="footer-tagline">Artistry in every touch. Elevating handcrafted nail wellness to a fine art.</p>
        <p class="footer-copy">&copy; <?php echo date('Y'); ?> Monet Nails. All rights reserved.</p>
      </div>
      <div class="footer-col">
        <h3>Explore</h3>
        <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/">Home</a>
        <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=gallery">Gallery</a>
        <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=cart">Shopping Cart</a>
      </div>
      <div class="footer-col">
        <h3>Customer Care</h3>
        <p>No 90/1, 4th Lane, Werellawatta, Yakkala</p>
        <p>+94 77 210 5954</p>
        <p>hello@monetnails.com</p>
      </div>
    </div>
  </footer>

</body>
</html>
