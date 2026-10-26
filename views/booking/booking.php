<?php
/**
 * views/booking/booking.php â€” Appointment & Salon Booking View
 * Monet Nails MVC Application
 *
 * Provides a booking workflow for treatments and nail art.
 * Automatically adjusts header for guest clients vs logged-in users.
 */

if (!function_exists('bk_money')) {
    function bk_money($amount): string {
        return 'Rs. ' . number_format((float)$amount, 0, '.', ',');
    }
}

$base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';
$first_main_done = false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve Your Appointment â€” Monet Nails Atelier</title>
    <meta name="description" content="Book your bespoke manicure, pedicure, or signature nail art appointment at Monet Nails.">

    <!-- Fonts: Bodoni Moda (headings) + Manrope (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..700&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Header CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/header.css">

    <!-- Booking Stylesheet -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/booking.css">

    <!-- PayHere JavaScript SDK -->
    <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>
</head>
<body class="booking-page">

    <!-- ============================================================
         UNIFIED SITE HEADER (Registered / Unregistered auto-adjusted)
         ============================================================ -->
    <?php
    $activeNav = 'booking';
    require __DIR__ . '/../../includes/header.php';
    ?>

    <div class="booking-wrapper">
        <div class="booking-bg-art"></div>

        <main class="booking-main">

            <!-- Hero Section -->
            <section class="booking-hero">
                <h1>Reserve Your Time</h1>
                <p>Experience tranquil indulgence. Select your treatment and preferred time to begin your ritual.</p>
            </section>

            <!-- Alerts / Feedback -->
            <?php if (!empty($success_msg)): ?>
                <div class="booking-message success" id="bookingSuccessAlert">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span><?php echo htmlspecialchars($success_msg); ?></span>
                </div>
            <?php elseif (!empty($error_msg)): ?>
                <div class="booking-message error" id="bookingErrorAlert">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span><?php echo htmlspecialchars($error_msg); ?></span>
                </div>
            <?php endif; ?>

            <!-- Sign In Requirement Notice for Guests -->
            <?php if (!$isLoggedIn): ?>
                <div class="booking-message" style="background:rgba(169,39,94,0.06); border:1px solid rgba(169,39,94,0.2); border-radius:10px; display:flex; align-items:center; justify-content:space-between; gap:16px; padding:16px 20px; flex-wrap:wrap; margin-bottom:20px;" id="bookingSigninPrompt">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#A9275E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div>
                            <strong style="font-size:14px; color:#1C1B1B;">Sign in to confirm your appointment</strong>
                            <p style="margin:3px 0 0; font-size:13px; color:#574147;">You need to be logged in to book and pay for your appointment.</p>
                        </div>
                    </div>
                    <a href="<?php echo $base_url; ?>/index.php?page=signin&redirect=booking" style="flex-shrink:0; display:inline-flex; align-items:center; gap:6px; background:linear-gradient(135deg,#A9275E,#D4608A); color:#fff; text-decoration:none; padding:10px 20px; border-radius:8px; font-size:13px; font-weight:700;" id="bookingSigninBtn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                        Sign In First
                    </a>
                </div>
            <?php endif; ?>


            <!-- Booking Form -->
            <form method="POST" action="<?php echo $base_url; ?>/index.php?page=booking" id="bookingForm" class="booking-layout">

                <input type="hidden" name="date" id="hiddenDate" value="<?php echo htmlspecialchars($old['date'] ?? ''); ?>">
                <input type="hidden" name="time" id="hiddenTime" value="<?php echo htmlspecialchars($old['time'] ?? ''); ?>">

                <!-- Left Column -->
                <div class="booking-form">

                    <!-- ============ 1. SELECT SERVICE ============ -->
                    <section id="section-services">
                        <div class="section-head">
                            <span class="divider"></span>
                            <h2>1. Select Service</h2>
                        </div>

                        <?php foreach ($categories as $cat): ?>
                            <div class="service-group">
                                <p class="category-label"><?php echo htmlspecialchars($cat['name']); ?></p>
                                <div class="service-list">

                                    <?php foreach ($cat['services'] as $s): ?>
                                        <?php if (!empty($s['is_addon'])): ?>
                                            <!-- Add-on Service: Checkbox -->
                                            <label class="service-option">
                                                <input type="checkbox" class="service-radio" name="addons[]"
                                                       value="<?php echo htmlspecialchars((string)$s['service_id']); ?>"
                                                       data-name="<?php echo htmlspecialchars($s['service_name'] ?? $s['name']); ?>"
                                                       data-price="<?php echo (float)$s['price']; ?>"
                                                       data-duration="<?php echo (int)($s['duration_minutes'] ?? $s['duration']); ?>"
                                                       <?php if (in_array($s['service_id'], $old['addons'] ?? [])) echo 'checked'; ?>>
                                                <span class="service-info">
                                                    <span class="service-top">
                                                        <span class="service-name"><?php echo htmlspecialchars($s['service_name'] ?? $s['name']); ?></span>
                                                        <span class="service-price">+<?php echo bk_money($s['price']); ?></span>
                                                    </span>
                                                    <span class="service-desc"><?php echo htmlspecialchars($s['description'] ?? ''); ?></span>
                                                    <span class="duration-badge">+<?php echo (int)($s['duration_minutes'] ?? $s['duration']); ?> Min</span>
                                                </span>
                                            </label>
                                        <?php else: ?>
                                            <!-- Main Service: Radio -->
                                            <label class="service-option">
                                                <input type="radio" class="service-radio" name="service_id"
                                                       value="<?php echo htmlspecialchars((string)$s['service_id']); ?>"
                                                       data-name="<?php echo htmlspecialchars($s['service_name'] ?? $s['name']); ?>"
                                                       data-price="<?php echo (float)$s['price']; ?>"
                                                       data-duration="<?php echo (int)($s['duration_minutes'] ?? $s['duration']); ?>"
                                                       <?php
                                                       if (($old['service_id'] ?? '') == $s['service_id']) {
                                                           echo 'checked';
                                                       } elseif (empty($old['service_id']) && !$first_main_done) {
                                                           echo 'checked';
                                                           $first_main_done = true;
                                                       }
                                                       ?>>
                                                <span class="service-info">
                                                    <span class="service-top">
                                                        <span class="service-name"><?php echo htmlspecialchars($s['service_name'] ?? $s['name']); ?></span>
                                                        <span class="service-price"><?php echo bk_money($s['price']); ?></span>
                                                    </span>
                                                    <span class="service-desc"><?php echo htmlspecialchars($s['description'] ?? ''); ?></span>
                                                    <span class="duration-badge"><?php echo (int)($s['duration_minutes'] ?? $s['duration']); ?> Min</span>
                                                </span>
                                            </label>
                                        <?php endif; ?>
                                    <?php endforeach; ?>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </section>

                    <!-- ============ 2. CHOOSE DATE & TIME ============ -->
                    <section id="section-datetime">
                        <div class="section-head">
                            <span class="divider"></span>
                            <h2>2. Choose Date &amp; Time</h2>
                        </div>

                        <div class="datetime-row">
                            <!-- Calendar -->
                            <div class="calendar">
                                <div class="cal-header">
                                    <button type="button" class="cal-nav" id="calPrev" aria-label="Previous month">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="15 18 9 12 15 6"></polyline>
                                        </svg>
                                    </button>
                                    <span class="cal-month" id="calMonth"></span>
                                    <button type="button" class="cal-nav" id="calNext" aria-label="Next month">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </button>
                                </div>

                                <div class="cal-weekdays">
                                    <span>S</span><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span>
                                </div>

                                <div class="cal-grid" id="calGrid"></div>
                            </div>

                            <!-- Available Time Slots -->
                            <div class="time-slots">
                                <p class="category-label">Available Times</p>
                                <div class="time-grid" id="timeGrid"></div>
                            </div>
                        </div>
                    </section>

                    <!-- ============ 3. YOUR DETAILS ============ -->
                    <section id="section-details">
                        <div class="section-head">
                            <span class="divider"></span>
                            <h2>3. Your Details</h2>
                        </div>

                        <div class="form-grid">
                            <div class="form-row">
                                <div class="field">
                                    <label for="firstName">First Name</label>
                                    <input type="text" id="firstName" name="first_name" placeholder="Jane"
                                           value="<?php echo htmlspecialchars($old['first_name'] ?? ''); ?>" required>
                                </div>
                                <div class="field">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" id="lastName" name="last_name" placeholder="Doe"
                                           value="<?php echo htmlspecialchars($old['last_name'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="field">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" placeholder="jane@example.com"
                                           value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>" required>
                                </div>
                                <div class="field">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" placeholder="+94 77 123 4567"
                                           value="<?php echo htmlspecialchars($old['phone'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <div class="field">
                                <label for="specialRequests">Special Requests (Optional)</label>
                                <textarea id="specialRequests" name="special_requests"
                                          placeholder="Any allergies, previous gel to remove, or specific design ideas?"><?php echo htmlspecialchars($old['special_requests'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </section>

                </div><!-- /.booking-form -->

                <!-- Right Column: Booking Summary -->
                <aside class="booking-sidebar">
                    <div class="summary-card">
                        <svg class="summary-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>

                        <h3>Booking Summary</h3>

                        <!-- Selected Main Service -->
                        <div class="summary-line">
                            <div>
                                <p class="line-main" id="sumService">Select a service</p>
                                <p class="line-sub" id="sumDuration">&nbsp;</p>
                            </div>
                            <p class="line-price" id="sumPrice"></p>
                        </div>

                        <!-- Selected Add-ons -->
                        <div id="sumAddons"></div>

                        <!-- Chosen Date + Time -->
                        <div class="summary-line no-border">
                            <div>
                                <p class="line-main" id="sumDate">Select a date</p>
                                <p class="line-sub" id="sumTime">with Any Artist</p>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="summary-total">
                            <span>Total</span>
                            <span id="sumTotal">Rs. 0</span>
                        </div>

                        <?php if ($isLoggedIn): ?>
                            <button type="submit" class="confirm-btn" id="btnConfirmAppointment">Confirm &amp; Pay with PayHere</button>
                        <?php else: ?>
                            <a href="<?php echo $base_url; ?>/index.php?page=signin&redirect=booking" class="confirm-btn" id="btnConfirmAppointment" style="display:flex;align-items:center;justify-content:center;gap:8px;text-decoration:none;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                    <polyline points="10 17 15 12 10 7"></polyline>
                                    <line x1="15" y1="12" x2="3" y2="12"></line>
                                </svg>
                                Sign In to Confirm
                            </a>
                        <?php endif; ?>

                        <p class="summary-note">Secure payment processed via PayHere gateway.</p>
                    </div>
                </aside>

            </form>

        <!-- ============================================================
             PAYHERE MODAL TRIGGER (shown after appointment is saved)
             ============================================================ -->
        <?php if (!empty($payhereData)): ?>
            <?php $payhereEndpoint = !empty($payhereData['sandbox']) ? 'https://sandbox.payhere.lk/pay/checkout' : 'https://www.payhere.lk/pay/checkout'; ?>
            <div id="payhereRedirectOverlay" style="position:fixed;inset:0;background:rgba(28,27,27,0.75);backdrop-filter:blur(6px);z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#fff;">
                <div style="background:#fff;color:#1C1B1B;padding:40px 48px;border-radius:12px;text-align:center;box-shadow:0 12px 36px rgba(0,0,0,0.3);max-width:480px;width:90%;">
                    <div style="width:48px;height:48px;border:3px solid #E8DDE0;border-top-color:#A9275E;border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto 20px;"></div>
                    <h3 style="font-family:'Bodoni Moda',serif;font-size:24px;margin-bottom:8px;font-weight:600;">Connecting to PayHere...</h3>
                    <p style="font-size:14px;color:#574147;margin-bottom:24px;line-height:1.5;">Preparing your secure payment session for Appointment #<?php echo htmlspecialchars($payhereData['order_id']); ?>.</p>
                    <form id="payhereDirectForm" method="post" action="<?php echo $payhereEndpoint; ?>">
                        <?php foreach ($payhereData as $param => $val): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($param); ?>" value="<?php echo htmlspecialchars((string)$val); ?>">
                        <?php endforeach; ?>
                        <button type="submit" class="confirm-btn" style="margin-top:0;width:100%;font-size:13px;padding:12px 16px;">
                            Click Here If PayHere Window Does Not Open
                        </button>
                    </form>
                </div>
            </div>
            <style>@keyframes spin { to { transform: rotate(360deg); } }</style>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var payment = {
                        sandbox: <?php echo $payhereData['sandbox'] ? 'true' : 'false'; ?>,
                        merchant_id: "<?php echo htmlspecialchars($payhereData['merchant_id']); ?>",
                        return_url: "<?php echo htmlspecialchars($payhereData['return_url']); ?>",
                        cancel_url: "<?php echo htmlspecialchars($payhereData['cancel_url']); ?>",
                        notify_url: "<?php echo htmlspecialchars($payhereData['notify_url']); ?>",
                        order_id: "<?php echo htmlspecialchars($payhereData['order_id']); ?>",
                        items: "<?php echo htmlspecialchars($payhereData['items']); ?>",
                        amount: "<?php echo htmlspecialchars($payhereData['amount']); ?>",
                        currency: "<?php echo htmlspecialchars($payhereData['currency']); ?>",
                        hash: "<?php echo htmlspecialchars($payhereData['hash']); ?>",
                        first_name: "<?php echo htmlspecialchars($payhereData['first_name']); ?>",
                        last_name: "<?php echo htmlspecialchars($payhereData['last_name']); ?>",
                        email: "<?php echo htmlspecialchars($payhereData['email']); ?>",
                        phone: "<?php echo htmlspecialchars($payhereData['phone']); ?>",
                        address: "<?php echo htmlspecialchars($payhereData['address']); ?>",
                        city: "<?php echo htmlspecialchars($payhereData['city']); ?>",
                        country: "<?php echo htmlspecialchars($payhereData['country']); ?>"
                    };

                    if (typeof payhere !== 'undefined') {
                        payhere.onCompleted = function onCompleted(orderId) {
                            window.location.href = "<?php echo $payhereData['return_url']; ?>";
                        };

                        payhere.onDismissed = function onDismissed() {
                            var overlay = document.getElementById('payhereRedirectOverlay');
                            if (overlay) overlay.style.display = 'none';
                        };

                        payhere.onError = function onError(error) {
                            console.error("PayHere Error:", error);
                            document.getElementById('payhereDirectForm').submit();
                        };

                        // Launch PayHere popup
                        payhere.startPayment(payment);
                    } else {
                        // JS SDK blocked â€” direct form POST
                        document.getElementById('payhereDirectForm').submit();
                    }
                });
            </script>
        <?php endif; ?>

        </main>

        <!-- Site Footer -->
        <?php require __DIR__ . '/../../includes/footer.php'; ?>
    </div>
    <!-- ============================================================
         Client JavaScript: Calendar, Time Slots & Live Summary
         ============================================================ -->
    <script>
    var TODAY     = '<?php echo date('Y-m-d'); ?>';
    var INIT_DATE = '<?php echo !empty($old['date']) ? $old['date'] : date('Y-m-d'); ?>';
    var INIT_TIME = '<?php echo htmlspecialchars($old['time'] ?? ''); ?>';
    var AJAX_URL  = '<?php echo $base_url; ?>/index.php?page=booking&ajax=slots';

    var viewYear, viewMonth;
    var selectedDate  = INIT_DATE;
    var selectedTime  = INIT_TIME;
    var selectedLabel = INIT_TIME ? null : null;

    function pad(n) { return n < 10 ? '0' + n : '' + n; }
    function ymd(y, m, d) { return y + '-' + pad(m + 1) + '-' + pad(d); }

    function fmtMoney(n) {
        return 'Rs. ' + Math.round(n).toLocaleString('en-US');
    }

    function prettyDate(ymdStr) {
        if (!ymdStr) return 'Select a date';
        var p = ymdStr.split('-');
        var d = new Date(p[0], p[1] - 1, p[2]);
        return d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
    }

    /* 1. CALENDAR */
    function renderCalendar() {
        var firstDay  = new Date(viewYear, viewMonth, 1).getDay();
        var daysCount = new Date(viewYear, viewMonth + 1, 0).getDate();

        document.getElementById('calMonth').textContent =
            new Date(viewYear, viewMonth, 1)
                .toLocaleDateString('en-US', { month: 'long', year: 'numeric' })
                .toUpperCase();

        var grid = document.getElementById('calGrid');
        grid.innerHTML = '';

        for (var i = 0; i < firstDay; i++) {
            grid.appendChild(document.createElement('span'));
        }

        for (var d = 1; d <= daysCount; d++) {
            var dayStr = ymd(viewYear, viewMonth, d);
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'cal-day';
            btn.textContent = d;

            if (dayStr < TODAY) {
                btn.disabled = true;
            }
            if (dayStr === selectedDate) {
                btn.classList.add('selected');
            }

            btn.onclick = (function (dateStr) {
                return function () {
                    selectedDate  = dateStr;
                    selectedTime  = null;
                    selectedLabel = null;
                    renderCalendar();
                    loadSlots();
                };
            })(dayStr);

            grid.appendChild(btn);
        }

        var isCurrentMonth = (viewYear === Number(TODAY.slice(0, 4)) &&
                              viewMonth === Number(TODAY.slice(5, 7)) - 1);
        document.getElementById('calPrev').disabled = isCurrentMonth;
    }

    document.getElementById('calPrev').onclick = function () {
        viewMonth--;
        if (viewMonth < 0) { viewMonth = 11; viewYear--; }
        renderCalendar();
    };

    document.getElementById('calNext').onclick = function () {
        viewMonth++;
        if (viewMonth > 11) { viewMonth = 0; viewYear++; }
        renderCalendar();
    };

    /* 2. TIME SLOTS VIA AJAX */
    function loadSlots() {
        fetch(AJAX_URL + '&date=' + selectedDate)
            .then(function (res) { return res.json(); })
            .then(function (slots) { renderSlots(slots); })
            .catch(function () { renderSlots([]); });
    }

    function renderSlots(slots) {
        var wrap = document.getElementById('timeGrid');
        wrap.innerHTML = '';

        if (!slots || !slots.length) {
            wrap.innerHTML = '<p class="line-sub">No appointments available for this date.</p>';
            updateSummary();
            return;
        }

        slots.forEach(function (slot) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'time-btn';
            btn.textContent = slot.label;

            if (!slot.available) {
                btn.disabled = true;
            }
            if (slot.time === selectedTime) {
                btn.classList.add('selected');
                selectedLabel = slot.label;
            }

            btn.onclick = function () {
                selectedTime  = slot.time;
                selectedLabel = slot.label;
                updateSummary();
                var all = wrap.querySelectorAll('.time-btn');
                for (var i = 0; i < all.length; i++) { all[i].classList.remove('selected'); }
                btn.classList.add('selected');
            };

            wrap.appendChild(btn);
        });

        if (!selectedTime) {
            for (var j = 0; j < slots.length; j++) {
                if (slots[j].available) {
                    selectedTime  = slots[j].time;
                    selectedLabel = slots[j].label;
                    break;
                }
            }
            if (selectedTime) { renderSlots(slots); return; }
        }

        updateSummary();
    }

    /* 3. LIVE BOOKING SUMMARY */
    function updateSummary() {
        var main = document.querySelector('input[name="service_id"]:checked');
        var total = 0, minutes = 0;

        if (main) {
            document.getElementById('sumService').textContent  = main.dataset.name;
            document.getElementById('sumPrice').textContent    = fmtMoney(parseFloat(main.dataset.price));
            total   = parseFloat(main.dataset.price);
            minutes = parseInt(main.dataset.duration, 10);
            document.getElementById('sumDuration').textContent = minutes + ' Min';
        } else {
            document.getElementById('sumService').textContent  = 'Select a service';
            document.getElementById('sumPrice').textContent    = '';
            document.getElementById('sumDuration').textContent = '\u00A0';
        }

        var addonWrap = document.getElementById('sumAddons');
        addonWrap.innerHTML = '';
        var addons = document.querySelectorAll('input[name="addons[]"]:checked');
        for (var i = 0; i < addons.length; i++) {
            var a = addons[i];
            total   += parseFloat(a.dataset.price);
            minutes += parseInt(a.dataset.duration, 10);

            var line = document.createElement('div');
            line.className = 'summary-line';
            line.innerHTML =
                '<div><p class="line-main">' + a.dataset.name + '</p>' +
                '<p class="line-sub">+' + a.dataset.duration + ' Min</p></div>' +
                '<p class="line-price">+' + fmtMoney(parseFloat(a.dataset.price)) + '</p>';
            addonWrap.appendChild(line);
        }

        document.getElementById('sumDate').textContent = selectedDate ? prettyDate(selectedDate) : 'Select a date';
        document.getElementById('sumTime').textContent =
            selectedLabel ? (selectedLabel + ' with Any Artist') : 'with Any Artist';

        document.getElementById('sumTotal').textContent = fmtMoney(total);
    }

    var radios = document.querySelectorAll('.service-radio');
    for (var r = 0; r < radios.length; r++) {
        radios[r].addEventListener('change', updateSummary);
    }

    /* Submit Validation */
    document.getElementById('bookingForm').addEventListener('submit', function (e) {
        var main = document.querySelector('input[name="service_id"]:checked');
        if (!main) {
            alert('Please select a service first.');
            e.preventDefault();
            return;
        }
        if (!selectedTime) {
            alert('Please choose an available appointment time.');
            e.preventDefault();
            return;
        }

        document.getElementById('hiddenDate').value = selectedDate;
        document.getElementById('hiddenTime').value = selectedTime;
    });

    /* Initialize calendar */
    var start = new Date();
    viewYear  = start.getFullYear();
    viewMonth = start.getMonth();
    if (INIT_DATE) {
        viewYear  = Number(INIT_DATE.slice(0, 4));
        viewMonth = Number(INIT_DATE.slice(5, 7)) - 1;
    }

    renderCalendar();
    loadSlots();
    </script>

</body>
</html>
