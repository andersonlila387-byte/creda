<?php 
/**
 * Scriptly Escrow - Provider Contract / Milestone Details Workspace
 */
$page_title = 'Contract Details';
$active_tab = 'contracts';
require_once __DIR__ . '/components/head.php';

// Support friendly references (e.g. ?ref=CTR-00201 or ?contract=... or ?c=... or ?id=...)
$raw_ref = trim($_GET['ref'] ?? $_GET['contract'] ?? $_GET['c'] ?? $_GET['id'] ?? '');
$contract_id = 0;

if (preg_match('/^CTR-0*(\d+)$/i', $raw_ref, $matches)) {
    $contract_id = intval($matches[1]);
} elseif (is_numeric($raw_ref)) {
    $contract_id = intval($raw_ref);
}

$contract = null;
$milestones = [];
$is_sample = false;

try {
    $db = getDBConnection();

    if ($contract_id > 0) {
        // 1. Try to fetch as a custom proposal-based Project Contract
        $p_stmt = $db->prepare("
            SELECT p.id, p.title, p.category, p.description, p.created_at, p.status as project_status,
                   u.id as client_id, u.full_name as client_name, u.email as client_email, u.avatar_url as client_avatar,
                   pr.bid_amount, pr.status as proposal_status, pr.created_at as contract_started
            FROM projects p
            JOIN proposals pr ON pr.project_id = p.id
            LEFT JOIN users u ON p.client_id = u.id
            WHERE p.id = :pid AND pr.provider_id = :uid AND pr.status = 'accepted'
            LIMIT 1
        ");
        $p_stmt->execute([':pid' => $contract_id, ':uid' => $user_id]);
        $project_data = $p_stmt->fetch(PDO::FETCH_ASSOC);

        if ($project_data) {
            $contract = $project_data;
            
            // Fetch milestones
            $m_stmt = $db->prepare("SELECT * FROM milestones WHERE project_id = ? ORDER BY id ASC");
            $m_stmt->execute([$contract_id]);
            $milestones = $m_stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // 2. Try to fetch as a Package Order Contract
            $c_stmt = $db->prepare("
                SELECT c.*, c.total_amount as bid_amount, p.title as title, p.category as category, p.description as description,
                       u.id as client_id, u.full_name as client_name, u.email as client_email, u.avatar_url as client_avatar,
                       c.created_at as contract_started
                FROM contracts c
                JOIN packages p ON c.package_id = p.id
                JOIN users u ON c.client_id = u.id
                WHERE c.id = :cid AND c.provider_id = :uid
                LIMIT 1
            ");
            $c_stmt->execute([':cid' => $contract_id, ':uid' => $user_id]);
            $contract = $c_stmt->fetch(PDO::FETCH_ASSOC);

            if ($contract) {
                $milestones = [
                    [
                        'id' => $contract['id'],
                        'title' => 'Complete Package Deliverables & Handover',
                        'amount' => $contract['bid_amount'],
                        'status' => ($contract['status'] === 'completed' ? 'approved' : ($contract['status'] === 'delivered' ? 'submitted' : 'pending')),
                        'due_date' => $contract['deadline_at'] ?? date('Y-m-d', strtotime('+7 days'))
                    ]
                ];
            }
        }
    } else {
        // If no ID or ref provided, fetch latest active contract for user
        $p_stmt = $db->prepare("
            SELECT p.id, p.title, p.category, p.description, p.created_at, p.status as project_status,
                   u.id as client_id, u.full_name as client_name, u.email as client_email, u.avatar_url as client_avatar,
                   pr.bid_amount, pr.status as proposal_status, pr.created_at as contract_started
            FROM projects p
            JOIN proposals pr ON pr.project_id = p.id
            LEFT JOIN users u ON p.client_id = u.id
            WHERE pr.provider_id = :uid AND pr.status = 'accepted'
            ORDER BY p.created_at DESC LIMIT 1
        ");
        $p_stmt->execute([':uid' => $user_id]);
        $project_data = $p_stmt->fetch(PDO::FETCH_ASSOC);
        if ($project_data) {
            $contract = $project_data;
            $contract_id = $contract['id'];
            $m_stmt = $db->prepare("SELECT * FROM milestones WHERE project_id = ? ORDER BY id ASC");
            $m_stmt->execute([$contract_id]);
            $milestones = $m_stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (\Exception $e) {
    $contract = null;
}

// Fallback high-fidelity sample data if viewing demo / empty database
if (!$contract) {
    $is_sample = true;
    if ($contract_id === 202) {
        $contract = [
            'id' => 202,
            'title' => 'FinTech Mobile Dashboard UI/UX Design System',
            'category' => 'UI/UX Design',
            'description' => 'Comprehensive mobile dashboard design system tailored for banking and wealth management workflows. Deliverables include component library, dark/light modes, user flow documentation, and responsive click-through prototypes.',
            'created_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
            'contract_started' => date('Y-m-d H:i:s', strtotime('-3 days')),
            'project_status' => 'in_progress',
            'client_name' => 'Kuda Capital Partners',
            'client_email' => 'product@kudacapital.com',
            'client_avatar' => null,
            'bid_amount' => 280000,
        ];
        $milestones = [
            [
                'id' => 601,
                'title' => 'Milestone 1: Wireframes & High-Fidelity Design Tokens',
                'amount' => 140000,
                'status' => 'pending',
                'due_date' => date('Y-m-d', strtotime('+6 days')),
                'description' => 'Low and high fidelity wireframes for core transaction views and responsive design tokens.'
            ],
            [
                'id' => 602,
                'title' => 'Milestone 2: Interactive Figma Prototypes & Developer Specs',
                'amount' => 140000,
                'status' => 'pending',
                'due_date' => date('Y-m-d', strtotime('+16 days')),
                'description' => 'Full interactive click-through prototype and developer handover documentation.'
            ]
        ];
    } else {
        $contract = [
            'id' => 201,
            'title' => 'Enterprise E-Commerce API Integration & Security Audit',
            'category' => 'Web Development',
            'description' => 'Full architectural integration of payment webhooks, authentication tokens, load balancing, and multi-tier escrow endpoints for our high-volume digital merchant platform.',
            'created_at' => date('Y-m-d H:i:s', strtotime('-12 days')),
            'contract_started' => date('Y-m-d H:i:s', strtotime('-10 days')),
            'project_status' => 'in_progress',
            'client_name' => 'Aura Logistics Ltd',
            'client_email' => 'tech@auralogistics.com',
            'client_avatar' => null,
            'bid_amount' => 450000,
        ];
        $milestones = [
            [
                'id' => 501,
                'title' => 'Milestone 1: Architecture Blueprint & Auth Endpoints',
                'amount' => 150000,
                'status' => 'approved',
                'due_date' => date('Y-m-d', strtotime('-5 days')),
                'description' => 'Complete schema documentation and secure JWT authorization endpoints.'
            ],
            [
                'id' => 502,
                'title' => 'Milestone 2: Payment Gateway & Webhook Event Listeners',
                'amount' => 150000,
                'status' => 'submitted',
                'due_date' => date('Y-m-d', strtotime('+2 days')),
                'description' => 'Live gateway integration with real-time idempotency webhook verification.'
            ],
            [
                'id' => 503,
                'title' => 'Milestone 3: End-to-End Stress Test & Staging Deployment',
                'amount' => 150000,
                'status' => 'pending',
                'due_date' => date('Y-m-d', strtotime('+12 days')),
                'description' => 'Load testing under 10k concurrent requests and final staging sign-off.'
            ]
        ];
    }
}

// Calculate milestones statistics
$total_milestones = count($milestones);
$cleared_milestones = 0;
$cleared_amount = 0;
$in_review_amount = 0;
$pending_amount = 0;

foreach ($milestones as $m) {
    if ($m['status'] === 'approved' || $m['status'] === 'paid') {
        $cleared_milestones++;
        $cleared_amount += (float)$m['amount'];
    } elseif ($m['status'] === 'submitted') {
        $in_review_amount += (float)$m['amount'];
    } else {
        $pending_amount += (float)$m['amount'];
    }
}

$progress_pct = $total_milestones > 0 ? round(($cleared_milestones / $total_milestones) * 100) : 0;

$client_initials = '';
foreach (explode(' ', trim($contract['client_name'] ?? 'Client')) as $p) {
    $client_initials .= strtoupper(substr($p, 0, 1));
}
$client_initials = substr($client_initials, 0, 2);
?>

<!-- Vertical Navigation -->
<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Content Area -->
<div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
    <?php include __DIR__ . '/components/header.php'; ?>
    <main class="flex-1 px-4 sm:px-8 lg:px-12 py-6 pb-36 sm:pb-16 space-y-6 sm:space-y-7 max-w-[1600px] mx-auto w-full">
    
    <!-- ---------------------------------------------------------------------
         1. TOP NAV & BREADCRUMBS (Fully Mobile Responsive)
         --------------------------------------------------------------------- -->
    <div class="space-y-4">
        <!-- Back Action Link -->
        <div>
            <a href="contracts.php" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 text-[#1952E1] font-bold text-xs rounded-[4px] shadow-2xs hover:bg-blue-50 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                <span>Back to Contracts</span>
            </a>
        </div>

        <div class="flex flex-col md:flex-row justify-between md:items-start gap-4">
            <div class="space-y-2 flex-1 min-w-0">
                <!-- Metadata Chips Row -->
                <div class="flex items-center gap-2 flex-wrap text-xs">
                    <span class="px-2.5 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold tracking-wider rounded-[4px] uppercase">
                        <?= htmlspecialchars($contract['category'] ?? 'UI/UX DESIGN') ?>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-[4px]">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>ESCROW ACTIVE</span>
                    </span>
                    <span class="text-[11px] text-slate-400 font-medium">
                        Ref: CTR-<?= str_pad($contract['id'], 5, '0', STR_PAD_LEFT) ?> • Started <?= date('M j, Y', strtotime($contract['contract_started'] ?? $contract['created_at'])) ?>
                    </span>
                </div>

                <!-- Main Project Title -->
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight font-heading leading-snug break-words">
                    <?= htmlspecialchars($contract['title']) ?>
                </h1>
            </div>

            <!-- Client Message CTA -->
            <div class="flex items-center gap-2.5 shrink-0 w-full sm:w-auto">
                <a href="messages.php" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs rounded-[4px] shadow-2xs transition-all inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span>Message Client</span>
                </a>
            </div>
        </div>
    </div>

    <!-- =====================================================================
         SKELETON LOADING CONTAINER (Displayed on initial load)
         ===================================================================== -->
    <div id="detail-skeleton-view" class="space-y-7 transition-opacity duration-300">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-8 space-y-6">
                <!-- Skeleton Scope Brief -->
                <div class="bg-white rounded-[3px] p-6 border border-slate-200/80 shadow-xs space-y-3 animate-pulse">
                    <div class="h-3.5 bg-slate-200 rounded w-48"></div>
                    <div class="space-y-2 pt-2">
                        <div class="h-3.5 bg-slate-100 rounded w-full"></div>
                        <div class="h-3.5 bg-slate-100 rounded w-5/6"></div>
                    </div>
                </div>
                <!-- Skeleton Milestones -->
                <div class="bg-white rounded-[3px] p-6 border border-slate-200/80 shadow-xs space-y-4 animate-pulse">
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                        <div class="h-4 bg-slate-200 rounded w-44"></div>
                        <div class="h-3 bg-slate-100 rounded w-28"></div>
                    </div>
                    <div class="h-2 bg-slate-100 rounded w-full"></div>
                    <div class="space-y-3 pt-2">
                        <div class="h-20 bg-slate-50 border border-slate-100 rounded-[3px]"></div>
                        <div class="h-20 bg-slate-50 border border-slate-100 rounded-[3px]"></div>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-[3px] p-6 border border-slate-200/80 shadow-xs space-y-4 animate-pulse">
                    <div class="h-3 bg-slate-200 rounded w-28"></div>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-slate-200"></div>
                        <div class="space-y-1.5 flex-1">
                            <div class="h-3.5 bg-slate-200 rounded w-28"></div>
                            <div class="h-3 bg-slate-100 rounded w-36"></div>
                        </div>
                    </div>
                    <div class="h-8 bg-slate-100 rounded w-full"></div>
                </div>
                <div class="bg-white rounded-[3px] p-6 border border-slate-200/80 shadow-xs space-y-3 animate-pulse">
                    <div class="h-3 bg-slate-200 rounded w-32"></div>
                    <div class="h-4 bg-slate-100 rounded w-full"></div>
                    <div class="h-4 bg-slate-100 rounded w-full"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- =====================================================================
         REAL CONTENT WORKSPACE (Smoothly revealed after skeleton transition)
         ===================================================================== -->
    <div id="detail-content-view" class="space-y-7 hidden opacity-0 transition-opacity duration-300">

    <!-- ---------------------------------------------------------------------
         2. WORKSPACE 2-COLUMN LAYOUT (Wide Content + Context Sidebar)
         --------------------------------------------------------------------- -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start pb-8">
        
        <!-- Left 8 Columns: Scope Brief & Interactive Milestones List -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Project Scope Brief Box -->
            <div class="bg-white rounded-[3px] p-5 sm:p-6 border border-slate-200/90 shadow-sm space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#1952E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Project Scope & Specifications</span>
                    </h2>
                    <span class="text-[11px] text-slate-400 font-medium">Original Client Brief</span>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                    <?= nl2br(htmlspecialchars($contract['description'] ?? 'Work under this contract will proceed according to agreed milestones funded in escrow.')) ?>
                </p>
            </div>

            <!-- Milestone Deliverables Manager -->
            <div class="bg-white rounded-[3px] p-5 sm:p-6 border border-slate-200/90 shadow-sm space-y-5">
                
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 font-heading">Milestone Deliverables & Escrow</h2>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">Submit completed work for client inspection. 10-day review auto-release policy applies.</p>
                    </div>
                    <div class="text-xs font-bold text-slate-500 shrink-0">
                        <span class="text-[#1952E1] font-black"><?= $cleared_milestones ?></span> of <?= $total_milestones ?> Completed (<?= $progress_pct ?>%)
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full h-2.5 bg-slate-100 rounded-[3px] overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-600 to-emerald-500 rounded-[3px] transition-all duration-500" style="width: <?= $progress_pct ?>%"></div>
                </div>

                <!-- Milestones Timeline List -->
                <div class="space-y-4 pt-2">
                    <?php foreach ($milestones as $idx => $m): 
                        $m_no = $idx + 1;
                        $status = $m['status'] ?? 'pending';
                        $m_due = !empty($m['due_date']) ? date('M j, Y', strtotime($m['due_date'])) : 'Open Timeline';
                    ?>
                    <div class="p-4 sm:p-5 rounded-[3px] border <?= ($status === 'submitted') ? 'border-purple-200 bg-purple-50/30' : (($status === 'approved' || $status === 'paid') ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200/80 bg-slate-50/40') ?> space-y-3.5 transition-all">
                        
                        <div class="flex flex-col sm:flex-row justify-between sm:items-start gap-3">
                            <div class="flex items-start gap-3">
                                <!-- Status Icon -->
                                <?php if ($status === 'approved' || $status === 'paid'): ?>
                                    <div class="w-7 h-7 rounded-[3px] bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                <?php elseif ($status === 'submitted'): ?>
                                    <div class="w-7 h-7 rounded-[3px] bg-purple-100 text-purple-700 flex items-center justify-center shrink-0 mt-0.5 animate-pulse">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                <?php else: ?>
                                    <div class="w-7 h-7 rounded-[3px] bg-slate-200 text-slate-700 text-xs font-black flex items-center justify-center shrink-0 mt-0.5">
                                        <?= $m_no ?>
                                    </div>
                                <?php endif; ?>

                                <div>
                                    <h3 class="text-sm font-extrabold text-slate-900 font-heading">
                                        <?= htmlspecialchars($m['title']) ?>
                                    </h3>
                                    <?php if (!empty($m['description'])): ?>
                                        <p class="text-xs text-slate-500 font-normal mt-0.5"><?= htmlspecialchars($m['description']) ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="flex items-center gap-3 text-[11px] text-slate-500 font-medium mt-1.5 flex-wrap">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Due: <?= $m_due ?>
                                        </span>
                                        <span class="text-slate-300">•</span>
                                        <span class="text-slate-800 font-bold">Escrow Payout: ₦<?= number_format($m['amount']) ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Pill & Action Button -->
                            <div class="flex items-center gap-2.5 shrink-0 self-start sm:self-auto pl-10 sm:pl-0">
                                <?php if ($status === 'approved' || $status === 'paid'): ?>
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 text-emerald-800 border border-emerald-200 text-[10px] font-extrabold rounded-[3px] uppercase tracking-wider">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Funds Cleared
                                    </span>
                                <?php elseif ($status === 'submitted'): ?>
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-50 text-purple-800 border border-purple-200 text-[10px] font-extrabold rounded-[3px] uppercase tracking-wider">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        In Client Review (10-Day Window)
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-extrabold rounded-[3px] uppercase tracking-wider">
                                        In Progress
                                    </span>
                                    <button type="button" class="btn-submit-work px-4 py-1.5 bg-[#1952E1] hover:bg-blue-700 text-white font-extrabold text-xs rounded-[3px] transition-all hover:scale-105 active:scale-95 cursor-pointer shadow-xs inline-flex items-center gap-1.5" 
                                            data-id="<?= $m['id'] ?>" 
                                            data-title="<?= htmlspecialchars($m['title']) ?>"
                                            data-amount="<?= number_format($m['amount']) ?>">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        <span>Submit Work</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                    <?php endforeach; ?>
                </div>

            </div>

            <!-- Escrow Protection Rules Notice -->
            <div class="bg-blue-50/70 border border-blue-200/80 rounded-[3px] p-4 sm:p-5 flex items-start gap-3.5">
                <div class="w-8 h-8 rounded-[3px] bg-blue-100 text-[#1952E1] flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div class="space-y-1 text-xs text-blue-900">
                    <h4 class="font-extrabold font-heading">Scriptly Escrow Protection & 10-Day Inspection Rule</h4>
                    <p class="text-blue-800 leading-relaxed font-medium text-[11px]">
                        All contract funds are pre-funded into escrow prior to project kickoff. Once you submit a milestone deliverable, the client has a 10-day inspection period to review and accept the work. If no dispute is filed within 10 days, funds are automatically released to your wallet.
                    </p>
                </div>
            </div>

        </div>

        <!-- Right 4 Columns: Client Information & Financial Breakdown -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Client Profile Box -->
            <div class="bg-white rounded-[3px] p-5 sm:p-6 border border-slate-200/90 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                    Client Details
                </h3>

                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-full bg-slate-900 text-white font-extrabold text-sm flex items-center justify-center shrink-0 shadow-xs">
                        <?= htmlspecialchars($client_initials) ?>
                    </div>
                    <div class="space-y-0.5">
                        <h4 class="text-sm font-extrabold text-slate-900 font-heading"><?= htmlspecialchars($contract['client_name']) ?></h4>
                        <p class="text-xs text-slate-400 font-medium"><?= htmlspecialchars($contract['client_email'] ?? 'Verified Enterprise Client') ?></p>
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Payment Method Verified
                        </span>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="messages.php" class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-[3px] transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Open Direct Chat</span>
                    </a>
                </div>
            </div>

            <!-- Financial Summary Box -->
            <div class="bg-white rounded-[3px] p-5 sm:p-6 border border-slate-200/90 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                    Escrow Financials
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between items-center text-slate-500">
                        <span>Total Contract Bid</span>
                        <span class="font-black text-slate-900 font-heading text-sm">₦<?= number_format($contract['bid_amount']) ?></span>
                    </div>
                    <div class="flex justify-between items-center text-slate-500">
                        <span>Funds in Escrow</span>
                        <span class="font-bold text-blue-600">₦<?= number_format($contract['bid_amount'] - $cleared_amount) ?></span>
                    </div>
                    <div class="flex justify-between items-center text-slate-500">
                        <span>Cleared & Paid Out</span>
                        <span class="font-bold text-emerald-600">₦<?= number_format($cleared_amount) ?></span>
                    </div>
                    
                    <div class="pt-3 border-t border-slate-100 flex justify-between items-center">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Net Take-Home (95%)</span>
                            <span class="text-sm font-black text-slate-950 font-heading">₦<?= number_format($contract['bid_amount'] * 0.95) ?></span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium">5% platform fee</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>

</main>
</div>

<!-- Deliverable Submission Modal -->
<div id="submit-modal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-[3px] max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200/90 space-y-5">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-[#1952E1] tracking-wider">Milestone Deliverable</span>
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900 font-heading" id="submit-modal-title">Milestone Title</h3>
                </div>
            </div>
            <button type="button" id="btn-close-modal" class="text-slate-400 hover:text-slate-600 font-bold p-1">✕</button>
        </div>

        <form id="deliverable-form" class="space-y-4">
            <input type="hidden" id="modal-milestone-id">

            <div class="p-3 bg-blue-50/70 border border-blue-200/70 rounded-[3px] text-xs text-blue-900 space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#1952E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Escrow Release & 10-Day Review Rule</span>
                </div>
                <p class="text-[11px] text-blue-800 leading-relaxed font-medium">
                    Once submitted, the client is given 10 calendar days to inspect your work and approve release. If no dispute is raised within 10 days, funds are automatically released to your wallet.
                </p>
            </div>

            <div class="space-y-1.5">
                <label for="deliverable_notes" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Completion Summary & Handover Notes</label>
                <textarea id="deliverable_notes" rows="4" placeholder="Describe the finished deliverables, test credentials, or milestone summary..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white placeholder-slate-400 resize-none"></textarea>
            </div>

            <div class="space-y-1.5">
                <label for="deliverable_link" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Deliverable Link or Repository URL (Optional)</label>
                <input type="url" id="deliverable_link" placeholder="e.g. https://github.com/... or https://figma.com/file/..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white placeholder-slate-400">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" id="btn-cancel-submit" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-[3px] transition-colors">Cancel</button>
                <button type="button" id="btn-confirm-submit" class="px-6 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-extrabold text-xs rounded-[3px] transition-all shadow-sm hover:scale-105 active:scale-95">Confirm Submission →</button>
            </div>
        </form>

    </div>
</div>

<!-- Mobile Bottom Dock -->
<?php include __DIR__ . '/components/bottom-nav.php'; ?>

<!-- Scripts -->
<?php include __DIR__ . '/components/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Skeleton Shimmer to Content Transition Handler
    const skeletonView = document.getElementById('detail-skeleton-view');
    const contentView = document.getElementById('detail-content-view');

    setTimeout(() => {
        if (skeletonView && contentView) {
            skeletonView.classList.add('opacity-0');
            setTimeout(() => {
                skeletonView.style.display = 'none';
                contentView.classList.remove('hidden');
                setTimeout(() => {
                    contentView.classList.remove('opacity-0');
                }, 30);
            }, 250);
        }
    }, 400);

    const submitModal = document.getElementById('submit-modal');
    const submitModalTitle = document.getElementById('submit-modal-title');
    const modalMilestoneId = document.getElementById('modal-milestone-id');
    const btnConfirmSubmit = document.getElementById('btn-confirm-submit');
    const btnCancelSubmit = document.getElementById('btn-cancel-submit');
    const btnCloseModal = document.getElementById('btn-close-modal');
    
    let activeMilestoneId = null;

    document.querySelectorAll('.btn-submit-work').forEach(btn => {
        btn.addEventListener('click', () => {
            activeMilestoneId = btn.dataset.id;
            modalMilestoneId.value = activeMilestoneId;
            submitModalTitle.textContent = btn.dataset.title;
            submitModal.classList.remove('hidden');
        });
    });

    const hideModal = () => {
        submitModal.classList.add('hidden');
        activeMilestoneId = null;
    };

    if (btnCancelSubmit) btnCancelSubmit.addEventListener('click', hideModal);
    if (btnCloseModal) btnCloseModal.addEventListener('click', hideModal);

    if (btnConfirmSubmit) {
        btnConfirmSubmit.addEventListener('click', async () => {
            if (!activeMilestoneId) return;

            btnConfirmSubmit.textContent = 'Submitting...';
            btnConfirmSubmit.disabled = true;

            try {
                const response = await fetch('../../api/provider/submit-milestone.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ milestone_id: parseInt(activeMilestoneId) })
                });

                const data = await response.json();
                if (data.success) {
                    if (typeof ScriptlyToast !== 'undefined') {
                        ScriptlyToast.success('Milestone deliverable submitted successfully! Client has been notified.', 'Submitted!');
                    } else {
                        alert('Milestone deliverable submitted successfully! Client has been notified.');
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    if (typeof ScriptlyToast !== 'undefined') {
                        ScriptlyToast.error(data.message || 'Error submitting deliverable.');
                    } else {
                        alert(data.message || 'Error submitting deliverable.');
                    }
                    btnConfirmSubmit.textContent = 'Confirm Submission →';
                    btnConfirmSubmit.disabled = false;
                }
            } catch (err) {
                if (typeof ScriptlyToast !== 'undefined') {
                    ScriptlyToast.error('Network error. Please try again.');
                } else {
                    alert('Network error. Please try again.');
                }
                btnConfirmSubmit.textContent = 'Confirm Submission →';
                btnConfirmSubmit.disabled = false;
            }
        });
    }
});
</script>
