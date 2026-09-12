<?php
$page_title = "Scriptly - Verified Service Marketplace & Project Management Platform";
$page_description = "Scriptly connects clients with pre-assessed, verified professionals. Milestone escrow protection, real-time collaboration, and quality guarantees.";
$active_page = "home";
include 'includes/header.php';
?>

    <!-- Clean, Modern Hero Section -->
    <header class="relative w-full flex items-center pt-32 pb-24 overflow-hidden min-h-[80vh]">
        <!-- Full Bleed Background Image Asset -->
        <div class="absolute inset-0 z-0 pointer-events-none">
            <img src="assets/hero_bg.jpg" alt="Hero Background" class="w-full h-full object-cover object-center">
        </div>
        
        <!-- Subtle dark overlay for text readability -->
        <div class="absolute inset-0 z-0 bg-gray-900/60 pointer-events-none"></div>

        <div class="relative z-10 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center flex flex-col items-center">
                <!-- Glowing Pill Badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 mb-6 shadow-sm">
                    <span class="flex h-2 w-2 rounded-full bg-green-400"></span>
                    <span class="text-xs font-semibold text-white tracking-wide uppercase">The Standard for Outsourcing</span>
                </div>

                <!-- Main Heading -->
                <h1 class="font-serif text-4xl sm:text-5xl lg:text-[56px] leading-[1.15] text-white font-bold tracking-tight drop-shadow-md mb-6">
                    Hire Talent with <br>
                    <span class="text-blue-400">Absolute Certainty</span>.
                </h1>

                <!-- Subheading -->
                <p class="text-base sm:text-lg text-white/90 font-medium leading-relaxed drop-shadow-sm mb-8 max-w-xl mx-auto">
                    Connect with pre-assessed professionals. Track milestones, collaborate, and only release payments when satisfied.
                </p>

                <!-- CTA Buttons with Icons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full">
                    <a href="signup?role=client" class="w-full sm:w-auto flex items-center justify-center gap-2.5 bg-[#1952E1] hover:bg-blue-600 text-white font-semibold text-sm sm:text-base py-3 px-7 rounded-full transition-all duration-200 shadow-md group">
                        <span>Post a Project</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    
                    <a href="signup?role=provider" class="w-full sm:w-auto flex items-center justify-center gap-2.5 bg-white text-[#0A2342] border border-white/20 font-bold text-sm sm:text-base py-3 px-7 rounded-full hover:bg-gray-100 transition-all duration-200 group shadow-md">
                        <svg class="w-4 h-4 text-[#1952E1] group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span>Join as Talent</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Sleek Dark Trust & Verification Marquee Ribbon -->
    <section class="w-full bg-[#0A2342] text-white py-5 md:py-6 overflow-hidden mb-10 border-y border-white/10 relative">
        <div class="animate-marquee flex items-center gap-10 sm:gap-14 whitespace-nowrap text-sm md:text-base font-bold">
            
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <span>Verified Professionals</span>
                <span class="text-xs font-normal text-blue-200/90 bg-white/10 px-3 py-1 rounded-[3px] ml-1 border border-white/10">Pre-Assessed</span>
            </div>
            <span class="text-white/20 font-bold">✦</span>
            
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span>Secure Escrow Payments</span>
                <span class="text-xs font-normal text-blue-200/90 bg-white/10 px-3 py-1 rounded-[3px] ml-1 border border-white/10">Milestone Protected</span>
            </div>
            <span class="text-white/20 font-bold">✦</span>
            
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l9-4 9 4M3 6v14a2 2 0 002 2h14a2 2 0 002-2V6M3 6l9 6 9-6"></path></svg>
                <span>Protected Projects & Disputes</span>
                <span class="text-xs font-normal text-blue-200/90 bg-white/10 px-3 py-1 rounded-[3px] ml-1 border border-white/10">Audited Workflow</span>
            </div>
            <span class="text-white/20 font-bold">✦</span>
            
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span>Real-Time Messaging & Collaboration</span>
                <span class="text-xs font-normal text-blue-200/90 bg-white/10 px-3 py-1 rounded-[3px] ml-1 border border-white/10">Instant Chat</span>
            </div>
            <span class="text-white/20 font-bold">✦</span>

        </div>
    </section>

    <!-- 3. Popular Categories Showcase Grid (Fiverr-Style Service Cards with Custom Generated Images) -->
    <section id="services" class="max-w-7xl mx-auto px-3 sm:px-6 my-20">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-block px-3.5 py-1 bg-blue-50 border border-blue-200/80 text-[#1952E1] text-xs font-black rounded-full uppercase tracking-wider">
                Popular Categories
            </span>
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-brand-dark mt-3 tracking-tight">
                Explore Popular Services
            </h2>
            <p class="text-slate-600 text-sm sm:text-base mt-2 mb-4">
                Browse pre-assessed freelance services and student project solutions.
            </p>
            <a href="services" class="inline-flex items-center text-sm font-bold text-brand-dark hover:text-[#1952E1] transition-colors group">
                <span>Browse All Categories</span>
                <svg class="w-4 h-4 ml-1.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
            
            <!-- Fiverr-Style Card 1: Software Development -->
            <a href="services" class="bg-white rounded-[3px] border border-gray-200 overflow-hidden hover:border-[#1952E1] transition-all duration-300 group flex flex-col justify-between block">
                <div>
                    <!-- Image Banner -->
                    <div class="h-32 sm:h-52 w-full overflow-hidden relative bg-slate-100">
                        <img src="assets/services/software_dev.jpg" alt="Software Development" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <span class="absolute top-2 left-2 sm:top-3 sm:left-3 bg-[#0A2342] text-white text-[9px] sm:text-[11px] font-bold px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-[3px]">
                            Software Dev
                        </span>
                    </div>
                    <!-- Body Content -->
                    <div class="p-3 sm:p-6">
                        <div class="flex items-center gap-1 text-amber-500 text-[10px] sm:text-xs font-bold mb-1 sm:mb-2">
                            <span>★ 4.96</span>
                            <span class="text-slate-400 font-normal hidden sm:inline">(520+)</span>
                        </div>
                        <h3 class="font-bold text-sm sm:text-lg text-brand-dark group-hover:text-[#1952E1] transition-colors mb-1 sm:mb-2 leading-snug line-clamp-2">
                            Full-Stack Web Systems
                        </h3>
                        <p class="text-[10px] sm:text-xs text-slate-600 leading-relaxed mb-3 sm:mb-4 hidden sm:block">
                            PHP MVC portals, REST APIs, Laravel, React, database architectures & school management solutions.
                        </p>
                    </div>
                </div>
                <!-- Card Footer Pricing -->
                <div class="px-3 sm:px-6 py-2 sm:py-4 border-t border-gray-100 flex items-center justify-between bg-slate-50/50">
                    <span class="text-[9px] sm:text-[11px] font-medium text-slate-500 uppercase tracking-wider">Starts at</span>
                    <span class="text-xs sm:text-base font-extrabold text-brand-dark">₦50k</span>
                </div>
            </a>

            <!-- Fiverr-Style Card 2: Graphic & UI/UX Design -->
            <a href="services" class="bg-white rounded-[3px] border border-gray-200 overflow-hidden hover:border-[#1952E1] transition-all duration-300 group flex flex-col justify-between block">
                <div>
                    <!-- Image Banner -->
                    <div class="h-32 sm:h-52 w-full overflow-hidden relative bg-slate-100">
                        <img src="assets/services/uiux_design.jpg" alt="Graphic Design" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <span class="absolute top-2 left-2 sm:top-3 sm:left-3 bg-[#0A2342] text-white text-[9px] sm:text-[11px] font-bold px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-[3px]">
                            UI/UX Design
                        </span>
                    </div>
                    <!-- Body Content -->
                    <div class="p-3 sm:p-6">
                        <div class="flex items-center gap-1 text-amber-500 text-[10px] sm:text-xs font-bold mb-1 sm:mb-2">
                            <span>★ 4.92</span>
                            <span class="text-slate-400 font-normal hidden sm:inline">(340+)</span>
                        </div>
                        <h3 class="font-bold text-sm sm:text-lg text-brand-dark group-hover:text-[#1952E1] transition-colors mb-1 sm:mb-2 leading-snug line-clamp-2">
                            UI/UX & Brand Identity
                        </h3>
                        <p class="text-[10px] sm:text-xs text-slate-600 leading-relaxed mb-3 sm:mb-4 hidden sm:block">
                            Figma mockups, user flow designs, logos, typography systems, vector illustrations & presentations.
                        </p>
                    </div>
                </div>
                <!-- Card Footer Pricing -->
                <div class="px-3 sm:px-6 py-2 sm:py-4 border-t border-gray-100 flex items-center justify-between bg-slate-50/50">
                    <span class="text-[9px] sm:text-[11px] font-medium text-slate-500 uppercase tracking-wider">Starts at</span>
                    <span class="text-xs sm:text-base font-extrabold text-brand-dark">₦25k</span>
                </div>
            </a>

            <!-- Fiverr-Style Card 3: Report & Research Writing -->
            <a href="services" class="bg-white rounded-[3px] border border-gray-200 overflow-hidden hover:border-[#1952E1] transition-all duration-300 group flex flex-col justify-between block">
                <div>
                    <!-- Image Banner -->
                    <div class="h-32 sm:h-52 w-full overflow-hidden relative bg-slate-100">
                        <img src="assets/services/report_writing.jpg" alt="Report Writing" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <span class="absolute top-2 left-2 sm:top-3 sm:left-3 bg-[#0A2342] text-white text-[9px] sm:text-[11px] font-bold px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-[3px]">
                            Academic
                        </span>
                    </div>
                    <!-- Body Content -->
                    <div class="p-3 sm:p-6">
                        <div class="flex items-center gap-1 text-amber-500 text-[10px] sm:text-xs font-bold mb-1 sm:mb-2">
                            <span>★ 4.98</span>
                            <span class="text-slate-400 font-normal hidden sm:inline">(680+)</span>
                        </div>
                        <h3 class="font-bold text-sm sm:text-lg text-brand-dark group-hover:text-[#1952E1] transition-colors mb-1 sm:mb-2 leading-snug line-clamp-2">
                            Technical Reports
                        </h3>
                        <p class="text-[10px] sm:text-xs text-slate-600 leading-relaxed mb-3 sm:mb-4 hidden sm:block">
                            Business proposals, technical documentation, SPSS data analysis, chapter formatting & Turnitin reviews.
                        </p>
                    </div>
                </div>
                <!-- Card Footer Pricing -->
                <div class="px-3 sm:px-6 py-2 sm:py-4 border-t border-gray-100 flex items-center justify-between bg-slate-50/50">
                    <span class="text-[9px] sm:text-[11px] font-medium text-slate-500 uppercase tracking-wider">Starts at</span>
                    <span class="text-xs sm:text-base font-extrabold text-brand-dark">₦35k</span>
                </div>
            </a>
            
            <!-- Fiverr-Style Card 4: Data Analysis -->
            <a href="services" class="bg-white rounded-[3px] border border-gray-200 overflow-hidden hover:border-[#1952E1] transition-all duration-300 group flex flex-col justify-between block">
                <div>
                    <!-- Image Banner -->
                    <div class="h-32 sm:h-52 w-full overflow-hidden relative bg-slate-100">
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=400" alt="Data Analysis" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <span class="absolute top-2 left-2 sm:top-3 sm:left-3 bg-[#0A2342] text-white text-[9px] sm:text-[11px] font-bold px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-[3px]">
                            Data Science
                        </span>
                    </div>
                    <!-- Body Content -->
                    <div class="p-3 sm:p-6">
                        <div class="flex items-center gap-1 text-amber-500 text-[10px] sm:text-xs font-bold mb-1 sm:mb-2">
                            <span>★ 4.95</span>
                            <span class="text-slate-400 font-normal hidden sm:inline">(210+)</span>
                        </div>
                        <h3 class="font-bold text-sm sm:text-lg text-brand-dark group-hover:text-[#1952E1] transition-colors mb-1 sm:mb-2 leading-snug line-clamp-2">
                            Data Analysis & Vis
                        </h3>
                        <p class="text-[10px] sm:text-xs text-slate-600 leading-relaxed mb-3 sm:mb-4 hidden sm:block">
                            Python, R, Tableau dashboards, predictive modeling, and statistical surveys for businesses.
                        </p>
                    </div>
                </div>
                <!-- Card Footer Pricing -->
                <div class="px-3 sm:px-6 py-2 sm:py-4 border-t border-gray-100 flex items-center justify-between bg-slate-50/50">
                    <span class="text-[9px] sm:text-[11px] font-medium text-slate-500 uppercase tracking-wider">Starts at</span>
                    <span class="text-xs sm:text-base font-extrabold text-brand-dark">₦40k</span>
                </div>
            </a>

            <!-- Fiverr-Style Card 5: Mobile App Dev -->
            <a href="services" class="bg-white rounded-[3px] border border-gray-200 overflow-hidden hover:border-[#1952E1] transition-all duration-300 group flex flex-col justify-between block">
                <div>
                    <!-- Image Banner -->
                    <div class="h-32 sm:h-52 w-full overflow-hidden relative bg-slate-100">
                        <img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&q=80&w=400" alt="Mobile Apps" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <span class="absolute top-2 left-2 sm:top-3 sm:left-3 bg-[#0A2342] text-white text-[9px] sm:text-[11px] font-bold px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-[3px]">
                            Mobile Dev
                        </span>
                    </div>
                    <!-- Body Content -->
                    <div class="p-3 sm:p-6">
                        <div class="flex items-center gap-1 text-amber-500 text-[10px] sm:text-xs font-bold mb-1 sm:mb-2">
                            <span>★ 4.97</span>
                            <span class="text-slate-400 font-normal hidden sm:inline">(430+)</span>
                        </div>
                        <h3 class="font-bold text-sm sm:text-lg text-brand-dark group-hover:text-[#1952E1] transition-colors mb-1 sm:mb-2 leading-snug line-clamp-2">
                            iOS & Android Apps
                        </h3>
                        <p class="text-[10px] sm:text-xs text-slate-600 leading-relaxed mb-3 sm:mb-4 hidden sm:block">
                            Flutter, React Native, and Swift native applications with API integration and admin panels.
                        </p>
                    </div>
                </div>
                <!-- Card Footer Pricing -->
                <div class="px-3 sm:px-6 py-2 sm:py-4 border-t border-gray-100 flex items-center justify-between bg-slate-50/50">
                    <span class="text-[9px] sm:text-[11px] font-medium text-slate-500 uppercase tracking-wider">Starts at</span>
                    <span class="text-xs sm:text-base font-extrabold text-brand-dark">₦80k</span>
                </div>
            </a>

            <!-- Fiverr-Style Card 6: Digital Marketing -->
            <a href="services" class="bg-white rounded-[3px] border border-gray-200 overflow-hidden hover:border-[#1952E1] transition-all duration-300 group flex flex-col justify-between block">
                <div>
                    <!-- Image Banner -->
                    <div class="h-32 sm:h-52 w-full overflow-hidden relative bg-slate-100">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=400" alt="Marketing" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <span class="absolute top-2 left-2 sm:top-3 sm:left-3 bg-[#0A2342] text-white text-[9px] sm:text-[11px] font-bold px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-[3px]">
                            Marketing
                        </span>
                    </div>
                    <!-- Body Content -->
                    <div class="p-3 sm:p-6">
                        <div class="flex items-center gap-1 text-amber-500 text-[10px] sm:text-xs font-bold mb-1 sm:mb-2">
                            <span>★ 4.89</span>
                            <span class="text-slate-400 font-normal hidden sm:inline">(310+)</span>
                        </div>
                        <h3 class="font-bold text-sm sm:text-lg text-brand-dark group-hover:text-[#1952E1] transition-colors mb-1 sm:mb-2 leading-snug line-clamp-2">
                            SEO & Social Media
                        </h3>
                        <p class="text-[10px] sm:text-xs text-slate-600 leading-relaxed mb-3 sm:mb-4 hidden sm:block">
                            Google Ads, Facebook campaigns, SEO optimization, and content strategy for rapid growth.
                        </p>
                    </div>
                </div>
                <!-- Card Footer Pricing -->
                <div class="px-3 sm:px-6 py-2 sm:py-4 border-t border-gray-100 flex items-center justify-between bg-slate-50/50">
                    <span class="text-[9px] sm:text-[11px] font-medium text-slate-500 uppercase tracking-wider">Starts at</span>
                    <span class="text-xs sm:text-base font-extrabold text-brand-dark">₦20k</span>
                </div>
            </a>

        </div>
    </section>

    <!-- 4. Testimonials & Trusted By Section (Placed BEFORE Why Choose Scriptly as requested) -->
    <section class="max-w-7xl mx-auto px-3 sm:px-6 my-24 overflow-hidden">
        
        <!-- Header & Trustpilot-Style Rating Summary -->
        <div class="text-center max-w-3xl mx-auto mb-12 space-y-3">
            <span class="inline-block px-3.5 py-1 bg-blue-50 border border-blue-200/80 text-[#1952E1] text-xs font-black rounded-full uppercase tracking-wider">
                Trusted by
            </span>
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-brand-dark tracking-tight mt-3">
                They succeeded with Scriptly –<br class="hidden sm:block"> now it’s your turn
            </h2>
            
            <!-- Trust Rating Badge -->
            <div class="flex items-center justify-center gap-2 pt-2 text-xs sm:text-sm">
                <span class="font-bold text-brand-dark">Excellent</span>
                <div class="flex items-center gap-0.5">
                    <span class="w-5 h-5 bg-[#00b67a] text-white flex items-center justify-center rounded-[2px] text-xs font-bold">★</span>
                    <span class="w-5 h-5 bg-[#00b67a] text-white flex items-center justify-center rounded-[2px] text-xs font-bold">★</span>
                    <span class="w-5 h-5 bg-[#00b67a] text-white flex items-center justify-center rounded-[2px] text-xs font-bold">★</span>
                    <span class="w-5 h-5 bg-[#00b67a] text-white flex items-center justify-center rounded-[2px] text-xs font-bold">★</span>
                    <span class="w-5 h-5 bg-[#00b67a] text-white flex items-center justify-center rounded-[2px] text-xs font-bold">★</span>
                </div>
                <span class="text-slate-600 font-medium hover:text-[#1952E1] ml-1">
                    <strong class="text-brand-dark">2,480+</strong> reviews
                </span>
            </div>
        </div>

        <!-- Carousel Track with Cards (Marquee) -->
        <div class="relative overflow-hidden group">
            <!-- Marquee Container -->
            <div id="trusted-track" class="animate-marquee flex gap-6 py-4 px-2 cursor-grab active:cursor-grabbing">
                
                <!-- SET 1 (Original 4 Cards) -->
                <!-- Testimonial Card 1 -->
                <div class="w-[310px] sm:w-[360px] md:w-[390px] flex-shrink-0 bg-white rounded-[3px] border border-gray-200 p-7 flex flex-col justify-between hover:border-[#1952E1] transition-all duration-300">
                    <div class="space-y-4">
                        <div class="w-10 h-10 rounded-[3px] bg-blue-50 text-[#1952E1] border border-blue-100 flex items-center justify-center font-bold text-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                        </div>
                        <p class="text-slate-800 text-sm sm:text-base leading-relaxed">
                            “Scriptly’s milestone escrow and pre-assessed developer gave me 100% confidence. Our final year web portal was delivered 3 days ahead of schedule with zero bugs.”
                        </p>
                        <div class="flex flex-wrap gap-1.5 pt-2">
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-[3px]">Full-Stack Web</span>
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-[3px]">Escrow Safe</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-6 border-t border-gray-100 mt-6">
                        <div class="flex items-center gap-3">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=120" alt="Elena" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <h4 class="font-bold text-sm text-brand-dark">Elena Rostova</h4>
                                <p class="text-[11px] text-slate-500">CTO, FinTech Solutions</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-blue-600">Verified Client ✓</span>
                    </div>
                </div>

                <!-- Testimonial Card 2 -->
                <div class="w-[310px] sm:w-[360px] md:w-[390px] flex-shrink-0 bg-white rounded-[3px] border border-gray-200 p-7 flex flex-col justify-between hover:border-[#1952E1] transition-all duration-300">
                    <div class="space-y-4">
                        <div class="w-10 h-10 rounded-[3px] bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center font-bold text-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.37 2.63 14 7l-1.59-1.59a2 2 0 0 0-2.82 0L8 7l9 9 1.59-1.59a2 2 0 0 0 0-2.82L17 10l4.37-4.37a2.12 2.12 0 1 0-3-3Z"/><path d="M9 8c-2 3-4 3.5-7 4l8 8c.5-3 1-5 4-7"/></svg>
                        </div>
                        <p class="text-slate-800 text-sm sm:text-base leading-relaxed">
                            “Thanks to Scriptly’s direct chat and milestone inspection, we managed our UI prototype design easily. Clean Figma vectors delivered on point.”
                        </p>
                        <div class="flex flex-wrap gap-1.5 pt-2">
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-[3px]">UI/UX Figma</span>
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-[3px]">Brand Identity</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-6 border-t border-gray-100 mt-6">
                        <div class="flex items-center gap-3">
                            <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=120" alt="Elise" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <h4 class="font-bold text-sm text-brand-dark">Elise Hernaez</h4>
                                <p class="text-[11px] text-slate-500">Tech Lead & Founder</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-blue-600">Verified Client ✓</span>
                    </div>
                </div>

                <!-- Testimonial Card 3 -->
                <div class="w-[310px] sm:w-[360px] md:w-[390px] flex-shrink-0 bg-white rounded-[3px] border border-gray-200 p-7 flex flex-col justify-between hover:border-[#1952E1] transition-all duration-300">
                    <div class="space-y-4">
                        <div class="w-10 h-10 rounded-[3px] bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center font-bold text-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <p class="text-slate-800 text-sm sm:text-base leading-relaxed">
                            “The milestone escrow system is the best I've used. I never worry about delayed payments. Passing the assessment immediately brought client requests.”
                        </p>
                        <div class="flex flex-wrap gap-1.5 pt-2">
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-[3px]">Pre-Assessed</span>
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-[3px]">Instant Payout</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-6 border-t border-gray-100 mt-6">
                        <div class="flex items-center gap-3">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=120" alt="Marcus" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <h4 class="font-bold text-sm text-brand-dark">Marcus Vance</h4>
                                <p class="text-[11px] text-slate-500">System Architect • Top Pro</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-emerald-600">Verified Pro ✓</span>
                    </div>
                </div>

                <!-- Testimonial Card 4 -->
                <div class="w-[310px] sm:w-[360px] md:w-[390px] flex-shrink-0 bg-white rounded-[3px] border border-gray-200 p-7 flex flex-col justify-between hover:border-[#1952E1] transition-all duration-300">
                    <div class="space-y-4">
                        <div class="w-10 h-10 rounded-[3px] bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center font-bold text-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                        </div>
                        <p class="text-slate-800 text-sm sm:text-base leading-relaxed">
                            “The Turnitin 0% non-plagiarism guarantee and SPSS dataset analysis made our thesis defense preparation completely seamless.”
                        </p>
                        <div class="flex flex-wrap gap-1.5 pt-2">
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-[3px]">SPSS Analytics</span>
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-[3px]">Turnitin 0%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-6 border-t border-gray-100 mt-6">
                        <div class="flex items-center gap-3">
                            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=120" alt="Jordi" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <h4 class="font-bold text-sm text-brand-dark">Jordi Robert</h4>
                                <p class="text-[11px] text-slate-500">Postgraduate Researcher</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-blue-600">Verified Client ✓</span>
                    </div>
                </div>

                <!-- SET 2 (Duplicate for Seamless Marquee) -->
                <!-- Testimonial Card 1 Duplicate -->
                <div class="w-[310px] sm:w-[360px] md:w-[390px] flex-shrink-0 bg-white rounded-[3px] border border-gray-200 p-7 flex flex-col justify-between hover:border-[#1952E1] transition-all duration-300">
                    <div class="space-y-4">
                        <div class="w-10 h-10 rounded-[3px] bg-blue-50 text-[#1952E1] border border-blue-100 flex items-center justify-center font-bold text-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                        </div>
                        <p class="text-slate-800 text-sm sm:text-base leading-relaxed">
                            “Scriptly’s milestone escrow and pre-assessed developer gave me 100% confidence. Our final year web portal was delivered 3 days ahead of schedule with zero bugs.”
                        </p>
                        <div class="flex flex-wrap gap-1.5 pt-2">
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-[3px]">Full-Stack Web</span>
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-[3px]">Escrow Safe</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-6 border-t border-gray-100 mt-6">
                        <div class="flex items-center gap-3">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=120" alt="Elena" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <h4 class="font-bold text-sm text-brand-dark">Elena Rostova</h4>
                                <p class="text-[11px] text-slate-500">CTO, FinTech Solutions</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-blue-600">Verified Client ✓</span>
                    </div>
                </div>

                <!-- Testimonial Card 2 Duplicate -->
                <div class="w-[310px] sm:w-[360px] md:w-[390px] flex-shrink-0 bg-white rounded-[3px] border border-gray-200 p-7 flex flex-col justify-between hover:border-[#1952E1] transition-all duration-300">
                    <div class="space-y-4">
                        <div class="w-10 h-10 rounded-[3px] bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center font-bold text-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.37 2.63 14 7l-1.59-1.59a2 2 0 0 0-2.82 0L8 7l9 9 1.59-1.59a2 2 0 0 0 0-2.82L17 10l4.37-4.37a2.12 2.12 0 1 0-3-3Z"/><path d="M9 8c-2 3-4 3.5-7 4l8 8c.5-3 1-5 4-7"/></svg>
                        </div>
                        <p class="text-slate-800 text-sm sm:text-base leading-relaxed">
                            “Thanks to Scriptly’s direct chat and milestone inspection, we managed our UI prototype design easily. Clean Figma vectors delivered on point.”
                        </p>
                        <div class="flex flex-wrap gap-1.5 pt-2">
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-[3px]">UI/UX Figma</span>
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-[3px]">Brand Identity</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-6 border-t border-gray-100 mt-6">
                        <div class="flex items-center gap-3">
                            <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=120" alt="Elise" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <h4 class="font-bold text-sm text-brand-dark">Elise Hernaez</h4>
                                <p class="text-[11px] text-slate-500">Tech Lead & Founder</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-blue-600">Verified Client ✓</span>
                    </div>
                </div>

                <!-- Testimonial Card 3 Duplicate -->
                <div class="w-[310px] sm:w-[360px] md:w-[390px] flex-shrink-0 bg-white rounded-[3px] border border-gray-200 p-7 flex flex-col justify-between hover:border-[#1952E1] transition-all duration-300">
                    <div class="space-y-4">
                        <div class="w-10 h-10 rounded-[3px] bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center font-bold text-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <p class="text-slate-800 text-sm sm:text-base leading-relaxed">
                            “The milestone escrow system is the best I've used. I never worry about delayed payments. Passing the assessment immediately brought client requests.”
                        </p>
                        <div class="flex flex-wrap gap-1.5 pt-2">
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-[3px]">Pre-Assessed</span>
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-[3px]">Instant Payout</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-6 border-t border-gray-100 mt-6">
                        <div class="flex items-center gap-3">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=120" alt="Marcus" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <h4 class="font-bold text-sm text-brand-dark">Marcus Vance</h4>
                                <p class="text-[11px] text-slate-500">System Architect • Top Pro</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-emerald-600">Verified Pro ✓</span>
                    </div>
                </div>

                <!-- Testimonial Card 4 Duplicate -->
                <div class="w-[310px] sm:w-[360px] md:w-[390px] flex-shrink-0 bg-white rounded-[3px] border border-gray-200 p-7 flex flex-col justify-between hover:border-[#1952E1] transition-all duration-300">
                    <div class="space-y-4">
                        <div class="w-10 h-10 rounded-[3px] bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center font-bold text-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                        </div>
                        <p class="text-slate-800 text-sm sm:text-base leading-relaxed">
                            “The Turnitin 0% non-plagiarism guarantee and SPSS dataset analysis made our thesis defense preparation completely seamless.”
                        </p>
                        <div class="flex flex-wrap gap-1.5 pt-2">
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-[3px]">SPSS Analytics</span>
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-[3px]">Turnitin 0%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-6 border-t border-gray-100 mt-6">
                        <div class="flex items-center gap-3">
                            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=120" alt="Jordi" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <h4 class="font-bold text-sm text-brand-dark">Jordi Robert</h4>
                                <p class="text-[11px] text-slate-500">Postgraduate Researcher</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-blue-600">Verified Client ✓</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 5. Why Choose Us Section (Competitive Advantage Grid / All-In-One Solution) -->
    <section id="why-us" class="max-w-7xl mx-auto px-3 sm:px-6 mb-24">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <span class="inline-block px-3.5 py-1 bg-blue-50 border border-blue-200/80 text-[#1952E1] text-xs font-black rounded-full uppercase tracking-wider">
                Competitive Advantage
            </span>
            <h2 class="font-serif text-3xl md:text-5xl font-bold text-brand-dark mt-3 tracking-tight">Why Choose Scriptly</h2>
            <p class="text-gray-600 text-base md:text-lg mt-2">We eliminate risk, guarantee quality, and enforce milestone safety for every project.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            
            <!-- Left Side: Two stacked highlight cards (Span 5) -->
            <div class="lg:col-span-5 flex flex-col gap-6">
                <!-- Top Card -->
                <div class="relative flex-[1.2] min-h-[300px] rounded-[3px] overflow-hidden group shadow-sm flex flex-col justify-end p-6 text-left border border-slate-200/40">
                    <!-- Background Image -->
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=800" alt="Ambitious Professional" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 z-0">
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-slate-950/20 z-10"></div>
                    
                    <!-- Content -->
                    <div class="relative z-20 space-y-3">
                        <span class="inline-block bg-[#1952E1] text-white font-extrabold text-[9px] uppercase px-3 py-1 rounded-[3px] tracking-wider shadow-sm">
                            Built for excellence
                        </span>
                        <h3 class="font-serif text-2xl font-bold text-white leading-tight">
                            Built For Serious Service Professionals
                        </h3>
                        <div class="pt-2 border-t border-white/20 mt-3 space-y-1">
                            <h4 class="font-bold text-white text-base">Protected Platform</h4>
                            <p class="text-sm text-slate-300 leading-relaxed font-medium">
                                Scriptly guarantees that you work under protected platform regulations. From minimum categories budget floors to automatic milestone releases, your technical margins are locked in.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Bottom Card -->
                <div class="relative flex-1 min-h-[220px] rounded-[3px] overflow-hidden group shadow-sm flex flex-col justify-end p-6 text-left border border-slate-200/40">
                    <!-- Background Image -->
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=800" alt="Team Collaboration" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 z-0">
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-slate-950/40 z-10"></div>
                    
                    <!-- Content -->
                    <div class="relative z-20 space-y-3">
                        <span class="inline-block bg-white/20 text-white border border-white/20 font-extrabold text-[9px] uppercase px-3 py-1 rounded-[3px] tracking-wider backdrop-blur-sm">
                            Seamless Workflow
                        </span>
                        <h3 class="font-serif text-xl font-bold text-white leading-tight">
                            All-in-One Collaboration Hub
                        </h3>
                        <p class="text-sm text-slate-300 leading-relaxed font-medium">
                            Manage your project files, track deadlines, and communicate in real-time through our dedicated workspace. Experience true end-to-end transparency without relying on external tools.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Side: Clean 2x2 Grid of benefits (Span 7) -->
            <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-6">
                
                <!-- Benefit 1 -->
                <div class="relative min-h-[220px] rounded-[3px] overflow-hidden group shadow-sm flex flex-col justify-end p-6 text-left border border-slate-200/40 hover:border-[#1952E1] transition-all">
                    <!-- Background Image -->
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=800" alt="Professionals Assessed" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 z-0">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-slate-950/40 z-10"></div>
                    <div class="relative z-20 space-y-3">
                        <div class="w-10 h-10 rounded-[3px] bg-white/10 text-white border border-white/20 flex items-center justify-center text-lg backdrop-blur-sm group-hover:bg-[#1952E1] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="font-bold text-base text-white">Professionals Assessed</h3>
                        <p class="text-xs text-slate-300 leading-relaxed font-medium">Every talent undergoes mandatory technical and domain assessment tests before qualifying.</p>
                    </div>
                </div>

                <!-- Benefit 2 -->
                <div class="relative min-h-[220px] rounded-[3px] overflow-hidden group shadow-sm flex flex-col justify-end p-6 text-left border border-slate-200/40 hover:border-[#1952E1] transition-all">
                    <!-- Background Image -->
                    <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&q=80&w=800" alt="Verified Before Accepting Jobs" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 z-0">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-slate-950/40 z-10"></div>
                    <div class="relative z-20 space-y-3">
                        <div class="w-10 h-10 rounded-[3px] bg-white/10 text-white border border-white/20 flex items-center justify-center text-lg backdrop-blur-sm group-hover:bg-[#1952E1] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <h3 class="font-bold text-base text-white">Verified Before Accepting Jobs</h3>
                        <p class="text-xs text-slate-300 leading-relaxed font-medium">Identity verification and credential checks are mandatory before submitting proposals.</p>
                    </div>
                </div>

                <!-- Benefit 3 -->
                <div class="relative min-h-[220px] rounded-[3px] overflow-hidden group shadow-sm flex flex-col justify-end p-6 text-left border border-slate-200/40 hover:border-[#1952E1] transition-all">
                    <!-- Background Image -->
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=800" alt="Milestone-Based Projects" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 z-0">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-slate-950/40 z-10"></div>
                    <div class="relative z-20 space-y-3">
                        <div class="w-10 h-10 rounded-[3px] bg-white/10 text-white border border-white/20 flex items-center justify-center text-lg backdrop-blur-sm group-hover:bg-[#1952E1] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h3 class="font-bold text-base text-white">Milestone-Based Projects</h3>
                        <p class="text-xs text-slate-300 leading-relaxed font-medium">Break contracts into clear milestone deliverables so progress is tracked transparently.</p>
                    </div>
                </div>

                <!-- Benefit 4 -->
                <div class="relative min-h-[220px] rounded-[3px] overflow-hidden group shadow-sm flex flex-col justify-end p-6 text-left border border-slate-200/40 hover:border-[#1952E1] transition-all">
                    <!-- Background Image -->
                    <img src="https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&q=80&w=800" alt="Secure Payments" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 z-0">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-slate-950/40 z-10"></div>
                    <div class="relative z-20 space-y-3">
                        <div class="w-10 h-10 rounded-[3px] bg-white/10 text-white border border-white/20 flex items-center justify-center text-lg backdrop-blur-sm group-hover:bg-[#1952E1] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <h3 class="font-bold text-base text-white">Secure Payments</h3>
                        <p class="text-xs text-slate-300 leading-relaxed font-medium">Funds remain safely locked in escrow until milestone deliverables are approved.</p>
                    </div>
                </div>

                <!-- Benefit 5 -->
                <div class="relative min-h-[220px] rounded-[3px] overflow-hidden group shadow-sm flex flex-col justify-end p-6 text-left border border-slate-200/40 hover:border-[#1952E1] transition-all">
                    <!-- Background Image -->
                    <img src="https://images.unsplash.com/photo-1589829085413-56de8ae18c73?auto=format&fit=crop&q=80&w=800" alt="Dispute Handling" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 z-0">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-slate-950/40 z-10"></div>
                    <div class="relative z-20 space-y-3">
                        <div class="w-10 h-10 rounded-[3px] bg-white/10 text-white border border-white/20 flex items-center justify-center text-lg backdrop-blur-sm group-hover:bg-[#1952E1] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l9-4 9 4M3 6v14a2 2 0 002 2h14a2 2 0 002-2V6M3 6l9 6 9-6"></path></svg>
                        </div>
                        <h3 class="font-bold text-base text-white">Dispute Handling</h3>
                        <p class="text-xs text-slate-300 leading-relaxed font-medium">Dedicated admin dispute resolution desk ensures fair, audited contract outcomes.</p>
                    </div>
                </div>

                <!-- Benefit 6 -->
                <div class="relative min-h-[220px] rounded-[3px] overflow-hidden group shadow-sm flex flex-col justify-end p-6 text-left border border-slate-200/40 hover:border-[#1952E1] transition-all">
                    <!-- Background Image -->
                    <img src="https://images.unsplash.com/photo-1611162617474-5b21e879e113?auto=format&fit=crop&q=80&w=800" alt="Real-Time Chat" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 z-0">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-slate-950/40 z-10"></div>
                    <div class="relative z-20 space-y-3">
                        <div class="w-10 h-10 rounded-[3px] bg-white/10 text-white border border-white/20 flex items-center justify-center text-lg backdrop-blur-sm group-hover:bg-[#1952E1] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </div>
                        <h3 class="font-bold text-base text-white">Real-Time Chat</h3>
                        <p class="text-xs text-slate-300 leading-relaxed font-medium">Instant real-time messaging with direct file attachments and live notifications.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 7. How It Works Section (Full-Width Sticky Stacking Cards with Real Photography) -->
    <section id="how-it-works" class="w-full py-12 mb-24 bg-slate-50">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-14 px-4 sm:px-6">
            <span class="inline-block px-3.5 py-1 bg-blue-100 border border-blue-200/80 text-[#1952E1] text-xs font-black rounded-full uppercase tracking-wider">
                Simple 4-Step Process
            </span>
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-brand-dark tracking-tight mt-3">
                How Scriptly Works
            </h2>
            <p class="text-slate-600 text-sm sm:text-base mt-2">
                From project post to verified deliverable in 4 secure, escrow-protected steps.
            </p>
        </div>

        <!-- Sticky Stacking Cards Stream (Full Viewport Width) -->
        <div class="w-full space-y-0">
            
            <!-- STACK CARD 1: STEP 01 -->
            <div class="sticky top-20 lg:top-24 z-10 w-full bg-white border-y border-gray-200 py-10 sm:py-14 lg:py-16 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-center">
                    
                    <!-- Left Visual: Real High-Res Photography -->
                    <div class="lg:col-span-6 w-full h-[300px] sm:h-[400px] rounded-[3px] overflow-hidden relative shadow-sm">
                        <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80&w=1000" alt="Students planning project" class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4 bg-[#0A2342] text-white text-xs font-bold px-3 py-1.5 rounded-[3px]">
                            Step 01 • Project Setup
                        </div>
                    </div>

                    <!-- Right Content -->
                    <div class="lg:col-span-6 space-y-5">
                        <span class="inline-block px-3 py-1 bg-blue-50 border border-blue-200 text-[#1952E1] text-xs font-bold rounded-full uppercase tracking-wider">
                            Post Requirements
                        </span>
                        <h3 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 tracking-tight leading-[1.1]">
                            Post your project requirements & deadline
                        </h3>
                        <p class="text-slate-600 text-base sm:text-lg leading-relaxed">
                            Describe what you need — from full-stack web applications and PHP portals to SPSS data analysis and dissertation reports. Set your budget and milestones in minutes with zero upfront listing fees.
                        </p>
                        <div class="pt-4">
                            <a href="signup?role=client" class="inline-flex items-center gap-2 text-lg font-bold text-[#1952E1] hover:text-blue-800 transition-colors group">
                                <span>Post a Project for Free</span>
                                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- STACK CARD 2: STEP 02 -->
            <div class="sticky top-24 lg:top-28 z-20 w-full bg-slate-50 border-y border-gray-200 py-10 sm:py-14 lg:py-16 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-center">
                    
                    <!-- Left Visual: Real High-Res Photography -->
                    <div class="lg:col-span-6 w-full h-[300px] sm:h-[400px] rounded-[3px] overflow-hidden relative shadow-sm">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=1000" alt="Verified Freelancers working" class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4 bg-[#0A2342] text-white text-xs font-bold px-3 py-1.5 rounded-[3px]">
                            Step 02 • Pre-Assessed Talent
                        </div>
                    </div>

                    <!-- Right Content -->
                    <div class="lg:col-span-6 space-y-5">
                        <span class="inline-block px-3 py-1 bg-blue-50 border border-blue-200 text-[#1952E1] text-xs font-bold rounded-full uppercase tracking-wider">
                            Pre-Vetted Bids
                        </span>
                        <h3 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 tracking-tight leading-[1.1]">
                            Receive proposals from pre-assessed talent
                        </h3>
                        <p class="text-slate-600 text-base sm:text-lg leading-relaxed">
                            No unvetted freelancers or random proposals. Only candidates who passed rigorous technical domain assessments above 80% and verified their national ID can apply to your project.
                        </p>
                        <div class="pt-4">
                            <a href="talent" class="inline-flex items-center gap-2 text-lg font-bold text-[#1952E1] hover:text-blue-800 transition-colors group">
                                <span>Explore Verified Talent</span>
                                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- STACK CARD 3: STEP 03 -->
            <div class="sticky top-28 lg:top-32 z-30 w-full bg-white border-y border-gray-200 py-10 sm:py-14 lg:py-16 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-center">
                    
                    <!-- Left Visual: Real High-Res Photography -->
                    <div class="lg:col-span-6 w-full h-[300px] sm:h-[400px] rounded-[3px] overflow-hidden relative shadow-sm">
                        <img src="https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&q=80&w=1000" alt="Milestone Escrow Protection" class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4 bg-[#1952E1] text-white text-xs font-bold px-3 py-1.5 rounded-[3px]">
                            Step 03 • 100% Escrow
                        </div>
                    </div>

                    <!-- Right Content -->
                    <div class="lg:col-span-6 space-y-5">
                        <span class="inline-block px-3 py-1 bg-blue-50 border border-blue-200 text-[#1952E1] text-xs font-bold rounded-full uppercase tracking-wider">
                            Milestone Escrow
                        </span>
                        <h3 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 tracking-tight leading-[1.1]">
                            Deposit funds safely into milestone escrow
                        </h3>
                        <p class="text-slate-600 text-base sm:text-lg leading-relaxed">
                            Your payment is never sent directly to the freelancer upfront. Funds remain locked securely in Scriptly's regulated escrow vault until you inspect the submitted files and approve them.
                        </p>
                        <div class="pt-4">
                            <a href="about" class="inline-flex items-center gap-2 text-lg font-bold text-[#1952E1] hover:text-blue-800 transition-colors group">
                                <span>How Escrow Protects You</span>
                                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- STACK CARD 4: STEP 04 -->
            <div class="sticky top-32 lg:top-36 z-40 w-full bg-slate-50 border-y border-gray-200 py-10 sm:py-14 lg:py-16 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-center">
                    
                    <!-- Left Visual: Real High-Res Photography -->
                    <div class="lg:col-span-6 w-full h-[300px] sm:h-[400px] rounded-[3px] overflow-hidden relative shadow-sm">
                        <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&q=80&w=1000" alt="Completed Deliverable and Quality Release" class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4 bg-emerald-600 text-white text-xs font-bold px-3 py-1.5 rounded-[3px]">
                            Step 04 • Quality & Payout
                        </div>
                    </div>

                    <!-- Right Content -->
                    <div class="lg:col-span-6 space-y-5">
                        <span class="inline-block px-3 py-1 bg-blue-50 border border-blue-200 text-[#1952E1] text-xs font-bold rounded-full uppercase tracking-wider">
                            Quality & Payout
                        </span>
                        <h3 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 tracking-tight leading-[1.1]">
                            Inspect original work & release payment
                        </h3>
                        <p class="text-slate-600 text-base sm:text-lg leading-relaxed">
                            Test the delivered source code, verify 0% Turnitin similarity on reports, and request adjustments if needed. When you are 100% satisfied, release the funds instantly with one click.
                        </p>
                        <div class="pt-4">
                            <a href="signup?role=client" class="inline-flex items-center gap-2 text-lg font-bold text-[#1952E1] hover:text-blue-800 transition-colors group">
                                <span>Get Started with Scriptly</span>
                                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </section>

    <!-- FAQ & Performance Section (Hostinger-Style 2-Column Dark Navy UI with Metric Cards) -->
    <section id="faq" class="w-full bg-[#0A2342] text-white py-20 px-4 sm:px-6 lg:px-8 mb-24 relative overflow-hidden">
        <div class="max-w-7xl mx-auto space-y-16">
            
            <!-- Top Section Header -->
            <div class="text-center max-w-3xl mx-auto">
                <span class="inline-block px-3.5 py-1 bg-white/10 border border-white/20 text-blue-300 text-xs font-black rounded-full uppercase tracking-wider mb-3">
                    Support
                </span>
                <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-white tracking-tight">
                    Frequently Asked Questions
                </h2>
            </div>

            <!-- 2-Column Side-by-Side FAQ Grid (Hostinger Layout) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-start">
                
                <!-- COLUMN 1: ESCROW SAFETY & MILESTONES -->
                <div class="space-y-6">
                    <!-- Column Header with Mini Accent Bars -->
                    <div class="space-y-3 border-b border-white/10 pb-4">
                        <div class="flex items-center gap-1.5 text-[#1952E1]">
                            <span class="w-2.5 h-4 bg-[#1952E1] rounded-[1px]"></span>
                            <span class="w-2.5 h-4 bg-blue-400 rounded-[1px]"></span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                            Escrow Safety & Milestone Protection
                        </h3>
                    </div>

                    <!-- Column 1 FAQ Items -->
                    <div class="space-y-2 divide-y divide-white/10">
                        
                        <!-- Item 1.1 (Open by default) -->
                        <div class="faq-accordion-item pt-4 first:pt-0">
                            <button type="button" class="faq-trigger w-full flex items-center justify-between text-left py-3 text-base sm:text-lg font-bold text-white hover:text-blue-300 transition-colors">
                                <span>How does the milestone escrow protect my funds?</span>
                                <span class="faq-icon text-xl font-mono text-blue-400 shrink-0 ml-4">−</span>
                            </button>
                            <div class="faq-body text-xs sm:text-sm text-slate-300 leading-relaxed pt-1 pb-4 space-y-2">
                                <p>When you award a contract or project, your payment is deposited directly into Scriptly's secure escrow. The funds are held safely and are never released to the freelancer until you inspect the submitted files and confirm they meet your rubrics.</p>
                                <a href="about" class="inline-flex items-center text-xs font-bold text-blue-400 hover:text-blue-300 pt-1">
                                    <span>Learn more about Escrow</span>
                                    <i class="ph-bold ph-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Item 1.2 -->
                        <div class="faq-accordion-item pt-4">
                            <button type="button" class="faq-trigger w-full flex items-center justify-between text-left py-3 text-base sm:text-lg font-bold text-white hover:text-blue-300 transition-colors">
                                <span>What is the 7-day inspection window?</span>
                                <span class="faq-icon text-xl font-mono text-blue-400 shrink-0 ml-4">+</span>
                            </button>
                            <div class="faq-body hidden text-xs sm:text-sm text-slate-300 leading-relaxed pt-1 pb-4 space-y-2">
                                <p>Upon deliverable upload, you have a full 7-day inspection window to test source code, check SPSS datasets, or review chapters. If you request revisions, the auto-approval timer pauses immediately until revised files are delivered.</p>
                                <a href="about" class="inline-flex items-center text-xs font-bold text-blue-400 hover:text-blue-300 pt-1">
                                    <span>Learn more about Inspections</span>
                                    <i class="ph-bold ph-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Item 1.3 -->
                        <div class="faq-accordion-item pt-4">
                            <button type="button" class="faq-trigger w-full flex items-center justify-between text-left py-3 text-base sm:text-lg font-bold text-white hover:text-blue-300 transition-colors">
                                <span>What happens if there is a disagreement or issue?</span>
                                <span class="faq-icon text-xl font-mono text-blue-400 shrink-0 ml-4">+</span>
                            </button>
                            <div class="faq-body hidden text-xs sm:text-sm text-slate-300 leading-relaxed pt-1 pb-4 space-y-2">
                                <p>If deliverables fail to meet agreed rubrics after standard revisions, you can open a dispute ticket. Scriptly’s Arbitration Desk reviews the original contract specifications, code submissions, and chat logs to issue a fair resolution or full refund.</p>
                                <a href="contact" class="inline-flex items-center text-xs font-bold text-blue-400 hover:text-blue-300 pt-1">
                                    <span>Learn more about Dispute Mediation</span>
                                    <i class="ph-bold ph-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- COLUMN 2: QUALITY, TURNITIN & TALENT VERIFICATION -->
                <div class="space-y-6">
                    <!-- Column Header with Mini Accent Bars -->
                    <div class="space-y-3 border-b border-white/10 pb-4">
                        <div class="flex items-center gap-1.5 text-[#1952E1]">
                            <span class="w-2.5 h-4 bg-[#1952E1] rounded-[1px]"></span>
                            <span class="w-2.5 h-4 bg-blue-400 rounded-[1px]"></span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                            Verified Quality & Academic Integrity
                        </h3>
                    </div>

                    <!-- Column 2 FAQ Items -->
                    <div class="space-y-2 divide-y divide-white/10">
                        
                        <!-- Item 2.1 (Open by default) -->
                        <div class="faq-accordion-item pt-4 first:pt-0">
                            <button type="button" class="faq-trigger w-full flex items-center justify-between text-left py-3 text-base sm:text-lg font-bold text-white hover:text-blue-300 transition-colors">
                                <span>How are freelancers assessed before joining?</span>
                                <span class="faq-icon text-xl font-mono text-blue-400 shrink-0 ml-4">−</span>
                            </button>
                            <div class="faq-body text-xs sm:text-sm text-slate-300 leading-relaxed pt-1 pb-4 space-y-2">
                                <p>Every service provider must pass rigorous technical and domain assessment exams (e.g. PHP MVC, MySQL, Python, Academic Writing rubrics) scored above 80% and complete government ID / NIN verification before they can submit proposals.</p>
                                <a href="talent" class="inline-flex items-center text-xs font-bold text-blue-400 hover:text-blue-300 pt-1">
                                    <span>Explore Pre-Assessed Talent</span>
                                    <i class="ph-bold ph-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Item 2.2 -->
                        <div class="faq-accordion-item pt-4">
                            <button type="button" class="faq-trigger w-full flex items-center justify-between text-left py-3 text-base sm:text-lg font-bold text-white hover:text-blue-300 transition-colors">
                                <span>Is original, plagiarism-free work guaranteed?</span>
                                <span class="faq-icon text-xl font-mono text-blue-400 shrink-0 ml-4">+</span>
                            </button>
                            <div class="faq-body hidden text-xs sm:text-sm text-slate-300 leading-relaxed pt-1 pb-4 space-y-2">
                                <p>Yes. All project reports, software source code, and dissertations must strictly comply with Turnitin standard submissions. Freelancers pledge 0% unauthorized AI generation and 100% original academic integrity on every deliverable.</p>
                                <a href="about" class="inline-flex items-center text-xs font-bold text-blue-400 hover:text-blue-300 pt-1">
                                    <span>Learn more about Turnitin Compliance</span>
                                    <i class="ph-bold ph-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Item 2.3 -->
                        <div class="faq-accordion-item pt-4">
                            <button type="button" class="faq-trigger w-full flex items-center justify-between text-left py-3 text-base sm:text-lg font-bold text-white hover:text-blue-300 transition-colors">
                                <span>How do I communicate with my assigned freelancer?</span>
                                <span class="faq-icon text-xl font-mono text-blue-400 shrink-0 ml-4">+</span>
                            </button>
                            <div class="faq-body hidden text-xs sm:text-sm text-slate-300 leading-relaxed pt-1 pb-4 space-y-2">
                                <p>Scriptly features an integrated WhatsApp-style direct chat interface with live voice notes, code snippet sharing, document attachments, and 1-click Google Meet video call scheduling directly inside your workspace.</p>
                                <a href="signup?role=client" class="inline-flex items-center text-xs font-bold text-blue-400 hover:text-blue-300 pt-1">
                                    <span>Learn more about Messaging</span>
                                    <i class="ph-bold ph-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- 4 Hostinger-Style Solid Stat Metric Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 pt-6">
                
                <!-- Stat Card 1 -->
                <div class="bg-[#1952E1] p-6 sm:p-7 rounded-[3px] border border-blue-400/20 shadow-lg text-white space-y-2 hover:-translate-y-1 transition-transform">
                    <span class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight block">100%</span>
                    <p class="text-xs sm:text-sm font-bold text-blue-100">Milestone Escrow Protection</p>
                </div>

                <!-- Stat Card 2 -->
                <div class="bg-[#1952E1] p-6 sm:p-7 rounded-[3px] border border-blue-400/20 shadow-lg text-white space-y-2 hover:-translate-y-1 transition-transform">
                    <span class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight block">0%</span>
                    <p class="text-xs sm:text-sm font-bold text-blue-100">Turnitin Plagiarism Tolerance</p>
                </div>

                <!-- Stat Card 3 -->
                <div class="bg-[#1952E1] p-6 sm:p-7 rounded-[3px] border border-blue-400/20 shadow-lg text-white space-y-2 hover:-translate-y-1 transition-transform">
                    <span class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight block">1,420+</span>
                    <p class="text-xs sm:text-sm font-bold text-blue-100">Pre-Assessed Freelancers</p>
                </div>

                <!-- Stat Card 4 -->
                <div class="bg-[#1952E1] p-6 sm:p-7 rounded-[3px] border border-blue-400/20 shadow-lg text-white space-y-2 hover:-translate-y-1 transition-transform">
                    <span class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight block">98.6%</span>
                    <p class="text-xs sm:text-sm font-bold text-blue-100">On-Time Project Delivery</p>
                </div>

            </div>

        </div>
    </section>

    <!-- Combined CTA & Footer Wrapper -->
    <div class="w-full bg-gradient-to-br from-[#0A2342] to-[#1952E1] border-t border-white/10">
        <!-- Final Strong CTA Section -->
        <section class="w-full bg-transparent text-white py-20 md:py-28 px-4 sm:px-6 relative overflow-hidden">
        <div class="max-w-4xl mx-auto text-center relative z-10 space-y-6">
            <h2 class="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-white">
                Ready to get your project done?
            </h2>
            <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
                Join thousands of students, researchers, and verified professionals collaborating securely on Scriptly today.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                <a href="signup?role=client" class="inline-flex justify-center items-center gap-2 w-full sm:w-48 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-sm py-3 rounded-full transition-all hover:scale-105 shadow-md">
                    Request Service
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
                <a href="signup?role=provider" class="inline-flex justify-center items-center gap-2 w-full sm:w-52 bg-white/10 text-white border border-white/20 font-bold text-sm py-3 rounded-full hover:bg-white/20 transition-all hover:scale-105 shadow-md">
                    Become a Professional
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </a>
            </div>
        </div>
    </section>

    <script>
        // Trusted By Testimonials Carousel Logic (Auto-Scroll & Button Controls)
        const trustedTrack = document.getElementById('trusted-track');
        const trustedPrev = document.getElementById('trusted-prev');
        const trustedNext = document.getElementById('trusted-next');

        if (trustedTrack && trustedPrev && trustedNext) {
            const cardWidth = 380;

            trustedPrev.addEventListener('click', () => {
                trustedTrack.scrollBy({ left: -cardWidth, behavior: 'smooth' });
            });

            trustedNext.addEventListener('click', () => {
                trustedTrack.scrollBy({ left: cardWidth, behavior: 'smooth' });
            });

            // Gentle Auto-Scroll Carousel (with pause on hover)
            let autoScrollInterval = setInterval(() => {
                if (trustedTrack.scrollLeft + trustedTrack.clientWidth >= trustedTrack.scrollWidth - 10) {
                    trustedTrack.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    trustedTrack.scrollBy({ left: cardWidth, behavior: 'smooth' });
                }
            }, 4500);

            trustedTrack.addEventListener('mouseenter', () => clearInterval(autoScrollInterval));
            trustedTrack.addEventListener('mouseleave', () => {
                autoScrollInterval = setInterval(() => {
                    if (trustedTrack.scrollLeft + trustedTrack.clientWidth >= trustedTrack.scrollWidth - 10) {
                        trustedTrack.scrollTo({ left: 0, behavior: 'smooth' });
                    } else {
                        trustedTrack.scrollBy({ left: cardWidth, behavior: 'smooth' });
                    }
                }, 4500);
            });
        }

        // FAQ Accordion Toggle Logic (Hostinger Style +/-)
        document.querySelectorAll('.faq-trigger').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const item = trigger.closest('.faq-accordion-item');
                const body = item.querySelector('.faq-body');
                const icon = trigger.querySelector('.faq-icon');
                
                if (body.classList.contains('hidden')) {
                    body.classList.remove('hidden');
                    icon.textContent = '−';
                } else {
                    body.classList.add('hidden');
                    icon.textContent = '+';
                }
            });
        });
    </script>

<?php 
$close_footer_wrapper = true;
include 'includes/footer.php'; 
?>
