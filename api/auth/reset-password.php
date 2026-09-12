<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$inputData = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$token = trim($inputData['token'] ?? '');
$new_password = $inputData['new_password'] ?? '';
$confirm_password = $inputData['confirm_password'] ?? '';

if (empty($token)) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing password reset token.']);
    exit;
}

if (strlen($new_password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Your new password must be at least 8 characters long.']);
    exit;
}

if ($new_password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match. Please re-enter.']);
    exit;
}

try {
    $db = getDBConnection();

    // Query token from MySQL password_resets table
    $stmt = $db->prepare("SELECT * FROM password_resets WHERE token = :token LIMIT 1");
    $stmt->execute([':token' => $token]);
    $resetRecord = $stmt->fetch();

    if (!$resetRecord) {
        echo json_encode(['success' => false, 'message' => 'This password reset link is invalid or has already been used. Please request a new link.']);
        exit;
    }

    if (strtotime($resetRecord['expires_at']) < time()) {
        $deleteStmt = $db->prepare("DELETE FROM password_resets WHERE token = :token");
        $deleteStmt->execute([':token' => $token]);
        echo json_encode(['success' => false, 'message' => 'This password reset link has expired. Please request a new link.']);
        exit;
    }

    // Update password in MySQL users table
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $updateUser = $db->prepare("UPDATE users SET password_hash = :hash WHERE email = :email");
    $updateUser->execute([
        ':hash' => $new_hash,
        ':email' => $resetRecord['email']
    ]);

    // Delete token from password_resets table
    $deleteStmt = $db->prepare("DELETE FROM password_resets WHERE email = :email");
    $deleteStmt->execute([':email' => $resetRecord['email']]);

    echo json_encode([
        'success' => true,
        'message' => 'Your password has been updated successfully! Redirecting to login...',
        'redirect' => 'login'
    ]);
    exit;

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Password reset error: ' . $e->getMessage()]);
    exit;
}
