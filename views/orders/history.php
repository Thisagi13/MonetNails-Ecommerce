<?php
/**
 * views/orders/history.php — Customer Order History View
 * Monet Nails MVC Application
 *
 * Displays:
 *   - Customer profile badge
 *   - Status filter tabs (All / Pending / Processing / Shipped / Delivered / Cancelled)
 *   - Order cards with line items, delivery address, and payment summary
 *   - Order Inquiry Assistance box
 */

$base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';

if (!function_exists('h')) {
    function h($text): string {
        return htmlspecialchars((string)($text ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
if (!function_exists('ohMoney')) {
    function ohMoney($n): string {
        return 'Rs. ' . number_format((float)$n, 0);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History &amp; Custom Sets — Monet Nails Atelier</title>
    <meta name="description" content="View your handcrafted press-on orders, bespoke sizing profiles, and real-time dispatch tracking at Monet Nails.">

    <!-- Fonts: Bodoni Moda + Manrope -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..700&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Unified Header CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/header.css">

    <!-- Order History CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/order_history.css">

    <!-- Critical nav / profile-popup overrides (ensure profile icon is always clickable) -->
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
    $activeNav = 'order_history';
    require __DIR__ . '/../../includes/header.php';
    ?>

    <!-- ============================================================
         ORDER HISTORY MAIN CONTENT
         ============================================================ -->
    <main class="oh-page">
        <div class="oh-container">

            <!-- Breadcrumb -->
            <nav class="oh-breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo $base_url; ?>/">Home</a>
                <span class="sep">/</span>
                <a href="<?php echo $base_url; ?>/index.php?page=account_settings">Account</a>
                <span class="sep">/</span>
                <span class="current">Order History &amp; Custom Sets</span>
            </nav>

            <!-- Profile Bar -->
            <div class="oh-profile-bar">
                <div class="oh-avatar">
                    <?php if (!empty($customerAvatarUrl)): ?>
                        <img src="<?php echo h($customerAvatarUrl); ?>" alt="Profile photo">
                    <?php else: ?>
                        <span class="oh-avatar-initials"><?php echo h($initials); ?></span>
                    <?php endif; ?>
                </div>
                <div class="oh-profile-info">
                    <span class="oh-profile-name"><?php echo h($customerFullName); ?></span>
                    <span class="oh-profile-email"><?php echo h($customer['email'] ?? ''); ?></span>
                </div>
            </div>

            <!-- Page Title -->
            <header class="oh-page-title">
                <h1>Order History &amp; Custom Sets</h1>
                <p>View your handcrafted press-on orders, bespoke sizing profiles, and real-time dispatch tracking.</p>
            </header>

            <!-- Status Filter Tabs -->
            <nav class="oh-filter-tabs" aria-label="Order status filter">
                <?php foreach ($statusTabs as $tab): ?>
                    <a href="<?php echo $base_url; ?>/index.php?page=order_history&status=<?php echo h($tab['key']); ?>"
                       class="oh-filter-tab <?php echo $statusFilter === $tab['key'] ? 'active' : ''; ?>"
                       id="filter-<?php echo h($tab['key']); ?>">
                        <?php echo h($tab['label']); ?>
                        <span class="tab-count"><?php echo (int)$tab['count']; ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <!-- Order List -->
            <div class="oh-order-list" id="orderList">

                <?php if (empty($formattedOrders)): ?>
                    <!-- Empty State -->
                    <div class="oh-empty-state">
                        <div class="oh-empty-icon" aria-hidden="true">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                        </div>
                        <h3>No Orders Found</h3>
                        <p>
                            <?php if ($statusFilter !== 'all'): ?>
                                No orders with status "<?php echo h(ucfirst($statusFilter)); ?>" yet.
                                <a href="<?php echo $base_url; ?>/index.php?page=order_history" style="color:var(--mn-pink);text-decoration:underline;">View all orders</a>
                            <?php else: ?>
                                You haven't placed any orders yet. Explore our curated press-on collections and treat yourself.
                            <?php endif; ?>
                        </p>
                        <a href="<?php echo $base_url; ?>/index.php?page=product" class="oh-btn-shop">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/></svg>
                            Browse the Boutique
                        </a>
                    </div>

                <?php else: ?>
                    <?php foreach ($formattedOrders as $order): ?>
                        <article class="oh-order-card" id="order-<?php echo (int)$order['order_id']; ?>">

                            <!-- Card Header: Reference + Status + Date | Total -->
                            <header class="oh-card-header">
                                <div class="oh-card-header-left">
                                    <div class="oh-ref-row">
                                        <span class="oh-ref-no"><?php echo h($order['reference_no']); ?></span>
                                        <span class="oh-status-pill <?php echo h($order['status_style']); ?>">
                                            <?php echo h($order['status_label']); ?>
                                        </span>
                                    </div>
                                    <span class="oh-placed-on"><?php echo h($order['placed_on']); ?></span>
                                </div>
                                <div class="oh-card-header-right">
                                    <span class="oh-total-label">Total Settled</span>
                                    <span class="oh-total-amount"><?php echo ohMoney($order['total_amount']); ?></span>
                                </div>
                            </header>

                            <!-- Line Items -->
                            <div class="oh-items-list">
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="oh-item-row">
                                        <div class="oh-item-left">
                                            <!-- Product Thumbnail -->
                                            <div class="oh-item-thumb">
                                                <?php if (!empty($item['image_url'])): ?>
                                                    <img src="<?php echo h($item['image_url']); ?>"
                                                         alt="<?php echo h($item['product_name']); ?>"
                                                         loading="lazy">
                                                <?php else: ?>
                                                    <div class="oh-thumb-placeholder" aria-hidden="true">
                                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                                                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                                                            <circle cx="8.5" cy="8.5" r="1.5"/>
                                                            <polyline points="21 15 16 10 5 21"/>
                                                        </svg>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <!-- Product Info -->
                                            <div class="oh-item-details">
                                                <span class="oh-item-name"><?php echo h($item['product_name']); ?></span>
                                                <span class="oh-item-variant"><?php echo h($item['variant_desc']); ?></span>
                                            </div>
                                        </div>
                                        <div class="oh-item-right">
                                            <span class="oh-item-price"><?php echo ohMoney($item['unit_price']); ?></span>
                                            <span class="oh-item-qty">Qty: <?php echo (int)$item['quantity']; ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Card Footer: Delivery + Payment -->
                            <footer class="oh-card-footer">
                                <div class="oh-footer-col">
                                    <span class="oh-footer-label">Delivery Address</span>
                                    <span class="oh-footer-text"><?php echo h($order['shipping_address'] ?: '—'); ?></span>
                                </div>
                                <div class="oh-footer-col">
                                    <span class="oh-footer-label">Payment Summary</span>
                                    <div class="oh-payment-line">
                                        <!-- Badge-check icon -->
                                        <svg width="14" height="14" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                                            <path d="M4.43 12.25 3.33 10.38l-2.1-.46.21-2.16L0 6.13l1.43-1.64L1.22 2.33l2.1-.46L4.43 0l1.99.85L8.4 0l1.11 1.87 2.1.46-.21 2.16 1.43 1.64-1.43 1.63.21 2.16-2.1.47L8.4 12.25l-1.98-.85-1.99.85ZM4.93 10.76l1.49-.64 1.51.64.82-1.4 1.6-.38-.15-1.63 1.08-1.23-1.08-1.25.15-1.63-1.6-.35-.85-1.4-1.49.64-1.51-.64-.82 1.4-1.6.35.14 1.63-1.08 1.25L3.4 7.35l1.6.38.85 1.4-.93-.07ZM5.8 8.2 9.1 4.9l-.82-.85L5.8 6.53 4.55 5.31l-.82.81L5.8 8.2Z" fill="#A9275E"/>
                                        </svg>
                                        <span class="oh-footer-text"><?php echo h($order['payment_summary']); ?></span>
                                    </div>
                                </div>
                            </footer>

                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div><!-- /.oh-order-list -->

            <!-- Order Inquiry Assistance -->
            <div class="oh-inquiry-box">
                <div class="oh-inquiry-icon" aria-hidden="true">
                    <svg width="24" height="22" viewBox="0 0 17 15" fill="none">
                        <path d="M7.5 15v-1.67h6.67v-.83V7.42c0-1.63-.57-3-1.7-4.14C11.34 2.15 9.96 1.58 8.33 1.58S5.33 2.15 4.2 3.28C3.07 4.41 2.5 5.79 2.5 7.42V12.5H1.67c-.46 0-.85-.16-1.18-.49C.16 11.68 0 11.29 0 10.83V9.17c0-.29.07-.56.22-.82.15-.26.35-.46.61-.62L1.06 6.63C1.17 5.68 1.45 4.81 1.88 4c.44-.81.99-1.51 1.65-2.11C4.19 1.3 4.95.83 5.81.5 6.66.17 7.56 0 8.5 0s1.84.17 2.69.5c.85.33 1.61.79 2.28 1.39.66.59 1.21 1.29 1.64 2.1.43.8.71 1.68.82 2.62l.06 1.08c.27.13.47.32.62.57.15.25.22.51.22.79v1.92c0 .28-.07.54-.22.79-.15.25-.35.44-.62.57v1.02c0 .46-.16.85-.49 1.18-.33.33-.72.49-1.18.49H7.5Z" fill="#3E001C"/>
                    </svg>
                </div>
                <div class="oh-inquiry-body">
                    <h3>Order Inquiry Assistance</h3>
                    <p>All handcrafted orders are cataloged in read-only mode for verified security. For modifications, custom requests, or immediate courier dispatch assistance, contact us directly.</p>
                </div>
                <a href="tel:+94772105954" class="oh-inquiry-phone-link">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                        <path d="M16.95 18c-2.08 0-4.14-.45-6.18-1.36-2.03-.91-3.88-2.2-5.55-3.86C3.56 11.11 2.27 9.26 1.36 7.22.45 5.19 0 3.13 0 1.05 0 .75.1.5.3.3.5.1.75 0 1.05 0H5.1c.23 0 .44.08.62.24.18.16.29.35.33.58L6.7 4.3c.03.27.03.49-.02.66-.05.18-.13.34-.27.48L3.97 7.9c.33.62.73 1.21 1.19 1.79.46.58.96 1.13 1.51 1.66.52.52 1.06 1 1.63 1.44.57.44 1.17.83 1.8 1.18l2.35-2.35c.15-.15.34-.26.59-.34.24-.07.48-.09.71-.05l3.45.7c.14.07.25.11.34.21.09.1.13.21.13.34v4.05c0 .18-.06.32-.18.45-.12.12-.27.18-.45.18Z" fill="#A9275E"/>
                    </svg>
                    <div class="oh-phone-info">
                        <span class="oh-phone-num">+94 77 210 5954</span>
                        <span class="oh-phone-sub">Salon Direct Line (10:00 – 19:30)</span>
                    </div>
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                        <path d="M7.1 5.25H0V4.08h7.1L3.84.82 4.67 0l4.66 4.67L4.67 9.33l-.83-.81L7.1 5.25Z" fill="#8A7177"/>
                    </svg>
                </a>
            </div>

        </div><!-- /.oh-container -->
    </main>

    <!-- ============================================================
         FOOTER
         ============================================================ -->
    <?php require __DIR__ . '/../../includes/footer.php'; ?>


</body>
</html>
