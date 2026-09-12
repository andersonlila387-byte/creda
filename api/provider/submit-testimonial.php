<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

enforceSecurityHeaders();

// Verify Request Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$name = trim($input['name'] ?? '');
$role = trim($input['role'] ?? '');
$content = trim($input['content'] ?? '');
$rating = isset($input['rating']) ? intval($input['rating']) : 5;

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Please enter your name.']);
    exit;
}

if (empty($role)) {
    echo json_encode(['success' => false, 'message' => 'Please enter your professional role or university status.']);
    exit;
}

if (empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Please drop your testimonial comment description.']);
    exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5 stars.']);
    exit;
}

try {
    $db = getDBConnection();
    
    // Choose a random Unsplash placeholder avatar URL based on gender or simply a generic professional avatar
    $random_avatars = [
        'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=120',
        'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=120',
        'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=120',
        'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=120',
        'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&q=80&w=120'
    ];
    $avatar_url = $random_avatars[array_rand($random_avatars)];

    $stmt = $db->prepare("INSERT INTO testimonials (name, role, content, rating, avatar_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $role, $content, $rating, $avatar_url]);

    echo json_encode([
        'success' => true,
        'message' => 'Your review was submitted successfully! Refreshing marquee...'
    ]);
    exit;

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}
