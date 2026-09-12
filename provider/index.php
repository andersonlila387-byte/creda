<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/brand.php';
require_once __DIR__ . '/../config/database.php';
try {
    $db = getDBConnection();
    $t_stmt = $db->query("SELECT * FROM testimonials ORDER BY id DESC");
    $testimonials = $t_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $testimonials = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Earn Securely as a Verified Service Provider — Scriptly</title>
    
    <!-- Primary SEO Meta Tags -->
    <meta name="title" content="Earn Securely as a Verified Service Provider — Scriptly">
    <meta name="description" content="Scriptly connects Nigeria's top vetted service providers with high-paying clients. Built-in price floors, secure milestone escrow, and verified skill badges.">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#1952E1">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo getLogoIconUrl() ?: '../assets/brand/logo-icon.png'; ?>">

    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Load Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,800&display=swap" rel="stylesheet">
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Scriptly Custom Alerts & Toast Stylesheet -->
    <link rel="stylesheet" href="../assets/css/scriptly-alerts.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Space Grotesk', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        brand: {
                            bg: '#f8f7f5',
                            dark: '#0A2342',
                            blue: '#1952E1',
                            accent: '#1952E1',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Continuous Marquee Animation */
        @keyframes marquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            display: flex;
            width: max-content;
            animation: marquee 35s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f8f7f5;
        }
        ::-webkit-scrollbar-thumb {
            background: #0A2342;
            border-radius: 4px;
            border: 2px solid #f8f7f5;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #2563eb;
        }
    </style>
</head>
<body class="bg-brand-bg text-brand-dark font-sans antialiased selection:bg-blue-200 selection:text-blue-900" style="background-color: #f8f7f5;">

    <!-- Instantly Rendered Preloader -->
    <div id="page-preloader" style="position: fixed; inset: 0; background: #f8f7f5; z-index: 99999; display: flex; align-items: center; justify-content: center; transition: opacity 0.3s ease; opacity: 1;">
        <!-- Fancy Orb Spinner -->
        <div style="position: relative; width: 64px; height: 64px; display: flex; align-items: center; justify-content: center; background: white; border-radius: 50%; border: 1px solid rgba(0, 0, 0, 0.05); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);">
            <!-- Orbit ring in brand blue color only -->
            <div style="position: absolute; inset: -4px; border: 3px solid transparent; border-top-color: #1952E1; border-radius: 50%; animation: preloader-orbit 1s linear infinite;"></div>
            <!-- Pulsing Core -->
            <div style="width: 32px; height: 32px; background: #0A2342; border-radius: 50%; display: flex; align-items: center; justify-content: center; animation: preloader-pulse 1.4s ease-in-out infinite;">
                <svg style="width: 16px; height: 16px; color: white;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18.178 8c5.096 0 5.096 8 0 8-2.69 0-4.7-2.115-6.178-4-1.478-1.885-3.488-4-6.178-4-5.096 0-5.096 8 0 8 2.69 0 4.7-2.115 6.178-4 1.478-1.885-3.488-4 6.178-4z"></path>
                </svg>
            </div>
        </div>
    </div>
    <style>
        @keyframes preloader-orbit { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @keyframes preloader-pulse { 0%, 100% { transform: scale(1); opacity: 0.95; } 50% { transform: scale(1.15); opacity: 1; } }
    </style>

    <!-- Header / Navbar -->
    <nav id="main-nav" class="fixed top-0 w-full px-4 sm:px-12 py-4 z-50 transition-all duration-300 bg-transparent backdrop-blur-none border-transparent text-white">
        <div class="max-w-[1400px] mx-auto flex items-center justify-between">
            
            <!-- Left Nav (Desktop) -->
            <div class="hidden lg:flex items-center space-x-8 text-[15px] font-semibold text-white w-1/3 nav-links">
                <a href="#benefits" class="hover:opacity-75 transition-colors">Core Benefits</a>
                <a href="#price-floors" class="hover:opacity-75 transition-colors">Pricing Floors</a>
                <a href="#how-it-works" class="hover:opacity-75 transition-colors">How it Works</a>
            </div>

            <!-- Logo (Exact Center of Screen) -->
            <div class="flex justify-center items-center w-full lg:w-1/3">
                <a href="./" class="group logo-text text-white">
                    <?php echo renderLogoFull('flex items-center space-x-2 text-2xl font-extrabold tracking-tight group', 'w-6 h-6'); ?>
                </a>
            </div>

            <!-- Right Nav (Desktop) -->
            <div class="hidden lg:flex items-center justify-end space-x-4 text-[15px] font-semibold text-white w-1/3 nav-links">
                <a href="login.php" class="hover:opacity-75 transition-colors px-3 py-2">Login</a>
                <a href="signup.php" class="bg-white/20 backdrop-blur-sm text-white px-5 py-2.5 rounded-full hover:bg-white/30 transition-all border border-white/30 font-bold sign-up-btn">Apply to Join (Free)</a>
            </div>

            <!-- Mobile Hamburger Menu -->
            <div class="lg:hidden absolute left-4 top-1/2 -translate-y-1/2 flex items-center">
                <button id="mobile-menu-btn" class="text-white focus:outline-none p-2 -ml-2 logo-text">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile User Icon -->
            <div class="lg:hidden absolute right-4 top-1/2 -translate-y-1/2 flex items-center">
                 <a href="login.php" class="text-white focus:outline-none p-2 logo-text">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </a>
            </div>
        </div>
    </nav>

    <!-- Mobile Drawer Menu -->
    <div id="mobile-menu" class="fixed inset-0 bg-[#f8f7f5]/95 backdrop-blur-xl text-brand-dark z-40 transform -translate-y-full transition-transform duration-300 ease-in-out lg:hidden pt-24">
        <div class="flex flex-col p-6 space-y-5 text-lg font-semibold">
            <a href="#benefits" class="hover:text-blue-600">Core Benefits</a>
            <a href="#price-floors" class="hover:text-blue-600">Pricing Floors</a>
            <a href="#how-it-works" class="hover:text-blue-600">How it Works</a>
            <a href="#faq" class="hover:text-blue-600">FAQ</a>
            <div class="h-px w-full bg-gray-200 my-2"></div>
            <a href="login.php" class="hover:text-blue-600">Pro Login</a>
            <a href="signup.php" class="bg-brand-dark text-white text-center px-5 py-3 rounded-full font-bold">Apply to Join</a>
        </div>
    </div>

    <!-- Symmetrical Dark Hero Header (Background image clearly visible, rephrased concise text) -->
    <header class="relative w-full flex flex-col justify-center items-center text-center px-4 sm:px-6 py-20 sm:py-28 md:py-36 overflow-hidden">
        <!-- Crisp Visible Background Image Asset -->
        <div class="absolute inset-0 z-0 pointer-events-none">
            <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1600" alt="Vetted Service Providers Collaborating" class="w-full h-full object-cover">
        </div>
        <!-- Flat Dark Overlay tint allowing the background image to show clearly, making white text pop -->
        <div class="absolute inset-0 z-0 bg-slate-950/65 pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl mx-auto px-4">
            <!-- Main Heading (Playfair Display, Symmetrical White Text) -->
            <h1 class="font-serif text-3xl sm:text-5xl md:text-6xl leading-[1.12] text-white font-black tracking-tight mb-4 sm:mb-6">
                Nigeria's Premier <br class="hidden sm:block"> Provider Network.
            </h1>

            <!-- Subheading (Concise & Rephrased) -->
            <p class="text-sm sm:text-base md:text-lg text-slate-100 max-w-xl mx-auto mb-8 sm:mb-10 font-medium leading-relaxed">
                Get vetted. Connect with high-value clients. Earn securely with guaranteed escrow payments.
            </p>

            <!-- CTA Buttons (Pill shapes, responsive transitions) -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3.5 sm:gap-4 w-full max-w-sm sm:max-w-none mx-auto">
                <a href="signup.php" class="w-full sm:w-auto sm:min-w-[190px] text-center bg-[#1952E1] hover:bg-blue-700 text-white font-extrabold text-sm py-3.5 px-6 rounded-full transition-all hover:scale-105 shadow-md">
                    Apply to Join (Free)
                </a>
                <a href="login.php" class="w-full sm:w-auto sm:min-w-[190px] text-center bg-transparent text-white border-2 border-white font-extrabold text-sm py-3.5 px-6 rounded-full hover:bg-white/10 transition-all hover:scale-105 shadow-sm">
                    Pro Login
                </a>
            </div>
        </div>
    </header>

    <!-- Sleek Dark Trust & Verification Marquee Ribbon -->
    <section class="w-full bg-[#0A2342] text-white py-5 md:py-6 overflow-hidden border-y border-white/10 relative">
        <div class="animate-marquee flex items-center gap-10 sm:gap-14 whitespace-nowrap text-sm md:text-base font-bold">
            
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <span>Vetted Skill Badges</span>
                <span class="text-xs font-normal text-blue-200/90 bg-white/10 px-3 py-1 rounded-[3px] ml-1 border border-white/10">Pre-Assessed</span>
            </div>
            <span class="text-white/20 font-bold">✦</span>
            
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span>Escrow Protected Payouts</span>
                <span class="text-xs font-normal text-blue-200/90 bg-white/10 px-3 py-1 rounded-[3px] ml-1 border border-white/10">Milestone Safed</span>
            </div>
            <span class="text-white/20 font-bold">✦</span>
            
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l9-4 9 4M3 6v14a2 2 0 002 2h14a2 2 0 002-2V6M3 6l9 6 9-6"></path></svg>
                <span>Anti-Lowball Price Floors</span>
                <span class="text-xs font-normal text-blue-200/90 bg-white/10 px-3 py-1 rounded-[3px] ml-1 border border-white/10">Protected Margins</span>
            </div>
            <span class="text-white/20 font-bold">✦</span>
            
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span>Real-Time Messaging & Direct Chat</span>
                <span class="text-xs font-normal text-blue-200/90 bg-white/10 px-3 py-1 rounded-[3px] ml-1 border border-white/10">Instant Sync</span>
            </div>
            <span class="text-white/20 font-bold">✦</span>

        </div>
    </section>

    <!-- Bento Layout: Platform Protections (Modern grid card structure) -->
    <section id="price-floors" class="max-w-7xl mx-auto px-4 sm:px-6 my-20">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="text-xs font-black uppercase tracking-widest text-[#1952E1] bg-blue-50 border border-blue-200/80 px-3 py-1 rounded-full">
                Platform Protections
            </span>
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-brand-dark mt-3 tracking-tight">
                Safety Guardrails Built For Pros
            </h2>
            <p class="text-slate-600 text-xs sm:text-sm mt-2">
                Five powerful guardrails engineered to protect your time, margins, and payouts.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Card 1: Platform-Enforced Budget Floors (Large card, spans col-span-2) -->
            <div class="md:col-span-2 bg-white border border-slate-200 p-8 rounded-[3px] flex flex-col justify-between hover:border-[#1952E1] transition-all duration-300 group shadow-xs">
                <div class="space-y-4">
                    <span class="text-[10px] font-black uppercase text-blue-600 tracking-wider">Zero Price Lowballing</span>
                    <h3 class="font-serif text-2xl font-bold text-slate-900 leading-tight">Enforced Naira Price Floors</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-medium">Clients cannot create projects below minimum category thresholds. This guarantees you earn premium rates and bypass lowball bids.</p>
                </div>
                
                <!-- Interactive List inside the Large Bento Box -->
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-[3px] flex flex-col justify-between">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Software dev</span>
                        <span class="text-sm font-black text-brand-dark mt-2">₦180,000</span>
                        <span class="text-[9px] text-slate-500 font-semibold mt-1">Minimum budget</span>
                    </div>
                    <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-[3px] flex flex-col justify-between">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">UI/UX Figma</span>
                        <span class="text-sm font-black text-brand-dark mt-2">₦50,000</span>
                        <span class="text-[9px] text-slate-500 font-semibold mt-1">Minimum budget</span>
                    </div>
                    <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-[3px] flex flex-col justify-between">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Thesis & spss</span>
                        <span class="text-sm font-black text-brand-dark mt-2">₦50,000</span>
                        <span class="text-[9px] text-slate-500 font-semibold mt-1">Minimum budget</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Milestone Escrow Vault (Spans 1 col) -->
            <div class="bg-white border border-slate-200 p-8 rounded-[3px] flex flex-col justify-between hover:border-[#1952E1] transition-all duration-300 group shadow-xs">
                <div class="space-y-4">
                    <div class="w-10 h-10 rounded-[3px] bg-blue-50 text-[#1952E1] border border-blue-100 flex items-center justify-center font-bold text-lg group-hover:bg-[#0A2342] group-hover:text-white transition-colors">
                        <i class="ph-bold ph-shield-check"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 leading-tight mt-2">100% Escrow Protection</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">Clients fund milestone amounts before you begin work. Payouts are locked in escrow and released directly upon task upload and approval.</p>
                </div>
            </div>

            <!-- Card 3: Auto-Release Review Window (Spans 1 col) -->
            <div class="bg-white border border-slate-200 p-8 rounded-[3px] flex flex-col justify-between hover:border-[#1952E1] transition-all duration-300 group shadow-xs">
                <div class="space-y-4">
                    <div class="w-10 h-10 rounded-[3px] bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center font-bold text-lg group-hover:bg-[#0A2342] group-hover:text-white transition-colors">
                        <i class="ph-bold ph-timer"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 leading-tight mt-2">10-Day Review Lock</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">If the client fails to review submissions, the funds release automatically after 10 days. Zero delayed payments.</p>
                </div>
            </div>

            <!-- Card 4: Audited Dispute Resolution (Spans 1 col) -->
            <div class="bg-white border border-slate-200 p-8 rounded-[3px] flex flex-col justify-between hover:border-[#1952E1] transition-all duration-300 group shadow-xs">
                <div class="space-y-4">
                    <div class="w-10 h-10 rounded-[3px] bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center font-bold text-lg group-hover:bg-[#0A2342] group-hover:text-white transition-colors">
                        <i class="ph-bold ph-scales"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 leading-tight mt-2">Audited Arbitrations</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">Impartial project dispute moderations based strictly on chat attachment logs and submission evidence inside the dashboard.</p>
                </div>
            </div>

            <!-- Card 5: Fast Nigerian Bank Payouts (Spans 1 col) -->
            <div class="bg-white border border-slate-200 p-8 rounded-[3px] flex flex-col justify-between hover:border-[#1952E1] transition-all duration-300 group shadow-xs">
                <div class="space-y-4">
                    <div class="w-10 h-10 rounded-[3px] bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center font-bold text-lg group-hover:bg-[#0A2342] group-hover:text-white transition-colors">
                        <i class="ph-bold ph-bank"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 leading-tight mt-2">24h Local Payouts</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">Withdraw cleared earnings to OPay, Moniepoint, GTBank, Access Bank, or UBA. Withdrawals clear in 24 hours.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- Redesigned: 4-Column Image Bento Step Grid for How it Works (Fast, Visual, and Modern) -->
    <section id="how-it-works" class="max-w-7xl mx-auto px-4 sm:px-6 my-24 text-center">
        <div class="max-w-3xl mx-auto mb-12 space-y-2">
            <span class="text-xs font-black uppercase tracking-widest text-[#1952E1] bg-blue-50 border border-blue-200/80 px-3.5 py-1 rounded-full">
                Simple 4-Step Process
            </span>
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-brand-dark tracking-tight">
                How Scriptly Pro Works
            </h2>
            <p class="text-slate-600 text-xs sm:text-sm">
                Pass tests, pick high-budget contracts, and enjoy seamless escrow payouts.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Step 1 -->
            <div class="relative h-[340px] rounded-[3px] overflow-hidden group shadow-sm flex flex-col justify-end p-6 border border-slate-200/40 text-left">
                <!-- Background Image -->
                <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&q=80&w=600" alt="Pass Assessments" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 z-0">
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-slate-950/20 z-10"></div>
                
                <!-- Overlaid Content -->
                <div class="relative z-20 space-y-2">
                    <span class="w-7 h-7 rounded-full bg-[#1952E1] text-white flex items-center justify-center font-bold text-xs">01</span>
                    <h4 class="font-extrabold text-white text-sm tracking-tight leading-tight">Pass Assessments</h4>
                    <p class="text-[11px] text-slate-300 leading-relaxed font-medium">Verify your student status and pass a timed technical category quiz scoring 80% or above.</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="relative h-[340px] rounded-[3px] overflow-hidden group shadow-sm flex flex-col justify-end p-6 border border-slate-200/40 text-left">
                <!-- Background Image -->
                <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&q=80&w=600" alt="Bid on Open Gigs" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 z-0">
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-slate-950/20 z-10"></div>
                
                <!-- Overlaid Content -->
                <div class="relative z-20 space-y-2">
                    <span class="w-7 h-7 rounded-full bg-[#1952E1] text-white flex items-center justify-center font-bold text-xs">02</span>
                    <h4 class="font-extrabold text-white text-sm tracking-tight leading-tight">Bid on Open Gigs</h4>
                    <p class="text-[11px] text-slate-300 leading-relaxed font-medium">Browse verified client contracts and pitch custom proposals using our platform price floors.</p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="relative h-[340px] rounded-[3px] overflow-hidden group shadow-sm flex flex-col justify-end p-6 border border-slate-200/40 text-left">
                <!-- Background Image -->
                <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=600" alt="Deliver Milestones" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 z-0">
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-slate-950/20 z-10"></div>
                
                <!-- Overlaid Content -->
                <div class="relative z-20 space-y-2">
                    <span class="w-7 h-7 rounded-full bg-[#1952E1] text-white flex items-center justify-center font-bold text-xs">03</span>
                    <h4 class="font-extrabold text-white text-sm tracking-tight leading-tight">Deliver Milestones</h4>
                    <p class="text-[11px] text-slate-300 leading-relaxed font-medium">Share files and design details inside chat. Submit deliverables for client review stages.</p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="relative h-[340px] rounded-[3px] overflow-hidden group shadow-sm flex flex-col justify-end p-6 border border-slate-200/40 text-left">
                <!-- Background Image -->
                <img src="https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&q=80&w=600" alt="Direct Bank Transfers" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 z-0">
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-slate-950/20 z-10"></div>
                
                <!-- Overlaid Content -->
                <div class="relative z-20 space-y-2">
                    <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">04</span>
                    <h4 class="font-extrabold text-white text-sm tracking-tight leading-tight">Bank Withdrawals</h4>
                    <p class="text-[11px] text-slate-300 leading-relaxed font-medium">Withdraw released funds directly to local Nigerian bank accounts within 24 hours.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- Redesigned: Split Showcase for Core Benefits (Image + Clean 2x2 Grid) -->
    <section id="benefits" class="max-w-7xl mx-auto px-4 sm:px-6 mb-24">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <span class="text-xs font-black uppercase tracking-widest text-[#1952E1] bg-blue-50 border border-blue-200/80 px-3.5 py-1 rounded-full">
                Core Benefits
            </span>
            <h2 class="font-serif text-3xl md:text-5xl font-bold text-brand-dark mt-3 tracking-tight">
                Why Choose Scriptly Pro
            </h2>
            <p class="text-gray-600 text-sm mt-2">
                We eliminate payment risk, prevent lowball margins, and guarantee milestone deposits.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            
            <!-- Left Side: Large vertical image highlight card (Span 5) -->
            <div class="lg:col-span-5 relative min-h-[420px] rounded-[3px] overflow-hidden group shadow-sm flex flex-col justify-end p-8 text-left border border-slate-200/40">
                <!-- Background Image -->
                <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=800" alt="Ambitious Professional" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 z-0">
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-slate-950/20 z-10"></div>
                
                <!-- Content -->
                <div class="relative z-20 space-y-3">
                    <span class="inline-block bg-[#1952E1] text-white font-extrabold text-[9px] uppercase px-3 py-1 rounded-[3px] tracking-wider">
                        Built for excellence
                    </span>
                    <h3 class="font-serif text-xl sm:text-2xl font-bold text-white leading-tight">
                        Built For Serious Service Professionals
                    </h3>
                    <p class="text-xs text-slate-300 leading-relaxed font-medium">
                        Scriptly Pro guarantees that you work under protected platform regulations. From minimum categories budget floors to automatic milestone releases, your technical margins are locked in.
                    </p>
                </div>
            </div>

            <!-- Right Side: Clean 2x2 Grid of benefits (Span 7) -->
            <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-6">
                
                <!-- Benefit 1 -->
                <div class="bg-white rounded-[3px] p-6 border border-slate-200 hover:border-[#1952E1] transition-all flex flex-col justify-between shadow-2xs group">
                    <div class="space-y-4">
                        <div class="w-10 h-10 rounded-[3px] bg-blue-50 text-[#1952E1] border border-blue-100 flex items-center justify-center group-hover:bg-[#0A2342] group-hover:text-white transition-colors text-lg">
                            <i class="ph-bold ph-certificate"></i>
                        </div>
                        <h3 class="font-bold text-base text-brand-dark">Vetted Skill Badges</h3>
                        <p class="text-xs text-slate-500 leading-relaxed font-medium">Prove your expertise with pre-assessed categories tests and student matric validations that clients trust instantly.</p>
                    </div>
                </div>

                <!-- Benefit 2 -->
                <div class="bg-white rounded-[3px] p-6 border border-slate-200 hover:border-[#1952E1] transition-all flex flex-col justify-between shadow-2xs group">
                    <div class="space-y-4">
                        <div class="w-10 h-10 rounded-[3px] bg-blue-50 text-[#1952E1] border border-blue-100 flex items-center justify-center group-hover:bg-[#0A2342] group-hover:text-white transition-colors text-lg">
                            <i class="ph-bold ph-shield-check"></i>
                        </div>
                        <h3 class="font-bold text-base text-brand-dark">Milestone Escrow Vault</h3>
                        <p class="text-xs text-slate-500 leading-relaxed font-medium">Payments are deposited by clients upfront into platform escrows before you write a single line of code or start project drafts.</p>
                    </div>
                </div>

                <!-- Benefit 3 -->
                <div class="bg-white rounded-[3px] p-6 border border-slate-200 hover:border-[#1952E1] transition-all flex flex-col justify-between shadow-2xs group">
                    <div class="space-y-4">
                        <div class="w-10 h-10 rounded-[3px] bg-blue-50 text-[#1952E1] border border-blue-100 flex items-center justify-center group-hover:bg-[#0A2342] group-hover:text-white transition-colors text-lg">
                            <i class="ph-bold ph-trend-up"></i>
                        </div>
                        <h3 class="font-bold text-base text-brand-dark">Platform Price Floors</h3>
                        <p class="text-xs text-slate-500 leading-relaxed font-medium">Say goodbye to lowball rates. The platform automatically blocks any project requests or custom bids below category floors.</p>
                    </div>
                </div>

                <!-- Benefit 4 -->
                <div class="bg-white rounded-[3px] p-6 border border-slate-200 hover:border-[#1952E1] transition-all flex flex-col justify-between shadow-2xs group">
                    <div class="space-y-4">
                        <div class="w-10 h-10 rounded-[3px] bg-blue-50 text-[#1952E1] border border-blue-100 flex items-center justify-center group-hover:bg-[#0A2342] group-hover:text-white transition-colors text-lg">
                            <i class="ph-bold ph-bank"></i>
                        </div>
                        <h3 class="font-bold text-base text-brand-dark">24h Local Payouts</h3>
                        <p class="text-xs text-slate-500 leading-relaxed font-medium">Withdraw cleared earnings straight to OPay, Moniepoint, GTBank, Zenith, or UBA. Withdrawals clear in 24 hours.</p>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- Auto-scrolling Testimonials Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 my-24 overflow-hidden">
        <div class="text-center max-w-3xl mx-auto mb-8 space-y-3">
            <span class="inline-block px-3.5 py-1 bg-blue-50 border border-blue-200/80 text-[#1952E1] text-xs font-black rounded-full uppercase tracking-wider">
                Vetted Success
            </span>
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-brand-dark tracking-tight">
                Providers earning securely on Scriptly
            </h2>
        </div>

        <style>
            @keyframes scroll-testimonials {
                0% { transform: translateX(0); }
                100% { transform: translateX(-50%); }
            }
            .animate-scroll-testimonials {
                display: flex;
                width: max-content;
                animation: scroll-testimonials 35s linear infinite;
            }
            .animate-scroll-testimonials:hover {
                animation-play-state: paused;
            }
        </style>

        <!-- Continuous Scrolling Testimonials Marquee -->
        <div class="w-full overflow-hidden relative my-6 py-2">
            <div class="animate-scroll-testimonials flex gap-6">
                
                <!-- Loop 1 -->
                <?php foreach ($testimonials as $t): 
                    $stars = str_repeat('★', intval($t['rating'] ?? 5)) . str_repeat('☆', 5 - intval($t['rating'] ?? 5));
                ?>
                <div class="w-[310px] sm:w-[360px] flex-shrink-0 snap-start bg-white rounded-[3px] border border-gray-200 p-6 flex flex-col justify-between hover:border-[#1952E1] transition-all duration-300 shadow-2xs whitespace-normal">
                    <div class="space-y-3">
                        <div class="text-amber-500 text-xs font-bold leading-none"><?php echo $stars; ?></div>
                        <p class="text-slate-800 text-xs sm:text-sm leading-relaxed">
                            “<?php echo htmlspecialchars($t['content']); ?>”
                        </p>
                    </div>
                    <div class="flex items-center justify-between pt-5 border-t border-gray-100 mt-5">
                        <div class="flex items-center gap-3">
                            <img src="<?php echo htmlspecialchars($t['avatar_url'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=120'); ?>" alt="Avatar" class="w-9 h-9 rounded-full object-cover">
                            <div>
                                <h4 class="font-bold text-xs text-brand-dark"><?php echo htmlspecialchars($t['name']); ?></h4>
                                <p class="text-[10px] text-slate-400"><?php echo htmlspecialchars($t['role']); ?></p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-600">Verified Provider ✓</span>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Loop 2 (Duplicate for seamless marquee) -->
                <?php foreach ($testimonials as $t): 
                    $stars = str_repeat('★', intval($t['rating'] ?? 5)) . str_repeat('☆', 5 - intval($t['rating'] ?? 5));
                ?>
                <div class="w-[310px] sm:w-[360px] flex-shrink-0 snap-start bg-white rounded-[3px] border border-gray-200 p-6 flex flex-col justify-between hover:border-[#1952E1] transition-all duration-300 shadow-2xs whitespace-normal">
                    <div class="space-y-3">
                        <div class="text-amber-500 text-xs font-bold leading-none"><?php echo $stars; ?></div>
                        <p class="text-slate-800 text-xs sm:text-sm leading-relaxed">
                            “<?php echo htmlspecialchars($t['content']); ?>”
                        </p>
                    </div>
                    <div class="flex items-center justify-between pt-5 border-t border-gray-100 mt-5">
                        <div class="flex items-center gap-3">
                            <img src="<?php echo htmlspecialchars($t['avatar_url'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=120'); ?>" alt="Avatar" class="w-9 h-9 rounded-full object-cover">
                            <div>
                                <h4 class="font-bold text-xs text-brand-dark"><?php echo htmlspecialchars($t['name']); ?></h4>
                                <p class="text-[10px] text-slate-400"><?php echo htmlspecialchars($t['role']); ?></p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-600">Verified Provider ✓</span>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
        </div>

        <!-- Add Review Action Button -->
        <div class="text-center pt-6">
            <button type="button" id="btn-open-review" class="bg-brand-dark hover:bg-slate-800 text-white font-extrabold text-xs px-8 py-3.5 rounded-full transition-transform hover:scale-105 shadow-md flex items-center justify-center gap-2 mx-auto cursor-pointer">
                <i class="ph-bold ph-note-pencil text-sm"></i>
                <span>Drop a Review & Star Rating</span>
            </button>
        </div>
    </section>

    <!-- Review Modal Dialog -->
    <div id="review-modal" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-[3px] max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <span class="text-[10px] font-black uppercase text-blue-600 tracking-wider">Leave a Review</span>
                    <h3 class="text-sm sm:text-base font-black text-slate-900">Share Your Experience</h3>
                </div>
                <button type="button" id="btn-close-review" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form id="review-form" class="space-y-4">
                
                <!-- Name -->
                <div class="space-y-1">
                    <label for="review_name" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Full Name</label>
                    <input type="text" id="review_name" name="name" required placeholder="e.g. David Alao" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white placeholder-slate-400">
                </div>

                <!-- Professional Role / Student Status -->
                <div class="space-y-1">
                    <label for="review_role" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Professional Role / University Status</label>
                    <input type="text" id="review_role" name="role" required placeholder="e.g. Student Developer (UNILAG)" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white placeholder-slate-400">
                </div>

                <!-- Star Rating -->
                <div class="space-y-1">
                    <label for="review_rating" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Star Rating</label>
                    <select id="review_rating" name="rating" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white text-slate-700">
                        <option value="5" selected>★★★★★ (5 Stars)</option>
                        <option value="4">★★★★☆ (4 Stars)</option>
                        <option value="3">★★★☆☆ (3 Stars)</option>
                        <option value="2">★★☆☆☆ (2 Stars)</option>
                        <option value="1">★☆☆☆☆ (1 Star)</option>
                    </select>
                </div>

                <!-- Comments -->
                <div class="space-y-1">
                    <label for="review_content" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Review Comments</label>
                    <textarea id="review_content" name="content" required rows="4" placeholder="Share your experience working on Scriptly..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white placeholder-slate-400 resize-none"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" id="btn-cancel-review" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-[3px]">Cancel</button>
                    <button type="submit" id="btn-submit-review" class="px-6 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-black text-xs rounded-[3px] shadow-sm">Submit Review →</button>
                </div>
            </form>
        </div>
    </div>

    <!-- FAQ & Performance Section (Hostinger-Style 2-Column Dark Navy UI) -->
    <section id="faq" class="w-full bg-[#0A2342] text-white py-20 px-4 sm:px-6 lg:px-8 mb-24 relative overflow-hidden">
        <div class="max-w-7xl mx-auto space-y-16">
            
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-white tracking-tight">
                    Frequently Asked Questions
                </h2>
                <p class="text-slate-300 text-sm sm:text-base mt-3 leading-relaxed">
                    Quick guidelines on price floors, milestone safety, and verification timelines.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                
                <!-- Left: Interactive FAQ Accordion items -->
                <div class="lg:col-span-7 space-y-4 text-left">
                    <div class="bg-white/5 border border-white/10 p-5 rounded-[3px] faq-item cursor-pointer">
                        <div class="flex justify-between items-center select-none">
                            <h3 class="text-sm font-bold text-white">How do price floors work?</h3>
                            <span class="faq-toggle-icon text-slate-400 font-bold transition-transform duration-200 text-sm">+</span>
                        </div>
                        <p class="faq-answer text-xs text-slate-300 leading-relaxed font-medium mt-3 hidden">Scriptly protects pro margins by enforcing minimum budget sizes per category. For instance, clients cannot list software projects below ₦180,000 or writing projects below ₦50,000. Bids below these floors are blocked.</p>
                    </div>

                    <div class="bg-white/5 border border-white/10 p-5 rounded-[3px] faq-item cursor-pointer">
                        <div class="flex justify-between items-center select-none">
                            <h3 class="text-sm font-bold text-white">What does the skill assessment involve?</h3>
                            <span class="faq-toggle-icon text-slate-400 font-bold transition-transform duration-200 text-sm">+</span>
                        </div>
                        <p class="faq-answer text-xs text-slate-300 leading-relaxed font-medium mt-3 hidden">To qualify for the Pro network, you must complete a timed category-specific test (e.g. software programming, database queries, thesis formatting, or copy-editing) and score 80% or higher. You have 20 minutes to complete the test.</p>
                    </div>

                    <div class="bg-white/5 border border-white/10 p-5 rounded-[3px] faq-item cursor-pointer">
                        <div class="flex justify-between items-center select-none">
                            <h3 class="text-sm font-bold text-white">How does the payout process work?</h3>
                            <span class="faq-toggle-icon text-slate-400 font-bold transition-transform duration-200 text-sm">+</span>
                        </div>
                        <p class="faq-answer text-xs text-slate-300 leading-relaxed font-medium mt-3 hidden">Once a milestone is marked as approved/paid by the client, funds clear instantly to your available balance. You can request a withdrawal to any Nigerian bank (GTB, OPay, Zenith, Moniepoint) directly from your wallet dashboard. Payouts clear in under 24 hours.</p>
                    </div>

                    <div class="bg-white/5 border border-white/10 p-5 rounded-[3px] faq-item cursor-pointer">
                        <div class="flex justify-between items-center select-none">
                            <h3 class="text-sm font-bold text-white">What happens if a client doesn't release escrow?</h3>
                            <span class="faq-toggle-icon text-slate-400 font-bold transition-transform duration-200 text-sm">+</span>
                        </div>
                        <p class="faq-answer text-xs text-slate-300 leading-relaxed font-medium mt-3 hidden">Under platform rules, clients have a 10-day review window upon submission. If they don't request revisions or dispute the contract within 10 days, the system automatically releases the milestone funds to your wallet.</p>
                    </div>

                    <div class="bg-white/5 border border-white/10 p-5 rounded-[3px] faq-item cursor-pointer">
                        <div class="flex justify-between items-center select-none">
                            <h3 class="text-sm font-bold text-white">How long does credential verification take?</h3>
                            <span class="faq-toggle-icon text-slate-400 font-bold transition-transform duration-200 text-sm">+</span>
                        </div>
                        <p class="faq-answer text-xs text-slate-300 leading-relaxed font-medium mt-3 hidden">Once you pass the timed skill assessment, our moderators check your student matric details and NIN records. The manual verification review is typically completed within 24 to 48 hours.</p>
                    </div>
                </div>

                <!-- Right: 4 Metrics Cards (Grid adjusted to 4) -->
                <div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white/5 border border-white/10 p-6 rounded-[3px] space-y-2">
                        <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Fast Bank Payouts</span>
                        <h4 class="text-3xl font-black font-heading text-white">Under 24h</h4>
                        <p class="text-[10px] text-slate-400 font-medium">Average payment clearance timeframe to local accounts.</p>
                    </div>

                    <div class="bg-white/5 border border-white/10 p-6 rounded-[3px] space-y-2">
                        <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Average Pro Score</span>
                        <h4 class="text-3xl font-black font-heading text-white">88% Passing</h4>
                        <p class="text-[10px] text-slate-400 font-medium font-sans">Vetted technical accuracy benchmarks across tags.</p>
                    </div>

                    <div class="bg-white/5 border border-white/10 p-6 rounded-[3px] space-y-2">
                        <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Active Providers</span>
                        <h4 class="text-3xl font-black font-heading text-white">1,850+ Pros</h4>
                        <p class="text-[10px] text-slate-400 font-medium">Verified students and professional freelancers onboarded.</p>
                    </div>

                    <div class="bg-white/5 border border-white/10 p-6 rounded-[3px] space-y-2">
                        <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Guaranteed Escrow</span>
                        <h4 class="text-3xl font-black font-heading text-white">100% Safe</h4>
                        <p class="text-[10px] text-slate-400 font-medium">Deposited contract milestones locked securely.</p>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- CTA Section that Blends into Footer -->
    <section class="bg-gradient-to-b from-[#f8f7f5] to-[#07192e] pt-24 pb-16 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="font-serif text-3xl sm:text-5xl font-bold text-white mb-6">Ready to scale your earning potential?</h2>
            <p class="text-gray-300 text-lg mb-10 max-w-2xl mx-auto">Join thousands of verified professionals who trust Scriptly to connect them with serious clients, protect their payments, and eliminate the hustle.</p>
            <a href="signup.php" class="inline-block bg-white text-[#07192e] font-bold text-lg px-8 py-4 rounded-full shadow-lg hover:scale-105 transition-transform duration-300">
                Apply as a Professional Now
            </a>
        </div>
    </section>

    <!-- Footer Component (Matching root footer layout structure) -->
    <footer class="bg-[#07192e] text-gray-400 pt-8 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-12">
                
                <div class="lg:col-span-2">
                    <a href="./" class="flex items-center space-x-2 text-2xl font-extrabold tracking-tight text-white mb-4 group">
                        <svg class="w-6 h-6 text-blue-400 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18.178 8c5.096 0 5.096 8 0 8-2.69 0-4.7-2.115-6.178-4-1.478-1.885-3.488-4-6.178-4-5.096 0-5.096 8 0 8 2.69 0 4.7-2.115 6.178-4 1.478-1.885-3.488-4 6.178-4z"></path>
                        </svg>
                        <span>Scriptly</span>
                        <span class="inline-flex items-center justify-center w-4 h-4 bg-blue-600 text-white rounded-full text-[9px] font-bold">✓</span>
                    </a>
                    <p class="text-xs text-gray-400 leading-relaxed max-w-sm mb-6">
                        Scriptly is a web-based verified professional service marketplace & project management platform connecting pre-assessed talent with ambitious clients.
                    </p>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Platform</h4>
                    <ul class="space-y-2.5 text-xs font-medium">
                        <li><a href="../services" class="hover:text-white transition-colors">Services</a></li>
                        <li><a href="../#how-it-works" class="hover:text-white transition-colors">How It Works</a></li>
                        <li><a href="../#why-us" class="hover:text-white transition-colors">Why Choose Us</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Workspace</h4>
                    <ul class="space-y-2.5 text-xs font-medium">
                        <li><a href="login.php" class="hover:text-white transition-colors">Pro Login</a></li>
                        <li><a href="signup.php" class="hover:text-white transition-colors">Become a Professional</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Company</h4>
                    <ul class="space-y-2.5 text-xs font-medium">
                        <li><a href="../faq" class="hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="../terms" class="hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="../privacy" class="hover:text-white transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>

            </div>

            <div class="border-t border-white/10 pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500">
                <p>© <?php echo date('Y'); ?> Scriptly Platform Inc. All rights reserved.</p>
                <p class="mt-2 sm:mt-0 font-medium">Developed with excellence by <span class="text-white font-bold">Scriptly Team</span></p>
            </div>
        </div>
    </footer>

    <!-- Scriptly Custom Alerts & Toast Subsystem -->
    <script src="../assets/js/scriptly-alerts.js"></script>

    <script>
        // Scroll-responsive Navigation
        const nav = document.getElementById('main-nav');
        const navLinksGroup = document.querySelectorAll('.nav-links');
        const logoText = document.querySelectorAll('.logo-text');
        const signUpBtn = document.querySelector('.sign-up-btn');

        function updateNav() {
            if(!nav) return;
            const isMobile = window.innerWidth < 1024;
            if (window.scrollY > 50 || isMobile) {
                nav.classList.remove('bg-transparent', 'text-white', 'border-transparent', 'backdrop-blur-none');
                nav.classList.add('bg-[#f8f7f5]/90', 'backdrop-blur-md', 'text-brand-dark', 'border-b', 'border-gray-200/60');
                
                navLinksGroup.forEach(group => { group.classList.remove('text-white'); group.classList.add('text-gray-600'); });
                logoText.forEach(el => { el.classList.remove('text-white'); el.classList.add('text-brand-dark'); });

                if(signUpBtn) {
                    signUpBtn.classList.remove('bg-white/20', 'text-white', 'border-white/30', 'hover:bg-white/30');
                    signUpBtn.classList.add('bg-brand-dark', 'text-white', 'hover:bg-slate-800');
                }
            } else {
                nav.classList.add('bg-transparent', 'text-white', 'border-transparent', 'backdrop-blur-none');
                nav.classList.remove('bg-[#f8f7f5]/90', 'backdrop-blur-md', 'text-brand-dark', 'border-b', 'border-gray-200/60');
                
                navLinksGroup.forEach(group => { group.classList.add('text-white'); group.classList.remove('text-gray-600'); });
                logoText.forEach(el => { el.classList.add('text-white'); el.classList.remove('text-brand-dark'); });

                if(signUpBtn) {
                    signUpBtn.classList.add('bg-white/20', 'text-white', 'border-white/30', 'hover:bg-white/30');
                    signUpBtn.classList.remove('bg-brand-dark', 'hover:bg-slate-800');
                }
            }
        }
        
        window.addEventListener('scroll', updateNav);
        window.addEventListener('resize', updateNav);
        updateNav();

        // Global Mobile Drawer Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('-translate-y-full');
            });
        }

        // FAQ Accordion Click Toggle Handler
        document.querySelectorAll('.faq-item').forEach(item => {
            item.addEventListener('click', () => {
                const answer = item.querySelector('.faq-answer');
                const icon = item.querySelector('.faq-toggle-icon');
                const isHidden = answer.classList.contains('hidden');
                
                // Toggle Answer
                answer.classList.toggle('hidden', !isHidden);
                
                // Toggle Icon
                icon.textContent = isHidden ? '−' : '+';
                icon.classList.toggle('text-white', isHidden);
                icon.classList.toggle('text-slate-400', !isHidden);
            });
        });

        // Review Modal Controls
        const reviewModal = document.getElementById('review-modal');
        const btnOpenReview = document.getElementById('btn-open-review');
        const btnCloseReview = document.getElementById('btn-close-review');
        const btnCancelReview = document.getElementById('btn-cancel-review');
        const reviewForm = document.getElementById('review-form');
        const btnSubmitReview = document.getElementById('btn-submit-review');

        if (btnOpenReview) {
            btnOpenReview.addEventListener('click', () => {
                reviewModal.classList.remove('hidden');
            });
        }

        const hideReviewModal = () => {
            reviewModal.classList.add('hidden');
        };

        if (btnCloseReview) btnCloseReview.addEventListener('click', hideReviewModal);
        if (btnCancelReview) btnCancelReview.addEventListener('click', hideReviewModal);

        if (reviewForm) {
            reviewForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                btnSubmitReview.textContent = 'Submitting Review...';
                btnSubmitReview.disabled = true;

                const formData = {
                    name: document.getElementById('review_name').value.trim(),
                    role: document.getElementById('review_role').value.trim(),
                    rating: parseInt(document.getElementById('review_rating').value),
                    content: document.getElementById('review_content').value.trim()
                };

                try {
                    const response = await fetch('../api/provider/submit-testimonial.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(formData)
                    });

                    const data = await response.json();
                    if (data.success) {
                        ScriptlyToast.success('Thank you! Your testimonial has been posted.', 'Review Published!');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        ScriptlyToast.error(data.message || 'Error publishing review.');
                        btnSubmitReview.textContent = 'Submit Review →';
                        btnSubmitReview.disabled = false;
                    }
                } catch (err) {
                    ScriptlyToast.error('Network error. Please try again.');
                    btnSubmitReview.textContent = 'Submit Review →';
                    btnSubmitReview.disabled = false;
                }
            });
        }

        // Fade-out Preloader
        window.addEventListener('load', () => {
            const preloader = document.getElementById('page-preloader');
            if (preloader) {
                preloader.style.opacity = '0';
                setTimeout(() => preloader.style.display = 'none', 300);
            }
        });
    </script>
</body>
</html>
