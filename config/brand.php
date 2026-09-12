<?php
/**
 * Scriptly Brand & Asset Configuration Helper
 * Centralizes the logo, logo icon, site icon/favicon rendering, and SEO tags.
 */

define('BRAND_LOGO_PATH', __DIR__ . '/../assets/brand/logo.png');
define('BRAND_ICON_PATH', __DIR__ . '/../assets/brand/logo-icon.png');

function getLogoIconUrl() {
    $base_path = (strpos($_SERVER['REQUEST_URI'] ?? '', '/creda') !== false ? '/creda' : '');
    // Check if the uploaded brand icon exists on disk
    if (file_exists(BRAND_ICON_PATH)) {
        return $base_path . '/assets/brand/logo-icon.png';
    }
    // Return null if not uploaded yet (will fallback to SVG icon)
    return null;
}

function getLogoFullUrl() {
    $base_path = (strpos($_SERVER['REQUEST_URI'] ?? '', '/creda') !== false ? '/creda' : '');
    // Check if the uploaded full logo image exists on disk
    if (file_exists(BRAND_LOGO_PATH)) {
        return $base_path . '/assets/brand/logo.png';
    }
    // Return null if not uploaded yet (will fallback to SVG + Text logo)
    return null;
}

function renderLogoIcon($class = 'w-6 h-6') {
    $url = getLogoIconUrl();
    if ($url) {
        return '<img src="' . htmlspecialchars($url) . '" class="' . htmlspecialchars($class) . ' object-contain" alt="Scriptly Icon">';
    }
    // Fallback to SVG Infinity Icon
    return '
    <svg class="' . htmlspecialchars($class) . ' text-[#1952E1] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18.178 8c5.096 0 5.096 8 0 8-2.69 0-4.7-2.115-6.178-4-1.478-1.885-3.488-4-6.178-4-5.096 0-5.096 8 0 8 2.69 0 4.7-2.115 6.178-4 1.478-1.885-3.488-4 6.178-4z"></path>
    </svg>';
}

function renderLogoFull($containerClass = 'flex items-center space-x-2 text-2xl font-extrabold tracking-tight text-slate-900', $iconClass = 'w-6 h-6') {
    $fullLogoUrl = getLogoFullUrl();
    if ($fullLogoUrl) {
        return '
        <div class="' . htmlspecialchars($containerClass) . '">
            <img src="' . htmlspecialchars($fullLogoUrl) . '" class="h-8 w-auto object-contain" alt="Scriptly Logo">
        </div>';
    }
    
    // Otherwise, render Logo Icon + "Scriptly" Text + Verified Tick Badge
    $icon = renderLogoIcon($iconClass);
    return '
    <div class="' . htmlspecialchars($containerClass) . '">
        ' . $icon . '
        <span class="font-sans font-extrabold tracking-tight">Scriptly</span>
        <span class="inline-flex items-center justify-center w-4 h-4 bg-blue-600 text-white rounded-full text-[9px] font-bold font-sans shrink-0 select-none">✓</span>
    </div>';
}

function getPortalUrl($portal, $path = '') {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    
    // Check if we are running locally in XAMPP subfolders
    $is_local_subfolder = (strpos($_SERVER['REQUEST_URI'] ?? '', '/creda') !== false || strpos($_SERVER['SCRIPT_NAME'] ?? '', '/creda') !== false);
    
    if ($is_local_subfolder) {
        $base = $protocol . $host . '/creda';
        if ($portal === 'provider') {
            return $base . '/provider/' . $path;
        } elseif ($portal === 'admin') {
            return $base . '/admin/' . $path;
        } else {
            return $base . '/' . $path;
        }
    } else {
        // Subdomain-based routing in production
        // Resolve parent domain e.g., cliniconnect.com from provider.cliniconnect.com
        $host_parts = explode(':', $host)[0];
        $parts = explode('.', $host_parts);
        if (count($parts) >= 2) {
            $parent_domain = implode('.', array_slice($parts, -2));
        } else {
            $parent_domain = $host_parts;
        }
        
        if ($portal === 'provider') {
            return $protocol . 'provider.' . $parent_domain . '/' . $path;
        } elseif ($portal === 'admin') {
            return $protocol . 'admin.' . $parent_domain . '/' . $path;
        } else {
            return $protocol . $parent_domain . '/' . $path;
        }
    }
}

