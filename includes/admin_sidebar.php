<?php
/**
 * includes/admin_sidebar.php — Shared side navigation for all admin pages.
 * Monet Nails MVC
 *
 * Markup & styling follow the admin dashboard sidebar (the base design).
 * Styles: assets/css/admin_sidebar.css — include it AFTER the page's own
 * stylesheet so the shared sidebar rules win the cascade.
 *
 * Expected variables:
 *   $activePage (string) — page key to highlight: admin | admin_orders |
 *       admin_products | admin_appointments | admin_services | admin_reports
 *   $base_url   (string) — app base URL (falls back to the standard path)
 *
 * Optional variables:
 *   $sidebarTip (array)  — custom tip card content:
 *       ['label' => .., 'text' => .., 'guard' => .., 'status' => ..]
 *       Omitted keys fall back to the dashboard's curation tip.
 */

$admin_sidebar_base   = $base_url ?? '/MonetNails-Ecommerce2/MonetNails-Ecommerce';
$admin_sidebar_active = $activePage ?? 'admin';

$admin_sidebar_items = [
    'admin' => [
        'label' => 'Dashboard',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>',
    ],
    'admin_orders' => [
        'label' => 'Orders &amp; Queue',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 L3 6 L3 20 a2 2 0 0 0 2 2 h14 a2 2 0 0 0 2 -2 V6 L18 2 Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10 a4 4 0 0 1 -8 0"/></svg>',
    ],
    'admin_products' => [
        'label' => 'Products &amp; Stock',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8 L12 3 L3 8 V16 L12 21 L21 16 Z"/><line x1="3" y1="8" x2="12" y2="13"/><line x1="21" y1="8" x2="12" y2="13"/><line x1="12" y1="13" x2="12" y2="21"/></svg>',
    ],
    'admin_appointments' => [
        'label' => 'Appointments',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
    ],
    'admin_services' => [
        'label' => 'Services',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
    ],
    'admin_reports' => [
        'label' => 'Sales &amp; Reports',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',
    ],
];

$admin_sidebar_tip = array_merge([
    'label'  => 'CURATION TIP',
    'text'   => 'Prioritize handcrafted 3D charms restock 48 hours before weekends for optimal salon conversion.',
    'guard'  => 'Restock alert guard',
    'status' => 'Active',
], $sidebarTip ?? []);
?>

<!-- ============== SIDEBAR (shared: includes/admin_sidebar.php) ============== -->
<aside class="sidebar">
    <div>
        <a href="<?php echo $admin_sidebar_base; ?>/index.php?page=admin" class="brand-row" style="text-decoration:none;">
            <span class="brand-dot"></span>
            <span class="brand-name">Monet Atelier</span>
        </a>
        <div class="brand-sub">NAIL BAR &amp; PRESS-ON STUDIO</div>
    </div>

    <nav class="nav">
        <?php foreach ($admin_sidebar_items as $admin_sidebar_key => $admin_sidebar_item): ?>
            <a href="<?php echo $admin_sidebar_base; ?>/index.php?page=<?php echo $admin_sidebar_key; ?>"
               class="nav-item<?php echo $admin_sidebar_key === $admin_sidebar_active ? ' active' : ''; ?>">
                <?php echo $admin_sidebar_item['icon']; ?>
                <span><?php echo $admin_sidebar_item['label']; ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="tip-card">
        <div class="tip-head">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            <span><?php echo htmlspecialchars($admin_sidebar_tip['label']); ?></span>
        </div>
        <p class="tip-text"><?php echo htmlspecialchars($admin_sidebar_tip['text']); ?></p>
        <div class="tip-foot">
            <span><?php echo htmlspecialchars($admin_sidebar_tip['guard']); ?></span>
            <span><?php echo htmlspecialchars($admin_sidebar_tip['status']); ?></span>
        </div>
    </div>
</aside>
