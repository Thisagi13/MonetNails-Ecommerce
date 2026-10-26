<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password — Monet Nails</title>
  <meta name="description" content="Create a new password for your Monet Nails account.">

  <!-- Google Fonts: Bodoni Moda + Manrope -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@0,400..900;1,400..900&family=Manrope:wght@300..800&display=swap" rel="stylesheet">

  <!-- Auth stylesheet -->
  <link rel="stylesheet" href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/css/auth.css">

  <style>
    /* =========================================================
       Reset Password Page & Popup 2 Styles
       ========================================================= */
    .input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
      width: 100%;
    }
    .input-wrapper input {
      padding-right: 40px !important;
    }
    .toggle-pw-btn {
      position: absolute;
      right: 0;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #8A757A;
      cursor: pointer;
      padding: 8px 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: color 0.15s;
    }
    .toggle-pw-btn:hover {
      color: #A9275E;
    }
    .toggle-pw-btn svg {
      width: 20px;
      height: 20px;
    }

    /* Password Strength Indicator */
    .strength-meter-wrap {
      display: flex;
      flex-direction: column;
      gap: 6px;
      margin-top: 4px;
    }
    .strength-bar-track {
      width: 100%;
      height: 4px;
      background: #EFE9EA;
      border-radius: 2px;
      overflow: hidden;
      display: flex;
      gap: 4px;
    }
    .strength-segment {
      flex: 1;
      height: 100%;
      background: #EFE9EA;
      transition: background 0.3s ease;
    }
    .strength-label {
      font-size: 12px;
      color: #8A757A;
      display: flex;
      justify-content: space-between;
    }

    /* Account badge */
    .account-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 14px;
      background: #FDF4F6;
      border: 1px solid #F2DDE2;
      border-radius: 20px;
      font-size: 13px;
      color: #A9275E;
      font-weight: 500;
      margin-bottom: 8px;
    }

    /* =========================================================
       POPUP WINDOW 2: Password Reset Successful Modal
       ========================================================= */
    .success-overlay {
      position: fixed;
      inset: 0;
      z-index: 1000;
      background: rgba(28, 27, 27, 0.65);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      display: <?php echo (!empty($success)) ? 'flex' : 'none'; ?>;
      align-items: center;
      justify-content: center;
      padding: 24px;
      opacity: <?php echo (!empty($success)) ? '1' : '0'; ?>;
      transition: opacity 0.3s ease;
    }
    .success-overlay.active {
      display: flex;
      opacity: 1;
    }
    .success-card {
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
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      animation: modalPop 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes modalPop {
      from { transform: translateY(30px) scale(0.95); opacity: 0; }
      to   { transform: translateY(0) scale(1); opacity: 1; }
    }
    .success-card-topline {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      border-radius: 12px 12px 0 0;
      background: linear-gradient(90deg, #F2DDE2 0%, #A9275E 50%, #F2DDE2 100%);
    }
    .success-badge {
      width: 68px;
      height: 68px;
      border-radius: 50%;
      background: #EAF7EC;
      border: 2px solid #7FC88F;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
      box-shadow: 0 6px 16px rgba(37, 107, 54, 0.15);
      animation: checkPulse 0.5s ease-out;
    }
    @keyframes checkPulse {
      0%   { transform: scale(0.6); opacity: 0; }
      60%  { transform: scale(1.15); }
      100% { transform: scale(1); opacity: 1; }
    }
    .success-badge svg {
      width: 36px;
      height: 36px;
      color: #256B36;
    }
    .success-title {
      font-family: 'Bodoni Moda', serif;
      font-size: 30px;
      font-weight: 500;
      color: #1C1B1B;
      margin-bottom: 10px;
      letter-spacing: -0.5px;
    }
    .success-desc {
      font-size: 15px;
      line-height: 1.6;
      color: #574147;
      margin-bottom: 24px;
    }
    .success-timer-bar {
      width: 100%;
      height: 3px;
      background: #EFE9EA;
      border-radius: 2px;
      overflow: hidden;
      margin-bottom: 14px;
    }
    .success-timer-fill {
      width: 100%;
      height: 100%;
      background: #A9275E;
      transform-origin: left;
      animation: timerCountdown 5s linear forwards;
    }
    @keyframes timerCountdown {
      from { transform: scaleX(1); }
      to   { transform: scaleX(0); }
    }
    .btn-signin-now {
      width: 100%;
      font-family: var(--font-body);
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 1.2px;
      color: #FFFFFF;
      background: #1C1B1B;
      border: 1px solid #1C1B1B;
      padding: 15px 24px;
      text-decoration: none;
      border-radius: 4px;
      transition: all 0.2s ease;
      display: inline-block;
      box-sizing: border-box;
    }
    .btn-signin-now:hover {
      background: #A9275E;
      border-color: #A9275E;
      box-shadow: 0 4px 14px rgba(169, 39, 94, 0.25);
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
  <div class="topbar" id="reset-topbar">
    <div class="topbar-inner">
      <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/" class="brand" id="reset-logo">Monet Nails</a>
      <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin" class="topbar-back" id="reset-back">Return to Sign In</a>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-wrap">
    <div class="main">

      <?php if ($tokenStatus !== 'valid' || !$resetRecord): ?>
        <!-- =========================================================
             INVALID OR EXPIRED LINK STATE
             ========================================================= -->
        <div style="text-align:center; display:flex; flex-direction:column; align-items:center;">
          <div style="width:64px;height:64px;border-radius:50%;background:#FDF4F6;border:1px solid #F2DDE2;display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
            <svg style="width:30px;height:30px;color:#A9275E;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
          </div>

          <h1 class="heading-title" style="margin-bottom:8px;">Link Expired or Invalid</h1>
          <p class="heading-sub" style="margin-bottom:28px;">
            <?php if ($tokenStatus === 'used'): ?>
              This password reset link has already been used. Each link is single-use for your security.
            <?php elseif ($tokenStatus === 'expired'): ?>
              This password reset link has expired. For your safety, reset links are valid for 1 hour.
            <?php else: ?>
              This password reset link is invalid or incomplete.
            <?php endif; ?>
          </p>

          <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin&forgot=1"
             class="btn-submit"
             style="text-align:center;display:inline-block;padding:15px 32px;">
            REQUEST A NEW LINK
          </a>

          <p class="below-form" style="margin-top:24px;">
            Remember your credentials?
            <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin">Sign In</a>
          </p>
        </div>

      <?php else: ?>
        <!-- =========================================================
             VALID TOKEN: SET NEW PASSWORD FORM
             ========================================================= -->
        <div style="text-align:center;">
          <div class="account-pill">
            <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
            </svg>
            <?php echo htmlspecialchars($resetRecord['email']); ?>
          </div>
          <h1 class="heading-title">Reset Password</h1>
          <p class="heading-sub">Please enter a new, secure password for your account.</p>
        </div>

        <!-- Errors -->
        <?php if (!empty($errors)): ?>
          <ul class="errors" id="reset-errors">
            <?php foreach ($errors as $e): ?>
              <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" action="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=reset_password" id="resetPasswordForm">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

          <!-- New Password -->
          <div class="field">
            <label for="new_password" class="field-label">New Password</label>
            <div class="input-wrapper">
              <input type="password" id="new_password" name="password"
                     placeholder="Enter your new password" required minlength="6" autocomplete="new-password">
              <button type="button" class="toggle-pw-btn" data-target="new_password" aria-label="Toggle password visibility">
                <svg class="eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <svg class="eye-closed" style="display:none;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
              </button>
            </div>
            <!-- Strength meter -->
            <div class="strength-meter-wrap">
              <div class="strength-bar-track">
                <div class="strength-segment" id="seg1"></div>
                <div class="strength-segment" id="seg2"></div>
                <div class="strength-segment" id="seg3"></div>
              </div>
              <div class="strength-label">
                <span id="strengthText">Password Strength</span>
                <span style="font-size:11px;color:#8A757A;">Min 6 characters</span>
              </div>
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="field">
            <label for="confirm_password" class="field-label">Confirm New Password</label>
            <div class="input-wrapper">
              <input type="password" id="confirm_password" name="confirm_password"
                     placeholder="Re-enter your new password" required minlength="6" autocomplete="new-password">
              <button type="button" class="toggle-pw-btn" data-target="confirm_password" aria-label="Toggle password visibility">
                <svg class="eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <svg class="eye-closed" style="display:none;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
              </button>
            </div>
            <div id="matchMessage" style="font-size:12px;margin-top:4px;display:none;"></div>
          </div>

          <button type="submit" class="btn-submit" id="reset-submit-btn">RESET PASSWORD</button>
        </form>

        <p class="below-form">
          Remember your password?
          <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin">Sign In</a>
        </p>

      <?php endif; ?>

    </div>
  </div>

  <!-- Footer -->
  <div class="footer" id="reset-footer">
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
     POPUP WINDOW 2: Password Reset Successful Modal
     ========================================================= -->
<div class="success-overlay" id="successOverlay" aria-modal="true" role="dialog" aria-labelledby="successTitle">
  <div class="success-card">
    <div class="success-card-topline"></div>

    <div class="success-badge">
      <svg fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
      </svg>
    </div>

    <h2 class="success-title" id="successTitle">Password Reset Successful!</h2>
    <p class="success-desc">
      Your account password has been updated securely. You can now sign in using your new credentials.
    </p>

    <div class="success-timer-bar">
      <div class="success-timer-fill"></div>
    </div>
    <p style="font-size:12px;color:#8A757A;margin-bottom:20px;">
      Redirecting to Sign In in <span id="countdownSec" style="font-weight:700;color:#A9275E;">5</span>s...
    </p>

    <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin&reset_success=1" class="btn-signin-now" id="btnSigninNow">
      SIGN IN NOW
    </a>
  </div>
</div>

<script>
(function() {
  // Password Visibility Toggle
  document.querySelectorAll('.toggle-pw-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.getAttribute('data-target');
      const input = document.getElementById(targetId);
      const eyeOpen = btn.querySelector('.eye-open');
      const eyeClosed = btn.querySelector('.eye-closed');

      if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.style.display = 'none';
        eyeClosed.style.display = 'block';
      } else {
        input.type = 'password';
        eyeOpen.style.display = 'block';
        eyeClosed.style.display = 'none';
      }
    });
  });

  // Password Strength & Match Validation
  const newPwInput     = document.getElementById('new_password');
  const confirmPwInput = document.getElementById('confirm_password');
  const seg1           = document.getElementById('seg1');
  const seg2           = document.getElementById('seg2');
  const seg3           = document.getElementById('seg3');
  const strengthText   = document.getElementById('strengthText');
  const matchMsg       = document.getElementById('matchMessage');
  const resetForm      = document.getElementById('resetPasswordForm');
  const successOverlay = document.getElementById('successOverlay');
  const countdownSpan  = document.getElementById('countdownSec');

  if (newPwInput) {
    newPwInput.addEventListener('input', () => {
      const val = newPwInput.value;
      let score = 0;

      if (val.length >= 6) score++;
      if (val.length >= 8 && (/[A-Z]/.test(val) || /[0-9]/.test(val))) score++;
      if (val.length >= 10 && (/[^a-zA-Z0-9]/.test(val) && /[0-9]/.test(val))) score++;

      // Reset segments
      seg1.style.background = '#EFE9EA';
      seg2.style.background = '#EFE9EA';
      seg3.style.background = '#EFE9EA';

      if (val.length === 0) {
        strengthText.textContent = 'Password Strength';
        strengthText.style.color = '#8A757A';
      } else if (score === 1) {
        seg1.style.background = '#E55353';
        strengthText.textContent = 'Weak';
        strengthText.style.color = '#E55353';
      } else if (score === 2) {
        seg1.style.background = '#F9A03F';
        seg2.style.background = '#F9A03F';
        strengthText.textContent = 'Good';
        strengthText.style.color = '#F9A03F';
      } else if (score >= 3) {
        seg1.style.background = '#256B36';
        seg2.style.background = '#256B36';
        seg3.style.background = '#256B36';
        strengthText.textContent = 'Strong';
        strengthText.style.color = '#256B36';
      }

      checkMatch();
    });
  }

  function checkMatch() {
    if (!confirmPwInput || !newPwInput) return;
    const p1 = newPwInput.value;
    const p2 = confirmPwInput.value;

    if (!p2) {
      matchMsg.style.display = 'none';
      return;
    }

    matchMsg.style.display = 'block';
    if (p1 === p2) {
      matchMsg.style.color = '#256B36';
      matchMsg.textContent = '✓ Passwords match';
    } else {
      matchMsg.style.color = '#A9275E';
      matchMsg.textContent = '✕ Passwords do not match';
    }
  }

  if (confirmPwInput) {
    confirmPwInput.addEventListener('input', checkMatch);
  }

  // Trigger Popup 2 Redirect Countdown if success is already active from server
  function startRedirectCountdown() {
    let timeLeft = 5;
    const timer = setInterval(() => {
      timeLeft--;
      if (countdownSpan) countdownSpan.textContent = timeLeft;
      if (timeLeft <= 0) {
        clearInterval(timer);
        window.location.href = '/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin&reset_success=1';
      }
    }, 1000);
  }

  <?php if (!empty($success)): ?>
    startRedirectCountdown();
  <?php endif; ?>

  // Form submission with AJAX for seamless popup display
  if (resetForm) {
    resetForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const p1 = newPwInput.value;
      const p2 = confirmPwInput.value;

      if (p1.length < 6) {
        alert('Password must be at least 6 characters.');
        return;
      }
      if (p1 !== p2) {
        alert('Passwords do not match. Please verify.');
        return;
      }

      const submitBtn = document.getElementById('reset-submit-btn');
      submitBtn.disabled = true;
      submitBtn.textContent = 'UPDATING...';

      const formData = new FormData(resetForm);
      formData.append('ajax', '1');

      fetch('/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=reset_password', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data && data.success) {
          // Open Popup 2 immediately
          successOverlay.classList.add('active');
          document.body.style.overflow = 'hidden';
          startRedirectCountdown();
        } else {
          submitBtn.disabled = false;
          submitBtn.textContent = 'RESET PASSWORD';
          const errList = data.errors ? data.errors.join("\n") : (data.message || 'Error resetting password.');
          alert(errList);
        }
      })
      .catch(err => {
        console.error(err);
        // Fallback to standard submit
        resetForm.submit();
      });
    });
  }
})();
</script>

</body>
</html>
