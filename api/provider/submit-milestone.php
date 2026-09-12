<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

enforceSecurityHeaders();

// Verify Authentication
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

// Read input JSON
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$milestone_id = isset($input['milestone_id']) ? intval($input['milestone_id']) : 0;

if ($milestone_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid milestone ID.']);
    exit;
}

try {
    $db = getDBConnection();

    // Verify milestone exists and provider has access
    $m_stmt = $db->prepare("
        SELECT m.id, m.title, m.status, p.id as project_id, p.title as project_title, p.client_id
        FROM milestones m
        JOIN projects p ON m.project_id = p.id
        JOIN proposals pr ON pr.project_id = p.id AND pr.provider_id = :uid AND pr.status = 'accepted'
        WHERE m.id = :mid LIMIT 1
    ");
    $m_stmt->execute([':uid' => $user_id, ':mid' => $milestone_id]);
    $milestone = $m_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$milestone) {
        echo json_encode(['success' => false, 'message' => 'Milestone not found or access denied.']);
        exit;
    }

    if ($milestone['status'] !== 'pending') {
        echo json_encode(['success' => false, 'message' => 'This milestone is already ' . $milestone['status'] . '.']);
        exit;
    }

    // Update milestone status to 'submitted'
    $up_stmt = $db->prepare("UPDATE milestones SET status = 'submitted' WHERE id = ?");
    $up_stmt->execute([$milestone_id]);

    // Create notification for client
    $provider_name = $_SESSION['user_name'] ?? 'A verified professional';
    $notif_msg = "Milestone \"" . htmlspecialchars($milestone['title']) . "\" submitted for review by " . htmlspecialchars($provider_name);
    
    $notif_stmt = $db->prepare("INSERT INTO notifications (user_id, message, type, link, is_read) VALUES (?, ?, 'milestone', 'my-projects.php', 0)");
    $notif_stmt->execute([$milestone['client_id'], $notif_msg]);

    echo json_encode([
        'success' => true,
        'message' => 'Milestone submitted for review! Client has been notified.'
    ]);
    exit;

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}
