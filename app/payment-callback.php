<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();
$user_id = $_SESSION['user_id'] ?? 1;

$reference = $_GET['reference'] ?? '';

if (!empty($reference)) {
    $db->beginTransaction();
    try {
        // Double-check if reference exists
        $chk = $db->prepare("SELECT COUNT(*) FROM transactions WHERE description LIKE ?");
        $chk->execute(["%Ref: $reference%"]);
        
        if ($chk->fetchColumn() == 0) {
            // Mock dynamic deposit verification (defaulting to 50k credit sandbox)
            $amount = 50000.00;
            
            $up = $db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $up->execute([$amount, $user_id]);
            
            $tx = $db->prepare("INSERT INTO transactions (user_id, amount, transaction_type, description) VALUES (?, ?, 'deposit', ?)");
            $tx->execute([$user_id, $amount, "Direct Escrow Deposit (Ref: $reference)"]);
        }
        $db->commit();
        header('Location: wallet.php?success=payment');
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        header('Location: wallet.php?error=verification_failed');
        exit;
    }
} else {
    header('Location: wallet.php');
    exit;
}
