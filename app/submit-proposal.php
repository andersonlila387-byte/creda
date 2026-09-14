<?php 
/**
 * Scriptly Escrow - Submit Proposal
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();

// Require Provider Login
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['primary_role'] ?? 'client';
$project_id = $_GET['project_id'] ?? null;

if (!$user_id || $user_role !== 'provider' || !$project_id) {
    header("Location: projects.php");
    exit;
}

// Fetch Project Info
$stmt = $db->prepare("SELECT id, title, slug, budget, status FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project || $project['status'] !== 'open') {
    die("Project is either not found or no longer open for bids.");
}

// Check if already submitted
$chk_stmt = $db->prepare("SELECT id FROM proposals WHERE project_id = ? AND provider_id = ?");
$chk_stmt->execute([$project_id, $user_id]);
if ($chk_stmt->fetch()) {
    header("Location: project-details.php?slug=" . urlencode($project['slug']) . "&error=already_bid");
    exit;
}

// Handle Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bid_amount = floatval($_POST['bid_amount'] ?? 0);
    $cover_letter = trim($_POST['cover_letter'] ?? '');

    if ($bid_amount <= 0 || empty($cover_letter)) {
        $error = "Please provide a valid bid amount and a cover letter.";
    } else {
        try {
            $ins = $db->prepare("INSERT INTO proposals (project_id, provider_id, cover_letter, bid_amount, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
            $ins->execute([$project_id, $user_id, $cover_letter, $bid_amount]);
            
            header("Location: project-details.php?slug=" . urlencode($project['slug']) . "&success=proposal_submitted");
            exit;
        } catch (PDOException $e) {
            $error = "Failed to submit proposal: " . $e->getMessage();
        }
    }
}

$page_title = 'Submit Proposal';
$active_tab = 'projects';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/components/head.php'; ?>
    <title>Submit Proposal - <?= htmlspecialchars($project['title']) ?></title>
</head>
<body class="bg-[#EFF2F7] text-slate-800 font-sans antialiased min-h-screen flex flex-col md:flex-row">

    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <?php include __DIR__ . '/components/bottom-nav.php'; ?>

    <main class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include __DIR__ . '/components/header.php'; ?>

        <div class="p-4 md:p-8 pt-20 max-w-3xl mx-auto w-full mobile-bottom-space md:pt-8 md:pb-8">
            
            <div class="mb-6">
                <a href="project-details.php?slug=<?= urlencode($project['slug']) ?>" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-1 w-max">
                    <i class="ph-bold ph-arrow-left"></i> Back to Project
                </a>
            </div>

            <h1 class="text-2xl font-black text-slate-900 mb-2">Submit Proposal</h1>
            <p class="text-sm text-slate-600 mb-6 font-medium">Bidding on: <span class="font-bold text-slate-900"><?= htmlspecialchars($project['title']) ?></span></p>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-[3px] border border-red-200 mb-6 text-sm font-bold">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="bg-white border border-slate-200 rounded-[3px] p-6 shadow-sm">
                <form method="POST" class="space-y-6">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Bid Amount (₦)</label>
                            <input type="number" name="bid_amount" required min="1000" step="1000" placeholder="e.g. 50000" class="w-full border border-slate-300 rounded-[3px] px-3 py-2 text-sm focus:border-[#1952E1] focus:ring-1 focus:ring-[#1952E1] outline-none">
                            <p class="text-[11px] text-slate-500 mt-1">Client budget: <?= $project['budget'] > 0 ? '₦' . number_format($project['budget']) : 'Negotiable' ?></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Cover Letter</label>
                        <textarea name="cover_letter" required rows="6" placeholder="Introduce yourself, explain why you're a good fit, and detail your approach to the project..." class="w-full border border-slate-300 rounded-[3px] px-3 py-2 text-sm focus:border-[#1952E1] focus:ring-1 focus:ring-[#1952E1] outline-none"></textarea>
                    </div>
                    
                    <div class="bg-slate-50 border border-slate-100 p-4 rounded-[3px] flex items-start gap-3">
                        <i class="ph-bold ph-info text-[#1952E1] mt-0.5"></i>
                        <p class="text-xs text-slate-600">By submitting this proposal, you agree to Scriptly's terms. You will only be paid once the client approves your final deliverables via Escrow.</p>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-[#1952E1] hover:bg-blue-700 text-white font-black text-sm rounded-[3px] transition-colors shadow-sm">
                        Submit Proposal
                    </button>
                </form>
            </div>
            
        </div>
        
    </main>
</body>
</html>
