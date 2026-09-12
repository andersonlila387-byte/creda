<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();
$user_id = $_SESSION['user_id'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fund_wallet') {
    header('Content-Type: application/json');
    $amount = (float)($_POST['amount'] ?? 0);
    if ($amount > 0) {
        $db->beginTransaction();
        try {
            // Update balance
            $up = $db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $up->execute([$amount, $user_id]);
            
            // Insert transaction
            $tx_in = $db->prepare("INSERT INTO transactions (user_id, amount, type, reference, description) VALUES (?, ?, 'deposit', ?, 'Wallet Top-Up via Paystack')");
            $ref = 'pay_inline_' . time();
            $tx_in->execute([$user_id, $amount, $ref]);
            
            $db->commit();
            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid amount']);
        exit;
    }
}

// Resolve Logged-in User Info
$me_stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$me_stmt->execute([$user_id]);
$me_info = $me_stmt->fetch(PDO::FETCH_ASSOC);
$balance = (float)($me_info['balance'] ?? 0.00);

// Calculate Locked Escrow sum
$esc_stmt = $db->prepare("
    SELECT SUM(m.amount) 
    FROM milestones m
    JOIN projects p ON m.project_id = p.id
    WHERE p.client_id = ? AND m.status IN ('pending', 'submitted', 'approved')
");
$esc_stmt->execute([$user_id]);
$locked_escrow = (float)$esc_stmt->fetchColumn();

// Calculate Lifetime Disbursed
$dis_stmt = $db->prepare("
    SELECT SUM(m.amount) 
    FROM milestones m
    JOIN projects p ON m.project_id = p.id
    WHERE p.client_id = ? AND m.status = 'paid'
");
$dis_stmt->execute([$user_id]);
$lifetime_disbursed = (float)$dis_stmt->fetchColumn();

// Load transactions list
$t_stmt = $db->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
$t_stmt->execute([$user_id]);
$transactions = $t_stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate transaction type counts
$all_tx_count = count($transactions);
$deposit_tx_count = 0;
$release_tx_count = 0;
$refund_tx_count = 0;

foreach ($transactions as $tx) {
    if ($tx['type'] === 'deposit') {
        $deposit_tx_count++;
    } elseif ($tx['type'] === 'escrow_lock' || $tx['type'] === 'escrow_release' || $tx['type'] === 'withdrawal') {
        $release_tx_count++;
    } elseif ($tx['type'] === 'escrow_refund') {
        $refund_tx_count++;
    }
}

// Load Active Project Escrow Reserves list
$res_stmt = $db->prepare("
    SELECT p.id, p.title, p.category, u.full_name as provider_name,
           COALESCE((SELECT SUM(amount) FROM milestones WHERE project_id = p.id AND status IN ('pending', 'submitted', 'approved')), 0) as locked_amount,
           COALESCE((SELECT SUM(amount) FROM milestones WHERE project_id = p.id), 0) as total_contract_amount
    FROM projects p
    LEFT JOIN proposals pr ON pr.project_id = p.id AND pr.status = 'accepted'
    LEFT JOIN users u ON u.id = pr.provider_id
    WHERE p.client_id = ? AND p.status = 'in_progress'
");
$res_stmt->execute([$user_id]);
$active_reserves = $res_stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Escrow Wallet';
$active_tab = 'wallet';
require_once __DIR__ . '/components/head.php'; 
?>

<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Layout Area -->
<main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-hidden relative bg-[#EFF2F7]">
    
    <?php include __DIR__ . '/components/header.php'; ?>

    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 pb-24 md:pb-6 lg:pb-12 scroll-smooth">
        
        <div class="max-w-7xl mx-auto space-y-5 sm:space-y-6">

            <!-- PAGE TITLE & PRIMARY ACTIONS -->
            <div class="bg-white p-4 sm:p-6 rounded-[3px] border border-slate-200/90 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1.5 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-lg sm:text-2xl font-black text-slate-900 tracking-tight">Escrow Wallet & Financials</h1>
                        <span class="bg-emerald-50 text-emerald-800 border border-emerald-200/80 text-[11px] font-bold px-2.5 py-0.5 rounded-[3px] inline-flex items-center gap-1.5 whitespace-nowrap shadow-2xs">
                            <i class="ph-fill ph-shield-check text-emerald-600 text-sm"></i>
                            <span>100% Escrow Protected</span>
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-2xl leading-relaxed">
                        Manage platform funds, monitor active milestone escrow reserves, and review automated disbursal invoices.
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 sm:gap-2.5 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                    <button type="button" onclick="window.open('documents/wallet-statement.php', '_blank');" class="inline-flex items-center justify-center gap-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs px-3.5 py-2.5 rounded-[3px] transition-all hover:border-slate-300">
                        <i class="ph-bold ph-download-simple text-sm text-slate-500"></i>
                        <span>Statement PDF</span>
                    </button>
                    <button type="button" id="open-deposit-modal" class="inline-flex items-center justify-center gap-1.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-[3px] transition-all shadow-sm">
                        <i class="ph-bold ph-plus-circle text-base"></i>
                        <span>Deposit Funds</span>
                    </button>
                </div>
            </div>

            <!-- 3 FINANCIAL KPI OVERVIEW CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                
                <!-- CARD 1: Locked in Active Escrow -->
                <div class="bg-white p-4 sm:p-5 rounded-[3px] border border-slate-200/90 shadow-sm space-y-3 relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Locked in Escrow</span>
                        <span class="w-8 h-8 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center text-base">
                            <i class="ph-bold ph-lock-key"></i>
                        </span>
                    </div>
                    <div>
                        <span class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight block">₦<?= number_format($locked_escrow) ?></span>
                        <div class="flex items-center gap-1.5 mt-1 text-[11px] text-slate-500">
                            <span class="font-bold text-[#1952E1]"><?= count($active_reserves) ?> Active Contracts</span>
                            <span>•</span>
                            <span>Pledged to Milestones</span>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-600">
                        <span>Protected by 7-Day Review</span>
                        <span class="text-emerald-700 font-bold">100% Safe</span>
                    </div>
                </div>

                <!-- CARD 2: Available Wallet Balance -->
                <div class="bg-white p-4 sm:p-5 rounded-[3px] border border-slate-200/90 shadow-sm space-y-3 relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Available Balance</span>
                        <span class="w-8 h-8 rounded-[3px] bg-emerald-50 text-emerald-700 flex items-center justify-center text-base">
                            <i class="ph-bold ph-wallet"></i>
                        </span>
                    </div>
                    <div>
                        <span class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight block">₦<?= number_format($balance) ?></span>
                        <div class="flex items-center gap-1.5 mt-1 text-[11px] text-slate-500">
                            <span class="font-bold text-emerald-700">Unallocated Funds</span>
                            <span>•</span>
                            <span>Ready for Escrow</span>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-600">
                        <span>Instant Escrow Funding</span>
                        <a href="javascript:void(0)" onclick="alert('Refund request submitted.');" class="text-[#1952E1] font-bold hover:underline">Withdraw / Refund</a>
                    </div>
                </div>

                <!-- CARD 3: Total Platform Disbursed -->
                <div class="bg-white p-4 sm:p-5 rounded-[3px] border border-slate-200/90 shadow-sm space-y-3 relative overflow-hidden sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Lifetime Disbursed</span>
                        <span class="w-8 h-8 rounded-[3px] bg-slate-100 text-slate-700 flex items-center justify-center text-base">
                            <i class="ph-bold ph-check-circle"></i>
                        </span>
                    </div>
                    <div>
                        <span class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight block">₦<?= number_format($lifetime_disbursed) ?></span>
                        <div class="flex items-center gap-1.5 mt-1 text-[11px] text-slate-500">
                            <span class="font-bold text-slate-800">Escrow Disbursed</span>
                            <span>•</span>
                            <span>Verified Deliverables</span>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-600">
                        <span>All Invoices Verified</span>
                        <span class="text-slate-500 font-bold">14 Invoices</span>
                    </div>
                </div>

            </div>

            <!-- ACTIVE ESCROW RESERVES BREAKDOWN -->
            <div class="bg-white rounded-[3px] border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                            <i class="ph-bold ph-shield-check text-[#1952E1] text-base"></i>
                            Active Project Escrow Reserves
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Live breakdown of milestone deposits locked per active project contract.</p>
                    </div>
                    <span class="text-xs font-bold text-slate-500">3 Funded Projects</span>
                </div>

                <!-- Reserves Table (Desktop Table / Mobile Stacked Cards) -->
                <div class="divide-y divide-slate-100">
                    <?php if (empty($active_reserves)): ?>
                        <div class="p-6 text-center text-xs text-slate-400">
                            No active project escrow reserves locked.
                        </div>
                    <?php else: ?>
                        <?php foreach ($active_reserves as $res): ?>
                        <div class="p-4 sm:p-5 hover:bg-slate-50/70 transition-colors flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="space-y-1 max-w-xl">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="my-projects.php" class="text-xs sm:text-sm font-bold text-slate-900 hover:text-[#1952E1] transition-colors">
                                        <?= htmlspecialchars($res['title']) ?>
                                    </a>
                                    <span class="bg-blue-50 text-[#1952E1] border border-blue-200 text-[10px] font-bold px-2 py-0.5 rounded-[3px]">
                                        <?= htmlspecialchars($res['category']) ?>
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500">Talent: <strong class="text-slate-800"><?= htmlspecialchars($res['provider_name'] ?: 'Not Assigned') ?></strong></p>
                            </div>

                            <!-- Progress Bar & Status -->
                            <div class="flex items-center justify-between lg:justify-end gap-5 shrink-0">
                                <div class="text-left lg:text-right">
                                    <span class="text-xs font-bold text-slate-900 block">₦<?= number_format($res['locked_amount']) ?> Locked in Escrow</span>
                                    <span class="text-[11px] text-slate-400 font-medium block">Total Contract: ₦<?= number_format($res['total_contract_amount']) ?></span>
                                </div>
                                <a href="my-projects.php" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-[#1952E1] text-xs font-bold rounded-[3px] transition-colors whitespace-nowrap">
                                    View Contract
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- COMPREHENSIVE TRANSACTION LEDGER -->
            <div class="bg-white rounded-[3px] border border-slate-200/90 shadow-sm space-y-4 p-4 sm:p-5">
                
                <!-- Ledger Header & Filter Controls -->
                <div class="flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900">Transaction History & Invoices</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Audited record of all deposits, escrow locks, milestone releases, and refunds.</p>
                    </div>

                    <!-- Search & Filter Options -->
                    <div class="grid grid-cols-2 sm:flex sm:items-center gap-2">
                        <div class="relative col-span-2 sm:col-span-1">
                            <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-sm"></i>
                            <input type="text" id="tx-search" placeholder="Search reference or project..." class="w-full sm:w-56 bg-slate-50 border border-slate-200 rounded-[3px] pl-9 pr-3 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#1952E1]">
                        </div>

                        <select id="tx-type-filter" class="bg-slate-50 border border-slate-200 rounded-[3px] px-2.5 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:border-[#1952E1]">
                            <option value="all">All Channels</option>
                            <option value="deposit">Deposits</option>
                            <option value="release">Milestone Releases</option>
                            <option value="refund">Refunds</option>
                        </select>

                        <select id="tx-date-filter" class="bg-slate-50 border border-slate-200 rounded-[3px] px-2.5 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:border-[#1952E1]">
                            <option value="30">Last 30 Days</option>
                            <option value="90">Last 90 Days</option>
                            <option value="all">All Time (2026)</option>
                        </select>
                    </div>
                </div>

                <!-- Transaction Tabs -->
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar border-t border-slate-100 pt-3" id="tx-category-tabs">
                    <button class="tx-tab active px-3 py-1.5 text-xs font-bold rounded-[3px] bg-[#1952E1] text-white shadow-sm whitespace-nowrap" data-type="all">
                        All Records (<?= $all_tx_count ?>)
                    </button>
                    <button class="tx-tab px-3 py-1.5 text-xs font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-colors whitespace-nowrap" data-type="deposit">
                        Escrow Deposits (<?= $deposit_tx_count ?>)
                    </button>
                    <button class="tx-tab px-3 py-1.5 text-xs font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-colors whitespace-nowrap" data-type="release">
                        Milestone Releases (<?= $release_tx_count ?>)
                    </button>
                    <button class="tx-tab px-3 py-1.5 text-xs font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-colors whitespace-nowrap" data-type="refund">
                        Refunds (<?= $refund_tx_count ?>)
                    </button>
                </div>

                <!-- Data Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px] bg-slate-50/70">
                                <th class="py-3 px-3">Date & Reference</th>
                                <th class="py-3 px-3">Description & Project</th>
                                <th class="py-3 px-3">Channel / Gateway</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3 text-right">Amount (₦)</th>
                                <th class="py-3 px-3 text-right">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700" id="tx-table-body">
                            
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-xs text-slate-400">
                                        No transactions recorded.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $tx): 
                                    $tx_date = date('M d, Y', strtotime($tx['created_at']));
                                    $tx_ref = 'CRD-TX-' . str_pad($tx['id'], 6, '0', STR_PAD_LEFT);
                                    $is_plus = ($tx['type'] === 'deposit' || $tx['type'] === 'escrow_refund');
                                    $amt_prefix = $is_plus ? '+' : '-';
                                    
                                    $js_type = 'deposit';
                                    if ($tx['type'] === 'escrow_lock' || $tx['type'] === 'escrow_release' || $tx['type'] === 'withdrawal') {
                                        $js_type = 'release';
                                    } elseif ($tx['type'] === 'escrow_refund') {
                                        $js_type = 'refund';
                                    }
                                ?>
                                <tr class="tx-row hover:bg-slate-50/50 transition-colors" data-type="<?= $js_type ?>">
                                    <td class="py-3 px-3 font-medium whitespace-nowrap">
                                        <span class="block text-slate-900 font-bold"><?= $tx_date ?></span>
                                        <span class="block text-[10px] text-slate-400 font-mono"><?= $tx_ref ?></span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="font-bold text-slate-900 block"><?= htmlspecialchars($tx['description']) ?></span>
                                        <span class="text-[11px] text-slate-500">Milestone Payment Ref</span>
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 text-slate-700">
                                            <i class="ph-bold ph-credit-card text-slate-400"></i> Paystack Inline
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <span class="bg-emerald-50 text-emerald-800 border border-emerald-200 text-[10px] font-bold px-2 py-0.5 rounded-[3px] inline-flex items-center gap-1">
                                            <i class="ph-bold ph-check text-xs"></i> Completed
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right font-black text-slate-900 whitespace-nowrap">
                                        <?= $amt_prefix ?>₦<?= number_format($tx['amount']) ?>
                                    </td>
                                    <td class="py-3 px-3 text-right whitespace-nowrap">
                                        <button type="button" onclick="alert('Downloading invoice <?= $tx_ref ?>...');" class="text-[#1952E1] hover:text-blue-800 font-bold text-xs inline-flex items-center gap-1">
                                            <i class="ph-bold ph-download-simple"></i> PDF
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>

            </div>

        </div>

    </div>
</main>

<!-- Main flex container ends -->
</div> 

<!-- ==========================================================================
     DEPOSIT FUNDS TO ESCROW MODAL DIALOG
     ========================================================================== -->
<div id="deposit-modal-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 opacity-0 pointer-events-none transition-opacity duration-300 flex items-center justify-center p-3 sm:p-4">
    <div id="deposit-modal" class="bg-white rounded-[3px] border border-slate-200 shadow-2xl max-w-lg w-full p-5 sm:p-6 transform scale-95 transition-transform duration-300 space-y-4">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                    <i class="ph-bold ph-plus-circle text-[#1952E1]"></i>
                    Deposit Funds to Escrow
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Top up your wallet or fund an active project milestone directly.</p>
            </div>
            <button type="button" id="close-deposit-modal" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-[3px] transition-colors">
                <i class="ph-bold ph-x text-sm"></i>
            </button>
        </div>

        <!-- Preset Amount Pills -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">Select Preset Deposit Amount</label>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" class="deposit-preset-btn active py-2 text-xs font-bold border border-[#1952E1] bg-blue-50 text-[#1952E1] rounded-[3px] transition-all" data-amount="50000">₦50,000</button>
                <button type="button" class="deposit-preset-btn py-2 text-xs font-bold border border-slate-200 bg-slate-50 text-slate-700 rounded-[3px] transition-all hover:bg-slate-100" data-amount="100000">₦100,000</button>
                <button type="button" class="deposit-preset-btn py-2 text-xs font-bold border border-slate-200 bg-slate-50 text-slate-700 rounded-[3px] transition-all hover:bg-slate-100" data-amount="250000">₦250,000</button>
            </div>
        </div>

        <!-- Custom Amount Input -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">Or Enter Specific Amount (₦)</label>
            <div class="relative">
                <span class="absolute left-3 top-2.5 text-xs font-bold text-slate-400">₦</span>
                <input type="number" id="deposit-custom-amount" value="50000" min="5000" step="5000" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] pl-7 pr-3 py-2 text-xs font-bold text-slate-900 focus:outline-none focus:bg-white focus:border-[#1952E1]">
            </div>
        </div>

        <!-- Payment Gateway Selection -->
        <div class="space-y-2">
            <label class="block text-xs font-bold text-slate-700">Choose Secure Payment Method</label>
            <div class="space-y-2">
                
                <label class="flex items-center justify-between p-3 border border-blue-200 bg-blue-50/40 rounded-[3px] cursor-pointer">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="payment_channel" value="paystack" checked class="text-[#1952E1] focus:ring-0">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Paystack (Debit Card / USSD / Transfer)</span>
                            <span class="text-[11px] text-slate-500">Instant credit with 0.0% transaction fee</span>
                        </div>
                    </div>
                    <i class="ph-bold ph-credit-card text-[#1952E1] text-lg"></i>
                </label>

                <label class="flex items-center justify-between p-3 border border-slate-200 bg-slate-50 rounded-[3px] cursor-pointer hover:border-slate-300">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="payment_channel" value="bank" class="text-[#1952E1] focus:ring-0">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Dedicated Virtual Bank Transfer (Wema / Providus)</span>
                            <span class="text-[11px] text-slate-500">Direct transfer to your unique Scriptly Escrow account</span>
                        </div>
                    </div>
                    <i class="ph-bold ph-bank text-slate-500 text-lg"></i>
                </label>

            </div>
        </div>

        <!-- Escrow Protection Info Card -->
        <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-[3px] flex items-center gap-2 text-xs text-emerald-900 font-medium">
            <i class="ph-bold ph-shield-check text-emerald-700 text-base shrink-0"></i>
            <span>Deposits remain 100% in your escrow balance and are only released when you approve completed milestones.</span>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
            <button type="button" id="cancel-deposit-btn" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-[3px] transition-colors">
                Cancel
            </button>
            <button type="button" onclick="initiatePaystackDeposit()" class="px-4 py-2 text-xs font-bold bg-[#1952E1] hover:bg-blue-700 text-white rounded-[3px] transition-colors shadow-sm">
                Proceed to Pay
            </button>
        </div>

    </div>
</div>

<!-- JAVASCRIPT: TRANSACTION FILTERING & DEPOSIT MODAL -->
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // Transaction Tabs Filter
    const tabs = document.querySelectorAll('.tx-tab');
    const rows = document.querySelectorAll('.tx-row');
    const searchInput = document.getElementById('tx-search');
    const typeSelect = document.getElementById('tx-type-filter');

    function filterTransactions() {
        const activeTab = document.querySelector('.tx-tab.active');
        const tabType = activeTab ? activeTab.dataset.type : 'all';
        const selectType = typeSelect ? typeSelect.value : 'all';
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

        rows.forEach(row => {
            const rowType = row.dataset.type || '';
            const rowText = row.textContent.toLowerCase();

            const matchesTab = (tabType === 'all' || rowType === tabType);
            const matchesSelect = (selectType === 'all' || rowType === selectType);
            const matchesQuery = (query === '' || rowText.includes(query));

            if (matchesTab && matchesSelect && matchesQuery) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => {
                t.classList.remove('active', 'bg-[#1952E1]', 'text-white', 'shadow-sm');
                t.classList.add('bg-slate-100', 'text-slate-600');
            });
            tab.classList.add('active', 'bg-[#1952E1]', 'text-white', 'shadow-sm');
            tab.classList.remove('bg-slate-100', 'text-slate-600');
            filterTransactions();
        });
    });

    if (searchInput) searchInput.addEventListener('input', filterTransactions);
    if (typeSelect) typeSelect.addEventListener('change', filterTransactions);

    // Deposit Modal Triggers
    const backdrop = document.getElementById('deposit-modal-backdrop');
    const modal = document.getElementById('deposit-modal');
    const openBtn = document.getElementById('open-deposit-modal');
    const closeBtn = document.getElementById('close-deposit-modal');
    const cancelBtn = document.getElementById('cancel-deposit-btn');
    const customAmountInput = document.getElementById('deposit-custom-amount');
    const presetBtns = document.querySelectorAll('.deposit-preset-btn');

    presetBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            presetBtns.forEach(b => {
                b.classList.remove('active', 'border-[#1952E1]', 'bg-blue-50', 'text-[#1952E1]');
                b.classList.add('border-slate-200', 'bg-slate-50', 'text-slate-700');
            });
            btn.classList.add('active', 'border-[#1952E1]', 'bg-blue-50', 'text-[#1952E1]');
            btn.classList.remove('border-slate-200', 'bg-slate-50', 'text-slate-700');
            if (customAmountInput) customAmountInput.value = btn.dataset.amount;
        });
    });

    const openModal = () => {
        if (backdrop && modal) {
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.remove('scale-95');
            modal.classList.add('scale-100');
        }
    };

    const closeModal = () => {
        if (backdrop && modal) {
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            modal.classList.remove('scale-100');
            modal.classList.add('scale-95');
        }
    };

    if (openBtn) openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    if (backdrop) {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) closeModal();
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
});

function initiatePaystackDeposit() {
    const amountInput = document.getElementById('deposit-custom-amount');
    const amount = parseFloat(amountInput ? amountInput.value : 0);
    if (isNaN(amount) || amount <= 0) {
        alert('Please enter a valid deposit amount.');
        return;
    }
    
    let handler = PaystackPop.setup({
        key: 'pk_test_a0d84fde90a887b415a77033cb2188ff6e65a0db',
        email: '<?= htmlspecialchars($me_info['email']) ?>',
        amount: amount * 100,
        currency: 'NGN',
        callback: function(response) {
            const formData = new FormData();
            formData.append('action', 'fund_wallet');
            formData.append('amount', amount);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Wallet deposit of ₦' + amount.toLocaleString() + ' successful!');
                    location.reload();
                } else {
                    alert('Error funding wallet.');
                }
            });
        },
        onClose: function() {
            alert('Payment was cancelled.');
        }
    });
    handler.openIframe();
}
</script>

<?php include __DIR__ . '/components/footer.php'; ?>
