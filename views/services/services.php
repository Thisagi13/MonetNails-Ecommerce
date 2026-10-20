<?php
/**
 * views/services/services.php — Atelier Services View
 * Monet Nails MVC
 *
 * Displays services categorized into Manicures, Pedicures,
 * and Specialty Enhancements.
 * Uses unified includes/header.php to dynamically adjust between
 * unregistered guests and registered clients.
 */

if (!function_exists('srv_price_text')) {
    function srv_price_text($price): string {
        $num = (float)$price;
        if ($num <= 0) {
            return "";
        }
        return "Rs. " . number_format($num, 0, '.', ',');
    }
}

$base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services &amp; Atelier Treatments — Monet Nails</title>
    <meta name="description" content="Discover our curated manicure and pedicure treatments, bespoke nail art, and specialty enhancements at Monet Nails Bar.">

    <!-- Fonts: Bodoni Moda (headings) + Manrope (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..700&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Header CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/header.css">

    <!-- Services Specific CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/services.css">
</head>
<body class="services-page">

    <!-- ============================================================
         UNIFIED SITE HEADER (Registered / Unregistered auto-adjusted)
         ============================================================ -->
    <?php
    $activeNav = 'services';
    require __DIR__ . '/../../includes/header.php';
    ?>

    <!-- ============================================================
         HERO SECTION ("Elevated Artistry")
         ============================================================ -->
    <section class="srv-hero" id="services-hero">
        <div class="srv-hero-bg-accent"></div>
        <div class="srv-hero-text">
            <h1>Elevated Artistry</h1>
            <p>
                Experience a curated approach to nail care. Our services blend
                classical technique with premium lacquers to create enduring,
                personalized artistry for your hands and feet.
            </p>
        </div>
    </section>

    <!-- ============================================================
         SERVICES MENU (Manicures & Pedicures)
         ============================================================ -->
    <section class="srv-section" id="services-menu">
        <div class="srv-cols">

            <!-- ---------- COLUMN 1: MANICURES ---------- -->
            <div class="srv-col" id="col-manicures">
                <div class="srv-col-header">
                    <h2>Manicures</h2>
                    <div class="srv-divider"></div>
                </div>

                <div class="srv-list">
                    <?php if (!empty($manicures)): ?>
                        <?php foreach ($manicures as $item): ?>
                            <div class="srv-item">
                                <div class="srv-info">
                                    <div class="srv-title">
                                        <h3><?php echo htmlspecialchars($item['name'] ?? ''); ?></h3>
                                        <?php if (!empty($item['tag'])): ?>
                                            <span class="srv-badge"><?php echo htmlspecialchars($item['tag']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($item['duration'])): ?>
                                            <span class="srv-duration">&bull; <?php echo (int)$item['duration']; ?> mins</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="srv-desc"><?php echo htmlspecialchars($item['description'] ?? ''); ?></p>
                                </div>
                                <div class="srv-price"><?php echo srv_price_text($item['price'] ?? 0); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color:var(--srv-muted);">No manicure treatments currently listed.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ---------- COLUMN 2: PEDICURES ---------- -->
            <div class="srv-col" id="col-pedicures">
                <div class="srv-col-header">
                    <h2>Pedicures</h2>
                    <div class="srv-divider"></div>
                </div>

                <div class="srv-list">
                    <?php if (!empty($pedicures)): ?>
                        <?php foreach ($pedicures as $item): ?>
                            <div class="srv-item">
                                <div class="srv-info">
                                    <div class="srv-title">
                                        <h3><?php echo htmlspecialchars($item['name'] ?? ''); ?></h3>
                                        <?php if (!empty($item['tag'])): ?>
                                            <span class="srv-badge"><?php echo htmlspecialchars($item['tag']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($item['duration'])): ?>
                                            <span class="srv-duration">&bull; <?php echo (int)$item['duration']; ?> mins</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="srv-desc"><?php echo htmlspecialchars($item['description'] ?? ''); ?></p>
                                </div>
                                <div class="srv-price"><?php echo srv_price_text($item['price'] ?? 0); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color:var(--srv-muted);">No pedicure treatments currently listed.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- ---------- SPECIALTY ENHANCEMENTS ---------- -->
        <div class="srv-specialty" id="specialty-enhancements">
            <h2>Specialty Enhancements</h2>

            <div class="srv-enhance-grid">
                <?php if (!empty($specials)): ?>
                    <?php foreach ($specials as $item): ?>
                        <div class="srv-enhance-card">
                            <div>
                                <div class="srv-enhance-icon-wrap">
                                    <?php
                                    $nameLower = strtolower($item['name'] ?? '');
                                    if (strpos($nameLower, 'repair') !== false):
                                    ?>
                                        <!-- Repair Icon: Shield / Sparkle -->
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                            <path d="M9 12l2 2 4-4"/>
                                        </svg>
                                    <?php elseif (strpos($nameLower, 'acrylic') !== false || strpos($nameLower, 'extension') !== false): ?>
                                        <!-- Acrylic / Sculpted Icon: Diamond / Gem -->
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="6 3 18 3 22 9 12 22 2 9"/>
                                            <line x1="2" y1="9" x2="22" y2="9"/>
                                            <line x1="12" y1="22" x2="6" y2="9"/>
                                            <line x1="12" y1="22" x2="18" y2="9"/>
                                        </svg>
                                    <?php else: ?>
                                        <!-- Overlay / Care Icon: Sparkles / Star -->
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                                <h3><?php echo htmlspecialchars($item['name'] ?? ''); ?></h3>
                                <?php if (!empty($item['duration'])): ?>
                                    <div class="srv-duration" style="margin:4px 0 8px 0;"><?php echo (int)$item['duration']; ?> mins duration</div>
                                <?php endif; ?>
                                <p><?php echo htmlspecialchars($item['description'] ?? ''); ?></p>
                            </div>
                            <a href="<?php echo $base_url; ?>/contact.php" class="srv-inquire-link">
                                Inquire for pricing
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ============================================================
         BOOKING CTA ("Reserve Your Time")
         ============================================================ -->
    <section class="srv-cta" id="booking-cta">
        <div class="srv-cta-content">
            <h2>Reserve Your Time</h2>
            <p>
                Secure your appointment and let us prepare a tranquil space for your
                self-care ritual.
            </p>
            <a href="<?php echo $base_url; ?>/booking.php" class="srv-btn-book" id="btn-book-appointment">
                Book an Appointment
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
    </section>

    <!-- ============================================================
         SITE FOOTER
         ============================================================ -->
    <footer class="site-footer" id="site-footer" style="background:#F0EDED; border-top:1px solid #E5E7EB; padding:70px 24px 40px;">
        <div style="max-width:1200px; margin:0 auto; display:flex; flex-wrap:wrap; gap:40px; justify-content:space-between;">

            <!-- Column 1: Brand -->
            <div style="flex:1.2; min-width:240px;">
                <a href="<?php echo $base_url; ?>/" style="display:inline-block; text-decoration:none;">
                    <img src="<?php echo $base_url; ?>/assets/images/home/node-161.png" alt="Monet Nails logo" style="width:46px; opacity:0.85;">
                </a>
                <p style="margin-top:18px; max-width:280px; font-size:15px; line-height:1.6; color:#574147;">
                    Artistry in every touch. Elevating nail care to a fine art.
                </p>
                <p style="margin-top:16px; font-size:13px; color:#8C7C83;">
                    &copy; <?php echo date('Y'); ?> Monet Nails. All rights reserved.
                </p>
            </div>

            <!-- Column 2: Explore -->
            <div style="flex:1; min-width:160px;">
                <h4 style="font-family:'Bodoni Moda', serif; font-size:22px; color:#1C1B1B; margin:0 0 16px;">Explore</h4>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <a href="<?php echo $base_url; ?>/index.php?page=services" style="color:#574147; text-decoration:none; font-size:15px;">Services</a>
                    <a href="<?php echo $base_url; ?>/booking.php" style="color:#574147; text-decoration:none; font-size:15px;">Booking</a>
                    <a href="<?php echo $base_url; ?>/index.php?page=about" style="color:#574147; text-decoration:none; font-size:15px;">About Us</a>
                    <a href="<?php echo $base_url; ?>/privacy.php" style="color:#574147; text-decoration:none; font-size:15px;">Privacy Policy</a>
                </div>
            </div>

            <!-- Column 3: Contact -->
            <div style="flex:1.2; min-width:220px;">
                <h4 style="font-family:'Bodoni Moda', serif; font-size:22px; color:#1C1B1B; margin:0 0 16px;">Contact</h4>
                <div style="display:flex; flex-direction:column; gap:12px; font-size:15px; color:#574147;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <img src="<?php echo $base_url; ?>/assets/images/home/icon-184.svg" alt="" style="width:14px;">
                        <span>No 90/1, 4th Lane, Werellawatta, Yakkala</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <img src="<?php echo $base_url; ?>/assets/images/home/icon-188.svg" alt="" style="width:14px;">
                        <span>+94 77 210 5954</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <img src="<?php echo $base_url; ?>/assets/images/home/icon-192.svg" alt="" style="width:14px;">
                        <span>hello@monetnails.com</span>
                    </div>
                </div>
            </div>

            <!-- Column 4: Follow Us -->
            <div style="flex:1; min-width:180px;">
                <h4 style="font-family:'Bodoni Moda', serif; font-size:22px; color:#1C1B1B; margin:0 0 16px;">Follow Us</h4>
                <div style="display:flex; gap:12px;">
                    <a href="#" style="width:40px; height:40px; border:1px solid rgba(221,191,198,0.7); border-radius:10px; display:flex; align-items:center; justify-content:center; text-decoration:none;">
                        <img src="<?php echo $base_url; ?>/assets/images/home/icon-201.svg" alt="Instagram" style="max-height:16px;">
                    </a>
                    <a href="#" style="width:40px; height:40px; border:1px solid rgba(221,191,198,0.7); border-radius:10px; display:flex; align-items:center; justify-content:center; text-decoration:none;">
                        <img src="<?php echo $base_url; ?>/assets/images/home/icon-204.svg" alt="Facebook" style="max-height:16px;">
                    </a>
                    <a href="#" style="width:40px; height:40px; border:1px solid rgba(221,191,198,0.7); border-radius:10px; display:flex; align-items:center; justify-content:center; text-decoration:none;">
                        <img src="<?php echo $base_url; ?>/assets/images/home/icon-207.svg" alt="Twitter" style="max-height:16px;">
                    </a>
                </div>
            </div>

        </div>
    </footer>

</body>
</html>
