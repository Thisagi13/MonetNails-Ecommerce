<?php
/**
 * includes/header.php — Unified Master Header Section
 * Monet Nails E-Commerce Application
 *
 * Automatically detects whether the user is logged in or guest,
 * prepares necessary user data (name, email, avatar, cart count),
 * and loads the matching header section (Registered vs Unregistered).
 *
 * Can be included on ANY page:
 *   <?php require __DIR__ . '/../../includes/header.php'; ?>
 * Optional parameter:
 *   $activeNav = 'home' | 'about' | 'services' | 'gallery' | 'cart' | 'contact' | 'checkout';
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_url   = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';
$isLoggedIn = isset($_SESSION['customer_id']) || isset($_SESSION['user_id']);
$cid        = (int)($_SESSION['customer_id'] ?? $_SESSION['user_id'] ?? 0);
$activeNav  = $activeNav ?? ($currentPage ?? ($_GET['page'] ?? 'home'));

// Ensure DB connection is available
if (!isset($pdo) || !($pdo instanceof PDO)) {
    if (isset($GLOBALS['pdo']) && ($GLOBALS['pdo'] instanceof PDO)) {
        $pdo = $GLOBALS['pdo'];
    } else {
        $dbPath = __DIR__ . '/../config/db.php';
        if (file_exists($dbPath)) {
            require_once $dbPath;
            if (isset($pdo) && ($pdo instanceof PDO)) {
                $GLOBALS['pdo'] = $pdo;
            } elseif (isset($GLOBALS['pdo']) && ($GLOBALS['pdo'] instanceof PDO)) {
                $pdo = $GLOBALS['pdo'];
            }
        }
    }
}

// Resolve Cart Item Count for badge across ALL pages
if (!isset($headerCartCount)) {
    if (isset($data['itemCount'])) {
        $headerCartCount = (int)$data['itemCount'];
    } elseif (isset($itemCount)) {
        $headerCartCount = (int)$itemCount;
    } elseif (isset($cartItems) && is_array($cartItems)) {
        $headerCartCount = count($cartItems);
    } else {
        // Fetch live count from DB cart_items table
        if (isset($pdo) && ($pdo instanceof PDO)) {
            try {
                if ($cid > 0) {
                    $cStmt = $pdo->prepare("SELECT COUNT(*) FROM cart_items WHERE customer_id = ?");
                    $cStmt->execute([$cid]);
                    $headerCartCount = (int)$cStmt->fetchColumn();
                } else {
                    $sKey = session_id();
                    if (!empty($sKey)) {
                        $cStmt = $pdo->prepare("SELECT COUNT(*) FROM cart_items WHERE session_key = ?");
                        $cStmt->execute([$sKey]);
                        $headerCartCount = (int)$cStmt->fetchColumn();
                    } else {
                        $headerCartCount = 0;
                    }
                }
            } catch (Exception $e) {
                $headerCartCount = 0;
            }
        } else {
            $headerCartCount = 0;
        }
    }
}

if ($isLoggedIn) {
    // 1. Resolve Customer Data if not already provided by controller
    if (empty($customer_name) || empty($customer_email) || empty($avatar_html)) {
        if (isset($pdo) && ($pdo instanceof PDO) && $cid > 0) {
            try {
                $stmt = $pdo->prepare("SELECT customer_id, f_name, l_name, email, profile_image FROM Customer WHERE customer_id = ? LIMIT 1");
                $stmt->execute([$cid]);
                $cust = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($cust) {
                    if (empty($customer_name)) {
                        $customer_name = trim(($cust['f_name'] ?? '') . ' ' . ($cust['l_name'] ?? ''));
                    }
                    if (empty($customer_email)) {
                        $customer_email = $cust['email'] ?? '';
                    }
                    if (empty($customer_avatar)) {
                        $customer_avatar = $cust['profile_image'] ?? '';
                    }
                }
            } catch (Exception $e) {
                // Silently fallback to session values
            }
        }
    }

    if (empty($customer_name)) {
        $customer_name = $_SESSION['customer_name'] ?? 'Client';
    }

    if (empty($customer_avatar) && !empty($_SESSION['profile_image'])) {
        $customer_avatar = $_SESSION['profile_image'];
    }

    // 2. Build Avatar HTML if not already created
    if (empty($avatar_html)) {
        $avatarDiskPath = __DIR__ . '/../' . ltrim($customer_avatar ?? '', '/');
        if (!empty($customer_avatar) && file_exists($avatarDiskPath)) {
            $avatarSrc = (strpos($customer_avatar, 'http') === 0 || strpos($customer_avatar, '/') === 0)
                ? $customer_avatar
                : $base_url . '/' . ltrim($customer_avatar, '/');
            $avatar_html = '<img src="' . htmlspecialchars($avatarSrc) . '" alt="avatar">';
        } else {
            $initials = strtoupper(substr($customer_name ?: 'M', 0, 1));
            $avatar_html = '<span class="avatar-initials">' . htmlspecialchars($initials ?: 'M') . '</span>';
        }
    }

    // Load registered user header
    require __DIR__ . '/header_registered.php';
} else {
    // Load unregistered user header
    require __DIR__ . '/header_unregistered.php';
}
