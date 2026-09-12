<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/security.php';

enforceSecurityHeaders();

// Verify Authentication
$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['user_email'] ?? null;

if (empty($user_email)) {
    header('Location: ../login.php');
    exit;
}

// Fetch Fresh User Record from Database
$current_user = null;
try {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT id, full_name, email, primary_role, email_verified, onboarding_completed, is_verified_pro, assessment_status, assessment_score, phone_number, address, status, avatar_url, created_at, verification_status FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $user_email]);
    $current_user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$current_user || $current_user['status'] !== 'active') {
        session_destroy();
        header('Location: ../login.php?msg=suspended');
        exit;
    }

    // Verify role matches portal
    if ($current_user['primary_role'] !== 'provider') {
        header('Location: ../../app/index.php');
        exit;
    }

    if (!$current_user['email_verified']) {
        header('Location: ../verify-email.php');
        exit;
    }

    if (!$current_user['onboarding_completed']) {
        header('Location: ../onboarding.php');
        exit;
    }

} catch (\Exception $e) {
    // Fallback
}

$user_id = $current_user['id'] ?? $user_id;
$user_name = $current_user['full_name'] ?? $_SESSION['user_name'] ?? 'Provider';
$user_email = $current_user['email'] ?? $user_email;
$is_verified_pro = (bool)($current_user['is_verified_pro'] ?? false);
$assessment_status = $current_user['assessment_status'] ?? 'not_started';
$assessment_score = $current_user['assessment_score'] ?? null;
$verification_status = $current_user['verification_status'] ?? 'unverified';

// User Initials
$name_parts = explode(' ', trim($user_name));
$initials = '';
foreach (array_slice($name_parts, 0, 2) as $part) {
    $initials .= strtoupper(substr($part, 0, 1));
}
if (empty($initials)) $initials = 'PR';

// Page Title & Active Tab fallback
$page_title = $page_title ?? 'Provider Workspace';
$active_tab = $active_tab ?? 'dashboard';

// Role Guard: Only providers and admins can access provider dashboard pages.
$current_script = basename($_SERVER['PHP_SELF']);
$user_role = $current_user['primary_role'] ?? $_SESSION['user_role'] ?? 'client';

if ($user_role !== 'provider' && $user_role !== 'admin') {
    header('Location: ../../app/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-[#EFF2F7]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> — Scriptly Pro Workspace</title>
    
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Google Fonts: Space Grotesk & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Local Tailwind CSS -->
    <link rel="stylesheet" href="../../assets/css/tailwind.min.css">
    
    <!-- Scriptly Custom Alerts & Toast Stylesheet -->
    <link rel="stylesheet" href="../../assets/css/scriptly-alerts.css">
    
    <style>
        body {
            font-family: 'Inter', 'Space Grotesk', system-ui, -apple-system, sans-serif;
            background-color: #EFF2F7;
            color: #0E131F;
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .blob-cluster {
            filter: drop-shadow(0 20px 30px rgba(25, 82, 225, 0.22));
        }
        .blob-ball {
            background: radial-gradient(circle at 35% 35%, #93C5FD 0%, #3B82F6 45%, #1952E1 85%, #173DB5 100%);
            box-shadow: inset -6px -6px 12px rgba(0, 0, 0, 0.25), inset 6px 6px 14px rgba(255, 255, 255, 0.6);
        }

        .drawer-slide-up {
            transition: transform 0.35s cubic-bezier(0.32, 0.72, 0, 1), opacity 0.25s ease;
        }
    </style>
</head>
<body class="min-h-screen h-full bg-[#EFF2F7] flex antialiased selection:bg-blue-200 selection:text-blue-900 m-0 p-0 overflow-x-hidden" style="background-color: #EFF2F7;">

    <!-- Instantly Rendered Preloader -->
    <div id="page-preloader" style="position: fixed; inset: 0; background: #EFF2F7; z-index: 99999; display: flex; align-items: center; justify-content: center; transition: opacity 0.3s ease; opacity: 1;">
        <!-- Fancy Orb Spinner -->
        <div style="position: relative; width: 64px; height: 64px; display: flex; align-items: center; justify-content: center; background: white; border-radius: 50%; border: 1px solid rgba(0, 0, 0, 0.05); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);">
            <!-- Orbit ring in brand blue color only -->
            <div style="position: absolute; inset: -4px; border: 3px solid transparent; border-top-color: #1952E1; border-radius: 50%; animation: preloader-orbit 1s linear infinite;"></div>
            <!-- Pulsing Core -->
            <div style="width: 32px; height: 32px; background: #0A2342; border-radius: 50%; display: flex; align-items: center; justify-content: center; animation: preloader-pulse 1.4s ease-in-out infinite;">
                <svg style="width: 16px; height: 16px; color: white;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18.178 8c5.096 0 5.096 8 0 8-2.69 0-4.7-2.115-6.178-4-1.478-1.885-3.488-4-6.178-4-5.096 0-5.096 8 0 8 2.69 0 4.7-2.115 6.178-4 1.478-1.885-3.488-4 6.178-4z"></path>
                </svg>
            </div>
        </div>
    </div>
    <style>
        @keyframes preloader-orbit { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @keyframes preloader-pulse { 0%, 100% { transform: scale(1); opacity: 0.95; } 50% { transform: scale(1.15); opacity: 1; } }
    </style>
