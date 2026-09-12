<?php
$page_title = 'Browse Open Gigs';
$active_tab = 'jobs';
require_once __DIR__ . '/components/head.php';

// Fetch Open Projects with filters
$search = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$tier_filter = trim($_GET['tier'] ?? 'all');
$sort = trim($_GET['sort'] ?? 'newest');

$projects = [];
$categories = [];

try {
    $db = getDBConnection();
    
    $query = "
        SELECT p.*, u.full_name as client_name, u.avatar_url as client_avatar,
               (SELECT COUNT(*) FROM proposals pr WHERE pr.project_id = p.id) as proposal_count,
               (SELECT COUNT(*) FROM proposals pr2 WHERE pr2.project_id = p.id AND pr2.provider_id = :uid_check) as has_applied
        FROM projects p
        LEFT JOIN users u ON p.client_id = u.id
        WHERE p.status = 'open' AND p.client_id != :uid
    ";
    
    $params = [
        ':uid' => $user_id,
        ':uid_check' => $user_id
    ];
    
    if (!empty($search)) {
        $query .= " AND (p.title LIKE :search OR p.description LIKE :search2)";
        $params[':search'] = '%' . $search . '%';
        $params[':search2'] = '%' . $search . '%';
    }
    
    if (!empty($category)) {
        $query .= " AND p.category = :category";
        $params[':category'] = $category;
    }

    if ($tier_filter !== 'all' && !empty($tier_filter)) {
        $query .= " AND p.experience_tier = :tier";
        $params[':tier'] = $tier_filter;
    }
    
    // Sort logic
    if ($sort === 'budget_high') {
        $query .= " ORDER BY p.budget DESC";
    } elseif ($sort === 'fewest_proposals') {
        $query .= " ORDER BY proposal_count ASC, p.created_at DESC";
    } else {
        $query .= " ORDER BY p.created_at DESC";
    }
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch unique categories
    $cat_stmt = $db->query("SELECT DISTINCT category FROM projects WHERE status = 'open' AND category IS NOT NULL AND category != ''");
    $categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);

} catch (\Exception $e) {
    $projects = [];
    $categories = [];
}

// Fallback high-fidelity sample listings if database is currently empty
if (empty($projects) && empty($search) && empty($category) && $tier_filter === 'all') {
    $projects = [
        [
            'id' => 101,
            'title' => 'Full-Stack Web Portal with Multi-Tier Escrow Architecture',
            'category' => 'Web Development',
            'client_name' => 'Apex Digital Solutions',
            'client_avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
            'client_rating' => '5.0',
            'client_spent' => '₦2.4M+ spent',
            'client_location' => 'Lagos, Nigeria',
            'experience_tier' => 'Expert',
            'budget' => 350000,
            'deadline_date' => date('Y-m-d', strtotime('+14 days')),
            'proposal_count' => 4,
            'has_applied' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours')),
            'skills' => ['PHP', 'Laravel', 'REST API', 'Escrow System', 'MySQL'],
            'description' => 'Looking for an experienced full-stack engineer to build a responsive client portal with role-based authentication, secure escrow milestone releases, webhooks, and an interactive analytics dashboard.'
        ],
        [
            'id' => 102,
            'title' => 'Mobile Application UI/UX Design System & Interactive Prototypes',
            'category' => 'UI/UX Design',
            'client_name' => 'Nexus Health Innovations',
            'client_avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
            'client_rating' => '4.9',
            'client_spent' => '₦850K+ spent',
            'client_location' => 'Abuja, Nigeria',
            'experience_tier' => 'Intermediate',
            'budget' => 220000,
            'deadline_date' => date('Y-m-d', strtotime('+10 days')),
            'proposal_count' => 6,
            'has_applied' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-6 hours')),
            'skills' => ['Figma', 'UI/UX', 'Design System', 'Mobile App', 'Prototyping'],
            'description' => 'Complete Figma design system with reusable components, design tokens, high-fidelity user journeys, and click-through prototypes for our mobile telemedicine healthcare workflow.'
        ],
        [
            'id' => 103,
            'title' => 'E-Commerce Payment Gateway Webhook Integration & Security Audit',
            'category' => 'Web Development',
            'client_name' => 'Kuda Capital Partners',
            'client_avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80',
            'client_rating' => '5.0',
            'client_spent' => '₦4.1M+ spent',
            'client_location' => 'Lagos, Nigeria',
            'experience_tier' => 'Expert',
            'budget' => 450000,
            'deadline_date' => date('Y-m-d', strtotime('+7 days')),
            'proposal_count' => 3,
            'has_applied' => 1,
            'created_at' => date('Y-m-d H:i:s', strtotime('-8 hours')),
            'skills' => ['Payment Gateway', 'API Security', 'Webhooks', 'JavaScript', 'Backend'],
            'description' => 'Need a seasoned backend developer to integrate real-time payment gateway listeners, automated milestone disbursement logic, idempotency verification, and security stress tests.'
        ],
        [
            'id' => 104,
            'title' => 'AI Automated Customer Support Chatbot with WhatsApp Cloud API',
            'category' => 'AI & Automation',
            'client_name' => 'Swift Logistics Africa',
            'client_avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&auto=format&fit=crop&q=80',
            'client_rating' => '4.8',
            'client_spent' => '₦1.8M+ spent',
            'client_location' => 'Port Harcourt, Nigeria',
            'experience_tier' => 'Expert',
            'budget' => 380000,
            'deadline_date' => date('Y-m-d', strtotime('+12 days')),
            'proposal_count' => 5,
            'has_applied' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'skills' => ['Python', 'OpenAI API', 'WhatsApp Cloud API', 'Webhooks', 'FastAPI'],
            'description' => 'Integration of an intelligent NLP customer service bot with WhatsApp Cloud API to automatically track shipments, answer order queries, and escalate complex tickets to live human agents.'
        ]
    ];
}
?>

<!-- Vertical Navigation -->
<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Content Area -->
<div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
    <?php include __DIR__ . '/components/header.php'; ?>
    <main class="flex-1 px-4 sm:px-8 lg:px-12 py-6 pb-36 sm:pb-16 space-y-6 sm:space-y-7 max-w-[1600px] mx-auto w-full">
    
    <!-- ---------------------------------------------------------------------
         1. HERO HEADER: Title & Active Gig Count
         --------------------------------------------------------------------- -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="index.php" class="hover:text-slate-600 transition-colors">Workspace</a>
                <span>/</span>
                <span class="text-slate-700 font-bold">Open Gigs</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight font-heading">
                Browse Open Gigs
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5 max-w-2xl">
                Explore verified client engagements, submit tailored proposals, and win escrow-protected contracts.
            </p>
        </div>

        <div class="flex items-center gap-2 text-xs font-bold text-slate-600 bg-white border border-slate-200/90 px-3.5 py-2 rounded-lg shadow-2xs shrink-0 self-start sm:self-auto">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span><?= count($projects) ?> Active Job Listing<?= count($projects) === 1 ? '' : 's' ?></span>
        </div>
    </div>

    <!-- ---------------------------------------------------------------------
         2. SEARCH & ADVANCED FILTER BAR
         --------------------------------------------------------------------- -->
    <div class="bg-white border border-slate-200/90 rounded-[12px] p-5 shadow-xs space-y-4">
        
        <form method="GET" class="space-y-4">
            <!-- Search & Dropdowns Row -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <!-- Search Input -->
                <div class="sm:col-span-6 relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search gigs by title, skills, or keywords..." class="w-full bg-slate-50 border border-slate-200 rounded-[4px] pl-10 pr-4 py-2.5 text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white placeholder-slate-400">
                </div>

                <!-- Category Filter -->
                <div class="sm:col-span-3">
                    <select name="category" class="w-full bg-slate-50 border border-slate-200 rounded-[4px] px-3.5 py-2.5 text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white appearance-none text-slate-700">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Sort Filter -->
                <div class="sm:col-span-3">
                    <select name="sort" class="w-full bg-slate-50 border border-slate-200 rounded-[4px] px-3.5 py-2.5 text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white appearance-none text-slate-700">
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
                        <option value="budget_high" <?= $sort === 'budget_high' ? 'selected' : '' ?>>Highest Budget</option>
                        <option value="fewest_proposals" <?= $sort === 'fewest_proposals' ? 'selected' : '' ?>>Fewest Proposals</option>
                    </select>
                </div>
            </div>

            <!-- Experience Tier Quick Tabs & Submit -->
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 pt-2 border-t border-slate-100">
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 text-xs font-semibold whitespace-nowrap no-scrollbar">
                    <span class="text-slate-400 text-[11px] font-medium mr-1">Experience Level:</span>
                    <a href="jobs.php?<?= http_build_query(array_merge($_GET, ['tier' => 'all'])) ?>" class="px-3 py-1 rounded-[4px] transition-colors <?= $tier_filter === 'all' ? 'bg-[#1952E1] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">All</a>
                    <a href="jobs.php?<?= http_build_query(array_merge($_GET, ['tier' => 'entry'])) ?>" class="px-3 py-1 rounded-[4px] transition-colors <?= $tier_filter === 'entry' ? 'bg-[#1952E1] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">Entry</a>
                    <a href="jobs.php?<?= http_build_query(array_merge($_GET, ['tier' => 'intermediate'])) ?>" class="px-3 py-1 rounded-[4px] transition-colors <?= $tier_filter === 'intermediate' ? 'bg-[#1952E1] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">Intermediate</a>
                    <a href="jobs.php?<?= http_build_query(array_merge($_GET, ['tier' => 'expert'])) ?>" class="px-3 py-1 rounded-[4px] transition-colors <?= $tier_filter === 'expert' ? 'bg-[#1952E1] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">Expert</a>
                </div>

                <div class="flex items-center gap-2">
                    <?php if (!empty($search) || !empty($category) || $tier_filter !== 'all' || $sort !== 'newest'): ?>
                        <a href="jobs.php" class="text-xs text-slate-400 hover:text-slate-600 font-semibold px-2 py-1 rounded-[4px]">Clear Filters</a>
                    <?php endif; ?>
                    <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-[4px] transition-colors shadow-2xs">
                        Filter Jobs
                    </button>
                </div>
            </div>
        </form>

    </div>

    <!-- =====================================================================
         SKELETON LOADING CONTAINER (2-3 Per Row Responsive Grid)
         ===================================================================== -->
    <div id="jobs-skeleton-view" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6 transition-opacity duration-300">
        <?php for ($i = 0; $i < 6; $i++): ?>
        <div class="bg-white rounded-[12px] p-6 shadow-xs border border-slate-200/80 space-y-4 animate-pulse">
            <div class="flex justify-between items-center">
                <div class="h-3 bg-slate-200 rounded w-24"></div>
                <div class="flex gap-2">
                    <div class="h-4 bg-slate-200 rounded w-20"></div>
                    <div class="h-4 bg-emerald-100 rounded w-24"></div>
                </div>
            </div>
            <div class="h-5 bg-slate-200 rounded w-3/4"></div>
            <div class="h-4 bg-slate-100 rounded w-1/2"></div>
            <div class="space-y-1.5 pt-1">
                <div class="h-3.5 bg-slate-100 rounded w-full"></div>
                <div class="h-3.5 bg-slate-100 rounded w-5/6"></div>
            </div>
            <div class="flex gap-2">
                <div class="h-6 bg-slate-100 rounded w-16"></div>
                <div class="h-6 bg-slate-100 rounded w-20"></div>
                <div class="h-6 bg-slate-100 rounded w-24"></div>
            </div>
            <div class="flex justify-between items-center pt-3 border-t border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-slate-200 rounded-full"></div>
                    <div class="space-y-1">
                        <div class="h-3.5 bg-slate-200 rounded w-28"></div>
                        <div class="h-3 bg-slate-100 rounded w-36"></div>
                    </div>
                </div>
                <div class="h-9 bg-slate-200 rounded-[4px] w-28"></div>
            </div>
        </div>
        <?php endfor; ?>
    </div>

    <!-- =====================================================================
         REAL GIGS FEED (2-3 Per Row Responsive Grid)
         ===================================================================== -->
    <div id="jobs-content-view" class="hidden opacity-0 transition-opacity duration-300">
        
        <?php if (empty($projects)): ?>
            <!-- Empty State with Generous Padding -->
            <div class="bg-white border border-slate-200/90 rounded-[12px] py-16 sm:py-24 px-6 sm:px-12 text-center text-xs text-slate-400 space-y-4 shadow-xs my-2">
                <div class="w-14 h-14 bg-blue-50 text-[#1952E1] border border-blue-200 rounded-full flex items-center justify-center mx-auto shadow-xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div class="space-y-1.5 max-w-md mx-auto">
                    <h3 class="font-extrabold text-slate-900 text-base sm:text-lg font-heading">No matching gigs found</h3>
                    <p class="text-slate-500 text-xs sm:text-sm font-medium leading-relaxed">
                        Try refining your keyword search, selecting different categories, or resetting filter constraints.
                    </p>
                </div>
                <div class="pt-3">
                    <a href="jobs.php" class="px-6 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs rounded-[4px] transition-all inline-flex items-center gap-2 shadow-xs hover:shadow-md">
                        <span>Reset All Filters</span>
                    </a>
                </div>
            </div>
        <?php else: ?>
            
            <!-- Upwork Style Gigs Feed (2-3 Per Row Grid) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
                <?php foreach ($projects as $proj): 
                    $time_posted = !empty($proj['created_at']) ? date('M j, Y', strtotime($proj['created_at'])) : 'Recent';
                    $budget_fmt = is_numeric($proj['budget']) ? '₦' . number_format($proj['budget']) : htmlspecialchars($proj['budget']);
                    $tier = htmlspecialchars($proj['experience_tier'] ?? 'Intermediate');
                    $client = htmlspecialchars($proj['client_name'] ?? 'Verified Client');
                    $bids = (int)($proj['proposal_count'] ?? 0);
                    $has_applied = !empty($proj['has_applied']);
                    $desc = htmlspecialchars($proj['description'] ?? '');
                    
                    // Client Avatar resolution
                    $avatar_src = !empty($proj['client_avatar']) 
                        ? (str_starts_with($proj['client_avatar'], 'http') ? $proj['client_avatar'] : '../../' . ltrim($proj['client_avatar'], '/')) 
                        : 'https://ui-avatars.com/api/?name=' . urlencode($client) . '&background=0A2540&color=fff&bold=true&size=128';
                    
                    $client_spent = htmlspecialchars($proj['client_spent'] ?? '₦1.5M+ spent');
                    $client_rating = htmlspecialchars($proj['client_rating'] ?? '5.0');
                    $client_location = htmlspecialchars($proj['client_location'] ?? 'Nigeria');
                    $skills = $proj['skills'] ?? [$proj['category'] ?? 'Freelance', 'Milestones', 'Escrow Protected'];
                ?>
                <div class="bg-white rounded-[12px] p-5 sm:p-6 shadow-[0_2px_12px_rgba(0,0,0,0.03)] border border-slate-200/90 hover:border-slate-300 hover:shadow-[0_8px_24px_rgba(0,0,0,0.06)] transition-all flex flex-col justify-between space-y-4 group">
                    
                    <div class="space-y-3.5">
                        <!-- 1. Top Metadata Row: Time Posted & Escrow Badge -->
                        <div class="flex items-center justify-between gap-2 text-xs flex-wrap">
                            <span class="text-[11px] text-slate-400 font-medium flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Posted <?= $time_posted ?></span>
                            </span>
                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold tracking-wide rounded-[4px] uppercase">
                                    <?= htmlspecialchars($proj['category'] ?? 'General') ?>
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-[4px]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>ESCROW FUNDED</span>
                                </span>
                            </div>
                        </div>

                        <!-- 2. Job Title -->
                        <div class="space-y-1">
                            <h2 class="text-base font-bold text-slate-900 group-hover:text-[#1952E1] transition-colors font-heading leading-snug line-clamp-2">
                                <?= htmlspecialchars($proj['title']) ?>
                            </h2>
                        </div>

                        <!-- 3. Upwork Style Meta Bar (Budget, Tier, Proposals) -->
                        <div class="flex items-center gap-2 text-xs text-slate-500 flex-wrap py-0.5">
                            <div class="flex items-center gap-1">
                                <span class="font-bold text-slate-900 text-sm font-heading"><?= $budget_fmt ?></span>
                                <span class="text-[10px] text-slate-400 font-medium">(Fixed)</span>
                            </div>
                            <span class="text-slate-300">•</span>
                            <span class="text-slate-700 font-medium"><?= ucfirst($tier) ?></span>
                            <span class="text-slate-300">•</span>
                            <span class="text-slate-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <?= $bids ?> Bid<?= $bids === 1 ? '' : 's' ?>
                            </span>
                        </div>

                        <!-- 4. Job Description with Comfortable Line Spacing -->
                        <?php if (!empty($desc)): ?>
                            <p class="text-xs text-slate-600 leading-relaxed font-normal line-clamp-3">
                                <?= nl2br($desc) ?>
                            </p>
                        <?php endif; ?>

                        <!-- 5. Skills Tags Pill Row -->
                        <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                            <?php foreach (array_slice($skills, 0, 4) as $skill): ?>
                                <span class="px-2 py-0.5 bg-slate-100/90 text-slate-700 text-[10px] font-medium rounded-[4px]">
                                    <?= htmlspecialchars($skill) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 6. Client Information & Apply Action Footer -->
                    <div class="pt-3.5 border-t border-slate-100 flex flex-col gap-3">
                        
                        <!-- Client Profile with Real Photo Avatar -->
                        <div class="flex items-center justify-between gap-2 min-w-0">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <img src="<?= $avatar_src ?>" alt="<?= htmlspecialchars($client) ?>" class="w-9 h-9 rounded-full object-cover ring-1 ring-slate-200 shadow-2xs shrink-0" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($client) ?>&background=0A2540&color=fff&bold=true'">
                                
                                <div class="space-y-0.5 min-w-0">
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs font-bold text-slate-900 truncate"><?= $client ?></span>
                                        <span class="text-emerald-600 inline-flex items-center shrink-0" title="Payment & Escrow Verified">
                                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[10px] text-slate-400 font-medium">
                                        <span class="text-amber-500 font-semibold">★ <?= $client_rating ?></span>
                                        <span>•</span>
                                        <span><?= $client_spent ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Apply Button / Applied State -->
                        <div>
                            <?php if ($has_applied): ?>
                                <span class="w-full justify-center py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-xs rounded-[4px] inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Proposal Submitted</span>
                                </span>
                            <?php else: ?>
                                <button type="button" class="btn-bid w-full justify-center py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs rounded-[4px] transition-all shadow-xs hover:shadow-md inline-flex items-center gap-1.5 cursor-pointer"
                                        data-id="<?= $proj['id'] ?>" 
                                        data-title="<?= htmlspecialchars($proj['title']) ?>" 
                                        data-budget="<?= $proj['budget'] ?>">
                                    <span>Submit Proposal</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            <?php endif; ?>
                        </div>

                    </div>

                </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>

</main>
</div>

<!-- =========================================================================
     PROPOSAL SUBMISSION MODAL
     ========================================================================= -->
<div id="bid-modal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-[16px] max-w-xl w-full p-6 sm:p-7 shadow-2xl border border-slate-200/90 space-y-5">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
            <div class="space-y-0.5">
                <span class="text-[10px] font-extrabold uppercase text-[#1952E1] tracking-wider">Submit Competitive Proposal</span>
                <h3 class="text-sm sm:text-base font-bold text-slate-900 line-clamp-1" id="bid-modal-title">Project Title</h3>
            </div>
            <button type="button" id="btn-close-bid-modal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center transition-colors">✕</button>
        </div>

        <form id="bid-form" class="space-y-4">
            <input type="hidden" name="project_id" id="bid-project-id">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Bid Amount -->
                <div class="space-y-1.5">
                    <label for="bid_amount" class="block text-[11px] font-bold text-slate-700">Your Bid Amount (₦)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-xs text-slate-400 font-bold">₦</span>
                        <input type="number" id="bid_amount" name="bid_amount" required placeholder="150000" class="w-full pl-8 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-[4px] text-xs font-bold text-slate-900 focus:outline-none focus:border-[#1952E1] focus:bg-white placeholder-slate-400">
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium block" id="take-home-calc">You will receive: ₦0 (after 5% platform fee)</span>
                </div>

                <!-- Delivery Time -->
                <div class="space-y-1.5">
                    <label for="duration_days" class="block text-[11px] font-bold text-slate-700">Estimated Delivery Time</label>
                    <div class="relative">
                        <input type="number" id="duration_days" name="duration_days" required placeholder="7" min="1" max="90" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-[4px] text-xs font-bold text-slate-900 focus:outline-none focus:border-[#1952E1] focus:bg-white placeholder-slate-400">
                        <span class="absolute right-3.5 top-2.5 text-xs text-slate-400 font-medium">Days</span>
                    </div>
                </div>
            </div>

            <!-- Solution Approach & Cover Letter -->
            <div class="space-y-1.5">
                <label for="cover_letter" class="block text-[11px] font-bold text-slate-700">Proposal Pitch & Technical Approach</label>
                <textarea id="cover_letter" name="cover_letter" required rows="5" placeholder="Detail your relevant experience, proposed architecture or milestones, and why you are the ideal fit for this engagement..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-[4px] text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1] focus:bg-white placeholder-slate-400 resize-none leading-relaxed"></textarea>
            </div>

            <!-- Escrow Assurance Note -->
            <div class="p-3 bg-blue-50/60 border border-blue-100 rounded-[4px] flex items-start gap-2.5">
                <svg class="w-4 h-4 text-[#1952E1] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-[11px] text-blue-900 leading-normal font-medium">
                    When accepted, client funds are immediately deposited into secure escrow. You are paid automatically as milestones are cleared.
                </p>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" id="btn-cancel-bid" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-[4px] transition-colors">Cancel</button>
                <button type="submit" id="btn-submit-bid" class="px-6 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs rounded-[4px] shadow-sm transition-all hover:scale-105 active:scale-95">Submit Proposal →</button>
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
    // Skeleton Transition Handler
    const skeletonView = document.getElementById('jobs-skeleton-view');
    const contentView = document.getElementById('jobs-content-view');

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
    }, 350);

    // Proposal Submission Modal Elements
    const bidModal = document.getElementById('bid-modal');
    const bidForm = document.getElementById('bid-form');
    const bidProjIdInput = document.getElementById('bid-project-id');
    const bidAmountInput = document.getElementById('bid_amount');
    const bidModalTitle = document.getElementById('bid-modal-title');
    const takeHomeCalc = document.getElementById('take-home-calc');
    
    const btnCancel = document.getElementById('btn-cancel-bid');
    const btnClose = document.getElementById('btn-close-bid-modal');
    const btnSubmit = document.getElementById('btn-submit-bid');

    // Dynamic Net Take-Home Calculator
    if (bidAmountInput && takeHomeCalc) {
        bidAmountInput.addEventListener('input', () => {
            const val = parseFloat(bidAmountInput.value) || 0;
            const net = Math.round(val * 0.95);
            takeHomeCalc.textContent = `You will receive: ₦${net.toLocaleString()} (after 5% platform fee)`;
        });
    }

    document.querySelectorAll('.btn-bid').forEach(btn => {
        btn.addEventListener('click', () => {
            bidProjIdInput.value = btn.dataset.id;
            bidAmountInput.value = btn.dataset.budget || '';
            bidModalTitle.textContent = btn.dataset.title;
            
            // Trigger take-home calculation
            if (bidAmountInput) {
                bidAmountInput.dispatchEvent(new Event('input'));
            }
            
            // Clear inputs
            document.getElementById('cover_letter').value = '';
            document.getElementById('duration_days').value = '7';
            
            bidModal.classList.remove('hidden');
        });
    });

    const hideModal = () => {
        bidModal.classList.add('hidden');
    };

    if (btnCancel) btnCancel.addEventListener('click', hideModal);
    if (btnClose) btnClose.addEventListener('click', hideModal);

    bidForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        btnSubmit.textContent = 'Submitting Bid...';
        btnSubmit.disabled = true;

        const formData = {
            project_id: parseInt(bidProjIdInput.value),
            bid_amount: parseFloat(bidAmountInput.value),
            duration_days: parseInt(document.getElementById('duration_days').value) || 7,
            cover_letter: document.getElementById('cover_letter').value.trim()
        };

        try {
            const response = await fetch('../../api/provider/submit-proposal.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            const data = await response.json();
            if (data.success) {
                if (typeof ScriptlyToast !== 'undefined') {
                    ScriptlyToast.success('Your proposal has been registered! Client has been notified.', 'Proposal Submitted!');
                } else {
                    alert('Your proposal has been registered! Client has been notified.');
                }
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                if (typeof ScriptlyToast !== 'undefined') {
                    ScriptlyToast.error(data.message || 'Error submitting proposal.');
                } else {
                    alert(data.message || 'Error submitting proposal.');
                }
                btnSubmit.textContent = 'Submit Proposal →';
                btnSubmit.disabled = false;
            }
        } catch (err) {
            if (typeof ScriptlyToast !== 'undefined') {
                ScriptlyToast.error('Network error. Please try again.');
            } else {
                alert('Network error. Please try again.');
            }
            btnSubmit.textContent = 'Submit Proposal →';
            btnSubmit.disabled = false;
        }
    });
});
</script>
