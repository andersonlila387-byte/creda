<?php
$page_title = 'Earnings & Wallet';
$active_tab = 'earnings';
require_once __DIR__ . '/components/head.php';

try {
    $db = getDBConnection();
    
    // 1. Fetch available cleared balance
    $u_stmt = $db->prepare("SELECT balance FROM users WHERE id = ? LIMIT 1");
    $u_stmt->execute([$user_id]);
    $cleared_balance = floatval($u_stmt->fetchColumn() ?: 0);
    
    // 2. Calculate locked escrow amount (Milestones pending or submitted on active contracts)
    $locked_stmt = $db->prepare("
        SELECT SUM(m.amount) 
        FROM milestones m
        JOIN projects p ON m.project_id = p.id
        JOIN proposals pr ON pr.project_id = p.id AND pr.provider_id = :uid AND pr.status = 'accepted'
        WHERE m.status IN ('pending', 'submitted') AND p.status = 'in_progress'
    ");
    $locked_stmt->execute([':uid' => $user_id]);
    $locked_escrow = floatval($locked_stmt->fetchColumn() ?: 0);
    
    // 3. Calculate total earnings cleared (milestones approved/paid)
    $total_stmt = $db->prepare("
        SELECT SUM(m.amount) 
        FROM milestones m
        JOIN projects p ON m.project_id = p.id
        JOIN proposals pr ON pr.project_id = p.id AND pr.provider_id = :uid AND pr.status = 'accepted'
        WHERE m.status = 'paid'
    ");
    $total_stmt->execute([':uid' => $user_id]);
    $total_cleared = floatval($total_stmt->fetchColumn() ?: 0);
    
    // 4. Fetch recent transactions
    $tx_stmt = $db->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $tx_stmt->execute([$user_id]);
    $transactions = $tx_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (\Exception $e) {
    $cleared_balance = 0;
    $locked_escrow = 0;
    $total_cleared = 0;
    $transactions = [];
}
?>

<!-- Vertical Navigation -->
<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Content Area -->
<div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
    <?php include __DIR__ . '/components/header.php'; ?>
    <main class="flex-1 px-4 sm:px-8 lg:px-12 py-6 pb-36 sm:pb-16 space-y-7 max-w-[1600px] mx-auto w-full">
    
    <!-- Top Header -->
    

    <!-- Title Bar -->
    <div class="bg-white border border-slate-200/90 rounded-[3px] p-5 sm:p-6 shadow-sm flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight font-heading">Escrow Wallet & Payouts</h1>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Track cleared milestone earnings, locked contract escrows, and withdraw bank payouts.</p>
        </div>
        <button type="button" id="btn-open-withdrawal" class="bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs px-5 py-3 rounded-[3px] transition-colors shadow-sm cursor-pointer shrink-0 self-start sm:self-auto">
            Withdraw Funds
        </button>
    </div>

    <!-- Balance Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Cleared Balance -->
        <div class="bg-white p-6 border border-slate-200/90 rounded-[3px] shadow-sm space-y-3">
            <span class="text-xs font-black text-slate-400 uppercase tracking-wider block">Available Balance (Cleared)</span>
            <div class="flex justify-between items-center">
                <span class="text-2xl sm:text-3xl font-black text-[#1952E1] font-heading">₦<?php echo number_format($cleared_balance); ?></span>
                <span class="w-8 h-8 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 text-xs">✓</span>
            </div>
            <p class="text-[10px] text-slate-400 leading-normal font-medium">Cleared earnings ready for immediate withdrawal to your local bank account.</p>
        </div>

        <!-- Locked Escrow -->
        <div class="bg-white p-6 border border-slate-200/90 rounded-[3px] shadow-sm space-y-3">
            <span class="text-xs font-black text-slate-400 uppercase tracking-wider block">Locked in Escrow</span>
            <div class="flex justify-between items-center">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">₦<?php echo number_format($locked_escrow); ?></span>
                <span class="w-8 h-8 rounded-full bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 text-xs">🔒</span>
            </div>
            <p class="text-[10px] text-slate-400 leading-normal font-medium">Deposited contract funds currently locked in escrow. Releases upon milestone approvals.</p>
        </div>

        <!-- Total Cleared -->
        <div class="bg-white p-6 border border-slate-200/90 rounded-[3px] shadow-sm space-y-3">
            <span class="text-xs font-black text-slate-400 uppercase tracking-wider block">Total Lifetime Clearance</span>
            <div class="flex justify-between items-center">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">₦<?php echo number_format($total_cleared); ?></span>
                <span class="w-8 h-8 rounded-full bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 text-xs">🚀</span>
            </div>
            <p class="text-[10px] text-slate-400 leading-normal font-medium">Accumulated milestone payments successfully released and cleared to date.</p>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="bg-white border border-slate-200/90 rounded-[3px] p-5 sm:p-6 shadow-sm space-y-4">
        <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Recent Transactions</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs font-medium text-slate-600">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 font-black text-slate-800 text-[10px] uppercase tracking-wider">
                        <th class="p-3">Reference</th>
                        <th class="p-3">Description</th>
                        <th class="p-3">Type</th>
                        <th class="p-3">Date</th>
                        <th class="p-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400">No transactions recorded yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $tx): 
                            $tx_date = date('M j, Y H:i', strtotime($tx['created_at']));
                            $tx_amount = '₦' . number_format($tx['amount']);
                            
                            // Format Type Badges
                            $t_class = 'bg-slate-100 text-slate-700';
                            if ($tx['type'] === 'escrow_release' || $tx['type'] === 'deposit') {
                                $t_class = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            } elseif ($tx['type'] === 'withdrawal') {
                                $t_class = 'bg-rose-50 text-rose-700 border-rose-200';
                            }
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-3 font-bold text-slate-900"><?php echo htmlspecialchars($tx['reference']); ?></td>
                            <td class="p-3 max-w-xs truncate" title="<?php echo htmlspecialchars($tx['description']); ?>"><?php echo htmlspecialchars($tx['description']); ?></td>
                            <td class="p-3">
                                <span class="text-[9px] font-bold uppercase tracking-wider border px-2 py-0.5 rounded-[3px] <?php echo $t_class; ?>">
                                    <?php echo htmlspecialchars($tx['type']); ?>
                                </span>
                            </td>
                            <td class="p-3 text-slate-400"><?php echo $tx_date; ?></td>
                            <td class="p-3 text-right font-black text-slate-800"><?php echo $tx_amount; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Withdrawal Modal -->
<div id="withdrawal-modal" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-[3px] max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200/90 space-y-5">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <span class="text-[10px] font-black uppercase text-blue-600 tracking-wider">Bank Transfer</span>
                <h3 class="text-sm sm:text-base font-black text-slate-900">Withdraw Cleared Earnings</h3>
            </div>
            <button type="button" id="btn-close-withdraw" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form id="withdraw-form" class="space-y-4">
            
            <!-- Amount -->
            <div class="space-y-1">
                <div class="flex justify-between items-center">
                    <label for="w_amount" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Withdrawal Amount (₦)</label>
                    <span class="text-[10px] text-slate-400 font-bold">Max: ₦<?php echo number_format($cleared_balance); ?></span>
                </div>
                <input type="number" id="w_amount" name="amount" required max="<?php echo $cleared_balance; ?>" placeholder="e.g. 50000" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white placeholder-slate-400">
            </div>

            <!-- Bank Name -->
            <div class="space-y-1">
                <label for="bank_name" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Bank Name</label>
                <select id="bank_name" name="bank_name" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white appearance-none text-slate-700">
                    <option value="" disabled selected>Select your bank</option>
                    <option value="Access Bank">Access Bank</option>
                    <option value="Guaranty Trust Bank (GTB)">Guaranty Trust Bank (GTB)</option>
                    <option value="United Bank for Africa (UBA)">United Bank for Africa (UBA)</option>
                    <option value="Zenith Bank">Zenith Bank</option>
                    <option value="First Bank of Nigeria">First Bank of Nigeria</option>
                    <option value="OPay">OPay</option>
                    <option value="Moniepoint">Moniepoint</option>
                    <option value="Kuda Bank">Kuda Bank</option>
                </select>
            </div>

            <!-- Account Number -->
            <div class="space-y-1">
                <label for="account_number" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Account Number</label>
                <input type="text" id="account_number" name="account_number" required maxlength="10" placeholder="e.g. 0123456789" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white placeholder-slate-400">
            </div>

            <!-- Account Name -->
            <div class="space-y-1">
                <label for="account_name" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Account Name (Beneficiary)</label>
                <input type="text" id="account_name" name="account_name" required placeholder="e.g. John Doe" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white placeholder-slate-400">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" id="btn-cancel-withdraw" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-[3px]">Cancel</button>
                <button type="submit" id="btn-submit-withdraw" class="px-6 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-black text-xs rounded-[3px] shadow-sm">Confirm Withdrawal →</button>
            </div>
        </form>
    </div>
</main>
</div>
<!-- Mobile Bottom Navigation -->
<?php include __DIR__ . '/components/bottom-nav.php'; ?>

<!-- Scripts -->
<?php include __DIR__ . '/components/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const withdrawModal = document.getElementById('withdrawal-modal');
    const withdrawForm = document.getElementById('withdraw-form');
    
    const btnOpen = document.getElementById('btn-open-withdrawal');
    const btnCancel = document.getElementById('btn-cancel-withdraw');
    const btnClose = document.getElementById('btn-close-withdraw');
    const btnSubmit = document.getElementById('btn-submit-withdraw');

    if (btnOpen) {
        btnOpen.addEventListener('click', () => {
            // Check if balance is 0
            const maxBalance = <?php echo $cleared_balance; ?>;
            if (maxBalance <= 0) {
                ScriptlyToast.error('You do not have any available balance to withdraw.', 'Insufficient Balance');
                return;
            }
            withdrawModal.classList.remove('hidden');
        });
    }

    const hideModal = () => {
        withdrawModal.classList.add('hidden');
    };

    if (btnCancel) btnCancel.addEventListener('click', hideModal);
    if (btnClose) btnClose.addEventListener('click', hideModal);

    withdrawForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        btnSubmit.textContent = 'Processing Request...';
        btnSubmit.disabled = true;

        const formData = {
            amount: parseFloat(document.getElementById('w_amount').value),
            bank_name: document.getElementById('bank_name').value,
            account_number: document.getElementById('account_number').value.trim(),
            account_name: document.getElementById('account_name').value.trim()
        };

        try {
            const response = await fetch('../../api/provider/request-withdrawal.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            const data = await response.json();
            if (data.success) {
                ScriptlyToast.success('Withdrawal request submitted successfully! Funds will clear shortly.', 'Withdrawal Placed!');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                ScriptlyToast.error(data.message || 'Error processing withdrawal request.');
                btnSubmit.textContent = 'Confirm Withdrawal →';
                btnSubmit.disabled = false;
            }
        } catch (err) {
            ScriptlyToast.error('Network error. Please try again.');
            btnSubmit.textContent = 'Confirm Withdrawal →';
            btnSubmit.disabled = false;
        }
    });
});
</script>
