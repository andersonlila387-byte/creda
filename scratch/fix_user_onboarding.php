<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = getDBConnection();
    $stmt = $db->prepare("UPDATE users SET phone_number = '+234 801 234 5678', address = 'Victoria Island, Lagos, Nigeria', onboarding_completed = 1 WHERE email = :email");
    $stmt->execute([':email' => 'jaytech101d@gmail.com']);
    
    echo "Updated onboarding_completed = 1 for jaytech101d@gmail.com in MySQL database!\n";
} catch (\Exception $e) {
    echo "DB Error: " . $e->getMessage();
}
