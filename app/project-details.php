<?php 
/**
 * Scriptly Escrow - Public Project Details
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
\ = getDBConnection();

\ = \['slug'] ?? '';
if (empty(\)) {
    header("Location: projects.php");
    exit;
}

// Require Login
\ = \['user_id'] ?? null;
\ = \['primary_role'] ?? 'client';
if (!\) {
    header("Location: ../login.php?redirect=project-details.php?slug=" . urlencode(\));
    exit;
}

// Fetch Project Info
\ = "
    SELECT p.*, u.full_name as client_name, u.created_at as client_joined,
           (SELECT COUNT(*) FROM projects WHERE client_id = p.client_id) as client_total_projects,
           (SELECT COUNT(*) FROM proposals WHERE project_id = p.id) as proposal_count
    FROM projects p
    JOIN users u ON p.client_id = u.id
    WHERE p.slug = ?
";
\ = \->prepare(\);
\->execute([\]);
\ = \->fetch(PDO::FETCH_ASSOC);

if (!\) {
    die("Project not found.");
}

// If provider, check if already submitted a proposal
\ = null;
if (\ === 'provider') {
    \ = \->prepare("SELECT id, bid_amount, status FROM proposals WHERE project_id = ? AND provider_id = ? LIMIT 1");
    \->execute([\['id'], \]);
    \ = \->fetch(PDO::FETCH_ASSOC);
}

\ = 'Project Details';
\ = 'projects';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/components/head.php'; ?>
    <title><?= htmlspecialchars(\['title']) ?> - Scriptly</title>
</head>
<body class="bg-[#EFF2F7] text-slate-800 font-sans antialiased min-h-screen flex overflow-hidden">

    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <?php include __DIR__ . '/components/bottom-nav.php'; ?>

    <main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-hidden relative">
        <?php include __DIR__ . '/components/header.php'; ?>

        <div class="flex-1 overflow-y-auto w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 pb-[80px] md:pb-6 lg:pb-12 scroll-smooth">
            <div class="max-w-5xl mx-auto space-y-5">
                
                <!-- Back Link -->
                <div>
                    <a href="projects.php" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-1 w-max">
                        <i class="ph-bold ph-arrow-left"></i> Back to Jobs
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                    
                    <!-- Left: Main Details -->
                    <div class="lg:col-span-8 space-y-5">
                        
                        <!-- Header Card -->
                        <div class="bg-white p-5 sm:p-7 rounded-[3px] border border-slate-200/90 shadow-sm">
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="bg-blue-50 text-[#1952E1] border border-blue-200 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-[2px]">
                                    <?= htmlspecialchars(\['category'] ?: 'Uncategorized') ?>
                                </span>
                                <?php if (\['status'] === 'open'): ?>
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-[2px]">
                                        Open for Bids
                                    </span>
                                <?php else: ?>
                                    <span class="bg-slate-100 text-slate-600 border border-slate-200 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-[2px]">
                                        <?= strtoupper(\['status']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <h1 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight mb-4">
                                <?= htmlspecialchars(\['title']) ?>
                            </h1>

                            <div class="flex flex-wrap gap-y-3 gap-x-6 text-xs text-slate-600 font-medium pb-5 border-b border-slate-100">
                                <div>
                                    <span class="text-slate-400 block mb-0.5">Posted</span>
                                    <span class="text-slate-800 font-bold"><?= date('M j, Y', strtotime(\['created_at'])) ?></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block mb-0.5">Proposals</span>
                                    <span class="text-slate-800 font-bold"><?= \['proposal_count'] ?> received</span>
                                </div>
                                <?php if (\['deadline_date']): ?>
                                <div>
                                    <span class="text-slate-400 block mb-0.5">Deadline</span>
                                    <span class="text-slate-800 font-bold"><?= date('M j, Y', strtotime(\['deadline_date'])) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="pt-5 space-y-4">
                                <h3 class="text-sm font-bold text-slate-900">Project Description</h3>
                                <div class="text-sm text-slate-700 leading-relaxed space-y-3">
                                    <?= nl2br(htmlspecialchars(\['description'])) ?>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right: Sticky Sidebar -->
                    <div class="lg:col-span-4 space-y-5 lg:sticky lg:top-4">
                        
                        <!-- Action & Budget Card -->
                        <div class="bg-white p-5 rounded-[3px] border border-slate-200/90 shadow-sm text-center">
                            <div class="text-xs text-slate-500 font-medium mb-1">Estimated Budget</div>
                            <div class="text-2xl font-black text-slate-900 mb-6">
                                <?= \['budget'] > 0 ? '?' . number_format(\['budget']) : 'Open to Bids' ?>
                            </div>

                            <?php if (\['status'] !== 'open'): ?>
                                <div class="w-full py-3 bg-slate-100 text-slate-500 text-sm font-bold rounded-[3px]">
                                    Project is closed
                                </div>
                            <?php elseif (\ === 'provider'): ?>
                                <?php if (\): ?>
                                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-[3px] mb-3 text-left">
                                        <div class="text-xs font-bold text-[#1952E1] mb-1">Proposal Submitted</div>
                                        <div class="text-[11px] text-slate-600">You placed a bid of ?<?= number_format(\['bid_amount']) ?>.</div>
                                    </div>
                                    <a href="my-projects.php" class="block w-full py-3 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-[3px] transition-colors">
                                        View in Dashboard
                                    </a>
                                <?php else: ?>
                                    <a href="submit-proposal.php?project_id=<?= \['id'] ?>" class="block w-full py-3 bg-[#1952E1] hover:bg-blue-700 text-white text-sm font-bold rounded-[3px] shadow-sm transition-colors">
                                        Submit a Proposal
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if (\['client_id'] == \): ?>
                                    <a href="review-proposals.php?id=<?= \['id'] ?>" class="block w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-[3px] shadow-sm transition-colors">
                                        Review Proposals (<?= \['proposal_count'] ?>)
                                    </a>
                                <?php else: ?>
                                    <div class="w-full py-3 bg-slate-100 text-slate-500 text-xs font-bold rounded-[3px]">
                                        Switch to Provider role to bid
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Client Info Card -->
                        <div class="bg-white p-5 rounded-[3px] border border-slate-200/90 shadow-sm">
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">About the Client</h3>
                            <div class="space-y-3">
                                <div>
                                    <div class="font-bold text-sm text-slate-900"><?= htmlspecialchars(\['client_name']) ?></div>
                                    <div class="text-xs text-slate-500">Member since <?= date('M Y', strtotime(\['client_joined'])) ?></div>
                                </div>
                                <div class="flex items-center gap-2 text-xs font-medium text-slate-700 pt-2 border-t border-slate-50">
                                    <i class="ph-bold ph-briefcase text-slate-400"></i>
                                    <?= \['client_total_projects'] ?> total projects posted
                                </div>
                                <div class="flex items-center gap-2 text-xs font-medium text-emerald-700">
                                    <i class="ph-bold ph-shield-check"></i>
                                    Payment Verified
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        
    </main>
</div>
</body>
</html>


