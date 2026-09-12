<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/emails/verification_email.php';

enforceSecurityHeaders();

$email = $_SESSION['pending_user']['email'] ?? $_SESSION['user_email'] ?? '';
$full_name = $_SESSION['pending_user']['full_name'] ?? $_SESSION['user_name'] ?? 'Member';

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'No registration session found. Please register first.']);
    exit;
}

// Rate limit check on resends (max 5 resends per 15 minutes)
$resend_key = 'resend_otp_' . $email;
$resend_check = checkRateLimit($resend_key, 5, 900);
if (!$resend_check['allowed']) {
    echo json_encode(['success' => false, 'message' => $resend_check['message']]);
    exit;
}

try {
    $db = getDBConnection();

    // Generate New 6-Digit OTP Code
    $new_otp = sprintf('%06d', mt_rand(100000, 999999));
    $expires_at = date('Y-m-d H:i:s', time() + 900); // 15 Minutes

    // Insert new OTP record into MySQL verification_otps table
    $stmt = $db->prepare("INSERT INTO verification_otps (email, otp_code, expires_at) VALUES (:email, :otp_code, :expires_at)");
    $stmt->execute([
        ':email' => $email,
        ':otp_code' => $new_otp,
        ':expires_at' => $expires_at
    ]);

    // Update Pending User Session
    $_SESSION['pending_user']['otp_code'] = $new_otp;
    $_SESSION['pending_user']['otp_expires'] = time() + 900;

    // Protocol & Host for Verification Link
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $verify_url = $protocol . $host . "/scriptly/verify-email";

    // Send Email via PHPMailer Live SMTP Server
    $sent = sendScriptlyVerificationEmail($email, $full_name, $new_otp, $verify_url);

    if (!$sent) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send verification email via SMTP server. Please check your connection and try again.'
        ]);
        exit;
    }

    // Record attempt on successful send
    recordFailedAttempt($resend_key, 5, 900);

    echo json_encode([
        'success' => true,
        'message' => 'A new 6-digit verification code has been sent to ' . htmlspecialchars($email) . '. Please check your Inbox and Spam / Junk folder.'
    ]);
    exit;

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Resend OTP error: ' . $e->getMessage()]);
    exit;
}
