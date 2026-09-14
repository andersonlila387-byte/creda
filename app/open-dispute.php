<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();
$user_id = $_SESSION['user_id'] ?? 1;

// Fetch client's active projects to select from
$p_stmt = $db->prepare("
    SELECT p.id, p.title 
    FROM projects p
    WHERE p.client_id = ? AND p.status = 'in_progress'
");
$p_stmt->execute([$user_id]);
$active_projects = $p_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = (int)($_POST['project_id'] ?? 0);
    $reason = trim($_POST['reason'] ?? '');
    
    if ($project_id > 0 && !empty($reason)) {
        // Start transaction
        $db->beginTransaction();
        try {
            // Save dispute
            $disp_stmt = $db->prepare("
                INSERT INTO disputes (project_id, reason, status) 
                VALUES (?, ?, 'pending')
            ");
            $disp_stmt->execute([$project_id, $reason]);
            
            // Set project status to disputed
            $up_stmt = $db->prepare("UPDATE projects SET status = 'disputed' WHERE id = ?");
            $up_stmt->execute([$project_id]);
            
            $db->commit();
            header('Location: disputes.php?success=file');
            exit;
        } catch (Exception $e) {
            $db->rollBack();
            $error = $e->getMessage();
        }
    } else {
        $error = "Please select a project and provide a valid reason for the dispute.";
    }
}

$page_title = 'File Dispute Case';
$active_tab = 'disputes';
require_once __DIR__ . '/components/head.php';
?>

<?php include __DIR__ . '/components/sidebar.php'; ?>

<main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-y-auto bg-[#EFF2F7] pb-36 md:pb-8">
    <?php include __DIR__ . '/components/header.php'; ?>

    <div class="max-w-xl w-full mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        
        <!-- Header -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Open Dispute Case</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Submit a formal escrow resolution request to platform administrators.</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-[3px] text-xs font-bold text-rose-800 flex items-center gap-2">
                <i class="ph-fill ph-warning-circle text-rose-600 text-sm"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="bg-white rounded-[3px] border border-slate-200/90 shadow-sm p-5 sm:p-6">
            <form method="POST" action="open-dispute.php" class="space-y-4">
                
                <!-- Project Selection -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Select Project Contract</label>
                    <select name="project_id" required class="w-full bg-slate-50 border border-slate-200 rounded-[3px] px-3 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]">
                        <option value="">-- Choose Active Contract --</option>
                        <?php foreach ($active_projects as $proj): ?>
                            <option value="<?= $proj['id'] ?>"><?= htmlspecialchars($proj['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Reason description -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Reason / Details of Dispute</label>
                    <textarea name="reason" rows="4" required class="w-full bg-slate-50 border border-slate-200 rounded-[3px] p-3 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]" placeholder="Detail why you are filing a dispute (e.g. deliverable missed, quality issues, no communication)..."></textarea>
                </div>

                <!-- Terms Notice -->
                <div class="p-3 bg-amber-50/70 border border-amber-200 rounded-[3px] flex items-start gap-2 text-xs text-amber-900">
                    <i class="ph-bold ph-info text-amber-700 text-base shrink-0 mt-0.5"></i>
                    <span><strong>Escrow Frozen Notice</strong>: Once submitted, all locked milestone funds for this project will remain frozen in escrow until resolved.</span>
                </div>

                <!-- Submit buttons -->
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <a href="disputes.php" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-[3px] transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 text-xs font-bold bg-[#1952E1] hover:bg-blue-700 text-white rounded-[3px] transition-colors shadow-sm">
                        Submit Dispute
                    </button>
                </div>

            </form>
        </div>

    </div>
</main>

<?php include __DIR__ . '/components/footer.php'; ?>

