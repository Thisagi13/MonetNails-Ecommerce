<?php
/**
 * views/admin/products.php - Admin Product Catalog & Inventory Management
 * Monet Nails MVC Application
 */

function aplink(string $base, array $extra = []): string {
    $q = array_merge([
        'page'        => 'admin_products',
        'search'      => $_GET['search']      ?? '',
        'category_id' => $_GET['category_id'] ?? '',
        'stock'       => $_GET['stock']        ?? 'all',
        'status'      => $_GET['status']       ?? 'all',
        'per_page'    => $_GET['per_page']     ?? 10,
        'pg'          => $_GET['pg']           ?? 1,
    ], $extra);
    return $base . '/index.php?' . http_build_query(array_filter($q, fn($v) => $v !== ''));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products &amp; Inventory - Monet Atelier Admin</title>
    <meta name="description" content="Admin product catalog management for Monet Nails press-on sets.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/admin_products.css">
</head>
<body class="admin-body">
<div class="page">
    <!-- SIDEBAR -->
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
            <a href="<?php echo $base_url; ?>/index.php?page=admin_products" class="nav-item active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8 L12 3 L3 8 V16 L12 21 L21 16 Z"/><line x1="3" y1="8" x2="12" y2="13"/><line x1="21" y1="8" x2="12" y2="13"/><line x1="12" y1="13" x2="12" y2="21"/></svg>
                <span>Products &amp; Stock</span>
            </a>
            <a href="<?php echo $base_url; ?>/index.php?page=admin_appointments" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>Appointments</span>
            </a>
            <a href="<?php echo $base_url; ?>/index.php?page=admin_services" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
                <span>Services</span>
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
                <span>Curation Tip</span>
            </div>
            <p class="tip-text">Keep safety stock at 5 units or above for featured sets to avoid customer checkout lockouts during promotions.</p>
            <div class="tip-foot">
                <span>Inventory Guard</span>
                <span class="tip-badge">Active</span>
            </div>
        </div>
    </aside>

    <!-- MAIN WORKSPACE -->
    <main class="workspace">
        <!-- Topbar -->
        <header class="ws-header">
            <div>
                <h1>Product Catalog &amp; Atelier Suite</h1>
                <p>Unified press-on collection curation, real-time inventory surveillance, and SKU lifecycle management.</p>
            </div>
            <div class="ws-actions">
                <button class="btn-outline" onclick="window.print()" id="btn-export-lookbook">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Print Catalog
                </button>
                <button class="btn-primary" id="btn-add-new" onclick="openAddModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add New Set
                </button>
                <div class="profile">
                    <div class="avatar"><?php echo htmlspecialchars($adminInitials); ?></div>
                    <div>
                        <div class="profile-name"><?php echo htmlspecialchars($adminName); ?></div>
                        <div class="profile-role">Lead Administrator</div>
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

        <!-- Stat Row -->
        <section class="stat-row">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    <span class="stat-chip">Catalog</span>
                </div>
                <div>
                    <div class="stat-label">Active Master Sets</div>
                    <div class="stat-value"><?php echo $total_active; ?></div>
                    <div class="stat-note stat-note-green">Live in storefront</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span class="stat-chip stat-chip-orange">&le; 5 Left</span>
                </div>
                <div>
                    <div class="stat-label">Low Stock Alert</div>
                    <div class="stat-value stat-value-orange"><?php echo $low_stock; ?></div>
                    <div class="stat-note stat-note-orange">Replenish needed</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                    <span class="stat-chip stat-chip-red">Depleted</span>
                </div>
                <div>
                    <div class="stat-label">Out of Stock</div>
                    <div class="stat-value stat-value-red"><?php echo $out_of_stock; ?></div>
                    <div class="stat-note stat-note-red">Immediate action</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    <span class="stat-chip">Valuation</span>
                </div>
                <div>
                    <div class="stat-label">Inventory Retail Value</div>
                    <div class="stat-value">Rs. <?php echo number_format($inventory_value, 2); ?></div>
                    <div class="stat-note">Active product stock</div>
                </div>
            </div>
        </section>

        <!-- Filter Bar -->
        <form method="GET" action="<?php echo $base_url; ?>/index.php" class="filter-bar" id="filter-form">
            <input type="hidden" name="page" value="admin_products">
            <div class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" id="search-input" placeholder="Search product name, SKU (#MN-001)..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="filter-group">
                <select name="category_id" class="filter-select" id="filter-category" onchange="this.form.submit()">
                    <option value="">Category: All Sets</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['category_id']; ?>" <?php if ($categoryId == $cat['category_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <select name="status" class="filter-select" id="filter-status" onchange="this.form.submit()">
                    <option value="all" <?php if ($statusFilter==='all') echo 'selected'; ?>>Status: All</option>
                    <option value="active" <?php if ($statusFilter==='active') echo 'selected'; ?>>Active</option>
                    <option value="inactive" <?php if ($statusFilter==='inactive') echo 'selected'; ?>>Inactive</option>
                </select>

                <select name="stock" class="filter-select" id="filter-stock" onchange="this.form.submit()">
                    <option value="all" <?php if ($stockFilter==='all') echo 'selected'; ?>>Stock: All</option>
                    <option value="in"  <?php if ($stockFilter==='in')  echo 'selected'; ?>>In Stock (>5)</option>
                    <option value="low" <?php if ($stockFilter==='low') echo 'selected'; ?>>Low Stock (1-5)</option>
                    <option value="out" <?php if ($stockFilter==='out') echo 'selected'; ?>>Out of Stock (0)</option>
                </select>

                <button type="submit" class="filter-btn" id="btn-search">Search</button>
                <a href="<?php echo $base_url; ?>/index.php?page=admin_products" class="reset-link" id="btn-reset">Reset</a>
            </div>
        </form>

        <!-- Product Table -->
        <section class="table-wrap">
            <div class="table-title">
                <span>Artisan Sets &mdash; <?php echo $totalRows; ?> result<?php echo $totalRows!==1?'s':''; ?></span>
                <span class="table-title-muted">Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></span>
            </div>
            <table class="products-table">
                <thead>
                    <tr>
                        <th class="col-check"><input type="checkbox" id="chk-all"></th>
                        <th>Artisan Set / SKU</th>
                        <th>Shape &amp; Finish</th>
                        <th>Price</th>
                        <th>Live Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $p):
                    if (!$p['is_active']) {
                        $st = 'Inactive';
                        $sc = 'status-grey';
                        $kt = $p['stock_quantity'] . ' units';
                        $kc = 'stock-green';
                    } elseif ($p['stock_quantity'] == 0) {
                        $st = 'Out of Stock';
                        $sc = 'status-red';
                        $kt = '0 left (Depleted)';
                        $kc = 'stock-red';
                    } elseif ($p['stock_quantity'] <= 5) {
                        $st = 'Low Stock';
                        $sc = 'status-orange';
                        $kt = $p['stock_quantity'] . ' left (Low)';
                        $kc = 'stock-orange';
                    } else {
                        $st = 'Active';
                        $sc = 'status-green';
                        $kt = $p['stock_quantity'] . ' in stock';
                        $kc = 'stock-green';
                    }
                    $sku = !empty($p['sku']) ? $p['sku'] : '#MN-' . str_pad((string)$p['product_id'], 3, '0', STR_PAD_LEFT);
                    
                    // Image URL handling
                    $imgSrc = '';
                    if (!empty($p['image_url'])) {
                        if (str_starts_with($p['image_url'], 'http://') || str_starts_with($p['image_url'], 'https://') || str_starts_with($p['image_url'], '/')) {
                            $imgSrc = $p['image_url'];
                        } else {
                            $imgSrc = $base_url . '/' . ltrim($p['image_url'], '/');
                        }
                    }
                ?>
                <tr data-product-id="<?php echo $p['product_id']; ?>">
                    <td class="col-check"><input type="checkbox" name="bulk[]" value="<?php echo $p['product_id']; ?>"></td>
                    <td class="col-product">
                        <div class="product-cell">
                            <?php if (!empty($imgSrc)): ?>
                            <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($p['product_name']); ?>" class="product-thumb" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div class="product-thumb-placeholder" style="display:none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                            <?php else: ?>
                            <div class="product-thumb-placeholder">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                            <?php endif; ?>
                            <div>
                                <div class="product-name"><?php echo htmlspecialchars($p['product_name']); ?></div>
                                <div class="product-sku"><?php echo htmlspecialchars($sku); ?></div>
                                <div class="product-cat"><?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorised'); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="col-shape">
                        <div class="shape-main"><?php echo htmlspecialchars($p['shape'] ?: 'Custom Fit'); ?></div>
                        <div class="finish-sub"><?php echo htmlspecialchars($p['finish'] ?: 'Glossy Gel'); ?></div>
                    </td>
                    <td class="col-price">Rs. <?php echo number_format((float)$p['price'], 2); ?></td>
                    <td>
                        <div class="stock-wrap">
                            <span class="stock-pill <?php echo $kc; ?>" id="stock-pill-<?php echo $p['product_id']; ?>"><?php echo $kt; ?></span>
                            <button class="plus-btn" title="Add 1 unit to stock" id="plus-btn-<?php echo $p['product_id']; ?>" onclick="adjustStock(<?php echo $p['product_id']; ?>, 1, this)">+</button>
                        </div>
                    </td>
                    <td><span class="status-badge <?php echo $sc; ?>"><span class="status-dot"></span><?php echo $st; ?></span></td>
                    <td class="col-actions">
                        <button class="action-btn" title="Edit product" id="edit-btn-<?php echo $p['product_id']; ?>"
                                onclick='openEditModal(<?php echo htmlspecialchars(json_encode($p), ENT_QUOTES, "UTF-8"); ?>)'>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button class="action-btn action-delete" title="Delete product" id="del-btn-<?php echo $p['product_id']; ?>"
                                onclick="openDeleteModal(<?php echo $p['product_id']; ?>, '<?php echo addslashes(htmlspecialchars($p['product_name'])); ?>')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                <tr><td colspan="7" class="empty-row">No products found matching your filters.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="table-foot">
                <div class="showing">
                    Showing <strong><?php echo min(($currentPage - 1) * $perPage + 1, max($totalRows, 1)); ?>&ndash;<?php echo min($currentPage * $perPage, $totalRows); ?></strong>
                    of <strong><?php echo $totalRows; ?></strong> artisan sets
                </div>
                <div class="pagination">
                    <button class="page-btn" id="btn-prev-page" <?php if ($currentPage <= 1) echo 'disabled'; ?>
                            onclick="location.href='<?php echo aplink($base_url, ['pg' => max(1, $currentPage - 1)]); ?>'">Previous</button>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <button class="page-btn <?php echo $i === $currentPage ? 'page-active' : ''; ?>" id="btn-page-<?php echo $i; ?>"
                            onclick="location.href='<?php echo aplink($base_url, ['pg' => $i]); ?>'">
                        <?php echo $i; ?>
                    </button>
                    <?php endfor; ?>
                    <button class="page-btn" id="btn-next-page" <?php if ($currentPage >= $totalPages) echo 'disabled'; ?>
                            onclick="location.href='<?php echo aplink($base_url, ['pg' => min($totalPages, $currentPage + 1)]); ?>'">Next</button>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- ================= ADD MODAL ================= -->
<div class="modal-overlay" id="modal-add">
    <div class="modal" role="dialog" aria-labelledby="modal-add-title">
        <div class="modal-header">
            <h2 id="modal-add-title">Add New Artisan Set</h2>
            <button class="modal-close" onclick="closeModal('modal-add')" aria-label="Close">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" action="<?php echo $base_url; ?>/index.php?page=admin_products" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_product">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group form-full">
                        <label for="add-product-name">Product Name *</label>
                        <input type="text" name="product_name" id="add-product-name" required placeholder="e.g. Signature Blossom Press-On Set">
                    </div>
                    <div class="form-group">
                        <label for="add-sku">SKU</label>
                        <input type="text" name="sku" id="add-sku" placeholder="Auto-generated if empty (#MN-XXX)">
                    </div>
                    <div class="form-group">
                        <label for="add-category">Category *</label>
                        <select name="category_id" id="add-category" required>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="add-price">Price (LKR) *</label>
                        <input type="number" step="0.01" name="price" id="add-price" required placeholder="1200.00">
                    </div>
                    <div class="form-group">
                        <label for="add-stock">Initial Stock Quantity *</label>
                        <input type="number" name="stock_quantity" id="add-stock" required min="0" value="10">
                    </div>
                    <div class="form-group">
                        <label for="add-shape">Shape Profile</label>
                        <input type="text" name="shape" id="add-shape" placeholder="e.g. Almond Medium, Coffin Long">
                    </div>
                    <div class="form-group">
                        <label for="add-finish">Finish / Art Type</label>
                        <input type="text" name="finish" id="add-finish" placeholder="e.g. High Gloss Gel, Velvet Matte">
                    </div>
                    <div class="form-group form-full">
                        <label for="add-desc">Description</label>
                        <textarea name="description" id="add-desc" placeholder="Artisan collection description, nail style notes, inspiration..."></textarea>
                    </div>
                    <div class="form-group form-full">
                        <label for="add-image">Upload Product Image</label>
                        <input type="file" name="image" id="add-image" accept="image/*" onchange="previewImg(this, 'add-preview')">
                        <div class="img-preview-wrap">
                            <img id="add-preview" class="img-preview" alt="Preview">
                        </div>
                    </div>
                    <div class="form-group form-full">
                        <label class="form-check">
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span>Publish to Storefront Catalog (Active)</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modal-add')">Cancel</button>
                <button type="submit" class="btn-primary">Save &amp; Publish</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= EDIT MODAL ================= -->
<div class="modal-overlay" id="modal-edit">
    <div class="modal" role="dialog" aria-labelledby="modal-edit-title">
        <div class="modal-header">
            <h2 id="modal-edit-title">Edit Artisan Set</h2>
            <button class="modal-close" onclick="closeModal('modal-edit')" aria-label="Close">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" action="<?php echo $base_url; ?>/index.php?page=admin_products" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit_product">
            <input type="hidden" name="product_id" id="edit-id">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group form-full">
                        <label for="edit-name">Product Name *</label>
                        <input type="text" name="product_name" id="edit-name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-sku">SKU</label>
                        <input type="text" name="sku" id="edit-sku">
                    </div>
                    <div class="form-group">
                        <label for="edit-category">Category</label>
                        <select name="category_id" id="edit-category">
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-price">Price (LKR) *</label>
                        <input type="number" step="0.01" name="price" id="edit-price" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-stock">Stock Quantity *</label>
                        <input type="number" name="stock_quantity" id="edit-stock" required min="0">
                    </div>
                    <div class="form-group">
                        <label for="edit-shape">Shape Profile</label>
                        <input type="text" name="shape" id="edit-shape">
                    </div>
                    <div class="form-group">
                        <label for="edit-finish">Finish / Art Type</label>
                        <input type="text" name="finish" id="edit-finish">
                    </div>
                    <div class="form-group form-full">
                        <label for="edit-desc">Description</label>
                        <textarea name="description" id="edit-desc"></textarea>
                    </div>
                    <div class="form-group form-full">
                        <label for="edit-image">Replace Product Image</label>
                        <input type="file" name="image" id="edit-image" accept="image/*" onchange="previewImg(this, 'edit-preview')">
                        <div class="img-preview-wrap">
                            <img id="edit-preview" class="img-preview" alt="Preview">
                        </div>
                    </div>
                    <div class="form-group form-full">
                        <label class="form-check">
                            <input type="checkbox" name="is_active" id="edit-active" value="1">
                            <span>Active in Storefront Catalog</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modal-edit')">Cancel</button>
                <button type="submit" class="btn-primary">Update Product</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= DELETE CONFIRM MODAL ================= -->
<div class="modal-overlay" id="modal-delete">
    <div class="modal" style="max-width:440px;" role="dialog" aria-labelledby="modal-del-title">
        <form method="POST" action="<?php echo $base_url; ?>/index.php?page=admin_products">
            <input type="hidden" name="action" value="delete_product">
            <input type="hidden" name="product_id" id="delete-id">
            <div class="modal-body delete-warning">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <h3 id="modal-del-title">Remove Artisan Set?</h3>
                <p>Are you sure you want to delete <strong id="delete-name"></strong>? If past orders reference this design, it will be safely deactivated.</p>
            </div>
            <div class="modal-footer" style="justify-content:center;">
                <button type="button" class="btn-cancel" onclick="closeModal('modal-delete')">Keep Set</button>
                <button type="submit" class="btn-danger">Confirm Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
const BASE_URL = '<?php echo $base_url; ?>';

function openAddModal() {
    document.getElementById('modal-add').classList.add('open');
}

function openEditModal(prod) {
    document.getElementById('edit-id').value       = prod.product_id;
    document.getElementById('edit-name').value     = prod.product_name || '';
    document.getElementById('edit-sku').value      = prod.sku || '';
    document.getElementById('edit-price').value    = prod.price || 0;
    document.getElementById('edit-stock').value    = prod.stock_quantity || 0;
    document.getElementById('edit-shape').value    = prod.shape || '';
    document.getElementById('edit-finish').value   = prod.finish || '';
    document.getElementById('edit-desc').value     = prod.description || '';
    document.getElementById('edit-active').checked = parseInt(prod.is_active) === 1;

    const catSelect = document.getElementById('edit-category');
    if (catSelect && prod.category_id) {
        catSelect.value = prod.category_id;
    }

    const preview = document.getElementById('edit-preview');
    if (prod.image_url) {
        let src = prod.image_url;
        if (!src.startsWith('http') && !src.startsWith('/')) {
            src = BASE_URL + '/' + src;
        }
        preview.src = src;
        preview.classList.add('visible');
    } else {
        preview.src = '';
        preview.classList.remove('visible');
    }

    document.getElementById('modal-edit').classList.add('open');
}

function openDeleteModal(id, name) {
    document.getElementById('delete-id').value = id;
    document.getElementById('delete-name').textContent = name;
    document.getElementById('modal-delete').classList.add('open');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}

// Close modal on click backdrop
document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', e => {
        if (e.target === el) el.classList.remove('open');
    });
});

// Image file preview
function previewImg(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.add('visible');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Real-time stock +1 increment via AJAX
async function adjustStock(productId, delta, btn) {
    btn.classList.add('loading');
    const form = new FormData();
    form.append('action', 'adjust_stock');
    form.append('product_id', productId);
    form.append('delta', delta);

    try {
        const res = await fetch(BASE_URL + '/index.php?page=admin_products', {
            method: 'POST',
            body: form,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();
        if (json.ok) {
            const pill = document.getElementById('stock-pill-' + productId);
            if (pill) {
                const qty = json.stock;
                pill.className = 'stock-pill ' + (qty === 0 ? 'stock-red' : qty <= 5 ? 'stock-orange' : 'stock-green');
                pill.textContent = qty === 0 ? '0 left (Depleted)' : qty <= 5 ? `${qty} left (Low)` : `${qty} in stock`;
            }
        }
    } catch (e) {
        console.error('Stock adjustment error:', e);
    } finally {
        btn.classList.remove('loading');
    }
}

// Select all checkboxes
const chkAll = document.getElementById('chk-all');
if (chkAll) {
    chkAll.addEventListener('change', function() {
        document.querySelectorAll('input[name="bulk[]"]').forEach(cb => cb.checked = chkAll.checked);
    });
}

// Auto fade flash message
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
