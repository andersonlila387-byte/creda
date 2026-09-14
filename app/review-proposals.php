<?php 
/**
 * Scriptly Escrow - Review Proposals
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();

// Require Client Login
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id || ($_SESSION['primary_role'] ?? 'client') !== 'client') {
    header("Location: ../login.php?redirect=review-proposals.php?" . http_build_query($_GET));
    exit;
}

// Fetch Project Info by Slug or ID
$project = null;
if (isset($_GET['slug'])) {
    $stmt = $db->prepare("SELECT * FROM projects WHERE slug = ? AND client_id = ?");
    $stmt->execute([$_GET['slug'], $user_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
} elseif (isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM projects WHERE id = ? AND client_id = ?");
    $stmt->execute([$_GET['id'], $user_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$project) {
    die("Project not found or you don't have permission to view it.");
}

// Handle Accept Proposal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_proposal_id'])) {
    $proposal_id = (int)$_POST['accept_proposal_id'];
    
    // Fetch Proposal
    $p_stmt = $db->prepare("SELECT * FROM proposals WHERE id = ? AND project_id = ? AND status = 'pending'");
    $p_stmt->execute([$proposal_id, $project['id']]);
    $proposal = $p_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($proposal) {
        try {
            $db->beginTransaction();
            
            // 1. Update proposal status
            $upd_p = $db->prepare("UPDATE proposals SET status = 'accepted' WHERE id = ?");
            $upd_p->execute([$proposal_id]);
            
            // 2. Reject other proposals
            $upd_o = $db->prepare("UPDATE proposals SET status = 'rejected' WHERE project_id = ? AND id != ?");
            $upd_o->execute([$project['id'], $proposal_id]);
            
            // 3. Update project status
            $upd_proj = $db->prepare("UPDATE projects SET status = 'in_progress' WHERE id = ?");
            $upd_proj->execute([$project['id']]);
            
            // 4. Create Contract
            $fee = $proposal['bid_amount'] * 0.05;
            $total = $proposal['bid_amount'] + $fee;
            $stmt_c = $db->prepare("INSERT INTO contracts (client_id, provider_id, package_id, title, total_amount, status, created_at, updated_at) VALUES (?, ?, NULL, ?, ?, 'active', NOW(), NOW())");
            $stmt_c->execute([$user_id, $proposal['provider_id'], $project['title'], $total]);
            $contract_id = $db->lastInsertId();
            
            // 5. Fund Escrow Mock
            $stmt_e = $db->prepare("INSERT INTO escrow_transactions (contract_id, amount, fee_amount, status) VALUES (?, ?, ?, 'funded')");
            $stmt_e->execute([$contract_id, $proposal['bid_amount'], $fee]);
            
            $db->commit();
            header("Location: contract-details.php?id=" . $contract_id . "&success=proposal_accepted");
            exit;
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $error = "Failed to accept proposal: " . $e->getMessage();
        }
    } else {
        $error = "Invalid proposal or already processed.";
    }
}

// Fetch all proposals for this project
$proposals_stmt = $db->prepare("
    SELECT pr.*, u.full_name, tp.avatar_url, tp.title as provider_title, tp.rating, tp.rating_count, tp.job_success_percentage, tp.location
    FROM proposals pr
    JOIN users u ON pr.provider_id = u.id
    LEFT JOIN talent_profiles tp ON u.id = tp.user_id
    WHERE pr.project_id = ?
    ORDER BY pr.created_at ASC
");
$proposals_stmt->execute([$project['id']]);
$proposals = $proposals_stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Review Proposals';
$active_tab = 'projects';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/components/head.php'; ?>
    <title>Review Proposals - <?= htmlspecialchars($project['title']) ?></title>
</head>
<body class="bg-[#EFF2F7] text-slate-800 font-sans antialiased min-h-screen flex overflow-hidden">

    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <?php include __DIR__ . '/components/bottom-nav.php'; ?>

    <main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-hidden relative bg-[#EFF2F7]">
        
        <?php include __DIR__ . '/components/header.php'; ?>

        <div class="flex-1 overflow-y-auto w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 mobile-bottom-space md:pb-6 lg:pb-12 scroll-smooth">
            <div class="max-w-5xl mx-auto space-y-5">

                <?php if (isset($error)): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-[3px] border border-red-200 mb-6 text-sm font-bold">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <!-- PROJECT HEADER -->
                <div class="bg-white p-4 sm:p-6 rounded-[3px] border border-slate-200/90 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <a href="my-projects.php" class="text-xs font-bold text-slate-400 hover:text-slate-700 uppercase tracking-wider flex items-center gap-1">
                                <i class="ph-bold ph-arrow-left"></i> My Projects /
                            </a>
                            <span class="font-mono text-xs font-bold text-[#1952E1] bg-blue-50 px-2 py-0.5 rounded-[3px]"><?= htmlspecialchars($project['slug']) ?></span>
                            <?php if ($project['status'] === 'open'): ?>
                                <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-[3px] border border-emerald-200">Open for Bids</span>
                            <?php else: ?>
                                <span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-0.5 rounded-[3px] border border-slate-200"><?= strtoupper($project['status']) ?></span>
                            <?php endif; ?>
                        </div>
                        <h1 class="text-lg sm:text-2xl font-bold text-slate-900 tracking-tight">
                            <?= htmlspecialchars($project['title']) ?>
                        </h1>
                        <div class="flex items-center gap-3 text-xs text-slate-500 pt-0.5 flex-wrap">
                            <span class="flex items-center gap-1.5"><i class="ph-bold ph-clock text-slate-400"></i> Posted <?= date('M j, Y', strtotime($project['created_at'])) ?></span>
                            <span class="text-slate-300">|</span>
                            <span class="flex items-center gap-1.5"><i class="ph-bold ph-users text-slate-400"></i> <?= count($proposals) ?> Proposals</span>
                        </div>
                    </div>
                    <div class="shrink-0 flex items-center gap-3">
                        <div class="text-right">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Your Budget</div>
                            <div class="font-black text-slate-900 text-lg">
                                <?= $project['budget'] > 0 ? '₦' . number_format($project['budget']) : 'Negotiable' ?>
                            </div>
                        </div>
                    </div>
                </div>

                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mt-6 mb-4 px-1">Candidate Proposals</h2>
                
                <?php if (empty($proposals)): ?>
                    <div class="bg-white p-12 rounded-[3px] border border-slate-200/90 shadow-sm text-center">
                        <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="ph-bold ph-tray text-2xl text-slate-300"></i>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900 mb-1">No proposals yet</h3>
                        <p class="text-xs text-slate-500">When providers submit bids to your project, they will appear here.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($proposals as $p): ?>
                            <div class="proposal-card bg-white rounded-[3px] border border-slate-200/90 p-5 sm:p-6 shadow-sm hover:border-[#1952E1] transition-all space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-slate-100">
                                    
                                    <div class="flex items-start gap-3.5">
                                        <div class="relative shrink-0">
                                            <?php 
                                            $avatar = $p['avatar_url'] ?: "https://ui-avatars.com/api/?name=" . urlencode($p['full_name']) . "&background=f1f5f9&color=0f172a&bold=true"; 
                                            ?>
                                            <img src="<?= htmlspecialchars($avatar) ?>" alt="<?= htmlspecialchars($p['full_name']) ?>" class="w-12 h-12 rounded-full object-cover border-2 border-slate-200">
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 mb-0.5">
                                                <a href="provider-profile.php?id=<?= $p['provider_id'] ?>" class="text-base font-bold text-slate-900 hover:text-[#1952E1] transition-colors">
                                                    <?= htmlspecialchars($p['full_name']) ?>
                                                </a>
                                                <i class="ph-fill ph-seal-check text-blue-500 text-sm" title="Verified Professional"></i>
                                            </div>
                                            <p class="text-xs font-semibold text-slate-600 mb-1.5"><?= htmlspecialchars($p['provider_title'] ?: 'Verified Professional') ?></p>
                                            <div class="flex flex-wrap items-center gap-3 text-[11px] font-medium text-slate-500">
                                                <span class="flex items-center gap-1 text-amber-600"><i class="ph-fill ph-star"></i> <?= number_format($p['rating'] ?? 5.0, 1) ?> (<?= $p['rating_count'] ?? 0 ?> reviews)</span>
                                                <span class="flex items-center gap-1"><i class="ph-bold ph-chart-line-up"></i> <?= $p['job_success_percentage'] ?? 100 ?>% Job Success</span>
                                                <span class="flex items-center gap-1"><i class="ph-bold ph-map-pin"></i> <?= htmlspecialchars($p['location'] ?: 'Remote') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="shrink-0 flex flex-row sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-2 bg-slate-50 sm:bg-transparent p-3 sm:p-0 rounded-[3px]">
                                        <div class="text-left sm:text-right">
                                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Bid Amount</div>
                                            <div class="font-black text-slate-900 text-xl">₦<?= number_format($p['bid_amount']) ?></div>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900 mb-2 uppercase tracking-wider">Cover Letter</h4>
                                    <div class="text-sm text-slate-700 leading-relaxed bg-slate-50/50 p-4 rounded-[3px] border border-slate-100">
                                        <?= nl2br(htmlspecialchars($p['cover_letter'])) ?>
                                    </div>
                                </div>
                                
                                <div class="pt-2 flex items-center justify-between gap-3">
                                    <a href="messages.php?user=<?= $p['provider_id'] ?>" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 px-4 py-2.5 rounded-[3px] transition-colors flex items-center gap-2">
                                        <i class="ph-bold ph-chat-teardrop-text text-sm"></i> Message
                                    </a>
                                    
                                    <?php if ($project['status'] === 'open' && $p['status'] === 'pending'): ?>
                                    <form method="POST" class="shrink-0">
                                        <input type="hidden" name="accept_proposal_id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-6 py-2.5 rounded-[3px] transition-colors shadow-sm flex items-center gap-2">
                                            <i class="ph-bold ph-check-circle text-sm"></i> Accept Bid & Hire
                                        </button>
                                    </form>
                                    <?php elseif ($p['status'] === 'accepted'): ?>
                                        <span class="bg-emerald-50 text-emerald-700 font-bold text-xs px-4 py-2 rounded-[3px] border border-emerald-200">Hired</span>
                                    <?php else: ?>
                                        <span class="bg-slate-100 text-slate-500 font-bold text-xs px-4 py-2 rounded-[3px]">Rejected</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
    </main>
</body>
</html>
