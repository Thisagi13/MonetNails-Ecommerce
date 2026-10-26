<?php
/**
 * views/booking/booking_success.php — Appointment Booking Confirmation Page
 * Monet Nails MVC Application
 *
 * Shown after a successful PayHere payment for an appointment booking.
 */

$base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmed — Monet Nails Atelier</title>
    <meta name="description" content="Your appointment at Monet Nails has been successfully booked and payment confirmed.">

    <!-- Fonts: Bodoni Moda (headings) + Manrope (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..700&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Header CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/header.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Manrope', sans-serif;
            background: #FAF8F9;
            color: #1C1B1B;
            min-height: 100vh;
        }

        .success-wrapper {
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 24px;
        }

        .success-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(169, 39, 94, 0.08), 0 2px 12px rgba(0,0,0,0.06);
            max-width: 600px;
            width: 100%;
            padding: 56px 48px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .success-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #A9275E, #D4608A, #A9275E);
            background-size: 200% 100%;
            animation: shimmer 2s linear infinite;
        }

        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .success-icon-wrap {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #fce4ef, #f9d0e2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
        }

        @keyframes popIn {
            from { transform: scale(0); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }

        .success-icon-wrap svg {
            color: #A9275E;
        }

        .success-card h1 {
            font-family: 'Bodoni Moda', serif;
            font-size: 32px;
            font-weight: 700;
            color: #1C1B1B;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .success-card .subtitle {
            font-size: 15px;
            color: #574147;
            line-height: 1.6;
            margin-bottom: 36px;
        }

        /* Appointment details card */
        .appt-details {
            background: #FAF8F9;
            border: 1px solid #EDD9E5;
            border-radius: 12px;
            padding: 24px 28px;
            text-align: left;
            margin-bottom: 36px;
        }

        .appt-details h2 {
            font-family: 'Bodoni Moda', serif;
            font-size: 14px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #A9275E;
            margin-bottom: 18px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px 0;
            border-bottom: 1px solid rgba(221,191,198,0.4);
            gap: 12px;
        }

        .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .detail-label {
            font-size: 13px;
            color: #8C7C83;
            font-weight: 500;
            white-space: nowrap;
        }

        .detail-value {
            font-size: 14px;
            color: #1C1B1B;
            font-weight: 600;
            text-align: right;
        }

        .appt-id-badge {
            display: inline-block;
            background: linear-gradient(135deg, #A9275E, #D4608A);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.05em;
            padding: 4px 14px;
            border-radius: 20px;
        }

        .payment-confirmed-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #e8f5e9;
            color: #2e7d32;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
            margin-bottom: 32px;
        }

        .payment-confirmed-badge svg {
            flex-shrink: 0;
        }

        .actions {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #A9275E, #D4608A);
            color: #fff;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 10px;
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.03em;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(169, 39, 94, 0.35);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: #A9275E;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 10px;
            border: 1.5px solid #A9275E;
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.03em;
            transition: background 0.2s, color 0.2s;
        }

        .btn-secondary:hover {
            background: #A9275E;
            color: #fff;
        }

        .note {
            margin-top: 28px;
            font-size: 12px;
            color: #8C7C83;
            line-height: 1.6;
        }

        @media (max-width: 600px) {
            .success-card { padding: 40px 24px; }
            .success-card h1 { font-size: 26px; }
            .actions { flex-direction: column; }
            .btn-primary, .btn-secondary { justify-content: center; }
        }
    </style>
</head>
<body>
    <!-- Unified Site Header -->
    <?php
    $activeNav = 'booking';
    require __DIR__ . '/../../includes/header.php';
    ?>

    <div class="success-wrapper">
        <div class="success-card">

            <!-- Success Icon -->
            <div class="success-icon-wrap">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>

            <h1>Appointment Confirmed</h1>
            <p class="subtitle">
                Thank you<?php echo $appointment ? ', ' . htmlspecialchars($appointment['first_name']) : ''; ?>!
                Your appointment has been successfully booked and your payment is being processed.
                We look forward to welcoming you to the atelier.
            </p>

            <!-- Payment Badge -->
            <div class="payment-confirmed-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                Payment Submitted via PayHere
            </div>

            <?php if ($appointment): ?>
            <!-- Appointment Details -->
            <div class="appt-details">
                <h2>Booking Details</h2>

                <div class="detail-row">
                    <span class="detail-label">Appointment ID</span>
                    <span class="detail-value">
                        <span class="appt-id-badge">#<?php echo $appointment['appointment_id']; ?></span>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Guest Name</span>
                    <span class="detail-value">
                        <?php echo htmlspecialchars(trim($appointment['first_name'] . ' ' . $appointment['last_name'])); ?>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Date</span>
                    <span class="detail-value">
                        <?php echo date('F j, Y', strtotime($appointment['appointment_date'])); ?>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Time</span>
                    <span class="detail-value">
                        <?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?>
                    </span>
                </div>

                <?php if (!empty($appointment['service_name'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Service</span>
                    <span class="detail-value"><?php echo htmlspecialchars($appointment['service_name']); ?></span>
                </div>
                <?php endif; ?>

                <div class="detail-row">
                    <span class="detail-label">Total Amount</span>
                    <span class="detail-value" style="color:#A9275E;">
                        Rs. <?php echo number_format((float)$appointment['total_price'], 2); ?>
                    </span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="actions">
                <a href="<?php echo $base_url; ?>/" class="btn-primary" id="btnGoHome">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Return Home
                </a>
                <a href="<?php echo $base_url; ?>/index.php?page=booking" class="btn-secondary" id="btnBookAnother">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    Book Another
                </a>
            </div>

            <p class="note">
                A confirmation may be sent to your email address on file.<br>
                For assistance, contact us at <strong>hello@monetnails.com</strong> or <strong>+94 77 210 5954</strong>.
            </p>

        </div>
    </div>

    <!-- Footer -->
    <?php require __DIR__ . '/../../includes/footer.php'; ?>

</body>
</html>
