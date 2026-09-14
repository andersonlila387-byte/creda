<?php 
/**
 * Scriptly Escrow - Checkout & Escrow Funding
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();

// Require Login
$client_id = $_SESSION['user_id'] ?? null;
if (!$client_id || ($_SESSION['primary_role'] ?? 'client') !== 'client') {
    header("Location: ../login.php?redirect=checkout.php?" . http_build_query($_GET));
    exit;
}

$package_id = $_GET['package_id'] ?? null;
$tier_type = $_GET['tier'] ?? null;

if (!$package_id || !$tier_type) {
    header("Location: services.php");
    exit;
}

// Fetch Package & Tier Info
$sql = "
    SELECT p.id as package_id, p.title as package_title, p.provider_id, u.full_name as provider_name,
           t.name as tier_name, t.price, t.delivery_days, t.revisions, t.description
    FROM packages p
    JOIN package_pricing_tiers t ON p.id = t.package_id
    JOIN users u ON p.provider_id = u.id
    WHERE p.id = ? AND t.tier_type = ? AND p.status = 'active'
";
$stmt = $db->prepare($sql);
$stmt->execute([$package_id, $tier_type]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Invalid package or tier.");
}

$fee = $order['price'] * 0.05; // 5% escrow fee
$total = $order['price'] + $fee;

// Handle Checkout Submission (Mock Payment)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();
        
        // 1. Create Contract
        $contract_title = $order['package_title'] . " (" . ucfirst($tier_type) . " Tier)";
        $stmt_c = $db->prepare("INSERT INTO contracts (client_id, provider_id, package_id, title, total_amount, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 'awaiting_requirements', NOW(), NOW())");
        $stmt_c->execute([$client_id, $order['provider_id'], $package_id, $contract_title, $total]);
        $contract_id = $db->lastInsertId();

        // 2. Create Escrow Transaction (Mock Funded)
        $stmt_e = $db->prepare("INSERT INTO escrow_transactions (contract_id, amount, fee_amount, status) VALUES (?, ?, ?, 'funded')");
        $stmt_e->execute([$contract_id, $order['price'], $fee]);
        
        $db->commit();
        
        // Redirect to Contract Details
        header("Location: contract-details.php?id=" . $contract_id . "&success=checkout");
        exit;
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $error = "Payment failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/components/head.php'; ?>
    <title>Secure Checkout - Scriptly</title>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col md:flex-row">

    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <?php include __DIR__ . '/components/bottom-nav.php'; ?>

    <main class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include __DIR__ . '/components/header.php'; ?>

        <div class="p-4 md:p-8 pt-20 pb-36 md:pt-8 md:pb-8 max-w-4xl mx-auto w-full">
            <h1 class="text-2xl font-black text-slate-900 mb-6">Complete Your Order</h1>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-[3px] border border-red-200 mb-6 text-sm font-bold">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Left: Payment details -->
                <div class="flex-1 bg-white border border-slate-200 rounded-[3px] p-6 shadow-sm">
                    <h2 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3 uppercase tracking-wider mb-6">Payment Method</h2>
                    
                    <div class="bg-blue-50/50 border border-blue-200 rounded-[3px] p-4 flex items-start gap-4 mb-8">
                        <div class="w-10 h-10 bg-white border border-blue-200 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-[#1952E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Paystack Secure Gateway</h3>
                            <p class="text-xs text-slate-500 mt-1">Your funds will be held securely in Scriptly Escrow until you approve the provider's final delivery.</p>
                        </div>
                    </div>

                    <form method="POST" id="checkout-form">
                        <button type="submit" class="w-full py-4 bg-[#1952E1] hover:bg-blue-700 text-white font-black text-sm rounded-[3px] transition-colors shadow-sm">
                            Fund Escrow & Place Order (₦<?php echo number_format($total); ?>)
                        </button>
                        <p class="text-[10px] text-center text-slate-400 font-bold uppercase mt-3">You won't be charged yet (MVP Demo)</p>
                    </form>
                </div>

                <!-- Right: Order Summary -->
                <div class="w-full lg:w-80 shrink-0">
                    <div class="bg-white border border-slate-200 rounded-[3px] shadow-sm overflow-hidden">
                        <div class="bg-slate-50 border-b border-slate-200 p-4">
                            <h2 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Order Summary</h2>
                        </div>
                        <div class="p-4 space-y-4">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 leading-snug"><?php echo htmlspecialchars($order['package_title']); ?></h3>
                                <p class="text-xs text-slate-500 mt-1">Provider: <?php echo htmlspecialchars($order['provider_name']); ?></p>
                            </div>
                            
                            <div class="border-t border-slate-100 pt-4">
                                <div class="flex justify-between items-center text-sm mb-2">
                                    <span class="font-bold text-slate-700"><?php echo htmlspecialchars($order['tier_name']); ?> Tier</span>
                                    <span class="font-bold text-slate-900">₦<?php echo number_format($order['price']); ?></span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] text-slate-500 font-semibold mb-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <?php echo $order['delivery_days']; ?> Days Delivery
                                </div>
                                <div class="flex items-center gap-2 text-[11px] text-slate-500 font-semibold">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    <?php echo $order['revisions'] == -1 ? 'Unlimited' : $order['revisions']; ?> Revisions
                                </div>
                            </div>

                            <div class="border-t border-slate-100 pt-4 space-y-2 text-sm">
                                <div class="flex justify-between text-slate-600">
                                    <span>Subtotal</span>
                                    <span>₦<?php echo number_format($order['price']); ?></span>
                                </div>
                                <div class="flex justify-between text-slate-600">
                                    <span>Escrow Fee (5%)</span>
                                    <span>₦<?php echo number_format($fee); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 border-t border-slate-200 p-4">
                            <div class="flex justify-between items-center">
                                <span class="font-extrabold text-slate-900 text-sm">Total</span>
                                <span class="font-black text-slate-900 text-lg">₦<?php echo number_format($total); ?></span>
                            </div>
                            <div class="text-[10px] text-slate-400 text-right mt-1 font-bold">Delivery Time: <?php echo $order['delivery_days']; ?> Days</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php include __DIR__ . '/components/footer.php'; ?>
    </main>
</body>
</html>

