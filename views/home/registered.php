<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Monet Nails — Nail Perfection At Your Fingertips</title>
  <meta name="description" content="Monet Nails Bar — Experience the intersection of classical artistry and modern high-end luxury. Book your appointment today.">

  <!-- Google Fonts: Bodoni Moda (headings) + Manrope (body) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..900;1,6..96,400..900&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Home stylesheet (MVC: assets/css/home.css) -->
  <link rel="stylesheet" href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/css/home.css">
</head>
<body>

<!-- ============ TOP NAV ============ -->
<nav class="top-nav" id="top-nav">
  <div class="nav-inner">
    <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/" id="nav-logo">
      <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/node-210.png" alt="Monet Nails" class="logo-img">
    </a>
    <div class="nav-links">
      <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/" class="active" id="nav-home">Home</a>
      <a href="about.php" id="nav-about">About</a>
      <a href="services.php" id="nav-services">Services</a>
      <a href="index.php?page=gallery" id="nav-gallery">Gallery</a>
      <a href="index.php?page=cart" id="nav-cart">Cart</a>
      <a href="contact.php" id="nav-contact">Contact</a>
    </div>
    <div class="nav-actions">
      <!-- Cart Icon -->
      <a href="index.php?page=cart" class="nav-cart-btn" title="Cart" id="nav-cart-icon">
        <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home_registered/vector-226.svg" alt="Cart">
      </a>

      <!-- Profile Avatar Button -->
      <div class="profile-btn" id="profileBtn" title="Profile">
        <?php echo $avatar_html ?? '<span class="avatar-initials">M</span>'; ?>
      </div>

      <!-- Profile Dropdown Popup -->
      <div class="profile-popup" id="profilePopup">
        <div class="popup-header">
          <div class="popup-avatar">
            <?php echo $avatar_html ?? '<span class="avatar-initials">M</span>'; ?>
          </div>
          <div>
            <div class="popup-name"><?php echo htmlspecialchars($customer_name ?? 'Client'); ?></div>
            <div class="popup-email"><?php echo htmlspecialchars($customer_email ?? ''); ?></div>
          </div>
        </div>

        <div class="popup-menu">
          <a href="my_appointments.php" class="popup-item" id="popup-appointments">
            <!-- calendar icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="#A9275E" stroke-width="1.8">
              <rect x="3" y="5" width="18" height="16" rx="2"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
              <line x1="8" y1="3" x2="8" y2="7"/>
              <line x1="16" y1="3" x2="16" y2="7"/>
            </svg>
            My Appointments
          </a>
          <a href="order_history.php" class="popup-item" id="popup-orders">
            <!-- shopping bag icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="#A9275E" stroke-width="1.8">
              <path d="M6 8h12l-1 12H7L6 8z"/>
              <path d="M9 8V6a3 3 0 0 1 6 0v2"/>
            </svg>
            Order History &amp; Custom Sets
          </a>
          <a href="account_settings.php" class="popup-item" id="popup-settings">
            <!-- gear icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="#A9275E" stroke-width="1.8">
              <circle cx="12" cy="12" r="3"/>
              <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
            Account Settings
          </a>
        </div>

        <div class="popup-footer">
          <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=logout" class="popup-item" id="popup-logout">
            <!-- sign out icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="#A9275E" stroke-width="1.8">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Sign Out
          </a>
        </div>
      </div>
    </div>
  </div>
</nav>

<!-- popup backdrop -->
<div class="popup-overlay" id="popupOverlay"></div>

<!-- ============ TRUST BAR ============ -->
<section class="trust-bar" id="trust-bar">
  <div class="trust-inner">
    <div class="trust-item">
      <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-7.svg" alt="Hygienic tools icon">
      <span>Hygienic Tools</span>
    </div>
    <div class="v-divider"></div>
    <div class="trust-item">
      <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-13.svg" alt="Expert therapists icon">
      <span>Expert Therapists</span>
    </div>
    <div class="v-divider"></div>
    <div class="trust-item">
      <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-19.svg" alt="Premium products icon">
      <span>Premium Products</span>
    </div>
  </div>
</section>

<!-- ============ HERO ============ -->
<section class="hero" id="hero">
  <div class="hero-bg">
    <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/node-24.png" alt="Monet Nails salon interior">
  </div>
  <div class="hero-content">
    <h1>
      Nail Perfection
      <span class="pink">At Your Fingertips</span>
    </h1>
    <p class="hero-desc">
      Experience the intersection of classical artistry and modern high-end luxury.
      A ritualistic pampering for the discerning clientele.
    </p>
    <div class="hero-btns">
      <a href="booking.php" class="btn-dark" id="hero-book-btn">Book an Appointment</a>
      <a href="services.php" class="btn-outline" id="hero-services-btn">View Services</a>
    </div>
  </div>
</section>

<!-- ============ CURATED EXPERIENCES ============ -->
<section class="curated" id="curated-experiences">
  <div class="section-title">
    <h2>Curated Experiences</h2>
    <p>Discover our signature treatments designed to elevate your natural beauty
       through meticulous craftsmanship.</p>
  </div>

  <div class="curated-grid">

    <!-- Card 1: Pedicure (text only) -->
    <article class="exp-card" id="card-pedicure">
      <div>
        <span class="card-tag">Pedicure</span>
        <h3>Botanical Spa Pedicure</h3>
        <p>Rejuvenate tired feet with our botanical soak,
           exfoliating sugar scrub, and deeply hydrating
           mask, leaving skin soft and renewed.</p>
      </div>
    </article>

    <!-- Card 2: Manicure (with faded image watermark on top) -->
    <article class="exp-card card-with-image" id="card-manicure">
      <div class="card-img-bg">
        <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/image-54.png" alt="">
      </div>
      <div>
        <span class="card-tag">Manicure</span>
        <h3>Signature Manicure</h3>
        <p>A comprehensive treatment focusing on cuticle care, nail
           shaping, and a flawless application of premium lacquer,
           finished with a relaxing hand massage.</p>
      </div>
    </article>

    <!-- Card 3: Nail Art (text + image split) -->
    <article class="exp-card card-split" id="card-nailart">
      <div class="card-text">
        <span class="card-tag">Nail Art</span>
        <h3>Bespoke Nail Art &amp; Styles</h3>
        <p>Express your unique personality with our custom nail art. From
           minimalist line work to intricate hand-painted designs and 3D
           embellishments.</p>
      </div>
      <div class="card-image">
        <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/node-72.png" alt="Bespoke nail art">
      </div>
    </article>

  </div>
</section>

<!-- ============ WELCOME OFFER ============ -->
<section class="welcome-offer" id="welcome-offer">
  <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-75.svg" alt="" class="welcome-icon">
  <h2>Exclusive Welcome Offer</h2>
  <div class="discount">20% OFF Your First Visit</div>
  <p>Embrace the art of self-care. Book your initial consultation and treatment
     today to experience the Monet Nails difference.</p>
  <a href="booking.php" class="btn-dark" id="claim-offer-btn">Claim Offer</a>
</section>

<!-- ============ ABOUT US ============ -->
<section class="about-us" id="about-us">
  <div class="about-img-wrap">
    <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/node-87.png" alt="Owner of Monet Nails holding a certificate of completion">
    <div class="about-badge">
      <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-90.svg" alt="Certified professional badge">
    </div>
  </div>
  <div class="about-text">
    <span class="card-tag">Our Philosophy</span>
    <h2>Artistry &amp; Precision in Every Detail</h2>
    <p>At Monet Nails, we view nail care not merely as a routine, but as a form of self-
       expression and ritualistic pampering. Inspired by classical artistry, our approach
       marries meticulous technique with modern luxury.</p>
    <p>Our sanctuary is designed to offer tranquil indulgence—a minimalist space
       where refined craftsmanship takes center stage. We use only the finest,
       premium products to ensure lasting beauty and health for your nails.</p>
  </div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<section class="testimonials" id="testimonials">
  <div class="testimonials-inner">
    <div class="section-title">
      <h2>Client Reverie</h2>
      <p>Hear from those who have experienced the tranquil indulgence.</p>
    </div>

    <div class="testimonials-grid">

      <!-- Testimonial 1 -->
      <article class="testimonial-card" id="testimonial-1">
        <div class="stars">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-113.svg" alt="star">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-115.svg" alt="star">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-117.svg" alt="star">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-119.svg" alt="star">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-121.svg" alt="star">
        </div>
        <p class="testimonial-quote">"An absolute oasis of calm. The attention to
           detail during my signature manicure was unparalleled. The minimalist,
           gallery-like space truly makes you feel pampered."</p>
        <p class="testimonial-name">— Sarah J.</p>
      </article>

      <!-- Testimonial 2 -->
      <article class="testimonial-card" id="testimonial-2">
        <div class="stars">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-129.svg" alt="star">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-131.svg" alt="star">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-133.svg" alt="star">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-135.svg" alt="star">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-137.svg" alt="star">
        </div>
        <p class="testimonial-quote">"The Botanical Spa Pedicure is a must. The
           environment is so clean and professional, and the aesthetic of the
           salon itself is incredibly inspiring. A true luxury experience."</p>
        <p class="testimonial-name">— Emily R.</p>
      </article>

      <!-- Testimonial 3 -->
      <article class="testimonial-card" id="testimonial-3">
        <div class="stars">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-145.svg" alt="star">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-147.svg" alt="star">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-149.svg" alt="star">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-151.svg" alt="star">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-153.svg" alt="star">
        </div>
        <p class="testimonial-quote">"I appreciate the focus on hygiene and the
           high-quality products used. My nails have never looked healthier or
           more elegant. Monet Nails sets a new standard."</p>
        <p class="testimonial-name">— Jessica M.</p>
      </article>

    </div>
  </div>
</section>

<!-- ============ FOOTER ============ -->
<footer class="site-footer" id="site-footer">
  <div class="footer-grid">

    <div class="footer-brand">
      <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/" class="footer-logo" id="footer-logo">
        <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/node-161.png" alt="Monet Nails">
      </a>
      <p class="brand-tagline">Artistry in every touch. Elevating nail care to a fine art.</p>
      <p class="brand-copy">&copy; 2024 Monet Nails. Artistry in every touch.</p>
    </div>

    <div class="footer-col">
      <h4>Explore</h4>
      <a href="services.php" id="footer-services">Services</a>
      <a href="booking.php" id="footer-booking">Booking</a>
      <a href="about.php" id="footer-about">About Us</a>
      <a href="privacy.php" id="footer-privacy">Privacy Policy</a>
    </div>

    <div class="footer-col">
      <h4>Contact</h4>
      <div class="contact-row">
        <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-184.svg" alt="Address icon">
        <span>No 90/1, 4th Lane, Werellawatta, Yakkala</span>
      </div>
      <div class="contact-row">
        <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-188.svg" alt="Phone icon">
        <span>+94 77 210 5954</span>
      </div>
      <div class="contact-row">
        <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-192.svg" alt="Email icon">
        <span>hello@monetnails.com</span>
      </div>
    </div>

    <div class="footer-col">
      <h4>Follow Us</h4>
      <div class="social-row">
        <a href="#" id="social-instagram" aria-label="Instagram">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-201.svg" alt="Instagram">
        </a>
        <a href="#" id="social-facebook" aria-label="Facebook">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-204.svg" alt="Facebook">
        </a>
        <a href="#" id="social-twitter" aria-label="Twitter">
          <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/icon-207.svg" alt="Twitter">
        </a>
      </div>
    </div>

  </div>
</footer>

<script>
  /* Profile popup toggle — click profile button to open, click outside or overlay to close */
  (function () {
    var btn     = document.getElementById('profileBtn');
    var popup   = document.getElementById('profilePopup');
    var overlay = document.getElementById('popupOverlay');

    function togglePopup(e) {
      e.stopPropagation();
      var isOpen = popup.classList.toggle('show');
      overlay.classList.toggle('show', isOpen);
    }

    function closePopup() {
      popup.classList.remove('show');
      overlay.classList.remove('show');
    }

    if (btn && popup) {
      btn.addEventListener('click', togglePopup);
      // Click inside popup should not close it
      popup.addEventListener('click', function (e) { e.stopPropagation(); });
      // Click on overlay closes
      overlay.addEventListener('click', closePopup);
      // ESC closes
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closePopup();
      });
    }
  })();
</script>
</body>
</html>
