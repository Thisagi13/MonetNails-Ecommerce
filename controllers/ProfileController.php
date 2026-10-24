<?php
/**
 * ProfileController.php — Customer Account & Profile Settings Controller
 * Monet Nails MVC Application
 *
 * Handles:
 *   1. Viewing customer account details
 *   2. Updating personal profile info & uploading profile photo
 *   3. Changing account password securely
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Customer.php';

class ProfileController
{
    private PDO $pdo;
    private Customer $customerModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->customerModel = new Customer($pdo);
    }

    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';

        // 1. Authentication check
        $customerId = (int)($_SESSION['customer_id'] ?? $_SESSION['user_id'] ?? 0);
        if ($customerId <= 0) {
            $_SESSION['redirect_after_login'] = 'account_settings';
            header('Location: ' . $base_url . '/index.php?page=signin');
            exit;
        }

        // 2. Fetch customer from DB
        $customer = $this->customerModel->findById($customerId);
        if (!$customer) {
            session_destroy();
            header('Location: ' . $base_url . '/index.php?page=signin');
            exit;
        }

        $profile_msg  = '';
        $profile_err  = '';
        $password_msg = '';
        $password_err = '';

        // 3. Process Profile Save
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
            $fullName = trim($_POST['full_name'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $phone    = trim($_POST['phone_no'] ?? '');
            $address  = trim($_POST['address'] ?? '');

            // Split full name into first and last name
            $parts = explode(' ', $fullName, 2);
            $fName = trim($parts[0] ?? '');
            $lName = isset($parts[1]) ? trim($parts[1]) : '';
            if ($lName === '') {
                $lName = $fName;
            }

            if ($fullName === '' || $fName === '') {
                $profile_err = 'Please enter your full name.';
            } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $profile_err = 'Please enter a valid email address.';
            } elseif ($this->customerModel->emailExistsExcept($email, $customerId)) {
                $profile_err = 'That email address is already in use by another account.';
            } else {
                $photoPath = null;

                // Handle profile photo upload if provided
                if (isset($_FILES['profile_photo']) && is_array($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $photo = $_FILES['profile_photo'];

                    if ($photo['error'] === UPLOAD_ERR_OK && $photo['size'] > 0) {
                        $maxSize = 2 * 1024 * 1024; // 2MB
                        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
                        $ext = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));

                        // Check file size
                        if ($photo['size'] > $maxSize) {
                            $profile_err = 'Photo is too large. Maximum allowed size is 2MB.';
                        } elseif (!in_array($ext, $allowedExts, true)) {
                            $profile_err = 'Only JPG, JPEG, PNG, or WEBP photos are allowed.';
                        } else {
                            // Verify actual image validity
                            $imgInfo = @getimagesize($photo['tmp_name']);
                            if ($imgInfo === false) {
                                $profile_err = 'Uploaded file is not a valid image.';
                            } else {
                                $uploadDir = __DIR__ . '/../uploads';
                                if (!is_dir($uploadDir)) {
                                    mkdir($uploadDir, 0755, true);
                                }

                                $newFileName = 'customer_' . $customerId . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                                $targetFilePath = $uploadDir . '/' . $newFileName;

                                if (move_uploaded_file($photo['tmp_name'], $targetFilePath)) {
                                    $photoPath = 'uploads/' . $newFileName;

                                    // Remove old uploaded avatar file if it exists and was custom
                                    $oldImage = $customer['profile_image'] ?? '';
                                    if (!empty($oldImage) && strpos($oldImage, 'uploads/customer_') !== false) {
                                        $oldDiskPath = __DIR__ . '/../' . ltrim($oldImage, '/');
                                        if (file_exists($oldDiskPath) && is_file($oldDiskPath)) {
                                            @unlink($oldDiskPath);
                                        }
                                    }
                                } else {
                                    $profile_err = 'Failed to save uploaded photo. Please try again.';
                                }
                            }
                        }
                    } else {
                        $profile_err = 'Photo upload error. Please choose a different photo.';
                    }
                }

                if ($profile_err === '') {
                    $ok = $this->customerModel->updateProfile(
                        $customerId,
                        $fName,
                        $lName,
                        $email,
                        $phone,
                        $address,
                        $photoPath
                    );

                    if ($ok) {
                        $profile_msg = 'Your profile details have been updated successfully.';
                        
                        // Update active session metadata
                        $_SESSION['customer_name'] = trim($fName . ' ' . $lName);
                        if ($photoPath !== null) {
                            $_SESSION['profile_image'] = $photoPath;
                        }

                        // Reload fresh customer data
                        $customer = $this->customerModel->findById($customerId);
                    } else {
                        $profile_err = 'An error occurred while saving your profile. Please try again.';
                    }
                }
            }
        }

        // 4. Process Password Update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword     = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($customer['password']) || !password_verify($currentPassword, $customer['password'])) {
                $password_err = 'Your current password is incorrect.';
            } elseif (strlen($newPassword) < 6) {
                $password_err = 'New password must be at least 6 characters long.';
            } elseif ($newPassword !== $confirmPassword) {
                $password_err = 'New password and confirm password do not match.';
            } else {
                $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $ok = $this->customerModel->updatePassword($customerId, $newHash);

                if ($ok) {
                    $password_msg = 'Your password has been changed successfully.';
                    // Refresh data
                    $customer = $this->customerModel->findById($customerId);
                } else {
                    $password_err = 'Could not update password. Please try again.';
                }
            }
        }

        // 5. Determine Avatar Display
        $avatarDiskPath = __DIR__ . '/../' . ltrim($customer['profile_image'] ?? '', '/');
        $hasCustomAvatar = !empty($customer['profile_image']) && file_exists($avatarDiskPath);
        $avatarUrl = $hasCustomAvatar ? ($base_url . '/' . ltrim($customer['profile_image'], '/')) : '';

        $initials = strtoupper(substr(trim(($customer['f_name'] ?? '') . ' ' . ($customer['l_name'] ?? '')), 0, 1));
        if ($initials === '') {
            $initials = 'M';
        }

        $activeNav = 'account_settings';

        // Render view
        require __DIR__ . '/../views/profile/edit_profile.php';
    }
}
