<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — Monet Nails</title>
  <meta name="description" content="Sign in to your Monet Nails account to book appointments and manage your orders.">

  <!-- Google Fonts: Bodoni Moda + Manrope -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@0,400..900;1,400..900&family=Manrope:wght@300..800&display=swap" rel="stylesheet">

  <!-- Auth stylesheet (MVC: assets/css/auth.css) -->
  <link rel="stylesheet" href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/css/auth.css">
</head>
<body>
<div class="page">

  <!-- Decorative brand illustrations -->
  <div class="brand-art left">
    <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/signin/node-3.png" alt="">
  </div>
  <div class="brand-art right">
    <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/signin/node-4.png" alt="">
  </div>

  <!-- Top nav -->
  <div class="topbar" id="signin-topbar">
    <div class="topbar-inner">
      <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/" class="brand" id="signin-logo">Monet Nails</a>
      <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/" class="topbar-back" id="signin-back">Return Back</a>
    </div>
  </div>

  <!-- Main -->
  <div class="main-wrap">
    <div class="main">

      <!-- Heading -->
      <div>
        <h1 class="heading-title">Welcome Back</h1>
        <p class="heading-sub">Sign in to book your next appointment.</p>
      </div>

      <!-- Role toggle: Customer / Admin -->
      <div class="role-toggle" id="roleToggle">
        <button type="button" data-role="customer" class="active" id="role-customer-btn">Customer</button>
        <button type="button" data-role="admin" id="role-admin-btn">Admin</button>
      </div>

      <!-- Errors -->
      <?php if (!empty($errors)): ?>
        <ul class="errors" id="signin-errors">
          <?php foreach ($errors as $e): ?>
            <li><?php echo htmlspecialchars($e); ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <!-- Sign-in form -->
      <form method="POST" action="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin" id="signinForm">
        <input type="hidden" name="role" id="roleInput" value="customer">

        <div class="field">
          <label for="email" class="field-label">Email Address</label>
          <input type="email" id="email" name="email"
                 placeholder="Enter your email"
                 value="<?php echo $old_email ?? ''; ?>" required>
        </div>

        <div class="field">
          <div class="field-row">
            <label for="password" class="field-label">Password</label>
            <a href="forgot_password.php" class="link-pink" id="forgot-pw-link">Forgot Password?</a>
          </div>
          <input type="password" id="password" name="password"
                 placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn-submit" id="signin-submit-btn">SIGN IN</button>
      </form>

      <!-- Create account -->
      <p class="below-form">
        Don't have an account?
        <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signup" id="goto-signup">Create an Account</a>
      </p>

    </div>
  </div>

  <!-- Footer -->
  <div class="footer" id="signin-footer">
    <div class="footer-inner">
      <div class="footer-brand">Monet Nails</div>
      <nav class="footer-nav">
        <a href="privacy.php">Privacy Policy</a>
        <a href="terms.php">Terms of Service</a>
        <a href="contact.php">Contact</a>
      </nav>
    </div>
    <p class="footer-copy">&copy; 2024 Monet Nails. Artistry in Every Touch.</p>
  </div>

</div>

<script>
  /* Role toggle — no library */
  (function () {
    var toggle = document.getElementById('roleToggle');
    var input  = document.getElementById('roleInput');
    if (!toggle) return;

    toggle.addEventListener('click', function (e) {
      var btn = e.target.closest('button');
      if (!btn) return;
      toggle.querySelectorAll('button').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      input.value = btn.getAttribute('data-role');
    });
  })();
</script>
</body>
</html>
