<?php 
$page_title = 'Workspace';
$active_tab = 'dashboard';
require_once __DIR__ . '/components/head.php';
require_once __DIR__ . '/controllers/DashboardController.php'; 
?>

<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Layout Area (Takes full remaining width and height) -->
<main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-hidden relative bg-[#EFF2F7]">
    
    <?php include __DIR__ . '/components/header.php'; ?>

    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 mobile-bottom-space md:pb-6 lg:pb-12 scroll-smooth">
        
        <!-- 2-COLUMN SPLIT DASHBOARD LAYOUT (Column 1 is Wider than Column 2) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 w-full gap-4 sm:gap-6 lg:gap-8 items-start">
            
            <!-- =================================================================
                 COLUMN 1: WIDER SCROLLABLE COLUMN (8 Cols on Desktop)
                 Banner + Active Jobs + Find Professionals + Open Work Cards
                 ================================================================= -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- CLEAR IMAGE BACKGROUND BANNER WITH INTEGRATED SEARCH (No Gradients, Strict 3px Radius) -->
                <?php $firstName = explode(' ', $user_name ?? 'User')[0]; ?>
                <div class="relative rounded-[3px] p-6 sm:p-7 md:p-8 overflow-hidden border border-slate-300/80 shadow-sm min-h-[220px] flex flex-col justify-between">
                    <!-- Clear High-Quality Image Background -->
                    <img src="../assets/hero_bg.jpg" alt="Workspace Background" class="absolute inset-0 w-full h-full object-cover">
                    <!-- Solid High-Contrast Dark Overlay -->
                    <div class="absolute inset-0 bg-[#0A2342]/90"></div>

                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full mb-5">
                        <div class="max-w-xl text-white">
                            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white leading-tight">
                                Welcome back, <?= htmlspecialchars($firstName) ?>
                            </h1>
                            <p class="text-xs text-slate-200 font-normal mt-1 leading-relaxed">
                                Manage active contracts, inspect submitted deliverables, and hire pre-assessed talent.
                            </p>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <a href="post-project.php" class="inline-flex items-center justify-center gap-1.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-[3px] transition-colors shadow-sm">
                                <i class="ph-bold ph-plus-circle text-sm"></i>
                                <span>Post a Project</span>
                            </a>
                        </div>
                    </div>

                    <!-- Integrated Banner Search Bar & Quick Tags -->
                    <div class="relative z-10 w-full">
                        <form action="talent.php" method="GET" class="relative flex items-center w-full">
                            <i class="ph-bold ph-magnifying-glass absolute left-3.5 text-slate-400 text-base"></i>
                            <input type="text" name="q" placeholder="Search for services or verified professionals (e.g. Full-Stack Developer, UI/UX Designer)..." class="w-full bg-white border border-slate-200 rounded-[3px] pl-10 pr-24 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#1952E1] focus:ring-1 focus:ring-[#1952E1] shadow-lg transition-all">
                            <button type="submit" class="absolute right-1.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs px-3.5 py-1.5 rounded-[3px] transition-colors">
                                Search
                            </button>
                        </form>
                        <!-- Quick Search Suggestions -->
                        <div class="flex items-center gap-2 mt-2 text-[11px] text-slate-300 overflow-x-auto">
                            <span class="text-slate-400 font-semibold shrink-0">Popular:</span>
                            <a href="talent.php?q=Web+Developer" class="hover:text-white hover:underline whitespace-nowrap">Web Developer</a>
                            <span class="text-slate-500">•</span>
                            <a href="talent.php?q=UI+UX+Designer" class="hover:text-white hover:underline whitespace-nowrap">UI/UX Designer</a>
                            <span class="text-slate-500">•</span>
                            <a href="talent.php?q=Data+Analyst" class="hover:text-white hover:underline whitespace-nowrap">Data Analyst</a>
                            <span class="text-slate-500">•</span>
                            <a href="talent.php?q=DevOps" class="hover:text-white hover:underline whitespace-nowrap">DevOps</a>
                        </div>
                    </div>
                </div>


                <!-- SECTION 1: ACTIVE JOBS / MILESTONE CONTRACTS -->
                <div class="bg-white border border-slate-200/90 rounded-[3px] p-5 sm:p-6 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between pb-4 mb-4 border-b border-slate-100 gap-3">
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-slate-900">Active Jobs & Deliverables</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Live contracts currently in progress</p>
                        </div>
                        <a href="my-projects.php" class="text-xs font-bold text-[#1952E1] hover:underline flex items-center gap-1">
                            <span>View All Projects</span>
                            <i class="ph-bold ph-arrow-right text-xs"></i>
                        </a>
                    </div>

                    <?php if(empty($active_jobs)): ?>
                        <div class="w-full flex flex-col items-center justify-center py-12 bg-slate-50/50 border border-dashed border-slate-300 rounded-[3px]">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm border border-slate-200">
                                <i class="ph-bold ph-briefcase text-2xl text-slate-400"></i>
                            </div>
                            <h3 class="font-bold text-sm text-slate-900 mb-1.5">No active jobs right now</h3>
                            <p class="text-[11px] text-slate-500 text-center max-w-[280px] mb-4">You don't have any ongoing contracts or active milestones. Post a project to hire verified talent.</p>
                            <a href="post-project.php" class="bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-[3px] transition-colors shadow-sm inline-flex items-center gap-1.5">
                                <i class="ph-bold ph-plus"></i> Post a Project
                            </a>
                        </div>
                    <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <?php foreach($active_jobs as $job): ?>
                            <div class="p-4 sm:p-5 rounded-[3px] bg-slate-50/70 border border-slate-200/90 hover:bg-white hover:border-[#1952E1] transition-all flex flex-col justify-between shadow-sm">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-2.5">
                                        <span class="text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-[#1952E1] border border-blue-200 px-2 py-0.5 rounded-[3px]">In Progress</span>
                                        <span class="text-[11px] font-medium text-slate-500">Updated <?= date('M j', strtotime($job['created_at'])) ?></span>
                                    </div>
                                    <h3 class="font-bold text-sm text-slate-900 leading-snug mb-1"><?= htmlspecialchars($job['title']) ?></h3>
                                    <p class="text-[11px] text-slate-500 mb-3">Client: <strong class="text-slate-700 font-semibold"><?= htmlspecialchars($job['client_name']) ?></strong></p>
                                    
                                    <!-- Milestone Deliverable Tracker -->
                                    <div class="space-y-1.5 mb-4">
                                        <div class="flex justify-between text-[11px] font-medium text-slate-600">
                                            <span class="truncate pr-2">Overall Progress</span>
                                            <span class="font-bold text-[#1952E1] shrink-0"><?= $job['progress_percentage'] ?>%</span>
                                        </div>
                                        <div class="w-full h-2 bg-slate-200 rounded-[3px] overflow-hidden">
                                            <div class="h-full bg-[#1952E1] rounded-[3px]" style="width: <?= $job['progress_percentage'] ?>%"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200/70">
                                    <a href="messages.php" class="text-[11px] font-bold text-slate-700 hover:text-slate-900 px-3 py-1.5 rounded-[3px] bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                                        Chat
                                    </a>
                                    <a href="<?= !empty($job['contract_id']) ? 'contract-details.php?id=' . $job['contract_id'] : 'project-details.php?slug=' . urlencode($job['slug']) ?>" class="text-[11px] font-bold bg-[#1952E1] text-white hover:bg-blue-700 px-3.5 py-1.5 rounded-[3px] transition-colors shadow-sm">
                                        View Contract
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- SECTION 2: FIND PRE-ASSESSED PROFESSIONALS (Bigger Detailed Cards) -->
                <div class="bg-white border border-slate-200/90 rounded-[3px] p-5 sm:p-6 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between pb-4 mb-4 border-b border-slate-100 gap-3">
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-slate-900">Find Pre-Assessed Professionals</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Top vetted talent audited for skills, code quality, and on-time delivery</p>
                        </div>
                        <a href="talent.php" class="text-xs font-bold text-[#1952E1] hover:underline flex items-center gap-1">
                            <span>Browse All Talent (<?= $total_talent_count ?>)</span>
                            <i class="ph-bold ph-arrow-right text-xs"></i>
                        </a>
                    </div>

                                        <?php if(empty($recommended_talent)): ?>
                        <div class="w-full flex flex-col items-center justify-center py-12 bg-slate-50/50 border border-dashed border-slate-300 rounded-[3px]">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm border border-slate-200">
                                <i class="ph-bold ph-users text-2xl text-slate-400"></i>
                            </div>
                            <h3 class="font-bold text-sm text-slate-900 mb-1.5">No professionals found</h3>
                            <p class="text-[11px] text-slate-500 text-center max-w-[280px] mb-4">No verified professionals are currently available. Please check back soon as talent joins.</p>
                            <a href="talent.php" class="bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-[3px] transition-colors shadow-sm inline-flex items-center gap-1.5">
                                <i class="ph-bold ph-magnifying-glass"></i> Browse Directory
                            </a>
                        </div>
                    <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <?php foreach($recommended_talent as $pro): ?>
                            <?php 
                                $avatar_url = !empty($pro['avatar_url']) ? $pro['avatar_url'] : "https://ui-avatars.com/api/?name=" . urlencode($pro['full_name']) . "&background=f1f5f9&color=0f172a&bold=true";
                                $rating = number_format((float)($pro['rating'] ?? 5.0), 1);
                                $success = (int)($pro['job_success_percentage'] ?? 100);
                                $title = htmlspecialchars($pro['title'] ?: 'Independent Professional');
                                $bio = htmlspecialchars($pro['bio'] ?: 'Verified professional on the Scriptly platform.');
                            ?>
                            <div class="p-5 rounded-[3px] border border-slate-200/90 bg-slate-50/60 hover:bg-white hover:border-[#1952E1] transition-all flex flex-col justify-between group shadow-sm">
                                <div>
                                    <!-- Header & Avatar -->
                                    <div class="flex items-start gap-3.5 mb-3.5">
                                        <div class="relative shrink-0">
                                            <img src="<?= $avatar_url ?>" alt="<?= htmlspecialchars($pro['full_name']) ?>" class="w-14 h-14 rounded-[3px] object-cover border border-slate-200">
                                            <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-500 rounded-full ring-2 ring-white" title="Available for hire"></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <h3 class="font-bold text-sm text-slate-900 group-hover:text-[#1952E1] transition-colors truncate"><?= htmlspecialchars($pro['full_name']) ?></h3>
                                                <i class="ph-fill ph-check-circle text-[#1952E1] text-sm shrink-0" title="Scriptly Verified"></i>
                                            </div>
                                            <p class="text-[11px] text-slate-600 font-semibold truncate"><?= $title ?></p>
                                            <div class="flex items-center gap-2 mt-1 text-[11px]">
                                                <span class="text-amber-500 font-bold flex items-center gap-0.5">★ <?= $rating ?></span>
                                                <span class="text-slate-300">•</span>
                                                <span class="text-emerald-700 font-semibold bg-emerald-50 px-1.5 py-0.5 rounded-[3px]"><?= $success ?>% Success</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bio / Tagline -->
                                    <p class="text-xs text-slate-600 leading-relaxed mb-3.5 line-clamp-2">
                                        <?= $bio ?>
                                    </p>

                                    <!-- Skill Badges -->
                                    <div class="flex flex-wrap gap-1.5 mb-4">
                                        <?php 
                                        $skills_list = array_slice(array_map('trim', explode(',', $pro['skills'] ?? '')), 0, 3);
                                        foreach ($skills_list as $skill) {
                                            if (!empty($skill)) {
                                                echo '<span class="text-[10px] font-semibold bg-white text-slate-700 border border-slate-200 px-2.5 py-1 rounded-[3px]">' . htmlspecialchars($skill) . '</span>';
                                            }
                                        }
                                        if (empty($skills_list[0])) {
                                            echo '<span class="text-[10px] font-semibold bg-white text-slate-700 border border-slate-200 px-2.5 py-1 rounded-[3px]">Verified</span>';
                                        }
                                        ?>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2 pt-3 border-t border-slate-200/80">
                                    <a href="messages.php?user=<?= $pro['id'] ?>" class="flex-1 text-center bg-white hover:bg-slate-100 border border-slate-200 text-slate-800 text-[11px] font-bold py-2.5 rounded-[3px] transition-colors">
                                        Message
                                    </a>
                                    <a href="provider-profile.php?id=<?= $pro['id'] ?>" class="flex-1 text-center bg-[#1952E1] hover:bg-blue-700 text-white text-[11px] font-bold py-2.5 rounded-[3px] transition-colors shadow-sm">
                                        View Profile
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- SECTION 3: WORK POSTED BY USERS (CARDS LAYOUT) -->
                <div class="bg-white border border-slate-200/90 rounded-[3px] p-5 sm:p-6 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between pb-4 mb-4 border-b border-slate-100 gap-3">
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-slate-900">Work Posted by Clients</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Explore open project contracts & service requests</p>
                        </div>
                        <a href="projects.php" class="text-xs font-bold text-[#1952E1] hover:underline flex items-center gap-1">
                            <span>Explore All Listings</span>
                            <i class="ph-bold ph-arrow-right text-xs"></i>
                        </a>
                    </div>

                    
                    <?php if(empty($open_projects)): ?>
                        <div class="w-full flex flex-col items-center justify-center py-12 bg-slate-50/50 border border-dashed border-slate-300 rounded-[3px]">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm border border-slate-200">
                                <i class="ph-bold ph-clipboard-text text-2xl text-slate-400"></i>
                            </div>
                            <h3 class="font-bold text-sm text-slate-900 mb-1.5">No open work right now</h3>
                            <p class="text-[11px] text-slate-500 text-center max-w-[280px] mb-4">There are currently no active projects posted by other clients. Check back later!</p>
                            <a href="post-project.php" class="bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-[3px] transition-colors shadow-sm inline-flex items-center gap-1.5">
                                <i class="ph-bold ph-plus"></i> Post a Project
                            </a>
                        </div>
                    <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach($open_projects as $proj): ?>
                        <div class="p-4 rounded-[3px] border border-slate-200/90 bg-slate-50/50 hover:bg-white hover:border-[#1952E1] transition-all flex flex-col justify-between group">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2.5">
                                    <span class="text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-[#1952E1] border border-blue-200/80 px-2 py-0.5 rounded-[3px]">
                                        <?= htmlspecialchars($proj['category'] ?: 'Uncategorized') ?>
                                    </span>
                                    <span class="text-[11px] text-slate-400 font-medium">
                                        <?= $proj['proposal_count'] ?> proposal<?= $proj['proposal_count'] !== 1 ? 's' : '' ?>
                                    </span>
                                </div>
                                <h3 class="font-bold text-sm text-slate-900 group-hover:text-[#1952E1] transition-colors mb-1.5 truncate">
                                    <?= htmlspecialchars($proj['title']) ?>
                                </h3>
                                <p class="text-[11px] text-slate-600 leading-relaxed mb-3 line-clamp-3">
                                    <?= htmlspecialchars($proj['description']) ?>
                                </p>
                                <div class="flex flex-wrap gap-1.5 mb-4">
                                    <span class="text-[10px] font-medium bg-white text-slate-600 border border-slate-200 px-2 py-0.5 rounded-[3px]">Remote</span>
                                    <span class="text-[10px] font-medium bg-white text-slate-600 border border-slate-200 px-2 py-0.5 rounded-[3px]">Escrow</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-slate-200/80">
                                <span class="text-[11px] font-semibold text-slate-500">
                                    Budget: <?= $proj['budget'] > 0 ? '$' . number_format($proj['budget'], 2) : 'Negotiable' ?>
                                </span>
                                <a href="project-details.php?slug=<?= urlencode($proj['slug']) ?>" class="text-[11px] font-bold text-[#1952E1] hover:underline flex items-center gap-1">
                                    <span>View Details</span>
                                    <i class="ph-bold ph-caret-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- =================================================================
                 COLUMN 2: NARROWER STICKY COLUMN WITH INDEPENDENT SCROLL (4 Cols on Desktop)
                 Analysis Summary -> Direct Messages -> Recent Updates (Hidden Scrollbar)
                 ================================================================= -->
            <div class="lg:col-span-4 lg:sticky lg:top-0 space-y-5 self-start lg:max-h-[calc(100vh-100px)] lg:overflow-y-auto no-scrollbar scroll-smooth">
                
                <!-- SECTION 2A: WORKFLOW & CONTRACT ANALYSIS -->
                <div class="bg-white border border-slate-200/90 rounded-[3px] p-5 shadow-sm">
                    <div class="pb-3 mb-4 border-b border-slate-100">
                        <h3 class="font-bold text-sm text-slate-900">Project & Workflow Analysis</h3>
                        <p class="text-xs text-slate-500">Summary of ongoing operations</p>
                    </div>

                    
                    <!-- Double-Column Stat Cards Inside the Right Pane -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-[3px]">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Active Jobs</span>
                            <span class="text-xl font-black text-slate-900 mt-1 block"><?= $stats['active_jobs'] ?></span>
                        </div>
                        <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-[3px]">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Proposals</span>
                            <span class="text-xl font-black text-slate-900 mt-1 block"><?= $stats['proposals'] ?></span>
                        </div>
                        <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-[3px]">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Deliverables</span>
                            <span class="text-xl font-black text-slate-900 mt-1 block"><?= $stats['deliverables'] ?></span>
                        </div>
                        <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-[3px]">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Completed</span>
                            <span class="text-xl font-black text-slate-900 mt-1 block"><?= $stats['completed'] ?></span>
                        </div>
                    </div>
                    <a href="my-projects.php" class="block w-full text-center bg-[#0A2342] hover:bg-black text-white text-xs font-bold py-2.5 rounded-[3px] transition-colors mt-4">
                        View Project Management Desk
                    </a>
                </div>

                <!-- SECTION 2B: DIRECT CHAT MESSAGES PREVIEW -->
                <div class="bg-white border border-slate-200/90 rounded-[3px] p-5 shadow-sm">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-sm text-slate-900">Direct Messages</h3>
                            <?php $unread = count(array_filter($recent_messages, fn($m) => !$m['is_read'])); ?>
                            <?php if($unread > 0): ?>
                            <span class="bg-blue-50 text-[#1952E1] font-bold text-[10px] px-2 py-0.5 rounded-[3px]"><?= $unread ?> New</span>
                            <?php endif; ?>
                        </div>
                        <a href="messages.php" class="text-xs font-bold text-[#1952E1] hover:underline">Open Inbox</a>
                    </div>
                    
                    <div class="space-y-2.5">
                        <?php if(empty($recent_messages)): ?>
                            <div class="flex flex-col items-center justify-center py-6 text-center">
                                <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-2">
                                    <i class="ph-bold ph-chat-teardrop-text text-2xl text-slate-300"></i>
                                </div>
                                <p class="text-[11px] font-bold text-slate-600 mb-0.5">No messages yet</p>
                                <p class="text-[10px] text-slate-400">Your recent conversations will appear here.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach($recent_messages as $msg): ?>
                            <a href="messages.php" class="flex items-center gap-3 p-2 rounded-[3px] hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-colors">
                                <div class="w-9 h-9 rounded-[3px] bg-blue-100 text-[#1952E1] flex items-center justify-center font-bold text-sm shrink-0 uppercase">
                                    <?= substr($msg['sender_name'], 0, 1) ?>
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-bold text-slate-900 truncate"><?= htmlspecialchars($msg['sender_name']) ?></p>
                                        <span class="text-[10px] text-slate-400"><?= date('M j', strtotime($msg['created_at'])) ?></span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 truncate mt-0.5"><?= htmlspecialchars($msg['content']) ?></p>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="bg-white border border-slate-200/90 rounded-[3px] p-5 shadow-sm mt-5">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                        <h3 class="font-bold text-sm text-slate-900">Recent Activities</h3>
                        <i class="ph-bold ph-clock-counter-clockwise text-slate-400"></i>
                    </div>
                    
                    <div class="flex flex-col items-center justify-center py-5 text-center">
                        <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-2">
                            <i class="ph-bold ph-activity text-2xl text-slate-300"></i>
                        </div>
                        <p class="text-[11px] font-bold text-slate-600 mb-0.5">No recent activity</p>
                        <p class="text-[10px] text-slate-400">Project updates and milestone alerts will show up here.</p>
                    </div>
                </div>

            </div>

        </div>

    </div>
</main>

<!-- Main flex container ends -->
</div> 

<?php include __DIR__ . '/components/footer.php'; ?>



