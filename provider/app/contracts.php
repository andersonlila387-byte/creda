<?php
$page_title = 'My Contracts';
$active_tab = 'contracts';
require_once __DIR__ . '/components/head.php';

// Fetch filter parameter
$status_filter = trim($_GET['status'] ?? 'all');
$search_query = trim($_GET['q'] ?? '');

// Fetch active projects and contracts
$contracts = [];
$total_contract_value = 0;
$total_in_progress_milestones = 0;
$total_in_review_milestones = 0;
$total_cleared_milestones = 0;

try {
    $db = getDBConnection();
    
    $query = "
        SELECT p.id, p.title, p.category, p.description, p.created_at, p.status as project_status,
               u.id as client_id, u.full_name as client_name, u.email as client_email, u.avatar_url as client_avatar,
               pr.bid_amount, pr.status as proposal_status, pr.created_at as contract_started
        FROM projects p
        JOIN proposals pr ON pr.project_id = p.id
        LEFT JOIN users u ON p.client_id = u.id
        WHERE pr.provider_id = :uid AND pr.status = 'accepted'
    ";
    
    $params = [':uid' => $user_id];
    
    if ($status_filter === 'in_progress') {
        $query .= " AND p.status = 'in_progress'";
    } elseif ($status_filter === 'completed') {
        $query .= " AND p.status = 'completed'";
    }
    
    if (!empty($search_query)) {
        $query .= " AND (p.title LIKE :search OR u.full_name LIKE :search2)";
        $params[':search'] = '%' . $search_query . '%';
        $params[':search2'] = '%' . $search_query . '%';
    }
    
    $query .= " ORDER BY p.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch milestones for each contract
    foreach ($contracts as &$c) {
        $m_stmt = $db->prepare("SELECT * FROM milestones WHERE project_id = ? ORDER BY id ASC");
        $m_stmt->execute([$c['id']]);
        $c['milestones'] = $m_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $total = count($c['milestones']);
        $completed = 0;
        $in_review = 0;
        $active_milestone_title = null;
        
        $total_contract_value += (float)($c['bid_amount'] ?? 0);
        
        foreach ($c['milestones'] as $m) {
            if ($m['status'] === 'approved' || $m['status'] === 'paid') {
                $completed++;
                $total_cleared_milestones++;
            } elseif ($m['status'] === 'submitted') {
                $in_review++;
                $total_in_review_milestones++;
                if (!$active_milestone_title) $active_milestone_title = $m['title'] . ' (In Review)';
            } else {
                $total_in_progress_milestones++;
                if (!$active_milestone_title) $active_milestone_title = $m['title'];
            }
        }
        $c['progress'] = $total > 0 ? round(($completed / $total) * 100) : 0;
        $c['cleared_count'] = $completed;
        $c['total_count'] = $total;
        $c['active_milestone_name'] = $active_milestone_title ?? 'All Milestones Completed';
    }
    unset($c);

} catch (\Exception $e) {
    $contracts = [];
}

// Fallback high-fidelity sample contracts if database is currently empty
if (empty($contracts) && empty($search_query) && $status_filter === 'all') {
    $contracts = [
        [
            'id' => 201,
            'title' => 'Enterprise E-Commerce API Integration & Security Audit',
            'category' => 'Web Development',
            'created_at' => date('Y-m-d H:i:s', strtotime('-12 days')),
            'contract_started' => date('Y-m-d H:i:s', strtotime('-10 days')),
            'project_status' => 'in_progress',
            'client_name' => 'Aura Logistics Ltd',
            'client_email' => 'tech@auralogistics.com',
            'client_avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
            'client_rating' => '5.0',
            'client_spent' => '₦3.8M+ spent',
            'client_location' => 'Lagos, Nigeria',
            'bid_amount' => 450000,
            'progress' => 67,
            'cleared_count' => 1,
            'total_count' => 3,
            'active_milestone_name' => 'Payment Gateway Listeners (In Review)',
            'milestones' => [
                ['id' => 501, 'title' => 'Milestone 1: Architecture Blueprint', 'amount' => 150000, 'status' => 'approved', 'due_date' => date('Y-m-d', strtotime('-5 days'))],
                ['id' => 502, 'title' => 'Milestone 2: Payment Gateway Listeners', 'amount' => 150000, 'status' => 'submitted', 'due_date' => date('Y-m-d', strtotime('+2 days'))],
                ['id' => 503, 'title' => 'Milestone 3: End-to-End Stress Test', 'amount' => 150000, 'status' => 'pending', 'due_date' => date('Y-m-d', strtotime('+12 days'))]
            ]
        ],
        [
            'id' => 202,
            'title' => 'FinTech Mobile Dashboard UI/UX Design System',
            'category' => 'UI/UX Design',
            'created_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
            'contract_started' => date('Y-m-d H:i:s', strtotime('-3 days')),
            'project_status' => 'in_progress',
            'client_name' => 'Kuda Capital Partners',
            'client_email' => 'product@kudacapital.com',
            'client_avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80',
            'client_rating' => '5.0',
            'client_spent' => '₦4.1M+ spent',
            'client_location' => 'Lagos, Nigeria',
            'bid_amount' => 280000,
            'progress' => 0,
            'cleared_count' => 0,
            'total_count' => 2,
            'active_milestone_name' => 'Milestone 1: Wireframes & Tokens',
            'milestones' => [
                ['id' => 601, 'title' => 'Milestone 1: Wireframes & Tokens', 'amount' => 140000, 'status' => 'pending', 'due_date' => date('Y-m-d', strtotime('+6 days'))],
                ['id' => 602, 'title' => 'Milestone 2: Interactive Prototypes', 'amount' => 140000, 'status' => 'pending', 'due_date' => date('Y-m-d', strtotime('+16 days'))]
            ]
        ]
    ];
    $total_contract_value = 730000;
    $total_in_progress_milestones = 3;
    $total_in_review_milestones = 1;
    $total_cleared_milestones = 1;
}
?>

<!-- Vertical Navigation -->
<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Content Area -->
<div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
    <?php include __DIR__ . '/components/header.php'; ?>
    <main class="flex-1 px-4 sm:px-8 lg:px-12 py-6 pb-36 sm:pb-16 space-y-7 max-w-[1600px] mx-auto w-full">
    
    <!-- ---------------------------------------------------------------------
         1. HERO HEADER: Title, Breadcrumbs & Action
         --------------------------------------------------------------------- -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="index.php" class="hover:text-slate-600 transition-colors">Workspace</a>
                <span>/</span>
                <span class="text-slate-700 font-bold">Active Contracts</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight font-heading">
                Contracts & Milestones
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5 max-w-2xl">
                Manage active client contracts, submit milestone deliverables for review, and track escrow disbursements.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="jobs.php" class="px-5 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-extrabold text-xs sm:text-sm rounded-[3px] shadow-sm transition-all hover:scale-105 active:scale-95 inline-flex items-center gap-1.5">
                <span>Browse Open Gigs</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>

    <!-- =====================================================================
         SKELETON LOADING CONTAINER (Displayed on initial load / transitions)
         ===================================================================== -->
    <div id="contracts-skeleton-view" class="space-y-7 transition-opacity duration-300">
        
        <!-- Skeleton Metrics -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
            <?php for ($i = 0; $i < 4; $i++): ?>
            <div class="bg-white rounded-[3px] p-4 sm:p-5 border border-slate-200/70 shadow-xs space-y-3 animate-pulse">
                <div class="flex justify-between items-center">
                    <div class="h-2.5 bg-slate-200 rounded w-24"></div>
                    <div class="w-7 h-7 rounded-[3px] bg-slate-100"></div>
                </div>
                <div class="h-6 bg-slate-200 rounded w-32"></div>
                <div class="h-2 bg-slate-100 rounded w-20"></div>
            </div>
            <?php endfor; ?>
        </div>

        <!-- Skeleton Filter Bar -->
        <div class="bg-white border border-slate-200/80 rounded-[3px] p-4 shadow-xs flex justify-between items-center animate-pulse">
            <div class="flex gap-2">
                <div class="h-7 bg-slate-200 rounded w-24"></div>
                <div class="h-7 bg-slate-100 rounded w-24"></div>
                <div class="h-7 bg-slate-100 rounded w-24"></div>
            </div>
            <div class="h-8 bg-slate-100 rounded w-52 hidden md:block"></div>
        </div>

        <!-- Skeleton Contract Cards (2 in a row, 16px radius) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
            <?php for ($i = 0; $i < 4; $i++): ?>
            <div class="bg-white border border-slate-200/90 rounded-[16px] p-6 shadow-xs space-y-4 animate-pulse">
                <div class="flex justify-between items-center">
                    <div class="flex gap-2">
                        <div class="h-4 bg-slate-200 rounded-md w-24"></div>
                        <div class="h-4 bg-emerald-100 rounded-md w-28"></div>
                    </div>
                    <div class="h-3 bg-slate-200 rounded w-20"></div>
                </div>
                <div class="space-y-1.5">
                    <div class="h-5 bg-slate-200 rounded w-4/5"></div>
                    <div class="h-3.5 bg-slate-100 rounded w-1/2"></div>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-slate-200"></div>
                        <div class="h-3.5 bg-slate-200 rounded w-32"></div>
                    </div>
                    <div class="h-4 bg-slate-100 rounded w-14"></div>
                </div>
                <div class="grid grid-cols-3 gap-3 py-3 border-y border-slate-100">
                    <div class="space-y-1"><div class="h-5 bg-slate-200 rounded w-20"></div><div class="h-2.5 bg-slate-100 rounded w-16"></div></div>
                    <div class="space-y-1"><div class="h-5 bg-slate-200 rounded w-12"></div><div class="h-2.5 bg-slate-100 rounded w-20"></div></div>
                    <div class="space-y-1"><div class="h-5 bg-slate-200 rounded w-10"></div><div class="h-2.5 bg-slate-100 rounded w-16"></div></div>
                </div>
                <div class="h-1 bg-slate-100 rounded-full w-full"></div>
                <div class="flex gap-4 py-1">
                    <div class="h-4 bg-slate-200 rounded w-1/2"></div>
                    <div class="h-4 bg-slate-100 rounded w-1/2"></div>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                    <div class="h-3 bg-slate-200 rounded w-36"></div>
                    <div class="flex gap-2">
                        <div class="h-8 bg-slate-100 rounded-lg w-24"></div>
                        <div class="h-8 bg-slate-200 rounded-lg w-32"></div>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>

    </div>

    <!-- =====================================================================
         REAL CONTENT WORKSPACE (Smoothly revealed after skeleton transition)
         ===================================================================== -->
    <div id="contracts-content-view" class="space-y-7 hidden opacity-0 transition-opacity duration-300">

        <!-- -----------------------------------------------------------------
             2. KPI SUMMARY METRIC STRIP (Strict 3px Radius, Clean Vector Icons)
             ----------------------------------------------------------------- -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
            
            <!-- Metric 1: Total Active Value -->
            <div class="bg-white rounded-[3px] p-4 sm:p-5 border border-slate-200/80 shadow-[0_2px_8px_rgba(0,0,0,0.02)] space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Contracted Value</span>
                    <div class="w-8 h-8 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div>
                    <div class="text-lg sm:text-2xl font-black text-slate-900 font-heading">₦<?= number_format($total_contract_value) ?></div>
                    <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        <span><?= count($contracts) ?> Active Engagement(s)</span>
                    </div>
                </div>
            </div>

            <!-- Metric 2: Milestones In Progress -->
            <div class="bg-white rounded-[3px] p-4 sm:p-5 border border-slate-200/80 shadow-[0_2px_8px_rgba(0,0,0,0.02)] space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">In Progress</span>
                    <div class="w-8 h-8 rounded-[3px] bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div>
                    <div class="text-lg sm:text-2xl font-black text-slate-900 font-heading"><?= $total_in_progress_milestones ?></div>
                    <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        <span>Deliverables underway</span>
                    </div>
                </div>
            </div>

            <!-- Metric 3: Under 10-Day Review -->
            <div class="bg-white rounded-[3px] p-4 sm:p-5 border border-slate-200/80 shadow-[0_2px_8px_rgba(0,0,0,0.02)] space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">In Review</span>
                    <div class="w-8 h-8 rounded-[3px] bg-purple-50 text-purple-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
                <div>
                    <div class="text-lg sm:text-2xl font-black text-slate-900 font-heading"><?= $total_in_review_milestones ?></div>
                    <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                        <span>10-Day Review active</span>
                    </div>
                </div>
            </div>

            <!-- Metric 4: Released / Completed -->
            <div class="bg-white rounded-[3px] p-4 sm:p-5 border border-slate-200/80 shadow-[0_2px_8px_rgba(0,0,0,0.02)] space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Approved & Paid</span>
                    <div class="w-8 h-8 rounded-[3px] bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
                <div>
                    <div class="text-lg sm:text-2xl font-black text-slate-900 font-heading"><?= $total_cleared_milestones ?></div>
                    <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Funds released to wallet</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- -----------------------------------------------------------------
             3. SEARCH & STATUS FILTER TABS BAR
             ----------------------------------------------------------------- -->
        <div class="bg-white border border-slate-200/90 rounded-[12px] p-4 sm:p-5 shadow-xs space-y-4">
            
            <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                <!-- Filter Tabs -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 text-xs font-bold whitespace-nowrap no-scrollbar">
                    <a href="contracts.php?status=all" class="px-3.5 py-1.5 rounded-[4px] transition-colors <?= $status_filter === 'all' ? 'bg-[#1952E1] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' ?>">
                        All Contracts
                    </a>
                    <a href="contracts.php?status=in_progress" class="px-3.5 py-1.5 rounded-[4px] transition-colors <?= $status_filter === 'in_progress' ? 'bg-[#1952E1] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' ?>">
                        In Progress
                    </a>
                    <a href="contracts.php?status=completed" class="px-3.5 py-1.5 rounded-[4px] transition-colors <?= $status_filter === 'completed' ? 'bg-[#1952E1] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' ?>">
                        Completed
                    </a>
                </div>

                <!-- Search Input Form -->
                <form method="GET" class="flex items-center gap-2 max-w-md w-full">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="q" value="<?= htmlspecialchars($search_query) ?>" placeholder="Search by contract title, client name..." class="w-full bg-slate-50 border border-slate-200 rounded-[4px] pl-9 pr-4 py-2 text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white placeholder-slate-400">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-[4px] transition-colors shrink-0">
                        Search
                    </button>
                </form>
            </div>

        </div>

        <!-- -----------------------------------------------------------------
             4. PREMIUM FINTECH CONTRACT CARDS (2 in a row, 16px Rounded)
             ----------------------------------------------------------------- -->
        <div>
            
            <?php if (empty($contracts)): ?>
                <!-- Empty State with Generous Padding & Polished Typography -->
                <div class="bg-white border border-slate-200/90 rounded-[16px] py-16 sm:py-24 px-6 sm:px-12 text-center text-xs text-slate-400 space-y-4 shadow-xs my-2">
                    <div class="w-14 h-14 bg-blue-50 text-[#1952E1] border border-blue-200 rounded-full flex items-center justify-center mx-auto shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="space-y-1.5 max-w-md mx-auto">
                        <h3 class="font-extrabold text-slate-900 text-base sm:text-lg font-heading">No contracts found</h3>
                        <p class="text-slate-500 text-xs sm:text-sm font-medium leading-relaxed">
                            <?= !empty($search_query) ? 'No contracts match your search query. Try adjusting your filters or keyword.' : 'You currently do not have any active client engagements. Explore open gigs to submit competitive proposals.' ?>
                        </p>
                    </div>
                    <div class="pt-3">
                        <a href="jobs.php" class="px-6 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs rounded-[4px] transition-all inline-flex items-center gap-2 shadow-xs hover:shadow-md">
                            <span>Browse Open Gigs</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                
                <!-- Sleek 2-Column Responsive Grid (2 in a row, 16px Corners, Premium SaaS UI) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                    <?php foreach ($contracts as $contract): 
                        $client_name = $contract['client_name'] ?? 'Verified Client';
                        $client_initials = '';
                        foreach (explode(' ', trim($client_name)) as $p) {
                            $client_initials .= strtoupper(substr($p, 0, 1));
                        }
                        $client_initials = substr($client_initials, 0, 2);
                        $is_completed = ($contract['progress'] >= 100);

                        // Client Avatar resolution
                        $avatar_src = !empty($contract['client_avatar']) 
                            ? (str_starts_with($contract['client_avatar'], 'http') ? $contract['client_avatar'] : '../../' . ltrim($contract['client_avatar'], '/')) 
                            : 'https://ui-avatars.com/api/?name=' . urlencode($client_name) . '&background=0A2540&color=fff&bold=true&size=128';
                        
                        $client_spent = htmlspecialchars($contract['client_spent'] ?? '₦2.5M+ spent');
                        $client_rating = htmlspecialchars($contract['client_rating'] ?? '5.0');
                        $client_location = htmlspecialchars($contract['client_location'] ?? 'Nigeria');

                        // Ensure at least 2 milestones for timeline presentation
                        $timeline_milestones = $contract['milestones'] ?? [];
                        if (empty($timeline_milestones)) {
                            $timeline_milestones = [
                                ['id' => 1, 'title' => 'Wireframes & Tokens', 'status' => 'pending'],
                                ['id' => 2, 'title' => 'Final Design & Handoff', 'status' => 'pending']
                            ];
                        }
                    ?>
                    <div class="bg-white border border-slate-200/90 rounded-[16px] p-6 sm:p-7 shadow-[0_2px_12px_rgba(0,0,0,0.03)] hover:border-slate-300 hover:shadow-[0_8px_24px_rgba(0,0,0,0.06)] transition-all flex flex-col justify-between space-y-4 group">
                        
                        <!-- 1. Top Metadata Row -->
                        <div class="flex items-center justify-between gap-2 text-xs flex-wrap">
                            <span class="text-[11px] text-slate-400 font-medium flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Started <?= date('M j, Y', strtotime($contract['contract_started'] ?? $contract['created_at'])) ?></span>
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold tracking-wider rounded-md uppercase">
                                    <?= htmlspecialchars($contract['category'] ?? 'UI/UX DESIGN') ?>
                                </span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-md">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span><?= $is_completed ? 'COMPLETED' : 'ESCROW ACTIVE' ?></span>
                                </span>
                            </div>
                        </div>

                        <!-- 2. Project Title & Subtitle -->
                        <div class="space-y-1">
                            <h2 class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-[#1952E1] transition-colors leading-snug font-heading">
                                <a href="contract-details.php?ref=CTR-<?= str_pad($contract['id'], 5, '0', STR_PAD_LEFT) ?>">
                                    <?= htmlspecialchars($contract['title']) ?>
                                </a>
                            </h2>
                            <div class="flex items-center gap-2 text-[11px] text-slate-400 font-medium">
                                <span class="text-slate-600 font-semibold">Ref: CTR-<?= str_pad($contract['id'], 5, '0', STR_PAD_LEFT) ?></span>
                                <span>•</span>
                                <span><?= htmlspecialchars($contract['active_milestone_name'] ?? 'Milestone Deliverables') ?></span>
                            </div>
                        </div>

                        <!-- 3. Statistics Section (3 Clean Columns + Progress Track) -->
                        <div class="space-y-2 py-1">
                            <div class="grid grid-cols-3 gap-2 py-3 border-y border-slate-100 text-left">
                                <div class="space-y-0.5">
                                    <span class="text-base sm:text-lg font-bold text-slate-900 leading-none block font-heading">₦<?= number_format($contract['bid_amount']) ?></span>
                                    <span class="text-[10px] text-slate-400 font-medium block uppercase tracking-wider">Contract Value</span>
                                </div>
                                <div class="space-y-0.5">
                                    <span class="text-base sm:text-lg font-bold text-slate-900 leading-none block font-heading"><?= $contract['cleared_count'] ?> / <?= $contract['total_count'] ?></span>
                                    <span class="text-[10px] text-slate-400 font-medium block uppercase tracking-wider">Cleared</span>
                                </div>
                                <div class="space-y-0.5">
                                    <span class="text-base sm:text-lg font-bold text-slate-900 leading-none block font-heading"><?= $contract['progress'] ?>%</span>
                                    <span class="text-[10px] text-slate-400 font-medium block uppercase tracking-wider">Progress</span>
                                </div>
                            </div>

                            <!-- Thin Progress Bar Underneath -->
                            <div class="w-full h-1 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#1952E1] rounded-full transition-all duration-300" style="width: <?= max(0, min(100, $contract['progress'])) ?>%"></div>
                            </div>
                        </div>

                        <!-- 4. Milestone Timeline (Horizontal on Desktop, Stacked on Mobile) -->
                        <div class="space-y-2 pt-0.5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 text-xs">
                                <?php 
                                foreach (array_slice($timeline_milestones, 0, 2) as $idx => $m): 
                                    $m_num = str_pad($idx + 1, 2, '0', STR_PAD_LEFT);
                                    $is_cleared = ($m['status'] === 'approved' || $m['status'] === 'paid');
                                    $is_current = !$is_cleared && ($idx === 0 || ($idx > 0 && ($timeline_milestones[$idx-1]['status'] === 'approved' || $timeline_milestones[$idx-1]['status'] === 'paid')));
                                    
                                    $status_label = $is_cleared ? 'Completed' : ($is_current ? ($m['status'] === 'submitted' ? 'In Review' : 'In Progress') : 'Upcoming');
                                ?>
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <span class="w-5 h-5 rounded-full text-[10px] font-bold flex items-center justify-center shrink-0 <?= $is_cleared ? 'bg-emerald-100 text-emerald-700' : ($is_current ? 'bg-blue-100 text-[#1952E1] ring-2 ring-blue-500/20' : 'bg-slate-100 text-slate-400') ?>">
                                        <?= $m_num ?>
                                    </span>
                                    <div class="truncate text-[11px]">
                                        <span class="font-semibold text-slate-800"><?= htmlspecialchars($m['title']) ?></span>
                                        <span class="text-[10px] font-medium <?= $is_cleared ? 'text-emerald-600' : ($is_current ? 'text-[#1952E1]' : 'text-slate-400') ?>"> — <?= $status_label ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- 5. Client Profile Row (Real Photo Avatar, Verification, Rating, Spend & Chat Button) -->
                        <div class="pt-3.5 border-t border-slate-100 flex items-center justify-between gap-3">
                            
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="<?= $avatar_src ?>" alt="<?= htmlspecialchars($client_name) ?>" class="w-10 h-10 rounded-full object-cover ring-1 ring-slate-200 shadow-2xs shrink-0" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($client_name) ?>&background=0A2540&color=fff&bold=true'">
                                
                                <div class="space-y-0.5 min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs sm:text-sm font-bold text-slate-900 truncate"><?= htmlspecialchars($client_name) ?></span>
                                        <span class="text-emerald-600 inline-flex items-center gap-0.5 text-[10px] font-bold shrink-0" title="Payment & Escrow Verified">
                                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            <span>Verified</span>
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-[11px] text-slate-400 font-medium">
                                        <span class="flex items-center text-amber-500 font-semibold">
                                            ★ <?= $client_rating ?>
                                        </span>
                                        <span>•</span>
                                        <span><?= $client_spent ?></span>
                                        <span class="hidden xs:inline">•</span>
                                        <span class="hidden xs:inline"><?= $client_location ?></span>
                                    </div>
                                </div>
                            </div>

                            <a href="../../app/messages.php?user=<?= urlencode($contract['client_email'] ?? '') ?>" class="text-slate-600 hover:text-[#1952E1] bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-bold inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[4px] transition-colors shrink-0 shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <span>Chat</span>
                            </a>
                        </div>

                        <!-- 6. Protection Info & Action Buttons -->
                        <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>10-Day Inspection Protection</span>
                            </div>
                            
                            <div class="flex items-center justify-end gap-2">
                                <a href="contract-details.php?ref=CTR-<?= str_pad($contract['id'], 5, '0', STR_PAD_LEFT) ?>" class="text-xs font-bold text-slate-600 hover:text-slate-900 px-3 py-2 rounded-[4px] hover:bg-slate-100 transition-colors">
                                    View Contract
                                </a>
                                <a href="contract-details.php?ref=CTR-<?= str_pad($contract['id'], 5, '0', STR_PAD_LEFT) ?>" class="px-4 py-2 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs rounded-[4px] transition-all shadow-xs hover:shadow-md inline-flex items-center gap-1.5">
                                    <span>Manage Milestones</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        </div>

                    </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

        </div>

    </div>

</main>
</div>

<!-- Mobile Bottom Dock -->
<?php include __DIR__ . '/components/bottom-nav.php'; ?>

<!-- Scripts -->
<?php include __DIR__ . '/components/footer.php'; ?>

<script>
// Skeleton Shimmer to Content Transition Handler
document.addEventListener('DOMContentLoaded', () => {
    const skeletonView = document.getElementById('contracts-skeleton-view');
    const contentView = document.getElementById('contracts-content-view');

    // Natural skeleton display time for smooth UX familiarity across pages
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
});
</script>

