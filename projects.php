<?php
$page_title = "Browse Projects - Scriptly Marketplace";
$page_description = "Explore open client project listings backed by 100% upfront milestone escrow protection on Scriptly.";
$active_page = "projects";
$breadcrumb = [
    'category' => 'Open Marketplace',
    'title' => 'Browse Project Listings',
    'subtitle' => 'Explore funded client project requirements and submit proposals to get hired.',
    'bg_image' => 'assets/breadcrumbs/legal_bg.jpg'
];
include 'includes/header.php';
?>

    <!-- Projects Marketplace Main Container -->
    <main class="max-w-7xl mx-auto px-3 sm:px-6 py-12">
        
        <!-- Search & Filter Controls Bar -->
        <div class="bg-white rounded-[3px] p-6 border border-gray-200/80 shadow-sm mb-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                
                <!-- Search Input -->
                <div class="md:col-span-6 relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" id="project-search" placeholder="Search project titles or skill keywords..." class="w-full pl-10 pr-4 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium">
                </div>

                <!-- Category Selector -->
                <div class="md:col-span-3">
                    <select id="category-filter" class="w-full px-3 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium bg-white">
                        <option value="all">All Categories</option>
                        <option value="development">Software Development</option>
                        <option value="business">Business & Reports</option>
                        <option value="design">Design & UI/UX</option>
                    </select>
                </div>

                <!-- Budget Range Selector -->
                <div class="md:col-span-3">
                    <select id="budget-filter" class="w-full px-3 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium bg-white">
                        <option value="all">All Budget Ranges</option>
                        <option value="under50">Under ₦50,000</option>
                        <option value="50to200">₦50,000 - ₦200,000</option>
                        <option value="over200">Over ₦200,000</option>
                    </select>
                </div>

            </div>
        </div>

        <!-- Project Listings Stack -->
        <div class="space-y-4 mb-16" id="projects-list">
            
            <!-- Project 1 -->
            <div class="project-card bg-white rounded-[3px] p-6 border border-gray-200/80 hover:border-blue-500/50 transition-all duration-300 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6" data-category="development" data-budget="250000">
                <div class="space-y-3 max-w-3xl">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="bg-blue-50 text-blue-600 font-extrabold text-[11px] px-2.5 py-0.5 rounded-[3px] uppercase tracking-wider">Software Development</span>
                        <span class="bg-emerald-50 text-emerald-700 font-extrabold text-[11px] px-2.5 py-0.5 rounded-full flex items-center gap-1">🔒 Escrow Funded</span>
                        <span class="text-xs text-gray-400 font-medium">• Posted 2 hours ago</span>
                    </div>

                    <h3 class="font-serif text-xl font-bold text-brand-dark hover:text-blue-600 transition-colors cursor-pointer">
                        Full-Stack Custom PHP & Tailwind Web Application
                    </h3>
                    
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Looking for a senior PHP developer to build a responsive web marketplace portal with REST API integration, WebSocket real-time chat, and clean MySQL schema.
                    </p>

                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <span class="text-[11px] font-semibold bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-[3px]">PHP 8.2</span>
                        <span class="text-[11px] font-semibold bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-[3px]">MySQL</span>
                        <span class="text-[11px] font-semibold bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-[3px]">Tailwind CSS</span>
                        <span class="text-[11px] font-semibold bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-[3px]">WebSocket</span>
                    </div>
                </div>

                <div class="flex md:flex-col justify-between md:items-end border-t md:border-t-0 pt-4 md:pt-0 border-gray-100 shrink-0 space-y-2">
                    <div class="text-right">
                        <span class="text-[11px] text-gray-400 uppercase font-bold block">Fixed Budget</span>
                        <span class="font-serif text-2xl font-extrabold text-brand-dark">₦250,000</span>
                    </div>
                    <span class="text-xs font-medium text-gray-500 block">12 Proposals Submitted</span>
                    <a href="signup?role=provider" class="inline-block bg-brand-dark text-white text-xs font-bold px-6 py-2.5 rounded-full hover:bg-slate-800 transition-all text-center">
                        Submit Proposal →
                    </a>
                </div>
            </div>

            <!-- Project 2 -->
            <div class="project-card bg-white rounded-[3px] p-6 border border-gray-200/80 hover:border-blue-500/50 transition-all duration-300 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6" data-category="business" data-budget="85000">
                <div class="space-y-3 max-w-3xl">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="bg-purple-50 text-purple-600 font-extrabold text-[11px] px-2.5 py-0.5 rounded-[3px] uppercase tracking-wider">Business & Reports</span>
                        <span class="bg-emerald-50 text-emerald-700 font-extrabold text-[11px] px-2.5 py-0.5 rounded-full flex items-center gap-1">🔒 Escrow Funded</span>
                        <span class="text-xs text-gray-400 font-medium">• Posted 5 hours ago</span>
                    </div>

                    <h3 class="font-serif text-xl font-bold text-brand-dark hover:text-blue-600 transition-colors cursor-pointer">
                        Corporate Feasibility Study & Market Analysis Report
                    </h3>
                    
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Need an experienced business analyst to prepare an executive feasibility study and financial forecasting report for a tech investment presentation.
                    </p>

                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <span class="text-[11px] font-semibold bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-[3px]">Market Analysis</span>
                        <span class="text-[11px] font-semibold bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-[3px]">Financial Modeling</span>
                        <span class="text-[11px] font-semibold bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-[3px]">Pitch Deck</span>
                    </div>
                </div>

                <div class="flex md:flex-col justify-between md:items-end border-t md:border-t-0 pt-4 md:pt-0 border-gray-100 shrink-0 space-y-2">
                    <div class="text-right">
                        <span class="text-[11px] text-gray-400 uppercase font-bold block">Fixed Budget</span>
                        <span class="font-serif text-2xl font-extrabold text-brand-dark">₦85,000</span>
                    </div>
                    <span class="text-xs font-medium text-gray-500 block">6 Proposals Submitted</span>
                    <a href="signup?role=provider" class="inline-block bg-brand-dark text-white text-xs font-bold px-6 py-2.5 rounded-full hover:bg-slate-800 transition-all text-center">
                        Submit Proposal →
                    </a>
                </div>
            </div>

            <!-- Project 3 -->
            <div class="project-card bg-white rounded-[3px] p-6 border border-gray-200/80 hover:border-blue-500/50 transition-all duration-300 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6" data-category="design" data-budget="45000">
                <div class="space-y-3 max-w-3xl">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="bg-amber-50 text-amber-600 font-extrabold text-[11px] px-2.5 py-0.5 rounded-[3px] uppercase tracking-wider">Design & UI/UX</span>
                        <span class="bg-emerald-50 text-emerald-700 font-extrabold text-[11px] px-2.5 py-0.5 rounded-full flex items-center gap-1">🔒 Escrow Funded</span>
                        <span class="text-xs text-gray-400 font-medium">• Posted 1 day ago</span>
                    </div>

                    <h3 class="font-serif text-xl font-bold text-brand-dark hover:text-blue-600 transition-colors cursor-pointer">
                        Figma UI/UX Dashboard Prototype Design
                    </h3>
                    
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Design a clean, modern Figma admin dashboard interface with light and dark theme components following Tailwind CSS design tokens.
                    </p>

                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <span class="text-[11px] font-semibold bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-[3px]">Figma</span>
                        <span class="text-[11px] font-semibold bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-[3px]">UI/UX</span>
                        <span class="text-[11px] font-semibold bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-[3px]">Design System</span>
                    </div>
                </div>

                <div class="flex md:flex-col justify-between md:items-end border-t md:border-t-0 pt-4 md:pt-0 border-gray-100 shrink-0 space-y-2">
                    <div class="text-right">
                        <span class="text-[11px] text-gray-400 uppercase font-bold block">Fixed Budget</span>
                        <span class="font-serif text-2xl font-extrabold text-brand-dark">₦45,000</span>
                    </div>
                    <span class="text-xs font-medium text-gray-500 block">18 Proposals Submitted</span>
                    <a href="signup?role=provider" class="inline-block bg-brand-dark text-white text-xs font-bold px-6 py-2.5 rounded-full hover:bg-slate-800 transition-all text-center">
                        Submit Proposal →
                    </a>
                </div>
            </div>

        </div>

    </main>

<?php include 'includes/footer.php'; ?>
