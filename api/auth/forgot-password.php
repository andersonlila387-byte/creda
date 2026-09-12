<?php
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../config/smtp.php';
require_once __DIR__ . '/../../includes/emails/reset_password.php';

enforceSecurityHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$inputData = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$email = strtolower(trim($inputData['email'] ?? ''));

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// Rate limiting on password reset requests (3 attempts per 15 minutes)
$reset_key = 'reset_req_' . getUserIP();
$reset_check = checkRateLimit($reset_key, 3, 900);
if (!$reset_check['allowed']) {
    echo json_encode(['success' => false, 'message' => $reset_check['message']]);
    exit;
}

try {
    $db = getDBConnection();

    // Check if user exists
    $userStmt = $db->prepare("SELECT id, full_name FROM users WHERE email = :email");
    $userStmt->execute([':email' => $email]);
    $user = $userStmt->fetch();

    if (!$user) {
        recordFailedAttempt($reset_key, 3, 900);
        // Friendly message without leaking email existence
        echo json_encode([
            'success' => true,
            'message' => 'If an account exists for ' . htmlspecialchars($email) . ', password reset instructions have been sent.'
        ]);
        exit;
    }

    // Generate Secure Token & Expiration (60 Minutes)
    $token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', time() + 3600);

    // Insert reset token into MySQL password_resets table
    $insertStmt = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)");
    $insertStmt->execute([
        ':email' => $email,
        ':token' => $token,
        ':expires_at' => $expires_at
    ]);

    // Determine protocol & host for reset URL
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $reset_url = $protocol . $host . "/scriptly/reset-password/token/" . $token;

    // Generate Custom HTML Email Content
    $html_email_content = getPasswordResetEmailTemplate($user['full_name'], $reset_url, 60);

    // Dispatch Password Reset Email via PHPMailer & Live SMTP Server
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = defined('SMTP_HOST') ? SMTP_HOST : 'armorbullet.host';
            $mail->SMTPAuth   = true;
            $mail->Username   = defined('SMTP_USER') ? SMTP_USER : 'noreply@armorbullet.host';
            $mail->Password   = defined('SMTP_PASS') ? SMTP_PASS : '$Helicopter123';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS Port 587
            $mail->Port       = defined('SMTP_PORT') ? SMTP_PORT : 587;
            $mail->Timeout    = 15;

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom(defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@armorbullet.host', defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Scriptly Platform');
            $mail->addAddress($email, $user['full_name']);
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Scriptly Account Password';
            $mail->Body    = $html_email_content;

            $mail->send();
        } catch (Exception $e) {
            error_log("Password Reset Mail Error: " . $mail->ErrorInfo);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Password reset instructions have been sent to ' . htmlspecialchars($email) . '. Please check your Inbox and Spam / Junk folder.'
    ]);
    exit;

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Password reset error: ' . $e->getMessage()]);
    exit;
}
