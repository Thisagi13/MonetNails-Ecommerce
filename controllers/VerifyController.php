<?php
/**
 * VerifyController.php
 * ---------------------
 * Handles the email-verification step that runs AFTER sign-up.
 *
 * Flow:
 *   AuthController::signup() saves the customer (unverified),
 *   puts pending_customer_id + verify_email in session, then redirects:
 *       header('Location: /…/index.php?page=verify');
 *
 *   This controller:
 *     GET  → generate code (if none exists), send email, show the popup view
 *     POST action=verify  → check code; on success mark verified, redirect signin
 *     POST action=resend  → generate a new code and email it again
 *
 * MVC Layer  : Controller
 * Related view: views/auth/verify.php
 * Related models: VerificationCode.php, Customer.php
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/VerificationCode.php';
require_once __DIR__ . '/../models/Customer.php';

// PHPMailer — loaded from vendor/ after composer install, or from
// lib/phpmailer/ if you dropped the files in manually.
$mailerPaths = [
    __DIR__ . '/../vendor/autoload.php',                   // composer install
    __DIR__ . '/../lib/phpmailer/autoload.php',            // manual download
    __DIR__ . '/../lib/phpmailer/src/PHPMailer.php',       // raw files
];
$mailerLoaded = false;
foreach ($mailerPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $mailerLoaded = true;
        break;
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailerException;

class VerifyController
{
    private PDO $pdo;
    private VerificationCode $codeModel;
    private Customer $customerModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo           = $pdo;
        $this->codeModel     = new VerificationCode($pdo);
        $this->customerModel = new Customer($pdo);
    }

    /* ================================================================
     * index() — the only public action, handles GET + POST
     * ================================================================ */
    public function index(): void
    {
        // Must come from a successful sign-up
        $pendingId    = $_SESSION['pending_customer_id'] ?? null;
        $pendingEmail = $_SESSION['verify_email']        ?? '';

        // If no pending session, bounce to signup
        if ($pendingId === null) {
            header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signup');
            exit;
        }

        $verified = false;
        $error    = '';
        $notice   = '';

        // ── POST ─────────────────────────────────────────────────────
        $otpDigits = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = trim($_POST['action'] ?? '');
            if ($action !== 'resend') {
                $action = 'verify';
            }

            // Build the 6-digit string from the six separate boxes
            $otp = '';
            if (isset($_POST['otp']) && is_array($_POST['otp'])) {
                $otpDigits = $_POST['otp'];
                foreach ($_POST['otp'] as $digit) {
                    $otp .= trim($digit);
                }
            }

            if ($action === 'resend') {
                $code = $this->codeModel->create((int) $pendingId);
                if ($code !== false) {
                    $this->sendEmail($pendingEmail, $code);
                    $notice = 'A new verification code has been sent to your email.';
                } else {
                    $error = 'Could not generate a new code. Please try again.';
                }

            } elseif ($action === 'verify') {
                if (strlen($otp) !== 6 || !ctype_digit($otp)) {
                    $error = 'Please enter all 6 digits of your verification code.';
                } else {
                    $verifyId = $this->codeModel->verify((int) $pendingId, $otp);

                    if ($verifyId !== false) {
                        // Mark the code as used
                        $this->codeModel->markUsed($verifyId);

                        // Mark the customer as verified
                        $stmt = $this->pdo->prepare(
                            'UPDATE Customer SET is_verified = 1 WHERE customer_id = ?'
                        );
                        $stmt->execute([$pendingId]);

                        // Clean up session
                        unset($_SESSION['pending_customer_id']);
                        unset($_SESSION['verify_email']);

                        // Redirect to sign-in with a success flag
                        header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin&verified=1');
                        exit;
                    } else {
                        $error = 'Invalid or expired code. Please try again or resend.';
                    }
                }
            }

        // ── GET (first load) ─────────────────────────────────────────
        } else {
            // Only create + send a code if there isn't a fresh one yet
            if (!$this->codeModel->hasFreshCode((int) $pendingId)) {
                $code = $this->codeModel->create((int) $pendingId);
                if ($code !== false) {
                    $this->sendEmail($pendingEmail, $code);
                }
            }
        }

        // Pass variables to the view
        require __DIR__ . '/../views/auth/verify.php';
    }

    /* ================================================================
     * sendEmail() — sends the 6-digit code via PHPMailer (SMTP/Gmail)
     * ================================================================ */
    private function sendEmail(string $toEmail, string $code): bool
    {
        // ── CONFIGURE BELOW ─────────────────────────────────────────
        // If you are using Gmail, enable "App Passwords" and paste the
        // 16-character app password here (not your regular password).
        //
        $smtpHost     = 'smtp.gmail.com';
        $smtpUsername = 'omethrathisagi@gmail.com';
        $smtpPassword = 'xplcqvgqruxcdwfv';
        $smtpPort     = 587;
        $fromEmail    = 'omethrathisagi@gmail.com';
        $fromName     = 'Monet Nails';
        // ─────────────────────────────────────────────────────────────

        // Check whether PHPMailer was actually loaded
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            // Fallback: PHP's built-in mail() — works if your XAMPP
            // php.ini [mail function] is configured.
            $subject = 'Your Monet Nails Verification Code';
            $body    = "Your 6-digit verification code is: $code\n\n"
                     . "This code is valid for 10 minutes.\n\n"
                     . "— Monet Nails";
            return mail($toEmail, $subject, $body,
                "From: Monet Nails <$fromEmail>\r\nContent-Type: text/plain; charset=UTF-8");
        }

        try {
            $mail = new PHPMailer(true);

            // SMTP settings
            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpUsername;
            $mail->Password   = $smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $smtpPort;

            // Sender / recipient
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($toEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Monet Nails Verification Code';
            $mail->Body    = $this->emailHtml($code);
            $mail->AltBody = "Your verification code is: $code\nValid for 10 minutes.";

            $mail->send();
            return true;
        } catch (MailerException $e) {
            // Silently fail — the user can still resend
            error_log('PHPMailer error: ' . $e->getMessage());
            return false;
        }
    }

    /* ================================================================
     * emailHtml() — branded HTML email body
     * ================================================================ */
    private function emailHtml(string $code): string
    {
        $digits = '';
        foreach (str_split($code) as $d) {
            $digits .= '<span style="display:inline-block;width:44px;height:52px;line-height:52px;'
                     . 'text-align:center;font-size:28px;font-weight:700;'
                     . 'background:#F6F3F2;border-radius:6px;margin:0 4px;'
                     . 'font-family:Georgia,serif;color:#1C1B1B;">' . htmlspecialchars($d) . '</span>';
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#FCF9F8;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr><td align="center" style="padding:48px 16px;">
      <table width="540" cellpadding="0" cellspacing="0"
             style="background:#fff;border-radius:8px;overflow:hidden;
                    box-shadow:0 4px 24px rgba(0,0,0,0.08);">
        <!-- top bar -->
        <tr><td height="4" style="background:linear-gradient(90deg,#F2DDE2 0%,#A9275E 50%,#F2DDE2 100%);"></td></tr>
        <!-- body -->
        <tr><td style="padding:40px 48px;">
          <p style="font-family:Georgia,serif;font-size:26px;color:#1C1B1B;margin:0 0 8px;">Monet Nails</p>
          <p style="font-size:13px;letter-spacing:2px;text-transform:uppercase;color:#6A5A5F;margin:0 0 32px;">
            Haute Manicure &amp; Wellness Sanctuary</p>
          <p style="font-size:16px;color:#5E5B5C;margin:0 0 24px;">
            Here is your 6-digit verification code:</p>
          <div style="text-align:center;margin:0 0 24px;">$digits</div>
          <p style="font-size:14px;color:#9CA3AF;margin:0 0 32px;">
            This code expires in <strong>10 minutes</strong>.
            If you did not sign up, you can safely ignore this email.</p>
          <hr style="border:none;border-top:1px solid #F0EDED;margin:0 0 24px;">
          <p style="font-size:13px;color:#706065;margin:0;">
            &copy; 2024 Monet Nails. Artistry in Every Touch.</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }
}
