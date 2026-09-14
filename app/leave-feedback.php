<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();
$user_id = $_SESSION['user_id'] ?? 1;

// Fetch rateable contracts for this client
$c_stmt = $db->prepare("
    SELECT c.id as contract_id, c.title, c.provider_id, u.full_name as provider_name
    FROM contracts c
    JOIN users u ON c.provider_id = u.id
    WHERE c.client_id = ?
    ORDER BY c.created_at DESC
");
$c_stmt->execute([$user_id]);
$rateable_contracts = $c_stmt->fetchAll(PDO::FETCH_ASSOC);

$preselected_contract_id = (int)($_GET['contract_id'] ?? 0);

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contract_id = (int)($_POST['contract_id'] ?? 0);
    $rating = min(5, max(1, (float)($_POST['rating'] ?? 5)));
    $comment = trim($_POST['comment'] ?? '');
    
    if ($contract_id > 0 && !empty($comment)) {
        // Fetch contract provider
        $stmt_check = $db->prepare("SELECT provider_id FROM contracts WHERE id = ? AND client_id = ? LIMIT 1");
        $stmt_check->execute([$contract_id, $user_id]);
        $provider_id = $stmt_check->fetchColumn();
        
        if ($provider_id) {
            try {
                $rev_stmt = $db->prepare("
                    INSERT INTO contract_reviews (contract_id, provider_id, client_id, rating, review_text, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $rev_stmt->execute([$contract_id, $provider_id, $user_id, $rating, $comment]);
                
                // Recalculate provider rating in talent_profiles
                $avg_stmt = $db->prepare("SELECT AVG(rating) as avg_r, COUNT(*) as cnt FROM contract_reviews WHERE provider_id = ?");
                $avg_stmt->execute([$provider_id]);
                $stats = $avg_stmt->fetch(PDO::FETCH_ASSOC);
                if ($stats) {
                    $db->prepare("UPDATE talent_profiles SET rating = ?, rating_count = ? WHERE user_id = ?")
                       ->execute([$stats['avg_r'], $stats['cnt'], $provider_id]);
                }
                
                header('Location: my-projects.php?msg=review_submitted');
                exit;
            } catch (Exception $e) {
                $error = "Failed to submit review: " . $e->getMessage();
            }
        } else {
            $error = "Contract not found or access denied.";
        }
    } else {
        $error = "Please select a contract and provide review comments.";
    }
}

$page_title = 'Leave Client Feedback';
$active_tab = 'talent';
require_once __DIR__ . '/components/head.php';
?>

<?php include __DIR__ . '/components/sidebar.php'; ?>

<main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-y-auto bg-[#EFF2F7] mobile-bottom-space md:pb-8">
    <?php include __DIR__ . '/components/header.php'; ?>

    <div class="max-w-xl w-full mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        
        <!-- Header -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Leave Feedback & Review</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Submit your rating and comments to build transparency and reward quality talent.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="p-3 bg-rose-50 border border-rose-200 rounded-[3px] text-xs font-bold text-rose-800 flex items-center gap-2">
                <i class="ph-fill ph-warning-circle text-rose-600 text-sm shrink-0"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="bg-white rounded-[3px] border border-slate-200/90 shadow-sm p-5 sm:p-6">
            <form method="POST" action="leave-feedback.php" class="space-y-4">
                
                <!-- Project Selection -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Select Contract / Collaborator</label>
                    <select name="contract_id" required class="w-full bg-slate-50 border border-slate-200 rounded-[3px] px-3 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]">
                        <option value="">-- Select Completed Contract --</option>
                        <?php foreach ($rateable_contracts as $c): ?>
                            <option value="<?= $c['contract_id'] ?>" <?= ($preselected_contract_id === (int)$c['contract_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['title']) ?> (with <?= htmlspecialchars($c['provider_name']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Star Rating -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Rating (1 to 5 Stars)</label>
                    <select name="rating" required class="w-full bg-slate-50 border border-slate-200 rounded-[3px] px-3 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]">
                        <option value="5">⭐⭐⭐⭐⭐ Excellent (5/5)</option>
                        <option value="4">⭐⭐⭐⭐ Good (4/5)</option>
                        <option value="3">⭐⭐⭐ Average (3/5)</option>
                        <option value="2">⭐⭐ Fair (2/5)</option>
                        <option value="1">⭐ Poor (1/5)</option>
                    </select>
                </div>

                <!-- Comment -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Feedback / Review Comments</label>
                    <textarea name="comment" rows="4" required class="w-full bg-slate-50 border border-slate-200 rounded-[3px] p-3 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]" placeholder="Detail your experience with this specialist..."></textarea>
                </div>

                <!-- Submit buttons -->
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <a href="my-projects.php" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-[3px] transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 text-xs font-bold bg-[#1952E1] hover:bg-blue-700 text-white rounded-[3px] transition-colors shadow-sm cursor-pointer">
                        Submit Review
                    </button>
                </div>

            </form>
        </div>

    </div>
</main>

<?php include __DIR__ . '/components/footer.php'; ?>
