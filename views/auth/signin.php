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

  <style>
    /* =========================================================
       Forgot Password Modal (Popup 1) Styles
       Matches Monet Nails Atelier Luxury Aesthetics
       ========================================================= */
    .forgot-overlay {
      position: fixed;
      inset: 0;
      z-index: 1000;
      background: rgba(28, 27, 27, 0.65);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      display: none;
      align-items: center;
      justify-content: center;
      padding: 24px;
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    .forgot-overlay.active {
      display: flex;
      opacity: 1;
    }
    .forgot-modal {
      position: relative;
      width: 100%;
      max-width: 480px;
      padding: 44px 38px 40px;
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(30px);
      -webkit-backdrop-filter: blur(30px);
      border-radius: 12px;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.35),
                  0 25px 50px -12px rgba(169, 39, 94, 0.25);
      border: 1px solid rgba(221, 194, 198, 0.5);
      transform: translateY(24px) scale(0.97);
      transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .forgot-overlay.active .forgot-modal {
      transform: translateY(0) scale(1);
    }
    .modal-topline {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      border-radius: 12px 12px 0 0;
      background: linear-gradient(90deg, #F2DDE2 0%, #A9275E 50%, #F2DDE2 100%);
    }
    .modal-close-btn {
      position: absolute;
      top: 16px;
      right: 18px;
      background: transparent;
      border: none;
      font-size: 24px;
      line-height: 1;
      color: #8A757A;
      cursor: pointer;
      padding: 6px;
      border-radius: 50%;
      transition: color 0.2s, transform 0.2s;
    }
    .modal-close-btn:hover {
      color: #A9275E;
      transform: scale(1.1);
    }
    .modal-badge-icon {
      width: 58px;
      height: 58px;
      border-radius: 50%;
      background: #FDF4F6;
      border: 1px solid #F2DDE2;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
      box-shadow: 0 4px 12px rgba(169, 39, 94, 0.1);
    }
    .modal-badge-icon svg {
      width: 26px;
      height: 26px;
      color: #A9275E;
    }
    .modal-title {
      font-family: 'Bodoni Moda', serif;
      font-size: 30px;
      font-weight: 500;
      color: #1C1B1B;
      text-align: center;
      margin-bottom: 8px;
      letter-spacing: -0.5px;
    }
    .modal-subtitle {
      font-size: 14px;
      line-height: 1.6;
      color: #6A5A5F;
      text-align: center;
      max-width: 380px;
      margin-bottom: 24px;
    }
    .modal-form {
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .modal-alert {
      display: none;
      padding: 12px 14px;
      border-radius: 6px;
      font-size: 13px;
      line-height: 1.5;
      text-align: center;
      width: 100%;
      box-sizing: border-box;
    }
    .modal-alert.alert-error {
      display: block;
      background: rgba(169, 39, 94, 0.08);
      border: 1px solid rgba(169, 39, 94, 0.35);
      color: #A9275E;
    }
    .modal-alert.alert-success {
      display: block;
      background: #EAF7EC;
      border: 1px solid #7FC88F;
      color: #256B36;
    }
    .modal-btn-submit {
      font-family: var(--font-body);
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 1.2px;
      color: #FFFFFF;
      background: #1C1B1B;
      border: 1px solid #1C1B1B;
      padding: 15px 24px;
      cursor: pointer;
      border-radius: 4px;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }
    .modal-btn-submit:hover:not(:disabled) {
      background: #A9275E;
      border-color: #A9275E;
      box-shadow: 0 4px 14px rgba(169, 39, 94, 0.25);
    }
    .modal-btn-submit:disabled {
      opacity: 0.7;
      cursor: not-allowed;
    }
    .modal-spinner {
      display: none;
      width: 16px;
      height: 16px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-top-color: #ffffff;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    .modal-back-link {
      font-size: 13px;
      color: #8A757A;
      margin-top: 18px;
      cursor: pointer;
      transition: color 0.15s;
      background: none;
      border: none;
      text-decoration: underline;
    }
    .modal-back-link:hover {
      color: #A9275E;
    }
    .success-sent-view {
      display: none;
      width: 100%;
      text-align: center;
      flex-direction: column;
      align-items: center;
    }
    .success-sent-icon {
      width: 64px;
      height: 64px;
      border-radius: 50%;
      background: #EAF7EC;
      color: #256B36;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 16px;
    }
    .success-sent-icon svg {
      width: 32px;
      height: 32px;
    }
    .success-sent-email {
      font-weight: 700;
      color: #1C1B1B;
    }
  </style>
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

      <!-- Password reset successful banner (if redirected from reset password) -->
      <?php if (isset($_GET['reset_success']) && $_GET['reset_success'] == '1'): ?>
        <div class="errors" id="signin-reset-success-banner"
             style="background:#EAF7EC;border-color:#7FC88F;color:#256B36;list-style:none;">
          &#10003;&nbsp; Password reset successfully! You can now sign in with your new password.
        </div>
      <?php endif; ?>

      <!-- Verified success banner -->
      <?php if (isset($_GET['verified']) && $_GET['verified'] == '1'): ?>
        <div class="errors" id="signin-verified-banner"
             style="background:#EAF7EC;border-color:#7FC88F;color:#256B36;list-style:none;">
          &#10003;&nbsp; Your email has been verified! Please sign in to continue.
        </div>
      <?php endif; ?>

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
            <a href="javascript:void(0);" class="link-pink" id="forgot-pw-link">Forgot Password?</a>
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
    <p class="footer-copy">&copy; 2026 Monet Nails. Artistry in Every Touch.</p>
  </div>

</div>

<!-- =========================================================
     POPUP WINDOW 1: Get Email for Password Reset Link
     ========================================================= -->
<div class="forgot-overlay" id="forgotOverlay" aria-modal="true" role="dialog" aria-labelledby="forgotModalTitle">
  <div class="forgot-modal" id="forgotModal">
    <div class="modal-topline"></div>
    <button type="button" class="modal-close-btn" id="forgotCloseBtn" aria-label="Close dialog">&times;</button>

    <!-- State 1: Request Email Form -->
    <div id="forgotFormContainer" style="width:100%; display:flex; flex-direction:column; align-items:center;">
      <div class="modal-badge-icon">
        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
        </svg>
      </div>

      <h2 class="modal-title" id="forgotModalTitle">Forgot Password?</h2>
      <p class="modal-subtitle">
        Enter your registered email address and we will send you a secure link to reset your password.
      </p>

      <div class="modal-alert" id="forgotAlert"></div>

      <form class="modal-form" id="forgotForm" method="POST" action="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=forgot_password_request">
        <input type="hidden" name="ajax" value="1">
        <div class="field" style="margin-bottom: 4px;">
          <label for="forgot-email" class="field-label">Email Address</label>
          <input type="email" id="forgot-email" name="email" placeholder="e.g. yourname@gmail.com" required autocomplete="email">
        </div>

        <button type="submit" class="modal-btn-submit" id="forgotSubmitBtn">
          <span class="modal-spinner" id="forgotSpinner"></span>
          <span id="forgotSubmitText">SEND RESET LINK</span>
        </button>
      </form>

      <button type="button" class="modal-back-link" id="forgotCancelBtn">Return to Sign In</button>
    </div>

    <!-- State 2: Confirmation / Success View -->
    <div class="success-sent-view" id="forgotSuccessView">
      <div class="success-sent-icon">
        <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>

      <h2 class="modal-title">Check Your Inbox</h2>
      <p class="modal-subtitle" style="margin-bottom: 16px;">
        A password reset link has been dispatched to <span class="success-sent-email" id="sentEmailDisplay"></span>.
      </p>
      <div style="background:#FAF6F5;border-left:3px solid #A9275E;padding:12px 14px;border-radius:4px;font-size:13px;color:#574147;line-height:1.5;margin-bottom:24px;text-align:left;width:100%;">
        <strong>Note:</strong> The link is valid for 1 hour. If you don't see the email within a couple of minutes, please check your spam/junk folder.
      </div>

      <div style="display:flex;gap:12px;width:100%;">
        <button type="button" class="modal-btn-submit" id="resendResetBtn" style="flex:1;background:transparent;color:#1C1B1B;border-color:#1C1B1B;">
          RESEND LINK
        </button>
        <button type="button" class="modal-btn-submit" id="finishForgotBtn" style="flex:1;background:#A9275E;border-color:#A9275E;">
          DONE
        </button>
      </div>
    </div>

  </div>
</div>

<script>
(function() {
  const overlay        = document.getElementById('forgotOverlay');
  const openLink       = document.getElementById('forgot-pw-link');
  const closeBtn       = document.getElementById('forgotCloseBtn');
  const cancelBtn      = document.getElementById('forgotCancelBtn');
  const finishBtn      = document.getElementById('finishForgotBtn');
  const resendBtn      = document.getElementById('resendResetBtn');
  const forgotForm     = document.getElementById('forgotForm');
  const forgotEmail    = document.getElementById('forgot-email');
  const submitBtn      = document.getElementById('forgotSubmitBtn');
  const submitText     = document.getElementById('forgotSubmitText');
  const spinner        = document.getElementById('forgotSpinner');
  const alertBox       = document.getElementById('forgotAlert');
  const formContainer  = document.getElementById('forgotFormContainer');
  const successView    = document.getElementById('forgotSuccessView');
  const sentEmailSpan  = document.getElementById('sentEmailDisplay');

  let lastSentEmail = '';

  function openModal() {
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    resetToFormState();
    // Pre-fill email from signin input if user already typed one
    const signinEmail = document.getElementById('email').value.trim();
    if (signinEmail && !forgotEmail.value) {
      forgotEmail.value = signinEmail;
    }
    setTimeout(() => forgotEmail.focus(), 150);
  }

  function closeModal() {
    overlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  function resetToFormState() {
    formContainer.style.display = 'flex';
    successView.style.display   = 'none';
    alertBox.style.display      = 'none';
    alertBox.className          = 'modal-alert';
    alertBox.textContent        = '';
    submitBtn.disabled          = false;
    submitText.textContent      = 'SEND RESET LINK';
    spinner.style.display       = 'none';
  }

  function showAlert(msg, isError = true) {
    alertBox.textContent = msg;
    alertBox.className = 'modal-alert ' + (isError ? 'alert-error' : 'alert-success');
    alertBox.style.display = 'block';
  }

  if (openLink) {
    openLink.addEventListener('click', (e) => {
      e.preventDefault();
      openModal();
    });
  }

  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
  if (finishBtn) finishBtn.addEventListener('click', closeModal);

  // Click backdrop to close
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) {
      closeModal();
    }
  });

  // Escape key closes modal
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && overlay.classList.contains('active')) {
      closeModal();
    }
  });

  // AJAX Form Submission
  forgotForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const emailVal = forgotEmail.value.trim();

    if (!emailVal) {
      showAlert('Please enter your email address.', true);
      forgotEmail.focus();
      return;
    }

    submitBtn.disabled     = true;
    submitText.textContent = 'SENDING LINK...';
    spinner.style.display  = 'inline-block';
    alertBox.style.display = 'none';

    fetch('/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=forgot_password_request', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: 'email=' + encodeURIComponent(emailVal) + '&ajax=1'
    })
    .then(res => res.json())
    .then(data => {
      if (data && data.success) {
        lastSentEmail = emailVal;
        sentEmailSpan.textContent = emailVal;
        formContainer.style.display = 'none';
        successView.style.display   = 'flex';
      } else {
        showAlert(data.message || 'Unable to send reset link. Please try again.', true);
        submitBtn.disabled     = false;
        submitText.textContent = 'SEND RESET LINK';
        spinner.style.display  = 'none';
      }
    })
    .catch(err => {
      console.error(err);
      showAlert('An unexpected error occurred. Please try again.', true);
      submitBtn.disabled     = false;
      submitText.textContent = 'SEND RESET LINK';
      spinner.style.display  = 'none';
    });
  });

  // Resend Button in Success View
  if (resendBtn) {
    resendBtn.addEventListener('click', () => {
      if (!lastSentEmail) return;
      resendBtn.disabled = true;
      resendBtn.textContent = 'SENDING...';

      fetch('/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=forgot_password_request', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'email=' + encodeURIComponent(lastSentEmail) + '&ajax=1'
      })
      .then(res => res.json())
      .then(data => {
        resendBtn.textContent = 'LINK RESENT!';
        setTimeout(() => {
          resendBtn.disabled = false;
          resendBtn.textContent = 'RESEND LINK';
        }, 3000);
      })
      .catch(() => {
        resendBtn.disabled = false;
        resendBtn.textContent = 'RESEND LINK';
      });
    });
  }

  // Auto-open modal if URL query param ?forgot=1 is present
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('forgot') === '1') {
    openModal();
  }
})();
</script>

</body>
</html>
