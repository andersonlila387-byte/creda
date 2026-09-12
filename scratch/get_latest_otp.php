<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT email, otp_code, expires_at, created_at FROM verification_otps WHERE email = :email ORDER BY id DESC LIMIT 5");
    $stmt->execute([':email' => 'jaytech101d@gmail.com']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Latest Verification OTP Codes in DB for jaytech101d@gmail.com:\n";
    print_r($rows);
} catch (\Exception $e) {
    echo "DB Error: " . $e->getMessage();
}
