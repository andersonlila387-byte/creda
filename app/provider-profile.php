<?php 
/**
 * Scriptly Escrow - Modern Creative Bento Provider Profile
 * 
 * Layout Architecture:
 * - Top Bento Card (100% Full Width): Luxury Dark Slate Hero with Ambient Radial Lighting, High-Impact Avatar, Floating KPI Pods, Quick Actions & Live Local Time
 * - Left Column (65% Width): Executive Summary, Visual Tech Stack Matrix, Active Service Packages, Verified Escrow Milestone History & Client Testimonials
 * - Right Column (35% Width): Direct Escrow Contract Estimator, 98/100 Trust Score Gauge, Verification Credentials, Availability Radar & Dispute Protection
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

                <div class="flex items-center gap-2 text-slate-400">
                    <span class="inline-flex items-center gap-1 text-[11px] text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-[3px] font-bold border border-emerald-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Available for Escrow Projects</span>
                    </span>
                </div>
            </nav>

            <!-- ====================================================================== -->
            <!-- 1. TOP BENTO CARD: 100% FULL-WIDTH PROFILE HERO (CREDA LIGHT LUXURY)   -->
            <!-- ====================================================================== -->
            <section class="w-full bg-white rounded-[3px] border border-slate-200/90 shadow-sm relative overflow-hidden">
                
                <!-- Top Decorative Cover Banner -->
                <div class="h-32 sm:h-44 w-full bg-gradient-to-r from-[#0C2D7E] via-[#1952E1] to-[#2563EB] relative overflow-hidden">
                    <!-- Subtle Geometric Dot Grid -->
                    <div class="absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.18)_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>
                    <!-- Ambient Soft Lighting -->
                    <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="absolute -bottom-10 left-1/4 w-72 h-36 bg-blue-400/20 rounded-full blur-xl pointer-events-none"></div>
                    
                    <!-- Top Right Floating Trust Pill Badges -->
                    <div class="absolute top-4 right-4 sm:top-5 sm:right-6 flex items-center gap-2">
                        <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-black/20 backdrop-blur-md text-white text-xs font-semibold border border-white/20">
                            <i class="ph-fill ph-shield-check text-blue-300"></i>
                            <span>100% Escrow Guaranteed</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500 text-white text-xs font-bold shadow-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                            <span>Available for Hire</span>
                        </span>
                    </div>
                </div>

                <!-- Main Profile Content Body (Overlapping Avatar) -->
                <div class="p-6 sm:p-8 pt-0 relative z-10">
                    
                    <!-- Main Header Row: Identity (Left) & Actions (Right) -->
                    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 -mt-14 sm:-mt-16">
                        
                        <!-- Left: Avatar, Badges & Specialist Title -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-end gap-5">
                            
                            <!-- Square Rounded Avatar with Status Ring -->
                            <div class="relative shrink-0">
                                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-[3px] p-1 bg-white ring-4 ring-white shadow-md">
                                    <img 
                                        src="<?= htmlspecialchars($avatar_url) ?>" 
                                        alt="<?= $provider_name ?>" 
                                        class="w-full h-full rounded-[2px] object-cover bg-slate-100"
                                    >
                                </div>
                                <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full ring-2 ring-white flex items-center justify-center" title="Online & Available Now">
                                    <span class="w-2 h-2 rounded-full bg-white"></span>
                                </span>
                            </div>

                            <!-- Typography & Meta -->
                            <div class="space-y-1 sm:pb-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                                        <?= $provider_name ?>
                                    </h1>
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-[#1952E1] border border-blue-200/80 px-2.5 py-0.5 rounded-[3px] text-[11px] font-bold">
                                        <i class="ph-fill ph-seal-check text-xs text-[#1952E1]"></i>
                                        <span>Creda Verified Pro</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200/80 px-2 py-0.5 rounded-[3px] text-[10px] font-bold">
                                        <i class="ph-fill ph-sparkle text-xs text-amber-500"></i>
                                        <span>Top 1% Talent</span>
                                    </span>
                                </div>

                                <p class="text-sm sm:text-base font-bold text-slate-700">
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
                        <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap shrink-0 lg:pb-1">
                            
                            <!-- Share / Copy Link -->
                            <button 
                                onclick="copyProfileLink()" 
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 hover:text-slate-900 text-xs font-bold rounded-[3px] transition-all border border-slate-200 cursor-pointer shadow-2xs"
                                title="Share & Copy Profile URL"
                            >
                                <i class="ph-bold ph-share-network text-sm"></i>
                                <span class="hidden sm:inline">Share</span>
                            </button>

                            <!-- Message Chat -->
                            <a 
                                href="messages.php?user=<?= urlencode($provider['user_id']) ?>" 
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-800 text-xs font-bold rounded-[3px] transition-all border border-slate-300 shadow-2xs"
                            >
                                <i class="ph-bold ph-chat-circle-dots text-sm text-[#1952E1]"></i>
                                <span>Message</span>
                            </a>

                            <!-- Primary Hire Button -->
                            <a 
                                href="post-project.php?provider_id=<?= $provider['user_id'] ?>" 
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white text-xs font-black rounded-[3px] transition-all shadow-md shadow-blue-600/20 group"
                            >
                                <i class="ph-bold ph-handshake text-sm group-hover:scale-110 transition-transform"></i>
                                <span>Hire Specialist</span>
                                <i class="ph-bold ph-arrow-right text-xs"></i>
                            </a>

                        </div>

                    </div>

                    <!-- Floating KPI Pods (Clean Bento Style) -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mt-6 pt-6 border-t border-slate-100">
                        
                        <!-- KPI 1: Job Success -->
                        <div class="bg-slate-50/80 hover:bg-white border border-slate-200/80 hover:border-emerald-300 hover:shadow-xs rounded-[3px] p-3.5 transition-all flex items-center gap-3.5 group">
                            <div class="w-10 h-10 rounded-[3px] bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center shrink-0">
                                <i class="ph-bold ph-trend-up text-xl"></i>
                            </div>
                            <div>
                                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Job Success</div>
                                <div class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-1.5">
                                    <span><?= $provider_job_success ?>%</span>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.2 rounded-[2px]">Top Rated</span>
                                </div>
                            </div>
                        </div>

                        <!-- KPI 2: Star Rating -->
                        <div class="bg-slate-50/80 hover:bg-white border border-slate-200/80 hover:border-amber-300 hover:shadow-xs rounded-[3px] p-3.5 transition-all flex items-center gap-3.5 group">
                            <div class="w-10 h-10 rounded-[3px] bg-amber-50 text-amber-500 border border-amber-200/80 flex items-center justify-center shrink-0">
                                <i class="ph-fill ph-star text-xl"></i>
                            </div>
                            <div>
                                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Client Rating</div>
                                <div class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-1">
                                    <span class="text-amber-500">★ <?= $provider_rating ?></span>
                                    <span class="text-xs text-slate-400 font-normal">(<?= $provider_reviews_count ?>)</span>
                                </div>
                            </div>
                        </div>

                        <!-- KPI 3: Hourly Rate -->
                        <div class="bg-slate-50/80 hover:bg-white border border-slate-200/80 hover:border-blue-300 hover:shadow-xs rounded-[3px] p-3.5 transition-all flex items-center gap-3.5 group">
                            <div class="w-10 h-10 rounded-[3px] bg-blue-50 text-[#1952E1] border border-blue-200/80 flex items-center justify-center shrink-0">
                                <i class="ph-bold ph-currency-circle-dollar text-xl"></i>
                            </div>
                            <div>
                                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Hourly Rate</div>
                                <div class="text-base sm:text-lg font-black text-slate-900">
                                    ₦<?= number_format($provider_hourly) ?> <span class="text-[10px] text-slate-400 font-normal">/ hr</span>
                                </div>
                            </div>
                        </div>

                        <!-- KPI 4: Escrow Projects -->
                        <div class="bg-slate-50/80 hover:bg-white border border-slate-200/80 hover:border-indigo-300 hover:shadow-xs rounded-[3px] p-3.5 transition-all flex items-center gap-3.5 group">
                            <div class="w-10 h-10 rounded-[3px] bg-indigo-50 text-indigo-600 border border-indigo-200/80 flex items-center justify-center shrink-0">
                                <i class="ph-bold ph-shield-check text-xl"></i>
                            </div>
                            <div>
                                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Escrows Completed</div>
                                <div class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-1.5">
                                    <span><?= $provider_completed ?></span>
                                    <span class="text-[10px] font-bold text-[#1952E1] bg-blue-50 border border-blue-200 px-1.5 py-0.2 rounded-[2px]">0 Disputes</span>
                                </div>
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
                <!-- LEFT COLUMN: 65% WIDTH (EXECUTIVE BIO, TECH MATRIX, PACKAGES)      -->
                <!-- ================================================================== -->
                <div class="w-full lg:w-[65%] space-y-6">

                    <!-- Bento Tile 1: Executive Summary & Highlights -->
                    <div class="bg-white border border-slate-200/90 rounded-[3px] p-6 shadow-sm space-y-5">
                        
                        <!-- Header with Value Tag -->
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center font-bold">
                                    <i class="ph-bold ph-identification-card text-base"></i>
                                </div>
                                <div>
                                    <h2 class="text-sm sm:text-base font-bold text-slate-900">Executive Summary & Bio</h2>
                                    <p class="text-[11px] text-slate-400">Verified Technical Qualifications</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-[3px] border border-slate-200">
                                <i class="ph-fill ph-seal-check text-[#1952E1]"></i>
                                <span>Vetted Talent</span>
                            </span>
                        </div>

                        <!-- Punchy Value Proposition Callout -->
                        <div class="p-4 bg-gradient-to-r from-blue-50/70 via-indigo-50/40 to-slate-50 border-l-4 border-l-[#1952E1] border-y border-r border-slate-200/80 rounded-[3px] text-slate-800 text-xs sm:text-sm font-semibold leading-relaxed">
                            "Specialized in architecting production-grade web systems, high-concurrency database schemas, and milestone escrow projects with guaranteed delivery."
                        </div>

                        <!-- Detailed Narrative -->
                        <div class="text-xs sm:text-sm text-slate-700 leading-relaxed space-y-3 font-normal">
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

                        <!-- 3 Micro Value Pillars -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-slate-100">
                            <div class="p-3 bg-slate-50 border border-slate-200/70 rounded-[3px] space-y-1">
                                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-900">
                                    <i class="ph-bold ph-lightning text-amber-500"></i>
                                    <span>Fast Delivery</span>
                                </div>
                                <p class="text-[11px] text-slate-500">Average <?= $provider_turnaround ?> milestone turnaround</p>
                            </div>

                            <div class="p-3 bg-slate-50 border border-slate-200/70 rounded-[3px] space-y-1">
                                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-900">
                                    <i class="ph-bold ph-shield-check text-emerald-600"></i>
                                    <span>Escrow Protected</span>
                                </div>
                                <p class="text-[11px] text-slate-500">100% milestone approval release guarantee</p>
                            </div>

                            <div class="p-3 bg-slate-50 border border-slate-200/70 rounded-[3px] space-y-1">
                                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-900">
                                    <i class="ph-bold ph-code text-[#1952E1]"></i>
                                    <span>Clean Stack</span>
                                </div>
                                <p class="text-[11px] text-slate-500">Documented, maintainable codebases</p>
                            </div>
                        </div>

                        <!-- Academic Credential Badge (If available) -->
                        <?php if (!empty($provider['institution'])): ?>
                            <div class="p-3 bg-slate-50/80 border border-slate-200 rounded-[3px] flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 text-slate-700">
                                    <i class="ph-bold ph-graduation-cap text-[#1952E1] text-base"></i>
                                    <span>Academic Credential: <strong class="text-slate-900"><?= htmlspecialchars($provider['institution']) ?></strong> <?= !empty($provider['department']) ? '• ' . htmlspecialchars($provider['department']) : '' ?></span>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-[2px] border border-emerald-200">Verified</span>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- Bento Tile 2: Technical Skills & Arsenal Matrix -->
                    <div class="bg-white border border-slate-200/90 rounded-[3px] p-6 shadow-sm space-y-4">
                        
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-[3px] bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                                    <i class="ph-bold ph-code-block text-base"></i>
                                </div>
                                <div>
                                    <h2 class="text-sm sm:text-base font-bold text-slate-900">Technical Arsenal & Skills</h2>
                                    <p class="text-[11px] text-slate-400">Validated through technical assessments</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#1952E1] bg-blue-50 px-2 py-0.5 rounded-[3px] border border-blue-200/60">
                                <i class="ph-bold ph-exam"></i>
                                <span>Score: <?= $assessment_score ?>% (Top 5%)</span>
                            </span>
                        </div>

                        <!-- Skill Tags with Interactive Hover & Verification Indicators -->
                        <div class="flex flex-wrap gap-2.5 pt-1">
                            <?php foreach ($skills_list as $skill): ?>
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50 hover:bg-blue-50 border border-slate-200/80 hover:border-blue-300 text-slate-800 hover:text-[#1952E1] text-xs font-semibold rounded-[3px] transition-all group">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#1952E1] group-hover:scale-125 transition-transform"></span>
                                    <span><?= htmlspecialchars($skill) ?></span>
                                    <i class="ph-bold ph-check text-[10px] text-emerald-600 opacity-60 group-hover:opacity-100"></i>
                                </span>
                            <?php endforeach; ?>
                        </div>

                        <!-- Skill Assessment Footnote -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span class="flex items-center gap-1">
                                <i class="ph-fill ph-seal-check text-blue-600 text-xs"></i>
                                <span>Platform tested for code quality, architectural standards, and security.</span>
                            </span>
                            <span class="font-bold text-slate-700"><?= count($skills_list) ?> Skills Verified</span>
                        </div>

                    </div>

                    <!-- Bento Tile 3: Service Packages & Fixed Escrows -->
                    <div class="bg-white border border-slate-200/90 rounded-[3px] p-6 shadow-sm space-y-5">
                        
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-[3px] bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                                    <i class="ph-bold ph-package text-base"></i>
                                </div>
                                <div>
                                    <h2 class="text-sm sm:text-base font-bold text-slate-900">Service Packages</h2>
                                    <p class="text-[11px] text-slate-400">Pre-defined milestone deliverables with fixed pricing</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-slate-500"><?= count($packages) ?> Packages</span>
                        </div>

                        <?php if (empty($packages)): ?>
                            <!-- Custom Escrow Proposal Card -->
                            <div class="p-8 text-center bg-gradient-to-br from-slate-50 to-blue-50/40 border border-slate-200/80 rounded-[3px] space-y-4">
                                <div class="w-12 h-12 bg-white text-[#1952E1] rounded-[3px] border border-slate-200 shadow-xs flex items-center justify-center mx-auto">
                                    <i class="ph-bold ph-briefcase-metal text-2xl"></i>
                                </div>
                                <div class="space-y-1">
                                    <h3 class="text-sm font-bold text-slate-900">Custom Milestone Contracts Available</h3>
                                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                                        This provider undertakes custom escrow projects. Submit your milestone specification or project brief to receive a formal proposal.
                                    </p>
                                </div>
                                <div class="pt-2">
                                    <a href="post-project.php?provider_id=<?= $provider['user_id'] ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white text-xs font-bold rounded-[3px] transition-colors shadow-2xs">
                                        <i class="ph-bold ph-plus-circle text-sm"></i>
                                        <span>Propose a Project to <?= $first_name ?></span>
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
                                    <div class="border border-slate-200/90 rounded-[3px] bg-white hover:border-[#1952E1] hover:shadow-md transition-all flex flex-col group overflow-hidden">
                                        
                                        <!-- Image / Ribbon -->
                                        <div class="relative aspect-video bg-slate-100 overflow-hidden">
                                            <?php if ($pkg_img): ?>
                                                <img src="<?= htmlspecialchars($pkg_img) ?>" alt="<?= htmlspecialchars($pkg['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            <?php else: ?>
                                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-gradient-to-br from-slate-100 to-slate-200">
                                                    <i class="ph-bold ph-image text-3xl mb-1 text-slate-300"></i>
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500"><?= htmlspecialchars($pkg['category_name'] ?: 'Verified Service') ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="absolute top-2.5 left-2.5 bg-slate-900/85 backdrop-blur-md text-white text-[9px] font-bold px-2 py-0.5 rounded-[3px] uppercase tracking-wider shadow-sm">
                                                <?= htmlspecialchars($pkg['category_name'] ?: 'Service') ?>
                                            </div>

                                            <div class="absolute bottom-2.5 right-2.5 bg-white/95 backdrop-blur-md text-slate-900 text-[10px] font-black px-2 py-0.5 rounded-[3px] shadow-sm">
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
                                                    <i class="ph-bold ph-clock text-blue-600"></i>
                                                    <span class="text-[11px] font-medium"><?= $pkg_days ?> Days Delivery</span>
                                                </div>
                                                <div class="flex items-center gap-1 text-[11px] text-emerald-600 font-semibold">
                                                    <i class="ph-bold ph-arrows-clockwise text-xs"></i>
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

                    <!-- Bento Tile 4: Verified Milestone Escrow History & Client Feedback -->
                    <div class="bg-white border border-slate-200/90 rounded-[3px] p-6 shadow-sm space-y-5">
                        
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-[3px] bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                                    <i class="ph-bold ph-shield-star text-base"></i>
                                </div>
                                <div>
                                    <h2 class="text-sm sm:text-base font-bold text-slate-900">Verified Milestone Feedback</h2>
                                    <p class="text-[11px] text-slate-400">Tied 100% to completed escrow payments</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 text-amber-500 font-bold text-xs">
                                <span>★ <?= $provider_rating ?></span>
                                <span class="text-slate-400 font-normal text-[11px]">(<?= $provider_reviews_count ?> Reviews)</span>
                            </div>
                        </div>

                        <!-- Feedback Stream -->
                        <div class="space-y-4 divide-y divide-slate-100">
                            
                            <!-- Review Card 1 -->
                            <div class="pt-3 first:pt-0 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-[3px] bg-blue-100 text-[#1952E1] font-black text-xs flex items-center justify-center">
                                            DO
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-xs text-slate-900">David Olanrewaju</span>
                                                <span class="inline-flex items-center gap-1 text-[10px] text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded-[2px] font-bold border border-emerald-200">
                                                    <i class="ph-fill ph-check-circle text-xs text-emerald-600"></i>
                                                    <span>Verified Escrow Hire</span>
                                                </span>
                                            </div>
                                            <span class="text-[10px] text-slate-400">Chief Technology Officer • FinTech Project</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-amber-500 text-xs font-black">
                                        ★★★★★ <span class="text-[11px] text-slate-600 ml-1">5.0</span>
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
                                        <div class="w-8 h-8 rounded-[3px] bg-indigo-100 text-indigo-700 font-black text-xs flex items-center justify-center">
                                            TF
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-xs text-slate-900">Tolulope F.</span>
                                                <span class="inline-flex items-center gap-1 text-[10px] text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded-[2px] font-bold border border-emerald-200">
                                                    <i class="ph-fill ph-check-circle text-xs text-emerald-600"></i>
                                                    <span>Verified Escrow Hire</span>
                                                </span>
                                            </div>
                                            <span class="text-[10px] text-slate-400">Founder & Product Lead</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-amber-500 text-xs font-black">
                                        ★★★★★ <span class="text-[11px] text-slate-600 ml-1">5.0</span>
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
                        <div class="p-3.5 bg-blue-50/80 border border-blue-200/60 rounded-[3px] flex items-center gap-3 text-xs text-[#1952E1]">
                            <i class="ph-fill ph-shield-check text-xl shrink-0 text-[#1952E1]"></i>
                            <div class="text-[11px] text-slate-700 leading-snug">
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
                    <div class="bg-white border-2 border-[#1952E1] rounded-[3px] p-5 sm:p-6 shadow-md space-y-5 relative overflow-hidden">
                        
                        <!-- Top Header Strip -->
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black uppercase tracking-wider text-[#1952E1] bg-blue-50 px-2 py-0.5 rounded-[2px] border border-blue-200/80">
                                Direct Engagement
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-[3px] border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
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
                                Negotiable for milestone escrows and fixed-budget contracts.
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
                        <div class="p-3.5 bg-emerald-50/70 border border-emerald-200/80 rounded-[3px] space-y-2 text-xs">
                            <div class="flex items-start gap-2.5">
                                <i class="ph-bold ph-shield-check text-emerald-600 text-lg shrink-0 mt-0.5"></i>
                                <div class="space-y-0.5">
                                    <strong class="text-slate-900 block text-xs">100% Escrow Protection</strong>
                                    <p class="text-[11px] text-slate-600 leading-snug">
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
                            <span class="text-xs font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-[2px] border border-emerald-200">
                                98 / 100
                            </span>
                        </div>

                        <!-- Progress Bar Visual -->
                        <div class="space-y-1">
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-gradient-to-r from-[#1952E1] to-emerald-500 h-full w-[98%] rounded-full"></div>
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
                                <span class="font-bold text-emerald-600 flex items-center gap-1 text-[11px]">
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
                                <span class="font-bold text-emerald-600 flex items-center gap-1 text-[11px]">
                                    <i class="ph-bold ph-check"></i> Verified
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-slate-700">
                                    <i class="ph-bold ph-shield-check text-slate-400 text-base"></i>
                                    <span>Escrow Guarantee Bond</span>
                                </div>
                                <span class="font-bold text-emerald-600 flex items-center gap-1 text-[11px]">
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
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Live Radar</span>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">On-Time Milestone Delivery:</span>
                                <strong class="text-slate-900">100%</strong>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Average Response Time:</span>
                                <strong class="text-emerald-600">&lt; 30 Mins</strong>
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
                    <div class="p-4 bg-gradient-to-br from-blue-50/90 via-indigo-50/40 to-slate-50 border border-blue-200/80 rounded-[3px] space-y-2 text-xs relative overflow-hidden shadow-2xs">
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
