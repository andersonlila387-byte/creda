<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = getDBConnection();
    $stmt = $db->prepare("UPDATE users SET onboarding_completed = 0 WHERE email = :email");
    $stmt->execute([':email' => 'jaytech101d@gmail.com']);
    echo "Reset onboarding_completed = 0 for jaytech101d@gmail.com for AJAX submission testing.\n";
} catch (\Exception $e) {
    echo "DB Error: " . $e->getMessage();
}
