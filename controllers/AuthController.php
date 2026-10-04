<?php
/**
 * AuthController.php
 * ------------------
 * Handles Sign In and Sign Up for customers + admin sign-in.
 * All DB work is delegated to the Customer model.
 * Admin auth uses the admins table directly (no Admin model yet).
 *
 * MVC Layer : Controller
 * Related views :
 *   views/auth/signin.php
 *   views/auth/signup.php
 * Related model :
 *   models/Customer.php
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Customer.php';

class AuthController
{
    private ?PDO $pdo = null;
    private ?Customer $customerModel = null;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
        if ($pdo !== null) {
            $this->customerModel = new Customer($pdo);
        }
    }

    /* ====================================================
     * SIGN IN  (GET → show form | POST → process login)
     * ==================================================== */
    public function signin(): void
    {
        $errors    = [];
        $old_email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $role     = trim($_POST['role']     ?? 'customer');
            $email    = trim($_POST['email']    ?? '');
            $password =      $_POST['password'] ?? '';

            $old_email = htmlspecialchars($email);

            // Validate
            if ($email === '') {
                $errors[] = 'Email is required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please enter a valid email address.';
            }
            if ($password === '') {
                $errors[] = 'Password is required.';
            }

            if (empty($errors)) {
                if ($role === 'admin') {
                    $this->_handleAdminLogin($email, $password, $errors);
                } else {
                    $this->_handleCustomerLogin($email, $password, $errors);
                }
            }
        }

        // Show the sign-in view, passing data via local vars
        require __DIR__ . '/../views/auth/signin.php';
    }

    /* ====================================================
     * SIGN UP  (GET → show form | POST → process register)
     * ==================================================== */
    public function signup(): void
    {
        $errors    = [];
        $old_name  = '';
        $old_email = '';
        $old_phone = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $full_name        = trim($_POST['full_name']        ?? '');
            $email            = trim($_POST['email']            ?? '');
            $phone            = trim($_POST['phone']            ?? '');
            $password         =      $_POST['password']         ?? '';
            $confirm_password =      $_POST['confirm_password'] ?? '';

            $old_name  = htmlspecialchars($full_name);
            $old_email = htmlspecialchars($email);
            $old_phone = htmlspecialchars($phone);

            // Validate
            if ($full_name === '') {
                $errors[] = 'Full name is required.';
            } elseif (mb_strlen($full_name) > 100) {
                $errors[] = 'Full name is too long (max 100 characters).';
            }
            if ($email === '') {
                $errors[] = 'Email is required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please enter a valid email address.';
            } elseif (mb_strlen($email) > 100) {
                $errors[] = 'Email is too long (max 100 characters).';
            }
            if ($phone !== '' && mb_strlen($phone) > 20) {
                $errors[] = 'Phone number is too long (max 20 characters).';
            }
            if ($password === '') {
                $errors[] = 'Password is required.';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters.';
            }
            if ($confirm_password === '') {
                $errors[] = 'Please confirm your password.';
            } elseif ($password !== $confirm_password) {
                $errors[] = 'Passwords do not match.';
            }

            if (empty($errors)) {
                // Uniqueness check
                if ($this->customerModel->emailExists($email)) {
                    $errors[] = 'An account with this email already exists.';
                }
            }

            if (empty($errors)) {
                // Split full name
                $parts  = preg_split('/\s+/', $full_name, 2);
                $f_name = $parts[0];
                $l_name = isset($parts[1]) && $parts[1] !== '' ? $parts[1] : $parts[0];

                $hash = password_hash($password, PASSWORD_BCRYPT);
                if ($hash === false) {
                    $errors[] = 'Could not hash password. Please try again.';
                } else {
                    $new_id = $this->customerModel->create($f_name, $l_name, $email, $hash, $phone);
                    if ($new_id === false) {
                        $errors[] = 'Could not create account. Please try again.';
                    } else {
                        // Store verification context in session, then redirect to verify page
                        $_SESSION['pending_customer_id'] = $new_id;
                        $_SESSION['verify_email']        = $email;
                        header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=verify');
                        exit;
                    }
                }
            }
        }

        // Show the sign-up view
        require __DIR__ . '/../views/auth/signup.php';
    }

    /* ====================================================
     * LOGOUT
     * ==================================================== */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
        header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/');
        exit;
    }

    /* ====================================================
     * PRIVATE HELPERS
     * ==================================================== */
    private function _handleCustomerLogin(string $email, string $password, array &$errors): void
    {
        $user = $this->customerModel->findByEmail($email);

        if (!$user) {
            $errors[] = 'No account found with that email.';
            return;
        }
        if (!password_verify($password, $user['password'])) {
            $errors[] = 'Incorrect password. Please try again.';
            return;
        }

        // Block unverified accounts — redirect back to verify page
        if (empty($user['is_verified'])) {
            $_SESSION['pending_customer_id'] = $user['customer_id'];
            $_SESSION['verify_email']        = $user['email'];
            header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=verify');
            exit;
        }

        $_SESSION['customer_id']   = $user['customer_id'];
        $_SESSION['user_id']       = $user['customer_id'];
        $_SESSION['customer_name'] = $user['f_name'] . ' ' . $user['l_name'];
        $_SESSION['role']          = 'customer';

        header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/');
        exit;
    }

    private function _handleAdminLogin(string $email, string $password, array &$errors): void
    {
        $stmt = $this->pdo->prepare(
            'SELECT admin_id, f_name, l_name, email, password FROM admins WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $errors[] = 'No admin account found with that email.';
            return;
        }
        if (!password_verify($password, $user['password'])) {
            $errors[] = 'Incorrect password. Please try again.';
            return;
        }

        $_SESSION['admin_id']   = $user['admin_id'];
        $_SESSION['admin_name'] = $user['f_name'] . ' ' . $user['l_name'];
        $_SESSION['role']       = 'admin';

        header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/admin/dashboard.php');
        exit;
    }
}
