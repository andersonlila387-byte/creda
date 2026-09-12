<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = getDBConnection();
    $db->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS address TEXT DEFAULT NULL AFTER phone_number;");
    echo "Successfully added 'address' column to MySQL 'users' table!\n";
} catch (\Exception $e) {
    echo "Migration Note: " . $e->getMessage() . "\n";
}
