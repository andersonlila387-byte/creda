<?php 
$page_title = 'Review Proposals';
$active_tab = 'projects';
require_once __DIR__ . '/components/head.php'; 

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : 'dummy-slug';
if (isset($_SERVER['REQUEST_URI']) && preg_match('#/([A-Za-z0-9_-]+)$#', $_SERVER['REQUEST_URI'], $matches)) {
    $slug = $matches[1];
}
?>

<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Layout Area -->
<main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-hidden relative bg-[#EFF2F7]">
    
    <?php include __DIR__ . '/components/header.php'; ?>

    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 pb-24 md:pb-6 lg:pb-12 scroll-smooth">
        
        <div class="max-w-7xl mx-auto space-y-5 sm:space-y-6">

            <!-- PROJECT HEADER & BREADCRUMB BAR -->
            <div class="bg-white p-4 sm:p-6 rounded-[3px] border border-slate-200/90 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="my-projects.php" class="text-xs font-bold text-slate-400 hover:text-slate-700 uppercase tracking-wider flex items-center gap-1">
                            <i class="ph-bold ph-arrow-left"></i> Projects /
                        </a>
                        <span class="font-mono text-xs font-bold text-[#1952E1] bg-blue-50 px-2 py-0.5 rounded-[3px]"><?php echo htmlspecialchars($slug); ?></span>
                        <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-[3px] border border-emerald-200">
                            Open for Bids
                        </span>
                        <span class="text-slate-400 text-xs">• Posted 2 days ago</span>
                    </div>
                    <h1 class="text-lg sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Full-Stack PHP & MySQL Web Portal with Escrow System
                    </h1>
                    <div class="flex items-center gap-3 text-xs text-slate-500 pt-0.5 flex-wrap">
                        <span>Client Budget: <strong class="text-slate-800">₦180,000</strong></span>
                        <span>•</span>
                        <span>Scope: <strong class="text-slate-800">3 Milestones (2 Weeks)</strong></span>
                        <span>•</span>
                        <span>Category: <strong class="text-slate-800">Web Development</strong></span>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start md:self-auto shrink-0">
                    <a href="edit-listing.php?id=<?php echo urlencode($listing_id); ?>" class="text-xs font-bold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 border border-slate-200 px-3.5 py-2 rounded-[3px] transition-colors flex items-center gap-1.5 shadow-2xs">
                        <i class="ph-bold ph-pencil-simple text-sm"></i>
                        <span>Edit Listing</span>
                    </a>
                </div>
            </div>

            <!-- PROPOSALS METRIC BANNER (4 IN A ROW ON DESKTOP) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white p-4 sm:p-5 rounded-[3px] border border-slate-200/90 shadow-sm flex items-center gap-3.5">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center font-bold text-lg shrink-0">
                        <i class="ph-bold ph-users text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-xl sm:text-2xl font-black text-slate-900 block leading-tight">4</span>
                        <span class="text-[11px] text-slate-500 font-semibold truncate block">Total Proposals</span>
                    </div>
                </div>

                <div class="bg-white p-4 sm:p-5 rounded-[3px] border border-slate-200/90 shadow-sm flex items-center gap-3.5">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-[3px] bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg shrink-0">
                        <i class="ph-bold ph-seal-check text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-xl sm:text-2xl font-black text-slate-900 block leading-tight">2</span>
                        <span class="text-[11px] text-emerald-700 font-semibold truncate block">Pre-Assessed Pros</span>
                    </div>
                </div>

                <div class="bg-white p-4 sm:p-5 rounded-[3px] border border-slate-200/90 shadow-sm flex items-center gap-3.5">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-[3px] bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg shrink-0">
                        <i class="ph-bold ph-currency-ngn text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-xl sm:text-2xl font-black text-slate-900 block leading-tight">₦163.7k</span>
                        <span class="text-[11px] text-slate-500 font-semibold truncate block">Average Bid</span>
                    </div>
                </div>

                <div class="bg-white p-4 sm:p-5 rounded-[3px] border border-slate-200/90 shadow-sm flex items-center gap-3.5">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-[3px] bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg shrink-0">
                        <i class="ph-bold ph-star text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-xl sm:text-2xl font-black text-slate-900 block leading-tight">1</span>
                        <span class="text-[11px] text-amber-700 font-semibold truncate block">Shortlisted Pro</span>
                    </div>
                </div>
            </div>

            <!-- FILTER TABS & SORT CONTROLS -->
            <div class="bg-white p-3.5 sm:p-4 rounded-[3px] border border-slate-200/90 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                
                <!-- Filter Tabs -->
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar">
                    <button class="filter-tab active px-3 py-1.5 text-xs font-bold rounded-[3px] bg-[#1952E1] text-white shadow-sm whitespace-nowrap" data-filter="all">
                        All Proposals (4)
                    </button>
                    <button class="filter-tab px-3 py-1.5 text-xs font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-colors whitespace-nowrap" data-filter="shortlisted">
                        Shortlisted (1)
                    </button>
                    <button class="filter-tab px-3 py-1.5 text-xs font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-colors whitespace-nowrap" data-filter="verified">
                        Verified Pros (2)
                    </button>
                </div>

                <!-- Sort Control -->
                <div class="flex items-center gap-2 shrink-0">
                    <label class="text-xs text-slate-500 font-medium hidden sm:inline">Sort by:</label>
                    <select id="sort-proposals" class="bg-slate-50 border border-slate-200 rounded-[3px] px-2.5 py-1.5 text-xs font-medium text-slate-700 focus:outline-none focus:border-[#1952E1]">
                        <option value="top">Highest Rated & Verified</option>
                        <option value="lowest_bid">Lowest Bid Amount</option>
                        <option value="fastest">Fastest Delivery Time</option>
                    </select>
                </div>

            </div>

            <!-- PROPOSALS LISTING CONTAINER -->
            <div class="space-y-4" id="proposals-container">
                
                <!-- PROPOSAL CARD 1 (Elena Vance - Shortlisted Pro) -->
                <div class="proposal-card bg-white rounded-[3px] border-2 border-[#1952E1] p-5 sm:p-6 shadow-sm space-y-4" data-shortlisted="true" data-verified="true" data-price="175000" data-time="10">
                    
                    <!-- Candidate Header -->
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-start gap-3.5">
                            <div class="relative shrink-0">
                                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=140" alt="Elena" class="w-12 h-12 rounded-[3px] object-cover border border-slate-200">
                                <span class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-500 rounded-full ring-2 ring-white" title="Online now"></span>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-base font-bold text-slate-900">Elena Vance</h3>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-[#1952E1] text-[10px] font-bold rounded-[3px] border border-blue-200">
                                        <i class="ph-fill ph-seal-check"></i> Pre-Assessed Pro (100%)
                                    </span>
                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-800 text-[10px] font-bold rounded-[3px] border border-amber-200">
                                        ⭐ Shortlisted
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium">Senior Full-Stack Architect • UNILAG Computer Science Alumni</p>
                                <div class="flex items-center gap-3 text-xs text-slate-600 pt-0.5">
                                    <span class="flex items-center gap-1 font-bold text-amber-500">★ 5.0 <span class="text-slate-400 font-normal">(48 completed projects)</span></span>
                                    <span>•</span>
                                    <span class="text-emerald-700 font-semibold">100% On-Time Delivery</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bid & Delivery Tag -->
                        <div class="text-left sm:text-right bg-slate-50 sm:bg-transparent p-3 sm:p-0 rounded-[3px] border border-slate-200 sm:border-0 shrink-0">
                            <span class="text-xs text-slate-500 font-medium block">Proposed Total Bid</span>
                            <span class="text-xl sm:text-2xl font-black text-slate-900 block text-[#1952E1]">₦175,000</span>
                            <span class="text-[11px] text-slate-500 font-medium block">Delivery: <strong>10 Days</strong> (3 Milestones)</span>
                        </div>
                    </div>

                    <!-- Cover Letter -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Candidate Proposal & Approach</h4>
                        <p class="text-xs text-slate-700 leading-relaxed bg-slate-50/70 p-3.5 rounded-[3px] border border-slate-100">
                            “Hello! I have reviewed your requirements for the PHP MVC Web Portal with milestone escrow locking. I specialize in building custom PHP & MySQL applications with clean MVC separation, secure PDO queries, Tailwind UI integration, and Paystack/Flutterwave escrow webhook listeners. I can complete all 3 milestones within 10 days with a 14-day post-launch warranty.”
                        </p>
                    </div>

                    <!-- Proposed Milestone Breakdown -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Proposed Milestone Schedule</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                            <div class="p-2.5 bg-slate-50 rounded-[3px] border border-slate-200">
                                <span class="font-bold text-slate-800 block">1. DB & Auth Core</span>
                                <span class="text-[#1952E1] font-extrabold text-[11px]">₦55,000 • 3 Days</span>
                            </div>
                            <div class="p-2.5 bg-slate-50 rounded-[3px] border border-slate-200">
                                <span class="font-bold text-slate-800 block">2. Client Dashboard UI</span>
                                <span class="text-[#1952E1] font-extrabold text-[11px]">₦60,000 • 4 Days</span>
                            </div>
                            <div class="p-2.5 bg-slate-50 rounded-[3px] border border-slate-200">
                                <span class="font-bold text-slate-800 block">3. Gateway & Testing</span>
                                <span class="text-[#1952E1] font-extrabold text-[11px]">₦60,000 • 3 Days</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Row -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] text-slate-500">Skills Verified:</span>
                            <span class="text-[10px] font-bold bg-blue-50 text-[#1952E1] px-2 py-0.5 rounded-[3px]">PHP MVC</span>
                            <span class="text-[10px] font-bold bg-blue-50 text-[#1952E1] px-2 py-0.5 rounded-[3px]">MySQL</span>
                            <span class="text-[10px] font-bold bg-blue-50 text-[#1952E1] px-2 py-0.5 rounded-[3px]">Tailwind</span>
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <a href="messages.php" class="flex-1 sm:flex-none text-center text-xs font-bold text-slate-700 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3.5 py-2 rounded-[3px] transition-colors">
                                Message Elena
                            </a>
                            <button type="button" onclick="openHireModal('Elena Vance', '₦175,000', '₦55,000', '10 Days')" class="flex-1 sm:flex-none text-center text-xs font-bold bg-[#1952E1] hover:bg-blue-700 text-white px-5 py-2 rounded-[3px] transition-all shadow-sm">
                                Accept & Fund Escrow →
                            </button>
                        </div>
                    </div>

                </div>

                <!-- PROPOSAL CARD 2 (David Olanrewaju - Verified Backend Pro) -->
                <div class="proposal-card bg-white rounded-[3px] border border-slate-200/90 p-5 sm:p-6 shadow-sm hover:border-[#1952E1] transition-all space-y-4" data-shortlisted="false" data-verified="true" data-price="180000" data-time="14">
                    
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-start gap-3.5">
                            <div class="relative shrink-0">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=140" alt="David" class="w-12 h-12 rounded-[3px] object-cover border border-slate-200">
                                <span class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-500 rounded-full ring-2 ring-white"></span>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-base font-bold text-slate-900">David Olanrewaju</h3>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-[#1952E1] text-[10px] font-bold rounded-[3px] border border-blue-200">
                                        <i class="ph-fill ph-seal-check"></i> Pre-Assessed Pro (98%)
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium">Backend & Database Specialist • Covenant University Postgrad</p>
                                <div class="flex items-center gap-3 text-xs text-slate-600 pt-0.5">
                                    <span class="flex items-center gap-1 font-bold text-amber-500">★ 4.95 <span class="text-slate-400 font-normal">(32 jobs)</span></span>
                                    <span>•</span>
                                    <span class="text-emerald-700 font-semibold">99% Success Score</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-left sm:text-right bg-slate-50 sm:bg-transparent p-3 sm:p-0 rounded-[3px] border border-slate-200 sm:border-0 shrink-0">
                            <span class="text-xs text-slate-500 font-medium block">Proposed Total Bid</span>
                            <span class="text-xl sm:text-2xl font-black text-slate-900 block text-[#1952E1]">₦180,000</span>
                            <span class="text-[11px] text-slate-500 font-medium block">Delivery: <strong>14 Days</strong> (3 Milestones)</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Candidate Proposal & Approach</h4>
                        <p class="text-xs text-slate-700 leading-relaxed bg-slate-50/70 p-3.5 rounded-[3px] border border-slate-100">
                            “I have built 15+ student management and freelance escrow web platforms. My focus is rock-solid database normalization, CSRF and SQL-injection defense, and clean RESTful endpoints. Ready to commence immediately.”
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-[3px]">PHP</span>
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-[3px]">MySQL InnoDB</span>
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-[3px]">API Security</span>
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <button type="button" onclick="alert('David shortlisted!');" class="flex-1 sm:flex-none text-center text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3.5 py-2 rounded-[3px] transition-colors">
                                Shortlist
                            </button>
                            <a href="messages.php" class="flex-1 sm:flex-none text-center text-xs font-bold text-slate-700 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3.5 py-2 rounded-[3px] transition-colors">
                                Message David
                            </a>
                            <button type="button" onclick="openHireModal('David Olanrewaju', '₦180,000', '₦60,000', '14 Days')" class="flex-1 sm:flex-none text-center text-xs font-bold bg-[#1952E1] hover:bg-blue-700 text-white px-5 py-2 rounded-[3px] transition-all shadow-sm">
                                Accept & Fund Escrow →
                            </button>
                        </div>
                    </div>

                </div>

                <!-- PROPOSAL CARD 3 (Chinedu Okeke) -->
                <div class="proposal-card bg-white rounded-[3px] border border-slate-200/90 p-5 sm:p-6 shadow-sm hover:border-[#1952E1] transition-all space-y-4" data-shortlisted="false" data-verified="false" data-price="160000" data-time="12">
                    
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-start gap-3.5">
                            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=140" alt="Chinedu" class="w-12 h-12 rounded-[3px] object-cover border border-slate-200 shrink-0">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-base font-bold text-slate-900">Chinedu Okeke</h3>
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold rounded-[3px]">
                                        Verified Student Dev (FUTA)
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium">Full-Stack Web & Scripting Developer</p>
                                <div class="flex items-center gap-3 text-xs text-slate-600 pt-0.5">
                                    <span class="flex items-center gap-1 font-bold text-amber-500">★ 4.88 <span class="text-slate-400 font-normal">(19 jobs)</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="text-left sm:text-right bg-slate-50 sm:bg-transparent p-3 sm:p-0 rounded-[3px] border border-slate-200 sm:border-0 shrink-0">
                            <span class="text-xs text-slate-500 font-medium block">Proposed Total Bid</span>
                            <span class="text-xl sm:text-2xl font-black text-slate-900 block text-[#1952E1]">₦160,000</span>
                            <span class="text-[11px] text-slate-500 font-medium block">Delivery: <strong>12 Days</strong></span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Candidate Proposal & Approach</h4>
                        <p class="text-xs text-slate-700 leading-relaxed bg-slate-50/70 p-3.5 rounded-[3px] border border-slate-100">
                            “I can deliver this project smoothly with PHP and clean modern Tailwind UI styling. I have sample code in my portfolio repository and can deliver the first milestone in 3 days.”
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-[3px]">PHP</span>
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-[3px]">Tailwind CSS</span>
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <a href="messages.php" class="flex-1 sm:flex-none text-center text-xs font-bold text-slate-700 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3.5 py-2 rounded-[3px] transition-colors">
                                Message Chinedu
                            </a>
                            <button type="button" onclick="openHireModal('Chinedu Okeke', '₦160,000', '₦50,000', '12 Days')" class="flex-1 sm:flex-none text-center text-xs font-bold bg-[#1952E1] hover:bg-blue-700 text-white px-5 py-2 rounded-[3px] transition-all shadow-sm">
                                Accept & Fund Escrow →
                            </button>
                        </div>
                    </div>

                </div>

                <!-- PROPOSAL CARD 4 (Amina Bello) -->
                <div class="proposal-card bg-white rounded-[3px] border border-slate-200/90 p-5 sm:p-6 shadow-sm hover:border-[#1952E1] transition-all space-y-4" data-shortlisted="false" data-verified="false" data-price="140000" data-time="16">
                    
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-start gap-3.5">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=140" alt="Amina" class="w-12 h-12 rounded-[3px] object-cover border border-slate-200 shrink-0">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-base font-bold text-slate-900">Amina Bello</h3>
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold rounded-[3px]">
                                        Web Developer (ABU Zaria)
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium">Frontend & PHP Developer</p>
                                <div class="flex items-center gap-3 text-xs text-slate-600 pt-0.5">
                                    <span class="flex items-center gap-1 font-bold text-amber-500">★ 4.75 <span class="text-slate-400 font-normal">(8 jobs)</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="text-left sm:text-right bg-slate-50 sm:bg-transparent p-3 sm:p-0 rounded-[3px] border border-slate-200 sm:border-0 shrink-0">
                            <span class="text-xs text-slate-500 font-medium block">Proposed Total Bid</span>
                            <span class="text-xl sm:text-2xl font-black text-slate-900 block text-[#1952E1]">₦140,000</span>
                            <span class="text-[11px] text-slate-500 font-medium block">Delivery: <strong>16 Days</strong></span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Candidate Proposal</h4>
                        <p class="text-xs text-slate-700 leading-relaxed bg-slate-50/70 p-3.5 rounded-[3px] border border-slate-100">
                            “I would love to help on this project. I am proficient in responsive CSS layout, PHP form processing, and database configuration.”
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-[3px]">HTML/CSS</span>
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-[3px]">PHP</span>
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <a href="messages.php" class="flex-1 sm:flex-none text-center text-xs font-bold text-slate-700 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3.5 py-2 rounded-[3px] transition-colors">
                                Message Amina
                            </a>
                            <button type="button" onclick="openHireModal('Amina Bello', '₦140,000', '₦45,000', '16 Days')" class="flex-1 sm:flex-none text-center text-xs font-bold bg-[#1952E1] hover:bg-blue-700 text-white px-5 py-2 rounded-[3px] transition-all shadow-sm">
                                Accept & Fund Escrow →
                            </button>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
</main>

<!-- HIRE & FUND ESCROW MODAL -->
<div id="hire-modal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-[3px] border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-5">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center font-bold">
                    <i class="ph-bold ph-shield-check text-lg"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Accept Proposal & Fund Escrow</h3>
                    <p class="text-xs text-slate-500">100% Upfront Milestone Escrow Locking</p>
                </div>
            </div>
            <button type="button" onclick="closeHireModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
        </div>

        <div class="space-y-3 text-xs">
            <div class="p-3.5 bg-slate-50 rounded-[3px] border border-slate-200 space-y-1.5">
                <div class="flex justify-between font-bold text-slate-900">
                    <span>Hired Freelancer:</span>
                    <span id="modal-freelancer-name" class="text-[#1952E1]">Elena Vance</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Total Contract Value:</span>
                    <span id="modal-contract-total" class="font-bold">₦175,000</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Estimated Completion:</span>
                    <span id="modal-contract-days" class="font-bold">10 Days</span>
                </div>
            </div>

            <!-- Escrow Deposit Breakdown -->
            <div class="p-3.5 bg-blue-50/80 rounded-[3px] border border-blue-200 space-y-2">
                <div class="flex justify-between items-center text-sm font-extrabold text-[#0A2342]">
                    <span>Milestone 1 Escrow Deposit:</span>
                    <span id="modal-milestone-deposit" class="text-[#1952E1] text-base">₦55,000</span>
                </div>
                <p class="text-[11px] text-slate-600 leading-relaxed">
                    Funds are locked in the Scriptly Escrow Vault. They will only be released to the freelancer once you inspect and approve Milestone 1 deliverables.
                </p>
            </div>

            <!-- Payment Method Selector -->
            <div class="space-y-2 pt-1">
                <label class="block font-bold text-slate-700">Choose Escrow Payment Source:</label>
                <div class="space-y-2">
                    <label class="flex items-center justify-between p-3 rounded-[3px] border border-[#1952E1] bg-blue-50/50 cursor-pointer">
                        <div class="flex items-center gap-2.5">
                            <input type="radio" name="payment_source" value="wallet" checked class="text-[#1952E1] focus:ring-0">
                            <span class="font-bold text-slate-900">Scriptly Wallet Balance</span>
                        </div>
                        <span class="font-bold text-emerald-700">₦320,000.00 Available</span>
                    </label>
                    <label class="flex items-center justify-between p-3 rounded-[3px] border border-slate-200 bg-slate-50 cursor-pointer">
                        <div class="flex items-center gap-2.5">
                            <input type="radio" name="payment_source" value="card" class="text-[#1952E1] focus:ring-0">
                            <span class="font-medium text-slate-700">Pay via Card / Bank (Paystack / Flutterwave)</span>
                        </div>
                        <i class="ph-bold ph-credit-card text-slate-400 text-base"></i>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
            <button type="button" onclick="closeHireModal()" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 rounded-[3px]">
                Cancel
            </button>
            <button type="button" onclick="confirmHireAndDeposit()" class="px-5 py-2.5 text-xs font-bold bg-[#1952E1] hover:bg-blue-700 text-white rounded-[3px] shadow-sm">
                Confirm & Lock Milestone 1 Deposit
            </button>
        </div>

    </div>
</div>

<script>
function openHireModal(name, total, m1, days) {
    document.getElementById('modal-freelancer-name').textContent = name;
    document.getElementById('modal-contract-total').textContent = total;
    document.getElementById('modal-milestone-deposit').textContent = m1;
    document.getElementById('modal-contract-days').textContent = days;
    document.getElementById('hire-modal').classList.remove('hidden');
}

function closeHireModal() {
    document.getElementById('hire-modal').classList.add('hidden');
}

function confirmHireAndDeposit() {
    alert('Milestone 1 escrow deposit locked successfully! Contract CRD-CNT-1042 created.');
    window.location.href = 'contract-details/<?= $slug ?>';
}

// Interactive filter tabs
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.filter-tab').forEach(t => {
            t.classList.remove('active', 'bg-[#1952E1]', 'text-white');
            t.classList.add('bg-slate-100', 'text-slate-600');
        });
        tab.classList.add('active', 'bg-[#1952E1]', 'text-white');
        tab.classList.remove('bg-slate-100', 'text-slate-600');

        const filter = tab.dataset.filter;
        document.querySelectorAll('.proposal-card').forEach(card => {
            if (filter === 'all') {
                card.style.display = 'block';
            } else if (filter === 'shortlisted') {
                card.style.display = card.dataset.shortlisted === 'true' ? 'block' : 'none';
            } else if (filter === 'verified') {
                card.style.display = card.dataset.verified === 'true' ? 'block' : 'none';
            }
        });
    });
});
</script>

<!-- Main flex container ends -->
</div> 

<?php include __DIR__ . '/components/footer.php'; ?>
