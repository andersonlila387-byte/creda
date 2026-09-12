<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/security.php';

enforceSecurityHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$inputData = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$email = strtolower(trim($inputData['email'] ?? ''));
$password = $inputData['password'] ?? '';
$login_portal = strtolower(trim($inputData['login_portal'] ?? 'client'));

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

if (empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please enter your password.']);
    exit;
}

// Security Check 1: Check IP Address Rate Limit (5 attempts per 15 minutes)
$ip_key = 'login_ip_' . getUserIP();
$ip_check = checkRateLimit($ip_key, 5, 900);
if (!$ip_check['allowed']) {
    echo json_encode(['success' => false, 'message' => $ip_check['message']]);
    exit;
}

// Security Check 2: Check Email Account Rate Limit (5 attempts per 15 minutes)
$email_key = 'login_email_' . $email;
$email_check = checkRateLimit($email_key, 5, 900);
if (!$email_check['allowed']) {
    echo json_encode(['success' => false, 'message' => $email_check['message']]);
    exit;
}

try {
    $db = getDBConnection();

    // Query user record from MySQL
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        recordFailedAttempt($ip_key, 5, 900);
        recordFailedAttempt($email_key, 5, 900);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password credentials.']);
        exit;
    }

    // Verify Password Hash
    if (!password_verify($password, $user['password_hash'])) {
        recordFailedAttempt($ip_key, 5, 900);
        recordFailedAttempt($email_key, 5, 900);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password credentials.']);
        exit;
    }

    if ($user['status'] !== 'active') {
        echo json_encode(['success' => false, 'message' => 'Your account has been suspended or restricted. Please contact support.']);
        exit;
    }

    // Reject clients trying to log in on provider portal
    if ($user['primary_role'] === 'client' && $login_portal === 'provider') {
        echo json_encode(['success' => false, 'message' => 'Access denied. Please log in from the client portal.']);
        exit;
    }

    // Clear Rate Limits on Successful Login
    clearRateLimit($ip_key);
    clearRateLimit($email_key);

    // Update last login IP address
    $login_ip = getUserIP();
    $ip_stmt = $db->prepare("UPDATE users SET last_login_ip = ? WHERE id = ?");
    $ip_stmt->execute([$login_ip, $user['id']]);

    // Set User Authentication Session State
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['primary_role'];
    $_SESSION['user_verified'] = (bool)$user['email_verified'];
    $_SESSION['is_verified_pro'] = (bool)($user['is_verified_pro'] ?? false);
    $_SESSION['assessment_status'] = $user['assessment_status'] ?? 'not_started';
    $_SESSION['user_logged_in'] = true;

    // Set Active Role Mode based on the login portal used
    if ($user['primary_role'] === 'provider') {
        if ($login_portal === 'client') {
            $_SESSION['active_role_mode'] = 'client';
        } else {
            $_SESSION['active_role_mode'] = 'provider';
        }
    } else {
        $_SESSION['active_role_mode'] = 'client';
    }

    // Role-based Conditional Redirection Logic
    $redirect = 'app/index.php';
    if (!$user['email_verified']) {
        $redirect = 'verify-email';
        
        // Auto-generate and send a fresh verification OTP code on login
        try {
            require_once __DIR__ . '/../../includes/emails/verification_email.php';
            
            $new_otp = sprintf('%06d', mt_rand(100000, 999999));
            $expires_at = date('Y-m-d H:i:s', time() + 900); // 15 Minutes
            
            $stmt = $db->prepare("INSERT INTO verification_otps (email, otp_code, expires_at) VALUES (:email, :otp_code, :expires_at)");
            $stmt->execute([
                ':email' => $user['email'],
                ':otp_code' => $new_otp,
                ':expires_at' => $expires_at
            ]);
            
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $verify_url = $protocol . $host . "/scriptly/verify-email";
            
            sendScriptlyVerificationEmail($user['email'], $user['full_name'], $new_otp, $verify_url);
        } catch (\Exception $ex) {
            error_log("Failed to auto-resend OTP during login: " . $ex->getMessage());
        }
    } elseif (!$user['onboarding_completed']) {
        $redirect = ($user['primary_role'] === 'provider') ? 'provider/onboarding.php' : 'onboarding';
    } elseif ($user['primary_role'] === 'provider') {
        if ($_SESSION['active_role_mode'] === 'client') {
            $redirect = 'app/index.php';
        } else {
            $redirect = 'provider/app/index.php';
        }
    } else {
        $redirect = 'app/index.php';
    }

    echo json_encode([
        'success' => true,
        'message' => 'Authentication successful! Redirecting...',
        'redirect' => $redirect
    ]);
    exit;

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Authentication error: ' . $e->getMessage()]);
    exit;
}
