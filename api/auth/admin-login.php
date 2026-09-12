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
$username_or_email = trim($inputData['username_or_email'] ?? '');
$password = $inputData['password'] ?? '';

if (empty($username_or_email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please provide both username/email and password.']);
    exit;
}

// Rate Limiting key for admin login
$ip_key = 'admin_login_ip_' . getUserIP();
$ip_check = checkRateLimit($ip_key, 5, 900);
if (!$ip_check['allowed']) {
    echo json_encode(['success' => false, 'message' => $ip_check['message']]);
    exit;
}

try {
    $db = getDBConnection();
    
    // Fetch Admin record
    $stmt = $db->prepare("SELECT * FROM admins WHERE email = :id OR username = :id LIMIT 1");
    $stmt->execute([':id' => $username_or_email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        recordFailedAttempt($ip_key, 5, 900);
        echo json_encode(['success' => false, 'message' => 'Invalid admin credentials.']);
        exit;
    }
    
    // Clear rate limits
    clearRateLimit($ip_key);
    
    // Set dedicated admin session states (completely isolated from users)
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_email'] = $admin['email'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Admin authentication successful! Loading console...',
        'redirect' => 'index.php'
    ]);
    exit;
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Admin login error: ' . $e->getMessage()]);
    exit;
}
