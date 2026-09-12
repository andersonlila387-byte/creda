<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = getDBConnection();
    
    $columns = [
        'student_id' => 'VARCHAR(50) DEFAULT NULL',
        'institution' => 'VARCHAR(191) DEFAULT NULL',
        'faculty' => 'VARCHAR(191) DEFAULT NULL',
        'department' => 'VARCHAR(191) DEFAULT NULL',
        'study_level' => 'VARCHAR(191) DEFAULT NULL',
        'expected_completion_year' => 'VARCHAR(50) DEFAULT NULL',
        'bio' => 'TEXT DEFAULT NULL'
    ];
    
    foreach ($columns as $col => $definition) {
        $stmt = $db->query("SHOW COLUMNS FROM users LIKE '$col'");
        if ($stmt->rowCount() == 0) {
            $db->exec("ALTER TABLE users ADD COLUMN `$col` $definition");
            echo "Added column `$col` successfully.\n";
        } else {
            echo "Column `$col` already exists.\n";
        }
    }
    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
