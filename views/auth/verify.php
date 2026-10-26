<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Your Account — Monet Nails</title>
  <meta name="description" content="Enter the 6-digit code sent to your email to verify your Monet Nails account.">

  <!-- Google Fonts: Bodoni Moda + Manrope -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@0,400..900;1,400..900&family=Manrope:wght@300..800&display=swap" rel="stylesheet">

  <!-- Shared auth stylesheet -->
  <link rel="stylesheet" href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/css/auth.css">

  <style>
    /* ===== Design tokens from the original UserVerify design ===== */
    :root {
      --pink:       #A9275E;
      --dark:       #1C1B1B;
      --subtitle:   #6A5A5F;
      --muted:      #5E5B5C;
      --placeholder:#9CA3AF;
      --softline:   #DDC2C6;
      --boxgrey:    #F6F3F2;
      --badgepink:  #F2DDE2;
      --mainbg:     #FCF9F8;
    }

    /* ===== Page layout — same shell as signup/signin ===== */
    body { background: var(--mainbg); }

    /* ===== Blurred backdrop that covers the whole page ===== */
    .verify-overlay {
      position: fixed;
      inset: 0;
      z-index: 900;
      background: rgba(28, 27, 27, 0.55);
      backdrop-filter: blur(6px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to   { opacity: 1; }
    }

    /* ===== The modal card ===== */
    .verify-card {
      position: relative;
      width: 100%;
      max-width: 540px;
      padding: 48px;
      background: rgba(255, 255, 255, 0.96);
      backdrop-filter: blur(40px);
      border-radius: 12px;
      box-shadow: 0 8px 10px -6px rgba(0,0,0,0.4),
                  0 20px 40px -5px rgba(0,0,0,0.3);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0;
      animation: slideUp 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes slideUp {
      from { transform: translateY(40px); opacity: 0; }
      to   { transform: translateY(0);    opacity: 1; }
    }

    /* pink top accent line */
    .card-topline {
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 4px;
      border-radius: 12px 12px 0 0;
      background: linear-gradient(90deg, #F2DDE2 0%, #A9275E 50%, #F2DDE2 100%);
    }

    /* watermark corner box */
    .card-watermark {
      position: absolute;
      top: 0; right: 0;
      width: 88px; height: 88px;
      background: var(--boxgrey);
      border-radius: 0 12px 0 12px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card-watermark svg { width: 44px; opacity: 0.55; }

    /* ===== Shield badge ===== */
    .shield-badge {
      width: 56px; height: 56px;
      background: var(--badgepink);
      border-radius: 12px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
    }
    .shield-badge svg { width: 24px; }

    /* ===== Title + subtitle ===== */
    .verify-title {
      font-family: 'Bodoni Moda', serif;
      font-size: 36px;
      font-weight: 500;
      letter-spacing: -0.5px;
      color: var(--dark);
      text-align: center;
      margin-bottom: 10px;
    }

    .verify-subtitle {
      font-size: 15px;
      line-height: 1.6;
      color: var(--subtitle);
      text-align: center;
      max-width: 400px;
      margin-bottom: 24px;
    }

    .verify-email-hint {
      font-weight: 700;
      color: var(--pink);
    }

    /* ===== Alerts ===== */
    .alert {
      width: 100%;
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 14px;
      text-align: center;
      margin-bottom: 16px;
    }
    .alert-success {
      background: #EAF7EC;
      border: 1px solid #7FC88F;
      color: #256B36;
    }
    .alert-error {
      background: #FBECEE;
      border: 1px solid #E08A96;
      color: #8E1F2C;
    }

    /* ===== Clipboard hint line ===== */
    .helper-line {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      color: var(--muted);
      letter-spacing: 0.5px;
      margin-bottom: 20px;
    }
    .helper-line svg { width: 16px; flex-shrink: 0; }

    /* ===== 6 OTP boxes ===== */
    .otp-row {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      margin-bottom: 8px;
    }

    .otp-box {
      width: 52px; height: 56px;
      border: 2px solid transparent;
      border-radius: 6px;
      background: var(--boxgrey);
      box-shadow: inset 0 2px 4px rgba(0,0,0,0.12);
      text-align: center;
      font-family: 'Bodoni Moda', serif;
      font-size: 28px;
      font-weight: 700;
      color: var(--dark);
      outline: none;
      transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
      caret-color: var(--pink);
    }
    .otp-box::placeholder {
      font-size: 36px;
      color: var(--placeholder);
      line-height: 56px;
    }
    .otp-box:focus {
      background: #fff;
      border-color: var(--pink);
      box-shadow: 0 0 0 3px rgba(169, 39, 94, 0.12);
    }
    .otp-box.filled {
      border-color: var(--softline);
      background: #fff;
    }

    .otp-divider {
      width: 12px; height: 2px;
      background: var(--softline);
      border-radius: 12px;
      flex-shrink: 0;
    }

    /* ===== Resend bar ===== */
    .resend-wrap {
      width: 100%;
      margin-top: 24px;
    }

    .resend-bar {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      padding: 16px;
      background: rgba(246, 243, 242, 0.6);
      border: 1px solid var(--softline);
      border-radius: 8px;
      cursor: default;
      font-family: 'Manrope', sans-serif;
      transition: background 0.2s, border-color 0.2s;
    }
    .resend-bar.active {
      cursor: pointer;
      background: var(--badgepink);
      border-color: var(--pink);
    }
    .resend-bar.active:hover {
      background: #eecdd6;
    }
    .resend-bar svg { width: 18px; flex-shrink: 0; }

    .resend-text {
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 0.7px;
      color: var(--pink);
    }

    /* ===== Verify button ===== */
    .verify-btn {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      padding: 16px;
      margin-top: 20px;
      background: var(--dark);
      border: none;
      border-radius: 12px;
      font-family: 'Manrope', sans-serif;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 1.2px;
      text-transform: uppercase;
      color: #fff;
      cursor: pointer;
      transition: background 0.2s, transform 0.15s;
    }
    .verify-btn:hover   { background: #333; }
    .verify-btn:active  { transform: scale(0.98); }
    .verify-btn.success {
      background: #1a7a38;
      pointer-events: none;
    }
    .verify-btn svg { width: 18px; flex-shrink: 0; }

    /* ===== Return link ===== */
    .return-link {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-top: 16px;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 0.7px;
      color: var(--subtitle);
      text-decoration: none;
      transition: color 0.15s;
    }
    .return-link:hover { color: var(--pink); }
    .return-link svg   { width: 14px; }

    /* ===== Responsive ===== */
    @media (max-width: 540px) {
      .verify-card  { padding: 36px 20px; }
      .verify-title { font-size: 28px; }
      .otp-box      { width: 42px; height: 48px; font-size: 22px; }
    }
  </style>
</head>
<body>

<!-- Background page (blurred signup-like shell so the context is clear) -->
<div class="page">

  <!-- Decorative brand illustrations (same as signup) -->
  <div class="brand-art left">
    <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/signup/node-3.png" alt="">
  </div>
  <div class="brand-art right">
    <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/signup/node-4.png" alt="">
  </div>

  <!-- Top nav -->
  <div class="topbar" id="verify-topbar">
    <div class="topbar-inner">
      <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/" class="brand" id="verify-logo">Monet Nails</a>
    </div>
  </div>

  <!-- Ghost form area (blurred, not interactive) -->
  <div class="main-wrap" style="filter:blur(2px); pointer-events:none; user-select:none; opacity:0.4;">
    <div class="main">
      <div>
        <h1 class="heading-title">Create an Account</h1>
        <p class="heading-sub">Verifying your email address&hellip;</p>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <div class="footer" id="verify-footer">
    <div class="footer-inner">
      <div class="footer-brand">Monet Nails</div>
      <nav class="footer-nav">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
        <a href="#">Contact</a>
      </nav>
    </div>
    <p class="footer-copy">&copy; 2024 Monet Nails. Artistry in Every Touch.</p>
  </div>
</div>

<!-- ===== VERIFICATION MODAL OVERLAY ===== -->
<div class="verify-overlay" id="verifyOverlay" role="dialog" aria-modal="true" aria-labelledby="verifyTitle">
  <div class="verify-card">

    <!-- Pink top accent -->
    <div class="card-topline"></div>

    <!-- Leaf watermark -->
    <div class="card-watermark">
      <!-- leaf SVG (inline, same design reference) -->
      <svg viewBox="0 0 54 60" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M27 4C27 4 8 16 8 34C8 44.5 16.5 53 27 53C37.5 53 46 44.5 46 34C46 16 27 4 27 4Z"
              fill="#DDC2C6" opacity="0.5"/>
        <path d="M27 10C27 10 27 30 27 50" stroke="#A9275E" stroke-width="1.5" stroke-linecap="round"/>
      </svg>
    </div>

    <!-- Shield badge -->
    <div class="shield-badge">
      <!-- shield-check SVG -->
      <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 2L4 6V12C4 16.4 7.4 20.5 12 22C16.6 20.5 20 16.4 20 12V6L12 2Z"
              fill="#A9275E" opacity="0.15" stroke="#A9275E" stroke-width="1.5"/>
        <path d="M9 12L11 14L15 10" stroke="#A9275E" stroke-width="1.8"
              stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>

    <!-- Title -->
    <h1 class="verify-title" id="verifyTitle">Verify Your Account</h1>

    <!-- Subtitle with masked email -->
    <p class="verify-subtitle">
      We've sent a discrete 6-digit code to
      <span class="verify-email-hint"><?php echo htmlspecialchars($pendingEmail ?: 'your email'); ?></span>.
      Enter it below to activate your account.
    </p>

    <!-- Alerts -->
    <?php if ($notice !== ''): ?>
      <div class="alert alert-success" id="verifyNotice"><?php echo htmlspecialchars($notice); ?></div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
      <div class="alert alert-error" id="verifyError"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Clipboard hint -->
    <div class="helper-line">
      <!-- clipboard SVG -->
      <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="8" y="2" width="8" height="4" rx="1" stroke="#5E5B5C" stroke-width="1.5"/>
        <path d="M8 3H6C4.9 3 4 3.9 4 5V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V5C20 3.9 19.1 3 18 3H16"
              stroke="#5E5B5C" stroke-width="1.5"/>
      </svg>
      <span>Supports automatic clipboard paste &amp; device autofill</span>
    </div>

    <!-- OTP form -->
    <form method="POST"
          action="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=verify"
          id="verifyForm"
          style="width:100%;">

      <input type="hidden" name="action" id="formAction" value="verify">

      <!-- 6 digit boxes -->
      <div class="otp-row" id="otpRow">
        <input class="otp-box <?php echo !empty($otpDigits[0]) ? 'filled' : ''; ?>" type="text" name="otp[]" id="otp0"
               maxlength="1" inputmode="numeric" autocomplete="one-time-code"
               value="<?php echo htmlspecialchars($otpDigits[0] ?? ''); ?>"
               placeholder="·" aria-label="Digit 1">
        <input class="otp-box <?php echo !empty($otpDigits[1]) ? 'filled' : ''; ?>" type="text" name="otp[]" id="otp1"
               maxlength="1" inputmode="numeric"
               value="<?php echo htmlspecialchars($otpDigits[1] ?? ''); ?>"
               placeholder="·" aria-label="Digit 2">
        <input class="otp-box <?php echo !empty($otpDigits[2]) ? 'filled' : ''; ?>" type="text" name="otp[]" id="otp2"
               maxlength="1" inputmode="numeric"
               value="<?php echo htmlspecialchars($otpDigits[2] ?? ''); ?>"
               placeholder="·" aria-label="Digit 3">
        <span class="otp-divider" aria-hidden="true"></span>
        <input class="otp-box <?php echo !empty($otpDigits[3]) ? 'filled' : ''; ?>" type="text" name="otp[]" id="otp3"
               maxlength="1" inputmode="numeric"
               value="<?php echo htmlspecialchars($otpDigits[3] ?? ''); ?>"
               placeholder="·" aria-label="Digit 4">
        <input class="otp-box <?php echo !empty($otpDigits[4]) ? 'filled' : ''; ?>" type="text" name="otp[]" id="otp4"
               maxlength="1" inputmode="numeric"
               value="<?php echo htmlspecialchars($otpDigits[4] ?? ''); ?>"
               placeholder="·" aria-label="Digit 5">
        <input class="otp-box <?php echo !empty($otpDigits[5]) ? 'filled' : ''; ?>" type="text" name="otp[]" id="otp5"
               maxlength="1" inputmode="numeric"
               value="<?php echo htmlspecialchars($otpDigits[5] ?? ''); ?>"
               placeholder="·" aria-label="Digit 6">
      </div>

      <!-- Resend bar -->
      <div class="resend-wrap">
        <button type="submit" name="action" value="resend"
                class="resend-bar" id="resendBar" disabled>
          <!-- refresh icon -->
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 12C4 7.6 7.6 4 12 4C14.5 4 16.7 5.1 18.2 6.8L20 5V10H15L17.1 7.9C16 6.7 14.1 6 12 6C8.7 6 6 8.7 6 12H4Z"
                  fill="#A9275E"/>
            <path d="M20 12C20 16.4 16.4 20 12 20C9.5 20 7.3 18.9 5.8 17.2L4 19V14H9L6.9 16.1C8 17.3 9.9 18 12 18C15.3 18 18 15.3 18 12H20Z"
                  fill="#A9275E"/>
          </svg>
          <span class="resend-text" id="resendText">
            Resend Code in <span id="timerDisplay">02:00</span>
          </span>
        </button>
      </div>

      <!-- Verify button -->
      <button type="submit" name="action" value="verify"
              class="verify-btn" id="verifyBtn">
        <!-- lock icon (swapped to tick on success via JS) -->
        <svg id="verifyIcon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="5" y="11" width="14" height="10" rx="2"
                fill="none" stroke="white" stroke-width="1.8"/>
          <path d="M8 11V7C8 4.8 9.8 3 12 3C14.2 3 16 4.8 16 7V11"
                stroke="white" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        <span id="verifyBtnText">Verify Code</span>
      </button>

    </form>

    <!-- Return link -->
    <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signup"
       class="return-link" id="verifyReturn">
      <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M19 12H5M5 12L12 19M5 12L12 5"
              stroke="#6A5A5F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Return to Sign Up
    </a>

  </div><!-- /verify-card -->
</div><!-- /verify-overlay -->


<script>
/* ============================================================
   verify.js (inline)
   - OTP boxes: focus-jump, backspace, paste
   - Countdown timer → enables resend button
   - On successful verify: swap lock→tick, change text, auto-redirect
   ============================================================ */

(function () {
  'use strict';

  /* ---- 1. OTP box helpers ---- */
  var boxes    = document.querySelectorAll('.otp-box');
  var verifyBtn = document.getElementById('verifyBtn');
  var verifyIcon= document.getElementById('verifyIcon');
  var verifyTxt = document.getElementById('verifyBtnText');
  var resendBar = document.getElementById('resendBar');
  var resendTxt = document.getElementById('resendText');
  var timerEl   = document.getElementById('timerDisplay');

  function nextBox(el) {
    var sib = el.nextElementSibling;
    while (sib && !sib.classList.contains('otp-box')) sib = sib.nextElementSibling;
    return sib;
  }
  function prevBox(el) {
    var sib = el.previousElementSibling;
    while (sib && !sib.classList.contains('otp-box')) sib = sib.previousElementSibling;
    return sib;
  }

  boxes.forEach(function (box) {
    box.addEventListener('input', function () {
      this.value = this.value.replace(/[^0-9]/g, '');
      if (this.value.length > 1) this.value = this.value.slice(-1);
      if (this.value) {
        this.classList.add('filled');
        var next = nextBox(this);
        if (next) next.focus();
      } else {
        this.classList.remove('filled');
      }
    });

    box.addEventListener('keydown', function (e) {
      if (e.key === 'Backspace' && this.value === '') {
        var prev = prevBox(this);
        if (prev) { prev.value = ''; prev.classList.remove('filled'); prev.focus(); }
      }
    });

    /* Paste: fill all boxes at once */
    box.addEventListener('paste', function (e) {
      var text   = (e.clipboardData || window.clipboardData).getData('text');
      var digits = text.replace(/[^0-9]/g, '');
      if (digits.length > 0) {
        e.preventDefault();
        boxes.forEach(function (b, i) {
          b.value = digits[i] || '';
          if (b.value) b.classList.add('filled');
          else         b.classList.remove('filled');
        });
        boxes[Math.min(digits.length, boxes.length) - 1].focus();
      }
    });
  });

  /* ---- 2. Countdown (2 minutes = 120 s) ---- */
  var seconds = 120;
  function tick() {
    if (seconds <= 0) {
      timerEl.textContent = '';
      resendTxt.textContent = 'Resend Code';
      resendBar.disabled = false;
      resendBar.classList.add('active');
      return;
    }
    var m = Math.floor(seconds / 60);
    var s = seconds % 60;
    timerEl.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    seconds--;
    setTimeout(tick, 1000);
  }
  tick();

  /* ---- 3. On verify-form submit: check if all boxes filled ---- */
  var verifyForm = document.getElementById('verifyForm');
  var formAction = document.getElementById('formAction');

  if (resendBar) {
    resendBar.addEventListener('click', function () {
      if (formAction) formAction.value = 'resend';
    });
  }

  verifyForm.addEventListener('submit', function (e) {
    var action = formAction ? formAction.value : (e.submitter ? e.submitter.value : 'verify');

    if (action === 'verify') {
      /* Require all 6 digits */
      var allFilled = true;
      boxes.forEach(function (b) { if (!b.value) allFilled = false; });
      if (!allFilled) {
        e.preventDefault();
        showError('Please fill in all 6 digits before verifying.');
        return;
      }

      /* Animate button → "Verifying…" */
      verifyBtn.style.pointerEvents = 'none';
      verifyBtn.style.opacity = '0.7';
      verifyTxt.textContent = 'Verifying…';
    }
  });

  /* ---- 4. PHP-set verified state: swap lock→tick & redirect ---- */
  <?php if (isset($verified) && $verified): ?>
  (function () {
    /* Swap lock SVG → tick SVG */
    verifyIcon.innerHTML =
      '<path d="M5 12L10 17L19 7" stroke="white" stroke-width="2.2" ' +
      'stroke-linecap="round" stroke-linejoin="round"/>';
    verifyTxt.textContent = 'Verified!';
    verifyBtn.classList.add('success');

    /* Auto-redirect after 1.5 s */
    setTimeout(function () {
      window.location.href =
        '/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin&verified=1';
    }, 1500);
  })();
  <?php endif; ?>

  /* ---- 5. Inline error helper (client-side only) ---- */
  function showError(msg) {
    var existing = document.getElementById('verifyError');
    if (existing) { existing.textContent = msg; return; }
    var div = document.createElement('div');
    div.id = 'verifyError';
    div.className = 'alert alert-error';
    div.textContent = msg;
    var card = document.querySelector('.verify-card');
    var hint = document.querySelector('.helper-line');
    card.insertBefore(div, hint);
  }

})();
</script>
</body>
</html>
