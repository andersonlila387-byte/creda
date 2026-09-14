<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/brand.php';

$role = $_GET['role'] ?? '';

// If a specific role is targetted
if ($role === 'provider') {
    $_SESSION['active_role_mode'] = 'provider';
    
    // Check if the user is already a registered provider in the database
    $user_id = $_SESSION['user_id'] ?? null;
    $is_provider = false;
    
    if ($user_id) {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("SELECT primary_role, is_verified_pro, assessment_status FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([$user_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && $row['primary_role'] === 'provider') {
                $is_provider = true;
            }
        } catch (Exception $e) {
            $is_provider = false;
        }
    }
    
    if ($is_provider) {
        header('Location: ' . getPortalUrl('provider', 'app/index.php'));
    } else {
        // If they are not yet a provider, take them to the provider signup page
        header('Location: ' . getPortalUrl('provider', 'signup.php'));
    }
    exit;
}

if ($role === 'client') {
    $_SESSION['active_role_mode'] = 'client';
    header('Location: ' . getPortalUrl('client', 'app/index.php'));
    exit;
}

// Toggle logic (fallback)
$current_mode = $_SESSION['active_role_mode'] ?? $_SESSION['user_role'] ?? 'client';
if ($current_mode === 'client') {
    header('Location: ' . getPortalUrl('client', 'app/switch-role.php?role=provider'));
} else {
    header('Location: ' . getPortalUrl('client', 'app/switch-role.php?role=client'));
}
exit;
