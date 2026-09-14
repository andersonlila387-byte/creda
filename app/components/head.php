<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../config/routing.php';

if (!defined('ROUTER_BUFFER_STARTED')) {
    define('ROUTER_BUFFER_STARTED', true);
    ob_start('ob_route_rewrite');
}

enforceSecurityHeaders();

// Verify Authentication
$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['user_email'] ?? null;

if (empty($user_email)) {
    header('Location: ../login');
    exit;
}

// Fetch Fresh User Record from Database
$current_user = null;
try {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT id, full_name, email, primary_role, email_verified, onboarding_completed, phone_number, address, status, created_at FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $user_email]);
    $current_user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$current_user || $current_user['status'] !== 'active') {
        session_destroy();
        header('Location: ../login?msg=suspended');
        exit;
    }

    if (!$current_user['email_verified']) {
        header('Location: ../verify-email');
        exit;
    }

    // Strict Role Guard: Providers can only access client dashboard if they explicitly switched to 'client' mode.
    // Otherwise, redirect them to the provider workspace.
    $db_role = $current_user['primary_role'] ?? $_SESSION['user_role'] ?? 'client';
    if ($db_role === 'provider' && ($_SESSION['active_role_mode'] ?? '') !== 'client') {
        if (!empty($current_user['is_verified_pro'])) {
            header("Location: ../provider/app/index.php");
        } else {
            if (($current_user['assessment_status'] ?? '') === 'passed') {
                header("Location: ../provider/app/pending-verification.php");
            } else {
                header("Location: ../provider/app/assessment.php");
            }
        }
        exit;
    }

    if (!$current_user['onboarding_completed']) {
        header('Location: ../onboarding');
        exit;
    }

} catch (\Exception $e) {
    // Fallback on session
}

// Global User Properties
$user_id = $current_user['id'] ?? $user_id;
$user_name = $current_user['full_name'] ?? $_SESSION['user_name'] ?? 'Member';
$user_role = $_SESSION['active_role_mode'] ?? ($current_user['primary_role'] ?? 'client'); // allows dynamic switching
$user_email = $current_user['email'] ?? $user_email;

// User Initials for Avatar Badge
$name_parts = explode(' ', trim($user_name));
$initials = '';
foreach (array_slice($name_parts, 0, 2) as $part) {
    $initials .= strtoupper(substr($part, 0, 1));
}
if (empty($initials)) $initials = 'CR';

// Page Title & Active Tab fallback
$page_title = $page_title ?? 'Dashboard';
$active_tab = $active_tab ?? 'dashboard';
$page_description = $page_description ?? 'Scriptly - Verified Student & Freelance Service Marketplace with 100% Escrow Protection.';
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base_path = strpos($_SERVER['REQUEST_URI'] ?? '', '/creda') !== false ? '/creda' : '';
$canonical_url = $protocol . "://" . $host . ($_SERVER['REQUEST_URI'] ?? '');
$og_image_url = $protocol . "://" . $host . $base_path . '/assets/hero_bg.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="<?php echo $base_path; ?>/app/">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($page_title); ?> — Scriptly</title>
    
    <!-- Primary SEO & Share Meta Tags -->
    <meta name="title" content="<?php echo htmlspecialchars($page_title); ?> — Scriptly">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="robots" content="noindex, nofollow">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta name="theme-color" content="#1952E1">
    
    <!-- Favicon / Site Icon -->
    <link rel="icon" type="image/png" href="<?php echo getLogoIconUrl() ?: ($base_path . '/assets/brand/logo-icon.png'); ?>">

    <!-- Open Graph / WhatsApp / Facebook Share Preview -->
    <meta property="og:site_name" content="Scriptly">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?> — Scriptly">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($og_image_url); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@ScriptlyApp">
    <meta name="twitter:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?> — Scriptly">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($og_image_url); ?>">
    
    <!-- Google Fonts: Space Grotesk -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Local Production Tailwind CSS Bundle (No CDN) -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/tailwind.min.css">
    
    <!-- Scriptly Custom Alerts & Toast Stylesheet -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/scriptly-alerts.css">

    <style>
        /* General Ultra-Slim Workspace Scrollbar */
        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        * {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        /* Hidden Scrollbar Utility (Scrollable without visible bar) */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none; /* Chrome, Safari and Opera */
        }

        /* Mobile Bottom Nav Clearance */
        @media (max-width: 767px) {
            .mobile-bottom-space {
                padding-bottom: 150px !important;
            }
        }
    </style>
</head>
<body class="bg-[#EFF2F7] text-slate-900 font-sans antialiased selection:bg-blue-200 selection:text-blue-900 overflow-hidden">
    <!-- App Wrapper: Fixed height, preventing body scroll -->
    <div class="flex h-screen w-full overflow-hidden bg-[#EFF2F7]">
