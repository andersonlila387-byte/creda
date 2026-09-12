<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

enforceSecurityHeaders();

// Verify Authentication
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

// Read input JSON
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$amount = isset($input['amount']) ? floatval($input['amount']) : 0;
$bank_name = trim($input['bank_name'] ?? '');
$account_number = trim($input['account_number'] ?? '');
$account_name = trim($input['account_name'] ?? '');

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid withdrawal amount.']);
    exit;
}

if (empty($bank_name) || empty($account_number) || empty($account_name)) {
    echo json_encode(['success' => false, 'message' => 'Please provide complete bank account transfer details.']);
    exit;
}

try {
    $db = getDBConnection();

    // Start transaction
    $db->beginTransaction();

    // 1. Fetch user balance and lock the row
    $u_stmt = $db->prepare("SELECT balance, email, full_name FROM users WHERE id = ? FOR UPDATE");
    $u_stmt->execute([$user_id]);
    $user = $u_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'User account not found.']);
        exit;
    }

    // Enforce bank account name matching registered full name
    $normalized_account = strtolower(preg_replace('/[^a-z0-9]/', '', $account_name));
    $normalized_user = strtolower(preg_replace('/[^a-z0-9]/', '', $user['full_name']));
    
    if ($normalized_account !== $normalized_user) {
        // Allow out of order names (e.g. "John Doe" vs "Doe John")
        $user_parts = array_filter(explode(' ', strtolower(preg_replace('/[^a-z0-9 ]/', '', $user['full_name']))));
        $account_parts = array_filter(explode(' ', strtolower(preg_replace('/[^a-z0-9 ]/', '', $account_name))));
        
        sort($user_parts);
        sort($account_parts);
        
        if (implode(' ', $user_parts) !== implode(' ', $account_parts)) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Security restriction: Your bank transfer account name must exactly match your registered full name (' . htmlspecialchars($user['full_name']) . ').']);
            exit;
        }
    }

    $available_balance = floatval($user['balance']);

    if ($amount > $available_balance) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Insufficient funds. Your available cleared balance is ₦' . number_format($available_balance) . '.']);
        exit;
    }

    // 2. Deduct balance from user
    $new_balance = $available_balance - $amount;
    $up_stmt = $db->prepare("UPDATE users SET balance = ? WHERE id = ?");
    $up_stmt->execute([$new_balance, $user_id]);

    // 3. Insert transaction
    $ref_code = 'WDL-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
    $tx_desc = "Withdrawal request to " . $bank_name . " (" . $account_number . ") for " . $account_name;
    
    $tx_stmt = $db->prepare("INSERT INTO transactions (user_id, amount, type, reference, description) VALUES (?, ?, 'withdrawal', ?, ?)");
    $tx_stmt->execute([$user_id, $amount, $ref_code, $tx_desc]);

    // 4. Create in-app notification for the provider
    $notif_msg = "Withdrawal request of ₦" . number_format($amount) . " has been submitted for bank processing. Reference: " . $ref_code;
    $notif_stmt = $db->prepare("INSERT INTO notifications (user_id, message, type, link, is_read) VALUES (?, ?, 'escrow', 'earnings.php', 0)");
    $notif_stmt->execute([$user_id, $notif_msg]);

    // Commit transaction
    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Withdrawal request submitted successfully! Your account has been updated.'
    ]);
    exit;

} catch (\Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}
