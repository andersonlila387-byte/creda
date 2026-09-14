<?php 
/**
 * Scriptly Escrow - Direct Hire & Custom Booking
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();

// Require Login
$client_id = $_SESSION['user_id'] ?? null;
$active_role = $_SESSION['active_role_mode'] ?? ($_SESSION['primary_role'] ?? 'client');
if (!$client_id || $active_role !== 'client') {
    header("Location: ../login.php?redirect=direct-hire.php?" . http_build_query($_GET));
    exit;
}

$provider_id = $_GET['provider_id'] ?? null;

if (!$provider_id) {
    header("Location: talent.php");
    exit;
}

// Fetch Provider Info
$sql = "SELECT u.id, u.full_name, tp.title FROM users u LEFT JOIN talent_profiles tp ON u.id = tp.user_id WHERE u.id = ? LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->execute([$provider_id]);
$provider = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$provider) {
    die("Invalid provider.");
}

// Handle Direct Booking Submission (Mock Payment)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_title = trim($_POST['title'] ?? '');
    $budget = floatval($_POST['budget'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    
    if (empty($project_title) || $budget <= 0) {
        $error = "Please provide a valid project title and budget.";
    } else {
        $fee = $budget * 0.05; // 5% escrow fee
        $total = $budget + $fee;
        
        try {
            $db->beginTransaction();
            
            // 1. Create Contract
            $stmt_c = $db->prepare("INSERT INTO contracts (client_id, provider_id, package_id, title, total_amount, status, created_at, updated_at) VALUES (?, ?, NULL, ?, ?, 'active', NOW(), NOW())");
            $stmt_c->execute([$client_id, $provider_id, $project_title, $total]);
            $contract_id = $db->lastInsertId();

            // 2. Create Escrow Transaction (Mock Funded)
            $stmt_e = $db->prepare("INSERT INTO escrow_transactions (contract_id, amount, fee_amount, status) VALUES (?, ?, ?, 'funded')");
            $stmt_e->execute([$contract_id, $budget, $fee]);
            
            // 3. Save requirements as first message
            if (!empty($description)) {
                $stmt_m = $db->prepare("INSERT INTO messages (contract_id, sender_id, receiver_id, content) VALUES (?, ?, ?, ?)");
                $stmt_m->execute([$contract_id, $client_id, $provider_id, "Project Description/Requirements: " . $description]);
            }
            
            $db->commit();
            
            // Redirect to Contract Details
            header("Location: contract-details.php?id=" . $contract_id . "&success=direct_hire");
            exit;
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $error = "Booking failed: " . $e->getMessage();
        }
    }
}

$page_title = 'Direct Hire';
$active_tab = 'talent';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/components/head.php'; ?>
    <title>Direct Hire - Scriptly</title>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col md:flex-row">

    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <?php include __DIR__ . '/components/bottom-nav.php'; ?>

    <main class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include __DIR__ . '/components/header.php'; ?>

        <div class="p-4 md:p-8 pt-20 max-w-4xl mx-auto w-full mobile-bottom-space md:pt-8 md:pb-8">
            <h1 class="text-2xl font-black text-slate-900 mb-6">Direct Hire Offer</h1>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-[3px] border border-red-200 mb-6 text-sm font-bold">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Left: Booking Form -->
                <div class="flex-1 bg-white border border-slate-200 rounded-[3px] p-6 shadow-sm">
                    <h2 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3 uppercase tracking-wider mb-6">Project Details</h2>
                    
                    <form method="POST" id="checkout-form" class="space-y-4">
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Project Title</label>
                            <input type="text" name="title" required placeholder="e.g. Develop custom API integration" class="w-full border border-slate-300 rounded-[3px] px-3 py-2 text-sm focus:border-[#1952E1] focus:ring-1 focus:ring-[#1952E1] outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Project Description & Requirements</label>
                            <textarea name="description" rows="4" placeholder="Describe the deliverables, timeline, and any specific requirements..." class="w-full border border-slate-300 rounded-[3px] px-3 py-2 text-sm focus:border-[#1952E1] focus:ring-1 focus:ring-[#1952E1] outline-none"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Total Budget (₦)</label>
                            <input type="number" name="budget" id="budget-input" required min="1000" step="1000" placeholder="50000" class="w-full border border-slate-300 rounded-[3px] px-3 py-2 text-sm focus:border-[#1952E1] focus:ring-1 focus:ring-[#1952E1] outline-none">
                        </div>

                        <div class="bg-blue-50/50 border border-blue-200 rounded-[3px] p-4 flex items-start gap-4 mt-6 mb-6">
                            <div class="w-10 h-10 bg-white border border-blue-200 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-[#1952E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Paystack Secure Gateway</h3>
                                <p class="text-xs text-slate-500 mt-1">Your funds will be held securely in Scriptly Escrow until you approve the provider's final delivery.</p>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-4 bg-[#1952E1] hover:bg-blue-700 text-white font-black text-sm rounded-[3px] transition-colors shadow-sm">
                            Fund Escrow & Send Contract
                        </button>
                    </form>
                </div>

                <!-- Right: Provider Summary -->
                <div class="w-full lg:w-80 shrink-0">
                    <div class="bg-white border border-slate-200 rounded-[3px] shadow-sm overflow-hidden sticky top-6">
                        <div class="bg-slate-50 border-b border-slate-200 p-4">
                            <h2 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Hiring Professional</h2>
                        </div>
                        <div class="p-4 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 border border-slate-200 text-lg font-black text-slate-700">
                                <?= substr($provider['full_name'], 0, 1) ?>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 leading-snug"><?php echo htmlspecialchars($provider['full_name']); ?></h3>
                            <p class="text-[11px] text-slate-500 mt-1 font-semibold"><?php echo htmlspecialchars($provider['title']); ?></p>
                        </div>
                        
                        <div class="border-t border-slate-100 pt-4 p-4 space-y-2 text-sm bg-slate-50">
                            <div class="flex justify-between text-slate-600">
                                <span>Subtotal</span>
                                <span id="summary-subtotal">₦0.00</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Escrow Fee (5%)</span>
                                <span id="summary-fee">₦0.00</span>
                            </div>
                        </div>
                        <div class="bg-slate-100 border-t border-slate-200 p-4">
                            <div class="flex justify-between items-center">
                                <span class="font-extrabold text-slate-900 text-sm">Total</span>
                                <span class="font-black text-slate-900 text-lg" id="summary-total">₦0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php include __DIR__ . '/components/footer.php'; ?>
    </main>

    <script>
        const budgetInput = document.getElementById('budget-input');
        const subtotalEl = document.getElementById('summary-subtotal');
        const feeEl = document.getElementById('summary-fee');
        const totalEl = document.getElementById('summary-total');

        if (budgetInput) {
            budgetInput.addEventListener('input', function() {
                const val = parseFloat(this.value) || 0;
                const fee = val * 0.05;
                const total = val + fee;

                subtotalEl.innerText = '₦' + val.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                feeEl.innerText = '₦' + fee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                totalEl.innerText = '₦' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            });
        }
    </script>
</body>
</html>
