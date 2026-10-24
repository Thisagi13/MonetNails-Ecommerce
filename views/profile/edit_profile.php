<?php
/**
 * views/profile/edit_profile.php — Customer Account Settings & Profile View
 * Monet Nails MVC Application
 *
 * Provides:
 *   - Profile details editor (Name, Email, Phone, Address)
 *   - Profile picture upload with instant live preview
 *   - Secure password update form
 * Uses unified site header and footer.
 */

$base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';

// Helper for safe output
if (!function_exists('h')) {
    function h($text) {
        return htmlspecialchars((string)($text ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$fullName = trim(($customer['f_name'] ?? '') . ' ' . ($customer['l_name'] ?? ''));
if ($fullName === '') {
    $fullName = $customer['email'] ?? 'Client';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings &amp; Profile — Monet Nails Atelier</title>
    <meta name="description" content="Manage your Monet Nails account details, personal dossier, profile photo, and security settings.">

    <!-- Fonts: Bodoni Moda (headings) + Manrope (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..700&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Unified Header CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/header.css">

    <!-- Profile Page CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/profile.css">

    <!-- Critical nav/popup overrides (ensure profile icon is always clickable) -->
    <style>
      .top-nav { position: sticky !important; top: 0 !important; z-index: 200 !important; }
      .top-nav .nav-actions { position: relative !important; }
      .top-nav .profile-btn {
        cursor: pointer !important;
        position: relative !important;
        z-index: 205 !important;
        pointer-events: auto !important;
      }
      .top-nav .profile-btn * { pointer-events: none !important; }
      .profile-popup { position: absolute !important; z-index: 300 !important; }
      .popup-overlay { position: fixed !important; inset: 0 !important; z-index: 90 !important; }
      .popup-item svg, .popup-item img { pointer-events: none !important; }
    </style>
</head>
<body>

    <!-- ============================================================
         UNIFIED SITE HEADER
         ============================================================ -->
    <?php
    $activeNav = 'account_settings';
    require __DIR__ . '/../../includes/header.php';
    ?>

    <!-- ============================================================
         ACCOUNT SETTINGS MAIN CONTENT
         ============================================================ -->
    <main class="profile-page">
        <div class="profile-container">

            <!-- Breadcrumb -->
            <nav class="profile-breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo $base_url; ?>/">Home</a>
                <span class="sep">/</span>
                <span class="current">Account Settings</span>
            </nav>

            <!-- Header -->
            <header class="profile-header">
                <h1>Account Settings &amp; Profile</h1>
                <p>Personalize your Monet Nails atelier dossier, delivery destinations, and security credentials.</p>
            </header>

            <div class="profile-cards-grid">

                <!-- ========================================================
                     CARD 1: PERSONAL PROFILE & PHOTO
                     ======================================================== -->
                <section class="profile-card" id="profileDetailsSection">
                    <div class="card-title-row">
                        <div class="card-title-left">
                            <div class="card-icon-badge" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div>
                                <h2>Profile Details</h2>
                                <div class="card-subtitle">Manage your name, contact details, and custom avatar.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Alerts for Profile -->
                    <?php if (!empty($profile_msg)): ?>
                        <div class="profile-alert success" role="alert">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span><?php echo h($profile_msg); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($profile_err)): ?>
                        <div class="profile-alert error" role="alert">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span><?php echo h($profile_err); ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo $base_url; ?>/index.php?page=account_settings" enctype="multipart/form-data" class="profile-form">
                        <input type="hidden" name="save_profile" value="1">

                        <!-- Avatar Upload Section -->
                        <div class="avatar-section">
                            <div class="avatar-preview-wrap" onclick="openFilePicker()" title="Click to change profile photo">
                                <?php if (!empty($avatarUrl)): ?>
                                    <img src="<?php echo h($avatarUrl); ?>" alt="Profile photo" id="avatarPreviewImg">
                                <?php else: ?>
                                    <div class="avatar-initials-lg" id="avatarInitialWrap"><?php echo h($initials); ?></div>
                                    <img src="" alt="Profile photo" id="avatarPreviewImg" style="display: none;">
                                <?php endif; ?>

                                <div class="avatar-camera-overlay" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                        <circle cx="12" cy="13" r="4"></circle>
                                    </svg>
                                </div>
                            </div>

                            <div class="avatar-action-info">
                                <input type="file" name="profile_photo" id="photoInput" accept=".jpg,.jpeg,.png,.webp" style="display: none;">
                                <button type="button" class="btn-upload-trigger" onclick="openFilePicker()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    <span>Upload New Photo</span>
                                </button>
                                <span class="avatar-specs">Supported formats: JPG, PNG, WEBP · Max file size: 2MB</span>
                            </div>
                        </div>

                        <!-- Profile Info Inputs -->
                        <div class="profile-form-grid">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label" for="full_name">Full Name *</label>
                                    <input type="text" id="full_name" name="full_name" class="form-control"
                                           value="<?php echo h($fullName); ?>" placeholder="e.g. Eleanor Vance" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="email">Email Address *</label>
                                    <input type="email" id="email" name="email" class="form-control"
                                           value="<?php echo h($customer['email'] ?? ''); ?>" placeholder="name@domain.com" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label" for="phone_no">Phone Number</label>
                                    <input type="tel" id="phone_no" name="phone_no" class="form-control"
                                           value="<?php echo h($customer['phone_no'] ?? ''); ?>" placeholder="e.g. +94 77 123 4567">
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="address">Shipping / Home Address</label>
                                    <textarea id="address" name="address" rows="3" class="form-control form-textarea"
                                              placeholder="Enter your street address, apartment, suite, or delivery notes"><?php echo h($customer['address'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="form-action-row">
                                <button type="submit" class="btn-primary-action">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                        <polyline points="7 3 7 8 15 8"></polyline>
                                    </svg>
                                    <span>Save Profile Changes</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </section>


                <!-- ========================================================
                     CARD 2: SECURITY & PASSWORD
                     ======================================================== -->
                <section class="profile-card" id="securitySection">
                    <div class="card-title-row">
                        <div class="card-title-left">
                            <div class="card-icon-badge" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </div>
                            <div>
                                <h2>Security &amp; Password</h2>
                                <div class="card-subtitle">Protect your account with a secure, confidential password.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Alerts for Password -->
                    <?php if (!empty($password_msg)): ?>
                        <div class="profile-alert success" role="alert">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span><?php echo h($password_msg); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($password_err)): ?>
                        <div class="profile-alert error" role="alert">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span><?php echo h($password_err); ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo $base_url; ?>/index.php?page=account_settings#securitySection" onsubmit="return validatePasswordForm();" class="profile-form">
                        <input type="hidden" name="update_password" value="1">

                        <div class="profile-form-grid">
                            <div class="form-group">
                                <label class="form-label" for="current_password">Current Password *</label>
                                <div class="form-input-wrap">
                                    <input type="password" id="current_password" name="current_password" class="form-control"
                                           placeholder="Enter your current password" required autocomplete="current-password">
                                    <button type="button" class="btn-toggle-pw" onclick="togglePasswordVisibility('current_password')" aria-label="Toggle password visibility">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label" for="new_password">New Password *</label>
                                    <div class="form-input-wrap">
                                        <input type="password" id="new_password" name="new_password" class="form-control"
                                               placeholder="Minimum 6 characters" required minlength="6" autocomplete="new-password">
                                        <button type="button" class="btn-toggle-pw" onclick="togglePasswordVisibility('new_password')" aria-label="Toggle password visibility">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </button>
                                    </div>
                                    <span class="password-requirements">Password must be at least 6 characters long.</span>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="confirm_password">Confirm New Password *</label>
                                    <div class="form-input-wrap">
                                        <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                                               placeholder="Re-enter new password" required autocomplete="new-password">
                                        <button type="button" class="btn-toggle-pw" onclick="togglePasswordVisibility('confirm_password')" aria-label="Toggle password visibility">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-action-row">
                                <button type="submit" class="btn-primary-action">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                    <span>Update Password</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </section>

            </div>
        </div>
    </main>

    <!-- ============================================================
         FOOTER
         ============================================================ -->
    <footer class="footer" style="background-color: #F0EDED; border-top: 1px solid #E5E7EB; padding: 48px 24px;">
        <div style="max-width: 1280px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <img src="<?php echo $base_url; ?>/assets/images/home/node-210.png" alt="Monet Nails" style="height: 36px; width: auto;">
                <span style="font-family: var(--mn-serif); font-size: 18px; color: var(--mn-dark); font-weight: 600;">Monet Nails Atelier</span>
            </div>
            <p style="font-size: 13px; color: #574147; margin: 0;">
                &copy; <?php echo date('Y'); ?> Monet Nails Bar. All rights reserved. Artistry in every touch.
            </p>
        </div>
    </footer>

    <!-- ============================================================
         INTERACTIVE JAVASCRIPT
         ============================================================ -->
    <script>
        // 1. File Picker trigger
        function openFilePicker() {
            var input = document.getElementById("photoInput");
            if (input) input.click();
        }

        // 2. Instant client-side live preview on file selection
        var photoInput = document.getElementById("photoInput");
        if (photoInput) {
            photoInput.addEventListener("change", function () {
                var file = this.files[0];
                if (!file) return;

                // Client-side size check (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert("The chosen photo exceeds the 2MB size limit. Please choose a smaller image.");
                    this.value = "";
                    return;
                }

                var reader = new FileReader();
                reader.onload = function (event) {
                    var img = document.getElementById("avatarPreviewImg");
                    var initialWrap = document.getElementById("avatarInitialWrap");
                    if (img) {
                        img.src = event.target.result;
                        img.style.display = "block";
                    }
                    if (initialWrap) {
                        initialWrap.style.display = "none";
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        // 3. Client-side password validation
        function validatePasswordForm() {
            var newPw  = document.getElementById("new_password").value;
            var confPw = document.getElementById("confirm_password").value;

            if (newPw.length < 6) {
                alert("New password must be at least 6 characters long.");
                document.getElementById("new_password").focus();
                return false;
            }

            if (newPw !== confPw) {
                alert("New password and confirm password do not match.");
                document.getElementById("confirm_password").focus();
                return false;
            }

            return true;
        }

        // 4. Toggle password visibility
        function togglePasswordVisibility(inputId) {
            var input = document.getElementById(inputId);
            if (!input) return;
            if (input.type === "password") {
                input.type = "text";
            } else {
                input.type = "password";
            }
        }
    </script>
</body>
</html>
