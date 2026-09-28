<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create an Account — Monet Nails</title>
  <meta name="description" content="Create a Monet Nails account to book bespoke appointments and shop custom press-on collections.">

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
    <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/signup/node-3.png" alt="">
  </div>
  <div class="brand-art right">
    <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/signup/node-4.png" alt="">
  </div>

  <!-- Top nav -->
  <div class="topbar" id="signup-topbar">
    <div class="topbar-inner">
      <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/" class="brand" id="signup-logo">Monet Nails</a>
      <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/" class="topbar-back" id="signup-back">Return Back</a>
    </div>
  </div>

  <!-- Main -->
  <div class="main-wrap">
    <div class="main">

      <!-- Heading -->
      <div>
        <h1 class="heading-title">Create an Account</h1>
        <p class="heading-sub">Join Monet Nails to book bespoke appointments and shop custom press-on collections.</p>
      </div>

      <!-- Errors -->
      <?php if (!empty($errors)): ?>
        <ul class="errors" id="signup-errors">
          <?php foreach ($errors as $e): ?>
            <li><?php echo htmlspecialchars($e); ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <!-- Sign-up form -->
      <form method="POST" action="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signup" id="signupForm">

        <div class="field">
          <label for="full_name" class="field-label">Full Name</label>
          <input type="text" id="full_name" name="full_name"
                 placeholder="Enter your full name"
                 value="<?php echo $old_name ?? ''; ?>" required>
        </div>

        <div class="field">
          <label for="email" class="field-label">Email Address</label>
          <input type="email" id="email" name="email"
                 placeholder="Enter your email"
                 value="<?php echo $old_email ?? ''; ?>" required>
        </div>

        <div class="field">
          <label for="phone" class="field-label">Phone Number</label>
          <input type="tel" id="phone" name="phone"
                 placeholder="e.g. +94 77 210 5954"
                 value="<?php echo $old_phone ?? ''; ?>">
        </div>

        <div class="field">
          <label for="password" class="field-label">Password</label>
          <input type="password" id="password" name="password"
                 placeholder="Create a password (min. 6 characters)" required>
        </div>

        <div class="field">
          <label for="confirm_password" class="field-label">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password"
                 placeholder="Re-enter your password" required>
        </div>

        <p class="terms">
          By signing up, you agree to our
          <a href="terms.php">Terms of Service</a> and
          <a href="privacy.php">Privacy Policy</a>.
        </p>

        <button type="submit" class="btn-submit" id="signup-submit-btn">CREATE ACCOUNT</button>
      </form>

      <!-- Sign in link -->
      <p class="below-form">
        Already have an account?
        <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin" id="goto-signin">Sign In</a>
      </p>

    </div>
  </div>

  <!-- Footer -->
  <div class="footer" id="signup-footer">
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
  /* Client-side UX guard — PHP also validates server-side */
  (function () {
    var form = document.getElementById('signupForm');
    if (!form) return;
    form.addEventListener('submit', function (e) {
      var p  = document.getElementById('password').value;
      var cp = document.getElementById('confirm_password').value;
      if (p.length < 6) {
        e.preventDefault();
        alert('Password must be at least 6 characters.');
        return;
      }
      if (p !== cp) {
        e.preventDefault();
        alert('Passwords do not match.');
      }
    });
  })();
</script>
</body>
</html>
