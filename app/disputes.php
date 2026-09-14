<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();
$user_id = $_SESSION['user_id'] ?? 1;

// Fetch disputes
$stmt = $db->prepare("
    SELECT d.*, p.title as project_title, u.full_name as provider_name 
    FROM disputes d
    JOIN projects p ON d.project_id = p.id
    LEFT JOIN proposals pr ON pr.project_id = p.id AND pr.status = 'accepted'
    LEFT JOIN users u ON u.id = pr.provider_id
    WHERE p.client_id = ?
    ORDER BY d.created_at DESC
");
$stmt->execute([$user_id]);
$disputes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Dispute Mediation Desk';
$active_tab = 'disputes';
require_once __DIR__ . '/components/head.php';
?>

<?php include __DIR__ . '/components/sidebar.php'; ?>

<main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-y-auto bg-[#EFF2F7] pb-[80px] md:pb-8">
    <?php include __DIR__ . '/components/header.php'; ?>

    <div class="max-w-6xl w-full mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Dispute Mediation Desk</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Raise or manage active escrow disputes. Scriptly admins mediate fairly using submitted deliverables.</p>
            </div>
            <a href="open-dispute.php" class="inline-flex items-center justify-center gap-1.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-[3px] transition-all shadow-sm">
                <i class="ph-bold ph-warning-circle text-base"></i>
                <span>File New Dispute</span>
            </a>
        </div>

        <!-- Disputes List -->
        <div class="bg-white rounded-[3px] border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Active & Resolved Cases</h3>
            </div>
            
            <div class="divide-y divide-slate-100">
                <?php if (empty($disputes)): ?>
                    <div class="p-8 text-center text-xs text-slate-400">
                        No active disputes found. Your project escrows are safe and sound!
                    </div>
                <?php else: ?>
                    <?php foreach ($disputes as $disp): 
                        $status = htmlspecialchars($disp['status']);
                        $status_class = 'bg-blue-50 text-[#1952E1] border-blue-200';
                        if ($status === 'resolved') {
                            $status_class = 'bg-emerald-50 text-emerald-850 border-emerald-200';
                        }
                    ?>
                    <div class="p-5 hover:bg-slate-50/50 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-bold text-slate-900">Case #<?= $disp['id'] ?></span>
                                <span class="bg-slate-100 text-slate-700 text-[10px] font-bold px-1.5 py-0.2 rounded-[3px] border border-slate-200 font-mono">
                                    Project ID: <?= $disp['project_id'] ?>
                                </span>
                                <span class="border text-[10px] font-bold px-2 py-0.5 rounded-[3px] <?= $status_class ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-950"><?= htmlspecialchars($disp['project_title']) ?></h4>
                            <p class="text-xs text-slate-500">
                                Partner: <strong class="text-slate-800"><?= htmlspecialchars($disp['provider_name'] ?: 'Collaborator') ?></strong> • 
                                Reason: <span class="text-slate-700 italic">"<?= htmlspecialchars($disp['reason']) ?>"</span>
                            </p>
                        </div>
                        <div class="shrink-0 flex items-center gap-2">
                            <button type="button" onclick="alert('Mediation desk: An admin has been notified and is currently reviewing case history.')" class="px-3.5 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-[3px] transition-colors">
                                View Case Logs
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

<?php include __DIR__ . '/components/footer.php'; ?>


