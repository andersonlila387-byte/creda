<?php
$page_title = "Verified Professionals Directory - Scriptly Marketplace";
$page_description = "Browse pre-assessed, verified professionals with validated technical assessment scores and proven project delivery histories.";
$active_page = "professionals";
$breadcrumb = [
    'category' => 'Pre-Assessed Talent Directory',
    'title' => 'Verified Service Professionals',
    'subtitle' => 'Connect directly with pre-vetted talent holding verified assessment test badges.',
    'bg_image' => 'assets/breadcrumbs/support_bg.jpg'
];
include 'includes/header.php';
?>

    <!-- Verified Talent Main Container -->
    <main class="max-w-7xl mx-auto px-3 sm:px-6 py-12">
        
        <!-- Search & Category Filter Toolbar -->
        <div class="bg-white rounded-[3px] p-6 border border-gray-200/80 shadow-sm mb-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                
                <div class="md:col-span-6 relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" id="pro-search" placeholder="Search professional name or skill domain..." class="w-full pl-10 pr-4 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium">
                </div>

                <div class="md:col-span-3">
                    <select id="domain-filter" class="w-full px-3 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium bg-white">
                        <option value="all">All Verified Domains</option>
                        <option value="software">Software Engineering</option>
                        <option value="business">Business & Financial Reporting</option>
                        <option value="data">Data Analytics</option>
                        <option value="design">UI/UX & Branding</option>
                    </select>
                </div>

                <div class="md:col-span-3">
                    <select id="score-filter" class="w-full px-3 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium bg-white">
                        <option value="all">All Assessment Scores</option>
                        <option value="90">90%+ Top Tier Score</option>
                        <option value="80">80%+ Verified Passing</option>
                    </select>
                </div>

            </div>
        </div>

        <!-- Verified Talent Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-16" id="professionals-grid">
            
            <!-- Talent 1 -->
            <div class="h-[390px] rounded-[3px] relative overflow-hidden bg-gray-900 group/card transition-all duration-300 border border-white/10 hover:border-blue-400/50 hover:scale-[1.02] shadow-lg">
                <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=600" alt="Bessie Cooper" class="w-full h-full object-cover group-hover/card:scale-105 transition-transform duration-700 ease-out">
                <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/40 to-transparent opacity-90 group-hover/card:opacity-95 transition-opacity"></div>
                
                <div class="absolute top-4 left-4 right-4 flex justify-between items-center z-10">
                    <span class="bg-amber-400 text-gray-900 font-extrabold text-[11px] px-2.5 py-0.5 rounded-full flex items-center gap-1">★ 4.95</span>
                    <span class="bg-blue-600/90 text-white font-bold text-[11px] px-2.5 py-0.5 rounded-full flex items-center gap-1 backdrop-blur-md">✓ Verified Pro</span>
                </div>

                <div class="absolute bottom-5 left-5 right-5 text-white text-left z-10">
                    <div class="flex items-center justify-between mb-0.5">
                        <h3 class="font-bold text-base text-white">Bessie Cooper</h3>
                        <span class="bg-emerald-500/90 text-white font-extrabold text-[11px] px-2 py-0.5 rounded-full">₦85,000/hr</span>
                    </div>
                    <p class="text-[11px] text-blue-200 font-medium mb-1">Senior Full Stack Engineer</p>
                    <div class="flex items-center justify-between text-[10px] text-gray-300 font-medium mb-2.5">
                        <span>48 Projects Completed</span>
                        <span class="text-amber-300 font-bold">Score: 96/100</span>
                    </div>
                    <div class="flex flex-wrap gap-1 text-[10px] font-medium mb-3">
                        <span class="glass-tag rounded-full px-2.5 py-0.5 text-white">PHP</span>
                        <span class="glass-tag rounded-full px-2.5 py-0.5 text-white">MySQL</span>
                        <span class="glass-tag rounded-full px-2.5 py-0.5 text-white">Go</span>
                    </div>
                    <a href="signup?role=client" class="block w-full text-center bg-blue-600 text-white font-bold text-xs py-2 rounded-full hover:bg-blue-700 transition-colors">
                        Request Service →
                    </a>
                </div>
            </div>

            <!-- Talent 2 -->
            <div class="h-[390px] rounded-[3px] relative overflow-hidden bg-gray-900 group/card transition-all duration-300 border border-white/10 hover:border-blue-400/50 hover:scale-[1.02] shadow-lg">
                <img src="https://images.unsplash.com/photo-1614283233556-f35b0c801ef1?auto=format&fit=crop&q=80&w=600" alt="Savannah Nguyen" class="w-full h-full object-cover group-hover/card:scale-105 transition-transform duration-700 ease-out">
                <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/40 to-transparent opacity-90 group-hover/card:opacity-95 transition-opacity"></div>
                
                <div class="absolute top-4 left-4 right-4 flex justify-between items-center z-10">
                    <span class="bg-amber-400 text-gray-900 font-extrabold text-[11px] px-2.5 py-0.5 rounded-full flex items-center gap-1">★ 5.0</span>
                    <span class="bg-blue-600/90 text-white font-bold text-[11px] px-2.5 py-0.5 rounded-full flex items-center gap-1 backdrop-blur-md">✓ Verified Pro</span>
                </div>

                <div class="absolute bottom-5 left-5 right-5 text-white text-left z-10">
                    <div class="flex items-center justify-between mb-0.5">
                        <h3 class="font-bold text-base text-white">Savannah Nguyen</h3>
                        <span class="bg-emerald-500/90 text-white font-extrabold text-[11px] px-2 py-0.5 rounded-full">₦115,000/hr</span>
                    </div>
                    <p class="text-[11px] text-blue-200 font-medium mb-1">Lead System Architect</p>
                    <div class="flex items-center justify-between text-[10px] text-gray-300 font-medium mb-2.5">
                        <span>62 Projects Completed</span>
                        <span class="text-amber-300 font-bold">Score: 98/100</span>
                    </div>
                    <div class="flex flex-wrap gap-1 text-[10px] font-medium mb-3">
                        <span class="glass-tag rounded-full px-2.5 py-0.5 text-white">System Architecture</span>
                        <span class="glass-tag rounded-full px-2.5 py-0.5 text-white">Docker</span>
                    </div>
                    <a href="signup?role=client" class="block w-full text-center bg-blue-600 text-white font-bold text-xs py-2 rounded-full hover:bg-blue-700 transition-colors">
                        Request Service →
                    </a>
                </div>
            </div>

            <!-- Talent 3 -->
            <div class="h-[390px] rounded-[3px] relative overflow-hidden bg-gray-900 group/card transition-all duration-300 border border-white/10 hover:border-blue-400/50 hover:scale-[1.02] shadow-lg">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=600" alt="Courtney Henry" class="w-full h-full object-cover group-hover/card:scale-105 transition-transform duration-700 ease-out">
                <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/40 to-transparent opacity-90 group-hover/card:opacity-95 transition-opacity"></div>
                
                <div class="absolute top-4 left-4 right-4 flex justify-between items-center z-10">
                    <span class="bg-amber-400 text-gray-900 font-extrabold text-[11px] px-2.5 py-0.5 rounded-full flex items-center gap-1">★ 4.90</span>
                    <span class="bg-blue-600/90 text-white font-bold text-[11px] px-2.5 py-0.5 rounded-full flex items-center gap-1 backdrop-blur-md">✓ Verified Pro</span>
                </div>

                <div class="absolute bottom-5 left-5 right-5 text-white text-left z-10">
                    <div class="flex items-center justify-between mb-0.5">
                        <h3 class="font-bold text-base text-white">Courtney Henry</h3>
                        <span class="bg-emerald-500/90 text-white font-extrabold text-[11px] px-2 py-0.5 rounded-full">₦65,000/hr</span>
                    </div>
                    <p class="text-[11px] text-blue-200 font-medium mb-1">Corporate Report Specialist</p>
                    <div class="flex items-center justify-between text-[10px] text-gray-300 font-medium mb-2.5">
                        <span>34 Projects Completed</span>
                        <span class="text-amber-300 font-bold">Score: 92/100</span>
                    </div>
                    <div class="flex flex-wrap gap-1 text-[10px] font-medium mb-3">
                        <span class="glass-tag rounded-full px-2.5 py-0.5 text-white">Business Reports</span>
                        <span class="glass-tag rounded-full px-2.5 py-0.5 text-white">Grant Writing</span>
                    </div>
                    <a href="signup?role=client" class="block w-full text-center bg-blue-600 text-white font-bold text-xs py-2 rounded-full hover:bg-blue-700 transition-colors">
                        Request Service →
                    </a>
                </div>
            </div>

            <!-- Talent 4 -->
            <div class="h-[390px] rounded-[3px] relative overflow-hidden bg-gray-900 group/card transition-all duration-300 border border-white/10 hover:border-blue-400/50 hover:scale-[1.02] shadow-lg">
                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=600" alt="Arlene McCoy" class="w-full h-full object-cover group-hover/card:scale-105 transition-transform duration-700 ease-out">
                <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/40 to-transparent opacity-90 group-hover/card:opacity-95 transition-opacity"></div>
                
                <div class="absolute top-4 left-4 right-4 flex justify-between items-center z-10">
                    <span class="bg-amber-400 text-gray-900 font-extrabold text-[11px] px-2.5 py-0.5 rounded-full flex items-center gap-1">★ 4.98</span>
                    <span class="bg-blue-600/90 text-white font-bold text-[11px] px-2.5 py-0.5 rounded-full flex items-center gap-1 backdrop-blur-md">✓ Verified Pro</span>
                </div>

                <div class="absolute bottom-5 left-5 right-5 text-white text-left z-10">
                    <div class="flex items-center justify-between mb-0.5">
                        <h3 class="font-bold text-base text-white">Arlene McCoy</h3>
                        <span class="bg-emerald-500/90 text-white font-extrabold text-[11px] px-2 py-0.5 rounded-full">₦95,000/hr</span>
                    </div>
                    <p class="text-[11px] text-blue-200 font-medium mb-1">Lead Power BI & Data Analyst</p>
                    <div class="flex items-center justify-between text-[10px] text-gray-300 font-medium mb-2.5">
                        <span>52 Projects Completed</span>
                        <span class="text-amber-300 font-bold">Score: 95/100</span>
                    </div>
                    <div class="flex flex-wrap gap-1 text-[10px] font-medium mb-3">
                        <span class="glass-tag rounded-full px-2.5 py-0.5 text-white">Power BI</span>
                        <span class="glass-tag rounded-full px-2.5 py-0.5 text-white">Python Data</span>
                    </div>
                    <a href="signup?role=client" class="block w-full text-center bg-blue-600 text-white font-bold text-xs py-2 rounded-full hover:bg-blue-700 transition-colors">
                        Request Service →
                    </a>
                </div>
            </div>

        </div>

    </main>

<?php include 'includes/footer.php'; ?>
