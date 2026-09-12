<?php
require_once __DIR__ . '/../config/brand.php';
// Default SEO Page Metadata
$page_title = isset($page_title) ? $page_title : "Scriptly - Verified Professional Service Marketplace";
$page_description = isset($page_description) ? $page_description : "Scriptly connects clients with pre-assessed, verified professionals. Milestone escrow protection, real-time collaboration, and quality guarantees.";
$active_page = isset($active_page) ? $active_page : "home";
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$canonical_url = $protocol . "://" . $host . ($_SERVER['REQUEST_URI'] ?? '');
$og_image_url = $protocol . "://" . $host . (strpos($_SERVER['REQUEST_URI'] ?? '', '/creda') !== false ? '/creda/assets/hero_bg.jpg' : '/assets/hero_bg.jpg');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <!-- Primary SEO Meta Tags -->
    <meta name="title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="keywords" content="Scriptly, verified professionals, milestone escrow, freelance marketplace, student projects, final year project writing, software developers, SPSS analysis, turnitin clean code">
    <meta name="author" content="Scriptly Team">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta name="theme-color" content="#1952E1">
    
    <!-- Favicon / Site Icon -->
    <link rel="icon" type="image/png" href="<?php echo getLogoIconUrl() ?: ((strpos($_SERVER['REQUEST_URI'] ?? '', '/creda') !== false ? '/creda' : '') . '/assets/brand/logo-icon.png'); ?>">

    <!-- Open Graph / Facebook & WhatsApp SEO Meta Tags -->
    <meta property="og:site_name" content="Scriptly">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($og_image_url); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@ScriptlyApp">
    <meta name="twitter:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($og_image_url); ?>">
    
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Load Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,800&display=swap" rel="stylesheet">
    
    <!-- Scriptly Custom Alerts & Toast Stylesheet -->
    <link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/creda') !== false ? '/creda' : ''); ?>/assets/css/scriptly-alerts.css">
    
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
        /* Hide scrollbars utility */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Fancy Custom Vertical Y Scrollbar */
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

        /* Glass Tag utility */
        .glass-tag {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

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
    </style>
</head>
<body class="bg-brand-bg text-brand-dark font-sans antialiased selection:bg-blue-200 selection:text-blue-900">

    <!-- Header / Navbar -->
    <nav id="main-nav" class="fixed top-0 w-full px-4 sm:px-12 py-4 z-50 transition-all duration-300 <?php echo ($active_page === 'home') ? 'bg-[#f8f7f5]/90 lg:bg-transparent backdrop-blur-md lg:backdrop-blur-none border-b lg:border-transparent border-gray-200/60 text-brand-dark lg:text-white' : 'bg-[#f8f7f5]/90 backdrop-blur-md text-brand-dark border-b border-gray-200/60'; ?>">
        <div class="max-w-[1400px] mx-auto flex items-center justify-between">
            
            <!-- Left Nav (Desktop) -->
            <div class="hidden lg:flex items-center space-x-8 text-[15px] font-semibold w-1/3 nav-links <?php echo ($active_page === 'home') ? 'text-white' : 'text-gray-600'; ?>">
                <a href="./#why-us" class="hover:opacity-75 transition-colors">Why Choose Us</a>
                <a href="services" class="hover:opacity-75 transition-colors <?php echo $active_page==='services'?'font-bold':''; ?>">Services</a>
                <a href="./#how-it-works" class="hover:opacity-75 transition-colors">How it Works</a>
            </div>

            <!-- Logo (Exact Center of Screen) -->
            <div class="flex justify-center items-center w-full lg:w-1/3">
                <a href="./" class="group">
                    <?php echo renderLogoFull('flex items-center space-x-2 text-2xl font-extrabold tracking-tight group logo-text ' . (($active_page === 'home') ? 'text-brand-dark lg:text-white' : 'text-brand-dark'), 'w-6 h-6'); ?>
                </a>
            </div>

            <!-- Right Nav (Desktop) -->
            <div class="hidden lg:flex items-center justify-end space-x-4 text-[15px] font-semibold w-1/3 nav-links <?php echo ($active_page === 'home') ? 'text-white' : 'text-gray-600'; ?>">
                <a href="login" class="hover:opacity-75 transition-colors px-3 py-2">Log in</a>
                <a href="signup" class="bg-white/20 backdrop-blur-sm px-5 py-2.5 rounded-full hover:bg-white/30 transition-all border border-white/30 sign-up-btn <?php echo ($active_page === 'home') ? 'text-white' : 'text-brand-dark bg-gray-200 border-none hover:bg-gray-300'; ?>">Sign up</a>
                <a href="signup?role=client" class="bg-[#1952E1] text-white px-5 py-2.5 rounded-full hover:bg-blue-700 transition-all font-bold">Request Service</a>
            </div>

            <!-- Mobile Hamburger Menu (Left) -->
            <div class="lg:hidden absolute left-4 top-1/2 -translate-y-1/2 flex items-center">
                <button id="mobile-menu-btn" class="focus:outline-none p-2 -ml-2 text-brand-dark">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile User Icon (Right) -->
            <div class="lg:hidden absolute right-4 top-1/2 -translate-y-1/2 flex items-center">
                 <a href="login" class="text-brand-dark focus:outline-none p-2">
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
            <a href="./#why-us" class="hover:text-blue-600">Why Choose Us</a>
            <a href="services" class="hover:text-blue-600">Services</a>
            <a href="./#how-it-works" class="hover:text-blue-600">How it Works</a>
            <a href="./#professionals" class="hover:text-blue-600">Explore Professionals</a>
            <a href="faq" class="hover:text-blue-600">FAQ</a>
            <div class="h-px w-full bg-gray-200 my-2"></div>
            <a href="login" class="hover:text-blue-600">Log in</a>
            <a href="signup" class="bg-gray-200 text-brand-dark text-center px-5 py-3 rounded-full">Sign up</a>
            <a href="signup?role=client" class="bg-brand-dark text-white text-center px-5 py-3 rounded-full font-bold">Request Service</a>
        </div>
    </div>

<?php if(isset($breadcrumb)): ?>
    <!-- Reusable SEO Breadcrumb Header Banner with Background Image -->
    <section class="bg-[#0A2342] text-white py-10 sm:py-14 px-4 sm:px-6 relative overflow-hidden text-center">
        <!-- Background Image Asset -->
        <?php if(!empty($breadcrumb['bg_image'])): ?>
        <div class="absolute inset-0 z-0 opacity-40 mix-blend-multiply pointer-events-none">
            <img src="<?php echo htmlspecialchars($breadcrumb['bg_image']); ?>" alt="Breadcrumb Header Background" class="w-full h-full object-cover">
        </div>
        <?php endif; ?>
        <div class="absolute inset-0 z-0 bg-gradient-to-b from-[#0A2342]/80 via-[#0A2342]/90 to-[#0A2342] pointer-events-none"></div>

        <div class="max-w-4xl mx-auto relative z-10">
            <!-- SEO Breadcrumb Links -->
            <nav class="flex flex-wrap justify-center items-center gap-1.5 text-[11px] sm:text-xs font-bold uppercase tracking-wider text-[#ffda79] mb-3">
                <a href="./" class="hover:underline">Home</a>
                <span class="text-white/40">/</span>
                <?php if(!empty($breadcrumb['category'])): ?>
                <span class="text-gray-300"><?php echo htmlspecialchars($breadcrumb['category']); ?></span>
                <?php endif; ?>
            </nav>
            <h1 class="font-serif text-2xl sm:text-4xl md:text-5xl font-bold tracking-tight mb-2 sm:mb-3 leading-tight"><?php echo htmlspecialchars($breadcrumb['title']); ?></h1>
            <?php if(!empty($breadcrumb['subtitle'])): ?>
            <p class="text-gray-300 text-xs sm:text-sm md:text-base max-w-2xl mx-auto leading-relaxed"><?php echo htmlspecialchars($breadcrumb['subtitle']); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Schema.org JSON-LD Breadcrumb List -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [{
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/scriptly/"; ?>"
      },{
        "@type": "ListItem",
        "position": 2,
        "name": "<?php echo htmlspecialchars($breadcrumb['title']); ?>",
        "item": "<?php echo htmlspecialchars($canonical_url); ?>"
      }]
    }
    </script>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nav = document.getElementById('main-nav');
        const navLinks = document.querySelectorAll('.nav-links');
        const logoText = document.querySelector('.logo-text');
        const signUpBtn = document.querySelector('.sign-up-btn');
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const isActiveHome = <?php echo ($active_page === 'home') ? 'true' : 'false'; ?>;

        if (isActiveHome) {
            function updateNav() {
                const isMobile = window.innerWidth < 1024; // Tailwind lg breakpoint
                if (window.scrollY > 50 || isMobile) {
                    nav.classList.remove('lg:bg-transparent', 'lg:text-white', 'lg:border-transparent', 'lg:backdrop-blur-none');
                    nav.classList.add('bg-[#f8f7f5]/90', 'backdrop-blur-md', 'text-brand-dark', 'border-b', 'border-gray-200/60');
                    
                    navLinks.forEach(link => {
                        link.classList.remove('text-white');
                        link.classList.add('text-gray-600');
                    });

                    if(logoText) {
                        logoText.classList.remove('lg:text-white');
                        logoText.classList.add('text-brand-dark');
                    }
                    
                    if (signUpBtn) {
                        signUpBtn.classList.remove('bg-white/20', 'text-white', 'border', 'border-white/30', 'hover:bg-white/30');
                        signUpBtn.classList.add('bg-gray-200', 'text-brand-dark', 'hover:bg-gray-300');
                    }
                } else {
                    nav.classList.add('lg:bg-transparent', 'lg:text-white', 'lg:border-transparent', 'lg:backdrop-blur-none');
                    nav.classList.remove('bg-[#f8f7f5]/90', 'backdrop-blur-md', 'text-brand-dark', 'border-b', 'border-gray-200/60');

                    navLinks.forEach(link => {
                        link.classList.add('text-white');
                        link.classList.remove('text-gray-600');
                    });

                    if(logoText) {
                        logoText.classList.add('lg:text-white');
                        logoText.classList.remove('text-brand-dark');
                    }
                    
                    if (signUpBtn) {
                        signUpBtn.classList.add('bg-white/20', 'text-white', 'border', 'border-white/30', 'hover:bg-white/30');
                        signUpBtn.classList.remove('bg-gray-200', 'text-brand-dark', 'hover:bg-gray-300');
                    }
                }
            }
            
            window.addEventListener('scroll', updateNav);
            window.addEventListener('resize', updateNav);
            // Run on load
            updateNav();
        }
    });
</script>
