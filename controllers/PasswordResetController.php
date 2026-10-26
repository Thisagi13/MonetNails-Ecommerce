<?php
/**
 * PasswordResetController.php
 * ----------------------------
 * Handles end-to-end Password Reset workflow:
 *  1. Request reset link (Popup 1 submission via AJAX or POST)
 *  2. Sends branded luxury HTML email with secure 1-hour token link
 *  3. Validates token & displays dedicated Reset Password page
 *  4. Processes password update & triggers Popup 2 ("Password Reset Successful")
 *
 * MVC Layer : Controller
 * Related views :
 *   views/auth/signin.php (contains Popup 1)
 *   views/auth/reset_password.php (Reset page + contains Popup 2)
 * Related models :
 *   models/PasswordReset.php
 *   models/Customer.php
 * Related service :
 *   services/MailService.php
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/PasswordReset.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../services/MailService.php';

class PasswordResetController
{
    private PDO $pdo;
    private PasswordReset $resetModel;
    private Customer $customerModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo           = $pdo;
        $this->resetModel    = new PasswordReset($pdo);
        $this->customerModel = new Customer($pdo);
    }

    /**
     * Helper to get the base URL of the application.
     */
    private function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . $host . '/MonetNails-Ecommerce2/MonetNails-Ecommerce';
    }

    /**
     * 1. Process "Forgot Password" request (Popup 1)
     * Handles both AJAX/Fetch JSON requests and regular form submissions.
     */
    public function requestReset(): void
    {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'))
               || (isset($_POST['ajax']) && $_POST['ajax'] === '1');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $this->getBaseUrl() . '/index.php?page=signin&forgot=1');
            exit;
        }

        // Parse email from POST (or JSON payload if sent as json)
        $email = '';
        if (isset($_POST['email'])) {
            $email = trim($_POST['email']);
        } else {
            $input = json_decode(file_get_contents('php://input'), true);
            $email = trim($input['email'] ?? '');
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($isAjax) {
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
                exit;
            }
            $_SESSION['forgot_error'] = 'Please enter a valid email address.';
            header('Location: ' . $this->getBaseUrl() . '/index.php?page=signin&forgot=1');
            exit;
        }

        // Look up user in Customer table first
        $customer = $this->customerModel->findByEmail($email);
        $userType = 'customer';
        $userName = '';

        if ($customer) {
            $userName = trim(($customer['f_name'] ?? '') . ' ' . ($customer['l_name'] ?? ''));
        } else {
            // Check Admin table
            try {
                $stmt = $this->pdo->prepare('SELECT admin_id, f_name, l_name, email FROM admins WHERE email = ? LIMIT 1');
                $stmt->execute([$email]);
                $admin = $stmt->fetch();
                if (!$admin) {
                    $stmt = $this->pdo->prepare('SELECT admin_id, f_name, l_name, email FROM admin WHERE email = ? LIMIT 1');
                    $stmt->execute([$email]);
                    $admin = $stmt->fetch();
                }
                if ($admin) {
                    $userType = 'admin';
                    $userName = trim(($admin['f_name'] ?? '') . ' ' . ($admin['l_name'] ?? ''));
                }
            } catch (\Exception $e) {
                // Table check fallback
            }
        }

        // If user found in system, generate token and send email
        if ($customer || (isset($admin) && $admin)) {
            $token = $this->resetModel->createToken($email, $userType);
            if ($token !== false) {
                $resetLink = $this->getBaseUrl() . '/index.php?page=reset_password&token=' . urlencode($token);
                MailService::sendPasswordResetLink($email, $userName, $resetLink);
            }
        }

        // In real-world security best practices, we always return success message so email enumeration is mitigated,
        // while reassuring real users that the email has been dispatched.
        $successMsg = 'A password reset link has been dispatched to ' . htmlspecialchars($email) . '. Please check your inbox and spam folder.';

        if ($isAjax) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'success' => true,
                'message' => $successMsg,
                'email'   => $email,
            ]);
            exit;
        }

        $_SESSION['forgot_success'] = $successMsg;
        $_SESSION['forgot_email']   = $email;
        header('Location: ' . $this->getBaseUrl() . '/index.php?page=signin&forgot_sent=1');
        exit;
    }

    /**
     * 2. Dedicated Reset Password Page & Action
     * GET  → Validates token, displays reset password form
     * POST → Validates input, hashes new password, updates DB, displays Popup 2
     */
    public function resetPassword(): void
    {
        $token = trim($_GET['token'] ?? $_POST['token'] ?? '');
        $errors = [];
        $success = false;

        if ($token === '') {
            $tokenStatus = 'not_found';
            $resetRecord = false;
        } else {
            $tokenStatus = $this->resetModel->checkTokenStatus($token);
            $resetRecord = ($tokenStatus === 'valid') ? $this->resetModel->findByToken($token) : false;
        }

        // Handle POST submission to update password
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                   || (isset($_POST['ajax']) && $_POST['ajax'] === '1');

            if ($tokenStatus !== 'valid' || !$resetRecord) {
                $errors[] = 'This password reset link is invalid or has expired. Please request a new one.';
            } else {
                $password        = $_POST['password']         ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';

                if ($password === '') {
                    $errors[] = 'Please enter a new password.';
                } elseif (strlen($password) < 6) {
                    $errors[] = 'Password must be at least 6 characters.';
                }

                if ($confirmPassword === '') {
                    $errors[] = 'Please confirm your new password.';
                } elseif ($password !== $confirmPassword) {
                    $errors[] = 'Passwords do not match.';
                }

                if (empty($errors)) {
                    $email    = $resetRecord['email'];
                    $userType = $resetRecord['user_type'] ?? 'customer';
                    $hash     = password_hash($password, PASSWORD_BCRYPT);
                    $updated  = false;

                    if ($userType === 'customer') {
                        $updated = $this->customerModel->updatePasswordByEmail($email, $hash);
                    } else {
                        // Admin password update
                        try {
                            $stmt = $this->pdo->prepare('UPDATE admins SET password = ? WHERE email = ?');
                            $updated = $stmt->execute([$hash, $email]);
                            if (!$updated || $stmt->rowCount() === 0) {
                                $stmt = $this->pdo->prepare('UPDATE admin SET password = ? WHERE email = ?');
                                $updated = $stmt->execute([$hash, $email]);
                            }
                        } catch (\Exception $e) {
                            $updated = false;
                        }
                    }

                    if ($updated !== false) {
                        // Invalidate the token
                        $this->resetModel->markUsed($token);
                        $success = true;

                        if ($isAjax) {
                            header('Content-Type: application/json; charset=UTF-8');
                            echo json_encode([
                                'success' => true,
                                'message' => 'Password reset successful!'
                            ]);
                            exit;
                        }
                    } else {
                        $errors[] = 'Unable to update password at this time. Please try again.';
                    }
                }
            }

            if ($isAjax && !empty($errors)) {
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['success' => false, 'errors' => $errors]);
                exit;
            }
        }

        // Render the dedicated view
        require __DIR__ . '/../views/auth/reset_password.php';
    }
}
