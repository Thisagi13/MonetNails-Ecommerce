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
                <img src="<?php echo $base_url; ?>/assets/images/home/image-54.png"
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
    <footer class="about-footer">
        <div class="about-footer-inner">

            <div class="footer-brand">
                <div class="footer-brand-title">Monet Nails</div>
                <p>Artistry in every touch. Elevating nail care to a fine art.</p>
                <p class="copyright">&copy; <?php echo date('Y'); ?> Monet Nails. All rights reserved.</p>
            </div>

            <div class="footer-col">
                <h4>Explore</h4>
                <a href="<?php echo $base_url; ?>/">Home</a>
                <a href="<?php echo $base_url; ?>/index.php?page=services">Services</a>
                <a href="<?php echo $base_url; ?>/index.php?page=booking">Booking</a>
                <a href="<?php echo $base_url; ?>/index.php?page=gallery">Gallery</a>
                <a href="<?php echo $base_url; ?>/index.php?page=contact">Contact</a>
            </div>

            <div class="footer-col">
                <h4>Contact</h4>
                <div class="contact-item">
                    <!-- Location icon -->
                    <svg viewBox="0 0 10 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.66667 5.83333C4.9875 5.83333 5.26215 5.7191 5.49062 5.49062C5.7191 5.26215 5.83333 4.9875 5.83333 4.66667C5.83333 4.34583 5.7191 4.07118 5.49062 3.84271C5.26215 3.61424 4.9875 3.5 4.66667 3.5C4.34583 3.5 4.07118 3.61424 3.84271 3.84271C3.61424 4.07118 3.5 4.34583 3.5 4.66667C3.5 4.9875 3.61424 5.26215 3.84271 5.49062C4.07118 5.7191 4.34583 5.83333 4.66667 5.83333ZM4.66667 10.1208C5.85278 9.03194 6.73264 8.04271 7.30625 7.15312C7.87986 6.26354 8.16667 5.47361 8.16667 4.78333C8.16667 3.72361 7.82882 2.8559 7.15312 2.18021C6.47743 1.50451 5.64861 1.16667 4.66667 1.16667C3.68472 1.16667 2.8559 1.50451 2.18021 2.18021C1.50451 2.8559 1.16667 3.72361 1.16667 4.78333C1.16667 5.47361 1.45347 6.26354 2.02708 7.15312C2.60069 8.04271 3.48056 9.03194 4.66667 10.1208ZM4.66667 11.6667C3.10139 10.3347 1.93229 9.09757 1.15937 7.95521C0.386458 6.81285 0 5.75556 0 4.78333C0 3.325 0.469097 2.16319 1.40729 1.29792C2.34549 0.432639 3.43194 0 4.66667 0C5.90139 0 6.98785 0.432639 7.92604 1.29792C8.86424 2.16319 9.33333 3.325 9.33333 4.78333C9.33333 5.75556 8.94688 6.81285 8.17396 7.95521C7.40104 9.09757 6.23194 10.3347 4.66667 11.6667Z" fill="currentColor"/>
                    </svg>
                    <span>No 90/1, 4th Lane, Werellawatta, Yakkala</span>
                </div>
                <div class="contact-item">
                    <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.8875 10.5C8.67222 10.5 7.47153 10.2351 6.28542 9.70521C5.09931 9.17535 4.02014 8.4243 3.04792 7.45208C2.07569 6.47986 1.32465 5.40069 0.794792 4.21458C0.264931 3.02847 0 1.82778 0 0.6125C0 0.4375 0.0583333 0.291667 0.175 0.175C0.291667 0.0583333 0.4375 0 0.6125 0H2.975C3.11111 0 3.23264 0.0461806 3.33958 0.138542C3.44653 0.230903 3.50972 0.340278 3.52917 0.466667L3.90833 2.50833C3.92778 2.66389 3.92292 2.79514 3.89375 2.90208C3.86458 3.00903 3.81111 3.10139 3.73333 3.17917L2.31875 4.60833C2.51319 4.96806 2.7441 5.31562 3.01146 5.65104C3.27882 5.98646 3.57292 6.30972 3.89375 6.62083C4.19514 6.92222 4.51111 7.20174 4.84167 7.45937C5.17222 7.71701 5.52222 7.95278 5.89167 8.16667L7.2625 6.79583C7.35 6.70833 7.46424 6.64271 7.60521 6.59896C7.74618 6.55521 7.88472 6.54306 8.02083 6.5625L10.0333 6.97083C10.1694 7.00972 10.2812 7.08021 10.3687 7.18229C10.4562 7.28437 10.5 7.39861 10.5 7.525V9.8875C10.5 10.0625 10.4417 10.2083 10.325 10.325C10.2083 10.4417 10.0625 10.5 9.8875 10.5Z" fill="currentColor"/>
                    </svg>
                    <span>+94 77 210 5954</span>
                </div>
                <div class="contact-item">
                    <svg viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.16667 9.33333C0.845833 9.33333 0.571181 9.2191 0.342708 8.99063C0.114236 8.76215 0 8.4875 0 8.16667V1.16667C0 0.845833 0.114236 0.571181 0.342708 0.342708C0.571181 0.114236 0.845833 0 1.16667 0H10.5C10.8208 0 11.0955 0.114236 11.324 0.342708C11.5524 0.571181 11.6667 0.845833 11.6667 1.16667V8.16667C11.6667 8.4875 11.5524 8.76215 11.324 8.99063C11.0955 9.2191 10.8208 9.33333 10.5 9.33333H1.16667ZM5.83333 5.25L1.16667 2.33333V8.16667H10.5V2.33333L5.83333 5.25ZM5.83333 4.08333L10.5 1.16667H1.16667L5.83333 4.08333Z" fill="currentColor"/>
                    </svg>
                    <span>hello@monetnails.com</span>
                </div>
            </div>

            <div class="footer-col">
                <h4>Follow Us</h4>
                <div class="social-icons">
                    <a href="#" aria-label="Instagram" id="linkAboutInstagram">
                        <svg viewBox="0 0 17 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.33333 12.0833C9.375 12.0833 10.2604 11.7188 10.9896 10.9896C11.7188 10.2604 12.0833 9.375 12.0833 8.33333C12.0833 7.29167 11.7188 6.40625 10.9896 5.67708C10.2604 4.94792 9.375 4.58333 8.33333 4.58333C7.29167 4.58333 6.40625 4.94792 5.67708 5.67708C4.94792 6.40625 4.58333 7.29167 4.58333 8.33333C4.58333 9.375 4.94792 10.2604 5.67708 10.9896C6.40625 11.7188 7.29167 12.0833 8.33333 12.0833ZM1.66667 15C1.20833 15 0.815972 14.8368 0.489583 14.5104C0.163194 14.184 0 13.7917 0 13.3333V3.33333C0 2.875 0.163194 2.48264 0.489583 2.15625C0.815972 1.82986 1.20833 1.66667 1.66667 1.66667H4.29167L5.83333 0H10.8333L12.375 1.66667H15C15.4583 1.66667 15.8507 1.82986 16.1771 2.15625C16.5035 2.48264 16.6667 2.875 16.6667 3.33333V13.3333C16.6667 13.7917 16.5035 14.184 16.1771 14.5104C15.8507 14.8368 15.4583 15 15 15H1.66667Z" fill="currentColor"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="Facebook" id="linkAboutFacebook">
                        <svg viewBox="0 0 10 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3.33333 15C2.41667 15 1.63194 14.6736 0.979167 14.0208C0.326389 13.3681 0 12.5833 0 11.6667C0 10.75 0.326389 9.96528 0.979167 9.3125C1.63194 8.65972 2.41667 8.33333 3.33333 8.33333C3.65278 8.33333 3.94792 8.37153 4.21875 8.44792C4.48958 8.52431 4.75 8.63889 5 8.79167V0H10V3.33333H6.66667V11.6667C6.66667 12.5833 6.34028 13.3681 5.6875 14.0208C5.03472 14.6736 4.25 15 3.33333 15Z" fill="currentColor"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="Pinterest" id="linkAboutPinterest">
                        <svg viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.3333 16.6667H4.16667V5.83333L10 0L11.0417 1.04167C11.1389 1.13889 11.2188 1.27083 11.2812 1.4375C11.3438 1.60417 11.375 1.76389 11.375 1.91667V2.20833L10.4583 5.83333H15.8333C16.2778 5.83333 16.6667 6 17 6.33333C17.3333 6.66667 17.5 7.05556 17.5 7.5V9.16667C17.5 9.26389 17.4861 9.36806 17.4583 9.47917C17.4306 9.59028 17.4028 9.69444 17.375 9.79167L14.875 15.6667C14.75 15.9444 14.5417 16.1806 14.25 16.375C13.9583 16.5694 13.6528 16.6667 13.3333 16.6667Z" fill="currentColor"/>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </footer>

</body>
</html>
