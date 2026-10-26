<?php
/**
 * includes/header_unregistered.php — Unregistered Customer Header
 * Monet Nails E-Commerce Application
 *
 * Renders the top navigation header for guests / unregistered users.
 */

$base_url  = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';
$activeNav = $activeNav ?? ($currentPage ?? ($_GET['page'] ?? 'home'));
?>
<!-- Stylesheet for Header if not already loaded in <head> -->
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/header.css">

<!-- ============ TOP NAV (Unregistered Guest) ============ -->
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
      <a href="<?php echo $base_url; ?>/index.php?page=cart" class="<?php echo ($activeNav === 'cart') ? 'active' : ''; ?>" id="nav-cart" style="position:relative;">
        Cart
        <?php if (!empty($headerCartCount) && $headerCartCount > 0): ?>
          <span class="cart-badge" style="top:-6px; right:-18px;"><?php echo (int)$headerCartCount; ?></span>
        <?php endif; ?>
      </a>
      <a href="<?php echo $base_url; ?>/index.php?page=contact" class="<?php echo ($activeNav === 'contact') ? 'active' : ''; ?>" id="nav-contact">Contact</a>
    </div>

    <div class="nav-actions">
      <a href="<?php echo $base_url; ?>/index.php?page=signup" class="signup-link" id="nav-signup">Sign up</a>
      <a href="<?php echo $base_url; ?>/index.php?page=signin" class="login-btn" id="nav-login">Login</a>
    </div>
  </div>
</nav>
