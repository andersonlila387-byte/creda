<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();
$user_id = $_SESSION['user_id'] ?? 1;

// Fetch completed/accepted proposals to rate
$p_stmt = $db->prepare("
    SELECT p.id, p.title, pr.id as proposal_id, u.full_name as provider_name
    FROM projects p
    JOIN proposals pr ON pr.project_id = p.id AND pr.status = 'accepted'
    JOIN users u ON u.id = pr.provider_id
    WHERE p.client_id = ?
");
$p_stmt->execute([$user_id]);
$rateable_projects = $p_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $proposal_id = (int)($_POST['proposal_id'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 5);
    $comment = trim($_POST['comment'] ?? '');
    
    if ($proposal_id > 0) {
        // Save review
        $rev_stmt = $db->prepare("
            INSERT INTO reviews (proposal_id, reviewer_id, rating, comment) 
            VALUES (?, ?, ?, ?)
        ");
        $rev_stmt->execute([$proposal_id, $user_id, $rating, $comment]);
        
        header('Location: services.php?success=feedback');
        exit;
    }
}

$page_title = 'Leave Client Feedback';
$active_tab = 'talent';
require_once __DIR__ . '/components/head.php';
?>

<?php include __DIR__ . '/components/sidebar.php'; ?>

<main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-y-auto bg-[#EFF2F7] pb-36 md:pb-8">
    <?php include __DIR__ . '/components/header.php'; ?>

    <div class="max-w-xl w-full mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        
        <!-- Header -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Leave Feedback & Review</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Submit your rating and comments to build transparency and reward quality talent.</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-[3px] border border-slate-200/90 shadow-sm p-5 sm:p-6">
            <form method="POST" action="leave-feedback.php" class="space-y-4">
                
                <!-- Project Selection -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Project / Collaborator</label>
                    <select name="proposal_id" required class="w-full bg-slate-50 border border-slate-200 rounded-[3px] px-3 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]">
                        <option value="">-- Select Completed Project --</option>
                        <?php foreach ($rateable_projects as $proj): ?>
                            <option value="<?= $proj['proposal_id'] ?>">
                                <?= htmlspecialchars($proj['title']) ?> (with <?= htmlspecialchars($proj['provider_name']) ?>)
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

                <!-- Star Comment -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Feedback / Review Comments</label>
                    <textarea name="comment" rows="4" required class="w-full bg-slate-50 border border-slate-200 rounded-[3px] p-3 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]" placeholder="Detail your experience with this candidate..."></textarea>
                </div>

                <!-- Submit buttons -->
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <a href="services.php" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-[3px] transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 text-xs font-bold bg-[#1952E1] hover:bg-blue-700 text-white rounded-[3px] transition-colors shadow-sm">
                        Submit Review
                    </button>
                </div>

            </form>
        </div>

    </div>
</main>

<?php include __DIR__ . '/components/footer.php'; ?>

