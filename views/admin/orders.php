<?php
/**
 * views/admin/orders.php — Monet Atelier Admin
 * Orders & Queue Management Workspace
 * MVC Layer: View
 */

$base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';

function getOrderStatusClass(string $status): string {
    $st = strtolower(trim($status));
    return match ($st) {
        'pending'    => 'st-pending',
        'processing' => 'st-processing',
        'shipped'    => 'st-shipped',
        'delivered'  => 'st-delivered',
        'cancelled', 'canceled' => 'st-cancelled',
        default      => 'st-pending',
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders &amp; Queue — Monet Atelier Admin</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Admin Orders Stylesheet -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/admin_orders.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/admin_sidebar.css">

    <!-- Critical inline CSS: guarantees modal & action buttons render correctly -->
    <style>
    /* ===== ACTION BUTTONS ===== */
    .action-cell { display: flex; align-items: center; justify-content: center; gap: 6px; overflow: visible; }
    .btn-action {
        display: inline-flex; align-items: center; justify-content: center;
        width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--line);
        background: var(--white); cursor: pointer; transition: all 0.15s ease;
        flex-shrink: 0; overflow: visible;
    }
    .btn-action svg { width: 14px; height: 14px; display: block; flex-shrink: 0; }
    .btn-action.edit { color: var(--rose); }
    .btn-action.edit:hover { background: var(--rose-bg); border-color: var(--rose-soft); transform: translateY(-1px); box-shadow: 0 3px 8px rgba(190,83,109,0.15); }
    .btn-action.delete { color: var(--red); }
    .btn-action.delete:hover { background: var(--red-bg); border-color: #FECDD3; transform: translateY(-1px); box-shadow: 0 3px 8px rgba(220,38,38,0.12); }

    /* ===== TABLE OVERFLOW FIX ===== */
    .table-card { overflow-x: auto !important; }
    .orders-table { min-width: 1060px !important; width: 100%; border-collapse: collapse; }
    .orders-table td, .orders-table th { overflow: visible !important; }
    .action-col-head { width: 90px !important; min-width: 90px !important; text-align: center !important; }
    .action-col-cell { width: 90px !important; min-width: 90px !important; overflow: visible !important; }

    /* ===== MODAL OVERLAY ===== */
    #orderDetailModal {
        position: fixed !important; top: 0 !important; left: 0 !important;
        right: 0 !important; bottom: 0 !important;
        width: 100vw !important; height: 100vh !important;
        background: rgba(28, 18, 22, 0.65) !important;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 99999 !important;
        display: flex !important;
        align-items: center !important; justify-content: center !important;
        padding: 20px !important;
        opacity: 0 !important; visibility: hidden !important; pointer-events: none !important;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }
    #orderDetailModal.active {
        opacity: 1 !important; visibility: visible !important; pointer-events: auto !important;
    }
    #orderDetailModal .modal-dialog {
        background: #FFFFFF;
        border-radius: 24px;
        border: 1px solid #E5D9DC;
        box-shadow: 0 32px 72px -12px rgba(190,83,109,0.30), 0 0 0 1px rgba(240,230,233,0.7);
        width: 100%;
        max-width: 980px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transform: scale(0.94) translateY(16px);
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    }
    #orderDetailModal.active .modal-dialog {
        transform: scale(1) translateY(0);
    }
    /* Keep modal-header, modal-body, modal-footer readable */
    #orderDetailModal .modal-header {
        padding: 22px 28px;
        background: linear-gradient(135deg, #FFFDFC 0%, #FFF4F7 100%);
        border-bottom: 1px solid #F0E6E9;
        display: flex; align-items: center; justify-content: space-between;
        flex-shrink: 0;
    }
    #orderDetailModal .modal-title { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 26px; font-weight: 700; color: #2A2024; }
    #orderDetailModal .modal-title-row { display: flex; align-items: center; gap: 12px; }
    #orderDetailModal .modal-subtitle { font-size: 13px; color: #8C7C83; margin-top: 4px; }
    #orderDetailModal .modal-close-btn {
        width: 36px; height: 36px; border-radius: 50%;
        background: #fff; border: 1px solid #F0E6E9;
        display: flex; align-items: center; justify-content: center;
        color: #8C7C83; cursor: pointer; transition: all 0.2s ease; flex-shrink: 0;
    }
    #orderDetailModal .modal-close-btn svg { width: 16px; height: 16px; display: block; }
    #orderDetailModal .modal-close-btn:hover { background: #F9ECEF; border-color: #E28EA2; color: #BE536D; transform: rotate(90deg); }
    #orderDetailModal .modal-body {
        padding: 24px 28px; overflow-y: auto;
        display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 22px;
        background: #FCFAFA; flex: 1; min-height: 0;
    }
    #orderDetailModal .modal-col { display: flex; flex-direction: column; gap: 16px; }
    #orderDetailModal .modal-card {
        background: #fff; border: 1px solid #F0E6E9; border-radius: 18px;
        padding: 18px 20px; display: flex; flex-direction: column; gap: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    #orderDetailModal .modal-card.highlight-card { background: #FFFDFD; border-color: #F0CED7; }
    #orderDetailModal .card-head {
        display: flex; align-items: center; gap: 8px;
        font-size: 11px; font-weight: 700; color: #BE536D;
        text-transform: uppercase; letter-spacing: 0.6px;
        padding-bottom: 10px; border-bottom: 1px solid #F0E6E9;
    }
    #orderDetailModal .card-head.between { justify-content: space-between; }
    #orderDetailModal .card-head-left { display: flex; align-items: center; gap: 8px; }
    #orderDetailModal .badge-count {
        padding: 2px 8px; border-radius: 10px; background: #F9ECEF;
        color: #BE536D; font-size: 11px; font-weight: 600; letter-spacing: 0;
    }
    #orderDetailModal .client-name { font-size: 15px; font-weight: 700; color: #2A2024; }
    #orderDetailModal .client-meta-row { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 4px; }
    #orderDetailModal .client-link { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: #8C7C83; }
    #orderDetailModal .client-link.email { color: #BE536D; font-weight: 500; }
    #orderDetailModal .client-address-box { margin-top: 10px; padding-top: 10px; border-top: 1px dashed #F0E6E9; font-size: 12px; }
    #orderDetailModal .addr-label { font-weight: 600; color: #8C7C83; font-size: 11px; margin-bottom: 3px; text-transform: uppercase; letter-spacing: 0.4px; }
    #orderDetailModal .addr-text { color: #4D3F45; line-height: 1.5; }
    #orderDetailModal .modal-items-list { display: flex; flex-direction: column; gap: 8px; max-height: 230px; overflow-y: auto; padding-right: 2px; }
    #orderDetailModal .modal-item-row { display: flex; align-items: center; gap: 12px; padding: 8px 10px; border-radius: 12px; background: #FAF7F5; border: 1px solid #F0E6E9; }
    #orderDetailModal .modal-item-thumb { width: 44px; height: 44px; border-radius: 10px; object-fit: cover; background: #fff; border: 1px solid #F0E6E9; flex-shrink: 0; }
    #orderDetailModal .modal-item-info { flex: 1; min-width: 0; }
    #orderDetailModal .modal-item-name { font-size: 13px; font-weight: 600; color: #2A2024; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    #orderDetailModal .modal-item-variant { font-size: 11px; color: #8C7C83; margin-top: 2px; }
    #orderDetailModal .modal-item-price { font-size: 13px; font-weight: 700; color: #2A2024; white-space: nowrap; }
    #orderDetailModal .summary-line { display: flex; justify-content: space-between; padding: 4px 0; font-size: 13px; color: #8C7C83; }
    #orderDetailModal .summary-line.total-line { margin-top: 8px; padding-top: 10px; border-top: 1px solid #F0E6E9; font-weight: 700; font-size: 15px; color: #2A2024; }
    #orderDetailModal .total-highlight { font-size: 18px; color: #BE536D; font-family: 'Cormorant Garamond', Georgia, serif; font-weight: 700; }
    #orderDetailModal .payment-method-row { margin-top: 8px; }
    #orderDetailModal .method-tag { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 10px; background: #FAF7F5; border: 1px solid #F0E6E9; font-size: 12px; color: #4D3F45; }
    #orderDetailModal .modal-form { display: flex; flex-direction: column; gap: 12px; }
    #orderDetailModal .form-group { display: flex; flex-direction: column; gap: 5px; }
    #orderDetailModal .form-label { font-size: 11px; font-weight: 700; color: #8C7C83; text-transform: uppercase; letter-spacing: 0.5px; }
    #orderDetailModal .form-select, #orderDetailModal .form-control, #orderDetailModal .form-textarea {
        width: 100%; padding: 9px 12px; border: 1px solid #E5D9DC; border-radius: 12px;
        background: #fff; font-family: inherit; font-size: 13px; color: #2A2024; outline: none;
        transition: all 0.15s ease;
    }
    #orderDetailModal .form-select:focus, #orderDetailModal .form-control:focus, #orderDetailModal .form-textarea:focus { border-color: #BE536D; box-shadow: 0 0 0 3px rgba(190,83,109,0.12); }
    #orderDetailModal .form-textarea { min-height: 60px; resize: vertical; }
    #orderDetailModal .btn-save-fulfillment {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; padding: 11px; margin-top: 4px; border-radius: 14px;
        background: #BE536D; color: #fff; font-size: 13px; font-weight: 600;
        border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(190,83,109,0.25);
        transition: all 0.15s ease; font-family: inherit;
    }
    #orderDetailModal .btn-save-fulfillment:hover { background: #a33f57; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(190,83,109,0.35); }
    #orderDetailModal .quick-actions-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    #orderDetailModal .btn-action-tile {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 6px; padding: 12px 10px; border-radius: 14px;
        background: #FAF7F5; border: 1px solid #F0E6E9;
        color: #4D3F45; font-size: 12px; font-weight: 600; cursor: pointer;
        text-align: center; transition: all 0.15s ease; text-decoration: none;
        font-family: inherit;
    }
    #orderDetailModal .btn-action-tile svg { color: #BE536D; width: 18px; height: 18px; }
    #orderDetailModal .btn-action-tile:hover { background: #F9ECEF; border-color: #E28EA2; color: #BE536D; transform: translateY(-1px); }
    #orderDetailModal .modal-footer {
        padding: 14px 28px; background: #fff; border-top: 1px solid #F0E6E9;
        display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;
    }
    .modal-footer-note { font-size: 12px; color: #8C7C83; }
    #orderDetailModal .btn-modal-close-secondary {
        padding: 8px 20px; border-radius: 12px; background: #FAF7F8;
        border: 1px solid #F0E6E9; color: #4D3F45; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.15s ease; font-family: inherit;
    }
    #orderDetailModal .btn-modal-close-secondary:hover { background: #F9ECEF; color: #BE536D; border-color: #E28EA2; }
    /* Delete confirm modal */
    #deleteConfirmModal {
        position: fixed !important; top: 0 !important; left: 0 !important;
        width: 100vw !important; height: 100vh !important;
        background: rgba(28,18,22,0.70) !important;
        backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
        z-index: 100000 !important;
        display: flex !important; align-items: center !important; justify-content: center !important;
        opacity: 0 !important; visibility: hidden !important; pointer-events: none !important;
        transition: opacity 0.2s ease, visibility 0.2s ease;
    }
    #deleteConfirmModal.active { opacity: 1 !important; visibility: visible !important; pointer-events: auto !important; }
    .delete-confirm-dialog {
        background: #fff; border-radius: 20px; padding: 32px 36px;
        max-width: 420px; width: 90%; text-align: center;
        box-shadow: 0 24px 60px -8px rgba(220,38,38,0.22);
        border: 1px solid #FFE4E6; transform: scale(0.92);
        transition: transform 0.22s cubic-bezier(0.16,1,0.3,1);
    }
    #deleteConfirmModal.active .delete-confirm-dialog { transform: scale(1); }
    .delete-confirm-icon { width: 56px; height: 56px; border-radius: 50%; background: #FFE4E6; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
    .delete-confirm-icon svg { width: 26px; height: 26px; color: #DC2626; }
    .delete-confirm-title { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 22px; font-weight: 700; color: #2A2024; margin-bottom: 8px; }
    .delete-confirm-body { font-size: 13px; color: #8C7C83; line-height: 1.5; margin-bottom: 24px; }
    .delete-confirm-order-id { font-weight: 700; color: #BE536D; }
    .delete-confirm-actions { display: flex; gap: 10px; justify-content: center; }
    .btn-cancel-delete { padding: 10px 22px; border-radius: 12px; background: #FAF7F8; border: 1px solid #F0E6E9; color: #4D3F45; font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit; transition: all 0.15s; }
    .btn-cancel-delete:hover { background: #F9ECEF; color: #BE536D; }
    .btn-confirm-delete { padding: 10px 22px; border-radius: 12px; background: #DC2626; color: #fff; border: none; font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit; transition: all 0.15s; box-shadow: 0 4px 12px rgba(220,38,38,0.22); }
    .btn-confirm-delete:hover { background: #b91c1c; transform: translateY(-1px); }
    @media (max-width: 768px) {
        #orderDetailModal .modal-body { grid-template-columns: 1fr; }
        #orderDetailModal .quick-actions-grid { grid-template-columns: 1fr; }
        #orderDetailModal { padding: 12px !important; }
        #orderDetailModal .modal-header, #orderDetailModal .modal-body, #orderDetailModal .modal-footer { padding: 16px; }
    }
    </style>
</head>
<body class="admin-body">

<div class="page">

    <!-- ============== 1. SIDEBAR ============== -->
    <?php
    $activePage = 'admin_orders';
    $sidebarTip = [
        'label'  => 'FULFILLMENT GUARD',
        'text'   => 'Dispatched orders should have tracking numbers attached to keep clients informed.',
        'guard'  => 'Queue Live',
        'status' => 'Active',
    ];
    include __DIR__ . '/../../includes/admin_sidebar.php';
    ?>

    <!-- ============== 2. ORDERS WORKSPACE (MIDDLE) ============== -->
    <main class="orders-col">

        <!-- Top Header -->
        <header class="orders-header">
            <div>
                <h1>Orders &amp; Queue</h1>
                <p>Manage, verify, and fulfill customer press-on nail sets and salon merchandise.</p>
            </div>
            <div class="header-actions">
                <a href="<?php echo $base_url; ?>/index.php?page=admin_orders&export=csv&filter=<?php echo urlencode($filter); ?>&search=<?php echo urlencode($search); ?>" class="btn-pill" title="Export current orders list to CSV">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Export CSV
                </a>

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

        <!-- Flash Notice -->
        <?php if (!empty($flashSuccess)): ?>
            <div class="alert-success">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span><?php echo htmlspecialchars($flashSuccess); ?></span>
            </div>
        <?php endif; ?>

        <!-- Stat Cards -->
        <section class="stat-row">
            <div class="stat-card">
                <div class="stat-label">Total Orders</div>
                <div class="stat-number"><?php echo number_format($stats['total']); ?></div>
                <div class="stat-pill-sub">All-time salon volume</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Pending Verification</div>
                <div class="stat-number stat-num-red"><?php echo number_format($stats['pending']); ?></div>
                <span class="stat-pill stat-pill-red">Needs review</span>
            </div>

            <div class="stat-card">
                <div class="stat-label">Processing / Atelier</div>
                <div class="stat-number stat-num-orange"><?php echo number_format($stats['processing']); ?></div>
                <span class="stat-pill stat-pill-orange">In production</span>
            </div>

            <div class="stat-card">
                <div class="stat-label">Shipped &amp; Delivered</div>
                <div class="stat-number stat-num-green"><?php echo number_format($stats['shipped']); ?></div>
                <span class="stat-pill stat-pill-green">Fulfilled</span>
            </div>
        </section>

        <!-- Search & Filter Controls -->
        <section class="filter-row">
            <!-- Search Form -->
            <form method="GET" action="<?php echo $base_url; ?>/index.php" class="search-box">
                <input type="hidden" name="page" value="admin_orders">
                <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" name="search" placeholder="Search orders by #ID, customer name, email, or tracking..." value="<?php echo htmlspecialchars($search); ?>">
            </form>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <a href="<?php echo $base_url; ?>/index.php?page=admin_orders&filter=all<?php echo $search ? '&search='.urlencode($search) : ''; ?>" 
                   class="filter-tab <?php echo ($filter === 'all') ? 'active' : ''; ?>">
                   All (<?php echo $stats['total']; ?>)
                </a>
                <a href="<?php echo $base_url; ?>/index.php?page=admin_orders&filter=pending<?php echo $search ? '&search='.urlencode($search) : ''; ?>" 
                   class="filter-tab <?php echo ($filter === 'pending') ? 'active' : ''; ?>">
                   Pending (<?php echo $stats['pending']; ?>)
                </a>
                <a href="<?php echo $base_url; ?>/index.php?page=admin_orders&filter=processing<?php echo $search ? '&search='.urlencode($search) : ''; ?>" 
                   class="filter-tab <?php echo ($filter === 'processing') ? 'active' : ''; ?>">
                   Processing (<?php echo $stats['processing']; ?>)
                </a>
                <a href="<?php echo $base_url; ?>/index.php?page=admin_orders&filter=shipped<?php echo $search ? '&search='.urlencode($search) : ''; ?>" 
                   class="filter-tab <?php echo ($filter === 'shipped') ? 'active' : ''; ?>">
                   Shipped (<?php echo $stats['shipped']; ?>)
                </a>
                <a href="<?php echo $base_url; ?>/index.php?page=admin_orders&filter=cancelled<?php echo $search ? '&search='.urlencode($search) : ''; ?>" 
                   class="filter-tab <?php echo ($filter === 'cancelled') ? 'active' : ''; ?>">
                   Cancelled (<?php echo $stats['cancelled']; ?>)
                </a>
            </div>
        </section>

        <!-- Orders Table Card -->
        <!-- Orders Table Card -->
        <section class="table-card">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th style="width: 120px;">Order ID</th>
                        <th style="min-width: 180px;">Client</th>
                        <th style="min-width: 240px;">Items Ordered</th>
                        <th style="width: 150px;">Date Placed</th>
                        <th style="width: 110px;">Total</th>
                        <th style="width: 130px;">Status</th>
                        <th class="action-col-head">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $o):
                        $oid = (int)$o['order_id'];
                        $isSelected = ($oid === $selectedOrderId);
                    ?>
                        <tr class="<?php echo $isSelected ? 'row-selected' : ''; ?>"
                            data-order-id="<?php echo $oid; ?>"
                            onclick="openOrderModal(<?php echo $oid; ?>)"
                            style="cursor: pointer;">
                            <td class="order-id">
                                <span class="order-id-badge">#ORD-<?php echo str_pad($oid, 4, "0", STR_PAD_LEFT); ?></span>
                            </td>
                            <td>
                                <div class="cust-name"><?php echo htmlspecialchars($o['customer_name']); ?></div>
                                <div class="cust-email"><?php echo htmlspecialchars($o['customer_email']); ?></div>
                                <?php if (!empty($o['customer_phone'])): ?>
                                    <div class="cust-phone"><?php echo htmlspecialchars($o['customer_phone']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="item-text">
                                <div class="items-summary-list">
                                    <?php if (!empty($o['items'])): ?>
                                        <?php foreach ($o['items'] as $it): ?>
                                            <span class="item-chip" title="<?php echo htmlspecialchars($it['product_name']); ?>">
                                                <?php echo htmlspecialchars($it['product_name']); ?> 
                                                <span class="chip-qty">&times;<?php echo (int)$it['quantity']; ?></span>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="item-chip-muted">Custom press-on set</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="date-cell">
                                <div class="date-main"><?php echo date("M j, Y", strtotime($o['created_at'])); ?></div>
                                <div class="date-sub"><?php echo date("g:i A", strtotime($o['created_at'])); ?></div>
                            </td>
                            <td class="total-cell">
                                <span class="amount-val">$<?php echo number_format((float)$o['total_amount'], 2); ?></span>
                            </td>
                            <td>
                                <span class="badge <?php echo getOrderStatusClass($o['status']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($o['status'])); ?>
                                </span>
                            </td>
                            <td class="action-col-cell" onclick="event.stopPropagation();">
                                <div class="action-cell">
                                    <!-- Edit / View Details -->
                                    <button type="button" class="btn-action edit" onclick="openOrderModal(<?php echo $oid; ?>)" title="View & Edit Order #ORD-<?php echo str_pad($oid, 4, '0', STR_PAD_LEFT); ?>">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </button>
                                    <!-- Delete -->
                                    <button type="button" class="btn-action delete" onclick="confirmDeleteOrder(<?php echo $oid; ?>, '<?php echo addslashes(htmlspecialchars($o['customer_name'])); ?>')" title="Delete Order #ORD-<?php echo str_pad($oid, 4, '0', STR_PAD_LEFT); ?>">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                            <path d="M10 11v6"></path>
                                            <path d="M14 11v6"></path>
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding: 48px; color: var(--muted);">
                            No orders found matching the selected filter or search criteria.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </section>

        <!-- Pagination Footer -->
        <footer class="table-foot">
            <div>
                Showing <strong><?php echo count($orders); ?></strong> of <strong><?php echo $totalRows; ?></strong> orders
            </div>
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($currentPage > 1): ?>
                        <a href="<?php echo $base_url; ?>/index.php?page=admin_orders&p=<?php echo $currentPage - 1; ?>&filter=<?php echo urlencode($filter); ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="pg-btn">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="<?php echo $base_url; ?>/index.php?page=admin_orders&p=<?php echo $i; ?>&filter=<?php echo urlencode($filter); ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" 
                           class="pg-btn <?php echo ($i === $currentPage) ? 'active' : ''; ?>">
                           <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?php echo $base_url; ?>/index.php?page=admin_orders&p=<?php echo $currentPage + 1; ?>&filter=<?php echo urlencode($filter); ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="pg-btn">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </footer>

    </main>

</div><!-- end .page -->

<!-- ============== 3. ORDER DETAIL POPUP MODAL WINDOW ============== -->
<?php
$dId = $orderDetail ? (int)$orderDetail['order_id'] : 0;
?>
<div id="orderDetailModal" class="modal-backdrop <?php echo $orderDetail ? 'active' : ''; ?>" onclick="handleModalBackdropClick(event)">
    <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="modalOrderTitle">
        
        <!-- Modal Header -->
        <div class="modal-header">
            <div class="modal-header-info">
                <div class="modal-title-row">
                    <h2 id="modalOrderTitle" class="modal-title">
                        Order <span id="modalOrderNumber">#ORD-<?php echo $orderDetail ? str_pad($dId, 4, "0", STR_PAD_LEFT) : '----'; ?></span>
                    </h2>
                    <span id="modalStatusBadge" class="badge <?php echo $orderDetail ? getOrderStatusClass($orderDetail['status']) : 'st-pending'; ?>">
                        <?php echo $orderDetail ? htmlspecialchars(ucfirst($orderDetail['status'])) : 'Pending'; ?>
                    </span>
                </div>
                <div class="modal-subtitle">
                    Placed <span id="modalPlacedDate"><?php echo $orderDetail ? date("F j, Y · g:i A", strtotime($orderDetail['created_at'])) : '—'; ?></span>
                </div>
            </div>
            
            <button type="button" class="modal-close-btn" onclick="closeOrderModal()" title="Close (Esc)" aria-label="Close modal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Modal Body: 2 columns -->
        <div class="modal-body">
            
            <!-- Column 1: Client info, Ordered Items, Financial Summary -->
            <div class="modal-col modal-col-left">
                
                <!-- Client & Shipping Dossier -->
                <div class="modal-card">
                    <div class="card-head">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Client &amp; Delivery Destination</span>
                    </div>
                    <div>
                        <div class="client-name" id="modalCustomerName">
                            <?php echo $orderDetail ? htmlspecialchars($orderDetail['customer_name']) : '—'; ?>
                        </div>
                        <div class="client-meta-row">
                            <a id="modalCustomerEmail" href="<?php echo $orderDetail ? 'mailto:'.htmlspecialchars($orderDetail['customer_email']) : '#'; ?>" class="client-link email">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                <span id="modalCustomerEmailText"><?php echo $orderDetail ? htmlspecialchars($orderDetail['customer_email']) : '—'; ?></span>
                            </a>
                            <a id="modalCustomerPhone" href="<?php echo ($orderDetail && !empty($orderDetail['customer_phone'])) ? 'tel:'.htmlspecialchars($orderDetail['customer_phone']) : '#'; ?>" class="client-link phone" style="<?php echo ($orderDetail && !empty($orderDetail['customer_phone'])) ? '' : 'display:none;'; ?>">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                <span id="modalCustomerPhoneText"><?php echo $orderDetail ? htmlspecialchars($orderDetail['customer_phone']) : ''; ?></span>
                            </a>
                        </div>
                        <div class="client-address-box">
                            <div class="addr-label">Delivery Address:</div>
                            <div id="modalCustomerAddress" class="addr-text">
                                <?php echo $orderDetail ? nl2br(htmlspecialchars($orderDetail['customer_address'] ?: 'No address specified')) : '—'; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ordered Items Breakdown -->
                <div class="modal-card">
                    <div class="card-head between">
                        <div class="card-head-left">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span>Ordered Items</span>
                        </div>
                        <span id="modalItemsCount" class="badge-count">
                            <?php echo $orderDetail ? count($orderDetail['items']) : 0; ?> set<?php echo ($orderDetail && count($orderDetail['items']) === 1) ? '' : 's'; ?>
                        </span>
                    </div>
                    <div id="modalItemsList" class="modal-items-list">
                        <?php if ($orderDetail && !empty($orderDetail['items'])): ?>
                            <?php foreach ($orderDetail['items'] as $it):
                                $img = !empty($it['image_url']) ? $it['image_url'] : $base_url . '/images/product-image-180.png';
                                $subtotal = !empty($it['subtotal']) ? (float)$it['subtotal'] : ((float)$it['unit_price'] * (int)$it['quantity']);
                                $variantParts = array_filter([$it['shape'] ?? '', $it['finish'] ?? '', $it['size_preset'] ?? '']);
                                $variantStr = !empty($variantParts) ? implode(' · ', $variantParts) : 'Standard Sizing';
                            ?>
                                <div class="modal-item-row">
                                    <img class="modal-item-thumb" src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($it['product_name']); ?>" onerror="this.src='https://images.unsplash.com/photo-1632345031435-8727f6897d53?w=80&auto=format&fit=crop&q=80'">
                                    <div class="modal-item-info">
                                        <div class="modal-item-name"><?php echo htmlspecialchars($it['product_name']); ?></div>
                                        <div class="modal-item-variant">
                                            <?php echo htmlspecialchars($variantStr); ?> &middot; <span class="modal-item-qty">Qty: <?php echo (int)$it['quantity']; ?></span> &times; $<?php echo number_format((float)$it['unit_price'], 2); ?>
                                        </div>
                                    </div>
                                    <div class="modal-item-price">$<?php echo number_format($subtotal, 2); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php elseif ($orderDetail): ?>
                            <div style="padding:16px 0; color:var(--muted); font-size:12px; text-align:center;">
                                No items recorded for this order.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="modal-card">
                    <div class="card-head">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                            <line x1="2" y1="10" x2="22" y2="10"></line>
                        </svg>
                        <span>Payment &amp; Financial Summary</span>
                    </div>
                    <div>
                        <div class="summary-line">
                            <span>Items Subtotal</span>
                            <span id="modalSubtotal">$<?php echo $orderDetail ? number_format((float)$orderDetail['subtotal'], 2) : '0.00'; ?></span>
                        </div>
                        <div class="summary-line">
                            <span>Shipping Fee</span>
                            <span id="modalShippingFee"><?php echo ($orderDetail && (float)$orderDetail['shipping_fee'] > 0) ? '$'.number_format((float)$orderDetail['shipping_fee'], 2) : 'Free Shipping'; ?></span>
                        </div>
                        <div class="summary-line">
                            <span>Tax</span>
                            <span id="modalTax">$<?php echo $orderDetail ? number_format((float)($orderDetail['tax'] ?? 0), 2) : '0.00'; ?></span>
                        </div>
                        <div class="summary-line total-line">
                            <span>Total Amount</span>
                            <span id="modalTotalPaid" class="total-highlight">$<?php echo $orderDetail ? number_format((float)$orderDetail['total_amount'], 2) : '0.00'; ?></span>
                        </div>
                        <div class="payment-method-row">
                            <div class="method-tag">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                <span>Method: <strong id="modalPaymentMethod"><?php echo $orderDetail ? htmlspecialchars($orderDetail['payment_method']) : 'Card'; ?></strong></span>
                                <span id="modalPaymentStatus" class="pay-status" style="<?php echo ($orderDetail && !empty($orderDetail['payment_status'])) ? '' : 'display:none;'; ?>">
                                    &middot; <?php echo $orderDetail ? htmlspecialchars(ucfirst($orderDetail['payment_status'] ?? '')) : ''; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Column 2: Fulfillment & Status Controls Form + Quick Actions -->
            <div class="modal-col modal-col-right">
                
                <!-- Status & Fulfillment Form -->
                <div class="modal-card highlight-card">
                    <div class="card-head">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        <span>Fulfillment &amp; Status Controls</span>
                    </div>
                    
                    <form id="modalFulfillmentForm" method="POST" action="<?php echo $base_url; ?>/index.php?page=admin_orders&filter=<?php echo urlencode($filter); ?>&p=<?php echo $currentPage; ?>" class="modal-form">
                        <input type="hidden" name="action" value="update_fulfillment">
                        <input type="hidden" id="modalFormOrderId" name="order_id" value="<?php echo $orderDetail ? $dId : 0; ?>">

                        <div class="form-group">
                            <label class="form-label" for="modalStatusSelect">Lifecycle Status</label>
                            <select id="modalStatusSelect" name="status" class="form-select">
                                <option value="Pending"    <?php echo ($orderDetail && strcasecmp($orderDetail['status'], 'Pending') === 0) ? 'selected' : ''; ?>>Pending (Verification)</option>
                                <option value="Processing" <?php echo ($orderDetail && strcasecmp($orderDetail['status'], 'Processing') === 0) ? 'selected' : ''; ?>>Processing (Atelier Crafting)</option>
                                <option value="Shipped"    <?php echo ($orderDetail && strcasecmp($orderDetail['status'], 'Shipped') === 0) ? 'selected' : ''; ?>>Shipped (Dispatched to Courier)</option>
                                <option value="Delivered"  <?php echo ($orderDetail && strcasecmp($orderDetail['status'], 'Delivered') === 0) ? 'selected' : ''; ?>>Delivered (Fulfilled)</option>
                                <option value="Cancelled"  <?php echo ($orderDetail && (strcasecmp($orderDetail['status'], 'Cancelled') === 0 || strcasecmp($orderDetail['status'], 'Canceled') === 0)) ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="modalTrackingInput">Courier Tracking Number / Link</label>
                            <input type="text" id="modalTrackingInput" name="tracking_number" class="form-control" placeholder="e.g. PRMPT-9823471 or tracking link" value="<?php echo $orderDetail ? htmlspecialchars($orderDetail['tracking_number'] ?? '') : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="modalNotesTextarea">Atelier Notes / Instructions</label>
                            <textarea id="modalNotesTextarea" name="admin_notes" class="form-textarea" placeholder="Internal production notes, custom sizing specifications, courier notes..."><?php echo $orderDetail ? htmlspecialchars($orderDetail['admin_notes'] ?? '') : ''; ?></textarea>
                        </div>

                        <button type="submit" class="btn-save-fulfillment">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            <span>Save Status &amp; Fulfillment</span>
                        </button>
                    </form>
                </div>

                <!-- Quick Actions -->
                <div class="modal-card">
                    <div class="card-head">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                        <span>Atelier Actions</span>
                    </div>
                    <div class="quick-actions-grid">
                        <button type="button" class="btn-action-tile" onclick="window.print()" title="Print Packing Slip">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                <rect x="6" y="14" width="12" height="8"></rect>
                            </svg>
                            <span>Print Packing Slip</span>
                        </button>

                        <a id="modalEmailClientLink" href="<?php echo $orderDetail ? 'mailto:'.htmlspecialchars($orderDetail['customer_email']).'?subject=Update%20regarding%20your%20Monet%20Nails%20Order%20%23ORD-'.str_pad($dId, 4, '0', STR_PAD_LEFT) : '#'; ?>" class="btn-action-tile" title="Email Client">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <span>Contact Client</span>
                        </a>
                    </div>
                </div>

            </div>

        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
            <span class="modal-footer-note">Changes are saved automatically when you click the save button above.</span>
            <button type="button" class="btn-modal-close-secondary" onclick="closeOrderModal()">
                Close
            </button>
        </div>

    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" onclick="if(event.target===this)closeDeleteModal()">
    <div class="delete-confirm-dialog">
        <div class="delete-confirm-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                <path d="M10 11v6"></path><path d="M14 11v6"></path>
                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
            </svg>
        </div>
        <div class="delete-confirm-title">Delete Order?</div>
        <div class="delete-confirm-body">
            You are about to permanently delete order
            <span class="delete-confirm-order-id" id="deleteOrderLabel"></span>.
            <br>This action <strong>cannot be undone</strong>.
        </div>
        <div class="delete-confirm-actions">
            <button type="button" class="btn-cancel-delete" onclick="closeDeleteModal()">Cancel</button>
            <form id="deleteOrderForm" method="POST" action="" style="display:inline;">
                <input type="hidden" name="action" value="delete_order">
                <input type="hidden" name="order_id" id="deleteOrderIdInput" value="">
                <button type="submit" class="btn-confirm-delete">Yes, Delete Order</button>
            </form>
        </div>
    </div>
</div>

<!-- Raw Orders Dataset for instant modal populating without extra requests -->
<script id="ordersDataJson" type="application/json">
<?php echo json_encode($orders, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
</script>

<!-- Client Modal Controller Script -->
<script>
const ordersMap = {};
try {
    const rawData = JSON.parse(document.getElementById('ordersDataJson').textContent || '[]');
    rawData.forEach(ord => {
        ordersMap[parseInt(ord.order_id, 10)] = ord;
    });
} catch(e) {
    console.error('Error loading orders JSON map:', e);
}

function getStatusBadgeClass(st) {
    st = (st || '').toLowerCase().trim();
    if (st === 'pending') return 'st-pending';
    if (st === 'processing') return 'st-processing';
    if (st === 'shipped') return 'st-shipped';
    if (st === 'delivered') return 'st-delivered';
    if (st === 'cancelled' || st === 'canceled') return 'st-cancelled';
    return 'st-pending';
}

function padOrderId(id) {
    return '#ORD-' + String(id).padStart(4, '0');
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    try {
        const d = new Date(dateStr.replace(/-/g, '/'));
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        }) + ' · ' + d.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    } catch(e) {
        return dateStr;
    }
}

function openOrderModal(orderId) {
    const ord = ordersMap[parseInt(orderId, 10)];
    const modal = document.getElementById('orderDetailModal');
    if (!modal) return;

    if (!ord) {
        // Fallback: navigate with order_id param if not found in current page's map
        const url = new URL(window.location.href);
        url.searchParams.set('order_id', orderId);
        window.location.href = url.toString();
        return;
    }

    const oid = parseInt(ord.order_id, 10);
    const ordNumberStr = padOrderId(oid);
    const statusVal = ord.status || 'Pending';

    // Header info
    document.getElementById('modalOrderNumber').textContent = ordNumberStr;
    document.getElementById('modalPlacedDate').textContent = formatDate(ord.created_at || ord.order_date);
    
    const badge = document.getElementById('modalStatusBadge');
    badge.className = 'badge ' + getStatusBadgeClass(statusVal);
    badge.textContent = statusVal.charAt(0).toUpperCase() + statusVal.slice(1);

    // Client details
    document.getElementById('modalCustomerName').textContent = ord.customer_name || 'Guest Client';
    
    const emailElem = document.getElementById('modalCustomerEmail');
    const emailText = document.getElementById('modalCustomerEmailText');
    if (ord.customer_email) {
        emailElem.href = 'mailto:' + ord.customer_email;
        emailText.textContent = ord.customer_email;
        emailElem.style.display = 'inline-flex';
    } else {
        emailElem.style.display = 'none';
    }

    const phoneElem = document.getElementById('modalCustomerPhone');
    const phoneText = document.getElementById('modalCustomerPhoneText');
    if (ord.customer_phone) {
        phoneElem.href = 'tel:' + ord.customer_phone;
        phoneText.textContent = ord.customer_phone;
        phoneElem.style.display = 'inline-flex';
    } else {
        phoneElem.style.display = 'none';
    }

    const addrElem = document.getElementById('modalCustomerAddress');
    addrElem.innerHTML = ord.customer_address ? ord.customer_address.replace(/\n/g, '<br>') : 'No address specified';

    // Items list
    const itemsList = document.getElementById('modalItemsList');
    const itemsCount = document.getElementById('modalItemsCount');
    const items = ord.items || [];
    itemsCount.textContent = items.length + ' set' + (items.length === 1 ? '' : 's');

    if (items.length > 0) {
        let itemsHtml = '';
        items.forEach(it => {
            const img = it.image_url || '<?php echo $base_url; ?>/images/product-image-180.png';
            const unitPrice = parseFloat(it.unit_price || 0);
            const qty = parseInt(it.quantity || 1, 10);
            const lineSub = it.subtotal ? parseFloat(it.subtotal) : (unitPrice * qty);
            
            const variantParts = [it.shape, it.finish, it.size_preset].filter(Boolean);
            const variantStr = variantParts.length > 0 ? variantParts.join(' · ') : 'Standard Sizing';

            itemsHtml += `
                <div class="modal-item-row">
                    <img class="modal-item-thumb" src="${img}" alt="${it.product_name || 'Product'}" onerror="this.src='https://images.unsplash.com/photo-1632345031435-8727f6897d53?w=80&auto=format&fit=crop&q=80'">
                    <div class="modal-item-info">
                        <div class="modal-item-name">${it.product_name || 'Press-On Set'}</div>
                        <div class="modal-item-variant">
                            ${variantStr} &middot; <span class="modal-item-qty">Qty: ${qty}</span> &times; $${unitPrice.toFixed(2)}
                        </div>
                    </div>
                    <div class="modal-item-price">$${lineSub.toFixed(2)}</div>
                </div>
            `;
        });
        itemsList.innerHTML = itemsHtml;
    } else {
        itemsList.innerHTML = '<div style="padding:16px 0; color:var(--muted); font-size:12px; text-align:center;">No items recorded for this order.</div>';
    }

    // Financial summary
    const subtotal = parseFloat(ord.subtotal || 0);
    const shipping = parseFloat(ord.shipping_fee || 0);
    const tax = parseFloat(ord.tax || 0);
    const total = parseFloat(ord.total_amount || 0);

    document.getElementById('modalSubtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('modalShippingFee').textContent = shipping > 0 ? '$' + shipping.toFixed(2) : 'Free Shipping';
    document.getElementById('modalTax').textContent = '$' + tax.toFixed(2);
    document.getElementById('modalTotalPaid').textContent = '$' + total.toFixed(2);

    document.getElementById('modalPaymentMethod').textContent = ord.payment_method || 'Card';
    const payStatusElem = document.getElementById('modalPaymentStatus');
    if (ord.payment_status) {
        payStatusElem.textContent = '· ' + ord.payment_status.charAt(0).toUpperCase() + ord.payment_status.slice(1);
        payStatusElem.style.display = 'inline';
    } else {
        payStatusElem.style.display = 'none';
    }

    // Form inputs
    document.getElementById('modalFormOrderId').value = oid;
    const statusSelect = document.getElementById('modalStatusSelect');
    for (let opt of statusSelect.options) {
        opt.selected = (opt.value.toLowerCase() === statusVal.toLowerCase());
    }

    document.getElementById('modalTrackingInput').value = ord.tracking_number || '';
    document.getElementById('modalNotesTextarea').value = ord.admin_notes || '';

    // Quick action mailto
    const mailLink = document.getElementById('modalEmailClientLink');
    if (ord.customer_email) {
        mailLink.href = 'mailto:' + encodeURIComponent(ord.customer_email) + '?subject=' + encodeURIComponent('Update regarding your Monet Nails Order ' + ordNumberStr);
        mailLink.style.pointerEvents = 'auto';
        mailLink.style.opacity = '1';
    } else {
        mailLink.href = '#';
        mailLink.style.pointerEvents = 'none';
        mailLink.style.opacity = '0.5';
    }

    // Open modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';

    // Highlight row in table
    document.querySelectorAll('.orders-table tr').forEach(r => r.classList.remove('row-selected'));
    const targetRow = document.querySelector(`.orders-table tr[data-order-id="${oid}"]`);
    if (targetRow) targetRow.classList.add('row-selected');
}

function closeOrderModal() {
    const modal = document.getElementById('orderDetailModal');
    if (!modal) return;
    modal.classList.remove('active');
    document.body.style.overflow = '';

    // Clean URL query param without refreshing
    const url = new URL(window.location.href);
    if (url.searchParams.has('order_id')) {
        url.searchParams.delete('order_id');
        window.history.replaceState({}, '', url.toString());
    }
}

function handleModalBackdropClick(event) {
    if (event.target === document.getElementById('orderDetailModal')) {
        closeOrderModal();
    }
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' || e.key === 'Esc') {
        const modal = document.getElementById('orderDetailModal');
        if (modal && modal.classList.contains('active')) {
            closeOrderModal();
        }
    }
});

/* ===== DELETE CONFIRM MODAL ===== */
function confirmDeleteOrder(orderId, customerName) {
    const label = document.getElementById('deleteOrderLabel');
    const input = document.getElementById('deleteOrderIdInput');
    const form  = document.getElementById('deleteOrderForm');
    if (!label || !input || !form) return;

    const padded = '#ORD-' + String(orderId).padStart(4, '0');
    label.textContent = padded + ' (' + customerName + ')';
    input.value = orderId;

    // Point form to current page with delete action param
    const url = new URL(window.location.href);
    url.searchParams.set('delete_order_id', orderId);
    form.action = url.toString();

    document.getElementById('deleteConfirmModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    const m = document.getElementById('deleteConfirmModal');
    if (m) m.classList.remove('active');
    document.body.style.overflow = '';
}

// Also close delete modal on Escape
document.addEventListener('keydown', function(e) {
    if ((e.key === 'Escape' || e.key === 'Esc') && document.getElementById('deleteConfirmModal')?.classList.contains('active')) {
        closeDeleteModal();
    }
});
</script>

</body>
</html>
