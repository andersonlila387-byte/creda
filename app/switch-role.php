<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/brand.php';

$role = $_GET['role'] ?? '';

// If a specific role is targetted
if ($role === 'provider') {
    $_SESSION['active_role_mode'] = 'provider';
    
    // Check if the user is a provider or client in session
    $user_role = $_SESSION['user_role'] ?? 'client';
    
    if ($user_role === 'provider' || $user_role === 'admin') {
        header('Location: ' . getPortalUrl('provider', 'app/index.php'));
    } else {
        // If they are a client, they go to provider setup / dashboard
        header('Location: ' . getPortalUrl('provider', 'app/index.php'));
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
    $_SESSION['active_role_mode'] = 'provider';
    header('Location: ' . getPortalUrl('client', 'app/switch-role.php?role=provider'));
} else {
    $_SESSION['active_role_mode'] = 'client';
    header('Location: ' . getPortalUrl('client', 'app/switch-role.php?role=client'));
}
exit;
