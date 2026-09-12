<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once __DIR__ . '/../config/database.php';
    $db = getDBConnection();

    echo "=== ALL USERS ===\n";
    $users = $db->query("SELECT id, full_name, email, primary_role, status FROM users")->fetchAll(PDO::FETCH_ASSOC);
    print_r($users);

    echo "\n=== TALENT PROFILES ===\n";
    $profiles = $db->query("SELECT * FROM talent_profiles")->fetchAll(PDO::FETCH_ASSOC);
    print_r($profiles);
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
