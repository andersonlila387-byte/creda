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

$project_id = isset($input['project_id']) ? intval($input['project_id']) : 0;
$bid_amount = isset($input['bid_amount']) ? floatval($input['bid_amount']) : 0;
$duration_days = isset($input['duration_days']) ? intval($input['duration_days']) : 0;
$cover_letter_raw = trim($input['cover_letter'] ?? '');

if ($project_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid project ID.']);
    exit;
}

if ($bid_amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid bid amount.']);
    exit;
}

if (empty($cover_letter_raw)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a proposal cover letter detailing your approach.']);
    exit;
}

try {
    $db = getDBConnection();

    // 1. Verify project exists, is open, and find the client ID
    $p_stmt = $db->prepare("SELECT client_id, title, status FROM projects WHERE id = ? LIMIT 1");
    $p_stmt->execute([$project_id]);
    $project = $p_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        echo json_encode(['success' => false, 'message' => 'Project not found.']);
        exit;
    }

    if ($project['status'] !== 'open') {
        echo json_encode(['success' => false, 'message' => 'This project is no longer open for bidding.']);
        exit;
    }

    $client_id = $project['client_id'];

    // Prevent provider from bidding on their own project
    if ($client_id == $user_id) {
        echo json_encode(['success' => false, 'message' => 'You cannot submit a proposal to your own project.']);
        exit;
    }

    // 2. Check if already bid
    $check_stmt = $db->prepare("SELECT id FROM proposals WHERE project_id = ? AND provider_id = ? LIMIT 1");
    $check_stmt->execute([$project_id, $user_id]);
    if ($check_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You have already submitted a proposal for this project.']);
        exit;
    }

    // 3. Prepend delivery days to cover letter text
    $cover_letter = "Estimated Delivery: " . $duration_days . " Days\n\n" . $cover_letter_raw;

    // 4. Insert proposal
    $ins_stmt = $db->prepare("INSERT INTO proposals (project_id, provider_id, cover_letter, bid_amount, status) VALUES (?, ?, ?, ?, 'pending')");
    $ins_stmt->execute([$project_id, $user_id, $cover_letter, $bid_amount]);

    // 5. Generate Notification for the Client
    $provider_name = $_SESSION['user_name'] ?? 'A verified professional';
    $notif_msg = htmlspecialchars($provider_name) . " submitted a proposal for \"" . htmlspecialchars($project['title']) . "\"";
    
    $notif_stmt = $db->prepare("INSERT INTO notifications (user_id, message, type, link, is_read) VALUES (?, ?, 'proposal', 'my-projects.php', 0)");
    $notif_stmt->execute([$client_id, $notif_msg]);

    echo json_encode([
        'success' => true,
        'message' => 'Proposal submitted successfully!'
    ]);
    exit;

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}
