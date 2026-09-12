<?php
/**
 * Scriptly / Creda - Secure Route Obfuscation & Token Engine
 * 
 * Converts plain script paths and query parameters into secure, scrambled alphanumeric tokens:
 * Example: app/talent.php?view=talent -> app/g32gkjeliwteid2wlkj3rgwliufetiuuw4giuy24
 * 
 * Provides:
 * - Deterministic / stateless reversible authenticated encryption (AES-128-CBC + HMAC)
 * - Whitelist security enforcement against unauthorized script inclusion
 * - Automatic output-buffer link rewriting for 100% template coverage
 */

if (!defined('ROUTING_SECRET_KEY')) {
    define('ROUTING_SECRET_KEY', 'Creda_SecureRouteSecretKey_2026_Xk9@mZ#8!');
}

/**
 * Whitelist of allowed client workspace scripts that can be dispatched
 */
function get_allowed_client_scripts(): array {
    return [
        'index.php',
        'talent.php',
        'my-projects.php',
        'contract-details.php',
        'provider-profile.php',
        'service-details.php',
        'post-project.php',
        'review-proposals.php',
        'messages.php',
        'wallet.php',
        'settings.php',
        'notifications.php',
        'disputes.php',
        'open-dispute.php',
        'checkout.php',
        'payment-callback.php',
        'leave-feedback.php',
        'switch-role.php',
        'logout.php',
        'documents/agreement.php',
        'documents/receipt.php',
        'documents/wallet-statement.php'
    ];
}

/**
 * Encodes a script name and optional query parameters into a scrambled route token
 *
 * @param string $script e.g. "talent.php" or "documents/receipt.php"
 * @param array $params e.g. ['view' => 'talent', 'id' => 15]
 * @return string Scrambled alphanumeric token (e.g. g32gkjeliwteid2wlkj3rgwliufetiuuw4giuy24)
 */
function encode_route_token(string $script, array $params = []): string {
    $script = trim(str_replace('\\', '/', $script), '/');
    
    // Normalize script name if prefixed with app/
    if (strpos($script, 'app/') === 0) {
        $script = substr($script, 4);
    }
    
    // Sort params for consistent deterministic token generation
    ksort($params);
    $query = http_build_query($params);
    $payload = $script . ($query !== '' ? '?' . $query : '');
    
    $key = hash('sha256', ROUTING_SECRET_KEY, true);
    $cipher = 'aes-128-cbc';
    
    // Fixed IV derived from HMAC of payload and key for deterministic caching/bookmarking
    $iv = substr(hash_hmac('md5', $payload, $key, true), 0, 16);
    
    $encrypted = openssl_encrypt($payload, $cipher, substr($key, 0, 16), OPENSSL_RAW_DATA, $iv);
    if ($encrypted === false) {
        return bin2hex(random_bytes(16));
    }
    
    // Append 6-byte HMAC authentication signature to prevent token tampering
    $hmac = substr(hash_hmac('sha256', $iv . $encrypted, $key, true), 0, 6);
    $packed = $iv . $encrypted . $hmac;
    
    // Format as URL-safe base64 string
    $token = rtrim(strtr(base64_encode($packed), '+/', '-_'), '=');
    return $token;
}

/**
 * Decodes and authenticates a scrambled route token
 *
 * @param string $token Scrambled alphanumeric token
 * @return array|null ['script' => 'talent.php', 'params' => ['view' => 'talent']] or null if invalid
 */
function decode_route_token(string $token): ?array {
    $token = trim($token);
    if (empty($token) || strlen($token) < 16) {
        return null;
    }
    
    // Restore base64 padding and characters
    $b64 = strtr($token, '-_', '+/');
    $remainder = strlen($b64) % 4;
    if ($remainder) {
        $b64 .= str_repeat('=', 4 - $remainder);
    }
    
    $packed = base64_decode($b64, true);
    if ($packed === false || strlen($packed) < 23) { // 16 IV + at least 1 byte ciphertext + 6 HMAC
        return null;
    }
    
    $key = hash('sha256', ROUTING_SECRET_KEY, true);
    $cipher = 'aes-128-cbc';
    
    $iv = substr($packed, 0, 16);
    $hmac = substr($packed, -6);
    $encrypted = substr($packed, 16, -6);
    
    // Validate HMAC signature
    $expected_hmac = substr(hash_hmac('sha256', $iv . $encrypted, $key, true), 0, 6);
    if (!hash_equals($expected_hmac, $hmac)) {
        return null;
    }
    
    $payload = openssl_decrypt($encrypted, $cipher, substr($key, 0, 16), OPENSSL_RAW_DATA, $iv);
    if ($payload === false) {
        return null;
    }
    
    $parts = explode('?', $payload, 2);
    $script = $parts[0] ?? '';
    $params = [];
    
    if (isset($parts[1])) {
        parse_str($parts[1], $params);
    }
    
    // Security check against script whitelist
    $allowed = get_allowed_client_scripts();
    if (!in_array($script, $allowed, true)) {
        return null;
    }
    
    return [
        'script' => $script,
        'params' => $params
    ];
}

/**
 * Resolves the absolute application base URL path (e.g. /creda/app/ or /app/)
 */
function get_app_base_url(): string {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $self = $_SERVER['PHP_SELF'] ?? '';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    if (strpos($uri, '/creda') !== false || strpos($self, '/creda') !== false || strpos($script, '/creda') !== false) {
        return '/creda/app/';
    }
    $doc_root = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', (realpath($_SERVER['DOCUMENT_ROOT']) ?: '')) : '';
    $project_root = str_replace('\\', '/', (realpath(__DIR__ . '/..') ?: ''));
    if ($doc_root && strpos($project_root, $doc_root) === 0) {
        $sub = trim(substr($project_root, strlen($doc_root)), '/');
        if (!empty($sub)) {
            return '/' . $sub . '/app/';
        }
    }
    return '/creda/app/';
}

/**
 * Generates an obfuscated application URL
 *
 * @param string $script e.g. "talent.php" or "my-projects.php"
 * @param array $params Optional GET query parameters
 * @param string $prefix Optional custom prefix
 * @return string Full scrambled URL e.g. "/creda/app/g32gkjeliwteid2wlkj3rgwliufetiuuw4giuy24"
 */
function route_url(string $script, array $params = [], string $prefix = ''): string {
    $token = encode_route_token($script, $params);
    if ($prefix === '') {
        $prefix = get_app_base_url();
    }
    return rtrim($prefix, '/') . '/' . $token;
}

/**
 * Shortcut alias for route_url()
 */
function app_route(string $script, array $params = [], string $prefix = ''): string {
    return route_url($script, $params, $prefix);
}

/**
 * Output buffer filter: automatically rewrites un-obfuscated script links in HTML
 * Matches: href="talent.php?view=talent" or href="my-projects.php"
 */
function ob_route_rewrite(string $html): string {
    if (empty($html)) {
        return $html;
    }
    
    try {
        $allowed = get_allowed_client_scripts();
        $scripts_pattern = implode('|', array_map(function($s) {
            return preg_quote($s, '#');
        }, $allowed));
        
        $base_url = get_app_base_url();
        
        // 1. Rewrite <a href="..."> and <form action="...">
        $pattern = '#(href|action)=["\'](?:\./)?(app/)?(' . $scripts_pattern . ')(\?[^"\']*)?["\']#i';
        
        $rewritten = preg_replace_callback($pattern, function($matches) use ($base_url) {
            $attr = $matches[1];
            $script = $matches[3];
            $query_string = isset($matches[4]) ? ltrim($matches[4], '?') : '';
            
            $params = [];
            if (!empty($query_string)) {
                parse_str($query_string, $params);
            }
            
            $token = encode_route_token($script, $params);
            return $attr . '="' . $base_url . $token . '"';
        }, $html);
        
        return $rewritten ?? $html;
    } catch (\Throwable $e) {
        // Fail-safe: Always return HTML to prevent blank screens
        return $html;
    }
}
