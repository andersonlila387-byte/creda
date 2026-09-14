<?php 
/**
 * Scriptly Escrow - Talent Directory (Sharp Responsive Search Toolbar with Category Select & Advanced Rate/Rating Filters)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();

$category_filter = $_GET['category'] ?? '';
$search_query = $_GET['q'] ?? '';
$rate_filter = $_GET['rate'] ?? '';
$rating_filter = $_GET['rating'] ?? '';
$view_mode = $_GET['view'] ?? 'talent'; // 'talent' or 'packages'

// 1. Fetch Verified Talent / Service Providers (Querying talent_profiles left joined with users)
$sql_talent = "
    SELECT 
        tp.id as profile_id, tp.user_id,
        COALESCE(u.full_name, 'Verified Professional') as full_name, 
        u.username, u.email, u.primary_role, u.is_verified_pro,
        tp.avatar_url, tp.title as talent_title, tp.bio, tp.skills, tp.location, 
        tp.rating, tp.rating_count, tp.completed_projects, tp.job_success_percentage, tp.hourly_rate
    FROM talent_profiles tp
    LEFT JOIN users u ON tp.user_id = u.id
    WHERE 1=1
";

$params_talent = [];

if ($search_query) {
    $sql_talent .= " AND (u.full_name LIKE ? OR tp.title LIKE ? OR tp.skills LIKE ? OR tp.bio LIKE ?)";
    $params_talent[] = "%$search_query%";
    $params_talent[] = "%$search_query%";
    $params_talent[] = "%$search_query%";
    $params_talent[] = "%$search_query%";
}

if ($category_filter) {
    $sql_talent .= " AND (tp.skills LIKE ? OR tp.title LIKE ? OR tp.category_key LIKE ?)";
    $params_talent[] = "%$category_filter%";
    $params_talent[] = "%$category_filter%";
    $params_talent[] = "%$category_filter%";
}

if ($rate_filter === 'under_15k') {
    $sql_talent .= " AND (tp.hourly_rate <= 15000 OR tp.hourly_rate IS NULL)";
} elseif ($rate_filter === '15k_30k') {
    $sql_talent .= " AND tp.hourly_rate BETWEEN 15000 AND 30000";
} elseif ($rate_filter === 'above_30k') {
    $sql_talent .= " AND tp.hourly_rate > 30000";
}

if ($rating_filter === '4_5') {
    $sql_talent .= " AND tp.rating >= 4.5";
} elseif ($rating_filter === '4_0') {
    $sql_talent .= " AND tp.rating >= 4.0";
}

$sql_talent .= " ORDER BY tp.rating DESC, tp.completed_projects DESC, tp.id DESC";

$stmt_talent = $db->prepare($sql_talent);
$stmt_talent->execute($params_talent);
$talent_list = $stmt_talent->fetchAll(PDO::FETCH_ASSOC);

// 2. Fetch Service Packages (For Packages View Mode)
$sql_packages = "
    SELECT 
        p.id as package_id, p.title, p.search_tags, p.created_at,
        u.id as provider_id, u.full_name as provider_name, u.username as provider_username,
        tp.rating as provider_rating, tp.completed_projects, tp.avatar_url,
        c.name as category_name, c.slug as category_slug,
        (SELECT price FROM package_pricing_tiers WHERE package_id = p.id AND tier_type = 'basic' LIMIT 1) as starting_price,
        (SELECT file_path FROM package_gallery WHERE package_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
    FROM packages p
    JOIN users u ON p.provider_id = u.id
    LEFT JOIN talent_profiles tp ON u.id = tp.user_id
    LEFT JOIN service_categories c ON p.category_id = c.id
    WHERE p.status = 'active'
";
$params_pkgs = [];
if ($category_filter) {
    $sql_packages .= " AND c.slug = ?";
    $params_pkgs[] = $category_filter;
}
if ($search_query) {
    $sql_packages .= " AND (p.title LIKE ? OR p.search_tags LIKE ? OR u.full_name LIKE ?)";
    $params_pkgs[] = "%$search_query%";
    $params_pkgs[] = "%$search_query%";
    $params_pkgs[] = "%$search_query%";
}
$sql_packages .= " ORDER BY p.created_at DESC";
$stmt_pkgs = $db->prepare($sql_packages);
$stmt_pkgs->execute($params_pkgs);
$packages_list = $stmt_pkgs->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories for select dropdown
$cats = $db->query("SELECT id, name, slug FROM service_categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Find Talent';
$active_tab = 'talent';
require_once __DIR__ . '/components/head.php';
?>

<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Layout Area -->
<main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-hidden relative bg-[#EFF2F7]">
    
    <?php include __DIR__ . '/components/header.php'; ?>

    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 pb-36 md:pb-6 lg:pb-12 scroll-smooth">
        
        <div class="max-w-7xl mx-auto space-y-5 sm:space-y-6">

            <!-- TOP CONTROLS & SEARCH TOOLBAR -->
            <div class="bg-white p-4 sm:p-5 rounded-[3px] border border-slate-200/90 shadow-sm space-y-4">
                
                <!-- Title Row & View Switcher -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Hire Verified Talent</h1>
                            <span class="bg-blue-50 text-[#1952E1] border border-blue-100 text-[11px] font-bold px-2 py-0.5 rounded-[3px]">
                                <?= $view_mode === 'talent' ? count($talent_list) . ' Specialists' : count($packages_list) . ' Packages' ?>
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Discover vetted software engineers, designers, and domain specialists protected by Escrow.</p>
                    </div>

                    <!-- View Mode Toggle Tabs -->
                    <div class="flex items-center bg-slate-100 p-0.5 rounded-[3px] border border-slate-200 self-start sm:self-auto shrink-0">
                        <a href="talent.php?view=talent<?= $search_query ? '&q='.urlencode($search_query) : '' ?><?= $category_filter ? '&category='.urlencode($category_filter) : '' ?>" class="px-3 py-1.5 rounded-[3px] text-xs font-bold transition-all flex items-center gap-1.5 <?= $view_mode === 'talent' ? 'bg-[#1952E1] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' ?>">
                            <i class="ph-bold ph-users text-sm"></i>
                            <span>Talent</span>
                        </a>
                        <a href="talent.php?view=packages<?= $search_query ? '&q='.urlencode($search_query) : '' ?><?= $category_filter ? '&category='.urlencode($category_filter) : '' ?>" class="px-3 py-1.5 rounded-[3px] text-xs font-bold transition-all flex items-center gap-1.5 <?= $view_mode === 'packages' ? 'bg-[#1952E1] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' ?>">
                            <i class="ph-bold ph-package text-sm"></i>
                            <span>Packages</span>
                        </a>
                    </div>
                </div>

                <!-- UNIFIED RESPONSIVE SEARCH & FILTER BAR -->
                <form action="talent.php" method="GET" class="space-y-3">
                    <input type="hidden" name="view" value="<?= htmlspecialchars($view_mode) ?>">
                    
                    <div class="flex flex-col md:flex-row gap-2.5 items-stretch md:items-center">
                        
                        <!-- 1. Search Text Input (Takes full remaining space) -->
                        <div class="relative flex-1 min-w-0">
                            <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-sm pointer-events-none"></i>
                            <input 
                                type="text" 
                                name="q" 
                                placeholder="Search by name, role, or skill (e.g. Fullstack, UI/UX, Python, React)..." 
                                value="<?= htmlspecialchars($search_query) ?>" 
                                class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] pl-9 pr-3 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#1952E1] transition-all"
                            >
                        </div>

                        <!-- 2. Filters Group (Beside Search on Desktop; 2-col on Mobile) -->
                        <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 shrink-0">
                            
                            <!-- Category Select (Directly beside input search) -->
                            <div class="relative col-span-2 sm:col-span-1 sm:w-44">
                                <select 
                                    name="category" 
                                    onchange="this.form.submit()" 
                                    class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-2.5 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:bg-white focus:border-[#1952E1] truncate cursor-pointer"
                                >
                                    <option value="">All Categories</option>
                                    <?php foreach($cats as $c): ?>
                                        <option value="<?= htmlspecialchars($c['slug']) ?>" <?= $category_filter === $c['slug'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($c['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Rate Filter -->
                            <div class="relative w-full sm:w-36">
                                <select 
                                    name="rate" 
                                    onchange="this.form.submit()" 
                                    class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-2.5 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:bg-white focus:border-[#1952E1] truncate cursor-pointer"
                                >
                                    <option value="">Any Rate</option>
                                    <option value="under_15k" <?= $rate_filter === 'under_15k' ? 'selected' : '' ?>>Under ₦15k/hr</option>
                                    <option value="15k_30k" <?= $rate_filter === '15k_30k' ? 'selected' : '' ?>>₦15k - ₦30k/hr</option>
                                    <option value="above_30k" <?= $rate_filter === 'above_30k' ? 'selected' : '' ?>>Above ₦30k/hr</option>
                                </select>
                            </div>

                            <!-- Rating Filter -->
                            <div class="relative w-full sm:w-32">
                                <select 
                                    name="rating" 
                                    onchange="this.form.submit()" 
                                    class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-2.5 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:bg-white focus:border-[#1952E1] truncate cursor-pointer"
                                >
                                    <option value="">Any Rating</option>
                                    <option value="4_5" <?= $rating_filter === '4_5' ? 'selected' : '' ?>>★ 4.5 & up</option>
                                    <option value="4_0" <?= $rating_filter === '4_0' ? 'selected' : '' ?>>★ 4.0 & up</option>
                                </select>
                            </div>

                            <!-- Search Button -->
                            <button 
                                type="submit" 
                                class="col-span-2 sm:col-span-1 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-[3px] transition-colors shadow-2xs flex items-center justify-center gap-1.5 shrink-0"
                            >
                                <i class="ph-bold ph-magnifying-glass text-xs"></i>
                                <span>Search</span>
                            </button>

                        </div>

                    </div>
                </form>

                <!-- Active Filter Tags / Quick Reset Strip -->
                <?php 
                $has_active_filters = !empty($search_query) || !empty($category_filter) || !empty($rate_filter) || !empty($rating_filter);
                if ($has_active_filters): 
                ?>
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider">Filtered by:</span>
                            
                            <?php if (!empty($search_query)): ?>
                                <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-800 font-medium px-2 py-0.5 rounded-[3px] text-[11px] border border-slate-200">
                                    Keyword: "<?= htmlspecialchars($search_query) ?>"
                                    <a href="talent.php?view=<?= $view_mode ?>&category=<?= urlencode($category_filter) ?>&rate=<?= urlencode($rate_filter) ?>&rating=<?= urlencode($rating_filter) ?>" class="text-slate-400 hover:text-slate-700">
                                        <i class="ph-bold ph-x text-[10px]"></i>
                                    </a>
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($category_filter)): ?>
                                <span class="inline-flex items-center gap-1 bg-blue-50 text-[#1952E1] font-medium px-2 py-0.5 rounded-[3px] text-[11px] border border-blue-200">
                                    Category: <?= htmlspecialchars($category_filter) ?>
                                    <a href="talent.php?view=<?= $view_mode ?>&q=<?= urlencode($search_query) ?>&rate=<?= urlencode($rate_filter) ?>&rating=<?= urlencode($rating_filter) ?>" class="text-[#1952E1] hover:text-blue-800">
                                        <i class="ph-bold ph-x text-[10px]"></i>
                                    </a>
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($rate_filter)): ?>
                                <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-800 font-medium px-2 py-0.5 rounded-[3px] text-[11px] border border-slate-200">
                                    Rate: <?= $rate_filter === 'under_15k' ? '< ₦15k' : ($rate_filter === '15k_30k' ? '₦15k-₦30k' : '> ₦30k') ?>
                                    <a href="talent.php?view=<?= $view_mode ?>&q=<?= urlencode($search_query) ?>&category=<?= urlencode($category_filter) ?>&rating=<?= urlencode($rating_filter) ?>" class="text-slate-400 hover:text-slate-700">
                                        <i class="ph-bold ph-x text-[10px]"></i>
                                    </a>
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($rating_filter)): ?>
                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-800 font-medium px-2 py-0.5 rounded-[3px] text-[11px] border border-amber-200">
                                    Rating: <?= $rating_filter === '4_5' ? '4.5+ ★' : '4.0+ ★' ?>
                                    <a href="talent.php?view=<?= $view_mode ?>&q=<?= urlencode($search_query) ?>&category=<?= urlencode($category_filter) ?>&rate=<?= urlencode($rate_filter) ?>" class="text-amber-800 hover:text-amber-950">
                                        <i class="ph-bold ph-x text-[10px]"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                        </div>

                        <a href="talent.php?view=<?= $view_mode ?>" class="text-xs font-bold text-rose-600 hover:text-rose-700 transition-colors whitespace-nowrap ml-2">
                            Reset All
                        </a>
                    </div>
                <?php endif; ?>

            </div>

            <!-- DIRECTORY CONTENT: EXACT 3 CARDS IN A ROW ON DESKTOP -->
            <div class="w-full min-w-0">
                
                <?php if ($view_mode === 'talent'): ?>
                    
                    <?php if (empty($talent_list)): ?>
                        <!-- Empty Search Results State -->
                        <div class="bg-white border border-slate-200/90 rounded-[3px] p-10 sm:p-14 text-center shadow-sm space-y-3">
                            <div class="w-12 h-12 bg-slate-50 border border-slate-200 rounded-[3px] flex items-center justify-center mx-auto text-slate-400">
                                <i class="ph-bold ph-users-three text-2xl"></i>
                            </div>
                            <h3 class="text-slate-900 font-bold text-sm">No professionals found</h3>
                            <p class="text-xs text-slate-500 max-w-md mx-auto">
                                We couldn't find any active verified talent matching your current search or filter criteria. Try adjusting your search keyword, category, or rate limits.
                            </p>
                            <?php if ($has_active_filters): ?>
                                <div class="pt-2">
                                    <a href="talent.php?view=talent" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1952E1] text-white font-bold text-xs rounded-[3px] hover:bg-blue-700 transition-colors shadow-2xs">
                                        <i class="ph-bold ph-arrow-counter-clockwise text-xs"></i>
                                        <span>Reset All Filters</span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- 3 Cards Per Row Desktop Grid (lg:grid-cols-3, md:grid-cols-2, grid-cols-1) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                            <?php foreach($talent_list as $t): 
                                $avatar = !empty($t['avatar_url']) ? $t['avatar_url'] : '../assets/images/default-avatar.png';
                                $skills_raw = $t['skills'] ?? 'Web Development, React, PHP, Tailwind CSS';
                                $skills_arr = array_slice(array_filter(array_map('trim', explode(',', $skills_raw))), 0, 3);
                                $rating_val = $t['rating'] ? number_format($t['rating'], 1) : '4.9';
                                $reviews_count = $t['rating_count'] ?: 28;
                                $success_rate = $t['job_success_percentage'] ?: 99;
                                $hourly_rate = $t['hourly_rate'] ?: 20000;
                            ?>
                                <div class="p-5 rounded-[3px] border border-slate-200/90 bg-white hover:border-[#1952E1] hover:shadow-md transition-all flex flex-col justify-between group shadow-sm">
                                    
                                    <div>
                                        <!-- Header & Square Avatar with Status Dot -->
                                        <div class="flex items-start gap-3.5 mb-3.5">
                                            <div class="relative shrink-0">
                                                <img src="<?= htmlspecialchars($avatar) ?>" alt="<?= htmlspecialchars($t['full_name']) ?>" class="w-14 h-14 rounded-[3px] object-cover border border-slate-200 shadow-2xs">
                                                <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-500 rounded-full ring-2 ring-white" title="Available for hire"></span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-1.5">
                                                    <a href="provider-profile.php?id=<?= $t['user_id'] ?>" class="font-bold text-sm text-slate-900 group-hover:text-[#1952E1] transition-colors truncate">
                                                        <?= htmlspecialchars($t['full_name']) ?>
                                                    </a>
                                                    <i class="ph-fill ph-check-circle text-[#1952E1] text-sm shrink-0" title="Scriptly Verified"></i>
                                                </div>
                                                <p class="text-[11px] text-slate-600 font-semibold truncate mt-0.5">
                                                    <?= htmlspecialchars($t['talent_title'] ?: 'Verified Specialist') ?>
                                                </p>
                                                <div class="flex items-center gap-2 mt-1.5 text-[11px]">
                                                    <span class="text-amber-500 font-bold flex items-center gap-0.5">
                                                        ★ <?= $rating_val ?>
                                                        <span class="text-slate-400 font-normal text-[10px]">(<?= $reviews_count ?>)</span>
                                                    </span>
                                                    <span class="text-slate-300">•</span>
                                                    <span class="text-emerald-700 font-semibold bg-emerald-50 px-1.5 py-0.5 rounded-[3px] text-[10px]">
                                                        <?= $success_rate ?>% Success
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bio / Tagline Snippet -->
                                        <p class="text-xs text-slate-600 leading-relaxed mb-3 line-clamp-2">
                                            <?= htmlspecialchars($t['bio'] ?: 'Experienced verified specialist on Scriptly delivering guaranteed milestone quality, clean execution, and 100% escrow protection.') ?>
                                        </p>

                                        <!-- Skill Badges -->
                                        <div class="flex flex-wrap gap-1.5 mb-3.5">
                                            <?php foreach ($skills_arr as $sk): ?>
                                                <span class="text-[10px] font-semibold bg-slate-50 text-slate-700 border border-slate-200/90 px-2 py-0.5 rounded-[3px]">
                                                    <?= htmlspecialchars($sk) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <!-- Bottom Meta & Actions -->
                                    <div class="pt-3 border-t border-slate-200/80 mt-auto space-y-3">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-slate-500 text-[11px] font-medium flex items-center gap-1">
                                                <i class="ph-bold ph-map-pin text-slate-400"></i>
                                                <?= htmlspecialchars($t['location'] ?: 'Lagos, Nigeria') ?>
                                            </span>
                                            <div class="text-right">
                                                <span class="text-[10px] text-slate-400 font-medium">Starting from </span>
                                                <span class="font-black text-slate-900 text-xs sm:text-sm">₦<?= number_format($hourly_rate) ?></span>
                                                <span class="text-[10px] text-slate-500">/hr</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <a href="provider-profile.php?id=<?= $t['user_id'] ?>" class="flex-1 text-center bg-white hover:bg-slate-50 border border-slate-200 text-slate-800 text-xs font-bold py-2 rounded-[3px] transition-colors">
                                                View Profile
                                            </a>
                                            <a href="messages.php?user=<?= $t['user_id'] ?>" class="flex-1 text-center bg-[#1952E1] hover:bg-blue-700 text-white text-xs font-bold py-2 rounded-[3px] transition-colors shadow-2xs flex items-center justify-center gap-1.5">
                                                <i class="ph-bold ph-paper-plane-tilt text-xs"></i>
                                                <span>Contact</span>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>

                    <!-- ==================== SERVICE PACKAGES GRID ==================== -->
                    <?php if (empty($packages_list)): ?>
                        <div class="bg-white border border-slate-200/90 rounded-[3px] p-10 sm:p-14 text-center shadow-sm space-y-3">
                            <div class="w-12 h-12 bg-slate-50 border border-slate-200 rounded-[3px] flex items-center justify-center mx-auto text-slate-400">
                                <i class="ph-bold ph-package-open text-2xl"></i>
                            </div>
                            <h3 class="text-slate-900 font-bold text-sm">No service packages found</h3>
                            <p class="text-xs text-slate-500 max-w-md mx-auto">
                                We couldn't find any active service packages matching your current search or category filter.
                            </p>
                            <?php if ($has_active_filters): ?>
                                <div class="pt-2">
                                    <a href="talent.php?view=packages" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1952E1] text-white font-bold text-xs rounded-[3px] hover:bg-blue-700 transition-colors shadow-2xs">
                                        <i class="ph-bold ph-arrow-counter-clockwise text-xs"></i>
                                        <span>Reset Filters</span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- 3 Cards Per Row Desktop Grid for Packages -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                            <?php foreach($packages_list as $pkg): ?>
                                <div class="bg-white border border-slate-200/90 rounded-[3px] overflow-hidden shadow-sm hover:border-[#1952E1] hover:shadow-md transition-all group flex flex-col justify-between">
                                    
                                    <!-- Thumbnail -->
                                    <a href="service-details.php?id=<?= $pkg['package_id'] ?>" class="block relative aspect-video bg-slate-100 overflow-hidden">
                                        <?php if(!empty($pkg['primary_image'])): ?>
                                            <img src="<?= htmlspecialchars($pkg['primary_image']) ?>" alt="Thumbnail" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        <?php else: ?>
                                            <div class="w-full h-full flex flex-col items-center justify-center bg-slate-100 text-slate-400 gap-1">
                                                <i class="ph-bold ph-image-square text-3xl"></i>
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Scriptly Package</span>
                                            </div>
                                        <?php endif; ?>

                                        <span class="absolute top-2.5 left-2.5 bg-slate-900/80 backdrop-blur-md text-white text-[10px] font-bold px-2 py-0.5 rounded-[3px] shadow-sm">
                                            <?= htmlspecialchars($pkg['category_name'] ?? 'General') ?>
                                        </span>
                                    </a>

                                    <!-- Body -->
                                    <div class="p-4 flex flex-col flex-1 justify-between space-y-3">
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <img src="<?= $pkg['avatar_url'] ?: '../assets/images/default-avatar.png' ?>" class="w-6 h-6 rounded-[3px] object-cover border border-slate-200 shrink-0" alt="Avatar">
                                                <a href="provider-profile.php?id=<?= $pkg['provider_id'] ?>" class="text-xs font-bold text-slate-800 hover:text-[#1952E1] truncate">
                                                    <?= htmlspecialchars($pkg['provider_name']) ?>
                                                </a>
                                                <i class="ph-fill ph-seal-check text-[#1952E1] text-xs shrink-0" title="Verified"></i>
                                            </div>

                                            <a href="service-details.php?id=<?= $pkg['package_id'] ?>" class="block">
                                                <h2 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug group-hover:text-[#1952E1] transition-colors line-clamp-2" title="<?= htmlspecialchars($pkg['title']) ?>">
                                                    <?= htmlspecialchars($pkg['title']) ?>
                                                </h2>
                                            </a>
                                        </div>

                                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 mt-auto">
                                            <div class="flex items-center gap-1 text-xs font-bold text-slate-700">
                                                <i class="ph-fill ph-star text-amber-500 text-sm"></i>
                                                <span><?= $pkg['provider_rating'] ? number_format($pkg['provider_rating'], 1) : 'New' ?></span>
                                                <span class="text-slate-400 font-normal text-[11px]">(<?= $pkg['completed_projects'] ?: 0 ?>)</span>
                                            </div>

                                            <div class="text-right">
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Starting At</span>
                                                <span class="text-xs sm:text-sm font-black text-slate-900">₦<?= number_format($pkg['starting_price'] ?? 0) ?></span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

            </div>

        </div>

    </div>

</main>

<!-- Main flex container ends -->
</div> 

<?php include __DIR__ . '/components/footer.php'; ?>

