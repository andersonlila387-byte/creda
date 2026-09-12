<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

enforceSecurityHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$inputData = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$user_otp = trim($inputData['otp_code'] ?? '');

$pending_email = $_SESSION['pending_user']['email'] ?? $_SESSION['user_email'] ?? '';

if (empty($pending_email)) {
    echo json_encode(['success' => false, 'message' => 'No pending registration found. Please register first.']);
    exit;
}

if (empty($user_otp) || strlen($user_otp) !== 6 || !ctype_digit($user_otp)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid 6-digit verification code.']);
    exit;
}

// Rate limiting on OTP attempts (5 attempts per 15 minutes)
$otp_key = 'otp_attempt_' . $pending_email;
$otp_check = checkRateLimit($otp_key, 5, 900);
if (!$otp_check['allowed']) {
    echo json_encode(['success' => false, 'message' => $otp_check['message']]);
    exit;
}

try {
    $db = getDBConnection();

    // Query active unexpired OTP from MySQL
    $stmt = $db->prepare("SELECT id, otp_code, expires_at FROM verification_otps WHERE email = :email AND is_used = 0 ORDER BY id DESC LIMIT 1");
    $stmt->execute([':email' => $pending_email]);
    $otpRecord = $stmt->fetch();

    if (!$otpRecord || $otpRecord['otp_code'] !== $user_otp) {
        recordFailedAttempt($otp_key, 5, 900);
        echo json_encode(['success' => false, 'message' => 'Invalid verification code. Please check and try again.']);
        exit;
    }

    if (strtotime($otpRecord['expires_at']) < time()) {
        echo json_encode(['success' => false, 'message' => 'Verification code has expired. Please click "Resend Code".']);
        exit;
    }

    // Clear Rate Limits on Successful Verification
    clearRateLimit($otp_key);

    // Mark OTP as used in MySQL
    $updateOtp = $db->prepare("UPDATE verification_otps SET is_used = 1 WHERE id = :id");
    $updateOtp->execute([':id' => $otpRecord['id']]);

    // Mark user email as verified in users table
    $updateUser = $db->prepare("UPDATE users SET email_verified = 1 WHERE email = :email");
    $updateUser->execute([':email' => $pending_email]);

    // Query user details to set full authentication session
    $userStmt = $db->prepare("SELECT id, full_name, email, primary_role, onboarding_completed, assessment_status, is_verified_pro FROM users WHERE email = :email LIMIT 1");
    $userStmt->execute([':email' => $pending_email]);
    $userRecord = $userStmt->fetch();

    // Populate Active Session State
    $_SESSION['user_id'] = $userRecord['id'] ?? null;
    $_SESSION['user_name'] = $userRecord['full_name'] ?? ($_SESSION['pending_user']['full_name'] ?? 'Member');
    $_SESSION['user_email'] = $pending_email;
    $_SESSION['user_role'] = $userRecord['primary_role'] ?? 'client';
    $_SESSION['user_verified'] = true;
    $_SESSION['user_logged_in'] = true;
    $_SESSION['onboarding_completed'] = (bool)($userRecord['onboarding_completed'] ?? false);
    $_SESSION['is_verified_pro'] = (bool)($userRecord['is_verified_pro'] ?? false);
    $_SESSION['assessment_status'] = $userRecord['assessment_status'] ?? 'not_started';

    // Role-based Conditional Redirection Logic upon successful verification
    if ($_SESSION['user_role'] === 'provider') {
        if (!empty($_SESSION['is_verified_pro'])) {
            $redirect = 'provider/app/index.php';
        } else {
            if (($_SESSION['assessment_status'] ?? 'not_started') === 'passed') {
                $redirect = 'provider/app/pending-verification.php';
            } else {
                $redirect = 'provider/app/assessment.php';
            }
        }
    } else {
        $redirect = $_SESSION['onboarding_completed'] ? 'app/index.php' : 'onboarding';
    }

    echo json_encode([
        'success' => true,
        'message' => 'Email verified successfully! Loading portal...',
        'redirect' => $redirect
    ]);
    exit;

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Verification error: ' . $e->getMessage()]);
    exit;
}
