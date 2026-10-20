<?php
/**
 * views/admin/dashboard.php — Admin Dashboard & Atelier Overview
 * Monet Nails MVC
 */

if (!function_exists('admin_status_badge')) {
    function admin_status_badge($s) {
        switch (strtolower(trim($s ?? ''))) {
            case "pending":    return ["Processing", "badge-amber"];
            case "processing": return ["Processing", "badge-amber"];
            case "shipped":    return ["Shipped",    "badge-green"];
            case "delivered":  return ["Delivered",  "badge-green"];
            case "cancelled":
            case "canceled":   return ["Cancelled",  "badge-red"];
            default:           return [ucfirst($s ?: 'Processing'), "badge-amber"];
        }
    }
}

$s_t = $salesPct >= 0 ? ["+" . $salesPct . "%", "trend-up"] : [$salesPct . "%", "trend-down"];
$o_t = $ordersDiff >= 0 ? ["+" . $ordersDiff . " orders", "trend-up"] : [$ordersDiff . " orders", "trend-down"];
$a_t = $apptsDiff >= 0 ? ["+" . $apptsDiff . " bookings", "trend-up"] : [$apptsDiff . " bookings", "trend-down"];
$v_t = $aovPct >= 0 ? ["+" . $aovPct . "%", "trend-up"] : [$aovPct . "%", "trend-down"];

$base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Monet Atelier Admin</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Admin Dashboard Stylesheet -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/admin_dashboard.css">
</head>
<body class="admin-body">

<div class="page">

    <!-- ============== SIDEBAR ============== -->
    <aside class="sidebar">
        <div>
            <a href="<?php echo $base_url; ?>/index.php?page=admin" class="brand-row" style="text-decoration:none;">
                <span class="brand-dot"></span>
                <span class="brand-name">Monet Atelier</span>
            </a>
            <div class="brand-sub">NAIL BAR &amp; PRESS-ON STUDIO</div>
        </div>

        <nav class="nav">
            <a href="<?php echo $base_url; ?>/index.php?page=admin" class="nav-item active">
                <!-- Dashboard icon: grid -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo $base_url; ?>/admin/orders.php" class="nav-item">
                <!-- Orders icon: shopping bag -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2 L3 6 L3 20 a2 2 0 0 0 2 2 h14 a2 2 0 0 0 2 -2 V6 L18 2 Z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10 a4 4 0 0 1 -8 0"/>
                </svg>
                <span>Orders &amp; Queue</span>
            </a>
            <a href="<?php echo $base_url; ?>/admin/services.php" class="nav-item">
                <!-- Products icon: package -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 8 L12 3 L3 8 V16 L12 21 L21 16 Z"/>
                    <line x1="3" y1="8" x2="12" y2="13"/>
                    <line x1="21" y1="8" x2="12" y2="13"/>
                    <line x1="12" y1="13" x2="12" y2="21"/>
                </svg>
                <span>Products &amp; Stock</span>
            </a>
            <a href="<?php echo $base_url; ?>/admin/appointments.php" class="nav-item">
                <!-- Appointments icon: calendar -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span>Appointments</span>
            </a>
            <a href="<?php echo $base_url; ?>/admin/users.php" class="nav-item">
                <!-- Users icon -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span>Customers &amp; Staff</span>
            </a>
            <a href="<?php echo $base_url; ?>/" class="nav-item" target="_blank" title="View Customer Storefront">
                <!-- External store icon -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                    <polyline points="15 3 21 3 21 9"/>
                    <line x1="10" y1="14" x2="21" y2="3"/>
                </svg>
                <span>Storefront View</span>
            </a>
        </nav>

        <!-- Curation tip card -->
        <div class="tip-card">
            <div class="tip-head">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="16" x2="12" y2="12"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <span>CURATION TIP</span>
            </div>
            <p class="tip-text">Prioritize handcrafted 3D charms restock 48 hours before weekends for optimal salon conversion.</p>
            <div class="tip-foot">
                <span>Restock alert guard</span>
                <span>Active</span>
            </div>
        </div>
    </aside>

    <!-- ============== WORKSPACE ============== -->
    <main class="workspace">

        <!-- Header -->
        <header class="ws-header">
            <div>
                <h1>Dashboard &amp; Atelier Overview</h1>
                <p>A little clarity for your day at the atelier.</p>
            </div>
            <div class="ws-actions">
                <button class="btn-pill" onclick="window.print()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Export report
                </button>
                <div class="profile">
                    <div class="avatar"><?php echo htmlspecialchars($adminInitials); ?></div>
                    <div>
                        <div class="profile-name"><?php echo htmlspecialchars($adminName); ?></div>
                        <div class="profile-role">Lead Administrator</div>
                    </div>
                    <a href="<?php echo $base_url; ?>/index.php?page=logout" class="logout-pill" title="Sign Out">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        </header>

        <!-- Business overview -->
        <section class="overview">

            <!-- Welcome + period -->
            <div class="welcome-row">
                <div>
                    <div class="greeting">Good day, <?php echo htmlspecialchars($adminFirstName); ?></div>
                    <div class="welcome-sub">Here's how your atelier is doing. A lovely week is taking shape.</div>
                </div>
                <div>
                    <div class="period-pill">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        This week
                    </div>
                    <div class="period-sub">
                        <?php echo date("M j", strtotime($weekStart)); ?> – <?php echo date("M j, Y", strtotime($weekEnd)); ?>
                    </div>
                </div>
            </div>

            <!-- KPI cards -->
            <div class="kpi-row">
                <div class="kpi">
                    <div class="kpi-head">
                        <span class="kpi-label">Total sales</span>
                        <span class="kpi-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/>
                                <path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/>
                                <path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="kpi-value">$<?php echo number_format($totalSales, 2); ?></div>
                    <div class="kpi-compare">
                        <span class="<?php echo $s_t[1]; ?>"><?php echo $s_t[0]; ?></span>
                        <span class="vs">vs. last week</span>
                    </div>
                </div>

                <div class="kpi">
                    <div class="kpi-head">
                        <span class="kpi-label">Orders received</span>
                        <span class="kpi-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2 L3 6 L3 20 a2 2 0 0 0 2 2 h14 a2 2 0 0 0 2 -2 V6 L18 2 Z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <path d="M16 10 a4 4 0 0 1 -8 0"/>
                            </svg>
                        </span>
                    </div>
                    <div class="kpi-value"><?php echo $ordersCount; ?></div>
                    <div class="kpi-compare">
                        <span class="<?php echo $o_t[1]; ?>"><?php echo $o_t[0]; ?></span>
                        <span class="vs"><?php echo $pendingCount; ?> awaiting fulfilment</span>
                    </div>
                </div>

                <div class="kpi">
                    <div class="kpi-head">
                        <span class="kpi-label">Appointments</span>
                        <span class="kpi-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </span>
                    </div>
                    <div class="kpi-value"><?php echo $apptsCount; ?></div>
                    <div class="kpi-compare">
                        <span class="<?php echo $a_t[1]; ?>"><?php echo $a_t[0]; ?></span>
                        <span class="vs"><?php echo $apptsToday; ?> scheduled today</span>
                    </div>
                </div>

                <div class="kpi">
                    <div class="kpi-head">
                        <span class="kpi-label">Average order value</span>
                        <span class="kpi-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="12" y1="18" x2="12" y2="12"/>
                                <line x1="9" y1="15" x2="15" y2="15"/>
                            </svg>
                        </span>
                    </div>
                    <div class="kpi-value">$<?php echo number_format($aov, 2); ?></div>
                    <div class="kpi-compare">
                        <span class="<?php echo $v_t[1]; ?>"><?php echo $v_t[0]; ?></span>
                        <span class="vs">vs. last week</span>
                    </div>
                </div>
            </div>

            <!-- Row 2: Sales chart + Upcoming appointments -->
            <div class="row-2">

                <!-- Sales overview chart -->
                <div class="card">
                    <div class="card-head">
                        <span class="card-title">Sales overview</span>
                        <span class="card-link">Live weekly trend</span>
                    </div>

                    <div class="rev-row">
                        <span class="rev-total">$<?php echo number_format($totalSales, 0); ?></span>
                        <span class="rev-sub">earned this week</span>
                        <div class="legend" style="margin-left:auto;">
                            <div class="legend-item"><span class="legend-dot nail"></span>Nail bar</div>
                            <div class="legend-item"><span class="legend-dot press"></span>Press-on sets</div>
                        </div>
                    </div>

                    <div class="chart-wrap">
                        <div class="y-scale">
                            <span>$<?php echo number_format($chartMax, 0); ?></span>
                            <span>$<?php echo number_format($chartMax / 2, 0); ?></span>
                            <span>$0</span>
                        </div>
                        <div class="bars">
                            <span class="hline"></span>
                            <?php
                            $bestIdx = 0; $bestVal = 0;
                            foreach ($daily as $i => $d) {
                                $sum = $d["press_on"] + $d["nail_bar"];
                                if ($sum > $bestVal) { $bestVal = $sum; $bestIdx = $i; }
                            }
                            $bestLabel = $daily[$bestIdx]["label"] ?? "Today";
                            $bestPct = $totalSales > 0 ? round($bestVal / $totalSales * 100) : 0;
                            ?>
                            <?php foreach ($daily as $d):
                                $pressH = $chartMax > 0 ? ($d["press_on"] / $chartMax) * 100 : 0;
                                $nailH  = $chartMax > 0 ? ($d["nail_bar"]  / $chartMax) * 100 : 0;
                            ?>
                                <div class="bar-col">
                                    <div class="bar-stack" style="height: <?php echo min(100, $pressH + $nailH); ?>%;">
                                        <div class="bar-press" style="height: <?php echo $pressH; ?>%; min-height:0;"></div>
                                        <div class="bar-nail"  style="height: <?php echo $nailH;  ?>%; min-height:0;"></div>
                                    </div>
                                    <span class="bar-label"><?php echo $d["label"]; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="sales-insight">
                        <?php echo $bestLabel; ?> was your strongest day · <?php echo $bestPct; ?>% of weekly sales
                    </div>
                </div>

                <!-- Upcoming appointments -->
                <div class="card">
                    <div class="card-head">
                        <span class="card-title">Upcoming appointments</span>
                    </div>

                    <div style="font-size:11px; color:var(--muted); display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                        <span>Today · <?php echo date("l, M j", strtotime($today)); ?></span>
                        <span style="display:inline-flex; align-items:center; gap:4px; color:var(--rose); font-weight:600;">
                            <span style="width:5px;height:5px;background:var(--rose-soft);border-radius:50%;"></span>
                            <?php echo $apptsToday; ?> bookings
                        </span>
                    </div>

                    <?php if (empty($upcoming)): ?>
                        <div style="padding:28px 0; text-align:center; color:var(--muted); font-size:12px;">
                            No appointments currently scheduled.
                        </div>
                    <?php else: ?>
                        <?php foreach ($upcoming as $i => $ap):
                            $t = strtotime($ap["appointment_time"]);
                            $isFirst = ($i === 0);
                        ?>
                            <div class="appt-row">
                                <div class="appt-time">
                                    <div class="time"><?php echo date("g:i", $t); ?></div>
                                    <div class="ampm"><?php echo date("A", $t); ?></div>
                                </div>
                                <div class="appt-marker <?php echo $isFirst ? '' : 'dim'; ?>"></div>
                                <div class="appt-info">
                                    <div class="appt-name">
                                        <?php echo htmlspecialchars($ap["f_name"] . " " . $ap["l_name"]); ?>
                                    </div>
                                    <div class="appt-svc">
                                        <?php echo htmlspecialchars($ap["service_name"]); ?> · <?php echo (int)($ap["duration_minutes"] ?? 45); ?> min
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <a href="<?php echo $base_url; ?>/admin/appointments.php" class="view-link">View full schedule →</a>
                </div>
            </div>

            <!-- Row 3: Recent orders + Stock alerts -->
            <div class="row-3">

                <!-- Recent orders -->
                <div class="card">
                    <div class="card-head">
                        <span class="card-title">Recent orders</span>
                        <a href="<?php echo $base_url; ?>/admin/orders.php" class="card-link">View all orders</a>
                    </div>

                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>ORDER</th>
                                <th>CUSTOMER / SET</th>
                                <th>TOTAL</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recentOrders as $o):
                            $b = admin_status_badge($o["status"]);
                            $oid = "#MA-" . str_pad($o["order_id"], 4, "0", STR_PAD_LEFT);
                        ?>
                            <tr>
                                <td class="col-id"><?php echo $oid; ?></td>
                                <td class="col-customer">
                                    <div class="name"><?php echo htmlspecialchars($o["customer_name"] ?: 'Client'); ?></div>
                                    <div class="set"><?php echo htmlspecialchars($o["product_name"] ?: "—"); ?></div>
                                </td>
                                <td class="col-total">$<?php echo number_format($o["total_amount"], 2); ?></td>
                                <td class="col-status">
                                    <span class="badge <?php echo $b[1]; ?>"><?php echo $b[0]; ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($recentOrders)): ?>
                            <tr><td colspan="4" style="text-align:center; padding:24px; color:var(--muted);">
                                No orders yet.
                            </td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Stock alerts + Quick actions -->
                <div class="card">
                    <div class="stock-head">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                        <span class="stock-title">Low-stock alert</span>
                        <span class="stock-pill"><?php echo $lowStockCount; ?> sets</span>
                    </div>
                    <div class="stock-sub">A few favourites need a little attention.</div>

                    <?php foreach ($lowStock as $p): ?>
                        <div class="stock-item">
                            <span class="stock-name"><?php echo htmlspecialchars($p["product_name"]); ?></span>
                            <span class="stock-count">
                                <?php echo $p["stock_quantity"] == 0 ? "Out of stock" : $p["stock_quantity"] . " left"; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($lowStock)): ?>
                        <div style="padding:14px 0; text-align:center; color:var(--muted); font-size:12px;">
                            All products well stocked.
                        </div>
                    <?php endif; ?>

                    <a href="<?php echo $base_url; ?>/admin/services.php" class="view-link">Review inventory →</a>

                    <div class="quick-actions">
                        <div class="qa-label">Quick actions</div>
                        <div class="qa-row">
                            <a href="<?php echo $base_url; ?>/admin/orders.php" class="qa-btn qa-btn-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                New order
                            </a>
                            <a href="<?php echo $base_url; ?>/admin/appointments.php" class="qa-btn qa-btn-outline">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                Book a visit
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </main>
</div>

</body>
</html>
