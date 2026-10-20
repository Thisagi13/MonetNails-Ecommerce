<?php
/**
 * views/contact/contact.php — Contact Us Page View
 * Monet Nails MVC Application
 *
 * Displays the contact form and salon info cards.
 * Uses the unified header (adjusts automatically for guest / logged-in user).
 */

$base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us — Monet Nails Atelier</title>
    <meta name="description" content="Get in touch with Monet Nails. Send us a message, visit our atelier, or find our business hours and contact details.">

    <!-- Fonts: Bodoni Moda (headings) + Manrope (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..700&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Header CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/header.css">

    <!-- Contact Page CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/contact.css">
</head>
<body class="contact-page">

    <!-- ============================================================
         UNIFIED SITE HEADER (Registered / Unregistered auto-adjusted)
         ============================================================ -->
    <?php
    $activeNav = 'contact';
    require __DIR__ . '/../../includes/header.php';
    ?>

    <!-- ============================================================
         MAIN CONTENT
         ============================================================ -->
    <main class="contact-main">

        <!-- Hero Section -->
        <section class="contact-hero">
            <img src="<?php echo $base_url; ?>/assets/images/home/node-161.png"
                 alt=""
                 class="contact-hero-watermark"
                 aria-hidden="true">
            <h1>Get in Touch</h1>
            <p class="contact-hero-sub">
                Personalized artistry awaits. Reach out to schedule your ritual or inquire about our bespoke nail services.
            </p>
        </section>

        <!-- Bento Grid: form left, info right -->
        <section class="contact-bento">

            <!-- ─── Left: Contact Form ─── -->
            <div class="contact-card form-card">
                <h2 class="form-card-title">Send a Message</h2>

                <!-- Success Banner -->
                <?php if ($sent): ?>
                    <div class="contact-alert success" id="contactSuccess" role="alert">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <span>Thank you! Your message has been sent. We will get back to you shortly.</span>
                    </div>
                <?php endif; ?>

                <!-- Error Banner -->
                <?php if (!empty($error_msg)): ?>
                    <div class="contact-alert error" id="contactError" role="alert">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <span><?php echo htmlspecialchars($error_msg); ?></span>
                    </div>
                <?php endif; ?>

                <form class="contact-form"
                      method="POST"
                      action="<?php echo $base_url; ?>/index.php?page=contact"
                      id="contactForm"
                      novalidate>

                    <!-- Name + Email row -->
                    <div class="form-row-2">
                        <div class="form-field">
                            <label for="contactName">Name</label>
                            <input type="text"
                                   id="contactName"
                                   name="name"
                                   placeholder="Your full name"
                                   value="<?php echo htmlspecialchars($fields['name']); ?>"
                                   <?php if ($isLoggedIn && !empty($fields['name'])): ?>readonly<?php endif; ?>
                                   required>
                            <?php if ($isLoggedIn && !empty($fields['name'])): ?>
                                <span class="field-hint">Auto-filled from your account</span>
                            <?php endif; ?>
                        </div>

                        <div class="form-field">
                            <label for="contactEmail">Email</label>
                            <input type="email"
                                   id="contactEmail"
                                   name="email"
                                   placeholder="your@email.com"
                                   value="<?php echo htmlspecialchars($fields['email']); ?>"
                                   <?php if ($isLoggedIn && !empty($fields['email'])): ?>readonly<?php endif; ?>
                                   required>
                            <?php if ($isLoggedIn && !empty($fields['email'])): ?>
                                <span class="field-hint">Auto-filled from your account</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Subject -->
                    <div class="form-field">
                        <label for="contactSubject">Subject</label>
                        <input type="text"
                               id="contactSubject"
                               name="subject"
                               placeholder="How can we help you?"
                               value="<?php echo htmlspecialchars($fields['subject']); ?>"
                               required>
                    </div>

                    <!-- Message -->
                    <div class="form-field">
                        <label for="contactMessage">Message</label>
                        <textarea id="contactMessage"
                                  name="message"
                                  placeholder="Tell us about your desired artistry, a question, or anything else..."
                                  required><?php echo htmlspecialchars($fields['message']); ?></textarea>
                    </div>

                    <!-- Submit button -->
                    <button type="submit" class="contact-send-btn" id="btnSendMessage">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                        Send Message
                    </button>

                </form>
            </div>

            <!-- ─── Right: Info Column ─── -->
            <div class="info-column">

                <!-- Visit Us card -->
                <div class="visit-card">
                    <div class="info-card-heading">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <h2 class="info-card-title">Visit Us</h2>
                    </div>
                    <p class="visit-address">No 90/1, 4th Lane, Werellawatta, Yakkala</p>

                    <!-- Google Maps embed (static) -->
                    <iframe
                        class="map-frame"
                        title="Monet Nails location"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.5!2d80.0667!3d7.0847!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNcKwMDUnMDMuMCJOIDgwwrAwNCcwMC4xIkU!5e0!3m2!1sen!2slk!4v1000000000000"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

                <!-- Connect + Business Hours card -->
                <div class="connect-card">

                    <!-- Connect block -->
                    <div class="connect-block">
                        <div class="info-card-heading">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <h2 class="info-card-title">Connect</h2>
                        </div>

                        <div class="connect-lines">
                            <a href="tel:+94772105954" class="connect-line" id="linkPhone">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.77 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <span>+94 77 210 5954</span>
                            </a>

                            <a href="mailto:hello@monetnails.com" class="connect-line" id="linkEmail">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <span>hello@monetnails.com</span>
                            </a>
                        </div>
                    </div>

                    <hr class="block-divider">

                    <!-- Business Hours block -->
                    <div class="hours-block">
                        <div class="info-card-heading">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <h2 class="info-card-title">Business Hours</h2>
                        </div>

                        <div class="hours-list">
                            <div class="hours-row">
                                <span class="hours-day">Mon – Fri</span>
                                <span class="hours-time">9:00 am – 7:00 pm</span>
                            </div>
                            <div class="hours-row">
                                <span class="hours-day">Saturday</span>
                                <span class="hours-time">10:00 am – 6:00 pm</span>
                            </div>
                            <div class="hours-row closed">
                                <span class="hours-day">Sunday</span>
                                <span class="hours-time">Closed</span>
                            </div>
                        </div>
                    </div>

                    <!-- Subtle watermark -->
                    <img src="<?php echo $base_url; ?>/assets/images/home/node-161.png"
                         alt=""
                         class="card-watermark"
                         aria-hidden="true">
                </div>

            </div>
        </section>

    </main>

    <!-- ============================================================
         SITE FOOTER
         ============================================================ -->
    <footer class="contact-footer">
        <div class="contact-footer-inner">

            <div class="footer-brand">
                <div class="footer-brand-title">Monet Nails</div>
                <p class="footer-tagline">Artistry in every touch. Elevating nail care to a fine art.</p>
                <p class="footer-copy">&copy; <?php echo date('Y'); ?> Monet Nails. All rights reserved.</p>
            </div>

            <div class="footer-col">
                <h3>Explore</h3>
                <a href="<?php echo $base_url; ?>/">Home</a>
                <a href="<?php echo $base_url; ?>/index.php?page=services">Services</a>
                <a href="<?php echo $base_url; ?>/index.php?page=booking">Booking</a>
                <a href="<?php echo $base_url; ?>/index.php?page=gallery">Gallery</a>
            </div>

            <div class="footer-col">
                <h3>Contact</h3>
                <p>No 90/1, 4th Lane, Werellawatta, Yakkala</p>
                <p>+94 77 210 5954</p>
                <p>hello@monetnails.com</p>
            </div>

            <div class="footer-col">
                <h3>Follow Us</h3>
                <div class="social-row">
                    <a href="#" class="social-btn" aria-label="Instagram" id="linkInstagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                        </svg>
                    </a>
                    <a href="#" class="social-btn" aria-label="Facebook" id="linkFacebook">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                    </a>
                    <a href="#" class="social-btn" aria-label="Twitter/X" id="linkTwitter">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </footer>

    <!-- ============================================================
         CLIENT-SIDE FORM VALIDATION
         ============================================================ -->
    <script>
    (function () {
        var form    = document.getElementById('contactForm');
        var nameEl  = document.getElementById('contactName');
        var emailEl = document.getElementById('contactEmail');
        var subjEl  = document.getElementById('contactSubject');
        var msgEl   = document.getElementById('contactMessage');
        var btn     = document.getElementById('btnSendMessage');

        if (!form) return;

        form.addEventListener('submit', function (e) {
            var name    = nameEl  ? nameEl.value.trim()  : '';
            var email   = emailEl ? emailEl.value.trim() : '';
            var subject = subjEl  ? subjEl.value.trim()  : '';
            var message = msgEl   ? msgEl.value.trim()   : '';

            if (!name || !email || !subject || !message) {
                e.preventDefault();
                showError('Please fill in all the fields.');
                return;
            }

            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                showError('Please enter a valid email address.');
                return;
            }

            // Show loading state
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 0.8s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg> Sending...';
            }
        });

        function showError(msg) {
            var existing = document.getElementById('contactError');
            if (existing) { existing.remove(); }

            var el = document.createElement('div');
            el.className = 'contact-alert error';
            el.id = 'contactError';
            el.setAttribute('role', 'alert');
            el.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>'
                          + '<span>' + msg + '</span>';
            form.insertBefore(el, form.firstChild);
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    })();
    </script>
    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>

</body>
</html>
