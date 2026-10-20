<?php
/**
 * index.php — Front Controller / Dispatcher
 * -----------------------------------------
 * Single entry point for the Monet Nails MVC application.
 * Routes requests to the appropriate controller based on 'page' or 'controller' query parameter.
 *
 * Examples:
 *   Home:    http://localhost/MonetNails-Ecommerce2/MonetNails-Ecommerce/ (or ?page=home)
 *   Gallery: http://localhost/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=gallery
 *   Cart:    http://localhost/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=cart
 *   Product: http://localhost/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=product&id=1
 */

require_once __DIR__ . '/config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page = strtolower(trim($_GET['page'] ?? $_GET['controller'] ?? 'home'));

switch ($page) {
    case 'booking':
    case 'appointment':
    case 'appointments':
        require_once __DIR__ . '/controllers/BookingController.php';
        $controller = new BookingController($pdo);
        $controller->index();
        break;

    case 'booking_success':
        require_once __DIR__ . '/controllers/BookingController.php';
        $controller = new BookingController($pdo);
        $controller->success();
        break;

    case 'booking_notify':
        require_once __DIR__ . '/controllers/BookingController.php';
        $controller = new BookingController($pdo);
        $controller->notify();
        break;

    case 'about':
    case 'about_us':
    case 'aboutus':
        require_once __DIR__ . '/controllers/AboutController.php';
        $controller = new AboutController($pdo);
        $controller->index();
        break;

    case 'services':
    case 'service':
        require_once __DIR__ . '/controllers/ServiceController.php';
        $controller = new ServiceController($pdo);
        $controller->index();
        break;

    case 'gallery':
        require_once __DIR__ . '/controllers/GalleryController.php';
        $controller = new GalleryController($pdo);
        $controller->index();
        break;

    case 'contact':
    case 'contact_us':
    case 'contactus':
        require_once __DIR__ . '/controllers/ContactController.php';
        $controller = new ContactController($pdo);
        $controller->index();
        break;


    case 'cart':
        require_once __DIR__ . '/controllers/CartController.php';
        $controller = new CartController($pdo);
        $controller->index();
        break;

    case 'product':
        require_once __DIR__ . '/controllers/ProductController.php';
        $controller = new ProductController($pdo);
        $controller->index();
        break;

    case 'checkout':
        require_once __DIR__ . '/controllers/CheckoutController.php';
        $controller = new CheckoutController($pdo);
        $controller->index();
        break;

    case 'order_success':
        require_once __DIR__ . '/controllers/CheckoutController.php';
        $controller = new CheckoutController($pdo);
        $controller->success();
        break;

    case 'payhere_notify':
        require_once __DIR__ . '/controllers/CheckoutController.php';
        $controller = new CheckoutController($pdo);
        $controller->notify();
        break;

    case 'admin':
    case 'admin_dashboard':
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController($pdo);
        $controller->dashboard();
        break;

    case 'signin':
    case 'login':
        if (isset($_SESSION['admin_id']) || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin')) {
            header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=admin');
            exit;
        }
        if (isset($_SESSION['customer_id'])) {
            header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/');
            exit;
        }
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->signin();
        break;

    case 'signup':
    case 'register':
        if (isset($_SESSION['customer_id'])) {
            header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/');
            exit;
        }
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->signup();
        break;

    case 'verify':
        require_once __DIR__ . '/controllers/VerifyController.php';
        $controller = new VerifyController($pdo);
        $controller->index();
        break;

    case 'logout':
    case 'signout':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->logout();
        break;

    case 'home':
    default:
        require_once __DIR__ . '/controllers/HomeController.php';
        $controller = new HomeController($pdo);
        $controller->index();
        break;
}

