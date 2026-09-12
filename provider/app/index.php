<?php
$page_title = 'Provider Dashboard';
$active_tab = 'dashboard';
require_once __DIR__ . '/components/head.php';

// Fetch Recommended Open Projects submitted by Clients
$recommended_jobs = [];
try {
    $r_stmt = $db->prepare("
        SELECT p.*, u.full_name as client_name, u.avatar_url as client_avatar,
               (SELECT COUNT(*) FROM proposals pr WHERE pr.project_id = p.id) as proposal_count
        FROM projects p
        LEFT JOIN users u ON p.client_id = u.id
        WHERE p.status = 'open' AND p.client_id != :uid
        ORDER BY p.created_at DESC
        LIMIT 4
    ");
    $r_stmt->execute([':uid' => $user_id]);
    $recommended_jobs = $r_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\Exception $e) {
    $recommended_jobs = [];
}

// Fallback high-fidelity sample listings if no client projects exist in DB
if (empty($recommended_jobs)) {
    $recommended_jobs = [
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
            'created_at' => date('Y-m-d H:i:s', strtotime('-8 hours')),
            'skills' => ['Payment Gateway', 'API Security', 'Webhooks', 'JavaScript', 'Backend'],
            'description' => 'Need a seasoned backend developer to integrate real-time payment gateway listeners, automated milestone disbursement logic, idempotency verification, and security stress tests.'
        ]
    ];
}

// Fetch Recent Messages / Active Client Conversations
$recent_messages = [];
try {
    $msg_query = "
        SELECT u.id as sender_id, u.full_name as sender_name, u.username, u.avatar_url,
               (SELECT content FROM messages WHERE (sender_id = :uid1 AND receiver_id = u.id) OR (sender_id = u.id AND receiver_id = :uid2) ORDER BY created_at DESC LIMIT 1) as last_message,
               (SELECT created_at FROM messages WHERE (sender_id = :uid3 AND receiver_id = u.id) OR (sender_id = u.id AND receiver_id = :uid4) ORDER BY created_at DESC LIMIT 1) as last_time,
               (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = :uid5 AND is_read = 0) as unread_count
        FROM users u
        WHERE u.id != :uid6 AND (
            EXISTS (SELECT 1 FROM messages WHERE sender_id = :uid7 AND receiver_id = u.id) OR
            EXISTS (SELECT 1 FROM messages WHERE sender_id = u.id AND receiver_id = :uid8)
        )
        ORDER BY last_time DESC LIMIT 3
    ";
    $msg_stmt = $db->prepare($msg_query);
    $msg_stmt->execute([
        ':uid1' => $user_id,
        ':uid2' => $user_id,
        ':uid3' => $user_id,
        ':uid4' => $user_id,
        ':uid5' => $user_id,
        ':uid6' => $user_id,
        ':uid7' => $user_id,
        ':uid8' => $user_id
    ]);
    $recent_messages = $msg_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\Exception $e) {
    $recent_messages = [];
}

// Fallback high-fidelity sample conversations if DB has no messages yet
if (empty($recent_messages)) {
    $recent_messages = [
        [
            'sender_id' => 10,
            'sender_name' => 'Apex Digital Solutions',
            'username' => 'apexdigital',
            'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
            'last_message' => 'Hey Joseph, we reviewed your proposal for the escrow API. Are you available for a brief sync today?',
            'last_time' => date('Y-m-d H:i:s', strtotime('-15 minutes')),
            'unread_count' => 1,
            'is_online' => true
        ],
        [
            'sender_id' => 11,
            'sender_name' => 'Kuda Capital Partners',
            'username' => 'kudacapital',
            'avatar_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80',
            'last_message' => 'Milestone 2 deliverable has been accepted. Great work on the payment listeners!',
            'last_time' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'unread_count' => 0,
            'is_online' => false
        ],
        [
            'sender_id' => 12,
            'sender_name' => 'Nexus Health Innovations',
            'username' => 'nexushealth',
            'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
            'last_message' => 'Can you send over the updated Figma component library link?',
            'last_time' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'unread_count' => 0,
            'is_online' => true
        ]
    ];
}
?>

<!-- =========================================================================
     1. FULL-HEIGHT LEFT VERTICAL NAVIGATION DOCK
     ========================================================================= -->
<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- =========================================================================
     2. FULL-PAGE MAIN CONTENT WORKSPACE
     ========================================================================= -->
<div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
    <?php include __DIR__ . '/components/header.php'; ?>
    <main class="flex-1 px-4 sm:px-8 lg:px-12 py-6 pb-36 sm:pb-16 space-y-7 max-w-[1600px] mx-auto w-full">
    
    <!-- ---------------------------------------------------------------------
         HEADER TOPBAR: Search Pill, Capsule Schedule Bar & Settings
         --------------------------------------------------------------------- -->
    

    <!-- ---------------------------------------------------------------------
         HERO BANNER: Greeting, Quick Actions & Integrated Gig Search (Mobile-Optimized & Fancy)
         --------------------------------------------------------------------- -->
    <?php $firstName = explode(' ', $user_name ?? 'Provider')[0]; ?>
    <section class="relative rounded-[3px] p-5 sm:p-7 md:p-9 lg:p-10 overflow-hidden bg-[#0A2342] border border-slate-700/50 shadow-[0_12px_40px_rgba(10,35,66,0.18)] flex flex-col justify-between">
        
        <!-- Ambient Decorative Glow Lights (Fancy Mesh Gradients) -->
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>
        
        <!-- High-Quality Photographic Background Layer with Dark Tint -->
        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1600&auto=format&fit=crop&q=80" alt="Workspace Background" class="absolute inset-0 w-full h-full object-cover opacity-15 mix-blend-luminosity pointer-events-none">
        
        <!-- Subtle High-Tech Dot Matrix Pattern -->
        <div class="absolute inset-0 opacity-[0.07] bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>
        
        <!-- High-Contrast Dark Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#0A2342]/98 via-[#0A2342]/90 to-[#07192F]/95 pointer-events-none"></div>

        <!-- Top Section: Welcome Info & Action Buttons -->
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-5 sm:gap-6 w-full mb-5 sm:mb-6">
            
            <div class="max-w-2xl text-white space-y-2.5">
                <!-- Badges Row -->
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 bg-white/10 backdrop-blur-md text-slate-100 border border-white/15 px-2.5 sm:px-3 py-1 rounded-[3px] text-[10px] sm:text-[11px] font-bold shadow-xs">
                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span><?= date('j F Y') ?></span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 <?= $is_verified_pro ? 'bg-emerald-500/20 text-emerald-300 border-emerald-400/40' : 'bg-blue-500/20 text-blue-300 border-blue-400/40' ?> backdrop-blur-md border px-2.5 sm:px-3 py-1 rounded-[3px] text-[10px] sm:text-[11px] font-bold shadow-xs">
                        <span class="w-1.5 h-1.5 rounded-full <?= $is_verified_pro ? 'bg-emerald-400 animate-pulse' : 'bg-blue-400' ?>"></span>
                        <span><?= $is_verified_pro ? 'Verified Pro Account' : 'Provider Workspace' ?></span>
                    </span>
                </div>
                
                <!-- Headline -->
                <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight font-heading">
                    Welcome back, <span class="bg-gradient-to-r from-white via-blue-100 to-blue-300 bg-clip-text text-transparent"><?= htmlspecialchars($firstName) ?></span>!
                </h1>
                
                <p class="text-xs sm:text-sm text-slate-300 font-normal leading-relaxed max-w-xl">
                    Discover high-budget client contracts, submit competitive bids with protected price floors, and deliver escrow milestones.
                </p>
            </div>

            <!-- Action Buttons: Responsive Grid / Flex -->
            <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2.5 sm:gap-3 shrink-0 w-full sm:w-auto pt-1 sm:pt-0">
                <a href="jobs.php" class="col-span-1 sm:col-auto px-4 sm:px-6 py-2.5 sm:py-3 bg-[#1952E1] hover:bg-blue-700 text-white font-extrabold text-xs sm:text-sm rounded-[3px] shadow-lg shadow-blue-600/30 transition-all hover:scale-105 active:scale-95 flex items-center justify-center gap-1.5 sm:gap-2 text-center">
                    <span>Browse Gigs</span>
                    <span class="text-sm">→</span>
                </a>
                <a href="create-package.php" class="col-span-1 sm:col-auto px-4 sm:px-6 py-2.5 sm:py-3 bg-white/95 hover:bg-white text-slate-900 font-extrabold text-xs sm:text-sm rounded-[3px] shadow-md transition-all hover:scale-105 active:scale-95 flex items-center justify-center gap-1.5 sm:gap-2 text-center">
                    <svg class="w-4 h-4 text-[#1952E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Post Service</span>
                </a>
            </div>

        </div>

        <!-- Integrated Search Bar & Trending Tags for Gigs -->
        <div class="relative z-10 w-full pt-3.5 sm:pt-4 border-t border-white/10">
            <form action="jobs.php" method="GET" class="relative flex items-center w-full">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" placeholder="Search open gigs (e.g. PHP Web Portal, Figma UI/UX, Data Analysis)..." class="w-full bg-white/95 focus:bg-white border border-slate-200 rounded-[3px] pl-10 pr-20 sm:pr-24 py-2 sm:py-2.5 text-xs font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1952E1] focus:ring-2 focus:ring-[#1952E1]/20 shadow-md transition-all">
                <button type="submit" class="absolute right-1 sm:right-1.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-[11px] sm:text-xs px-3 sm:px-4 py-1.5 rounded-[3px] transition-colors shadow-sm">
                    Search
                </button>
            </form>
            
            <!-- Quick Search Tags with Horizontal Scroll on Mobile -->
            <div class="flex items-center gap-1.5 sm:gap-2 mt-2.5 text-[10px] sm:text-[11px] text-slate-300 overflow-x-auto whitespace-nowrap pb-1 no-scrollbar">
                <span class="text-slate-400 font-semibold shrink-0">Popular:</span>
                <a href="jobs.php?category=web-development" class="px-2 py-0.5 bg-white/5 hover:bg-white/15 border border-white/10 rounded-[3px] text-slate-200 transition-colors">Web Development</a>
                <a href="jobs.php?category=ui-ux-design" class="px-2 py-0.5 bg-white/5 hover:bg-white/15 border border-white/10 rounded-[3px] text-slate-200 transition-colors">UI/UX Design</a>
                <a href="jobs.php?category=academic-writing" class="px-2 py-0.5 bg-white/5 hover:bg-white/15 border border-white/10 rounded-[3px] text-slate-200 transition-colors">Academic & SPSS</a>
                <a href="jobs.php?category=mobile-apps" class="px-2 py-0.5 bg-white/5 hover:bg-white/15 border border-white/10 rounded-[3px] text-slate-200 transition-colors">Mobile Apps</a>
            </div>
        </div>

    </section>

    <!-- ---------------------------------------------------------------------
         MIDDLE 3 BIG CARDS (APPLIED 3PX RADIUS): Interviews, Applications, Payments
         --------------------------------------------------------------------- -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- ================= CARD 1: Interviews (12) (3px Radius) ================= -->
        <div class="bg-white rounded-[3px] p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-200/70 flex flex-col justify-between space-y-4 hover:shadow-[0_8px_30px_rgba(0,0,0,0.06)] transition-all">
            
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h2 class="text-base font-black text-slate-900 font-heading">Interviews (3)</h2>
                <a href="interviews.php" class="px-3.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors">
                    All
                </a>
            </div>

            <!-- List Items -->
            <div class="space-y-3 flex-1">
                
                <!-- Item 1 -->
                <div class="p-3 bg-slate-50/80 hover:bg-slate-100/90 rounded-[3px] flex items-center justify-between transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[3px] bg-white shadow-xs flex items-center justify-center shrink-0 border border-slate-100">
                            <svg viewBox="0 0 24 24" class="w-5 h-5"><path fill="#00832d" d="M12 7l5 5-5 5V7z"/><path fill="#0066da" d="M3 7h9v10H3z"/><path fill="#e92714" d="M18 10.5l4-3v9l-4-3z"/><path fill="#fbb000" d="M18 10.5l4-3v9l-4-3z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-extrabold text-slate-900 font-heading">Google Meet Call</div>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <div class="flex -space-x-1.5">
                                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&auto=format&fit=crop&q=80" class="w-4 h-4 rounded-full border border-white object-cover">
                                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=80&auto=format&fit=crop&q=80" class="w-4 h-4 rounded-full border border-white object-cover">
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Apex Global Tech</span>
                            </div>
                        </div>
                    </div>
                    <span class="px-3.5 py-1.5 bg-[#1952E1] text-white text-[11px] font-bold rounded-full shadow-xs">
                        09:00 AM
                    </span>
                </div>

                <!-- Item 2 -->
                <div class="p-3 bg-slate-50/80 hover:bg-slate-100/90 rounded-[3px] flex items-center justify-between transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[3px] bg-white shadow-xs flex items-center justify-center shrink-0 border border-slate-100">
                            <svg viewBox="0 0 24 24" class="w-5 h-5"><path fill="#00832d" d="M12 7l5 5-5 5V7z"/><path fill="#0066da" d="M3 7h9v10H3z"/><path fill="#e92714" d="M18 10.5l4-3v9l-4-3z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-extrabold text-slate-900 font-heading">Google Meet Call</div>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <div class="flex -space-x-1.5">
                                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=80&auto=format&fit=crop&q=80" class="w-4 h-4 rounded-full border border-white object-cover">
                                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=80&auto=format&fit=crop&q=80" class="w-4 h-4 rounded-full border border-white object-cover">
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Madira Studios</span>
                            </div>
                        </div>
                    </div>
                    <span class="px-3.5 py-1.5 bg-[#1952E1] text-white text-[11px] font-bold rounded-full shadow-xs">
                        10:00 AM
                    </span>
                </div>

                <!-- Item 3 -->
                <div class="p-3 bg-slate-50/80 hover:bg-slate-100/90 rounded-[3px] flex items-center justify-between transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[3px] bg-white shadow-xs flex items-center justify-center shrink-0 border border-slate-100">
                            <svg viewBox="0 0 24 24" class="w-5 h-5"><path fill="#00832d" d="M12 7l5 5-5 5V7z"/><path fill="#0066da" d="M3 7h9v10H3z"/><path fill="#e92714" d="M18 10.5l4-3v9l-4-3z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-extrabold text-slate-900 font-heading">Google Meet Call</div>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <div class="flex -space-x-1.5">
                                    <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=80&auto=format&fit=crop&q=80" class="w-4 h-4 rounded-full border border-white object-cover">
                                    <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=80&auto=format&fit=crop&q=80" class="w-4 h-4 rounded-full border border-white object-cover">
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Venture Digital</span>
                            </div>
                        </div>
                    </div>
                    <span class="px-3.5 py-1.5 bg-[#1952E1] text-white text-[11px] font-bold rounded-full shadow-xs">
                        10:30 AM
                    </span>
                </div>

            </div>

        </div>

        <!-- ================= CARD 2: Job Applications (3px Radius) ================= -->
        <div class="bg-white rounded-[3px] p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-200/70 flex flex-col justify-between space-y-4 hover:shadow-[0_8px_30px_rgba(0,0,0,0.06)] transition-all">
            
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h2 class="text-base font-black text-slate-900 font-heading">Job Applications</h2>
                <a href="jobs.php" class="px-3.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors">
                    All
                </a>
            </div>

            <!-- Big Metric Inner Box -->
            <div class="p-4 bg-slate-50/90 rounded-[3px] border border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-[3px] bg-white border border-slate-200/80 text-slate-700 flex items-center justify-center shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <div class="text-3xl font-black text-slate-900 tracking-tight font-heading">33</div>
                        <div class="text-[11px] text-slate-400 font-medium">Applications Sent</div>
                    </div>
                </div>
                <a href="jobs.php" class="w-8 h-8 rounded-[3px] bg-white hover:bg-slate-100 border border-slate-200/80 text-slate-600 hover:text-[#1952E1] flex items-center justify-center transition-colors shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <!-- 3 Sub Metrics -->
            <div class="grid grid-cols-3 gap-2 text-center pt-1">
                <div>
                    <div class="text-lg font-black text-slate-950 font-heading">18</div>
                    <div class="text-[10px] text-slate-400 font-medium">Submitted</div>
                </div>
                <div>
                    <div class="text-lg font-black text-slate-955 font-heading">12</div>
                    <div class="text-[10px] text-slate-400 font-medium">In Review</div>
                </div>
                <div>
                    <div class="text-lg font-black text-slate-955 font-heading">3</div>
                    <div class="text-[10px] text-slate-400 font-medium">Interviews</div>
                </div>
            </div>

            <!-- Segmented Progress Bar -->
            <div class="flex items-center gap-1.5 pt-1">
                <div class="h-3 rounded-full bg-[#1952E1]/20 flex-1"></div>
                <div class="h-3 rounded-full bg-slate-200/80 w-1/3"></div>
                <div class="h-3 rounded-full border border-dashed border-slate-300 w-1/4"></div>
            </div>

        </div>

        <!-- ================= CARD 3: Payments & Escrow (3px Radius) ================= -->
        <div class="bg-white rounded-[3px] p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-200/70 flex flex-col justify-between space-y-4 hover:shadow-[0_8px_30px_rgba(0,0,0,0.06)] transition-all">
            
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h2 class="text-base font-black text-slate-900 font-heading">Payments</h2>
                <a href="earnings.php" class="px-3.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors">
                    All
                </a>
            </div>

            <!-- Big Metric Inner Box -->
            <div class="p-4 bg-slate-50/90 rounded-[3px] border border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-[3px] bg-white border border-slate-200/80 text-slate-700 flex items-center justify-center shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <div>
                        <div class="text-3xl font-black text-slate-900 tracking-tight font-heading">2.1k</div>
                        <div class="text-[11px] text-slate-400 font-medium">Current Earnings, $</div>
                    </div>
                </div>
                <a href="earnings.php" class="w-8 h-8 rounded-[3px] bg-white hover:bg-slate-100 border border-slate-200/80 text-slate-600 hover:text-[#1952E1] flex items-center justify-center transition-colors shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <!-- 3 Sub Metrics -->
            <div class="grid grid-cols-3 gap-2 text-center pt-1">
                <div>
                    <div class="text-lg font-black text-slate-900 font-heading">$2.1k</div>
                    <div class="text-[10px] text-slate-400 font-medium">Cleared</div>
                </div>
                <div>
                    <div class="text-lg font-black text-slate-900 font-heading">$900</div>
                    <div class="text-[10px] text-slate-400 font-medium">10-Day Review</div>
                </div>
                <div>
                    <div class="text-lg font-black text-slate-900 font-heading">$300</div>
                    <div class="text-[10px] text-slate-400 font-medium">In Escrow</div>
                </div>
            </div>

            <!-- Audio Equalizer Frequency Wave Activity Indicator -->
            <div class="flex items-center gap-1 h-3 pt-1">
                <span class="w-1 h-2 bg-blue-300 rounded-full"></span>
                <span class="w-1 h-3 bg-blue-400 rounded-full"></span>
                <span class="w-1 h-1.5 bg-blue-300 rounded-full"></span>
                <span class="w-1 h-2.5 bg-blue-500 rounded-full"></span>
                <span class="w-1 h-3 bg-blue-400 rounded-full"></span>
                <span class="w-1 h-1.5 bg-blue-300 rounded-full"></span>
                <span class="w-1 h-2 bg-blue-400 rounded-full"></span>
                <span class="w-1 h-3 bg-blue-500 rounded-full"></span>
                <div class="h-3 rounded-full bg-slate-200 flex-1 ml-1"></div>
                <div class="h-3 rounded-full bg-slate-100 w-1/4"></div>
            </div>

        </div>

    </section>

    <!-- ---------------------------------------------------------------------
         BOTTOM SECTION (APPLIED 3PX RADIUS): Recommended Jobs & Community
         --------------------------------------------------------------------- -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start pb-8">
        
        <!-- Left: Recommended Jobs (Span 7) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-black text-slate-900 font-heading">Recommended Jobs</h2>
                    <span class="px-2 py-0.5 bg-blue-50 text-[#1952E1] border border-blue-200 text-[10px] font-bold rounded-[3px]"><?= count($recommended_jobs) ?> Live</span>
                </div>
                <a href="jobs.php" class="text-xs font-bold text-[#1952E1] hover:underline flex items-center gap-1">
                    <span>View All Jobs</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <!-- Jobs List (Upwork Style with Proper Spacing & Real Avatars) -->
            <div class="space-y-4">
                <?php foreach ($recommended_jobs as $job): 
                    $time_posted = !empty($job['created_at']) ? date('M j, Y', strtotime($job['created_at'])) : 'Recent';
                    $budget_fmt = is_numeric($job['budget']) ? '₦' . number_format($job['budget']) : htmlspecialchars($job['budget']);
                    $tier = htmlspecialchars($job['experience_tier'] ?? 'Intermediate');
                    $client = htmlspecialchars($job['client_name'] ?? 'Verified Client');
                    $bids = (int)($job['proposal_count'] ?? 0);
                    $desc = htmlspecialchars($job['description'] ?? '');
                    
                    // Client Avatar resolution
                    $avatar_src = !empty($job['client_avatar']) 
                        ? (str_starts_with($job['client_avatar'], 'http') ? $job['client_avatar'] : '../../' . ltrim($job['client_avatar'], '/')) 
                        : 'https://ui-avatars.com/api/?name=' . urlencode($client) . '&background=0A2540&color=fff&bold=true&size=128';
                    
                    $client_spent = htmlspecialchars($job['client_spent'] ?? '₦1.2M+ spent');
                    $client_rating = htmlspecialchars($job['client_rating'] ?? '5.0');
                    $client_location = htmlspecialchars($job['client_location'] ?? 'Nigeria');
                    $skills = $job['skills'] ?? [$job['category'] ?? 'Freelance', 'Milestones', 'Escrow Verified'];
                ?>
                <div class="bg-white rounded-[12px] p-5 sm:p-6 shadow-[0_2px_12px_rgba(0,0,0,0.03)] border border-slate-200/90 hover:border-slate-300 hover:shadow-[0_8px_24px_rgba(0,0,0,0.06)] transition-all space-y-3.5 group">
                    
                    <!-- 1. Top Metadata Row: Time Posted & Escrow Badge -->
                    <div class="flex items-center justify-between gap-2 text-xs flex-wrap">
                        <span class="text-[11px] text-slate-400 font-medium flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Posted <?= $time_posted ?></span>
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold tracking-wide rounded-md uppercase">
                                <?= htmlspecialchars($job['category'] ?? 'General') ?>
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-md">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>ESCROW FUNDED</span>
                            </span>
                        </div>
                    </div>

                    <!-- 2. Job Title -->
                    <div class="space-y-1">
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-[#1952E1] transition-colors font-heading leading-snug">
                            <a href="jobs.php?q=<?= urlencode($job['title']) ?>">
                                <?= htmlspecialchars($job['title']) ?>
                            </a>
                        </h3>
                    </div>

                    <!-- 3. Upwork Style Meta Bar (Budget, Tier, Delivery, Proposals) -->
                    <div class="flex items-center gap-2.5 sm:gap-3.5 text-xs text-slate-500 flex-wrap py-0.5">
                        <div class="flex items-center gap-1">
                            <span class="font-bold text-slate-900 text-sm sm:text-base font-heading"><?= $budget_fmt ?></span>
                            <span class="text-[11px] text-slate-400 font-medium">(Fixed)</span>
                        </div>
                        <span class="text-slate-300">•</span>
                        <span class="text-slate-700 font-medium"><?= ucfirst($tier) ?> Level</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-slate-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <?= $bids ?> Proposal<?= $bids === 1 ? '' : 's' ?>
                        </span>
                        <?php if (!empty($job['deadline_date'])): ?>
                        <span class="text-slate-300 hidden sm:inline">•</span>
                        <span class="text-slate-500 text-[11px] hidden sm:inline">Delivery: <?= date('M j, Y', strtotime($job['deadline_date'])) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- 4. Job Description with Comfortable Line Spacing -->
                    <?php if (!empty($desc)): ?>
                        <p class="text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal line-clamp-3">
                            <?= $desc ?>
                        </p>
                    <?php endif; ?>

                    <!-- 5. Skills Tags Pill Row -->
                    <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                        <?php foreach (array_slice($skills, 0, 5) as $skill): ?>
                            <span class="px-2.5 py-1 bg-slate-100/90 text-slate-700 text-[11px] font-medium rounded-[4px]">
                                <?= htmlspecialchars($skill) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>

                    <!-- 6. Client Information & Apply Action Footer -->
                    <div class="pt-3.5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                        
                        <!-- Client Profile with Real Photo Avatar -->
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="<?= $avatar_src ?>" alt="<?= htmlspecialchars($client) ?>" class="w-10 h-10 rounded-full object-cover ring-1 ring-slate-200 shadow-2xs shrink-0" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($client) ?>&background=0A2540&color=fff&bold=true'">
                            
                            <div class="space-y-0.5 min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs sm:text-sm font-bold text-slate-900 truncate"><?= $client ?></span>
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

                        <!-- Apply Button -->
                        <div class="flex items-center justify-end shrink-0">
                            <a href="jobs.php?q=<?= urlencode($job['title']) ?>" class="w-full sm:w-auto px-5 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs rounded-[4px] transition-all shadow-xs hover:shadow-md inline-flex items-center justify-center gap-1.5">
                                <span>Apply Now</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>

                    </div>

                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Right: News & Updates (Span 5) (3px Radius) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-black text-slate-900 font-heading">News & Updates</h2>
                <a href="community.php" class="px-3.5 py-1 rounded-full bg-white hover:bg-slate-100 border border-slate-200/80 text-slate-600 text-xs font-bold transition-colors">
                    All
                </a>
            </div>

            <!-- Community Card (3px Radius) -->
            <div class="bg-white rounded-[3px] p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-200/70 hover:shadow-[0_8px_30px_rgba(0,0,0,0.06)] transition-all space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-[3px] bg-slate-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-slate-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18.178 8c5.096 0 5.096 8 0 8-2.69 0-4.7-2.115-6.178-4-1.478-1.885-3.488-4-6.178-4-5.096 0-5.096 8 0 8 2.69 0 4.7-2.115 6.178-4 1.478-1.885 3.488-4 6.178-4z"></path></svg>
                        </div>
                        <h3 class="text-xs sm:text-sm font-black text-slate-900 font-heading">Check out our Community Feature</h3>
                    </div>
                    <a href="community.php" class="px-4 py-1.5 bg-[#1952E1] hover:bg-blue-700 text-white text-xs font-extrabold rounded-full shadow-xs transition-colors font-heading">
                        Community
                    </a>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    Connect with other verified service providers, review marketplace best practices, and receive direct project leads from clients.
                </p>
            </div>

            <!-- Messages Box Widget (Under News & Updates) -->
            <div class="space-y-3 pt-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-black text-slate-900 font-heading">Recent Messages</h2>
                        <?php 
                        $total_unreads = array_sum(array_column($recent_messages, 'unread_count'));
                        if ($total_unreads > 0): 
                        ?>
                        <span class="px-2 py-0.5 bg-blue-50 text-[#1952E1] border border-blue-200 text-[10px] font-bold rounded-[3px]"><?= $total_unreads ?> New</span>
                        <?php endif; ?>
                    </div>
                    <a href="../../app/messages.php" class="text-xs font-bold text-[#1952E1] hover:underline flex items-center gap-1">
                        <span>Open Inbox</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <!-- Messages Card Container -->
                <div class="bg-white rounded-[12px] p-4 sm:p-5 shadow-[0_2px_12px_rgba(0,0,0,0.03)] border border-slate-200/90 divide-y divide-slate-100">
                    <?php foreach ($recent_messages as $msg): 
                        $msg_avatar = !empty($msg['avatar_url']) 
                            ? (str_starts_with($msg['avatar_url'], 'http') ? $msg['avatar_url'] : '../../' . ltrim($msg['avatar_url'], '/')) 
                            : 'https://ui-avatars.com/api/?name=' . urlencode($msg['sender_name']) . '&background=0A2540&color=fff&bold=true';
                        $msg_time = !empty($msg['last_time']) ? date('M j, g:i a', strtotime($msg['last_time'])) : 'Recent';
                        $unread = ($msg['unread_count'] ?? 0) > 0;
                    ?>
                    <a href="../../app/messages.php?user=<?= urlencode($msg['username'] ?? '') ?>" class="py-3 first:pt-0 last:pb-0 flex items-start gap-3 hover:bg-slate-50/80 -mx-2 px-2 rounded-lg transition-colors group">
                        
                        <!-- Avatar with Online Status Indicator -->
                        <div class="relative shrink-0 mt-0.5">
                            <img src="<?= $msg_avatar ?>" alt="<?= htmlspecialchars($msg['sender_name']) ?>" class="w-9 h-9 rounded-full object-cover ring-1 ring-slate-200 shadow-2xs" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($msg['sender_name']) ?>&background=0A2540&color=fff&bold=true'">
                            <?php if (!empty($msg['is_online'])): ?>
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                            <?php endif; ?>
                        </div>

                        <!-- Sender & Message Snippet -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <h4 class="text-xs font-bold text-slate-900 truncate group-hover:text-[#1952E1] transition-colors">
                                    <?= htmlspecialchars($msg['sender_name']) ?>
                                </h4>
                                <span class="text-[10px] text-slate-400 font-medium shrink-0"><?= $msg_time ?></span>
                            </div>
                            
                            <p class="text-xs text-slate-500 truncate mt-0.5 <?= $unread ? 'font-semibold text-slate-800' : 'font-normal' ?>">
                                <?= htmlspecialchars($msg['last_message'] ?? 'Start a conversation...') ?>
                            </p>
                        </div>

                        <!-- Unread Dot / Counter -->
                        <?php if ($unread): ?>
                        <div class="shrink-0 self-center">
                            <span class="w-5 h-5 rounded-full bg-[#1952E1] text-white text-[10px] font-extrabold flex items-center justify-center shadow-xs">
                                <?= $msg['unread_count'] ?>
                            </span>
                        </div>
                        <?php endif; ?>

                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </section>

</main>
</div>
<!-- Mobile Bottom Navigation Bar -->
<?php include __DIR__ . '/components/bottom-nav.php'; ?>

<!-- Component Footer & Scripts -->
<?php include __DIR__ . '/components/footer.php'; ?>
