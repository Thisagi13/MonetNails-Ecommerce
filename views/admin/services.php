<?php
/**
 * views/admin/services.php — Admin Services Management Dashboard View
 * Monet Nails MVC Application
 */

if (!function_exists('adm_srv_price')) {
    function adm_srv_price($n): string {
        $val = (float)$n;
        return '$' . number_format($val, 2);
    }
}

if (!function_exists('adm_srv_icon')) {
    function adm_srv_icon(string $name): string {
        $map = [
            'home' => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M.6 6.6 1.93 5.27 6.6.6l4.67 4.67L12.6 6.6M6.6.6 1.93 5.27v6.66c0 .37.3.67.67.67h2M12.6 6.6l-1.33-1.33v6.66c0 .37-.3.67-.67.67h-2M4.6 12.6c.37 0 .67-.3.67-.67V9.27c0-.37.3-.67.67-.67h1.33c.37 0 .67.3.67.67v2.66c0 .37.3.67.67.67M4.6 12.6h4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'bag' => '<svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.6 5.93V3.27C8.6 1.79 7.4.6 5.93.6 4.46.6 3.27 1.79 3.27 3.27v2.66M1.27 4.6h9.33l.67 8H.6l.67-8" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'box' => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.33 6H2M11.33 6c.74 0 1.33.6 1.33 1.33v4c0 .74-.6 1.34-1.33 1.34H2c-.74 0-1.33-.6-1.33-1.34v-4C.67 6.6 1.27 6 2 6M11.33 6V4.67c0-.74-.6-1.34-1.33-1.34M2 6V4.67c0-.74.6-1.34 1.33-1.34M3.33 3.33V2C3.33 1.26 3.93.67 4.67.67h4.66C10.07.67 10.67 1.26 10.67 2v1.33" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'calendar' => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.93 3.27V.6M9.27 3.27V.6M3.27 5.93h6.66M1.93 12.6h9.33c.74 0 1.34-.6 1.34-1.33V3.27c0-.74-.6-1.34-1.34-1.34H1.93c-.73 0-1.33.6-1.33 1.34v8c0 .73.6 1.33 1.33 1.33Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'chart' => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.6 11.27v-4c0-.74-.6-1.34-1.33-1.34H1.93c-.73 0-1.33.6-1.33 1.34v4c0 .73.6 1.33 1.33 1.33h1.34c.73 0 1.33-.6 1.33-1.33Zm0 0V4.6c0-.74.6-1.33 1.33-1.33h1.34c.73 0 1.33.6 1.33 1.33v6.67M4.6 11.27c0 .73.6 1.33 1.33 1.33h1.34c.73 0 1.33-.6 1.33-1.33Zm4 0V1.93c0-.74.6-1.33 1.33-1.33h1.34c.73 0 1.33.6 1.33 1.33v9.34c0 .73-.6 1.33-1.33 1.33H9.93c-.73 0-1.33-.6-1.33-1.33Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'sparkle' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 0.5v1.5m0 0v1.5m0-1.5h1.5m-1.5 0h-1.5M7.27 1.11c.03-.17.12-.33.26-.44.13-.11.3-.17.47-.17s.34.06.47.17c.13.11.22.27.26.44l.79 4.17c.05.3.2.57.41.78.21.21.49.36.78.41l4.17.79c.17.03.33.12.44.26.11.13.17.3.17.47s-.06.34-.17.47c-.11.13-.27.22-.44.26l-4.17.79c-.3.05-.57.2-.78.41-.21.21-.36.49-.41.78l-.79 4.17c-.03.17-.12.33-.26.44-.13.11-.3.17-.47.17s-.34-.06-.47-.17c-.13-.11-.22-.27-.26-.44l-.79-4.17c-.05-.3-.2-.57-.41-.78-.21-.21-.49-.36-.78-.41l-4.17-.79c-.17-.03-.33-.12-.44-.26C.56 8.34.5 8.18.5 8s.06-.34.17-.47c.11-.13.27-.22.44-.26l4.17-.79c.3-.05.57-.2.78-.41.21-.21.36-.49.41-.78ZM3.5 14c0 .83-.67 1.5-1.5 1.5S.5 14.83.5 14s.67-1.5 1.5-1.5 1.5.67 1.5 1.5Z" stroke="#C86D8A" stroke-linecap="round"/></svg>',
            'info' => '<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.33 8.67V6.33M6.33 4H6.34M12.17 6.33c0 3.22-2.61 5.84-5.84 5.84S.5 9.55.5 6.33 3.11.5 6.33.5s5.84 2.61 5.84 5.83Z" stroke="#fff" stroke-opacity="0.9" stroke-linecap="round"/></svg>',
            'plus' => '<svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M.5 5.17h4.67m0 0h4.67m-4.67 0V.5m0 4.67v4.66" stroke="#fff" stroke-linecap="round"/></svg>',
            'search' => '<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.75 11.75 9.04 9.04M10.5 5.5c0 2.76-2.24 5-5 5s-5-2.24-5-5 2.24-5 5-5 5 2.24 5 5Z" stroke="#96868D" stroke-linecap="round"/></svg>',
            'thumb' => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="7" cy="7" r="6" fill="#fff" stroke="#DE8EA7" stroke-width="1.5"/><path d="M4.5 7l1.8 1.8L9.5 5.5" stroke="#C86D8A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>',
            'thumb-hidden' => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="7" cy="7" r="6" fill="#fff" stroke="#96868D" stroke-width="1.5"/><path d="M4 4l6 6" stroke="#96868D" stroke-width="1.5" stroke-linecap="round"/></svg>',
            'pencil' => '<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.54 2.13 9.71 4.29M7.55 2.13 8.73.95c.29-.29.67-.45 1.08-.45.4 0 .79.16 1.07.45.29.28.45.67.45 1.08 0 .4-.16.79-.45 1.07L8.71 4.29M7.55 2.13 1.5 8.18c-.13.13-.22.28-.27.46L.51 10.98c-.01.05-.02.1 0 .15.02.05.05.09.1.13.04.03.09.06.14.07.05.01.1 0 .15-.01l2.36-.72c.17-.05.33-.14.45-.27L9.71 4.29" stroke="#96868D" stroke-linecap="round"/></svg>',
            'trash' => '<svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.29 5.38v3.25M6.46 5.38v3.25M9.17 2.67v7.58c0 .29-.12.56-.31.77-.2.2-.47.32-.77.32H2.67c-.29 0-.56-.12-.77-.32-.2-.2-.32-.48-.32-.77V2.67M9.17 2.67H7.54M9.17 2.67H10.25M1.58 2.67H.5M1.58 2.67h1.63M3.21 2.67h4.33M3.21 2.67V1.58c0-.29.12-.56.32-.77.2-.2.48-.32.77-.32h2.17c.29 0 .56.12.77.32.2.2.32.48.32.77v1.09" stroke="#C86D8A" stroke-linecap="round"/></svg>',
            'info-grey' => '<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.33 8.67V6.33M6.33 4H6.34M12.17 6.33c0 3.22-2.61 5.84-5.84 5.84S.5 9.55.5 6.33 3.11.5 6.33.5s5.84 2.61 5.84 5.83Z" stroke="#96868D" stroke-linecap="round"/></svg>',
            'close' => '<svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.5.5 5.5 5.5m0 0L.5 10.5M5.5 5.5.5.5m5 5L10.5 10.5" stroke="currentColor" stroke-linecap="round"/></svg>',
        ];
        return $map[$name] ?? '';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monet Atelier — Services Management</title>
    <meta name="description" content="Manage nail bar and salon services catalog, client visibility, and pricing.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/admin_services.css?v=<?php echo time(); ?>">

    <style>
    /* Critical Modal Guarantee Styles */
    .modal-backdrop {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: rgba(48, 39, 43, 0.65) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
        z-index: 999999 !important;
        display: none;
        align-items: center !important;
        justify-content: center !important;
        padding: 20px !important;
        box-sizing: border-box !important;
    }
    .modal-backdrop.open,
    .modal-backdrop.active {
        display: flex !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
    }
    .modal-dialog {
        position: relative !important;
        z-index: 1000000 !important;
        background: #FFFFFF !important;
        border-radius: 14px !important;
        max-width: 560px !important;
        width: 100% !important;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3) !important;
        overflow: hidden !important;
    }
    </style>
</head>
<body class="admin-services-body">

<div class="dashboard">

    <!-- ====================================================================
         LEFT SIDEBAR
         ==================================================================== -->
    <aside class="sidebar">

        <!-- Brand logo block -->
        <div class="brand-block">
            <a href="<?php echo $base_url; ?>/index.php?page=admin" class="brand-row">
                <span class="brand-dot"></span>
                <span class="brand-name"><?php echo htmlspecialchars($brand['name']); ?></span>
            </a>
            <span class="brand-subtitle"><?php echo htmlspecialchars($brand['subtitle']); ?></span>
        </div>

        <!-- Navigation Menu -->
        <nav class="nav-menu">
            <?php foreach ($nav_items as $item): ?>
                <a href="<?php echo htmlspecialchars($item['url']); ?>" class="nav-item <?php echo !empty($item['active']) ? 'active' : ''; ?>">
                    <span class="nav-icon"><?php echo adm_srv_icon($item['icon']); ?></span>
                    <span><?php echo htmlspecialchars($item['label']); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Curation Tip Card -->
        <div class="tip-card">
            <div class="tip-header">
                <?php echo adm_srv_icon('info'); ?>
                <span class="tip-label"><?php echo htmlspecialchars($curation_tip['label']); ?></span>
            </div>
            <p class="tip-text"><?php echo htmlspecialchars($curation_tip['text']); ?></p>
            <div class="tip-footer">
                <span class="guard-label"><?php echo htmlspecialchars($curation_tip['guard_label']); ?></span>
                <span class="guard-status"><?php echo htmlspecialchars($curation_tip['guard_status']); ?></span>
            </div>
        </div>

    </aside>

    <!-- ====================================================================
         RIGHT WORKSPACE
         ==================================================================== -->
    <main class="workspace">

        <!-- Workspace Header -->
        <header class="workspace-header">
            <div class="page-intro">
                <h1 class="page-title"><?php echo htmlspecialchars($page['title']); ?></h1>
                <p class="page-subtitle"><?php echo htmlspecialchars($page['subtitle']); ?></p>
            </div>
            <div class="admin-profile">
                <div class="admin-avatar">
                    <span class="initials"><?php echo htmlspecialchars($admin['initials']); ?></span>
                </div>
                <div class="admin-details">
                    <span class="admin-name"><?php echo htmlspecialchars($admin['f_name'] . ' ' . $admin['l_name']); ?></span>
                    <span class="admin-role"><?php echo htmlspecialchars($admin['role']); ?></span>
                </div>
            </div>
        </header>

        <!-- Service Catalog -->
        <div class="catalog">

            <!-- Toolbar -->
            <div class="catalog-toolbar">
                <div class="catalog-title-row">
                    <h2 class="catalog-title">All services</h2>
                    <span class="count-badge" id="totalCountBadge"><?php echo (int)$totalServices; ?></span>
                </div>
                <button type="button" class="add-btn" id="openAddModalBtn" onclick="openAddModal()">
                    <?php echo adm_srv_icon('plus'); ?>
                    <span>Add Service</span>
                </button>
            </div>

            <!-- Table Card -->
            <div class="table-card">

                <!-- Search Bar -->
                <div class="search-bar">
                    <div class="search-input-wrap">
                        <?php echo adm_srv_icon('search'); ?>
                        <input type="text" placeholder="Search services..." id="searchInput" value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>

                <!-- Column Headings -->
                <div class="table-head">
                    <span class="col col-service">SERVICE</span>
                    <span class="col col-duration">DURATION</span>
                    <span class="col col-price">PRICE</span>
                    <span class="col col-page">CLIENT PAGE</span>
                    <span class="col col-actions">ACTIONS</span>
                </div>

                <!-- Service Rows Container -->
                <div id="serviceRowsContainer">
                    <?php foreach ($services as $svc):
                        $isVisible = !empty($svc['is_visible']) || (!empty($svc['is_active']) && $svc['is_active'] == 1);
                    ?>
                        <div class="service-row"
                             id="service-row-<?php echo $svc['service_id']; ?>"
                             data-id="<?php echo $svc['service_id']; ?>"
                             data-name="<?php echo htmlspecialchars(strtolower($svc['name'] ?? $svc['service_name'] ?? '')); ?>"
                             data-desc="<?php echo htmlspecialchars(strtolower($svc['description'] ?? '')); ?>"
                             data-category="<?php echo htmlspecialchars($svc['category'] ?? 'Manicure'); ?>"
                             data-duration="<?php echo (int)($svc['duration'] ?? 30); ?>"
                             data-price="<?php echo htmlspecialchars($svc['price'] ?? '0.00'); ?>"
                             data-visible="<?php echo $isVisible ? '1' : '0'; ?>">

                            <div class="col-service">
                                <div class="name"><?php echo htmlspecialchars($svc['name'] ?? $svc['service_name'] ?? ''); ?></div>
                                <div class="desc"><?php echo htmlspecialchars($svc['description'] ?? ''); ?></div>
                            </div>

                            <div class="col-duration"><?php echo (int)($svc['duration'] ?? 30); ?> min</div>

                            <div class="col-price"><?php echo htmlspecialchars(adm_srv_price($svc['price'] ?? 0)); ?></div>

                            <div class="col-page">
                                <div class="visibility <?php echo $isVisible ? '' : 'hidden'; ?>"
                                     title="Click to toggle client visibility"
                                     onclick="toggleVisibility(<?php echo $svc['service_id']; ?>, this)">
                                    <span class="vis-icon"><?php echo $isVisible ? adm_srv_icon('thumb') : adm_srv_icon('thumb-hidden'); ?></span>
                                    <span class="label"><?php echo $isVisible ? 'Visible' : 'Hidden'; ?></span>
                                </div>
                            </div>

                            <div class="col-actions">
                                <button type="button" class="action edit" onclick="openEditModal(<?php echo $svc['service_id']; ?>)">
                                    <?php echo adm_srv_icon('pencil'); ?>
                                    <span>Edit</span>
                                </button>
                                <button type="button" class="action remove" onclick="openDeleteModal(<?php echo $svc['service_id']; ?>, '<?php echo addslashes(htmlspecialchars($svc['name'] ?? '')); ?>')">
                                    <?php echo adm_srv_icon('trash'); ?>
                                    <span>Remove</span>
                                </button>
                            </div>

                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($services)): ?>
                        <div class="empty-state" id="emptyRow">
                            <p>No services found matching your criteria.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- List Footer -->
                <div class="list-footer" id="listFooter">
                    Showing <span id="shownCount"><?php echo (int)$totalServices; ?></span> of <span id="maxCount"><?php echo (int)$totalServices; ?></span> services
                </div>
            </div>

            <!-- Visibility Help Text -->
            <div class="visibility-help">
                <?php echo adm_srv_icon('info-grey'); ?>
                <p>Only visible services appear on your client's services page.</p>
            </div>

        </div>
    </main>

</div>

<!-- ====================================================================
     ADD SERVICE POPUP MODAL (matches add_service_popup.php)
     ==================================================================== -->
<div class="modal-backdrop" id="addServiceModal" role="dialog" aria-modal="true" aria-labelledby="addModalTitle">
    <div class="modal-dialog">
        <header class="modal-header">
            <div class="intro">
                <span class="title" id="addModalTitle">Add Service</span>
                <span class="subtitle">Create a new service for your client's services page.</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeModal('addServiceModal')" aria-label="Close">
                <?php echo adm_srv_icon('close'); ?>
            </button>
        </header>

        <form class="modal-body" id="addServiceForm" method="POST" action="<?php echo $base_url; ?>/index.php?page=admin_services">
            <input type="hidden" name="action" value="add_service">

            <!-- Service Name -->
            <div class="field">
                <label for="add_service_name">Service name *</label>
                <input type="text" id="add_service_name" name="service_name" placeholder="e.g. Signature Manicure" required>
            </div>

            <!-- Description -->
            <div class="field">
                <label for="add_description">Description</label>
                <textarea id="add_description" name="description" rows="3" placeholder="Write a short description of this service..."></textarea>
            </div>

            <!-- Duration + Price Row -->
            <div class="field-row">
                <div class="field">
                    <label for="add_duration">Duration</label>
                    <div class="duration-wrap">
                        <input type="number" id="add_duration" name="duration" min="1" step="1" value="30">
                        <span class="suffix">minutes</span>
                    </div>
                </div>
                <div class="field">
                    <label for="add_price">Price</label>
                    <div class="price-wrap">
                        <span class="prefix">$</span>
                        <input type="number" id="add_price" name="price" min="0" step="0.01" value="0.00">
                    </div>
                </div>
            </div>

            <!-- Category -->
            <div class="field">
                <label for="add_category">Category</label>
                <select id="add_category" name="category">
                    <option value="Manicure">Manicure</option>
                    <option value="Pedicure">Pedicure</option>
                    <option value="Specialty">Specialty</option>
                    <option value="Enhancements & Art">Enhancements &amp; Art</option>
                </select>
            </div>

            <!-- Visibility toggle row -->
            <div class="toggle-row">
                <div class="text">
                    <span class="label">Visible on client's services page</span>
                    <span class="desc">Clients can view and book this service when enabled.</span>
                </div>
                <label class="switch">
                    <input type="checkbox" name="is_visible" value="1" id="add_is_visible" checked>
                    <span class="slider"></span>
                </label>
            </div>
        </form>

        <footer class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeModal('addServiceModal')">Cancel</button>
            <button type="submit" class="btn-save" form="addServiceForm">Add Service</button>
        </footer>
    </div>
</div>

<!-- ====================================================================
     EDIT SERVICE POPUP MODAL (matches edit_service_popup.php)
     ==================================================================== -->
<div class="modal-backdrop" id="editServiceModal" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
    <div class="modal-dialog">
        <header class="modal-header">
            <div class="intro">
                <span class="title" id="editModalTitle">Edit Service</span>
                <span class="subtitle">Update the details shown on your services page.</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeModal('editServiceModal')" aria-label="Close">
                <?php echo adm_srv_icon('close'); ?>
            </button>
        </header>

        <form class="modal-body" id="editServiceForm" method="POST" action="<?php echo $base_url; ?>/index.php?page=admin_services">
            <input type="hidden" name="action" value="edit_service">
            <input type="hidden" id="edit_service_id" name="service_id" value="">

            <!-- Service Name -->
            <div class="field">
                <label for="edit_service_name">Service name *</label>
                <input type="text" id="edit_service_name" name="service_name" required>
            </div>

            <!-- Description -->
            <div class="field">
                <label for="edit_description">Description</label>
                <textarea id="edit_description" name="description" rows="3"></textarea>
            </div>

            <!-- Duration + Price Row -->
            <div class="field-row">
                <div class="field">
                    <label for="edit_duration">Duration</label>
                    <div class="duration-wrap">
                        <input type="number" id="edit_duration" name="duration" min="1" step="1">
                        <span class="suffix">minutes</span>
                    </div>
                </div>
                <div class="field">
                    <label for="edit_price">Price</label>
                    <div class="price-wrap">
                        <span class="prefix">$</span>
                        <input type="number" id="edit_price" name="price" min="0" step="0.01">
                    </div>
                </div>
            </div>

            <!-- Category -->
            <div class="field">
                <label for="edit_category">Category</label>
                <select id="edit_category" name="category">
                    <option value="Manicure">Manicure</option>
                    <option value="Pedicure">Pedicure</option>
                    <option value="Specialty">Specialty</option>
                    <option value="Enhancements & Art">Enhancements &amp; Art</option>
                </select>
            </div>

            <!-- Visibility toggle row -->
            <div class="toggle-row">
                <div class="text">
                    <span class="label">Visible on client's services page</span>
                    <span class="desc">Clients can view and book this service when enabled.</span>
                </div>
                <label class="switch">
                    <input type="checkbox" name="is_visible" value="1" id="edit_is_visible">
                    <span class="slider"></span>
                </label>
            </div>
        </form>

        <footer class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeModal('editServiceModal')">Cancel</button>
            <button type="submit" class="btn-save" form="editServiceForm">Save Changes</button>
        </footer>
    </div>
</div>

<!-- ====================================================================
     DELETE CONFIRMATION MODAL
     ==================================================================== -->
<div class="modal-backdrop" id="deleteServiceModal" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
    <div class="modal-dialog" style="max-width: 440px;">
        <header class="modal-header">
            <div class="intro">
                <span class="title" id="deleteModalTitle" style="font-size: 20px;">Remove Service</span>
                <span class="subtitle">Are you sure you want to remove this service?</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeModal('deleteServiceModal')" aria-label="Close">
                <?php echo adm_srv_icon('close'); ?>
            </button>
        </header>

        <form class="modal-body" id="deleteServiceForm" method="POST" action="<?php echo $base_url; ?>/index.php?page=admin_services">
            <input type="hidden" name="action" value="delete_service">
            <input type="hidden" id="delete_service_id" name="service_id" value="">
            <p style="font-size: 13.5px; line-height: 1.5; color: var(--dark);">
                You are about to remove <strong id="deleteServiceName">this service</strong> from your catalog.
            </p>
            <p style="font-size: 12px; color: var(--grey-2); line-height: 1.4;">
                If this service is referenced by past salon appointments, it will be automatically archived and hidden from client bookings to preserve appointment records.
            </p>
        </form>

        <footer class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeModal('deleteServiceModal')">Cancel</button>
            <button type="submit" class="btn-save" style="background:#BE536D; border-color:#BE536D;" form="deleteServiceForm">Remove</button>
        </footer>
    </div>
</div>

<!-- Flash Toast Notification -->
<div class="toast-notice" id="toastNotice"></div>

<!-- ====================================================================
     JAVASCRIPT LOGIC
     ==================================================================== -->
<script>
const BASE_URL = "<?php echo $base_url; ?>";
const thumbIcon = `<?php echo addslashes(adm_srv_icon('thumb')); ?>`;
const thumbHiddenIcon = `<?php echo addslashes(adm_srv_icon('thumb-hidden')); ?>`;

// Toast Notification
function showToast(msg, isSuccess = true) {
    const toast = document.getElementById('toastNotice');
    if (!toast) return;
    toast.textContent = msg;
    toast.className = 'toast-notice show ' + (isSuccess ? 'success' : 'error');
    setTimeout(() => {
        toast.className = 'toast-notice';
    }, 3800);
}

<?php if (!empty($flashSuccess)): ?>
    showToast(<?php echo json_encode($flashSuccess); ?>, true);
<?php endif; ?>
<?php if (!empty($flashError)): ?>
    showToast(<?php echo json_encode($flashError); ?>, false);
<?php endif; ?>

// Global Modal Handlers (attached to window for direct access)
function openModal(id) {
    var el = document.getElementById(id);
    if (!el) {
        console.error('Modal element not found by ID:', id);
        return;
    }
    el.classList.add('open');
    el.classList.add('active');
    el.style.display = 'flex';
    el.style.opacity = '1';
    el.style.visibility = 'visible';
    el.style.pointerEvents = 'auto';
    el.style.zIndex = '999999';
    document.body.style.overflow = 'hidden';
}
window.openModal = openModal;

function closeModal(id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('open');
    el.classList.remove('active');
    el.style.display = 'none';
    el.style.opacity = '0';
    el.style.visibility = 'hidden';
    el.style.pointerEvents = 'none';
    document.body.style.overflow = '';
}
window.closeModal = closeModal;

// Add Service Modal trigger
function openAddModal() {
    var form = document.getElementById('addServiceForm');
    if (form) form.reset();
    var vis = document.getElementById('add_is_visible');
    if (vis) vis.checked = true;
    openModal('addServiceModal');
}
window.openAddModal = openAddModal;

// Edit Service Modal trigger
function openEditModal(serviceId) {
    try {
        serviceId = parseInt(serviceId, 10);
        var row = document.getElementById('service-row-' + serviceId);
        var form = document.getElementById('editServiceForm');
        if (form) form.reset();

        if (row) {
            var nameEl = row.querySelector('.name');
            var descEl = row.querySelector('.desc');
            var name = nameEl ? nameEl.textContent.trim() : '';
            var desc = descEl ? descEl.textContent.trim() : '';
            var duration = row.getAttribute('data-duration') || '30';
            var rawPrice = row.getAttribute('data-price') || '0.00';
            var price = String(rawPrice).replace(/[^0-9.]/g, '');
            var category = row.getAttribute('data-category') || 'Manicure';
            var isVisible = row.getAttribute('data-visible') === '1';

            var idInput = document.getElementById('edit_service_id');
            if (idInput) idInput.value = serviceId;
            var nameInput = document.getElementById('edit_service_name');
            if (nameInput) nameInput.value = name;
            var descInput = document.getElementById('edit_description');
            if (descInput) descInput.value = desc;
            var durInput = document.getElementById('edit_duration');
            if (durInput) durInput.value = duration;
            var priceInput = document.getElementById('edit_price');
            if (priceInput) priceInput.value = parseFloat(price || 0).toFixed(2);

            var catInput = document.getElementById('edit_category');
            if (catInput) {
                var matched = false;
                for (var i = 0; i < catInput.options.length; i++) {
                    if (catInput.options[i].value.toLowerCase() === category.toLowerCase()) {
                        catInput.selectedIndex = i;
                        matched = true;
                        break;
                    }
                }
                if (!matched) catInput.value = category;
            }

            var visInput = document.getElementById('edit_is_visible');
            if (visInput) visInput.checked = isVisible;
        }

        openModal('editServiceModal');

        // Fetch fresh from server asynchronously to sync with DB
        if (window.BASE_URL) {
            fetch(BASE_URL + '/index.php?page=admin_services&ajax=get_service&id=' + serviceId)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data && data.success && data.service) {
                        var s = data.service;
                        if (document.getElementById('edit_service_id')) document.getElementById('edit_service_id').value = s.service_id;
                        if (document.getElementById('edit_service_name')) document.getElementById('edit_service_name').value = s.name || s.service_name || '';
                        if (document.getElementById('edit_description')) document.getElementById('edit_description').value = s.description || '';
                        if (document.getElementById('edit_duration')) document.getElementById('edit_duration').value = s.duration || s.duration_min || 30;
                        if (document.getElementById('edit_price')) document.getElementById('edit_price').value = parseFloat(s.price || 0).toFixed(2);
                        if (document.getElementById('edit_category') && s.category) {
                            var catSel = document.getElementById('edit_category');
                            for (var j = 0; j < catSel.options.length; j++) {
                                if (catSel.options[j].value.toLowerCase() === s.category.toLowerCase()) {
                                    catSel.selectedIndex = j;
                                    break;
                                }
                            }
                        }
                        if (document.getElementById('edit_is_visible')) {
                            document.getElementById('edit_is_visible').checked = (s.is_active == 1);
                        }
                    }
                })
                .catch(function(err) {
                    console.log('AJAX fetch note:', err);
                });
        }
    } catch (err) {
        console.error('Error in openEditModal:', err);
        openModal('editServiceModal');
    }
}
window.openEditModal = openEditModal;

// Delete Service Modal trigger
function openDeleteModal(serviceId, serviceName) {
    document.getElementById('delete_service_id').value = serviceId;
    document.getElementById('deleteServiceName').textContent = `"${serviceName}"`;
    openModal('deleteServiceModal');
}
window.openDeleteModal = openDeleteModal;

// Live Search Filter
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const q = this.value.trim().toLowerCase();
        let visibleCount = 0;
        const rows = document.querySelectorAll('#serviceRowsContainer .service-row');

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const desc = row.getAttribute('data-desc') || '';
            const cat = (row.getAttribute('data-category') || '').toLowerCase();

            if (name.includes(q) || desc.includes(q) || cat.includes(q)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const shownEl = document.getElementById('shownCount');
        if (shownEl) shownEl.textContent = visibleCount;

        const emptyEl = document.getElementById('emptyRow');
        if (emptyEl) {
            emptyEl.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
        }
    });
}

// Quick AJAX Visibility Toggle
function toggleVisibility(serviceId, badgeEl) {
    const row = document.getElementById('service-row-' + serviceId);
    const currentVis = row ? row.getAttribute('data-visible') === '1' : false;
    const newVis = !currentVis;

    // Optimistic UI update
    updateBadgeUI(badgeEl, newVis);
    if (row) row.setAttribute('data-visible', newVis ? '1' : '0');

    const formData = new FormData();
    formData.append('action', 'toggle_visibility');
    formData.append('service_id', serviceId);
    formData.append('status', newVis ? '1' : '0');

    fetch(`${BASE_URL}/index.php?page=admin_services`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || (newVis ? 'Service is now visible to clients.' : 'Service hidden from client page.'), true);
        } else {
            // Revert
            updateBadgeUI(badgeEl, currentVis);
            if (row) row.setAttribute('data-visible', currentVis ? '1' : '0');
            showToast('Failed to update visibility.', false);
        }
    })
    .catch(() => {
        updateBadgeUI(badgeEl, currentVis);
        if (row) row.setAttribute('data-visible', currentVis ? '1' : '0');
        showToast('Network error while toggling visibility.', false);
    });
}

function updateBadgeUI(badgeEl, isVisible) {
    if (!badgeEl) return;
    if (isVisible) {
        badgeEl.className = 'visibility';
        badgeEl.querySelector('.vis-icon').innerHTML = thumbIcon;
        badgeEl.querySelector('.label').textContent = 'Visible';
    } else {
        badgeEl.className = 'visibility hidden';
        badgeEl.querySelector('.vis-icon').innerHTML = thumbHiddenIcon;
        badgeEl.querySelector('.label').textContent = 'Hidden';
    }
}

// AJAX Form Submissions (Add, Edit, Delete)
document.getElementById('addServiceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);

    fetch(`${BASE_URL}/index.php?page=admin_services`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeModal('addServiceModal');
            showToast(data.message || 'Service added successfully!', true);
            setTimeout(() => { window.location.reload(); }, 600);
        } else {
            showToast(data.message || 'Could not add service.', false);
        }
    })
    .catch(() => {
        form.submit(); // fallback
    });
});

document.getElementById('editServiceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);

    fetch(`${BASE_URL}/index.php?page=admin_services`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeModal('editServiceModal');
            showToast(data.message || 'Service updated successfully!', true);
            setTimeout(() => { window.location.reload(); }, 600);
        } else {
            showToast(data.message || 'Could not update service.', false);
        }
    })
    .catch(() => {
        form.submit(); // fallback
    });
});

document.getElementById('deleteServiceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    const sid = document.getElementById('delete_service_id').value;

    fetch(`${BASE_URL}/index.php?page=admin_services`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        closeModal('deleteServiceModal');
        if (data.success) {
            showToast(data.message, true);
            const row = document.getElementById('service-row-' + sid);
            if (data.archived) {
                // If archived, update row visibility badge to hidden
                if (row) {
                    const badge = row.querySelector('.visibility');
                    if (badge) updateBadgeUI(badge, false);
                    row.setAttribute('data-visible', '0');
                }
            } else {
                if (row) row.remove();
                const totalEl = document.getElementById('totalCountBadge');
                if (totalEl) totalEl.textContent = Math.max(0, parseInt(totalEl.textContent) - 1);
            }
        } else {
            showToast(data.message || 'Error removing service.', false);
        }
    })
    .catch(() => {
        form.submit();
    });
}); // end deleteServiceForm submit listener

// Auto-open modal if specified in query params
window.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const modal = params.get('modal');
    if (modal === 'add') {
        openModal('addServiceModal');
    } else if (modal === 'edit') {
        const id = params.get('id');
        if (id) openEditModal(parseInt(id));
    }
});
</script>

</body>
</html>
