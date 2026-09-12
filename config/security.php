<?php
/**
 * Scriptly Security & Brute Force Rate Limiting Subsystem
 */

require_once __DIR__ . '/database.php';

if (session_status() === PHP_SESSION_NONE) {
    // Share session across subdomains in production
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if (!empty($host) && !in_array($host, ['localhost', '127.0.0.1'])) {
        $host = explode(':', $host)[0];
        $parts = explode('.', $host);
        if (count($parts) >= 2) {
            $parent_domain = '.' . implode('.', array_slice($parts, -2));
            ini_set('session.cookie_domain', $parent_domain);
        }
    }
    // Configure secure session cookie defaults
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

/**
 * Get Client IP Address (supporting proxies)
 */
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    return trim($ip);
}

/**
 * Enforce Security Headers
 */
function enforceSecurityHeaders() {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

/**
 * CSRF Protection Token Helpers
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check Rate Limit Lockout for an action (IP / Email key)
 * @param string $action_key Unique key e.g. "login_user@example.com" or "login_127.0.0.1"
 * @param int $max_attempts Maximum allowed attempts before lockout (default: 5)
 * @param int $lockout_seconds Lockout duration in seconds (default: 900 = 15 mins)
 * @return array ['allowed' => bool, 'message' => string, 'retry_after' => int]
 */
function checkRateLimit($action_key, $max_attempts = 5, $lockout_seconds = 900) {
    try {
        $db = getDBConnection();
        $stmt = $db->prepare("SELECT attempts, last_attempt_at, locked_until FROM rate_limits WHERE identifier = :id LIMIT 1");
        $stmt->execute([':id' => $action_key]);
        $record = $stmt->fetch();

        if ($record && !empty($record['locked_until'])) {
            $locked_time = strtotime($record['locked_until']);
            $current_time = time();

            if ($locked_time > $current_time) {
                $remaining_seconds = $locked_time - $current_time;
                $remaining_minutes = ceil($remaining_seconds / 60);

                return [
                    'allowed' => false,
                    'retry_after' => $remaining_minutes,
                    'message' => "Too many failed attempts. For security, access is temporarily locked. Please try again in {$remaining_minutes} minute(s)."
                ];
            } else {
                // Lockout period expired, reset attempts
                clearRateLimit($action_key);
            }
        }

        return ['allowed' => true, 'message' => ''];
    } catch (\Exception $e) {
        // Fallback to allow if DB check fails
        return ['allowed' => true, 'message' => ''];
    }
}

/**
 * Record a Failed Attempt & Trigger Lockout if threshold reached
 */
function recordFailedAttempt($action_key, $max_attempts = 5, $lockout_seconds = 900) {
    try {
        $db = getDBConnection();
        $now = date('Y-m-d H:i:s');

        $stmt = $db->prepare("SELECT attempts FROM rate_limits WHERE identifier = :id LIMIT 1");
        $stmt->execute([':id' => $action_key]);
        $record = $stmt->fetch();

        if ($record) {
            $new_attempts = $record['attempts'] + 1;
            $locked_until = null;

            if ($new_attempts >= $max_attempts) {
                $locked_until = date('Y-m-d H:i:s', time() + $lockout_seconds);
            }

            $updateStmt = $db->prepare("UPDATE rate_limits SET attempts = :att, last_attempt_at = :now, locked_until = :locked WHERE identifier = :id");
            $updateStmt->execute([
                ':att' => $new_attempts,
                ':now' => $now,
                ':locked' => $locked_until,
                ':id' => $action_key
            ]);
        } else {
            $insertStmt = $db->prepare("INSERT INTO rate_limits (identifier, attempts, last_attempt_at) VALUES (:id, 1, :now)");
            $insertStmt->execute([
                ':id' => $action_key,
                ':now' => $now
            ]);
        }
    } catch (\Exception $e) {
        error_log("Failed recording rate limit: " . $e->getMessage());
    }
}

/**
 * Clear Rate Limit Counter on successful authentication
 */
function clearRateLimit($action_key) {
    try {
        $db = getDBConnection();
        $stmt = $db->prepare("DELETE FROM rate_limits WHERE identifier = :id");
        $stmt->execute([':id' => $action_key]);
    } catch (\Exception $e) {
        error_log("Failed clearing rate limit: " . $e->getMessage());
    }
}
