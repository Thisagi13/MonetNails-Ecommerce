<?php
/**
 * views/about/about.php — About Us Page View
 * Monet Nails MVC Application
 *
 * Displays the brand story, founder section, and philosophy pillars.
 * Uses the unified header — auto-adjusts for guest vs. logged-in customers.
 * No auth guard: accessible to all visitors.
 */

$base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us — Monet Nails Atelier</title>
    <meta name="description" content="Discover the story behind Monet Nails — a sanctuary where classical aesthetics meet contemporary precision in bespoke nail artistry.">

    <!-- Fonts: Bodoni Moda (headings) + Manrope (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..700&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Header CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/header.css">

    <!-- About Page CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/about.css">
</head>
<body>

    <!-- ============================================================
         UNIFIED SITE HEADER (auto-adjusts: guest / registered user)
         ============================================================ -->
    <?php
    $activeNav = 'about';
    require __DIR__ . '/../../includes/header.php';
    ?>

    <!-- ============================================================
         HERO SECTION
         ============================================================ -->
    <section class="about-hero">
        <!-- Animated blurred background blobs -->
        <div class="blob blob-1" aria-hidden="true"></div>
        <div class="blob blob-2" aria-hidden="true"></div>

        <h1>The Art of the Manicure</h1>
        <p>
            A sanctuary where classical aesthetics meet contemporary precision.
            We believe nail care is an art form — a delicate ritual of
            self-expression and refined indulgence.
        </p>
    </section>

    <!-- ============================================================
         OUR STORY
         ============================================================ -->
    <section class="our-story">
        <div class="our-story-text">
            <h2>Our Story</h2>
            <p>
                Monet Nails began as a vision to elevate the traditional salon
                experience into a gallery of craftsmanship. Inspired by the
                impressionist pursuit of beauty in transient moments, we
                established a space where every touch is deliberate and every
                detail matters.
            </p>
            <p>
                Our journey is rooted in a deep respect for both the timeless
                elegance of classic art and the innovative techniques of modern
                nail care. Here, you are not just receiving a service; you are
                partaking in a curated aesthetic experience designed to soothe
                the senses and celebrate personal style.
            </p>
        </div>
        <div class="our-story-img">
            <img src="<?php echo $base_url; ?>/assets/images/home/node-24.png"
                 alt="Monet Nails atelier interior">
        </div>
    </section>

    <!-- ============================================================
         MEET THE FOUNDER
         ============================================================ -->
    <section class="founder">
        <div class="founder-inner">

            <!-- Founder portrait -->
            <div class="founder-img-wrap">
                <img src="<?php echo $base_url; ?>/leha.jpeg"
                     alt="m.L.L.Methnara — Founder of Monet Nails">
            </div>

            <!-- Founder bio -->
            <div class="founder-text">
                <h2>Meet the Founder</h2>
                <span class="founder-name">m.L.L.Methnara</span>

                <div class="blockquote">
                    <!-- Quote mark icon (SVG inline) -->
                    <svg class="quote-icon" viewBox="0 0 41 34" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M8.45045 33.4821L12.0683 22.2685C11.9438 22.3769 11.7909 22.4668 11.6094 22.5381C11.428 22.6094 11.2432 22.6651 11.0551 22.7051C8.56898 23.2335 6.27233 22.8073 4.16515 21.4264C2.05796 20.0454 0.743236 18.1264 0.22096 15.6693C-0.30748 13.1832 0.11151 10.8881 1.47793 8.784C2.84435 6.6799 4.77061 5.36363 7.25672 4.83519C9.71383 4.31292 12.0017 4.73345 14.1203 6.09678C16.2389 7.46012 17.5618 9.38211 18.0891 11.8628C18.2729 12.7272 18.3391 13.5516 18.2879 14.3358C18.2367 15.1199 18.0817 15.8901 17.8231 16.6463L12.6639 32.5865L8.45045 33.4821ZM30.1766 28.8641L33.7944 17.6505C33.67 17.7589 33.5171 17.8487 33.3356 17.9201C33.1541 17.9914 32.9694 18.0471 32.7812 18.087C30.2951 18.6155 27.9985 18.1892 25.8913 16.8083C23.7841 15.4274 22.4694 13.5084 21.9471 11.0513C21.4153 8.54949 21.8335 6.25047 23.2016 4.1542C24.5697 2.05794 26.4968 0.745591 28.9829 0.217151C31.44 -0.305124 33.7278 0.115406 35.8464 1.47874C37.965 2.84208 39.288 4.76407 39.8153 7.24472C39.999 8.10919 40.0653 8.93352 40.0141 9.71771C39.9628 10.5019 39.8079 11.2721 39.5492 12.0283L34.3901 27.9685L30.1766 28.8641Z" fill="currentColor"/>
                    </svg>
                    <p>
                        Master Nail Technician and owner of Monet Nails Bar.
                        Experienced in salon environments, specializing in nail art
                        and customer service. Expert in advanced nail care techniques
                        including acrylics, gels, and bespoke nail art. Committed to
                        delivering a high-quality, premium customer experience.
                    </p>
                </div>
            </div>

        </div>
    </section>

    <!-- ============================================================
         OUR PHILOSOPHY
         ============================================================ -->
    <section class="philosophy">
        <div class="philosophy-header">
            <h2>Our Philosophy</h2>
            <p>The pillars that define the Monet Nails experience.</p>
        </div>

        <div class="phil-cards">

            <!-- Card 1: Artistic Vision -->
            <div class="phil-card">
                <div class="icon-bg">
                    <svg viewBox="0 0 22 21" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M3.87011 20.8022C3.15494 20.8022 2.44771 20.6451 1.74843 20.331C1.04915 20.017 0.466339 19.5971 0 19.0715C0.477559 18.9352 0.929475 18.6456 1.35575 18.2025C1.78202 17.7594 1.99516 17.1676 1.99516 16.4272C1.99516 15.5538 2.29763 14.8147 2.90259 14.2097C3.50755 13.6048 4.24672 13.3023 5.12011 13.3023C5.99349 13.3023 6.73267 13.6048 7.33763 14.2097C7.94258 14.8147 8.24506 15.5538 8.24506 16.4272C8.24506 17.6303 7.81668 18.6603 6.95992 19.517C6.10316 20.3738 5.07322 20.8022 3.87011 20.8022ZM3.87011 18.9272C4.55761 18.9272 5.14615 18.6824 5.63573 18.1929C6.12532 17.7033 6.37011 17.1147 6.37011 16.4272C6.37011 16.0731 6.25032 15.7762 6.01073 15.5366C5.77115 15.297 5.47428 15.1772 5.12011 15.1772C4.76594 15.1772 4.46907 15.297 4.22948 15.5366C3.9899 15.7762 3.87011 16.0731 3.87011 16.4272C3.87011 16.9064 3.81282 17.3439 3.69823 17.7397C3.58365 18.1356 3.43261 18.5106 3.24511 18.8647C3.34928 18.9064 3.45344 18.9272 3.55761 18.9272C3.66178 18.9272 3.76594 18.9272 3.87011 18.9272ZM10.5047 13.8551L7.71627 11.0667L18.423 0.36C18.6521 0.130833 18.9386 0.0110417 19.2823 0.000625C19.6261 -0.00979167 19.923 0.11 20.173 0.36L21.2114 1.39841C21.4614 1.64841 21.5864 1.94008 21.5864 2.27341C21.5864 2.60675 21.4614 2.89841 21.2114 3.14841L10.5047 13.8551Z" fill="currentColor"/>
                    </svg>
                </div>
                <h3>Artistic Vision</h3>
                <p>
                    Treating every nail as a miniature canvas. We prioritize
                    intricate design, perfect shaping, and a curated colour
                    palette inspired by fine art movements.
                </p>
            </div>

            <!-- Card 2: Uncompromising Hygiene -->
            <div class="phil-card">
                <div class="icon-bg">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M2.2596 23.1249C1.62819 23.1249 1.09374 22.9061 0.656244 22.4686C0.218748 22.0311 0 21.4967 0 20.8653V6.63455C0 6.00315 0.218748 5.46869 0.656244 5.0312C1.09374 4.5937 1.62819 4.37495 2.2596 4.37495H7.5V2.2596C7.5 1.62819 7.71875 1.09374 8.15624 0.656244C8.59374 0.218748 9.12819 0 9.7596 0H13.9903C14.6217 0 15.1562 0.218748 15.5937 0.656244C16.0312 1.09374 16.2499 1.62819 16.2499 2.2596V4.37495H21.4903C22.1217 4.37495 22.6562 4.5937 23.0937 5.0312C23.5312 5.46869 23.7499 6.00315 23.7499 6.63455V20.8653C23.7499 21.4967 23.5312 22.0311 23.0937 22.4686C22.6562 22.9061 22.1217 23.1249 21.4903 23.1249H2.2596ZM9.37495 4.37495H14.375V2.2596C14.375 2.16344 14.3349 2.07529 14.2548 1.99516C14.1746 1.91502 14.0865 1.87495 13.9903 1.87495H9.7596C9.66344 1.87495 9.57529 1.91502 9.49516 1.99516C9.41502 2.07529 9.37495 2.16344 9.37495 2.2596V4.37495ZM10.9375 14.6874V18.4374H12.8124V14.6874H16.5624V12.8124H12.8124V9.06243H10.9375V12.8124H7.18748V14.6874H10.9375Z" fill="currentColor"/>
                    </svg>
                </div>
                <h3>Uncompromising Hygiene</h3>
                <p>
                    Medical-grade sterilization processes ensure a pristine
                    environment. Your health and safety are the invisible
                    foundations of our luxury service.
                </p>
            </div>

            <!-- Card 3: Serene Ambiance -->
            <div class="phil-card">
                <div class="icon-bg">
                    <svg viewBox="0 0 24 23" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M11.8509 22.9325C10.4406 22.7615 9.03996 22.3862 7.64897 21.8067C6.25798 21.2271 5.008 20.3897 3.89903 19.2943C2.79007 18.199 1.88101 16.8132 1.17188 15.1369C0.462748 13.4606 0.0801341 11.4438 0.0240402 9.08647L0 7.95667L1.10576 7.98071C2.0721 8.00475 3.1358 8.18224 4.29686 8.51317C5.45792 8.84411 6.49197 9.29644 7.39902 9.87016C7.5769 8.19068 8.05445 6.44629 8.83169 4.637C9.60893 2.82771 10.5432 1.28205 11.6346 0C12.7259 1.28205 13.6602 2.83172 14.4374 4.64902C15.2147 6.46633 15.6922 8.21472 15.8701 9.8942C16.7531 9.34452 17.7671 8.89419 18.9121 8.54322C20.0572 8.19226 21.1329 8.00475 22.1393 7.98071L23.2691 7.95667L23.2451 9.11051C23.213 11.2435 22.8965 13.1061 22.2956 14.6982C21.6946 16.2903 20.8837 17.6421 19.8629 18.7534C18.8421 19.8648 17.649 20.7602 16.2836 21.4397C14.9182 22.1192 13.4407 22.6168 11.8509 22.9325Z" fill="currentColor"/>
                    </svg>
                </div>
                <h3>Serene Ambiance</h3>
                <p>
                    A curated atmosphere of calm. From the scent to the
                    soundscape, every element is designed to offer a tranquil
                    escape from the bustling world.
                </p>
            </div>

        </div>
    </section>

    <!-- ============================================================
         CALL TO ACTION STRIP
         ============================================================ -->
    <section class="about-cta">
        <h2>Ready to Experience the Atelier?</h2>
        <p>Book your bespoke appointment or explore our full range of services.</p>
        <div class="cta-btns">
            <a href="<?php echo $base_url; ?>/index.php?page=booking" class="cta-btn-primary" id="btnAboutBook">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Reserve an Appointment
            </a>
            <a href="<?php echo $base_url; ?>/index.php?page=services" class="cta-btn-secondary" id="btnAboutServices">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
                View Our Services
            </a>
        </div>
    </section>

    <!-- ============================================================
         SITE FOOTER
         ============================================================ -->
    <?php require __DIR__ . '/../../includes/footer.php'; ?>


</body>
</html>
