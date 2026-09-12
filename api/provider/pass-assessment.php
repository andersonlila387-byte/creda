<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

enforceSecurityHeaders();

$user_email = $_SESSION['user_email'] ?? null;
if (empty($user_email)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$score = isset($input['score']) ? intval($input['score']) : 90;

if ($score < 80) {
    echo json_encode(['success' => false, 'message' => 'Assessment failed. Score was below the 80% passing threshold.']);
    exit;
}

try {
    $db = getDBConnection();
    $stmt = $db->prepare("UPDATE users SET primary_role = 'provider', assessment_status = 'passed', assessment_score = :score WHERE email = :email");
    $stmt->execute([':score' => $score, ':email' => $user_email]);

    $_SESSION['user_role'] = 'provider';
    $_SESSION['active_role_mode'] = 'provider';
    $_SESSION['is_verified_pro'] = 0;
    $_SESSION['assessment_status'] = 'passed';

    echo json_encode([
        'success' => true,
        'message' => 'Assessment passed! Please submit your verification documents to activate your Pro profile.',
        'redirect' => 'pending-verification.php'
    ]);
} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
