<?php
$page_title = 'Skill Assessment';
$active_tab = 'assessment';
require_once __DIR__ . '/components/head.php';

// Fetch all subtopics from database dynamically
try {
    $subtopics_stmt = $db->query("
        SELECT s.slug as subtopic_id, s.name, s.description, c.name as category_name, c.slug as category_slug 
        FROM quiz_subtopics s 
        JOIN service_categories c ON s.category_id = c.id
        ORDER BY s.id ASC
    ");
    $subtopics_list = $subtopics_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $subtopics_list = [];
}

// Group by category name for selection modal mapping
$dynamicSubtopicsConfig = [];
foreach ($subtopics_list as $row) {
    $cat_name = $row['category_name'];
    if (!isset($dynamicSubtopicsConfig[$cat_name])) {
        $dynamicSubtopicsConfig[$cat_name] = [
            'slug' => $row['category_slug'],
            'topics' => []
        ];
    }
    $dynamicSubtopicsConfig[$cat_name]['topics'][] = [
        'id' => $row['subtopic_id'],
        'name' => $row['name'],
        'description' => $row['description']
    ];
}

// Derived state
$attempts_used     = ($assessment_status !== 'not_started') ? 1 : 0;
$score_pct         = (int)($assessment_score ?? 0);
$score_remaining   = 100 - $score_pct;
$circumference     = 2 * M_PI * 38; // r=38
$stroke_offset     = $circumference - ($score_pct / 100) * $circumference;
?>

<!-- Full-Height Left Vertical Navigation Dock -->
<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Assessment Hub Workspace -->
<main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-hidden relative bg-[#EFF2F7]">
    <?php include __DIR__ . '/components/header.php'; ?>

    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 pb-24 md:pb-6 lg:pb-12 scroll-smooth">

        <!-- =====================================================================
             TWO-COLUMN LAYOUT
             Col 1 (Left  – 60%): Banner + Quiz Selection Cards
             Col 2 (Right – 40%): Stats, Progress Ring, Credential Pipeline
             ===================================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 w-full gap-4 sm:gap-6 lg:gap-8 items-start">

            <!-- ================================================================
                 COLUMN 1 – LEFT: Banner + Quiz Cards
                 ================================================================ -->
            <div class="lg:col-span-8 space-y-6 min-w-0">

                <!-- Premium Assessment Banner -->
                <div class="relative rounded-[3px] overflow-hidden min-h-[260px] flex items-end shadow-xl border border-slate-700/30">

                    <!-- Background Photo Layer -->
                    <div class="absolute inset-0">
                        <img
                            src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=1200&auto=format&fit=crop&q=80"
                            alt="Assessment workspace"
                            class="w-full h-full object-cover object-center"
                        >
                        <!-- Dark gradient overlay for text legibility -->
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0A2342] via-[#0A2342]/80 to-slate-900/30"></div>
                        <!-- Blue accent overlay -->
                        <div class="absolute inset-0 bg-[#1952E1]/20 mix-blend-multiply"></div>
                    </div>

                    <!-- Floating Badge (top-right) -->
                    <div class="absolute top-5 right-5 z-10">
                        <?php if ($is_verified_pro): ?>
                            <span class="inline-flex items-center gap-1.5 bg-emerald-500 text-white text-[10px] font-black px-3 py-1.5 rounded-[3px] uppercase tracking-widest shadow-lg">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                Verified Pro
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 bg-amber-400 text-slate-900 text-[10px] font-black px-3 py-1.5 rounded-[3px] uppercase tracking-widest shadow-lg">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                Action Required
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Banner Text Content -->
                    <div class="relative z-10 p-6 sm:p-8 w-full">
                        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-amber-400 mb-2.5">Skill Verification</p>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-white leading-tight tracking-tight mb-3">
                            <?php echo $is_verified_pro
                                ? 'Credentials Active'
                                : 'Unlock Bidding Credentials'; ?>
                        </h1>
                        <p class="text-xs text-slate-300 leading-relaxed max-w-lg font-medium">
                            <?php if ($is_verified_pro): ?>
                                Your profile is verified. Bidding and escrow are fully active.
                            <?php else: ?>
                                Score 80%+ on a 20-minute domain quiz to unlock client bidding and milestone escrow.
                            <?php endif; ?>
                        </p>

                        <!-- Progress Breadcrumb Steps -->
                        <div class="grid grid-cols-4 gap-1 sm:flex sm:items-center sm:gap-3 mt-8 w-full">
                            <?php
                            $steps = [
                                ['label' => 'Onboarding',   'done' => true],
                                ['label' => 'Skill Quiz',   'done' => in_array($assessment_status, ['passed'])],
                                ['label' => 'KYC / NIN',    'done' => $verification_status === 'approved'],
                                ['label' => 'Verified Pro', 'done' => $is_verified_pro],
                            ];
                            foreach ($steps as $i => $step):
                            ?>
                                <div class="flex flex-col sm:flex-row items-center gap-1 sm:gap-2 text-center sm:text-left min-w-0">
                                    <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0
                                        <?= $step['done'] ? 'bg-emerald-500' : 'bg-white/20 border border-white/30' ?>">
                                        <?php if ($step['done']): ?>
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        <?php else: ?>
                                            <span class="text-[8px] font-black text-white/60"><?= $i+1 ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-[9px] sm:text-[10px] font-bold <?= $step['done'] ? 'text-emerald-400' : 'text-slate-400' ?> block sm:inline-block leading-tight"><?= $step['label'] ?></span>
                                    <?php if ($i < count($steps)-1): ?>
                                        <div class="w-4 h-px bg-white/20 hidden sm:block shrink-0 ml-1"></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Quiz Categories Heading -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-0">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Available Assessment Quizzes</h2>
                        <p class="text-[11px] text-slate-400 font-medium mt-0.5">Select a domain to begin your timed competency test</p>
                    </div>
                    <span class="self-start sm:self-auto flex items-center gap-1.5 text-[10px] font-black text-slate-500 uppercase tracking-wider bg-slate-100 px-3 py-1.5 rounded-[3px]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pass Mark: 80%
                    </span>
                </div>

                <!-- Quiz Domain Cards (Redesigned to Spacious 2-Column Grid) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <!-- Card: UI/UX & Product Design -->
                    <div class="bg-white rounded-[6px] border border-slate-200/80 shadow-sm hover:shadow-md hover:border-[#1952E1] hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group overflow-hidden">
                        <div class="p-6 space-y-4 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="w-10 h-10 rounded-[6px] bg-blue-50 text-[#1952E1] flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                                </div>
                                <span class="px-2.5 py-1 bg-slate-50 text-slate-500 rounded-[3px] border border-slate-100 text-[10px] font-extrabold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    20m &bull; 15 Qs
                                </span>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 group-hover:text-[#1952E1] transition-colors">UI/UX & Product Design</h3>
                                <p class="text-[11px] text-slate-500 mt-1.5 leading-relaxed font-medium">Figma workflows, design systems, usability heuristics, wireframing, and interactive prototyping.</p>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                            <button type="button" class="btn-start-quiz w-full sm:w-auto flex items-center justify-center gap-1.5 px-4 py-2 bg-[#1952E1] hover:bg-blue-700 text-white text-[11px] font-black rounded-[3px] shadow-sm transition-colors" data-category="UI/UX & Product Design">
                                <span><?= $is_verified_pro ? 'Retake Quiz' : 'Start Assessment' ?></span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Card: Web Development -->
                    <div class="bg-white rounded-[6px] border border-slate-200/80 shadow-sm hover:shadow-md hover:border-emerald-500 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group overflow-hidden">
                        <div class="p-6 space-y-4 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="w-10 h-10 rounded-[6px] bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                </div>
                                <span class="px-2.5 py-1 bg-slate-50 text-slate-500 rounded-[3px] border border-slate-100 text-[10px] font-extrabold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    20m &bull; 15 Qs
                                </span>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 group-hover:text-emerald-600 transition-colors">Web Development</h3>
                                <p class="text-[11px] text-slate-500 mt-1.5 leading-relaxed font-medium">PHP, MySQL, REST APIs, frontend components, modern architecture, and security practices.</p>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                            <button type="button" class="btn-start-quiz w-full sm:w-auto flex items-center justify-center gap-1.5 px-4 py-2 bg-[#1952E1] hover:bg-blue-700 text-white text-[11px] font-black rounded-[3px] shadow-sm transition-colors" data-category="Web Development">
                                <span><?= $is_verified_pro ? 'Retake Quiz' : 'Start Assessment' ?></span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Card: Mobile App Development -->
                    <div class="bg-white rounded-[6px] border border-slate-200/80 shadow-sm hover:shadow-md hover:border-purple-500 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group overflow-hidden">
                        <div class="p-6 space-y-4 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="w-10 h-10 rounded-[6px] bg-purple-50 text-purple-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="px-2.5 py-1 bg-slate-50 text-slate-500 rounded-[3px] border border-slate-100 text-[10px] font-extrabold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    20m &bull; 15 Qs
                                </span>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 group-hover:text-purple-600 transition-colors">Mobile App Development</h3>
                                <p class="text-[11px] text-slate-500 mt-1.5 leading-relaxed font-medium">Flutter, React Native, iOS Swift, Android Kotlin, and cloud state synchronization.</p>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                            <button type="button" class="btn-start-quiz w-full sm:w-auto flex items-center justify-center gap-1.5 px-4 py-2 bg-[#1952E1] hover:bg-blue-700 text-white text-[11px] font-black rounded-[3px] shadow-sm transition-colors" data-category="Mobile App Development">
                                <span><?= $is_verified_pro ? 'Retake Quiz' : 'Start Assessment' ?></span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Card: Data Science & AI -->
                    <div class="bg-white rounded-[6px] border border-slate-200/80 shadow-sm hover:shadow-md hover:border-orange-500 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group overflow-hidden">
                        <div class="p-6 space-y-4 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="w-10 h-10 rounded-[6px] bg-orange-50 text-orange-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                </div>
                                <span class="px-2.5 py-1 bg-slate-50 text-slate-500 rounded-[3px] border border-slate-100 text-[10px] font-extrabold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    20m &bull; 15 Qs
                                </span>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 group-hover:text-orange-600 transition-colors">Data Science & AI</h3>
                                <p class="text-[11px] text-slate-500 mt-1.5 leading-relaxed font-medium">Python, ML pipelines, data wrangling, model evaluation, and AI integration workflows.</p>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                            <button type="button" class="btn-start-quiz w-full sm:w-auto flex items-center justify-center gap-1.5 px-4 py-2 bg-[#1952E1] hover:bg-blue-700 text-white text-[11px] font-black rounded-[3px] shadow-sm transition-colors" data-category="Data Science & AI">
                                <span><?= $is_verified_pro ? 'Retake Quiz' : 'Start Assessment' ?></span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Card: DevOps & Cloud Infrastructure -->
                    <div class="bg-white rounded-[6px] border border-slate-200/80 shadow-sm hover:shadow-md hover:border-teal-500 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group overflow-hidden">
                        <div class="p-6 space-y-4 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="w-10 h-10 rounded-[6px] bg-teal-50 text-teal-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                                </div>
                                <span class="px-2.5 py-1 bg-slate-50 text-slate-500 rounded-[3px] border border-slate-100 text-[10px] font-extrabold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    20m &bull; 15 Qs
                                </span>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 group-hover:text-teal-600 transition-colors">DevOps & Cloud Infrastructure</h3>
                                <p class="text-[11px] text-slate-500 mt-1.5 leading-relaxed font-medium">Docker, CI/CD pipelines, AWS/GCP, Kubernetes orchestration, and site reliability.</p>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                            <button type="button" class="btn-start-quiz w-full sm:w-auto flex items-center justify-center gap-1.5 px-4 py-2 bg-[#1952E1] hover:bg-blue-700 text-white text-[11px] font-black rounded-[3px] shadow-sm transition-colors" data-category="DevOps & Cloud Infrastructure">
                                <span><?= $is_verified_pro ? 'Retake Quiz' : 'Start Assessment' ?></span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Card: Cybersecurity & Compliance -->
                    <div class="bg-white rounded-[6px] border border-slate-200/80 shadow-sm hover:shadow-md hover:border-red-500 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group overflow-hidden">
                        <div class="p-6 space-y-4 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="w-10 h-10 rounded-[6px] bg-red-50 text-red-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <span class="px-2.5 py-1 bg-slate-50 text-slate-500 rounded-[3px] border border-slate-100 text-[10px] font-extrabold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    20m &bull; 15 Qs
                                </span>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 group-hover:text-red-600 transition-colors">Cybersecurity & Compliance</h3>
                                <p class="text-[11px] text-slate-500 mt-1.5 leading-relaxed font-medium">Penetration testing, OWASP, network security, GDPR compliance, and incident response.</p>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                            <button type="button" class="btn-start-quiz w-full sm:w-auto flex items-center justify-center gap-1.5 px-4 py-2 bg-[#1952E1] hover:bg-blue-700 text-white text-[11px] font-black rounded-[3px] shadow-sm transition-colors" data-category="Cybersecurity & Compliance">
                                <span><?= $is_verified_pro ? 'Retake Quiz' : 'Start Assessment' ?></span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Card: Content Writing -->
                    <div class="bg-white rounded-[6px] border border-slate-200/80 shadow-sm hover:shadow-md hover:border-amber-500 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group overflow-hidden">
                        <div class="p-6 space-y-4 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="w-10 h-10 rounded-[6px] bg-amber-50 text-amber-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </div>
                                <span class="px-2.5 py-1 bg-slate-50 text-slate-500 rounded-[3px] border border-slate-100 text-[10px] font-extrabold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    20m &bull; 15 Qs
                                </span>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 group-hover:text-amber-600 transition-colors">Content Writing</h3>
                                <p class="text-[11px] text-slate-500 mt-1.5 leading-relaxed font-medium">Technical documentation, SEO copywriting, content strategy, and articles.</p>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                            <button type="button" class="btn-start-quiz w-full sm:w-auto flex items-center justify-center gap-1.5 px-4 py-2 bg-[#1952E1] hover:bg-blue-700 text-white text-[11px] font-black rounded-[3px] shadow-sm transition-colors" data-category="Content Writing">
                                <span><?= $is_verified_pro ? 'Retake Quiz' : 'Start Assessment' ?></span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Card: WordPress Development -->
                    <div class="bg-white rounded-[6px] border border-slate-200/80 shadow-sm hover:shadow-md hover:border-sky-500 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group overflow-hidden">
                        <div class="p-6 space-y-4 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="w-10 h-10 rounded-[6px] bg-sky-50 text-sky-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <span class="px-2.5 py-1 bg-slate-50 text-slate-500 rounded-[3px] border border-slate-100 text-[10px] font-extrabold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    20m &bull; 15 Qs
                                </span>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 group-hover:text-sky-600 transition-colors">WordPress Development</h3>
                                <p class="text-[11px] text-slate-500 mt-1.5 leading-relaxed font-medium">Theme creation, plugin customization, WP loops, widgets, and Gutenberg blocks.</p>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                            <button type="button" class="btn-start-quiz w-full sm:w-auto flex items-center justify-center gap-1.5 px-4 py-2 bg-[#1952E1] hover:bg-blue-700 text-white text-[11px] font-black rounded-[3px] shadow-sm transition-colors" data-category="WordPress">
                                <span><?= $is_verified_pro ? 'Retake Quiz' : 'Start Assessment' ?></span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                </div><!-- /grid quiz cards -->

            </div><!-- /col-1 -->

            <!-- ================================================================
                 COLUMN 2 – RIGHT: Stats, Score Chart, Credential Pipeline
                 ================================================================ -->
            <div class="lg:col-span-4 lg:sticky lg:top-0 space-y-6 max-h-none lg:max-h-[calc(100vh-6rem)] lg:overflow-y-auto no-scrollbar">
                <div class="grid grid-cols-2 gap-3">

                    <!-- Quiz Status -->
                    <div class="bg-white border border-slate-200/80 rounded-[3px] shadow-sm p-4 space-y-2">
                        <div class="w-8 h-8 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Quiz Status</p>
                            <h4 class="text-xs font-extrabold text-slate-900 leading-tight mt-0.5">
                                <?php
                                    if ($assessment_status === 'passed') echo 'Passed';
                                    elseif ($assessment_status === 'failed') echo 'Failed';
                                    else echo 'Not Taken';
                                ?>
                            </h4>
                            <p class="text-[9px] text-slate-400 font-medium mt-0.5">80% pass mark</p>
                        </div>
                    </div>

                    <!-- KYC Verification -->
                    <div class="bg-white border border-slate-200/80 rounded-[3px] shadow-sm p-4 space-y-2">
                        <div class="w-8 h-8 rounded-[3px] bg-purple-50 text-purple-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase text-slate-400 tracking-wider">KYC / NIN</p>
                            <h4 class="text-xs font-extrabold text-slate-900 leading-tight mt-0.5">
                                <?php
                                    if ($verification_status === 'approved') echo 'Verified';
                                    elseif ($verification_status === 'pending') echo 'In Review';
                                    elseif ($verification_status === 'rejected') echo 'Rejected';
                                    else echo 'Pending';
                                ?>
                            </h4>
                            <p class="text-[9px] text-slate-400 font-medium mt-0.5">NIN + liveness</p>
                        </div>
                    </div>

                    <!-- Marketplace Badge -->
                    <div class="bg-white border border-slate-200/80 rounded-[3px] shadow-sm p-4 space-y-2">
                        <div class="w-8 h-8 rounded-[3px] bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Badge</p>
                            <h4 class="text-xs font-extrabold text-slate-900 leading-tight mt-0.5">
                                <?php
                                    if ($is_verified_pro) echo 'Verified Pro';
                                    elseif ($assessment_status === 'passed') echo 'Awaiting KYC';
                                    else echo 'Not Earned';
                                ?>
                            </h4>
                            <p class="text-[9px] text-slate-400 font-medium mt-0.5">Qualification status</p>
                        </div>
                    </div>

                    <!-- Quiz Time Limit -->
                    <div class="bg-white border border-slate-200/80 rounded-[3px] shadow-sm p-4 space-y-2">
                        <div class="w-8 h-8 rounded-[3px] bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Time Limit</p>
                            <h4 class="text-xs font-extrabold text-slate-900 leading-tight mt-0.5">20 Minutes</h4>
                            <p class="text-[9px] text-slate-400 font-medium mt-0.5">15 questions per quiz</p>
                        </div>
                    </div>

                </div><!-- /stat 2x2 grid -->

                <!-- Score Analysis Speedometer/Gauge Chart Card -->
                <div class="bg-white border border-slate-200/80 rounded-[3px] shadow-sm p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Analysis & Performance</h3>
                            <p class="text-[10px] text-slate-400 font-medium mt-0.5">Top score vs target metrics</p>
                        </div>
                        <?php if ($assessment_status === 'passed'): ?>
                            <span class="flex items-center gap-1 text-[10px] font-extrabold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded-[3px]">
                                Passed
                            </span>
                        <?php elseif ($assessment_status === 'failed'): ?>
                            <span class="flex items-center gap-1 text-[10px] font-extrabold text-red-600 bg-red-50 border border-red-100 px-2.5 py-1 rounded-[3px]">
                                Failed
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Clean Speedometer Gauge SVG -->
                    <div class="flex flex-col items-center justify-center pt-2">
                        <div class="relative w-48 h-28 flex items-center justify-center overflow-hidden">
                            <svg class="w-full h-full" viewBox="0 0 100 50">
                                <!-- Track Arc -->
                                <path d="M 10 46 A 36 36 0 0 1 90 46" fill="none" stroke="#E2E8F0" stroke-width="8" stroke-linecap="round"/>
                                
                                <!-- Active Fill Arc -->
                                <?php 
                                    // Semicircle arc length for radius 36 = M_PI * 36 ≈ 113.1
                                    $semi_len = M_PI * 36;
                                    $semi_offset = $semi_len - ($score_pct / 100) * $semi_len;
                                    $stroke_color = ($assessment_status === 'passed') ? '#10b981' : (($assessment_status === 'failed' ? '#ef4444' : '#cbd5e1'));
                                ?>
                                <path d="M 10 46 A 36 36 0 0 1 90 46" fill="none" stroke="<?= $stroke_color ?>" stroke-width="8" stroke-linecap="round"
                                      stroke-dasharray="<?= $semi_len ?>" stroke-dashoffset="<?= $semi_offset ?>"
                                      style="transition: stroke-dashoffset 1s ease-in-out;"/>
                            </svg>
                            
                            <!-- Inner Content Label -->
                            <div class="absolute bottom-1 flex flex-col items-center">
                                <span class="text-3xl font-black text-slate-900 leading-none"><?= $score_pct > 0 ? $score_pct.'%' : '—' ?></span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-1">BEST SCORE</span>
                            </div>
                        </div>

                        <!-- Target Markers Legend -->
                        <div class="w-full flex justify-between px-3 text-[10px] text-slate-400 font-bold border-b border-slate-100 pb-3 mt-2">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                <span>Start (0%)</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-[#1952E1]"></span>
                                <span>Target (80%+)</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Max (100%)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pass threshold note -->
                    <div class="flex items-center justify-between text-[10px] text-slate-400 font-medium pt-1">
                        <span>Pass Threshold: <strong class="text-slate-700">80%</strong></span>
                        <span>Attempts: <strong class="text-slate-700"><?= $attempts_used ?> / 3</strong></span>
                    </div>

                    <!-- Attempt Bar -->
                    <div class="space-y-1.5">
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Trial Usage</div>
                        <div class="flex gap-1.5">
                            <?php for ($a = 1; $a <= 3; $a++): ?>
                                <div class="flex-1 h-1.5 rounded-full <?= $a <= $attempts_used ? 'bg-[#1952E1]' : 'bg-slate-100' ?>"></div>
                            <?php endfor; ?>
                        </div>
                        <p class="text-[10px] text-slate-400 font-medium"><?= $attempts_used ?> of 3 attempts used</p>
                    </div>
                </div>

                <!-- Credential Pathway Tracker -->
                <div class="bg-white border border-slate-200/80 rounded-[3px] shadow-sm p-5">
                    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-4">Credential Pathway</h3>
                    <div class="space-y-0">

                        <?php
                        $pipeline = [
                            [
                                'label'    => 'Account Onboarding',
                                'sub'      => 'Basic profile & entity info',
                                'done'     => true,
                                'icon_path'=> 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                                'color'    => 'emerald',
                            ],
                            [
                                'label'    => 'Skill Assessment Quiz',
                                'sub'      => 'Domain competency test (80%+)',
                                'done'     => in_array($assessment_status, ['passed']),
                                'icon_path'=> 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                                'color'    => 'blue',
                            ],
                            [
                                'label'    => 'KYC & Identity Verification',
                                'sub'      => 'NIN + liveness video check',
                                'done'     => $verification_status === 'approved',
                                'icon_path'=> 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                                'color'    => 'purple',
                            ],
                            [
                                'label'    => 'Verified Pro Badge',
                                'sub'      => 'Full marketplace bidding access',
                                'done'     => $is_verified_pro,
                                'icon_path'=> 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
                                'color'    => 'amber',
                            ],
                        ];
                        foreach ($pipeline as $idx => $step):
                            $colors = [
                                'emerald' => ['dot_done' => 'bg-emerald-500', 'dot_pending' => 'bg-slate-200', 'icon_done' => 'text-emerald-600 bg-emerald-50', 'icon_pending' => 'text-slate-400 bg-slate-100'],
                                'blue'    => ['dot_done' => 'bg-[#1952E1]',   'dot_pending' => 'bg-slate-200', 'icon_done' => 'text-[#1952E1] bg-blue-50',    'icon_pending' => 'text-slate-400 bg-slate-100'],
                                'purple'  => ['dot_done' => 'bg-purple-500',  'dot_pending' => 'bg-slate-200', 'icon_done' => 'text-purple-600 bg-purple-50',  'icon_pending' => 'text-slate-400 bg-slate-100'],
                                'amber'   => ['dot_done' => 'bg-amber-500',   'dot_pending' => 'bg-slate-200', 'icon_done' => 'text-amber-600 bg-amber-50',    'icon_pending' => 'text-slate-400 bg-slate-100'],
                            ][$step['color']];
                            $is_last = $idx === count($pipeline) - 1;
                        ?>
                        <div class="flex gap-3 <?= !$is_last ? 'pb-4' : '' ?>">
                            <!-- Timeline Column -->
                            <div class="flex flex-col items-center">
                                <div class="w-7 h-7 rounded-full <?= $step['done'] ? $colors['icon_done'] : $colors['icon_pending'] ?> flex items-center justify-center shrink-0 border <?= $step['done'] ? 'border-current/20' : 'border-slate-200' ?>">
                                    <?php if ($step['done']): ?>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    <?php else: ?>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $step['icon_path'] ?>"/></svg>
                                    <?php endif; ?>
                                </div>
                                <?php if (!$is_last): ?>
                                    <div class="w-px flex-1 mt-1 <?= $step['done'] ? $colors['dot_done'].' opacity-30' : 'bg-slate-200' ?>"></div>
                                <?php endif; ?>
                            </div>
                            <!-- Text Column -->
                            <div class="pb-1 min-w-0">
                                <p class="text-xs font-bold <?= $step['done'] ? 'text-slate-900' : 'text-slate-400' ?> leading-tight"><?= $step['label'] ?></p>
                                <p class="text-[10px] text-slate-400 font-medium mt-0.5"><?= $step['sub'] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>

                    </div>
                </div>

            </div><!-- /col-2 -->

        </div><!-- /2-col grid -->
    </div><!-- /scrollable -->
</main>

<!-- Mobile Bottom Navigation Bar -->
<?php include __DIR__ . '/components/bottom-nav.php'; ?>

<!-- Component Footer & Scripts -->
<?php include __DIR__ . '/components/footer.php'; ?>

<!-- Selection Gateway Modal -->
<div id="quiz-modal" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-[6px] max-w-md w-full shadow-2xl overflow-hidden">

        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50">
            <div>
                <span class="text-[10px] font-extrabold uppercase text-[#1952E1] tracking-widest">Assessment Gateway</span>
                <h3 class="text-sm font-extrabold text-slate-900 mt-0.5" id="quiz-title">Select Topic</h3>
            </div>
            <button type="button" id="btn-close-quiz-x" class="text-slate-400 hover:text-slate-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Topic Selection Area -->
        <div class="p-6 space-y-4">
            <p class="text-xs text-slate-500 font-medium leading-relaxed">
                Please select the specific sub-topic or programming language you want to be assessed on. Passing this assessment unlocks full bidding and service listing access for this domain.
            </p>

            <div class="space-y-3" id="subtopic-container">
                <!-- Dynamically populated options -->
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
            <button type="button" id="btn-cancel-quiz" class="px-5 py-2 text-slate-700 font-bold text-xs rounded-[3px] bg-slate-100 hover:bg-slate-200 transition-colors">Cancel</button>
            <button type="button" id="btn-proceed-quiz" class="flex items-center gap-1.5 px-6 py-2 bg-[#1952E1] hover:bg-blue-700 text-white font-extrabold text-xs rounded-[3px] shadow-sm transition-colors">
                <span>Start Assessment</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const quizModal        = document.getElementById('quiz-modal');
    const quizTitle        = document.getElementById('quiz-title');
    const btnCancel        = document.getElementById('btn-cancel-quiz');
    const btnCloseX        = document.getElementById('btn-close-quiz-x');
    const btnProceed       = document.getElementById('btn-proceed-quiz');
    const subtopicContainer = document.getElementById('subtopic-container');

    // Mapping display categories to DB slug + available subtopics loaded dynamically from database
    const subtopicsConfig = <?= json_encode($dynamicSubtopicsConfig) ?>;

    let selectedCategorySlug = "";

    document.querySelectorAll('.btn-start-quiz').forEach(btn => {
        btn.addEventListener('click', () => {
            const category = btn.dataset.category || '';
            const config = subtopicsConfig[category];

            if (!config) {
                ScriptlyToast.error("This category assessment is not configured yet.");
                return;
            }

            quizTitle.textContent = category;
            selectedCategorySlug = config.slug;

            // Populate options
            subtopicContainer.innerHTML = "";
            config.topics.forEach((t, index) => {
                const label = document.createElement('label');
                label.className = "flex items-start gap-3 p-3.5 rounded-[4px] border border-slate-200 hover:border-[#1952E1] hover:bg-blue-50/20 cursor-pointer transition-all flex items-center";
                label.innerHTML = `
                    <input type="radio" name="subtopic" value="${t.id}" ${index === 0 ? 'checked' : ''} class="mt-1 accent-[#1952E1] shrink-0">
                    <div>
                        <span class="block text-xs font-extrabold text-slate-800">${t.name}</span>
                        <span class="block text-[10px] text-slate-400 font-medium mt-0.5">${t.description}</span>
                    </div>
                `;
                subtopicContainer.appendChild(label);
            });

            quizModal.classList.remove('hidden');
        });
    });

    const closeModal = () => quizModal.classList.add('hidden');
    if (btnCancel) btnCancel.addEventListener('click', closeModal);
    if (btnCloseX) btnCloseX.addEventListener('click', closeModal);

    if (btnProceed) {
        btnProceed.addEventListener('click', () => {
            const checkedRadio = document.querySelector('input[name="subtopic"]:checked');
            if (!checkedRadio) {
                ScriptlyToast.error("Please select a topic first.");
                return;
            }
            const subtopic = checkedRadio.value;
            window.location.href = `assessment-quiz.php?category=${selectedCategorySlug}&subtopic=${subtopic}`;
        });
    }
});
</script>
