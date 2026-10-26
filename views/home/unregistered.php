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
  <link rel="stylesheet" href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/css/home.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- ============ TOP NAV ============ -->
<?php
$activeNav = 'home';
require __DIR__ . '/../../includes/header.php';
?>

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
      <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=services" class="btn-outline" id="hero-services-btn">View Services</a>
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

    <!-- Card 1: Signature Manicure (wide: text on left, image on right) -->
    <article class="exp-card card-manicure-wide" id="card-manicure">
      <div class="card-content">
        <span class="card-tag">Signature</span>
        <h3>Signature Manicure</h3>
        <p>A comprehensive treatment focusing on cuticle care, nail
           shaping, and a flawless application of premium lacquer,
           finished with a relaxing hand massage.</p>
      </div>
      <div class="card-image-wrap">
        <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/image-54.png" alt="Signature Manicure">
      </div>
    </article>

    <!-- Card 2: Botanical Spa Pedicure (text only) -->
    <article class="exp-card card-pedicure" id="card-pedicure">
      <div class="card-content">
        <span class="card-tag">Pedicure</span>
        <h3>Botanical Spa Pedicure</h3>
        <p>Rejuvenate tired feet with our botanical soak,
           exfoliating sugar scrub, and deeply hydrating
           mask, leaving skin soft and renewed.</p>
      </div>
    </article>

    <!-- Card 3: Bespoke Nail Art & Styles (split row: text on left, image on right) -->
    <article class="card-split" id="card-nailart">
      <div class="card-text">
        <span class="card-tag">Artistry</span>
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
  <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/home/GalleryIcon.jpg" alt="" class="welcome-icon" style="opacity: 1 !important;">
  <h2>Treat your fingertips</h2>
  <div class="discount">Explore our latest collection</div>
  <p>Embrace the art of self-care. Order your Nail collection and treatment
     today to experience the Monet Nails difference.</p>
  <a href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=gallery" class="btn-dark" id="claim-offer-btn">View Gallery</a>
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
<?php require __DIR__ . '/../../includes/footer.php'; ?>


</body>
</html>
