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

if ($isLoggedIn) {
    // 1. Resolve Customer Data if not already provided by controller
    if (empty($customer_name) || empty($customer_email) || empty($avatar_html)) {
        if (!isset($pdo) && isset($GLOBALS['pdo'])) {
            $pdo = $GLOBALS['pdo'];
        }
        if (!isset($pdo)) {
            $dbPath = __DIR__ . '/../config/db.php';
            if (file_exists($dbPath)) {
                require_once $dbPath;
            }
        }

        if (isset($pdo) && $pdo instanceof PDO && $cid > 0) {
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

    // 2. Build Avatar HTML if not already created
    if (empty($avatar_html)) {
        $avatarDiskPath = __DIR__ . '/../' . ltrim($customer_avatar ?? '', '/');
        if (!empty($customer_avatar) && file_exists($avatarDiskPath)) {
            $avatar_html = '<img src="' . htmlspecialchars($customer_avatar) . '" alt="avatar">';
        } else {
            $initials = strtoupper(substr($customer_name ?: 'M', 0, 1));
            $avatar_html = '<span class="avatar-initials">' . htmlspecialchars($initials ?: 'M') . '</span>';
        }
    }

    // 3. Resolve Cart Item Count for badge
    if (!isset($headerCartCount)) {
        if (isset($data['itemCount'])) {
            $headerCartCount = (int)$data['itemCount'];
        } elseif (isset($itemCount)) {
            $headerCartCount = (int)$itemCount;
        } elseif (isset($cartItems) && is_array($cartItems)) {
            $headerCartCount = count($cartItems);
        } else {
            // Fetch live count from DB
            if (!isset($pdo) && isset($GLOBALS['pdo'])) {
                $pdo = $GLOBALS['pdo'];
            }
            if (isset($pdo) && $pdo instanceof PDO && $cid > 0) {
                try {
                    $cStmt = $pdo->prepare("SELECT COALESCE(SUM(ci.quantity), 0) FROM Cart c JOIN CartItem ci ON c.cart_id = ci.cart_id WHERE c.user_id = ?");
                    $cStmt->execute([$cid]);
                    $headerCartCount = (int)$cStmt->fetchColumn();
                } catch (Exception $e) {
                    $headerCartCount = 0;
                }
            } else {
                $headerCartCount = 0;
            }
        }
    }

    // Load registered user header
    require __DIR__ . '/header_registered.php';
} else {
    // Load unregistered user header
    require __DIR__ . '/header_unregistered.php';
}
