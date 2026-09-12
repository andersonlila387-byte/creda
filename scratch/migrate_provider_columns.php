<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = getDBConnection();
    
    // Check if columns exist
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'is_verified_pro'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN `is_verified_pro` TINYINT(1) NOT NULL DEFAULT 0 AFTER `onboarding_completed`");
        echo "Added is_verified_pro column.\n";
    }

    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'assessment_status'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN `assessment_status` ENUM('not_started', 'in_progress', 'passed', 'failed') NOT NULL DEFAULT 'not_started' AFTER `is_verified_pro`");
        echo "Added assessment_status column.\n";
    }

    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'assessment_score'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN `assessment_score` INT DEFAULT NULL AFTER `assessment_status`");
        echo "Added assessment_score column.\n";
    }

    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'avatar_url'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN `avatar_url` VARCHAR(255) DEFAULT NULL AFTER `assessment_score`");
        echo "Added avatar_url column.\n";
    }

    echo "MIGRATION_SUCCESSFUL\n";
} catch (\Exception $e) {
    echo "MIGRATION_ERROR: " . $e->getMessage() . "\n";
}
