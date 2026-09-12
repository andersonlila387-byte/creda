<?php
$page_title = "About Us - Scriptly Marketplace";
$page_description = "Discover Scriptly's story, founders, and core mission to build the world's most trusted verified professional service marketplace.";
$active_page = "about";
$breadcrumb = [
    'category' => 'Our Story & Leadership',
    'title' => 'About Scriptly',
    'subtitle' => 'Empowering businesses with 100% pre-vetted talent, milestone escrow security, and real-time collaboration.',
    'bg_image' => 'assets/breadcrumbs/legal_bg.jpg'
];
include 'includes/header.php';
?>

    <!-- About Mission Section -->
    <main class="max-w-7xl mx-auto px-3 sm:px-6 py-16">
        
        <!-- Mission Section Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center mb-24">
            <div class="lg:col-span-6 space-y-6">
                <span class="text-xs font-extrabold uppercase tracking-widest text-blue-600 bg-blue-50 px-3 py-1 rounded-[3px]">Our Core Mission</span>
                <h2 class="font-serif text-3xl sm:text-5xl font-bold text-brand-dark tracking-tight leading-tight">
                    Eliminating quality guesswork from digital freelancing.
                </h2>
                <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                    Scriptly was founded on a simple premise: traditional online marketplaces prioritize quantity over quality, leaving clients to navigate unverified portfolios and payment insecurity.
                </p>
                <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                    We redefined the freelance ecosystem by instituting mandatory technical skill assessment challenges before any service provider can bid. Paired with upfront milestone escrow funding, Scriptly ensures clients receive exceptional work and providers get paid reliably.
                </p>
            </div>

            <div class="lg:col-span-6 relative">
                <div class="rounded-[3px] overflow-hidden border border-gray-200/80 shadow-xl relative group">
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=1000" alt="Scriptly Team Collaboration" class="w-full h-[400px] object-cover group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0A2342]/80 via-transparent to-transparent"></div>
                    <div class="absolute bottom-6 left-6 right-6 text-white">
                        <span class="bg-[#ffda79] text-brand-dark font-extrabold text-xs px-3 py-1 rounded-full uppercase">Trust & Transparency</span>
                        <h3 class="font-serif text-2xl font-bold mt-2">Built for high-stakes projects</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Founders & Leadership Section -->
        <div class="mb-24">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-extrabold uppercase tracking-widest text-blue-600 bg-blue-50 px-3 py-1 rounded-[3px]">Founding Team</span>
                <h2 class="font-serif text-3xl sm:text-5xl font-bold text-brand-dark mt-3 tracking-tight">Meet the Founders</h2>
                <p class="text-gray-600 text-sm sm:text-base mt-2">The visionaries and software engineers driving Scriptly's verified marketplace engine.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                
                <!-- Founder 1: Adebusuyi Olorunjunilo -->
                <div class="h-[480px] rounded-[3px] relative overflow-hidden bg-gray-900 group/card transition-all duration-300 border border-white/10 hover:border-blue-400/50 hover:scale-[1.02] shadow-xl">
                    <!-- Background Portrait Photo -->
                    <img src="assets/founders/adebusuyi.jpg" alt="Adebusuyi Olorunjunilo - Founder & Lead Product Architect" class="w-full h-full object-cover object-top group-hover/card:scale-105 transition-transform duration-700 ease-out">
                    <!-- Gradient Overlay for Contrast & Readability -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/50 to-transparent opacity-90 group-hover/card:opacity-95 transition-opacity"></div>
                    
                    <!-- Top Badge -->
                    <div class="absolute top-5 left-5 right-5 flex justify-between items-center z-10">
                        <span class="bg-[#ffda79] text-brand-dark font-extrabold text-xs px-3.5 py-1 rounded-full uppercase tracking-wider shadow">Founder & Visionary</span>
                        <span class="bg-blue-600/90 text-white font-bold text-xs px-3 py-1 rounded-full backdrop-blur-md">Lead Architect</span>
                    </div>

                    <!-- Bottom Text Content Overlay -->
                    <div class="absolute bottom-6 left-6 right-6 text-white text-left z-10">
                        <h3 class="font-serif text-2xl sm:text-3xl font-bold text-white mb-1">Adebusuyi Olorunjunilo</h3>
                        <p class="text-xs text-blue-300 font-extrabold uppercase tracking-wider mb-3">Founder & Lead Product Architect</p>
                        <p class="text-xs text-gray-300 leading-relaxed max-w-md font-medium">
                            Conceived the core vision for Scriptly and leads product architecture and full-stack engineering, driving our mission to eliminate freelance quality guesswork and protect client investments.
                        </p>
                    </div>
                </div>

                <!-- Founder 2: Olagundoye Joseph -->
                <div class="h-[480px] rounded-[3px] relative overflow-hidden bg-gray-900 group/card transition-all duration-300 border border-white/10 hover:border-emerald-400/50 hover:scale-[1.02] shadow-xl">
                    <!-- Background Portrait Photo -->
                    <img src="assets/founders/joseph.jpg" alt="Olagundoye Joseph - Co-Founder & Chief Technology Officer" class="w-full h-full object-cover object-top group-hover/card:scale-105 transition-transform duration-700 ease-out">
                    <!-- Gradient Overlay for Contrast & Readability -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/50 to-transparent opacity-90 group-hover/card:opacity-95 transition-opacity"></div>
                    
                    <!-- Top Badge -->
                    <div class="absolute top-5 left-5 right-5 flex justify-between items-center z-10">
                        <span class="bg-emerald-400 text-gray-900 font-extrabold text-xs px-3.5 py-1 rounded-full uppercase tracking-wider shadow">Co-Founder & CTO</span>
                        <span class="bg-slate-800/90 text-white font-bold text-xs px-3 py-1 rounded-full backdrop-blur-md">Lead Engineer</span>
                    </div>

                    <!-- Bottom Text Content Overlay -->
                    <div class="absolute bottom-6 left-6 right-6 text-white text-left z-10">
                        <h3 class="font-serif text-2xl sm:text-3xl font-bold text-white mb-1">Olagundoye Joseph</h3>
                        <p class="text-xs text-emerald-300 font-extrabold uppercase tracking-wider mb-3">Co-Founder & Chief Technology Officer</p>
                        <p class="text-xs text-gray-300 leading-relaxed max-w-md font-medium">
                            Architects backend infrastructure, database migration frameworks, and real-time communication systems powering Scriptly's high-concurrency verified service engine.
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <!-- 3 Core Pillars Section -->
        <div class="mb-24">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-extrabold uppercase tracking-widest text-blue-600">The Scriptly Advantage</span>
                <h2 class="font-serif text-3xl sm:text-5xl font-bold text-brand-dark mt-2 tracking-tight">Our 3 Core Pillars</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Pillar 1 -->
                <div class="bg-white rounded-[3px] p-8 border border-gray-200/80 hover:border-blue-500/50 transition-all duration-300">
                    <div class="w-12 h-12 rounded-[3px] bg-blue-50 text-blue-600 flex items-center justify-center mb-6 font-bold text-xl">
                        ✓
                    </div>
                    <h3 class="font-bold text-xl text-brand-dark mb-3">1. Mandatory Skill Verification</h3>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Service providers must complete timed domain assessment tests. Only candidates passing rigorous scoring thresholds receive the official Verified Pro badge.
                    </p>
                </div>

                <!-- Pillar 2 -->
                <div class="bg-white rounded-[3px] p-8 border border-gray-200/80 hover:border-emerald-500/50 transition-all duration-300">
                    <div class="w-12 h-12 rounded-[3px] bg-emerald-50 text-emerald-600 flex items-center justify-center mb-6 font-bold text-xl">
                        🔒
                    </div>
                    <h3 class="font-bold text-xl text-brand-dark mb-3">2. 100% Upfront Milestone Escrow</h3>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Milestone funds are deposited securely into escrow before work begins. Payments are released only when clients inspect and approve finished deliverables.
                    </p>
                </div>

                <!-- Pillar 3 -->
                <div class="bg-white rounded-[3px] p-8 border border-gray-200/80 hover:border-purple-500/50 transition-all duration-300">
                    <div class="w-12 h-12 rounded-[3px] bg-purple-50 text-purple-600 flex items-center justify-center mb-6 font-bold text-xl">
                        ⚡
                    </div>
                    <h3 class="font-bold text-xl text-brand-dark mb-3">3. Real-Time Communication & Messaging</h3>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Built for seamless direct collaboration, clients and providers communicate in real-time with live typing indicators, delivery receipts, and instant file sharing.
                    </p>
                </div>

            </div>
        </div>

        <!-- Platform Guarantees Banner -->
        <div class="bg-[#0A2342] text-white rounded-[3px] p-10 sm:p-14 mb-24">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="p-4">
                    <div class="text-blue-400 font-extrabold text-lg mb-2">100% Pre-Assessed Talent</div>
                    <p class="text-xs text-gray-300 leading-relaxed">Every provider undergoes mandatory category technical testing before bidding.</p>
                </div>
                <div class="p-4 border-y md:border-y-0 md:border-x border-white/10">
                    <div class="text-emerald-400 font-extrabold text-lg mb-2">Milestone Escrow Protection</div>
                    <p class="text-xs text-gray-300 leading-relaxed">Funds remain locked safely until deliverables meet contract specifications.</p>
                </div>
                <div class="p-4">
                    <div class="text-amber-300 font-extrabold text-lg mb-2">Audit Desk Resolution</div>
                    <p class="text-xs text-gray-300 leading-relaxed">Dedicated admin auditors resolve contract issues fairly and transparently.</p>
                </div>
            </div>
        </div>

        <!-- Call To Action -->
        <div class="text-center max-w-3xl mx-auto">
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-brand-dark mb-4">Ready to experience verified quality?</h2>
            <p class="text-gray-600 text-base mb-8">Join clients and pre-vetted professionals working on Scriptly today.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="signup?role=client" class="w-full sm:w-60 bg-brand-dark text-white font-bold text-sm py-4 rounded-full hover:bg-slate-800 transition-all">
                    Request Service →
                </a>
                <a href="signup?role=provider" class="w-full sm:w-60 bg-white text-brand-dark border border-gray-300 font-bold text-sm py-4 rounded-full hover:bg-gray-50 transition-all">
                    Become a Professional →
                </a>
            </div>
        </div>

    </main>

<?php include 'includes/footer.php'; ?>
