<?php
/**
 * views/admin/reports.php — Monet Atelier Admin
 * Sales & Performance Reports Workspace
 * MVC Layer: View
 */

$base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';

// Helper for trend pill
function getTrendDisplay(float $pct): array {
    if ($pct > 0) {
        return ['↗ +' . $pct . '%', 'trend-up'];
    } elseif ($pct < 0) {
        return ['↘ ' . $pct . '%', 'trend-down'];
    }
    return ['→ 0%', 'trend-flat'];
}

$revTrend  = getTrendDisplay($reportData['rev_pct']);
$ordTrend  = getTrendDisplay($reportData['ord_pct']);
$aovTrend  = getTrendDisplay($reportData['aov_pct']);
$setsTrend = getTrendDisplay($reportData['sets_pct']);

$currentPeriod = $_GET['period'] ?? 'this_month';
$startDate = $reportData['start'];
$endDate   = $reportData['end'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales &amp; Reports — Monet Atelier Admin</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Admin Reports Stylesheet -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/admin_reports.css">
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
            <a href="<?php echo $base_url; ?>/index.php?page=admin" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="<?php echo $base_url; ?>/index.php?page=admin_orders" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <span>Orders &amp; Queue</span>
            </a>

            <a href="<?php echo $base_url; ?>/index.php?page=admin_products" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
                <span>Products Catalog</span>
            </a>

            <a href="<?php echo $base_url; ?>/index.php?page=admin_appointments" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>Appointments</span>
            </a>

            <a href="<?php echo $base_url; ?>/index.php?page=admin_services" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
                <span>Services</span>
            </a>
            <a href="<?php echo $base_url; ?>/index.php?page=admin_reports" class="nav-item active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                <span>Sales &amp; Reports</span>
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="studio-card">
                <div class="studio-card-head">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                    <span>Crafted with care.</span>
                </div>
                <p>A little artistry. A lot of heart. Every set tells a story.</p>
                <a href="<?php echo $base_url; ?>/index.php?page=home" target="_blank">
                    <span>Visit storefront</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                </a>
            </div>

            <div class="system-status">
                <span><span class="status-dot"></span>Sales Sync</span>
                <span>Active</span>
            </div>
        </div>
    </aside>

    <!-- ============== WORKSPACE ============== -->
    <main class="workspace">

        <!-- Header -->
        <header class="ws-header">
            <div>
                <h1>Sales &amp; Reports</h1>
                <p>A thoughtful, data-driven look at your atelier's financial and commercial performance.</p>
            </div>
            <div class="ws-actions">
                <!-- CSV Export -->
                <a href="<?php echo $base_url; ?>/index.php?page=admin_reports&export=csv&start=<?php echo urlencode($startDate); ?>&end=<?php echo urlencode($endDate); ?>" class="btn-export primary" title="Download CSV Spreadsheet">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Export CSV
                </a>

                <!-- Print Report -->
                <button type="button" class="btn-export" onclick="window.print()" title="Print Executive Report">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Print Report
                </button>

                <!-- Profile -->
                <div class="profile">
                    <div class="avatar"><?php echo htmlspecialchars($adminInitials); ?></div>
                    <div>
                        <div class="profile-name"><?php echo htmlspecialchars($adminName); ?></div>
                        <div class="profile-role">Lead Administrator</div>
                    </div>
                    <a href="<?php echo $base_url; ?>/index.php?page=logout" class="logout-pill" title="Sign Out">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        </header>

        <!-- Period Controls & Date Filter -->
        <section class="period-panel">
            <div class="period-top-row">
                <div>
                    <div class="period-title">Your atelier, at a glance</div>
                    <div class="period-sub">
                        <?php echo date("F j, Y", strtotime($startDate)); ?> – <?php echo date("F j, Y", strtotime($endDate)); ?>
                        &middot; Compared with <?php echo date("F j", strtotime($reportData['prev_start'])); ?> – <?php echo date("F j, Y", strtotime($reportData['prev_end'])); ?>
                    </div>
                </div>

                <div class="period-pills-wrap">
                    <a href="<?php echo $base_url; ?>/index.php?page=admin_reports&period=this_month" 
                       class="filter-pill <?php echo ($currentPeriod === 'this_month') ? 'active' : ''; ?>">This Month</a>
                    
                    <a href="<?php echo $base_url; ?>/index.php?page=admin_reports&period=last_month" 
                       class="filter-pill <?php echo ($currentPeriod === 'last_month') ? 'active' : ''; ?>">Last Month</a>

                    <a href="<?php echo $base_url; ?>/index.php?page=admin_reports&period=this_week" 
                       class="filter-pill <?php echo ($currentPeriod === 'this_week') ? 'active' : ''; ?>">This Week</a>

                    <a href="<?php echo $base_url; ?>/index.php?page=admin_reports&period=today" 
                       class="filter-pill <?php echo ($currentPeriod === 'today') ? 'active' : ''; ?>">Today</a>

                    <a href="<?php echo $base_url; ?>/index.php?page=admin_reports&period=this_year" 
                       class="filter-pill <?php echo ($currentPeriod === 'this_year') ? 'active' : ''; ?>">This Year</a>

                    <a href="<?php echo $base_url; ?>/index.php?page=admin_reports&period=all_time" 
                       class="filter-pill <?php echo ($currentPeriod === 'all_time') ? 'active' : ''; ?>">All Time</a>
                </div>
            </div>

            <!-- Custom Date Range Form -->
            <form method="GET" action="<?php echo $base_url; ?>/index.php" class="custom-range-form">
                <input type="hidden" name="page" value="admin_reports">
                <span class="range-label">Custom Window:</span>
                <input type="date" name="start" class="range-input" value="<?php echo htmlspecialchars($startDate); ?>" required>
                <span class="range-label">to</span>
                <input type="date" name="end" class="range-input" value="<?php echo htmlspecialchars($endDate); ?>" required>
                <button type="submit" class="btn-apply-range">Apply Date Filter</button>
            </form>
        </section>

        <!-- KPI Cards Row -->
        <section class="kpi-row">
            <!-- 1. Total Revenue -->
            <div class="kpi">
                <div class="kpi-head">
                    <span class="kpi-label">Total Revenue</span>
                    <span class="kpi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </span>
                </div>
                <div class="kpi-value">$<?php echo number_format($reportData['total_rev'], 2); ?></div>
                <div class="kpi-compare">
                    <span class="<?php echo $revTrend[1]; ?>"><?php echo $revTrend[0]; ?></span>
                    <span class="vs">vs. previous period</span>
                </div>
            </div>

            <!-- 2. Orders Placed -->
            <div class="kpi">
                <div class="kpi-head">
                    <span class="kpi-label">Orders Placed</span>
                    <span class="kpi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                    </span>
                </div>
                <div class="kpi-value"><?php echo number_format($reportData['orders_count']); ?></div>
                <div class="kpi-compare">
                    <span class="<?php echo $ordTrend[1]; ?>"><?php echo $ordTrend[0]; ?></span>
                    <span class="vs">vs. previous period</span>
                </div>
            </div>

            <!-- 3. Average Order Value -->
            <div class="kpi">
                <div class="kpi-head">
                    <span class="kpi-label">Average Order Value</span>
                    <span class="kpi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                            <polyline points="17 6 23 6 23 12"></polyline>
                        </svg>
                    </span>
                </div>
                <div class="kpi-value">$<?php echo number_format($reportData['aov'], 2); ?></div>
                <div class="kpi-compare">
                    <span class="<?php echo $aovTrend[1]; ?>"><?php echo $aovTrend[0]; ?></span>
                    <span class="vs">vs. previous period</span>
                </div>
            </div>

            <!-- 4. Sets Sold -->
            <div class="kpi">
                <div class="kpi-head">
                    <span class="kpi-label">Sets &amp; Items Sold</span>
                    <span class="kpi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                    </span>
                </div>
                <div class="kpi-value"><?php echo number_format($reportData['sets_sold']); ?></div>
                <div class="kpi-compare">
                    <span class="<?php echo $setsTrend[1]; ?>"><?php echo $setsTrend[0]; ?></span>
                    <span class="vs">vs. previous period</span>
                </div>
            </div>
        </section>

        <!-- Revenue Reports Row (Trend Chart + Breakdown) -->
        <section class="rev-row">

            <!-- Card 1: Sales Trend SVG Chart -->
            <div class="card">
                <div class="chart-head">
                    <span class="card-title">Daily Sales &amp; Revenue Trend</span>
                    <div class="legend">
                        <span class="legend-dot"></span>
                        <span>Daily combined revenue</span>
                    </div>
                </div>

                <div class="chart-wrap">
                    <div class="y-scale">
                        <span>$0</span>
                        <span>$<?php echo number_format($reportData['chart_max'] * 0.25, 0); ?></span>
                        <span>$<?php echo number_format($reportData['chart_max'] * 0.50, 0); ?></span>
                        <span>$<?php echo number_format($reportData['chart_max'] * 0.75, 0); ?></span>
                        <span>$<?php echo number_format($reportData['chart_max'], 0); ?></span>
                    </div>

                    <div class="chart-body">
                        <div class="gridlines">
                            <div class="gridline"></div>
                            <div class="gridline"></div>
                            <div class="gridline"></div>
                            <div class="gridline"></div>
                            <div class="gridline"></div>
                        </div>

                        <svg class="chart-svg" viewBox="0 0 1000 100" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="roseChartGrad" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#BE536D" stop-opacity="0.32"></stop>
                                    <stop offset="100%" stop-color="#BE536D" stop-opacity="0.02"></stop>
                                </linearGradient>
                            </defs>
                            <?php if (!empty($reportData['area_path'])): ?>
                                <path d="<?php echo $reportData['area_path']; ?>" fill="url(#roseChartGrad)"></path>
                                <path d="<?php echo $reportData['line_path']; ?>" fill="none" stroke="#BE536D" stroke-width="2.2" stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke"></path>
                                <?php foreach ($reportData['points'] as $pt): ?>
                                    <circle class="chart-point" cx="<?php echo round($pt[0], 2); ?>" cy="<?php echo round($pt[1], 2); ?>" r="3.2" fill="#BE536D" vector-effect="non-scaling-stroke">
                                        <title><?php echo htmlspecialchars($pt[2]); ?>: $<?php echo number_format($pt[3], 2); ?></title>
                                    </circle>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </svg>
                    </div>
                </div>

                <!-- X Scale Dates -->
                <div class="x-scale">
                    <span><?php echo date("M j", strtotime($startDate)); ?></span>
                    <span><?php echo date("M j", strtotime($startDate . " +" . round(max(1, (strtotime($endDate) - strtotime($startDate)) / 86400 * 0.25)) . " days")); ?></span>
                    <span><?php echo date("M j", strtotime($startDate . " +" . round(max(1, (strtotime($endDate) - strtotime($startDate)) / 86400 * 0.50)) . " days")); ?></span>
                    <span><?php echo date("M j", strtotime($startDate . " +" . round(max(1, (strtotime($endDate) - strtotime($startDate)) / 86400 * 0.75)) . " days")); ?></span>
                    <span><?php echo date("M j", strtotime($endDate)); ?></span>
                </div>

                <div class="trend-summary">
                    <span class="pct"><?php echo $revTrend[0]; ?></span>
                    <span>revenue variance compared to the previous matching period.</span>
                </div>
            </div>

            <!-- Card 2: Revenue Breakdown -->
            <div class="card">
                <div class="card-title">Revenue Breakdown</div>
                <div class="bd-total">$<?php echo number_format($reportData['total_rev'], 2); ?></div>
                <div class="bd-sub">Across products, salon services &amp; accessories</div>

                <!-- Progress Bar -->
                <div class="bd-bar">
                    <div class="bd-bar-press" style="width: <?php echo $reportData['press_pct']; ?>%;" title="Press-on sets: <?php echo $reportData['press_pct']; ?>%"></div>
                    <div class="bd-bar-serv" style="width: <?php echo $reportData['services_pct']; ?>%;" title="Salon services: <?php echo $reportData['services_pct']; ?>%"></div>
                    <div class="bd-bar-acc" style="width: <?php echo $reportData['access_pct']; ?>%;" title="Care & accessories: <?php echo $reportData['access_pct']; ?>%"></div>
                </div>

                <!-- Breakdown Slices -->
                <div class="bd-cat-list">
                    <div class="bd-cat">
                        <span class="bd-cat-dot" style="background: #BE536D;"></span>
                        <div class="bd-cat-info">
                            <div class="bd-cat-name">Press-on Sets (E-Commerce)</div>
                            <div class="bd-cat-pct"><?php echo $reportData['press_pct']; ?>% of total revenue</div>
                        </div>
                        <div class="bd-cat-rev">$<?php echo number_format($reportData['press_rev'], 2); ?></div>
                    </div>

                    <div class="bd-cat">
                        <span class="bd-cat-dot" style="background: #E28EA2;"></span>
                        <div class="bd-cat-info">
                            <div class="bd-cat-name">Nail Bar Services (Bookings)</div>
                            <div class="bd-cat-pct"><?php echo $reportData['services_pct']; ?>% of total revenue</div>
                        </div>
                        <div class="bd-cat-rev">$<?php echo number_format($reportData['services_rev'], 2); ?></div>
                    </div>

                    <div class="bd-cat">
                        <span class="bd-cat-dot" style="background: #F0CED7;"></span>
                        <div class="bd-cat-info">
                            <div class="bd-cat-name">Care &amp; Accessories</div>
                            <div class="bd-cat-pct"><?php echo $reportData['access_pct']; ?>% of total revenue</div>
                        </div>
                        <div class="bd-cat-rev">$<?php echo number_format($reportData['accessories_rev'], 2); ?></div>
                    </div>
                </div>
            </div>

        </section>

        <!-- Best-Selling Products Section -->
        <section class="products-card">
            <div class="products-head">
                <span class="card-title">Best-Selling Products</span>
                <a href="<?php echo $base_url; ?>/index.php?page=admin_products" class="view-all">
                    <span>Manage Catalog</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>

            <table class="ptable">
                <thead>
                    <tr>
                        <th>Product &amp; Category</th>
                        <th>Units Sold</th>
                        <th>Revenue Generated</th>
                        <th>Product Share</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($reportData['top_products'])): ?>
                    <?php foreach ($reportData['top_products'] as $p):
                        $share = ($reportData['top_total'] > 0) ? round(($p['revenue'] / $reportData['top_total']) * 100, 1) : 0;
                        $img = !empty($p['image_url']) ? $p['image_url'] : $base_url . '/images/product-image-180.png';
                    ?>
                        <tr>
                            <td>
                                <div class="pcell">
                                    <img class="pimg" src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($p['product_name']); ?>" onerror="this.src='https://images.unsplash.com/photo-1632345031435-8727f6897d53?w=100&auto=format&fit=crop&q=80'">
                                    <div>
                                        <div class="pname"><?php echo htmlspecialchars($p['product_name']); ?></div>
                                        <div class="pmeta">
                                            <?php echo htmlspecialchars($p['category_name'] ?: 'Press-on Nails'); ?> &middot; Handcrafted Set
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="pnum"><?php echo (int)$p['units_sold']; ?> units</td>
                            <td class="pnum">$<?php echo number_format((float)$p['revenue'], 2); ?></td>
                            <td>
                                <div class="pshare-wrap">
                                    <div class="pshare-bar">
                                        <div class="pshare-bar-fill" style="width: <?php echo min(100, $share); ?>%;"></div>
                                    </div>
                                    <span class="pshare"><?php echo $share; ?>%</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 36px; color: var(--muted);">
                            No products sold during the selected date window.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </section>

        <!-- Recent Sales Ledger (Real-world Transaction Audit) -->
        <section class="ledger-card">
            <div class="products-head">
                <span class="card-title">Recent Order Transactions in Period</span>
                <span class="period-sub">Showing up to 10 latest orders</span>
            </div>

            <table class="ptable">
                <thead>
                    <tr>
                        <th>Order Ref</th>
                        <th>Customer</th>
                        <th>Email &amp; Contact</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Date &amp; Time</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($reportData['recent_orders'])): ?>
                    <?php foreach ($reportData['recent_orders'] as $ord): 
                        $st = strtolower($ord['status']);
                        $badgeClass = 'pending';
                        if (str_contains($st, 'complete') || str_contains($st, 'deliver')) $badgeClass = 'completed';
                        elseif (str_contains($st, 'process')) $badgeClass = 'processing';
                        elseif (str_contains($st, 'ship')) $badgeClass = 'shipped';
                        elseif (str_contains($st, 'cancel')) $badgeClass = 'cancelled';
                    ?>
                        <tr>
                            <td class="pnum">#ORD-<?php echo str_pad($ord['order_id'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td><strong><?php echo htmlspecialchars($ord['first_name'] . ' ' . $ord['last_name']); ?></strong></td>
                            <td style="color:var(--muted);"><?php echo htmlspecialchars($ord['email']); ?></td>
                            <td><?php echo htmlspecialchars($ord['payment_method']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $badgeClass; ?>">
                                    <?php echo htmlspecialchars(ucfirst($ord['status'])); ?>
                                </span>
                            </td>
                            <td style="color:var(--muted);"><?php echo date('M j, Y g:i A', strtotime($ord['created_at'])); ?></td>
                            <td class="pnum">$<?php echo number_format((float)$ord['total_amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding: 36px; color: var(--muted);">
                            No customer order transactions found in this period.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </section>

    </main>
</div>

</body>
</html>
