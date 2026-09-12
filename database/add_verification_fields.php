<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = getDBConnection();
    echo "Database connected successfully.\n";
    
    // 1. Add verification columns to 'users' table
    $columns = [
        'nin' => "VARCHAR(11) DEFAULT NULL",
        'id_card_url' => "VARCHAR(255) DEFAULT NULL",
        'selfie_url' => "VARCHAR(255) DEFAULT NULL",
        'liveness_video_url' => "VARCHAR(255) DEFAULT NULL",
        'verification_status' => "ENUM('unverified', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'unverified'",
        'verification_rejected_reason' => "TEXT DEFAULT NULL",
        'registration_ip' => "VARCHAR(45) DEFAULT NULL",
        'last_login_ip' => "VARCHAR(45) DEFAULT NULL"
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
    
    // 2. Create 'admins' table
    $db->exec("CREATE TABLE IF NOT EXISTS `admins` (
        `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(100) NOT NULL UNIQUE,
        `email` VARCHAR(191) NOT NULL UNIQUE,
        `password_hash` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Table 'admins' created or verified.\n";
    
    // 3. Seed default admin
    $admin_email = 'admin@cliniconnect.com';
    $chk_stmt = $db->prepare("SELECT id FROM admins WHERE email = ?");
    $chk_stmt->execute([$admin_email]);
    if ($chk_stmt->rowCount() == 0) {
        $password_hash = password_hash('adminpassword', PASSWORD_DEFAULT);
        $ins_stmt = $db->prepare("INSERT INTO admins (username, email, password_hash) VALUES (?, ?, ?)");
        $ins_stmt->execute(['admin', $admin_email, $password_hash]);
        echo "Default admin account seeded successfully (admin@cliniconnect.com / adminpassword).\n";
    } else {
        echo "Admin account already exists.\n";
    }
    
    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
