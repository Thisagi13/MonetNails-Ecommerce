<?php
/**
 * views/product/unregistered.php
 * Product Details page — public (guest) view
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

<!-- ==================== TOP NAV ==================== -->
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
      <a href="index.php?page=cart" id="nav-cart">Cart</a>
      <a href="contact.php" id="nav-contact">Contact</a>
    </div>
    <div class="nav-actions">
      <a href="index.php?page=signup" class="signup-link" id="nav-signup">Sign up</a>
      <a href="index.php?page=signin" class="login-btn" id="nav-login">Login</a>
    </div>
  </div>
</nav>

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
var alreadySent = false;
function lockButtons() {
  if (alreadySent) return false;
  alreadySent = true;
  return true;
}
</script>
</body>
</html>
