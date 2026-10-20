<?php
/**
 * views/admin/appointments.php — Admin Appointments & Salon Schedule
 * Monet Nails MVC Application
 */

function apt_link(string $base, array $extra = []): string {
    $q = array_merge([
        'page'      => 'admin_appointments',
        'search'    => $_GET['search']    ?? '',
        'category'  => $_GET['category']  ?? '',
        'status'    => $_GET['status']    ?? 'all',
        'time_slot' => $_GET['time_slot'] ?? 'all',
        'per_page'  => $_GET['per_page']  ?? 10,
        'pg'        => $_GET['pg']        ?? 1,
    ], $extra);
    return $base . '/index.php?' . http_build_query(array_filter($q, fn($v) => $v !== ''));
}

if (!function_exists('apt_status_info')) {
    function apt_status_info($s): array {
        switch (strtolower(trim($s ?? ''))) {
            case 'confirmed':
            case 'booked':    return ['Confirmed',      'st-confirmed'];
            case 'completed': return ['Completed',      'st-completed'];
            case 'pending':   return ['Pending Review', 'st-pending'];
            case 'cancelled':
            case 'canceled':  return ['Cancelled',      'st-cancelled'];
            default:          return [ucfirst($s ?: 'Pending'), 'st-pending'];
        }
    }
}

if (!function_exists('apt_category_info')) {
    function apt_category_info($cat): array {
        $c = strtolower(trim($cat ?? ''));
        if (str_contains($c, 'pedicure')) return ['Pedicure',     'cat-orange'];
        if (str_contains($c, '3d') || str_contains($c, 'art') || str_contains($c, 'consult')) return ['Bespoke 3D', 'cat-purple'];
        return ['Manicure', 'cat-pink'];
    }
}

if (!function_exists('apt_avatar_color')) {
    function apt_avatar_color($tier): string {
        $t = strtolower(trim($tier ?? ''));
        if (str_contains($t, 'vip'))    return 'av-pink';
        if (str_contains($t, 'new'))    return 'av-red';
        if (str_contains($t, 'repeat')) return 'av-pink';
        if (str_contains($t, 'bridal')) return 'av-purple';
        return 'av-orange';
    }
}

if (!function_exists('apt_initials')) {
    function apt_initials($first, $last): string {
        $f = trim($first ?? '');
        $l = trim($last ?? '');
        $in = strtoupper(substr($f, 0, 1) . substr($l, 0, 1));
        return !empty($in) ? $in : 'CL';
    }
}

if (!function_exists('apt_fmt_time')) {
    function apt_fmt_time($t): string {
        if (empty($t)) return '--';
        return date('g:i A', strtotime($t));
    }
}

if (!function_exists('apt_fmt_date')) {
    function apt_fmt_date($d): string {
        if (empty($d)) return '--';
        if ($d === date('Y-m-d')) return 'Today';
        if ($d === date('Y-m-d', strtotime('+1 day'))) return 'Tomorrow';
        return date('M j', strtotime($d));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments &amp; Salon Schedule - Monet Atelier Admin</title>
    <meta name="description" content="Admin salon appointments, stylist schedule allocation, and client dossier tracking.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Admin Appointments Stylesheet -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/admin_appointments.css">
</head>
<body class="admin-body">
<div class="page">
    
    <!-- ============== SIDEBAR ============== -->
    <aside class="sidebar">
        <div>
            <a href="<?php echo $base_url; ?>/index.php?page=admin" class="brand-row">
                <span class="brand-dot"></span>
                <span class="brand-name">Monet Atelier</span>
            </a>
            <div class="brand-sub">Nail Bar &amp; Press-On Studio</div>
        </div>

        <nav class="nav">
            <a href="<?php echo $base_url; ?>/index.php?page=admin" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo $base_url; ?>/index.php?page=admin_orders" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 L3 6 L3 20 a2 2 0 0 0 2 2 h14 a2 2 0 0 0 2 -2 V6 L18 2 Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10 a4 4 0 0 1 -8 0"/></svg>
                <span>Orders &amp; Queue</span>
            </a>
            <a href="<?php echo $base_url; ?>/index.php?page=admin_products" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8 L12 3 L3 8 V16 L12 21 L21 16 Z"/><line x1="3" y1="8" x2="12" y2="13"/><line x1="21" y1="8" x2="12" y2="13"/><line x1="12" y1="13" x2="12" y2="21"/></svg>
                <span>Products &amp; Stock</span>
            </a>
            <a href="<?php echo $base_url; ?>/index.php?page=admin_appointments" class="nav-item active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>Appointments</span>
            </a>
            <a href="<?php echo $base_url; ?>/index.php?page=admin_reports" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
                <span>Sales &amp; Reports</span>
            </a>
        </nav>

        <div class="tip-card">
            <div class="tip-head">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <span>Appointment Tip</span>
            </div>
            <p class="tip-text">
                Prioritize buffer intervals of 15 min between color consultations and extension sessions for optimal salon flow.
            </p>
            <div class="tip-foot">
                <span>Auto-buffer guard</span>
                <span class="tip-badge">Active</span>
            </div>
        </div>
    </aside>

    <!-- ============== WORKSPACE ============== -->
    <main class="workspace">
        
        <!-- Header -->
        <header class="ws-header">
            <div>
                <h1>Appointments &amp; Salon Schedule</h1>
                <p>Live salon bookings, stylist schedules, seat allocation, and client visit management.</p>
            </div>
            <div class="ws-actions">
                <button class="btn-outline" onclick="window.print()" id="btn-export-schedule">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export Schedule
                </button>
                <button class="btn-primary" id="btn-new-appointment" onclick="openCreateModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    New Appointment
                </button>
                <div class="profile">
                    <div class="avatar"><?php echo htmlspecialchars($adminInitials); ?></div>
                    <div>
                        <div class="profile-name"><?php echo htmlspecialchars($adminName); ?></div>
                        <div class="profile-role">Lead Stylist / Admin</div>
                    </div>
                    <a href="<?php echo $base_url; ?>/index.php?page=logout" class="logout-pill" title="Sign Out">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </a>
                </div>
            </div>
        </header>

        <!-- Flash Notice -->
        <?php if (!empty($flashMsg)): ?>
        <div class="flash" id="flash-msg">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            <?php echo htmlspecialchars($flashMsg); ?>
        </div>
        <?php endif; ?>

        <!-- Stat Cards -->
        <section class="stat-row">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span class="stat-chip chip-default">Salon Floor</span>
                </div>
                <div>
                    <div class="stat-label">Active Bookings</div>
                    <div class="stat-value"><?php echo $stats['active_count']; ?></div>
                    <div class="stat-note note-green">100% indexed</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span class="stat-chip chip-orange">&le; 2 Hrs</span>
                </div>
                <div>
                    <div class="stat-label">Pending Intake</div>
                    <div class="stat-value stat-value-orange"><?php echo $stats['pending_count']; ?></div>
                    <div class="stat-note note-orange">Needs review</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span class="stat-chip chip-red">Conflict</span>
                </div>
                <div>
                    <div class="stat-label">Schedule Overlaps</div>
                    <div class="stat-value stat-value-red"><?php echo $stats['conflict_count']; ?></div>
                    <div class="stat-note note-red"><?php echo $stats['conflict_count'] > 0 ? 'Resolution needed' : 'Zero overlaps'; ?></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    <span class="stat-chip chip-green">Live Capacity</span>
                </div>
                <div>
                    <div class="stat-label">Chair Utilization</div>
                    <div class="stat-value"><?php echo $stats['util_pct']; ?>%</div>
                    <div class="stat-note note-green"><?php echo $stats['completed_month']; ?> Completed</div>
                </div>
            </div>
        </section>

        <!-- Filter Bar -->
        <form method="GET" action="<?php echo $base_url; ?>/index.php" class="filter-bar" id="filter-form">
            <input type="hidden" name="page" value="admin_appointments">
            <div class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" id="search-input" placeholder="Search client name, service, stylist, or booking ID (#APT-0001)..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="filter-group">
                <select name="category" class="filter-select" onchange="this.form.submit()">
                    <option value="">Category: All Services</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php if ($category === $cat) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="all" <?php if ($status === 'all') echo 'selected'; ?>>Status: All</option>
                    <option value="confirmed" <?php if ($status === 'confirmed') echo 'selected'; ?>>Confirmed</option>
                    <option value="pending" <?php if ($status === 'pending') echo 'selected'; ?>>Pending Review</option>
                    <option value="completed" <?php if ($status === 'completed') echo 'selected'; ?>>Completed</option>
                    <option value="cancelled" <?php if ($status === 'cancelled') echo 'selected'; ?>>Cancelled</option>
                </select>

                <select name="time_slot" class="filter-select" onchange="this.form.submit()">
                    <option value="all" <?php if ($timeSlot === 'all') echo 'selected'; ?>>Time: All Slots</option>
                    <option value="morning" <?php if ($timeSlot === 'morning') echo 'selected'; ?>>Morning (Before 12 PM)</option>
                    <option value="afternoon" <?php if ($timeSlot === 'afternoon') echo 'selected'; ?>>Afternoon (12 PM – 4 PM)</option>
                    <option value="evening" <?php if ($timeSlot === 'evening') echo 'selected'; ?>>Evening (After 4 PM)</option>
                </select>

                <button type="submit" class="filter-btn">Search</button>
                <a href="<?php echo $base_url; ?>/index.php?page=admin_appointments" class="reset-link">Reset Filters</a>
            </div>
        </form>

        <!-- Appointments Table Wrap -->
        <section class="table-wrap">
            <div class="table-title">
                <span>Salon Appointments &mdash; <?php echo $totalRows; ?> booking<?php echo $totalRows !== 1 ? 's' : ''; ?></span>
                <span class="table-title-muted">Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></span>
            </div>

            <table class="appts-table">
                <thead>
                    <tr>
                        <th class="col-check"><input type="checkbox" id="chk-all"></th>
                        <th>Client / Dossier</th>
                        <th>Service &amp; Stylist</th>
                        <th>Duration &amp; Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($appts as $a):
                    $s_info = apt_status_info($a['status']);
                    $c_info = apt_category_info($a['category'] ?? '');
                    $av_cls = apt_avatar_color($a['client_tier'] ?? '');
                    $ini    = apt_initials($a['first_name'], $a['last_name']);
                    $apt_id = '#APT-' . str_pad((string)$a['appointment_id'], 4, '0', STR_PAD_LEFT);
                    $tier   = !empty($a['client_tier']) ? $a['client_tier'] : 'Patron';
                    $clientName = trim($a['first_name'] . ' ' . $a['last_name']);
                    if (empty($clientName)) $clientName = 'Guest Client';
                    $stationNum = ($a['appointment_id'] % 4) + 1;
                ?>
                    <tr data-appointment-id="<?php echo $a['appointment_id']; ?>">
                        <td class="col-check"><input type="checkbox" name="bulk[]" value="<?php echo $a['appointment_id']; ?>"></td>

                        <!-- Client / Dossier -->
                        <td>
                            <div class="client-cell">
                                <div class="av <?php echo $av_cls; ?>"><?php echo $ini; ?></div>
                                <div>
                                    <div class="client-name"><?php echo htmlspecialchars($clientName); ?></div>
                                    <div class="client-meta">
                                        <span class="apt-id"><?php echo $apt_id; ?></span>
                                        <span class="tier"><?php echo htmlspecialchars($tier); ?></span>
                                    </div>
                                    <div style="font-size:11.5px; color:var(--muted); margin-top:2px;">
                                        <?php echo htmlspecialchars($a['phone'] ?: ($a['email'] ?: 'No direct contact')); ?>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Service & Stylist -->
                        <td>
                            <div class="svc-name"><?php echo htmlspecialchars($a['service_name'] ?? 'Custom Service'); ?></div>
                            <div class="svc-cat-row">
                                <span class="cat-chip <?php echo $c_info[1]; ?>"><?php echo $c_info[0]; ?></span>
                            </div>
                            <div class="svc-stylist">
                                Lead Stylist • Station #<?php echo $stationNum; ?>
                            </div>
                        </td>

                        <!-- Duration & Time -->
                        <td>
                            <div class="time-main">
                                <?php echo apt_fmt_time($a['appointment_time']); ?>
                                (<?php echo apt_fmt_date($a['appointment_date']); ?>)
                            </div>
                            <div class="time-sub">
                                <?php echo (int)($a['duration_minutes'] ?: 45); ?> min &bull;
                                Rs. <?php echo number_format((float)$a['total_price'], 2); ?>
                            </div>
                        </td>

                        <!-- Status -->
                        <td>
                            <span class="status-badge <?php echo $s_info[1]; ?>" id="badge-<?php echo $a['appointment_id']; ?>">
                                <span class="status-dot"></span><?php echo $s_info[0]; ?>
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="col-actions">
                            <?php if ($a['status'] === 'pending'): ?>
                            <button class="action-btn btn-confirm" title="Confirm Booking" onclick="quickStatus(<?php echo $a['appointment_id']; ?>, 'confirmed')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </button>
                            <?php elseif ($a['status'] === 'confirmed' || $a['status'] === 'booked'): ?>
                            <button class="action-btn" title="Mark Completed" onclick="quickStatus(<?php echo $a['appointment_id']; ?>, 'completed')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                            </button>
                            <?php endif; ?>

                            <button class="action-btn" title="Reschedule Date/Time" onclick="openRescheduleModal(<?php echo $a['appointment_id']; ?>, '<?php echo $a['appointment_date']; ?>', '<?php echo substr($a['appointment_time'], 0, 5); ?>')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </button>

                            <button class="action-btn" title="Edit Dossier" onclick='openEditModal(<?php echo htmlspecialchars(json_encode($a), ENT_QUOTES, "UTF-8"); ?>)'>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>

                            <button class="action-btn btn-cancel-appt" title="Cancel/Delete" onclick="openDeleteModal(<?php echo $a['appointment_id']; ?>, '<?php echo addslashes(htmlspecialchars($clientName)); ?>')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($appts)): ?>
                    <tr>
                        <td colspan="6" class="empty-row">No appointments found matching your filter criteria.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination Footer -->
            <footer class="table-foot">
                <div class="showing">
                    Showing <strong><?php echo min(($currentPage - 1) * $perPage + 1, max($totalRows, 1)); ?>&ndash;<?php echo min($currentPage * $perPage, $totalRows); ?></strong>
                    of <strong><?php echo $totalRows; ?></strong> appointments
                </div>
                <div class="pagination">
                    <button class="page-btn" <?php if ($currentPage <= 1) echo 'disabled'; ?>
                            onclick="location.href='<?php echo apt_link($base_url, ['pg' => max(1, $currentPage - 1)]); ?>'">Previous</button>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <button class="page-btn <?php echo $i === $currentPage ? 'page-active' : ''; ?>"
                            onclick="location.href='<?php echo apt_link($base_url, ['pg' => $i]); ?>'">
                        <?php echo $i; ?>
                    </button>
                    <?php endfor; ?>
                    <button class="page-btn" <?php if ($currentPage >= $totalPages) echo 'disabled'; ?>
                            onclick="location.href='<?php echo apt_link($base_url, ['pg' => min($totalPages, $currentPage + 1)]); ?>'">Next</button>
                </div>
            </footer>
        </section>
    </main>
</div>

<!-- ================= CREATE APPOINTMENT MODAL ================= -->
<div class="modal-overlay" id="modal-create">
    <div class="modal" role="dialog" aria-labelledby="modal-create-title">
        <div class="modal-header">
            <h2 id="modal-create-title">New Salon Appointment</h2>
            <button class="modal-close" onclick="closeModal('modal-create')" aria-label="Close">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" action="<?php echo $base_url; ?>/index.php?page=admin_appointments">
            <input type="hidden" name="action" value="create_appointment">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="create-first-name">First Name *</label>
                        <input type="text" name="first_name" id="create-first-name" required placeholder="e.g. Camille">
                    </div>
                    <div class="form-group">
                        <label for="create-last-name">Last Name *</label>
                        <input type="text" name="last_name" id="create-last-name" required placeholder="e.g. Vance">
                    </div>
                    <div class="form-group">
                        <label for="create-email">Email Address</label>
                        <input type="email" name="email" id="create-email" placeholder="client@example.com">
                    </div>
                    <div class="form-group">
                        <label for="create-phone">Phone Number</label>
                        <input type="text" name="phone" id="create-phone" placeholder="+94 77 123 4567">
                    </div>
                    <div class="form-group">
                        <label for="create-service">Service *</label>
                        <select name="service_id" id="create-service" required onchange="updateServicePrice(this, 'create-price')">
                            <?php foreach ($services as $s): ?>
                            <option value="<?php echo $s['service_id']; ?>" data-price="<?php echo $s['price']; ?>">
                                <?php echo htmlspecialchars($s['name']); ?> &bull; Rs. <?php echo number_format($s['price'], 2); ?> (<?php echo $s['duration']; ?>m)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="create-tier">Client Tier</label>
                        <select name="client_tier" id="create-tier">
                            <option value="Patron">Patron (Standard)</option>
                            <option value="VIP Patron">VIP Patron</option>
                            <option value="New Client">New Client</option>
                            <option value="Repeat Patron">Repeat Patron</option>
                            <option value="Bridal Consultation">Bridal Consultation</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="create-date">Appointment Date *</label>
                        <input type="date" name="appointment_date" id="create-date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="create-time">Appointment Time Slot *</label>
                        <select name="appointment_time" id="create-time" required>
                            <?php foreach (Appointment::ALL_SLOTS as $slot): ?>
                            <option value="<?php echo $slot; ?>:00"><?php echo date('g:i A', strtotime($slot)); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="create-price">Total Price (LKR) *</label>
                        <input type="number" step="0.01" name="total_price" id="create-price" required value="<?php echo $services[0]['price'] ?? '3500.00'; ?>">
                    </div>
                    <div class="form-group">
                        <label for="create-status">Initial Status</label>
                        <select name="status" id="create-status">
                            <option value="confirmed">Confirmed</option>
                            <option value="pending">Pending Review</option>
                        </select>
                    </div>
                    <div class="form-group form-full">
                        <label for="create-notes">Special Requests / Styling Notes</label>
                        <textarea name="special_requests" id="create-notes" placeholder="e.g. Extra almond shaping requested, gentle cuticle care..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modal-create')">Cancel</button>
                <button type="submit" class="btn-primary">Book Appointment</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= EDIT APPOINTMENT MODAL ================= -->
<div class="modal-overlay" id="modal-edit">
    <div class="modal" role="dialog" aria-labelledby="modal-edit-title">
        <div class="modal-header">
            <h2 id="modal-edit-title">Edit Appointment Dossier</h2>
            <button class="modal-close" onclick="closeModal('modal-edit')" aria-label="Close">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" action="<?php echo $base_url; ?>/index.php?page=admin_appointments">
            <input type="hidden" name="action" value="edit_appointment">
            <input type="hidden" name="appointment_id" id="edit-id">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit-first-name">First Name *</label>
                        <input type="text" name="first_name" id="edit-first-name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-last-name">Last Name *</label>
                        <input type="text" name="last_name" id="edit-last-name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-email">Email Address</label>
                        <input type="email" name="email" id="edit-email">
                    </div>
                    <div class="form-group">
                        <label for="edit-phone">Phone Number</label>
                        <input type="text" name="phone" id="edit-phone">
                    </div>
                    <div class="form-group">
                        <label for="edit-service">Service *</label>
                        <select name="service_id" id="edit-service" required>
                            <?php foreach ($services as $s): ?>
                            <option value="<?php echo $s['service_id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-tier">Client Tier</label>
                        <select name="client_tier" id="edit-tier">
                            <option value="Patron">Patron</option>
                            <option value="VIP Patron">VIP Patron</option>
                            <option value="New Client">New Client</option>
                            <option value="Repeat Patron">Repeat Patron</option>
                            <option value="Bridal Consultation">Bridal Consultation</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-date">Date *</label>
                        <input type="date" name="appointment_date" id="edit-date" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-time">Time Slot *</label>
                        <select name="appointment_time" id="edit-time" required>
                            <?php foreach (Appointment::ALL_SLOTS as $slot): ?>
                            <option value="<?php echo $slot; ?>:00"><?php echo date('g:i A', strtotime($slot)); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-price">Total Price (LKR) *</label>
                        <input type="number" step="0.01" name="total_price" id="edit-price" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-status">Status</label>
                        <select name="status" id="edit-status">
                            <option value="pending">Pending Review</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group form-full">
                        <label for="edit-notes">Special Requests / Notes</label>
                        <textarea name="special_requests" id="edit-notes"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modal-edit')">Cancel</button>
                <button type="submit" class="btn-primary">Update Dossier</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= RESCHEDULE MODAL ================= -->
<div class="modal-overlay" id="modal-reschedule">
    <div class="modal" style="max-width:440px;" role="dialog" aria-labelledby="modal-resched-title">
        <div class="modal-header">
            <h2 id="modal-resched-title">Reschedule Visit</h2>
            <button class="modal-close" onclick="closeModal('modal-reschedule')" aria-label="Close">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" action="<?php echo $base_url; ?>/index.php?page=admin_appointments">
            <input type="hidden" name="action" value="reschedule">
            <input type="hidden" name="appointment_id" id="resched-id">
            <div class="modal-body">
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="resched-date">New Date *</label>
                    <input type="date" name="appointment_date" id="resched-date" required>
                </div>
                <div class="form-group">
                    <label for="resched-time">New Time Slot *</label>
                    <select name="appointment_time" id="resched-time" required>
                        <?php foreach (Appointment::ALL_SLOTS as $slot): ?>
                        <option value="<?php echo $slot; ?>:00"><?php echo date('g:i A', strtotime($slot)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modal-reschedule')">Cancel</button>
                <button type="submit" class="btn-primary">Confirm Reschedule</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= DELETE / CANCEL MODAL ================= -->
<div class="modal-overlay" id="modal-delete">
    <div class="modal" style="max-width:440px;" role="dialog" aria-labelledby="modal-del-title">
        <form method="POST" action="<?php echo $base_url; ?>/index.php?page=admin_appointments">
            <input type="hidden" name="action" value="delete_appointment">
            <input type="hidden" name="appointment_id" id="delete-id">
            <div class="modal-body" style="text-align:center; padding:24px 20px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:48px;height:48px;color:var(--red);margin:0 auto 12px;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                <h3 id="modal-del-title" style="font-size:18px; font-weight:700; margin-bottom:6px;">Cancel &amp; Remove Appointment?</h3>
                <p style="color:var(--muted); font-size:13px;">Are you sure you want to remove appointment for <strong id="delete-name"></strong>? This will release the station slot back to the salon schedule.</p>
            </div>
            <div class="modal-footer" style="justify-content:center;">
                <button type="button" class="btn-cancel" onclick="closeModal('modal-delete')">Keep Booking</button>
                <button type="submit" class="btn-danger">Confirm Cancellation</button>
            </div>
        </form>
    </div>
</div>

<script>
const BASE_URL = '<?php echo $base_url; ?>';

function openCreateModal() {
    document.getElementById('modal-create').classList.add('open');
}

function openEditModal(apt) {
    document.getElementById('edit-id').value         = apt.appointment_id;
    document.getElementById('edit-first-name').value = apt.first_name || '';
    document.getElementById('edit-last-name').value  = apt.last_name || '';
    document.getElementById('edit-email').value      = apt.email || '';
    document.getElementById('edit-phone').value      = apt.phone || '';
    document.getElementById('edit-tier').value       = apt.client_tier || 'Patron';
    document.getElementById('edit-service').value    = apt.service_id || 1;
    document.getElementById('edit-date').value       = apt.appointment_date || '';
    
    let timeVal = (apt.appointment_time || '10:00:00').substring(0, 5) + ':00';
    document.getElementById('edit-time').value       = timeVal;
    document.getElementById('edit-price').value      = apt.total_price || 0;
    document.getElementById('edit-status').value     = apt.status || 'pending';
    document.getElementById('edit-notes').value      = apt.special_requests || '';

    document.getElementById('modal-edit').classList.add('open');
}

function openRescheduleModal(id, date, time) {
    document.getElementById('resched-id').value   = id;
    document.getElementById('resched-date').value = date;
    document.getElementById('resched-time').value = time.substring(0, 5) + ':00';
    document.getElementById('modal-reschedule').classList.add('open');
}

function openDeleteModal(id, name) {
    document.getElementById('delete-id').value = id;
    document.getElementById('delete-name').textContent = name;
    document.getElementById('modal-delete').classList.add('open');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}

// Close backdrop
document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', e => {
        if (e.target === el) el.classList.remove('open');
    });
});

// Update price on service selection
function updateServicePrice(selectEl, priceInputId) {
    const selected = selectEl.options[selectEl.selectedIndex];
    const price = selected.getAttribute('data-price');
    if (price) {
        document.getElementById(priceInputId).value = parseFloat(price).toFixed(2);
    }
}

// Quick status AJAX toggle
async function quickStatus(id, newStatus) {
    const form = new FormData();
    form.append('action', 'update_status');
    form.append('appointment_id', id);
    form.append('status', newStatus);

    try {
        const res = await fetch(BASE_URL + '/index.php?page=admin_appointments', {
            method: 'POST',
            body: form,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();
        if (json.ok) {
            window.location.reload();
        }
    } catch (e) {
        console.error('Status update error:', e);
        window.location.reload();
    }
}

// Select all checkboxes
const chkAll = document.getElementById('chk-all');
if (chkAll) {
    chkAll.addEventListener('change', function() {
        document.querySelectorAll('input[name="bulk[]"]').forEach(cb => cb.checked = chkAll.checked);
    });
}

// Auto fade flash notice
const flash = document.getElementById('flash-msg');
if (flash) {
    setTimeout(() => {
        flash.style.transition = 'opacity 0.6s ease';
        flash.style.opacity = '0';
        setTimeout(() => flash.remove(), 600);
    }, 4000);
}
</script>
</body>
</html>
