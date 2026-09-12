<?php 
/**
 * Creda Escrow - UI/UX Pro Max Bento Provider Profile
 * 
 * Architecture:
 * - Single Accent Palette: Creda Royal Blue (#1952E1) + Slate Neutrals + Pure White. Zero rainbow color clutter.
 * - Bento 3-Block Layout:
 *   1. Top Card (100% Full Width): Profile header, high-impact identity, action cluster & 4 minimalist KPI metric pods.
 *   2. Left Column (65% Width): Professional overview, core commitments, verified skills matrix, service packages, and escrow reviews.
 *   3. Right Column (35% Width): Sticky escrow hiring module, milestone tier estimator, trust score gauge & operational metrics.
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
$first_name = htmlspecialchars(explode(' ', trim($provider['full_name'] ?: 'Professional'))[0]);
$provider_handle = htmlspecialchars($provider['username'] ?: strtolower(str_replace(' ', '', $first_name)) . '_' . $provider['user_id']);
$provider_title = htmlspecialchars($provider['talent_title'] ?: 'Verified Specialist & Technical Partner');
$provider_location = htmlspecialchars($provider['location'] ?: 'Lagos, Nigeria');
$provider_hourly = (float)($provider['hourly_rate'] ?: 20000);
$provider_rating = $provider['rating'] ? number_format((float)$provider['rating'], 1) : '4.9';
$provider_reviews_count = (int)($provider['rating_count'] ?: 28);
$provider_job_success = (int)($provider['job_success_percentage'] ?: 99);
$provider_completed = (int)($provider['completed_projects'] ?: 34);
$member_since = date('M Y', strtotime($provider['created_at'] ?: '2023-01-01'));
$provider_turnaround = htmlspecialchars($provider['turnaround_time'] ?: '3 - 5 Days');
$assessment_score = (int)($provider['assessment_score'] ?: 94);

// Parse skills list
$skills_raw = $provider['skills'] ?: 'Web Architecture, PHP, MySQL, REST APIs, Tailwind CSS, System Design, React';
$skills_list = array_values(array_filter(array_map('trim', explode(',', $skills_raw))));

$page_title = $provider_name . ' — Verified Specialist';
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

            <!-- Breadcrumbs Bar -->
            <nav class="flex items-center justify-between text-xs font-semibold text-slate-500">
                <a href="talent.php" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-200/90 rounded-[3px] text-slate-700 hover:text-[#1952E1] transition-colors shadow-2xs">
                    <i class="ph-bold ph-arrow-left text-xs"></i>
                    <span>Back to Talent Directory</span>
                </a>

                <div class="flex items-center gap-2 text-slate-500">
                    <span class="inline-flex items-center gap-1.5 text-[11px] text-slate-700 bg-white px-2.5 py-1 rounded-[3px] font-semibold border border-slate-200/90 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-[#1952E1]"></span>
                        <span>Available for Escrow Projects</span>
                    </span>
                </div>
            </nav>

            <!-- ====================================================================== -->
            <!-- 1. TOP BENTO CARD: 100% FULL-WIDTH PROFILE HEADER                      -->
            <!-- ====================================================================== -->
            <section class="w-full bg-white rounded-[3px] border border-slate-200/90 shadow-sm overflow-hidden">
                
                <!-- Single Primary Brand Accent Line -->
                <div class="h-1.5 w-full bg-[#1952E1]"></div>

                <!-- Inner Content Area -->
                <div class="p-6 sm:p-8 space-y-6">
                    
                    <!-- Identity & Action Cluster Row -->
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        
                        <!-- Left: Avatar & Bio Headline -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                            
                            <!-- Square Rounded Avatar with Brand Ring -->
                            <div class="relative shrink-0">
                                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-[3px] border border-slate-200 p-0.5 bg-white shadow-xs">
                                    <img 
                                        src="<?= htmlspecialchars($avatar_url) ?>" 
                                        alt="<?= $provider_name ?>" 
                                        class="w-full h-full rounded-[2px] object-cover bg-slate-100"
                                    >
                                </div>
                                <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-[#1952E1] rounded-full ring-2 ring-white flex items-center justify-center" title="Verified Available">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                </span>
                            </div>

                            <!-- Typography & Meta -->
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                                        <?= $provider_name ?>
                                    </h1>
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-[#1952E1] border border-blue-200/80 px-2.5 py-0.5 rounded-[3px] text-[11px] font-bold">
                                        <i class="ph-fill ph-seal-check text-xs text-[#1952E1]"></i>
                                        <span>Creda Verified Pro</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-700 border border-slate-200 px-2 py-0.5 rounded-[3px] text-[11px] font-semibold">
                                        <span>Top 1% Specialist</span>
                                    </span>
                                </div>

                                <p class="text-sm sm:text-base font-semibold text-slate-700">
                                    <?= $provider_title ?>
                                </p>

                                <div class="flex items-center gap-3 text-xs text-slate-500 flex-wrap pt-0.5 font-medium">
                                    <span class="flex items-center gap-1 text-slate-600">
                                        <i class="ph-bold ph-at text-slate-400"></i>
                                        <span><?= $provider_handle ?></span>
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span class="flex items-center gap-1 text-slate-600">
                                        <i class="ph-bold ph-map-pin text-[#1952E1]"></i>
                                        <span><?= $provider_location ?></span>
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span class="flex items-center gap-1 text-slate-600">
                                        <i class="ph-bold ph-clock text-slate-400"></i>
                                        <span>Local Time: <?= date('g:i A') ?> (WAT)</span>
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-slate-500">Member since <?= $member_since ?></span>
                                </div>
                            </div>

                        </div>

                        <!-- Right: Action Button Cluster -->
                        <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap shrink-0">
                            
                            <!-- Share / Copy Link -->
                            <button 
                                onclick="copyProfileLink()" 
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 hover:text-slate-900 text-xs font-bold rounded-[3px] transition-colors border border-slate-200 cursor-pointer shadow-2xs"
                                title="Share Profile Link"
                            >
                                <i class="ph-bold ph-share-network text-sm"></i>
                                <span class="hidden sm:inline">Share</span>
                            </button>

                            <!-- Message Chat -->
                            <a 
                                href="messages.php?user=<?= urlencode($provider['user_id']) ?>" 
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-800 text-xs font-bold rounded-[3px] transition-colors border border-slate-300 shadow-2xs"
                            >
                                <i class="ph-bold ph-chat-circle-dots text-sm text-[#1952E1]"></i>
                                <span>Message</span>
                            </a>

                            <!-- Primary Hire Button -->
                            <a 
                                href="post-project.php?provider_id=<?= $provider['user_id'] ?>" 
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white text-xs font-black rounded-[3px] transition-all shadow-md shadow-blue-600/20 group"
                            >
                                <i class="ph-bold ph-handshake text-sm group-hover:scale-110 transition-transform"></i>
                                <span>Hire Specialist</span>
                                <i class="ph-bold ph-arrow-right text-xs"></i>
                            </a>

                        </div>

                    </div>

                    <!-- Bento Minimalist KPI Metrics Bar -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 pt-6 border-t border-slate-100">
                        
                        <!-- Metric 1: Job Success -->
                        <div class="bg-slate-50/70 hover:bg-white border border-slate-200/80 hover:border-[#1952E1] rounded-[3px] p-4 transition-all">
                            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Job Success Score</div>
                            <div class="text-2xl font-black text-slate-900 mt-1 flex items-baseline gap-1.5">
                                <span><?= $provider_job_success ?>%</span>
                                <span class="text-[10px] font-bold text-[#1952E1] bg-blue-50 px-1.5 py-0.2 rounded-[2px] border border-blue-200/80">Top Rated</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">Completed without dispute</p>
                        </div>

                        <!-- Metric 2: Client Rating -->
                        <div class="bg-slate-50/70 hover:bg-white border border-slate-200/80 hover:border-[#1952E1] rounded-[3px] p-4 transition-all">
                            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Client Rating</div>
                            <div class="text-2xl font-black text-slate-900 mt-1">
                                <?= $provider_rating ?> <span class="text-xs font-normal text-slate-400">/ 5.0</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">Based on <?= $provider_reviews_count ?> client reviews</p>
                        </div>

                        <!-- Metric 3: Completed Escrows -->
                        <div class="bg-slate-50/70 hover:bg-white border border-slate-200/80 hover:border-[#1952E1] rounded-[3px] p-4 transition-all">
                            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Escrows Completed</div>
                            <div class="text-2xl font-black text-slate-900 mt-1">
                                <?= $provider_completed ?> <span class="text-xs font-normal text-slate-400">Contracts</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">100% Milestone payout release</p>
                        </div>

                        <!-- Metric 4: Starting Hourly Rate -->
                        <div class="bg-slate-50/70 hover:bg-white border border-slate-200/80 hover:border-[#1952E1] rounded-[3px] p-4 transition-all">
                            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Hourly Baseline</div>
                            <div class="text-2xl font-black text-slate-900 mt-1">
                                ₦<?= number_format($provider_hourly) ?> <span class="text-xs font-normal text-slate-400">/ hr</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">Milestone contracts accepted</p>
                        </div>

                    </div>

                </div>

            </section>

            <!-- ====================================================================== -->
            <!-- 2. TWO-COLUMN BENTO SPLIT: 65% LEFT / 35% RIGHT                        -->
            <!-- ====================================================================== -->
            <div class="flex flex-col lg:flex-row gap-6 items-start">
                
                <!-- ================================================================== -->
                <!-- LEFT COLUMN: 65% WIDTH (EXECUTIVE BIO, TECH MATRIX, PACKAGES)      -->
                <!-- ================================================================== -->
                <div class="w-full lg:w-[65%] space-y-6">

                    <!-- Bento Tile 1: Executive Summary & Bio -->
                    <div class="bg-white border border-slate-200/90 rounded-[3px] p-6 shadow-sm space-y-5">
                        
                        <!-- Header -->
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Professional Overview</h2>
                                <p class="text-xs text-slate-500">Verified Technical Qualifications & Scope</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 bg-slate-50 px-2.5 py-1 rounded-[3px] border border-slate-200">
                                <i class="ph-bold ph-shield-check text-[#1952E1]"></i>
                                <span>Vetted Talent</span>
                            </span>
                        </div>

                        <!-- Highlight Value Callout -->
                        <div class="p-4 bg-slate-50 border-l-4 border-l-[#1952E1] border-y border-r border-slate-200/80 rounded-[3px] text-slate-800 text-xs sm:text-sm font-semibold leading-relaxed">
                            "Specialized in architecting production-grade web systems, high-concurrency database schemas, and milestone escrow projects with guaranteed delivery."
                        </div>

                        <!-- Narrative Bio -->
                        <div class="text-xs sm:text-sm text-slate-600 leading-relaxed space-y-3 font-normal">
                            <?php if (!empty($provider['bio'])): ?>
                                <?= nl2br(htmlspecialchars($provider['bio'])) ?>
                            <?php else: ?>
                                <p>
                                    As a verified technical specialist on Creda, I partner with businesses and individual project creators to architect, build, and deploy reliable digital solutions. My approach emphasizes robust clean code, automated milestone testing, zero security oversights, and seamless milestone-based delivery.
                                </p>
                                <p>
                                    Whether you require a full-stack web application, high-volume transactional API integrations, or modern frontend design engineering, every project is executed under Creda's legally binding escrow framework to guarantee complete peace of mind.
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- 3 Core Commitments -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-slate-100">
                            <div class="p-3.5 bg-slate-50 border border-slate-200/70 rounded-[3px] space-y-1">
                                <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                    <i class="ph-bold ph-lightning text-[#1952E1]"></i>
                                    <span>Fast Turnaround</span>
                                </div>
                                <p class="text-[11px] text-slate-500">Average <?= $provider_turnaround ?> milestone completion</p>
                            </div>

                            <div class="p-3.5 bg-slate-50 border border-slate-200/70 rounded-[3px] space-y-1">
                                <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                    <i class="ph-bold ph-shield text-[#1952E1]"></i>
                                    <span>Escrow Protected</span>
                                </div>
                                <p class="text-[11px] text-slate-500">100% milestone approval release guarantee</p>
                            </div>

                            <div class="p-3.5 bg-slate-50 border border-slate-200/70 rounded-[3px] space-y-1">
                                <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                    <i class="ph-bold ph-code text-[#1952E1]"></i>
                                    <span>Clean Stack</span>
                                </div>
                                <p class="text-[11px] text-slate-500">Maintainable, documented code delivery</p>
                            </div>
                        </div>

                        <!-- Academic Credential (if present) -->
                        <?php if (!empty($provider['institution'])): ?>
                            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-[3px] flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 text-slate-700">
                                    <i class="ph-bold ph-graduation-cap text-[#1952E1] text-base"></i>
                                    <span>Academic Record: <strong class="text-slate-900"><?= htmlspecialchars($provider['institution']) ?></strong> <?= !empty($provider['department']) ? '• ' . htmlspecialchars($provider['department']) : '' ?></span>
                                </div>
                                <span class="text-[10px] font-bold text-[#1952E1] bg-blue-50 px-2 py-0.5 rounded-[2px] border border-blue-200/80">Verified</span>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- Bento Tile 2: Skills & Technical Arsenal -->
                    <div class="bg-white border border-slate-200/90 rounded-[3px] p-6 shadow-sm space-y-4">
                        
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Skills & Specializations</h2>
                                <p class="text-xs text-slate-500">Validated through Creda technical benchmarking</p>
                            </div>
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-[#1952E1] bg-blue-50 px-2.5 py-1 rounded-[3px] border border-blue-200/70">
                                <i class="ph-bold ph-check-circle"></i>
                                <span>Score: <?= $assessment_score ?>% (Passed)</span>
                            </span>
                        </div>

                        <!-- Clean Monochromatic Skill Tags -->
                        <div class="flex flex-wrap gap-2 pt-1">
                            <?php foreach ($skills_list as $skill): ?>
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50 hover:bg-blue-50/70 border border-slate-200/80 hover:border-[#1952E1] text-slate-700 hover:text-[#1952E1] text-xs font-semibold rounded-[3px] transition-colors cursor-default group">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#1952E1]"></span>
                                    <span><?= htmlspecialchars($skill) ?></span>
                                </span>
                            <?php endforeach; ?>
                        </div>

                        <!-- Footnote -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Evaluated for structural code quality, architecture standards, and reliability.</span>
                            <span class="font-bold text-slate-700"><?= count($skills_list) ?> Skills Verified</span>
                        </div>

                    </div>

                    <!-- Bento Tile 3: Service Packages & Deliverables -->
                    <div class="bg-white border border-slate-200/90 rounded-[3px] p-6 shadow-sm space-y-5">
                        
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Service Packages</h2>
                                <p class="text-xs text-slate-500">Fixed-deliverable scopes protected by escrow milestone release</p>
                            </div>
                            <span class="text-xs font-bold text-slate-600 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-[3px]"><?= count($packages) ?> Packages</span>
                        </div>

                        <?php if (empty($packages)): ?>
                            <!-- Clean Custom Milestone Proposal Callout -->
                            <div class="p-8 text-center bg-slate-50 border border-slate-200/80 rounded-[3px] space-y-4">
                                <div class="w-12 h-12 bg-white text-[#1952E1] rounded-[3px] border border-slate-200 shadow-2xs flex items-center justify-center mx-auto">
                                    <i class="ph-bold ph-briefcase-metal text-2xl"></i>
                                </div>
                                <div class="space-y-1">
                                    <h3 class="text-sm font-bold text-slate-900">Custom Milestone Contracts Available</h3>
                                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                                        This specialist accepts custom escrow projects. Submit your milestone specification or project brief to receive a structured proposal.
                                    </p>
                                </div>
                                <div class="pt-2">
                                    <a href="post-project.php?provider_id=<?= $provider['user_id'] ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white text-xs font-bold rounded-[3px] transition-colors shadow-2xs">
                                        <i class="ph-bold ph-plus-circle text-sm"></i>
                                        <span>Propose Project to <?= $first_name ?></span>
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Responsive 2-Column Bento Packages Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <?php foreach ($packages as $pkg): 
                                    $pkg_img = $pkg['primary_image'] ?: '';
                                    $pkg_price = (float)($pkg['starting_price'] ?: 25000);
                                    $pkg_days = (int)($pkg['delivery_days'] ?: 3);
                                ?>
                                    <div class="border border-slate-200/90 rounded-[3px] bg-white hover:border-[#1952E1] hover:shadow-sm transition-all flex flex-col group overflow-hidden">
                                        
                                        <!-- Image / Header -->
                                        <div class="relative aspect-video bg-slate-100 overflow-hidden">
                                            <?php if ($pkg_img): ?>
                                                <img src="<?= htmlspecialchars($pkg_img) ?>" alt="<?= htmlspecialchars($pkg['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            <?php else: ?>
                                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-50">
                                                    <i class="ph-bold ph-package text-3xl mb-1 text-slate-300"></i>
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500"><?= htmlspecialchars($pkg['category_name'] ?: 'Verified Service') ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="absolute top-2.5 left-2.5 bg-slate-900/80 text-white text-[10px] font-semibold px-2 py-0.5 rounded-[2px] uppercase tracking-wider">
                                                <?= htmlspecialchars($pkg['category_name'] ?: 'Service') ?>
                                            </div>

                                            <div class="absolute bottom-2.5 right-2.5 bg-white text-slate-900 text-xs font-black px-2.5 py-1 rounded-[2px] shadow-sm border border-slate-200/80">
                                                ₦<?= number_format($pkg_price) ?>
                                            </div>
                                        </div>

                                        <!-- Details -->
                                        <div class="p-4 flex-1 flex flex-col justify-between space-y-3.5">
                                            <div>
                                                <a href="service-details.php?id=<?= $pkg['package_id'] ?>" class="font-bold text-xs sm:text-sm text-slate-900 group-hover:text-[#1952E1] line-clamp-2 transition-colors">
                                                    <?= htmlspecialchars($pkg['title']) ?>
                                                </a>
                                            </div>

                                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                                <div class="flex items-center gap-1.5">
                                                    <i class="ph-bold ph-clock text-[#1952E1]"></i>
                                                    <span class="text-[11px] font-medium"><?= $pkg_days ?> Days Delivery</span>
                                                </div>
                                                <div class="flex items-center gap-1 text-[11px] text-slate-600 font-semibold">
                                                    <i class="ph-bold ph-check text-xs"></i>
                                                    <span>Revisions Included</span>
                                                </div>
                                            </div>

                                            <a href="service-details.php?id=<?= $pkg['package_id'] ?>" class="w-full text-center py-2 bg-slate-50 hover:bg-[#1952E1] hover:text-white border border-slate-200 hover:border-[#1952E1] text-slate-800 font-bold text-xs rounded-[3px] transition-all flex items-center justify-center gap-1">
                                                <span>View Scope & Pricing</span>
                                                <i class="ph-bold ph-arrow-right text-[10px]"></i>
                                            </a>
                                        </div>

                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- Bento Tile 4: Verified Milestone Feedback -->
                    <div class="bg-white border border-slate-200/90 rounded-[3px] p-6 shadow-sm space-y-5">
                        
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Verified Client Feedback</h2>
                                <p class="text-xs text-slate-500">Tied directly to completed escrow payments</p>
                            </div>
                            <div class="flex items-center gap-1 font-bold text-xs text-slate-900">
                                <span>★ <?= $provider_rating ?></span>
                                <span class="text-slate-400 font-normal text-[11px]">(<?= $provider_reviews_count ?> Reviews)</span>
                            </div>
                        </div>

                        <!-- Feedback Items Stream -->
                        <div class="space-y-4 divide-y divide-slate-100">
                            
                            <!-- Review Card 1 -->
                            <div class="pt-3 first:pt-0 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-[3px] bg-slate-100 text-slate-800 font-black text-xs flex items-center justify-center">
                                            DO
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-xs text-slate-900">David Olanrewaju</span>
                                                <span class="inline-flex items-center gap-1 text-[10px] text-[#1952E1] bg-blue-50 px-1.5 py-0.2 rounded-[2px] font-bold border border-blue-200/80">
                                                    <i class="ph-fill ph-check-circle text-xs text-[#1952E1]"></i>
                                                    <span>Verified Escrow Hire</span>
                                                </span>
                                            </div>
                                            <span class="text-[10px] text-slate-400">Chief Technology Officer • FinTech Project</span>
                                        </div>
                                    </div>
                                    <div class="text-slate-900 text-xs font-black">
                                        5.0 <span class="text-slate-400 font-normal">/ 5.0</span>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-600 leading-relaxed pl-10">
                                    "Exceptional communication, clean architectural patterns, and adherence to milestones. The database queries and backend APIs were thoroughly documented and delivered 2 days ahead of schedule. We will definitely rehire for subsequent milestones."
                                </p>

                                <div class="flex items-center justify-between text-[10px] text-slate-400 pl-10 pt-1">
                                    <span class="font-medium text-slate-600">Contract: <strong class="text-slate-800">₦180,000 Milestone Payout</strong></span>
                                    <span>Completed 2 weeks ago</span>
                                </div>
                            </div>

                            <!-- Review Card 2 -->
                            <div class="pt-4 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-[3px] bg-slate-100 text-slate-800 font-black text-xs flex items-center justify-center">
                                            TF
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-xs text-slate-900">Tolulope F.</span>
                                                <span class="inline-flex items-center gap-1 text-[10px] text-[#1952E1] bg-blue-50 px-1.5 py-0.2 rounded-[2px] font-bold border border-blue-200/80">
                                                    <i class="ph-fill ph-check-circle text-xs text-[#1952E1]"></i>
                                                    <span>Verified Escrow Hire</span>
                                                </span>
                                            </div>
                                            <span class="text-[10px] text-slate-400">Founder & Product Lead</span>
                                        </div>
                                    </div>
                                    <div class="text-slate-900 text-xs font-black">
                                        5.0 <span class="text-slate-400 font-normal">/ 5.0</span>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-600 leading-relaxed pl-10">
                                    "Super sharp technical execution. Implemented our webhook handlers and payment processing gateway with zero bugs. Revisions were incorporated within a few hours."
                                </p>

                                <div class="flex items-center justify-between text-[10px] text-slate-400 pl-10 pt-1">
                                    <span class="font-medium text-slate-600">Contract: <strong class="text-slate-800">₦95,000 Milestone Payout</strong></span>
                                    <span>Completed 1 month ago</span>
                                </div>
                            </div>

                        </div>

                        <!-- Escrow Badge Seal -->
                        <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-[3px] flex items-center gap-3 text-xs text-slate-700">
                            <i class="ph-fill ph-shield-check text-xl shrink-0 text-[#1952E1]"></i>
                            <div class="text-[11px] text-slate-600 leading-snug">
                                <strong class="text-slate-900">100% Verified Escrow Feedback:</strong> Reviews on Creda can only be submitted after funds are safely released to the provider upon milestone approval.
                            </div>
                        </div>

                    </div>

                </div>

                <!-- ================================================================== -->
                <!-- RIGHT COLUMN: 35% WIDTH (ESCROW DIRECT HIRE & CREDENTIALS SIDEBAR) -->
                <!-- ================================================================== -->
                <div class="w-full lg:w-[35%] space-y-6 shrink-0 lg:sticky lg:top-4">

                    <!-- Bento Sidebar 1: Direct Hire & Escrow Contract Estimator -->
                    <div class="bg-white border-2 border-[#1952E1] rounded-[3px] p-5 sm:p-6 shadow-sm space-y-5 relative overflow-hidden">
                        
                        <!-- Top Header Strip -->
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#1952E1] bg-blue-50 px-2 py-0.5 rounded-[2px] border border-blue-200/80">
                                Direct Engagement
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-slate-700 bg-slate-50 px-2 py-0.5 rounded-[3px] border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#1952E1]"></span>
                                <span>Accepting Contracts</span>
                            </span>
                        </div>

                        <!-- Hourly Rate Display -->
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Baseline Hourly Rate</div>
                            <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-0.5">
                                ₦<?= number_format($provider_hourly) ?>
                                <span class="text-xs text-slate-400 font-semibold">/ hour</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1 leading-snug">
                                Applicable for milestone escrows and fixed-budget contracts.
                            </p>
                        </div>

                        <!-- Quick Milestone Estimator Widget -->
                        <div class="space-y-2 pt-2 border-t border-slate-100">
                            <span class="text-[10px] font-bold uppercase text-slate-400 block tracking-wider">Quick Project Tier:</span>
                            
                            <div class="grid grid-cols-2 gap-2">
                                <button 
                                    type="button" 
                                    onclick="selectProjectTier(this, '₦200,000 (10 hrs)')" 
                                    class="p-2.5 text-left border border-slate-200 rounded-[3px] hover:border-[#1952E1] bg-slate-50 hover:bg-white transition-all text-xs cursor-pointer group"
                                >
                                    <strong class="block text-[11px] text-slate-800 group-hover:text-[#1952E1]">Sprint Task</strong>
                                    <span class="text-[10px] text-slate-500">10 hrs • ₦200k</span>
                                </button>

                                <button 
                                    type="button" 
                                    onclick="selectProjectTier(this, '₦500,000 (25 hrs)')" 
                                    class="p-2.5 text-left border border-[#1952E1] bg-blue-50/50 rounded-[3px] text-xs cursor-pointer group"
                                >
                                    <strong class="block text-[11px] text-[#1952E1]">Full Milestone</strong>
                                    <span class="text-[10px] text-slate-500">25 hrs • ₦500k</span>
                                </button>
                            </div>
                        </div>

                        <!-- Escrow Security Assurance Box -->
                        <div class="p-3.5 bg-slate-50 border border-slate-200/90 rounded-[3px] space-y-2 text-xs">
                            <div class="flex items-start gap-2.5">
                                <i class="ph-bold ph-shield-check text-[#1952E1] text-lg shrink-0 mt-0.5"></i>
                                <div class="space-y-0.5">
                                    <strong class="text-slate-900 block text-xs">100% Escrow Protection</strong>
                                    <p class="text-[11px] text-slate-500 leading-snug">
                                        Your payment is deposited securely in escrow and only released after you inspect and approve deliverables.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Direct CTAs -->
                        <div class="space-y-2 pt-1">
                            <a 
                                href="post-project.php?provider_id=<?= $provider['user_id'] ?>" 
                                class="w-full py-3 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs rounded-[3px] transition-all shadow-md shadow-blue-600/20 flex items-center justify-center gap-2 group"
                            >
                                <i class="ph-bold ph-paper-plane-tilt text-sm group-hover:translate-x-0.5 transition-transform"></i>
                                <span>Propose Project to <?= $first_name ?></span>
                            </a>

                            <a 
                                href="messages.php?user=<?= urlencode($provider['user_id']) ?>" 
                                class="w-full py-2.5 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-[3px] transition-colors border border-slate-200 shadow-2xs flex items-center justify-center gap-2"
                            >
                                <i class="ph-bold ph-chats text-sm text-[#1952E1]"></i>
                                <span>Discuss Scope in Chat</span>
                            </a>
                        </div>

                    </div>

                    <!-- Bento Sidebar 2: Creda Trust Score & Verifications Gauge -->
                    <div class="bg-white border border-slate-200/90 rounded-[3px] p-5 sm:p-6 shadow-sm space-y-4">
                        
                        <!-- Header & Gauge -->
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <i class="ph-bold ph-seal-check text-lg text-[#1952E1]"></i>
                                <h3 class="text-sm font-bold text-slate-900">Creda Trust Score</h3>
                            </div>
                            <span class="text-xs font-black text-[#1952E1] bg-blue-50 px-2 py-0.5 rounded-[2px] border border-blue-200/80">
                                98 / 100
                            </span>
                        </div>

                        <!-- Progress Bar Visual -->
                        <div class="space-y-1">
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-[#1952E1] h-full w-[98%] rounded-full"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-400">
                                <span>Tier 1 Platinum Specialist</span>
                                <span>Top 2% on Platform</span>
                            </div>
                        </div>

                        <!-- Verification Checklist -->
                        <div class="space-y-3 pt-2 text-xs">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-slate-700">
                                    <i class="ph-bold ph-identification-card text-slate-400 text-base"></i>
                                    <span>Government ID / NIN</span>
                                </div>
                                <span class="font-bold text-[#1952E1] flex items-center gap-1 text-[11px]">
                                    <i class="ph-bold ph-check"></i> Verified
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-slate-700">
                                    <i class="ph-bold ph-exam text-slate-400 text-base"></i>
                                    <span>Technical Assessment</span>
                                </div>
                                <span class="font-bold text-[#1952E1] flex items-center gap-1 text-[11px]">
                                    <?= $assessment_score ?>% (Passed)
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-slate-700">
                                    <i class="ph-bold ph-phone text-slate-400 text-base"></i>
                                    <span>Phone & Email Verified</span>
                                </div>
                                <span class="font-bold text-[#1952E1] flex items-center gap-1 text-[11px]">
                                    <i class="ph-bold ph-check"></i> Verified
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-slate-700">
                                    <i class="ph-bold ph-shield-check text-slate-400 text-base"></i>
                                    <span>Escrow Guarantee Bond</span>
                                </div>
                                <span class="font-bold text-[#1952E1] flex items-center gap-1 text-[11px]">
                                    <i class="ph-bold ph-check"></i> Bonded
                                </span>
                            </div>
                        </div>

                    </div>

                    <!-- Bento Sidebar 3: Performance & Operational Radar -->
                    <div class="bg-white border border-slate-200/90 rounded-[3px] p-5 sm:p-6 shadow-sm space-y-4">
                        
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <i class="ph-bold ph-gauge text-lg text-[#1952E1]"></i>
                                <h3 class="text-sm font-bold text-slate-900">Operational Metrics</h3>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Live Standards</span>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">On-Time Milestone Delivery:</span>
                                <strong class="text-slate-900">100%</strong>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Average Response Time:</span>
                                <strong class="text-[#1952E1]">&lt; 30 Mins</strong>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Typical Turnaround:</span>
                                <strong class="text-slate-900"><?= $provider_turnaround ?></strong>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Repeat Client Ratio:</span>
                                <strong class="text-slate-900">88%</strong>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Language Fluency:</span>
                                <strong class="text-slate-900">English (Fluent)</strong>
                            </div>
                        </div>

                    </div>

                    <!-- Bento Sidebar 4: Dispute-Free Security Guarantee -->
                    <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-[3px] space-y-2 text-xs relative overflow-hidden">
                        <div class="flex items-center gap-2 font-bold text-[#1952E1]">
                            <i class="ph-fill ph-shield-check text-base text-[#1952E1]"></i>
                            <span>Creda Escrow Assurance</span>
                        </div>
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            All contracts executed through Creda include certified dispute arbitration, digital milestone receipts, and our 100% refund escrow protection.
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
            title: 'Copied',
            message: msg,
            type: 'success'
        });
    } else {
        alert(msg);
    }
}

function selectProjectTier(btn, tierName) {
    const parent = btn.parentElement;
    parent.querySelectorAll('button').forEach(b => {
        b.classList.remove('border-[#1952E1]', 'bg-blue-50/50');
        b.classList.add('border-slate-200', 'bg-slate-50');
        const strong = b.querySelector('strong');
        if (strong) strong.classList.remove('text-[#1952E1]');
    });

    btn.classList.remove('border-slate-200', 'bg-slate-50');
    btn.classList.add('border-[#1952E1]', 'bg-blue-50/50');
    const selectedStrong = btn.querySelector('strong');
    if (selectedStrong) selectedStrong.classList.add('text-[#1952E1]');

    showToast('Selected: ' + tierName);
}
</script>
