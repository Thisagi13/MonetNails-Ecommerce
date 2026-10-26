<?php
/**
 * services/MailService.php
 * ------------------------
 * Centralized mail notification service for Monet Nails Atelier.
 * Handles HTML emails for:
 *   1. Account Created Successfully
 *   2. Order Placed Successfully (with full order breakdown & Order ID)
 *   3. Appointment Placed Successfully (with full appointment details)
 *   4. Appointment Deleted / Cancelled by Admin
 */

// Load PHPMailer classes
$mailerPaths = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../lib/phpmailer/autoload.php',
    __DIR__ . '/../lib/phpmailer/src/PHPMailer.php',
];
foreach ($mailerPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;

class MailService
{
    /**
     * Get mail configuration array.
     */
    public static function getConfig(): array
    {
        $configFile = __DIR__ . '/../config/mail.php';
        if (file_exists($configFile)) {
            return require $configFile;
        }
        return [
            'host'       => 'smtp.gmail.com',
            'username'   => 'omethrathisagi@gmail.com',
            'password'   => 'xplc qvgq ruxc dwfv',
            'port'       => 587,
            'encryption' => 'tls',
            'from_email' => 'omethrathisagi@gmail.com',
            'from_name'  => 'Monet Nails Atelier',
            'admin_email'=> 'omethrathisagi@gmail.com',
        ];
    }

    /**
     * Base sender method using PHPMailer with fallback to mail().
     */
    public static function send(string $toEmail, string $subject, string $htmlBody, string $altBody = ''): bool
    {
        if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            error_log("MailService: Invalid recipient email: " . $toEmail);
            return false;
        }

        $config = self::getConfig();

        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            try {
                $mail = new PHPMailer(true);
                $mail->CharSet = 'UTF-8';
                $mail->isSMTP();
                $mail->Host       = $config['host'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $config['username'];
                $mail->Password   = $config['password'];
                $mail->SMTPSecure = ($config['encryption'] === 'ssl') ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = (int)($config['port'] ?? 587);

                $mail->setFrom($config['from_email'], $config['from_name']);
                $mail->addAddress($toEmail);

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $htmlBody;
                $mail->AltBody = !empty($altBody) ? $altBody : strip_tags(str_replace(['<br>', '<br/>', '</p>'], ["\n", "\n", "\n\n"], $htmlBody));

                $mail->send();
                return true;
            } catch (MailerException $e) {
                error_log("MailService (PHPMailer) Error: " . $e->getMessage());
                // Fall back to native mail()
            } catch (\Throwable $t) {
                error_log("MailService General Error: " . $t->getMessage());
            }
        }

        // Native PHP mail() fallback
        $headers  = "From: " . $config['from_name'] . " <" . $config['from_email'] . ">\r\n";
        $headers .= "Reply-To: " . $config['from_email'] . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        return @mail($toEmail, $subject, $htmlBody, $headers);
    }

    /**
     * 1. Send Account Created Successfully Email
     */
    public static function sendAccountCreated(string $toEmail, string $customerName): bool
    {
        $name = trim($customerName) !== '' ? htmlspecialchars($customerName) : 'Valued Patron';
        $subject = 'Welcome to Monet Nails Atelier — Account Created Successfully';

        $bodyContent = <<<HTML
        <p style="font-size:16px;line-height:1.6;color:#332629;margin:0 0 16px;">
          Dear <strong>{$name}</strong>,
        </p>
        <p style="font-size:15px;line-height:1.6;color:#574147;margin:0 0 20px;">
          Welcome to <strong>Monet Nails Atelier</strong>. Your account has been created and verified successfully.
        </p>
        
        <div style="background:#FAF6F5;border-left:4px solid #A9275E;border-radius:4px;padding:16px 20px;margin-bottom:24px;">
          <h4 style="margin:0 0 8px;color:#A9275E;font-size:15px;font-family:Georgia,serif;">Your Atelier Privileges:</h4>
          <ul style="margin:0;padding-left:18px;color:#574147;font-size:14px;line-height:1.7;">
            <li>Exclusive access to handcrafted bespoke press-on nail collections</li>
            <li>Direct appointment reservations with our senior nail artisans</li>
            <li>Real-time order tracking and appointment history</li>
            <li>Custom nail size preset storage for effortless shopping</li>
          </ul>
        </div>

        <p style="font-size:14px;line-height:1.6;color:#574147;margin:0 0 28px;">
          You can now sign in at any time to explore our newest collections, schedule your next salon appointment, or customize your bespoke nail designs.
        </p>

        <div style="text-align:center;margin:32px 0;">
          <a href="http://localhost/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin"
             style="background:#A9275E;color:#FFFFFF;text-decoration:none;font-weight:600;font-size:14px;letter-spacing:0.05em;padding:13px 32px;border-radius:4px;display:inline-block;">
            SIGN IN TO YOUR ACCOUNT
          </a>
        </div>
HTML;

        $html = self::wrapTemplate('Account Created Successfully', 'Welcome to Monet Nails Atelier', $bodyContent);
        return self::send($toEmail, $subject, $html);
    }

    /**
     * 2. Send Order Placed Successfully Email (with order details & ID)
     */
    public static function sendOrderPlaced(string $toEmail, array $order, array $items = []): bool
    {
        $orderId = (int)($order['order_id'] ?? 0);
        $orderRef = '#MN-ORD-' . str_pad((string)$orderId, 5, '0', STR_PAD_LEFT);
        $customerName = htmlspecialchars(trim(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')) ?: 'Patron');
        $phone = htmlspecialchars($order['phone'] ?? 'N/A');
        $street = htmlspecialchars($order['street_address'] ?? '');
        $city = htmlspecialchars($order['city'] ?? '');
        $postal = htmlspecialchars($order['postal_code'] ?? '');
        $fullAddress = trim("{$street}, {$city} {$postal}", ', ');
        $paymentMethod = htmlspecialchars($order['payment_method'] ?? 'PayHere');
        $orderDate = !empty($order['created_at']) ? date('F j, Y, g:i A', strtotime($order['created_at'])) : date('F j, Y, g:i A');

        $subtotal = number_format((float)($order['subtotal'] ?? 0), 2);
        $shippingFee = number_format((float)($order['shipping_fee'] ?? 150), 2);
        $totalAmount = number_format((float)($order['total_amount'] ?? 0), 2);

        // Build items table rows
        $itemsHtml = '';
        foreach ($items as $item) {
            $pName = htmlspecialchars($item['name'] ?? ($item['product_name'] ?? 'Bespoke Artisan Set'));
            $variant = htmlspecialchars($item['variant'] ?? '');
            $variantSubtext = !empty($variant) ? '<br><span style="color:#8A757A;font-size:12px;">' . $variant . '</span>' : '';
            $qty = (int)($item['quantity'] ?? 1);
            $unitPrice = number_format((float)($item['price'] ?? ($item['unit_price'] ?? 0)), 2);
            $itemTotal = number_format((float)($item['price'] ?? ($item['unit_price'] ?? 0)) * $qty, 2);

            $itemsHtml .= <<<ROW
            <tr style="border-bottom:1px solid #EFEAE9;">
              <td style="padding:12px 8px;vertical-align:middle;color:#1C1B1B;font-size:14px;">
                <strong>{$pName}</strong>
                {$variantSubtext}
              </td>
              <td style="padding:12px 8px;text-align:center;color:#574147;font-size:14px;">{$qty}</td>
              <td style="padding:12px 8px;text-align:right;color:#574147;font-size:14px;">Rs. {$unitPrice}</td>
              <td style="padding:12px 8px;text-align:right;color:#1C1B1B;font-weight:600;font-size:14px;">Rs. {$itemTotal}</td>
            </tr>
ROW;
        }

        $subject = "Order Placed Successfully — {$orderRef}";

        $bodyContent = <<<HTML
        <p style="font-size:16px;line-height:1.6;color:#332629;margin:0 0 12px;">
          Dear <strong>{$customerName}</strong>,
        </p>
        <p style="font-size:15px;line-height:1.6;color:#574147;margin:0 0 20px;">
          Thank you for your purchase. We are pleased to confirm that your order <strong>{$orderRef}</strong> has been placed successfully and is now being carefully prepared.
        </p>

        <!-- Order Summary Card -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#FAF6F5;border-radius:6px;padding:16px;margin-bottom:24px;border:1px solid #EFEAE9;">
          <tr>
            <td width="50%" style="vertical-align:top;padding:8px;">
              <span style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#8A757A;display:block;margin-bottom:4px;">Order Reference</span>
              <strong style="font-size:16px;color:#A9275E;font-family:Georgia,serif;">{$orderRef}</strong>
              <div style="font-size:13px;color:#574147;margin-top:4px;">Placed on: {$orderDate}</div>
            </td>
            <td width="50%" style="vertical-align:top;padding:8px;">
              <span style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#8A757A;display:block;margin-bottom:4px;">Payment Method</span>
              <strong style="font-size:14px;color:#1C1B1B;">{$paymentMethod}</strong>
              <div style="font-size:13px;color:#574147;margin-top:4px;">Status: <span style="color:#2E7D32;font-weight:600;">Confirmed</span></div>
            </td>
          </tr>
          <tr>
            <td colspan="2" style="border-top:1px solid #E5DFDE;padding:12px 8px 4px;margin-top:8px;">
              <span style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#8A757A;display:block;margin-bottom:4px;">Delivery Address</span>
              <div style="font-size:14px;color:#332629;">{$customerName} &middot; {$phone}</div>
              <div style="font-size:13px;color:#574147;margin-top:2px;">{$fullAddress}</div>
            </td>
          </tr>
        </table>

        <!-- Order Items -->
        <h4 style="margin:0 0 12px;color:#1C1B1B;font-size:16px;font-family:Georgia,serif;">Items in Your Order</h4>
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-bottom:20px;">
          <thead>
            <tr style="background:#F2DDE2;border-bottom:2px solid #D49A89;">
              <th style="padding:10px 8px;text-align:left;font-size:13px;color:#574147;text-transform:uppercase;letter-spacing:0.04em;">Product</th>
              <th style="padding:10px 8px;text-align:center;font-size:13px;color:#574147;text-transform:uppercase;letter-spacing:0.04em;">Qty</th>
              <th style="padding:10px 8px;text-align:right;font-size:13px;color:#574147;text-transform:uppercase;letter-spacing:0.04em;">Price</th>
              <th style="padding:10px 8px;text-align:right;font-size:13px;color:#574147;text-transform:uppercase;letter-spacing:0.04em;">Total</th>
            </tr>
          </thead>
          <tbody>
            {$itemsHtml}
          </tbody>
        </table>

        <!-- Totals Table -->
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
          <tr>
            <td width="60%"></td>
            <td width="40%">
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="padding:6px 0;color:#574147;font-size:14px;">Subtotal:</td>
                  <td style="padding:6px 0;text-align:right;color:#1C1B1B;font-size:14px;">Rs. {$subtotal}</td>
                </tr>
                <tr>
                  <td style="padding:6px 0;color:#574147;font-size:14px;">Shipping Fee:</td>
                  <td style="padding:6px 0;text-align:right;color:#1C1B1B;font-size:14px;">Rs. {$shippingFee}</td>
                </tr>
                <tr style="border-top:2px solid #A9275E;">
                  <td style="padding:10px 0 0;font-weight:700;color:#1C1B1B;font-size:16px;">Total Amount:</td>
                  <td style="padding:10px 0 0;text-align:right;font-weight:700;color:#A9275E;font-size:18px;">Rs. {$totalAmount}</td>
                </tr>
              </table>
            </td>
          </tr>
        </table>

        <div style="text-align:center;margin:30px 0;">
          <a href="http://localhost/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=order_history"
             style="background:#A9275E;color:#FFFFFF;text-decoration:none;font-weight:600;font-size:14px;letter-spacing:0.05em;padding:13px 32px;border-radius:4px;display:inline-block;">
            VIEW ORDER IN YOUR ACCOUNT
          </a>
        </div>
HTML;

        $html = self::wrapTemplate('Order Placed Successfully', "Order Confirmation: {$orderRef}", $bodyContent);
        return self::send($toEmail, $subject, $html);
    }

    /**
     * 3. Send Appointment Placed Successfully Email (with appointment details)
     */
    public static function sendAppointmentBooked(string $toEmail, array $appointment): bool
    {
        $aptId = (int)($appointment['appointment_id'] ?? 0);
        $aptRef = $appointment['reference_no'] ?? ('#MN-APT-' . str_pad((string)$aptId, 5, '0', STR_PAD_LEFT));
        $clientName = htmlspecialchars(trim(($appointment['first_name'] ?? '') . ' ' . ($appointment['last_name'] ?? '')) ?: 'Patron');
        $phone = htmlspecialchars($appointment['phone'] ?? 'N/A');
        $serviceName = htmlspecialchars($appointment['service_name'] ?? 'Bespoke Nail Art Session');
        $serviceCategory = htmlspecialchars($appointment['service_category'] ?? 'Atelier Service');
        $specialRequests = htmlspecialchars($appointment['special_requests'] ?? '');

        // Format Date and Time
        $aptDateRaw = $appointment['appointment_date'] ?? date('Y-m-d');
        $aptDate = date('l, F j, Y', strtotime($aptDateRaw));
        $aptTimeRaw = $appointment['appointment_time'] ?? '10:00:00';
        $aptTime = date('g:i A', strtotime($aptTimeRaw));
        $duration = (int)($appointment['service_duration'] ?? 45);

        $totalPrice = number_format((float)($appointment['total_price'] ?? ($appointment['total_amount'] ?? 0)), 2);
        $status = ucfirst($appointment['status'] ?? 'Confirmed');

        $specialRequestsRowHtml = !empty($specialRequests)
            ? '<tr><td colspan="2" style="border-top:1px solid #E5DFDE;padding:10px 0 0;"><span style="font-size:12px;text-transform:uppercase;color:#8A757A;display:block;">Special Requests</span><div style="font-size:13px;color:#574147;">' . $specialRequests . '</div></td></tr>'
            : '';

        $subject = "Appointment Placed Successfully — {$aptRef} ({$aptDate})";

        $bodyContent = <<<HTML
        <p style="font-size:16px;line-height:1.6;color:#332629;margin:0 0 12px;">
          Dear <strong>{$clientName}</strong>,
        </p>
        <p style="font-size:15px;line-height:1.6;color:#574147;margin:0 0 20px;">
          Your appointment at <strong>Monet Nails Atelier</strong> has been placed and reserved successfully. Our salon artisans look forward to crafting an exceptional experience for you.
        </p>

        <!-- Appointment Card -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#FAF6F5;border-radius:6px;padding:20px;margin-bottom:24px;border:1px solid #EFEAE9;">
          <tr>
            <td colspan="2" style="padding-bottom:12px;border-bottom:1px solid #E5DFDE;">
              <span style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#8A757A;display:block;">Appointment Reference</span>
              <strong style="font-size:18px;color:#A9275E;font-family:Georgia,serif;">{$aptRef}</strong>
            </td>
          </tr>
          <tr>
            <td width="50%" style="vertical-align:top;padding:12px 8px 6px 0;">
              <span style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#8A757A;display:block;">Reserved Date</span>
              <strong style="font-size:15px;color:#1C1B1B;">{$aptDate}</strong>
            </td>
            <td width="50%" style="vertical-align:top;padding:12px 0 6px 8px;">
              <span style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#8A757A;display:block;">Time Slot & Duration</span>
              <strong style="font-size:15px;color:#1C1B1B;">{$aptTime} ({$duration} mins)</strong>
            </td>
          </tr>
          <tr>
            <td width="50%" style="vertical-align:top;padding:10px 8px 6px 0;">
              <span style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#8A757A;display:block;">Selected Service</span>
              <strong style="font-size:15px;color:#1C1B1B;">{$serviceName}</strong>
              <div style="font-size:12px;color:#8A757A;">Category: {$serviceCategory}</div>
            </td>
            <td width="50%" style="vertical-align:top;padding:10px 0 6px 8px;">
              <span style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#8A757A;display:block;">Total Amount</span>
              <strong style="font-size:17px;color:#A9275E;font-family:Georgia,serif;">Rs. {$totalPrice}</strong>
            </td>
          </tr>
          {$specialRequestsRowHtml}
        </table>

        <!-- Location & Preparation Notes -->
        <div style="background:#FFF;border:1px solid #EFEAE9;border-left:4px solid #A9275E;border-radius:4px;padding:16px 20px;margin-bottom:24px;">
          <h4 style="margin:0 0 6px;color:#A9275E;font-size:14px;font-family:Georgia,serif;">Atelier Location & Guidelines:</h4>
          <p style="margin:0 0 8px;font-size:13px;color:#332629;line-height:1.5;">
            📍 <strong>Monet Nails Atelier</strong> &mdash; 482/A Kandy Road, Yakkala, Sri Lanka
          </p>
          <ul style="margin:0;padding-left:18px;color:#574147;font-size:13px;line-height:1.6;">
            <li>Please arrive 10 minutes prior to your scheduled time to enjoy our welcome ritual.</li>
            <li>If you need to reschedule, kindly notify us at least 24 hours in advance.</li>
            <li>For inquiries or directions, contact us at +94 77 123 4567.</li>
          </ul>
        </div>

        <div style="text-align:center;margin:28px 0;">
          <a href="http://localhost/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=my_appointments"
             style="background:#A9275E;color:#FFFFFF;text-decoration:none;font-weight:600;font-size:14px;letter-spacing:0.05em;padding:13px 32px;border-radius:4px;display:inline-block;">
            VIEW YOUR APPOINTMENTS
          </a>
        </div>
HTML;

        $html = self::wrapTemplate('Appointment Placed Successfully', "Appointment Reserved: {$aptRef}", $bodyContent);
        return self::send($toEmail, $subject, $html);
    }

    /**
     * 4. Send Appointment Deleted / Cancelled by Admin Email
     */
    public static function sendAppointmentDeleted(string $toEmail, array $appointment): bool
    {
        $aptId = (int)($appointment['appointment_id'] ?? 0);
        $aptRef = $appointment['reference_no'] ?? ('#MN-APT-' . str_pad((string)$aptId, 5, '0', STR_PAD_LEFT));
        $clientName = htmlspecialchars(trim(($appointment['first_name'] ?? '') . ' ' . ($appointment['last_name'] ?? '')) ?: 'Patron');
        $serviceName = htmlspecialchars($appointment['service_name'] ?? 'Salon Appointment');

        $aptDateRaw = $appointment['appointment_date'] ?? date('Y-m-d');
        $aptDate = date('l, F j, Y', strtotime($aptDateRaw));
        $aptTimeRaw = $appointment['appointment_time'] ?? '10:00:00';
        $aptTime = date('g:i A', strtotime($aptTimeRaw));

        $subject = "Notice of Appointment Cancellation — Monet Nails Atelier ({$aptRef})";

        $bodyContent = <<<HTML
        <p style="font-size:16px;line-height:1.6;color:#332629;margin:0 0 12px;">
          Dear <strong>{$clientName}</strong>,
        </p>
        <p style="font-size:15px;line-height:1.6;color:#574147;margin:0 0 20px;">
          We are writing to inform you that your upcoming appointment at <strong>Monet Nails Atelier</strong> has been cancelled by administration.
        </p>

        <!-- Cancelled Appointment Details -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#FFF5F5;border-radius:6px;padding:20px;margin-bottom:24px;border:1px solid #F5C6CB;">
          <tr>
            <td colspan="2" style="padding-bottom:12px;border-bottom:1px solid #F5C6CB;">
              <span style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#721C24;display:block;">Cancelled Appointment Reference</span>
              <strong style="font-size:18px;color:#721C24;font-family:Georgia,serif;">{$aptRef}</strong>
              <span style="display:inline-block;margin-left:8px;padding:2px 8px;background:#E74C3C;color:#FFF;font-size:11px;font-weight:700;border-radius:3px;text-transform:uppercase;">CANCELLED</span>
            </td>
          </tr>
          <tr>
            <td width="50%" style="vertical-align:top;padding:12px 8px 6px 0;">
              <span style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#8A757A;display:block;">Scheduled Date</span>
              <strong style="font-size:15px;color:#1C1B1B;">{$aptDate}</strong>
            </td>
            <td width="50%" style="vertical-align:top;padding:12px 0 6px 8px;">
              <span style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#8A757A;display:block;">Scheduled Time</span>
              <strong style="font-size:15px;color:#1C1B1B;">{$aptTime}</strong>
            </td>
          </tr>
          <tr>
            <td colspan="2" style="vertical-align:top;padding:10px 0 4px;">
              <span style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#8A757A;display:block;">Service</span>
              <strong style="font-size:15px;color:#1C1B1B;">{$serviceName}</strong>
            </td>
          </tr>
        </table>

        <p style="font-size:14px;line-height:1.6;color:#574147;margin:0 0 20px;">
          We apologize for any inconvenience this schedule adjustment may cause. If any advance fee was collected, our customer concierge team will process an immediate refund or credit.
        </p>

        <p style="font-size:14px;line-height:1.6;color:#574147;margin:0 0 28px;">
          You are welcome to schedule a new appointment at your convenience or reach out directly to our concierge team if you have any questions.
        </p>

        <div style="text-align:center;margin:30px 0;">
          <a href="http://localhost/MonetNails-Ecommerce2/MonetNails-Ecommerce/booking.php"
             style="background:#A9275E;color:#FFFFFF;text-decoration:none;font-weight:600;font-size:14px;letter-spacing:0.05em;padding:13px 32px;border-radius:4px;display:inline-block;">
            SCHEDULE A NEW APPOINTMENT
          </a>
        </div>
HTML;

        $html = self::wrapTemplate('Appointment Cancelled', "Cancellation Notice: {$aptRef}", $bodyContent);
        return self::send($toEmail, $subject, $html);
    }

    /**
     * 5. Send Contact Us notification to Admin
     */
    public static function sendContactNotification(string $senderName, string $senderEmail, string $subjectText, string $messageText): bool
    {
        $config = self::getConfig();
        $adminEmail = $config['admin_email'] ?? $config['from_email'];

        $name = htmlspecialchars($senderName);
        $email = htmlspecialchars($senderEmail);
        $subj = htmlspecialchars($subjectText);
        $msgFormatted = nl2br(htmlspecialchars($messageText));
        $date = date('F j, Y, g:i A');

        $emailSubject = "New Contact Inquiry from {$name}: {$subj}";

        $bodyContent = <<<HTML
        <p style="font-size:16px;line-height:1.6;color:#332629;margin:0 0 12px;">
          Hello Admin,
        </p>
        <p style="font-size:15px;line-height:1.6;color:#574147;margin:0 0 20px;">
          A new customer inquiry has been received via the Monet Nails contact form on <strong>{$date}</strong>:
        </p>

        <table width="100%" cellpadding="0" cellspacing="0" style="background:#FAF6F5;border-radius:6px;padding:20px;margin-bottom:24px;border:1px solid #EFEAE9;">
          <tr>
            <td style="padding-bottom:10px;border-bottom:1px solid #E5DFDE;">
              <span style="font-size:12px;text-transform:uppercase;color:#8A757A;display:block;">Customer Name</span>
              <strong style="font-size:15px;color:#1C1B1B;">{$name}</strong>
            </td>
          </tr>
          <tr>
            <td style="padding:10px 0;border-bottom:1px solid #E5DFDE;">
              <span style="font-size:12px;text-transform:uppercase;color:#8A757A;display:block;">Customer Email</span>
              <a href="mailto:{$email}" style="color:#A9275E;font-size:14px;text-decoration:none;font-weight:600;">{$email}</a>
            </td>
          </tr>
          <tr>
            <td style="padding:10px 0;border-bottom:1px solid #E5DFDE;">
              <span style="font-size:12px;text-transform:uppercase;color:#8A757A;display:block;">Subject</span>
              <strong style="font-size:14px;color:#1C1B1B;">{$subj}</strong>
            </td>
          </tr>
          <tr>
            <td style="padding-top:12px;">
              <span style="font-size:12px;text-transform:uppercase;color:#8A757A;display:block;margin-bottom:6px;">Message</span>
              <div style="font-size:14px;color:#332629;line-height:1.6;background:#FFF;padding:12px;border-radius:4px;border:1px solid #EFEAE9;">
                {$msgFormatted}
              </div>
            </td>
          </tr>
        </table>

        <div style="text-align:center;margin:28px 0;">
          <a href="mailto:{$email}?subject=Re:%20{$subj}"
             style="background:#A9275E;color:#FFFFFF;text-decoration:none;font-weight:600;font-size:14px;letter-spacing:0.05em;padding:13px 32px;border-radius:4px;display:inline-block;">
            REPLY TO CUSTOMER
          </a>
        </div>
HTML;

        $html = self::wrapTemplate('New Customer Inquiry', "Contact Form Message from {$name}", $bodyContent);
        return self::send($adminEmail, $emailSubject, $html);
    }

    /**
     * Master responsive branded HTML layout wrapper.
     */
    private static function wrapTemplate(string $headerTitle, string $preheader, string $contentHtml): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{$headerTitle} — Monet Nails</title>
</head>
<body style="margin:0;padding:0;background:#FCF9F8;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">
  <!-- Hidden Preheader -->
  <div style="display:none;font-size:1px;color:#FCF9F8;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
    {$preheader}
  </div>

  <table width="100%" cellpadding="0" cellspacing="0" style="background:#FCF9F8;min-height:100vh;">
    <tr>
      <td align="center" style="padding:40px 16px;">
        <table width="580" cellpadding="0" cellspacing="0" style="max-width:580px;width:100%;background:#FFFFFF;border-radius:8px;overflow:hidden;box-shadow:0 6px 24px rgba(0,0,0,0.06);border:1px solid #F0EAE8;">
          
          <!-- Top decorative gradient band -->
          <tr>
            <td height="5" style="background:linear-gradient(90deg, #F2DDE2 0%, #A9275E 50%, #F2DDE2 100%);"></td>
          </tr>

          <!-- Header -->
          <tr>
            <td style="padding:36px 40px 24px;text-align:center;background:#FFFFFF;border-bottom:1px solid #FAF4F3;">
              <h1 style="font-family:Georgia,serif;font-size:28px;font-weight:700;color:#1C1B1B;margin:0 0 6px;letter-spacing:0.02em;">
                Monet Nails
              </h1>
              <span style="font-size:12px;letter-spacing:0.2em;text-transform:uppercase;color:#8A757A;font-weight:500;">
                High-End Artistry &bull; Atelier Priv&eacute;
              </span>
            </td>
          </tr>

          <!-- Content Body -->
          <tr>
            <td style="padding:32px 40px 36px;background:#FFFFFF;">
              {$contentHtml}
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="padding:28px 40px;background:#FAF6F5;border-top:1px solid #EFEAE9;text-align:center;">
              <p style="font-size:13px;color:#574147;margin:0 0 6px;font-weight:600;">
                Monet Nails Atelier
              </p>
              <p style="font-size:12px;color:#8A757A;margin:0 0 12px;line-height:1.5;">
                482/A Kandy Road, Yakkala, Sri Lanka &bull; Tel: +94 77 123 4567<br>
                Email: info@monetnails.com &bull; Web: www.monetnails.com
              </p>
              <p style="font-size:11px;color:#B3A1A5;margin:0;">
                &copy; 2026 Monet Nails. All rights reserved. You received this email regarding your activity on Monet Nails.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
    }
}
