<?php
/**
 * Scriptly Centralized SMTP Configuration File
 */

define('SMTP_HOST', 'armorbullet.host');
define('SMTP_USER', 'noreply@armorbullet.host');
define('SMTP_PASS', '$Helicopter123');
define('SMTP_PORT', 587); // Standard TLS Port 587 for instant delivery
define('SMTP_SECURE', 'tls'); // 'tls'
define('SMTP_FROM_EMAIL', 'noreply@armorbullet.host');
define('SMTP_FROM_NAME', 'Cliniconnect Platform');
define('ADMIN_EMAIL', 'admin@cliniconnect.com');

if (!function_exists('logEmailLocally')) {
    function logEmailLocally($to_email, $subject, $html_body) {
        $log_dir = __DIR__ . '/../uploads/';
        if (!file_exists($log_dir)) {
            @mkdir($log_dir, 0777, true);
        }
        $log_file = $log_dir . 'mail_log.txt';
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "=========================================\n";
        $log_entry .= "Timestamp: $timestamp\n";
        $log_entry .= "To: $to_email\n";
        $log_entry .= "Subject: $subject\n";
        $log_entry .= "Body:\n$html_body\n";
        $log_entry .= "=========================================\n\n";
        @file_put_contents($log_file, $log_entry, FILE_APPEND);
    }
}
