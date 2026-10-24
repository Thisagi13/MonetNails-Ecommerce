<?php
/**
 * views/appointments/history.php — Customer Appointment History View
 * Monet Nails MVC Application
 *
 * Displays:
 *   - Customer profile badge & booking counts
 *   - Upcoming appointments with time, specialist & reference numbers
 *   - Past salon visit history with duration & fee details
 */

$base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';

if (!function_exists('h')) {
    function h($text) {
        return htmlspecialchars((string)($text ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatMoney')) {
    function formatMoney($amount) {
        return 'Rs. ' . number_format((float)$amount, 0);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments — Monet Nails Atelier</title>
    <meta name="description" content="View your booked nail care sessions, artisan consultations, and salon visit history at Monet Nails.">

    <!-- Fonts: Bodoni Moda (headings) + Manrope (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..700&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Unified Header CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/header.css">

    <!-- Appointment History CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/appointment_history.css">

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
    $activeNav = 'my_appointments';
    require __DIR__ . '/../../includes/header.php';
    ?>

    <!-- ============================================================
         APPOINTMENT HISTORY MAIN CONTENT
         ============================================================ -->
    <main class="appt-history-page">
        <div class="appt-container">

            <!-- Breadcrumb Navigation -->
            <nav class="breadcrumb-nav" aria-label="Breadcrumb">
                <a href="<?php echo $base_url; ?>/">Home</a>
                <span class="sep">/</span>
                <a href="<?php echo $base_url; ?>/index.php?page=account_settings">Account</a>
                <span class="sep">/</span>
                <span class="current">My Appointments</span>
            </nav>

            <!-- Profile Bar -->
            <div class="profile-bar">
                <div class="profile-card">
                    <div class="profile-avatar-wrap">
                        <?php if (!empty($customerAvatarUrl)): ?>
                            <img src="<?php echo h($customerAvatarUrl); ?>" alt="Avatar">
                        <?php else: ?>
                            <span class="initials"><?php echo h($initials); ?></span>
                        <?php endif; ?>
                        <div class="sparkle-badge" aria-hidden="true">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                                <path d="M13.5 6 12.56 3.94 10.5 3l2.06-.94L13.5 0l.94 2.06L16.5 3l-2.06.94L13.5 6Zm0 10.5-.94-2.06L10.5 13.5l2.06-.94L13.5 10.5l.94 2.06L16.5 13.5l-2.06.94L13.5 16.5ZM6 14.25 4.13 10.13 0 8.25l4.13-1.88L6 2.25l1.88 4.12L12 8.25l-4.12 1.88L6 14.25ZM6 10.61 6.75 9l1.61-.75L6.75 7.5 6 5.89 5.25 7.5 3.64 8.25 5.25 9 6 10.61Z" fill="#A9275E"/>
                            </svg>
                        </div>
                    </div>
                    <div class="info">
                        <span class="name"><?php echo h($customerFullName); ?></span>
                        <span class="email"><?php echo h($customer['email'] ?? ''); ?></span>
                    </div>
                </div>

                <div class="stats-actions">
                    <span class="stats-badge">
                        <?php echo $upcomingCount; ?> Upcoming · <?php echo $pastCount; ?> Past Visits
                    </span>
                    <a href="<?php echo $base_url; ?>/index.php?page=booking" class="book-btn">
                        <svg viewBox="0 0 11 11" fill="none">
                            <path d="M4.5 6H0V4.5h4.5V0H6v4.5h4.5V6H6v4.5H4.5V6Z" fill="#FFFFFF"/>
                        </svg>
                        <span>BOOK NEW SESSION</span>
                    </a>
                </div>
            </div>

            <!-- Page Title Header -->
            <header class="page-title">
                <h1>My Appointments</h1>
                <p>View your booked nail care sessions, artisan consultations, and salon visit history.</p>
            </header>

            <!-- ========================================================
                 UPCOMING APPOINTMENTS SECTION
                 ======================================================== -->
            <section class="history-section" aria-labelledby="headingUpcoming">
                <div class="section-head">
                    <div class="title-block">
                        <svg viewBox="0 0 17 19" fill="none" aria-hidden="true">
                            <path d="M1.83 18.33c-.5 0-.93-.18-1.29-.54-.36-.36-.54-.79-.54-1.29V3.67c0-.5.18-.93.54-1.29.36-.36.79-.54 1.29-.54h.92V0h1.83v1.83h7.34V0h1.83v1.83h.92c.5 0 .93.18 1.29.54.36.36.54.79.54 1.29v12.83c0 .5-.18.93-.54 1.29-.36.36-.79.54-1.29.54H1.83Zm0-1.83h12.83V7.33H1.83v9.17Zm0-11h12.83V3.67H1.83v1.83Z" fill="currentColor"/>
                        </svg>
                        <h2 id="headingUpcoming">Upcoming Appointment</h2>
                    </div>
                </div>

                <?php if (!empty($upcomingAppointments)): ?>
                    <div class="upcoming-list">
                        <?php foreach ($upcomingAppointments as $apt): ?>
                            <article class="upcoming-card">
                                <div class="col-main">
                                    <div class="meta-row">
                                        <span>Reference No:</span>
                                        <span class="chip"><?php echo h($apt['reference_no']); ?></span>
                                        <span class="dot">•</span>
                                        <span>Payment:</span>
                                        <span class="chip paid"><?php echo formatMoney($apt['amount']) . ' (' . h($apt['payment_status']) . ')'; ?></span>
                                        <span class="dot">•</span>
                                        <span class="chip status-badge"><?php echo h($apt['status']); ?></span>
                                    </div>
                                    <h3><?php echo h($apt['service_name']); ?></h3>
                                    <p class="desc"><?php echo h($apt['description']); ?></p>
                                </div>
                                <div class="col-date">
                                    <div class="icon-wrap" aria-hidden="true">
                                        <svg viewBox="0 0 17 17" fill="none">
                                            <path d="M8.25 16.5C6.14 16.5 4.3 15.8 2.74 14.4 1.17 13 .27 11.26.05 9.17h1.88c.21 1.59.92 2.9 2.12 3.94 1.2 1.04 2.6 1.56 4.2 1.56 1.79 0 3.3-.62 4.55-1.87 1.24-1.24 1.87-2.76 1.87-4.55 0-1.79-.62-3.3-1.87-4.55C11.55 2.46 10.04 1.83 8.25 1.83c-1.05 0-2.04.24-2.95.74-.92.49-1.69 1.16-2.32 2.02H5.5v1.83H0V0.92h1.83v2.16C2.61 2.09 3.56 1.34 4.69.8 5.81.27 6.99 0 8.25 0c1.15 0 2.22.22 3.22.65 1 .44 1.87 1.03 2.61 1.77.74.74 1.33 1.61 1.77 2.61.43 1 .65 2.07.65 3.22 0 1.15-.22 2.22-.65 3.22-.44 1-1.03 1.87-1.77 2.61-.74.74-1.61 1.33-2.61 1.77-1 .43-2.07.65-3.22.65Zm2.57-4.4L7.33 8.62V3.67h1.84v4.21l2.92 2.93-1.27 1.29Z" fill="#A9275E"/>
                                        </svg>
                                    </div>
                                    <div class="date-text">
                                        <span class="date-label">DATE &amp; TIME</span>
                                        <span class="date-main"><?php echo h($apt['date_text']); ?></span>
                                        <span class="date-time"><?php echo h($apt['time_text']); ?></span>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state-card">
                        <div class="empty-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <h3>No Upcoming Appointments</h3>
                        <p>You have no scheduled appointments at the moment. Explore our curated nail care services and reserve your next atelier experience.</p>
                        <a href="<?php echo $base_url; ?>/index.php?page=booking" class="book-btn" style="margin-top: 8px;">
                            <span>BOOK AN APPOINTMENT</span>
                        </a>
                    </div>
                <?php endif; ?>
            </section>

            <!-- ========================================================
                 PAST SALON VISITS SECTION
                 ======================================================== -->
            <section class="history-section" aria-labelledby="headingPast">
                <div class="section-head">
                    <div class="title-block">
                        <svg viewBox="0 0 17 17" fill="none" aria-hidden="true">
                            <path d="M8.25 16.5C6.14 16.5 4.3 15.8 2.74 14.4 1.17 13 .27 11.26.05 9.17h1.88c.21 1.59.92 2.9 2.12 3.94 1.2 1.04 2.6 1.56 4.2 1.56 1.79 0 3.3-.62 4.55-1.87 1.24-1.24 1.87-2.76 1.87-4.55 0-1.79-.62-3.3-1.87-4.55C11.55 2.46 10.04 1.83 8.25 1.83c-1.05 0-2.04.24-2.95.74-.92.49-1.69 1.16-2.32 2.02H5.5v1.83H0V0.92h1.83v2.16C2.61 2.09 3.56 1.34 4.69.8 5.81.27 6.99 0 8.25 0c1.15 0 2.22.22 3.22.65 1 .44 1.87 1.03 2.61 1.77.74.74 1.33 1.61 1.77 2.61.43 1 .65 2.07.65 3.22 0 1.15-.22 2.22-.65 3.22-.44 1-1.03 1.87-1.77 2.61-.74.74-1.61 1.33-2.61 1.77-1 .43-2.07.65-3.22.65Zm2.57-4.4L7.33 8.62V3.67h1.84v4.21l2.92 2.93-1.27 1.29Z" fill="currentColor"/>
                        </svg>
                        <h2 id="headingPast">Past Salon Visits</h2>
                    </div>
                    <?php if ($pastCount > 0): ?>
                        <span class="meta"><?php echo $pastCount; ?> Completed Sessions</span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($pastAppointments)): ?>
                    <div class="past-list">
                        <?php foreach ($pastAppointments as $apt): ?>
                            <article class="past-card">
                                <div class="date-block">
                                    <span class="month"><?php echo h($apt['month_abbr']); ?></span>
                                    <span class="day"><?php echo h($apt['day']); ?></span>
                                </div>
                                <div class="body">
                                    <div class="top-row">
                                        <h4><?php echo h($apt['service_name']); ?></h4>
                                        <span class="status"><?php echo h($apt['status']); ?></span>
                                    </div>
                                    <div class="meta-row">
                                        <span><span class="lbl">Specialist: </span><span class="val"><?php echo h($apt['specialist']); ?></span></span>
                                        <span class="dot">•</span>
                                        <span><span class="lbl">Duration: </span><span class="val"><?php echo (int)$apt['duration_min']; ?> Min</span></span>
                                        <span class="dot">•</span>
                                        <span><span class="lbl">Fee: </span><span class="val"><?php echo formatMoney($apt['fee']); ?></span></span>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state-card">
                        <div class="empty-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <h3>No Past Visits Found</h3>
                        <p>Your salon visit history will appear here once your completed appointments are concluded.</p>
                    </div>
                <?php endif; ?>
            </section>

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

</body>
</html>
