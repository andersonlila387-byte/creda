<?php
/**
 * Scriptly / Creda - Central Route Dispatcher & Front Controller
 * 
 * Intercepts all scrambled token requests under /app/<token>, decodes the target script,
 * verifies whitelist integrity, and safely executes the intended application view.
 * 
 * Also intercepts direct .php URL access attempts and automatically canonicalizes them
 * into scrambled token URLs.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../config/routing.php';

// 1. Intercept Direct .php URL Access Attempts
if (isset($_GET['__direct_block'])) {
    $raw_script = basename($_GET['__direct_block']);
    if (substr($raw_script, -4) !== '.php') {
        $raw_script .= '.php';
    }
    
    // Copy GET parameters except the internal redirect marker
    $params = $_GET;
    unset($params['__direct_block']);
    
    // Check if script is an allowed client script
    $allowed = get_allowed_client_scripts();
    if (in_array($raw_script, $allowed, true)) {
        $scrambled_url = route_url($raw_script, $params);
        header("Location: " . $scrambled_url, true, 302);
        exit;
    } else {
        // Disallowed script: redirect to main dashboard
        $dashboard_url = route_url('index.php');
        header("Location: " . $dashboard_url, true, 302);
        exit;
    }
}

// 2. Process Scrambled Route Token
$token = $_GET['__token'] ?? '';

// Fallback for bare /app/ or /app/index.php access
if (empty($token)) {
    $dashboard_url = route_url('index.php');
    header("Location: " . $dashboard_url, true, 302);
    exit;
}

$route = decode_route_token($token);

if (!$route || empty($route['script'])) {
    // Invalid or tampered token: safely fall back to dashboard
    $dashboard_url = route_url('index.php');
    header("Location: " . $dashboard_url, true, 302);
    exit;
}

$target_script = $route['script'];
$target_file = __DIR__ . '/' . $target_script;

if (!file_exists($target_file)) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1><p>The requested application module does not exist.</p>";
    exit;
}

// 3. Populate Environment Parameters
// Merge decoded parameters into $_GET and $_REQUEST
if (!empty($route['params']) && is_array($route['params'])) {
    foreach ($route['params'] as $key => $val) {
        $_GET[$key] = $val;
        $_REQUEST[$key] = $val;
    }
}
unset($_GET['__token']);
unset($_REQUEST['__token']);

// Expose routing context
define('ROUTER_DISPATCHED', true);
define('CURRENT_ROUTE_TOKEN', $token);
define('CURRENT_SCRIPT_NAME', $target_script);

// 4. Start Output Buffering with Automated Route Rewriting Filter
define('ROUTER_BUFFER_STARTED', true);
ob_start('ob_route_rewrite');

// 5. Execute Target View Script
require $target_file;

// Flush rewritten output buffer if still active
if (ob_get_level() > 0) {
    ob_end_flush();
}
