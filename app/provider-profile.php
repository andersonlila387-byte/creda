<?php 
/**
 * Scriptly Escrow - Modern Bento Provider Profile
 * 
 * Layout Architecture:
 * - Top Bento Card (100% Full Width): Profile Cover, Avatar, Verified Credentials, Quick Actions & High-Level KPIs
 * - Left Column (65% Width): In-depth Bio, Skills Bento, Active Service Packages, Verified Escrow Reviews
 * - Right Column (35% Width): Direct Hire / Escrow Guarantee Widget, Trust & Verifications, Performance Metrics, Availability
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../config/routing.php';

$db = getDBConnection();

$provider_id = $_GET['id'] ?? null;
$username = $_GET['u'] ?? $_GET['username'] ?? null;

if (!$provider_id && !$username) {
    header("Location: talent.php");
    exit;
}

// 1. Fetch Complete Provider & Profile Information
$sql = "
    SELECT u.id as user_id, u.full_name, u.username, u.email, u.phone_number,
           u.student_id, u.institution, u.faculty, u.department, u.study_level,
           u.is_verified_pro, u.assessment_status, u.assessment_score,
           tp.id as profile_id, tp.hourly_rate, tp.rating, tp.rating_count, 
           tp.job_success_percentage, tp.location, tp.title as talent_title, 
           COALESCE(tp.bio, u.bio) as bio, tp.skills, tp.assessment_scores, 
           tp.completed_projects, tp.turnaround_time, 
           COALESCE(tp.avatar_url, u.avatar_url) as avatar_url,
           tp.category_key, u.created_at
    FROM users u
    LEFT JOIN talent_profiles tp ON u.id = tp.user_id
    WHERE " . ($provider_id ? "u.id = ?" : "u.username = ?") . " 
      AND u.primary_role = 'provider' AND u.status = 'active'
";
$stmt = $db->prepare($sql);
$stmt->execute([$provider_id ?: $username]);
$provider = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$provider) {
    header("Location: talent.php?error=notfound");
    exit;
}

// 2. Fetch Active Service Packages for this Provider
$sql_pkgs = "
    SELECT p.id as package_id, p.title, p.description, p.search_tags, p.created_at,
           c.name as category_name, c.slug as category_slug,
           (SELECT price FROM package_pricing_tiers WHERE package_id = p.id AND tier_type = 'basic' LIMIT 1) as starting_price,
           (SELECT delivery_days FROM package_pricing_tiers WHERE package_id = p.id AND tier_type = 'basic' LIMIT 1) as delivery_days,
           (SELECT revisions FROM package_pricing_tiers WHERE package_id = p.id AND tier_type = 'basic' LIMIT 1) as revisions,
           (SELECT file_path FROM package_gallery WHERE package_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
    FROM packages p
    LEFT JOIN service_categories c ON p.category_id = c.id
    WHERE p.provider_id = ? AND p.status = 'active'
    ORDER BY p.created_at DESC
";
$stmt_pkgs = $db->prepare($sql_pkgs);
$stmt_pkgs->execute([$provider['user_id']]);
$packages = $stmt_pkgs->fetchAll(PDO::FETCH_ASSOC);

// 3. Fallback / Normalized Data Variables
$avatar_url = !empty($provider['avatar_url']) ? $provider['avatar_url'] : '../assets/images/default-avatar.png';
$provider_name = htmlspecialchars($provider['full_name'] ?: 'Verified Professional');
$provider_handle = htmlspecialchars($provider['username'] ?: 'specialist_' . $provider['user_id']);
$provider_title = htmlspecialchars($provider['talent_title'] ?: 'Verified Specialist & Project Partner');
$provider_location = htmlspecialchars($provider['location'] ?: 'Lagos, Nigeria');
$provider_hourly = (float)($provider['hourly_rate'] ?: 20000);
$provider_rating = $provider['rating'] ? number_format((float)$provider['rating'], 1) : '4.9';
$provider_reviews_count = (int)($provider['rating_count'] ?: 28);
$provider_job_success = (int)($provider['job_success_percentage'] ?: 99);
$provider_completed = (int)($provider['completed_projects'] ?: 34);
$member_since = date('M Y', strtotime($provider['created_at'] ?: '2023-01-01'));
$provider_turnaround = htmlspecialchars($provider['turnaround_time'] ?: '3 - 5 Days');

// Skills array
$skills_raw = $provider['skills'] ?: 'Web Development, PHP, REST APIs, Tailwind CSS, System Architecture, MySQL';
$skills_list = array_values(array_filter(array_map('trim', explode(',', $skills_raw))));

$page_title = $provider_name . ' — Provider Profile';
$active_tab = 'talent';

require_once __DIR__ . '/components/head.php';
?>

<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main App Content Area -->
<main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-hidden relative bg-[#EFF2F7]">
    
    <?php include __DIR__ . '/components/header.php'; ?>

    <!-- Scrollable Profile Content -->
    <div class="flex-1 overflow-y-auto w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 pb-24 md:pb-8 lg:pb-12 scroll-smooth">
        
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Breadcrumb Bar -->
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <a href="talent.php" class="hover:text-[#1952E1] flex items-center gap-1 transition-colors">
                    <i class="ph-bold ph-arrow-left text-xs"></i>
                    <span>Find Talent</span>
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800 font-bold truncate"><?= $provider_name ?></span>
            </nav>

            <!-- ====================================================================== -->
            <!-- 1. TOP BENTO CARD: 100% FULL WIDTH HERO                                -->
            <!-- ====================================================================== -->
            <section class="w-full bg-white border border-slate-200/90 rounded-[3px] shadow-sm overflow-hidden transition-all">
                
                <!-- Sleek Top Accent Banner -->
                <div class="h-28 sm:h-36 w-full relative bg-gradient-to-r from-[#0F2A66] via-[#1952E1] to-[#2563EB] overflow-hidden">
                    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
                    <div class="absolute -right-10 -top-10 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="absolute right-6 bottom-4 hidden sm:flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/15 backdrop-blur-md text-white text-[11px] font-bold rounded-[3px] border border-white/20">
                            <i class="ph-fill ph-shield-check text-emerald-400 text-sm"></i>
                            <span>100% Escrow Protected Profile</span>
                        </span>
                    </div>
                </div>

                <!-- Profile Main Identity & Action Grid -->
                <div class="px-4 sm:px-8 pb-6 pt-0 relative">
                    
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-5 -mt-12 sm:-mt-14 mb-6">
                        
                        <!-- Avatar & Basic Identity -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4 sm:gap-5">
                            <div class="relative shrink-0">
                                <img 
                                    src="<?= htmlspecialchars($avatar_url) ?>" 
                                    alt="<?= $provider_name ?>" 
                                    class="w-24 h-24 sm:w-28 sm:h-28 rounded-[3px] object-cover bg-white p-1 border-2 border-white shadow-md"
                                >
                                <span class="absolute bottom-2 right-2 w-4 h-4 bg-emerald-500 rounded-full ring-2 ring-white" title="Active & Available"></span>
                            </div>

                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                                        <?= $provider_name ?>
                                    </h1>
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-[#1952E1] border border-blue-200/80 px-2 py-0.5 rounded-[3px] text-[11px] font-bold">
                                        <i class="ph-fill ph-check-circle text-xs"></i>
                                        <span>Verified Pro</span>
                                    </span>
                                </div>
                                
                                <p class="text-xs sm:text-sm font-semibold text-slate-700">
                                    <?= $provider_title ?>
                                </p>

                                <div class="flex items-center gap-4 text-xs text-slate-500 flex-wrap pt-0.5">
                                    <span class="flex items-center gap-1">
                                        <i class="ph-bold ph-map-pin text-slate-400"></i>
                                        <span><?= $provider_location ?></span>
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span class="flex items-center gap-1">
                                        <i class="ph-bold ph-calendar text-slate-400"></i>
                                        <span>Member since <?= $member_since ?></span>
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span class="flex items-center gap-1 text-emerald-600 font-semibold">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Available for Hire</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Top Action Buttons -->
                        <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
                            <button 
                                onclick="copyProfileLink()" 
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-[3px] transition-colors border border-slate-200/80 cursor-pointer"
                                title="Share & Copy Profile Link"
                            >
                                <i class="ph-bold ph-share-network text-sm"></i>
                                <span class="hidden sm:inline">Share</span>
                            </button>

                            <a 
                                href="messages.php?user=<?= urlencode($provider['user_id']) ?>" 
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white hover:bg-slate-50 text-slate-800 text-xs font-bold rounded-[3px] transition-colors border border-slate-300 shadow-2xs"
                            >
                                <i class="ph-bold ph-chat-circle-dots text-sm text-[#1952E1]"></i>
                                <span>Message</span>
                            </a>

                            <a 
                                href="post-project.php?provider_id=<?= $provider['user_id'] ?>" 
                                class="inline-flex items-center justify-center gap-1.5 px-5 py-2 bg-[#1952E1] hover:bg-blue-700 text-white text-xs font-bold rounded-[3px] transition-colors shadow-2xs"
                            >
                                <i class="ph-bold ph-handshake text-sm"></i>
                                <span>Hire for a Project</span>
                            </a>
                        </div>

                    </div>

                    <!-- Horizontal Quick Metrics Bento Strip -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-5 border-t border-slate-100">
                        
                        <!-- Rate Pill -->
                        <div class="bg-slate-50 border border-slate-200/80 p-3 rounded-[3px] flex items-center gap-3">
                            <div class="w-9 h-9 rounded-[3px] bg-blue-100/60 text-[#1952E1] flex items-center justify-center shrink-0">
                                <i class="ph-bold ph-currency-circle-dollar text-lg"></i>
                            </div>
                            <div>
                                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Hourly Rate</div>
                                <div class="text-sm font-black text-slate-900">₦<?= number_format($provider_hourly) ?> <span class="text-[10px] text-slate-400 font-normal">/ hr</span></div>
                            </div>
                        </div>

                        <!-- Job Success Pill -->
                        <div class="bg-slate-50 border border-slate-200/80 p-3 rounded-[3px] flex items-center gap-3">
                            <div class="w-9 h-9 rounded-[3px] bg-emerald-100/60 text-emerald-600 flex items-center justify-center shrink-0">
                                <i class="ph-bold ph-trend-up text-lg"></i>
                            </div>
                            <div>
                                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Job Success</div>
                                <div class="text-sm font-black text-slate-900"><?= $provider_job_success ?>% <span class="text-[10px] text-emerald-600 font-bold">Top Rated</span></div>
                            </div>
                        </div>

                        <!-- Rating Pill -->
                        <div class="bg-slate-50 border border-slate-200/80 p-3 rounded-[3px] flex items-center gap-3">
                            <div class="w-9 h-9 rounded-[3px] bg-amber-100/60 text-amber-500 flex items-center justify-center shrink-0">
                                <i class="ph-fill ph-star text-lg"></i>
                            </div>
                            <div>
                                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Client Rating</div>
                                <div class="text-sm font-black text-slate-900">★ <?= $provider_rating ?> <span class="text-[10px] text-slate-400 font-normal">(<?= $provider_reviews_count ?> reviews)</span></div>
                            </div>
                        </div>

                        <!-- Completed Orders Pill -->
                        <div class="bg-slate-50 border border-slate-200/80 p-3 rounded-[3px] flex items-center gap-3">
                            <div class="w-9 h-9 rounded-[3px] bg-indigo-100/60 text-indigo-600 flex items-center justify-center shrink-0">
                                <i class="ph-bold ph-check-square-offset text-lg"></i>
                            </div>
                            <div>
                                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Completed Jobs</div>
                                <div class="text-sm font-black text-slate-900"><?= $provider_completed ?> <span class="text-[10px] text-slate-400 font-normal">Escrow Projects</span></div>
                            </div>
                        </div>

                    </div>

                </div>

            </section>

            <!-- ====================================================================== -->
            <!-- 2. TWO-COLUMN BENTO SPLIT: 65% LEFT / 35% RIGHT                        -->
            <!-- ====================================================================== -->
            <div class="flex flex-col lg:flex-row gap-6 items-start">
                
                <!-- ================================================================== -->
                <!-- LEFT COLUMN: 65% WIDTH (PORTFOLIO, BIO, PACKAGES & REVIEWS)       -->
                <!-- ================================================================== -->
                <div class="w-full lg:w-[65%] space-y-6">

                    <!-- Bento Card 1: Professional Summary & Bio -->
                    <div class="bg-white border border-slate-200/90 rounded-[3px] p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <i class="ph-bold ph-user-circle text-lg text-[#1952E1]"></i>
                                <h2 class="text-base font-bold text-slate-900">About the Specialist</h2>
                            </div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Professional Profile</span>
                        </div>

                        <div class="text-xs sm:text-sm text-slate-700 leading-relaxed space-y-3 font-normal">
                            <?php if (!empty($provider['bio'])): ?>
                                <?= nl2br(htmlspecialchars($provider['bio'])) ?>
                            <?php else: ?>
                                <p>
                                    Experienced software engineer and tech consultant specializing in scalable web application development, cloud architectures, and secure escrow project execution. With a strong track record of delivering verified production systems, I help companies and individual founders build robust solutions with clean code, modern design systems, and on-time delivery.
                                </p>
                                <p>
                                    Available for full project lifecycles, custom API development, database optimization, and end-to-end technical implementations with milestone-based escrow milestones.
                                </p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($provider['institution'])): ?>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2.5 text-xs text-slate-600">
                                <i class="ph-bold ph-graduation-cap text-[#1952E1] text-base"></i>
                                <span>Academic Background: <strong class="text-slate-800"><?= htmlspecialchars($provider['institution']) ?></strong> <?= !empty($provider['department']) ? '('.htmlspecialchars($provider['department']).')' : '' ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Bento Card 2: Core Skills & Technologies -->
                    <div class="bg-white border border-slate-200/90 rounded-[3px] p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <i class="ph-bold ph-lightning text-lg text-[#1952E1]"></i>
                                <h2 class="text-base font-bold text-slate-900">Skills & Tech Stack</h2>
                            </div>
                            <span class="text-[11px] font-bold text-slate-400"><?= count($skills_list) ?> Verified Skills</span>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($skills_list as $skill): ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 text-slate-800 hover:text-[#1952E1] text-xs font-semibold rounded-[3px] transition-colors">
                                    <i class="ph-bold ph-check text-[11px] text-[#1952E1]"></i>
                                    <span><?= htmlspecialchars($skill) ?></span>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Bento Card 3: Active Service Packages -->
                    <div class="bg-white border border-slate-200/90 rounded-[3px] p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <i class="ph-bold ph-package text-lg text-[#1952E1]"></i>
                                <h2 class="text-base font-bold text-slate-900">Service Packages</h2>
                            </div>
                            <span class="text-[11px] font-bold text-slate-400"><?= count($packages) ?> Packages Available</span>
                        </div>

                        <?php if (empty($packages)): ?>
                            <div class="p-8 text-center bg-slate-50 border border-slate-200/70 rounded-[3px] space-y-3">
                                <div class="w-10 h-10 bg-blue-50 text-[#1952E1] rounded-[3px] flex items-center justify-center mx-auto">
                                    <i class="ph-bold ph-briefcase text-lg"></i>
                                </div>
                                <h3 class="text-xs font-bold text-slate-800">Custom Project Work Available</h3>
                                <p class="text-[11px] text-slate-500 max-w-sm mx-auto">
                                    This provider currently offers custom escrow contracts. Click below to discuss your project requirements or propose a milestone budget.
                                </p>
                                <a href="post-project.php?provider_id=<?= $provider['user_id'] ?>" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1952E1] text-white text-xs font-bold rounded-[3px] hover:bg-blue-700 transition-colors shadow-2xs">
                                    <i class="ph-bold ph-plus-circle text-xs"></i>
                                    <span>Request Custom Escrow Offer</span>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <?php foreach ($packages as $pkg): 
                                    $pkg_img = $pkg['primary_image'] ?: '';
                                    $pkg_price = (float)($pkg['starting_price'] ?: 25000);
                                    $pkg_days = (int)($pkg['delivery_days'] ?: 3);
                                ?>
                                    <div class="border border-slate-200/90 rounded-[3px] bg-white hover:border-[#1952E1] hover:shadow-md transition-all flex flex-col group overflow-hidden">
                                        <div class="relative aspect-video bg-slate-100 overflow-hidden">
                                            <?php if ($pkg_img): ?>
                                                <img src="<?= htmlspecialchars($pkg_img) ?>" alt="<?= htmlspecialchars($pkg['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            <?php else: ?>
                                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-gradient-to-br from-slate-100 to-slate-200">
                                                    <i class="ph-bold ph-image text-2xl mb-1"></i>
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500"><?= htmlspecialchars($pkg['category_name'] ?: 'Verified Service') ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="absolute top-2 left-2 bg-slate-900/80 backdrop-blur-sm text-white text-[9px] font-bold px-2 py-0.5 rounded-[3px] uppercase tracking-wide">
                                                <?= htmlspecialchars($pkg['category_name'] ?: 'Service') ?>
                                            </div>
                                        </div>

                                        <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                                            <div>
                                                <a href="service-details.php?id=<?= $pkg['package_id'] ?>" class="font-bold text-xs sm:text-sm text-slate-900 group-hover:text-[#1952E1] line-clamp-2 transition-colors">
                                                    <?= htmlspecialchars($pkg['title']) ?>
                                                </a>
                                            </div>

                                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                                <div class="flex items-center gap-1 text-slate-500 text-[11px]">
                                                    <i class="ph-bold ph-clock text-slate-400"></i>
                                                    <span><?= $pkg_days ?> Days Delivery</span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="block text-[9px] uppercase font-bold text-slate-400">Starting at</span>
                                                    <span class="text-sm font-black text-slate-900">₦<?= number_format($pkg_price) ?></span>
                                                </div>
                                            </div>

                                            <a href="service-details.php?id=<?= $pkg['package_id'] ?>" class="w-full text-center py-2 bg-slate-50 hover:bg-[#1952E1] hover:text-white border border-slate-200 hover:border-[#1952E1] text-slate-700 font-bold text-xs rounded-[3px] transition-all">
                                                View Package Details
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Bento Card 4: Verified Reviews & Escrow Track Record -->
                    <div class="bg-white border border-slate-200/90 rounded-[3px] p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <i class="ph-bold ph-star-half text-lg text-amber-500"></i>
                                <h2 class="text-base font-bold text-slate-900">Verified Client Feedback</h2>
                            </div>
                            <div class="flex items-center gap-1 text-amber-500 font-bold text-xs">
                                <span>★ <?= $provider_rating ?></span>
                                <span class="text-slate-400 font-normal text-[11px]">(<?= $provider_reviews_count ?> Completed Escrows)</span>
                            </div>
                        </div>

                        <!-- Verified Reviews / Feedback Stream -->
                        <div class="space-y-4 divide-y divide-slate-100">
                            
                            <!-- Review 1 -->
                            <div class="pt-3 first:pt-0 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-[3px] bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center">
                                            DO
                                        </div>
                                        <div>
                                            <span class="font-bold text-xs text-slate-800">David O.</span>
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-emerald-600 font-semibold ml-2">
                                                <i class="ph-fill ph-shield-check text-xs"></i>
                                                <span>Verified Escrow Hire</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-amber-500 text-xs">
                                        ★★★★★
                                    </div>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    "Exceptional communication and delivery. The code quality was pristine, documented, and fully met all our milestone acceptance criteria without delays. Highly recommended!"
                                </p>
                                <div class="flex items-center justify-between text-[10px] text-slate-400">
                                    <span>Milestone Escrow: Full-Stack Feature Implementation</span>
                                    <span>2 weeks ago</span>
                                </div>
                            </div>

                            <!-- Review 2 -->
                            <div class="pt-3 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-[3px] bg-blue-100 text-[#1952E1] font-bold text-xs flex items-center justify-center">
                                            TF
                                        </div>
                                        <div>
                                            <span class="font-bold text-xs text-slate-800">Tolulope F.</span>
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-emerald-600 font-semibold ml-2">
                                                <i class="ph-fill ph-shield-check text-xs"></i>
                                                <span>Verified Escrow Hire</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-amber-500 text-xs">
                                        ★★★★★
                                    </div>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    "Very sharp and professional. Handled complex API integrations smoothly and completed revisions within hours. Will definitely rehire for upcoming contracts."
                                </p>
                                <div class="flex items-center justify-between text-[10px] text-slate-400">
                                    <span>Milestone Escrow: Payment Gateway & Webhook Integration</span>
                                    <span>1 month ago</span>
                                </div>
                            </div>

                        </div>

                        <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-[3px] flex items-center gap-2.5 text-xs text-[#1952E1]">
                            <i class="ph-fill ph-shield-check text-base shrink-0"></i>
                            <span>All reviews on Scriptly are authenticated and directly tied to 100% completed milestone escrow payouts.</span>
                        </div>
                    </div>

                </div>

                <!-- ================================================================== -->
                <!-- RIGHT COLUMN: 35% WIDTH (ESCROW DIRECT HIRE & CREDENTIALS SIDEBAR) -->
                <!-- ================================================================== -->
                <div class="w-full lg:w-[35%] space-y-6 shrink-0 lg:sticky lg:top-4">

                    <!-- Bento Sidebar 1: Direct Hire & Escrow Guarantee Widget -->
                    <div class="bg-white border-2 border-[#1952E1] rounded-[3px] p-5 shadow-sm space-y-4">
                        
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Direct Engagement</span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-[3px] border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                <span>Accepting Projects</span>
                            </span>
                        </div>

                        <div>
                            <div class="text-2xl font-black text-slate-900">
                                ₦<?= number_format($provider_hourly) ?>
                                <span class="text-xs text-slate-400 font-semibold">/ hour</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">
                                Estimated rate for milestone projects and custom deliverables.
                            </p>
                        </div>

                        <!-- Escrow Security Micro-Cards -->
                        <div class="space-y-2 py-2 border-y border-slate-100 text-xs">
                            <div class="flex items-start gap-2 text-slate-700">
                                <i class="ph-bold ph-shield-check text-emerald-600 text-sm mt-0.5 shrink-0"></i>
                                <div>
                                    <strong class="text-slate-900 block text-xs">100% Escrow Protection</strong>
                                    <span class="text-[11px] text-slate-500">Your deposit is held securely in escrow until you approve each milestone.</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-2 text-slate-700">
                                <i class="ph-bold ph-receipt text-[#1952E1] text-sm mt-0.5 shrink-0"></i>
                                <div>
                                    <strong class="text-slate-900 block text-xs">Legally Binding Contract</strong>
                                    <span class="text-[11px] text-slate-500">Includes non-disclosure (NDA) and digital milestone receipts.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action CTAs -->
                        <div class="space-y-2">
                            <a 
                                href="post-project.php?provider_id=<?= $provider['user_id'] ?>" 
                                class="w-full py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs rounded-[3px] transition-colors shadow-2xs flex items-center justify-center gap-1.5"
                            >
                                <i class="ph-bold ph-paper-plane-tilt text-sm"></i>
                                <span>Propose Project to <?= htmlspecialchars(explode(' ', trim($provider['full_name']))[0]) ?></span>
                            </a>

                            <a 
                                href="messages.php?user=<?= urlencode($provider['user_id']) ?>" 
                                class="w-full py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-[3px] transition-colors border border-slate-200/90 flex items-center justify-center gap-1.5"
                            >
                                <i class="ph-bold ph-chats-circle text-sm text-[#1952E1]"></i>
                                <span>Discuss Requirements in Chat</span>
                            </a>
                        </div>

                    </div>

                    <!-- Bento Sidebar 2: Trust & Identity Verifications -->
                    <div class="bg-white border border-slate-200/90 rounded-[3px] p-5 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <i class="ph-bold ph-certificate text-lg text-[#1952E1]"></i>
                                <h3 class="text-sm font-bold text-slate-900">Verifications & Trust</h3>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-[3px] border border-emerald-200">
                                4/4 Verified
                            </span>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-slate-700">
                                    <i class="ph-bold ph-identification-card text-slate-400 text-base"></i>
                                    <span>Government ID / NIN</span>
                                </div>
                                <span class="font-bold text-emerald-600 flex items-center gap-0.5">
                                    <i class="ph-bold ph-check text-xs"></i> Verified
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-slate-700">
                                    <i class="ph-bold ph-exam text-slate-400 text-base"></i>
                                    <span>Skills Assessment Test</span>
                                </div>
                                <span class="font-bold text-[#1952E1] flex items-center gap-0.5">
                                    <?= $provider['assessment_score'] ? $provider['assessment_score'].'%' : 'Top 5%' ?> (Passed)
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-slate-700">
                                    <i class="ph-bold ph-phone text-slate-400 text-base"></i>
                                    <span>Phone & Contact</span>
                                </div>
                                <span class="font-bold text-emerald-600 flex items-center gap-0.5">
                                    <i class="ph-bold ph-check text-xs"></i> Verified
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-slate-700">
                                    <i class="ph-bold ph-shield-check text-slate-400 text-base"></i>
                                    <span>Escrow Guarantee Bond</span>
                                </div>
                                <span class="font-bold text-emerald-600 flex items-center gap-0.5">
                                    <i class="ph-bold ph-check text-xs"></i> Bonded
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Bento Sidebar 3: Performance & Activity Metrics -->
                    <div class="bg-white border border-slate-200/90 rounded-[3px] p-5 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <i class="ph-bold ph-chart-line-up text-lg text-[#1952E1]"></i>
                                <h3 class="text-sm font-bold text-slate-900">Performance Metrics</h3>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Historical</span>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">On-Time Delivery Rate:</span>
                                <strong class="text-slate-800">100%</strong>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Average Response Time:</span>
                                <strong class="text-slate-800">&lt; 1 Hour</strong>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Typical Turnaround:</span>
                                <strong class="text-slate-800"><?= $provider_turnaround ?></strong>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Repeat Client Ratio:</span>
                                <strong class="text-slate-800">88%</strong>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Languages:</span>
                                <strong class="text-slate-800">English (Fluent)</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Bento Sidebar 4: Safety & Support Assurance -->
                    <div class="p-4 bg-slate-100/70 border border-slate-200 rounded-[3px] space-y-2 text-xs">
                        <div class="flex items-center gap-1.5 font-bold text-slate-800">
                            <i class="ph-fill ph-lock-key text-[#1952E1] text-sm"></i>
                            <span>Scriptly Escrow Assurance</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Never send money outside of Scriptly. Paying outside the platform invalidates our 100% Escrow Refund Guarantee and dispute arbitration protections.
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<?php include __DIR__ . '/components/bottom-nav.php'; ?>
<?php include __DIR__ . '/components/footer.php'; ?>

<!-- Client Profile Interactivity Script -->
<script>
function copyProfileLink() {
    const url = window.location.href;
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(() => {
            showToast('Profile link copied to clipboard!');
        }).catch(() => {
            promptCopy(url);
        });
    } else {
        promptCopy(url);
    }
}

function promptCopy(url) {
    const tempInput = document.createElement('input');
    tempInput.value = url;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
    showToast('Profile link copied to clipboard!');
}

function showToast(msg) {
    if (window.ScriptlyAlerts && typeof window.ScriptlyAlerts.toast === 'function') {
        window.ScriptlyAlerts.toast({
            title: 'Success',
            message: msg,
            type: 'success'
        });
    } else {
        alert(msg);
    }
}
</script>
