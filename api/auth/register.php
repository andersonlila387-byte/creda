<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/emails/verification_email.php';
require_once __DIR__ . '/../../includes/emails/welcome_email.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$inputData = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$full_name = trim($inputData['full_name'] ?? '');
$email = strtolower(trim($inputData['email'] ?? ''));
$password = $inputData['password'] ?? '';
$terms_agreed = isset($inputData['terms_agreed']) ? (bool)$inputData['terms_agreed'] : false;

$role = strtolower(trim($inputData['role'] ?? 'client'));
if ($role !== 'provider' && $role !== 'client') {
    $role = 'client';
}

// Validation Rules
if (empty($full_name)) {
    echo json_encode(['success' => false, 'message' => 'Please enter your full name.']);
    exit;
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long.']);
    exit;
}

if (!$terms_agreed) {
    echo json_encode(['success' => false, 'message' => 'You must agree to the Terms of Service and Privacy Policy.']);
    exit;
}

try {
    $db = getDBConnection();

    // Check if email already exists in MySQL
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'This email address is already registered. Please log in.']);
        exit;
    }

    // Insert user into MySQL users table
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $ip = getUserIP();
    $insertStmt = $db->prepare("INSERT INTO users (full_name, email, password_hash, primary_role, registration_ip) VALUES (:full_name, :email, :password_hash, :role, :ip)");
    $insertStmt->execute([
        ':full_name' => $full_name,
        ':email' => $email,
        ':password_hash' => $password_hash,
        ':role' => $role,
        ':ip' => $ip
    ]);
    $user_id = $db->lastInsertId();

    // Generate 6-Digit OTP Verification Code
    $otp_code = sprintf('%06d', mt_rand(100000, 999999));
    $expires_at = date('Y-m-d H:i:s', time() + 900); // 15 Minutes

    // Insert OTP record into MySQL verification_otps table
    $otpStmt = $db->prepare("INSERT INTO verification_otps (email, otp_code, expires_at) VALUES (:email, :otp_code, :expires_at)");
    $otpStmt->execute([
        ':email' => $email,
        ':otp_code' => $otp_code,
        ':expires_at' => $expires_at
    ]);

    // Store session info
    $_SESSION['pending_user'] = [
        'id' => $user_id,
        'full_name' => $full_name,
        'email' => $email,
        'otp_code' => $otp_code,
        'verified' => false
    ];

    // Determine protocol & host for verification link
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $verify_url = $protocol . $host . "/scriptly/verify-email";

    // Send Verification Email via PHPMailer
    sendScriptlyVerificationEmail($email, $full_name, $otp_code, $verify_url);

    // Send Welcome Email to User
    sendCliniconnectWelcomeEmail($email, $full_name);

    echo json_encode([
        'success' => true,
        'message' => 'Account registered! A 6-digit verification code has been sent to ' . htmlspecialchars($email) . '. Please check your inbox and Spam / Junk folder.',
        'redirect' => 'verify-email'
    ]);
    exit;

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Registration error: ' . $e->getMessage()]);
    exit;
}
