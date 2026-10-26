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

    case 'admin_products':
    case 'admin_product':
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController($pdo);
        $controller->manageProducts();
        break;

    case 'admin_orders':
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController($pdo);
        $controller->manageOrders();
        break;

    case 'admin_appointments':
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController($pdo);
        $controller->manageAppointments();
        break;

    case 'admin_services':
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController($pdo);
        $controller->manageServices();
        break;

    case 'admin_users':
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController($pdo);
        $controller->manageUsers();
        break;

    case 'admin_reports':
    case 'admin_sales':
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController($pdo);
        $controller->salesReports();
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

    case 'forgot_password':
    case 'forgot-password':
    case 'forgot':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/controllers/PasswordResetController.php';
            $controller = new PasswordResetController($pdo);
            $controller->requestReset();
        } else {
            header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin&forgot=1');
            exit;
        }
        break;

    case 'forgot_password_request':
        require_once __DIR__ . '/controllers/PasswordResetController.php';
        $controller = new PasswordResetController($pdo);
        $controller->requestReset();
        break;

    case 'reset_password':
    case 'reset-password':
    case 'resetpassword':
        require_once __DIR__ . '/controllers/PasswordResetController.php';
        $controller = new PasswordResetController($pdo);
        $controller->resetPassword();
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

    case 'account_settings':
    case 'account':
    case 'profile':
    case 'edit_profile':
        require_once __DIR__ . '/controllers/ProfileController.php';
        $controller = new ProfileController($pdo);
        $controller->index();
        break;

    case 'my_appointments':
    case 'appointment_history':
    case 'appointments_history':
        require_once __DIR__ . '/controllers/AppointmentHistoryController.php';
        $controller = new AppointmentHistoryController($pdo);
        $controller->index();
        break;

    case 'order_history':
    case 'my_orders':
    case 'orders_history':
        require_once __DIR__ . '/controllers/OrderHistoryController.php';
        $controller = new OrderHistoryController($pdo);
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

