<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if no email session
if (empty($_SESSION['user_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';
$db = getDBConnection();
$stmt = $db->prepare("SELECT onboarding_completed, is_verified_pro, assessment_status, primary_role, full_name FROM users WHERE email = ?");
$stmt->execute([$_SESSION['user_email']]);
$db_user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($db_user) {
    $_SESSION['onboarding_completed'] = (bool)$db_user['onboarding_completed'];
    $_SESSION['user_role'] = $db_user['primary_role'];
    $_SESSION['assessment_status'] = $db_user['assessment_status'];
    $_SESSION['user_name'] = $db_user['full_name'];
}

// If onboarding is completed, redirect to their respective portals
if (isset($_SESSION['onboarding_completed']) && $_SESSION['onboarding_completed']) {
    require_once __DIR__ . '/config/brand.php';
    if ($_SESSION['user_role'] === 'provider') {
        if (($_SESSION['assessment_status'] ?? 'not_started') === 'passed') {
            header('Location: ' . getPortalUrl('provider', 'app/pending-verification.php'));
        } else {
            header('Location: ' . getPortalUrl('provider', 'app/assessment.php'));
        }
    } else {
        header('Location: app/index.php');
    }
    exit;
}

$user_name = $_SESSION['user_name'] ?? 'Member';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Onboarding - Scriptly Verified Marketplace</title>
    
    <!-- Primary SEO Meta Tags -->
    <meta name="title" content="Account Onboarding - Scriptly Verified Marketplace">
    <meta name="description" content="Complete your profile details, select your account type, review platform rules, and take a quick tour to enter Scriptly.">
    <meta name="robots" content="noindex, nofollow">

    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Load Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800&display=swap" rel="stylesheet">
    
    <!-- Scriptly Custom Alerts & Toast Stylesheet -->
    <link rel="stylesheet" href="assets/css/scriptly-alerts.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        brand: {
                            bg: '#f8f7f5',
                            dark: '#0A2342',
                            accent: '#ffda79',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f8f7f5; }
        ::-webkit-scrollbar-thumb { background: #0A2342; border-radius: 4px; border: 2px solid #f8f7f5; }
        ::-webkit-scrollbar-thumb:hover { background: #2563eb; }
    </style>
</head>
<body class="bg-brand-bg text-brand-dark font-sans antialiased selection:bg-blue-200 selection:text-blue-900">

    <!-- 2-Column Split Screen Auth Layout (No Public Header/Footer) -->
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12 overflow-hidden">
        
        <!-- LEFT COLUMN: Visual Brand Showcase (lg:col-span-5) -->
        <aside class="lg:col-span-5 hidden lg:flex flex-col justify-between bg-[#0A2342] text-white p-8 xl:p-10 relative overflow-hidden">
            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=1200" alt="Scriptly Onboarding Background" class="absolute inset-0 w-full h-full object-cover opacity-30 mix-blend-multiply">
            <div class="absolute inset-0 bg-gradient-to-b from-[#0A2342]/90 via-[#0A2342]/80 to-[#0A2342] pointer-events-none"></div>

            <div class="relative z-10">
                <a href="./" class="flex items-center space-x-2 text-2xl font-extrabold tracking-tight text-white">
                    <span>Scriptly</span>
                    <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-600 text-white rounded-full text-[10px] font-bold">✓</span>
                </a>
            </div>

            <div class="relative z-10 max-w-sm space-y-5">
                <span class="bg-[#ffda79] text-brand-dark font-extrabold text-[11px] px-3 py-1 rounded-full uppercase tracking-wider shadow">
                    Onboarding Wizard
                </span>
                <h2 class="font-serif text-3xl font-bold tracking-tight text-white leading-tight">
                    Welcome to Scriptly, <?php echo htmlspecialchars($user_name); ?>!
                </h2>
                <blockquote class="bg-white/10 backdrop-blur-md rounded-[3px] p-4 border border-white/15 text-xs leading-relaxed text-gray-200">
                    "Complete your basic info, accept platform rules, and take a quick 1-minute tour to get started with verified marketplace features."
                    <footer class="mt-2 text-[11px] font-bold text-[#ffda79]">— Scriptly Onboarding</footer>
                </blockquote>
            </div>

            <div class="relative z-10 pt-4 border-t border-white/15 flex items-center justify-between text-xs text-gray-400 font-medium">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Account Active</span>
                </div>
                <span id="left-step-indicator">Step 1 of 3</span>
            </div>
        </aside>

        <!-- RIGHT COLUMN: 3-Step Wizard Workspace (lg:col-span-7) -->
        <main class="lg:col-span-7 flex flex-col justify-between p-6 sm:p-10 bg-white overflow-y-auto">
            


            <!-- Compact Form Wrapper (max-w-md) -->
            <div class="max-w-md w-full mx-auto my-auto space-y-5">
                
                <!-- Top Back Arrow Button (Shown on Step 2 and Step 3) -->
                <button type="button" id="top-back-btn" class="hidden items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-brand-dark transition-colors mb-3 cursor-pointer group">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 group-hover:bg-gray-200 text-brand-dark transition-colors font-bold">←</span>
                    <span>Back</span>
                </button>

                <!-- Onboarding Multi-Step Form -->
                <form id="onboarding-form" novalidate>
                    
                    <!-- ==================== STEP 1: PHONE, ADDRESS & ACCOUNT TYPE ==================== -->
                    <div id="step-1-container" class="space-y-4">
                        <div>
                            <h1 class="font-serif text-2xl font-bold text-brand-dark tracking-tight">Basic Account Info</h1>
                            <p class="text-xs text-gray-500 mt-1">Please provide your mobile phone number, location, and account type.</p>
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone_number" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Mobile Phone Number</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </span>
                                <input type="tel" id="phone_number" name="phone_number" autocomplete="tel" required placeholder="e.g. +234 801 234 5678" class="w-full pl-10 pr-4 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium transition-colors">
                            </div>
                        </div>

                        <!-- Physical Address / Location -->
                        <div>
                            <label for="address" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">City / Physical Address</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </span>
                                <input type="text" id="address" name="address" autocomplete="street-address" required placeholder="e.g. Victoria Island, Lagos, Nigeria" class="w-full pl-10 pr-4 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium transition-colors">
                            </div>
                        </div>

                        <!-- Entity Type Selection -->
                        <div>
                            <label for="entity_type" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Entity Type</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </span>
                                <select id="entity_type" name="entity_type" required class="w-full pl-10 pr-4 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium transition-colors appearance-none bg-white">
                                    <option value="" disabled selected>Select your entity type</option>
                                    <option value="student">Student</option>
                                    <option value="business">Business</option>
                                    <option value="corporation">Corporation</option>
                                </select>
                                <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </span>
                            </div>
                        </div>

                        <!-- Primary Role hidden and dynamically set -->
                        <input type="hidden" name="primary_role" value="client">
                        
                        <button type="button" id="btn-step-1-next" class="w-full py-3.5 bg-brand-dark text-white rounded-full font-bold text-xs hover:bg-slate-800 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-md mt-2">
                            <span>Continue to Profile Details →</span>
                        </button>
                    </div>

                    <!-- ==================== STEP 2: PROFILE DETAILS (DYNAMIC BY ENTITY TYPE) ==================== -->
                    <div id="step-2-container" class="space-y-4 hidden">
                        <!-- Student Profile Fields -->
                        <div id="student-fields-step2" class="space-y-4 hidden">
                            <div>
                                <h1 class="font-serif text-2xl font-bold text-brand-dark tracking-tight">Academic & Institution Info</h1>
                                <p class="text-xs text-gray-500 mt-1">Please provide details about your institution and academic standing.</p>
                            </div>

                            <!-- Student ID -->
                            <div>
                                <label for="student_id" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Student ID / Matric Number</label>
                                <input type="text" id="student_id" name="student_id" placeholder="e.g. 190407082" class="w-full px-3.5 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium bg-white">
                            </div>

                            <!-- Institution -->
                            <div>
                                <label for="institution" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Institution / University</label>
                                <input type="text" id="institution" name="institution" list="institutions-list" placeholder="Search or type your university..." class="w-full px-3.5 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium bg-white">
                                <datalist id="institutions-list">
                                    <option value="University of Lagos (UNILAG)">
                                    <option value="Covenant University">
                                    <option value="University of Nigeria, Nsukka (UNN)">
                                    <option value="Federal University of Technology, Owerri (FUTO)">
                                    <option value="Ahmadu Bello University (ABU)">
                                    <option value="Obafemi Awolowo University (OAU)">
                                </datalist>
                            </div>

                            <!-- Department -->
                            <div>
                                <label for="department" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Department / Course of Study</label>
                                <input type="text" id="department" name="department" placeholder="e.g. Computer Science" class="w-full px-3.5 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium bg-white">
                            </div>
                        </div>

                        <!-- Business Profile Fields -->
                        <div id="business-fields-step2" class="space-y-4 hidden">
                            <div>
                                <h1 class="font-serif text-2xl font-bold text-brand-dark tracking-tight">Business Profile Details</h1>
                                <p class="text-xs text-gray-500 mt-1">Please provide basic business profile and industry category details.</p>
                            </div>

                            <!-- Business Name -->
                            <div>
                                <label for="business_name" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Registered Business Name</label>
                                <input type="text" id="business_name" name="business_name" placeholder="e.g. Alao Digital Services" class="w-full px-3.5 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium bg-white">
                            </div>

                            <!-- CAC Number -->
                            <div>
                                <label for="rc_number" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">CAC Registration / BN Number</label>
                                <input type="text" id="rc_number" name="rc_number" placeholder="e.g. BN 1234567" class="w-full px-3.5 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium bg-white">
                            </div>

                            <!-- Industry -->
                            <div>
                                <label for="industry" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Industry / Sector</label>
                                <select id="industry" name="industry" class="w-full px-3.5 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium bg-white">
                                    <option value="" disabled selected>Select business industry</option>
                                    <option value="Technology & IT">Technology & IT</option>
                                    <option value="Education & E-learning">Education & E-learning</option>
                                    <option value="Marketing & Creative">Marketing & Creative</option>
                                    <option value="Healthcare & Wellness">Healthcare & Wellness</option>
                                    <option value="Finance & Consulting">Finance & Consulting</option>
                                    <option value="Retail & E-commerce">Retail & E-commerce</option>
                                </select>
                            </div>
                        </div>

                        <!-- Corporation Profile Fields -->
                        <div id="corporation-fields-step2" class="space-y-4 hidden">
                            <div>
                                <h1 class="font-serif text-2xl font-bold text-brand-dark tracking-tight">Corporate Entity Details</h1>
                                <p class="text-xs text-gray-500 mt-1">Please provide corporate identification and contact information.</p>
                            </div>

                            <!-- Company Name -->
                            <div>
                                <label for="corp_company_name" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Company Name</label>
                                <input type="text" id="corp_company_name" name="corp_company_name" placeholder="e.g. Cliniconnect Solutions Ltd" class="w-full px-3.5 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium bg-white">
                            </div>

                            <!-- Company RC Number -->
                            <div>
                                <label for="corp_rc_number" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Corporate RC Number</label>
                                <input type="text" id="corp_rc_number" name="corp_rc_number" placeholder="e.g. RC 987654" class="w-full px-3.5 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium bg-white">
                            </div>

                            <!-- Company Website -->
                            <div>
                                <label for="company_website" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Company Website URL</label>
                                <input type="url" id="company_website" name="company_website" placeholder="e.g. https://example.com" class="w-full px-3.5 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium bg-white">
                            </div>
                        </div>

                        <button type="button" id="btn-step-2-next" class="w-full py-3.5 bg-brand-dark text-white rounded-full font-bold text-xs hover:bg-slate-800 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-md mt-2">
                            <span>Continue to Platform Rules →</span>
                        </button>
                    </div>

                    <!-- ==================== STEP 3: RULES & REGULATIONS ==================== -->
                    <div id="step-3-container" class="space-y-4 hidden">
                        <div>
                            <h1 class="font-serif text-2xl font-bold text-brand-dark tracking-tight">Rules & Regulations</h1>
                            <p class="text-xs text-gray-500 mt-1">Please review Scriptly's Code of Conduct and Escrow Guidelines before proceeding.</p>
                        </div>

                        <!-- Platform Rules Accordion Box -->
                        <div class="bg-gray-50 border border-gray-200 rounded-[3px] p-3.5 text-xs space-y-3 max-h-64 overflow-y-auto">
                            
                            <!-- Rule 1: On-Platform Escrow & Optional Milestones -->
                            <div class="border-b border-gray-200 pb-2.5">
                                <span class="font-extrabold text-brand-dark text-xs flex items-center gap-1.5">
                                    <span class="w-4 h-4 bg-blue-600 text-white rounded-full text-[10px] inline-flex items-center justify-center font-bold">1</span>
                                    100% On-Platform Escrow Deposits
                                </span>
                                <p class="text-[11px] text-gray-600 mt-1 leading-relaxed">Before work begins, clients make a full project deposit into Scriptly Escrow. Stage-by-stage <strong>Milestones</strong> (e.g. paying per page/phase) are an optional choice for larger tasks. Off-platform payments (wire transfer, direct bank, crypto, PayPal) are strictly prohibited and result in permanent account termination.</p>
                            </div>

                            <!-- Rule 2: 10-Day Review, Corrections & Auto-Release Window -->
                            <div class="border-b border-gray-200 pb-2.5">
                                <span class="font-extrabold text-brand-dark text-xs flex items-center gap-1.5">
                                    <span class="w-4 h-4 bg-blue-600 text-white rounded-full text-[10px] inline-flex items-center justify-center font-bold">2</span>
                                    10-Day Review, Corrections & Auto-Release
                                </span>
                                <p class="text-[11px] text-gray-600 mt-1 leading-relaxed">Upon project submission, clients have a <strong>10-day review window</strong> to request revisions/corrections or approve the work. If 10 days pass with no response or dispute, the deal automatically completes and escrow funds release to the provider.</p>
                            </div>

                            <!-- Rule 3: Communication & File Ownership -->
                            <div class="border-b border-gray-200 pb-2.5">
                                <span class="font-extrabold text-brand-dark text-xs flex items-center gap-1.5">
                                    <span class="w-4 h-4 bg-blue-600 text-white rounded-full text-[10px] inline-flex items-center justify-center font-bold">3</span>
                                    On-Platform Chat & IP Rights Transfer
                                </span>
                                <p class="text-[11px] text-gray-600 mt-1 leading-relaxed">All project messaging, design revisions, and file deliveries must occur inside Scriptly Chat. Upon final payment approval, full intellectual property (IP) rights transfer directly to the client.</p>
                            </div>

                            <!-- Rule 4: Pre-Assessed Talent Integrity -->
                            <div class="border-b border-gray-200 pb-2.5">
                                <span class="font-extrabold text-brand-dark text-xs flex items-center gap-1.5">
                                    <span class="w-4 h-4 bg-blue-600 text-white rounded-full text-[10px] inline-flex items-center justify-center font-bold">4</span>
                                    Skill Quiz Verification & Badges
                                </span>
                                <p class="text-[11px] text-gray-600 mt-1 leading-relaxed">Service providers must complete timed technical quiz assessments (80%+ score) to maintain verified status. Impersonation, account sharing, or fake quiz taking is strictly banned.</p>
                            </div>

                            <!-- Rule 5: Dispute Arbitration -->
                            <div>
                                <span class="font-extrabold text-brand-dark text-xs flex items-center gap-1.5">
                                    <span class="w-4 h-4 bg-rose-600 text-white rounded-full text-[10px] inline-flex items-center justify-center font-bold">5</span>
                                    Fair Arbitration & Anti-Harassment
                                </span>
                                <p class="text-[11px] text-gray-600 mt-1 leading-relaxed">Scriptly arbitration handles project disputes impartially based strictly on recorded chat messages and submission evidence. Professional, respectful conduct is required at all times.</p>
                            </div>

                        </div>

                        <!-- Agreement Checkbox -->
                        <div class="flex items-start gap-2 pt-1">
                            <input type="checkbox" id="accepted_rules" name="accepted_rules" class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" required>
                            <label for="accepted_rules" class="text-[11px] text-gray-700 font-medium leading-normal">
                                I have read, understood, and agree to abide by Scriptly's <strong class="text-brand-dark">Platform Rules & Code of Conduct</strong>.
                            </label>
                        </div>

                        <button type="button" id="btn-step-3-next" class="w-full py-3.5 bg-brand-dark text-white rounded-full font-bold text-xs hover:bg-slate-800 transition-all cursor-pointer shadow-md mt-2">
                            <span>Continue to Quick Tour →</span>
                        </button>
                    </div>

                    <!-- ==================== STEP 4: INTERACTIVE PRODUCT TOUR ==================== -->
                    <div id="step-4-container" class="space-y-4 hidden">
                        <div>
                            <h1 class="font-serif text-2xl font-bold text-brand-dark tracking-tight">Quick Platform Tour</h1>
                            <p class="text-xs text-gray-500 mt-1">Here is a 1-minute overview of how Scriptly works.</p>
                        </div>

                        <!-- Product Tour Highlight Cards -->
                        <div class="space-y-3">
                            
                            <div class="p-3.5 bg-blue-50/60 border border-blue-200 rounded-[3px] flex items-start gap-3">
                                <span class="p-2 bg-blue-600 text-white rounded-md text-sm font-bold shadow-sm">1</span>
                                <div>
                                    <h3 class="font-extrabold text-xs text-brand-dark">Browse Marketplace & Talent</h3>
                                    <p class="text-[11px] text-gray-600 mt-0.5">Explore pre-assessed service providers or list custom projects with specific budget ranges.</p>
                                </div>
                            </div>

                            <div class="p-3.5 bg-emerald-50/60 border border-emerald-200 rounded-[3px] flex items-start gap-3">
                                <span class="p-2 bg-emerald-600 text-white rounded-md text-sm font-bold shadow-sm">2</span>
                                <div>
                                    <h3 class="font-extrabold text-xs text-brand-dark">Escrow Milestone Funding</h3>
                                    <p class="text-[11px] text-gray-600 mt-0.5">Deposit funds securely into Escrow before work starts. Release payment only after milestone review.</p>
                                </div>
                            </div>

                            <div class="p-3.5 bg-amber-50/60 border border-amber-200 rounded-[3px] flex items-start gap-3">
                                <span class="p-2 bg-amber-600 text-white rounded-md text-sm font-bold shadow-sm">3</span>
                                <div>
                                    <h3 class="font-extrabold text-xs text-brand-dark">Verified Skill Badges</h3>
                                    <p class="text-[11px] text-gray-600 mt-0.5">Providers complete 20-minute timed quiz assessments to unlock verified badges & higher search ranking.</p>
                                </div>
                            </div>

                        </div>

                        <button type="submit" id="submit-btn" class="w-full py-3.5 bg-brand-dark text-white rounded-full font-bold text-xs hover:bg-slate-800 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-md mt-2">
                            <span id="btn-text">Finish & Enter Dashboard →</span>
                            <svg id="btn-spinner" class="w-4 h-4 text-white animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>

                </form>

            </div>

            <!-- Footer Baseline -->
            <div class="mt-8 text-center text-[11px] text-gray-400 font-medium">
                © <?php echo date('Y'); ?> Scriptly Platform Inc. • Developed by <span class="text-brand-dark font-bold">Scriptly Team</span>
            </div>

        </main>

    </div>

    <!-- Scriptly Custom Alerts & Toast Subsystem -->
    <script src="assets/js/scriptly-alerts.js"></script>

    <!-- JavaScript Handlers -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const onboardingForm = document.getElementById('onboarding-form');
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnSpinner = document.getElementById('btn-spinner');

            const cardClient = document.getElementById('card-client');
            const cardProvider = document.getElementById('card-provider');
            const roleRadios = document.querySelectorAll('input[name="primary_role"]');

            // Steps Controls & Top Back Button
            let currentStep = 1;
            const topBackBtn = document.getElementById('top-back-btn');
            const step1Container = document.getElementById('step-1-container');
            const step2Container = document.getElementById('step-2-container');
            const step3Container = document.getElementById('step-3-container');
            const step4Container = document.getElementById('step-4-container');

            const btnStep1Next = document.getElementById('btn-step-1-next');
            const btnStep2Next = document.getElementById('btn-step-2-next');
            const btnStep3Next = document.getElementById('btn-step-3-next');
            const leftStepIndicator = document.getElementById('left-step-indicator');

            // Select Step 2 containers
            const entityTypeSelect = document.getElementById('entity_type');
            const studentFields = document.getElementById('student-fields-step2');
            const businessFields = document.getElementById('business-fields-step2');
            const corporationFields = document.getElementById('corporation-fields-step2');

            const institutionsList = document.getElementById('institutions-list');
            const institutionInput = document.getElementById('institution');

            async function fetchUniversities() {
                try {
                    const response = await fetch('http://universities.hipolabs.com/search?country=Nigeria');
                    if (!response.ok) throw new Error('API issue');
                    const data = await response.json();
                    
                    // Clear existing options
                    institutionsList.innerHTML = '';
                    
                    // Sort names alphabetically
                    data.sort((a, b) => a.name.localeCompare(b.name));
                    
                    data.forEach(uni => {
                        const option = document.createElement('option');
                        option.value = uni.name;
                        institutionsList.appendChild(option);
                    });
                } catch (err) {
                    console.warn('Failed to fetch from Hipolabs API, using default list.', err);
                }
            }

            if (institutionInput) {
                institutionInput.addEventListener('focus', fetchUniversities, { once: true });
            }

            // Role Radio Card Styling & Checkmark Badge Toggle
            const badgeClient = document.getElementById('badge-client');
            const badgeProvider = document.getElementById('badge-provider');

            roleRadios.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    if (e.target.value === 'client') {
                        cardClient.className = 'relative border-2 rounded-[6px] p-4 cursor-pointer transition-all duration-200 role-card bg-blue-50/70 border-blue-600 ring-2 ring-blue-600/30 shadow-md transform scale-[1.01]';
                        cardProvider.className = 'relative border-2 border-gray-200 rounded-[6px] p-4 cursor-pointer hover:border-gray-300 transition-all duration-200 role-card opacity-70 hover:opacity-100 bg-white';
                        
                        if (badgeClient) { badgeClient.classList.remove('hidden'); badgeClient.classList.add('flex'); }
                        if (badgeProvider) { badgeProvider.classList.remove('flex'); badgeProvider.classList.add('hidden'); }
                    } else {
                        cardProvider.className = 'relative border-2 rounded-[6px] p-4 cursor-pointer transition-all duration-200 role-card bg-emerald-50/70 border-emerald-600 ring-2 ring-emerald-600/30 shadow-md transform scale-[1.01]';
                        cardClient.className = 'relative border-2 border-gray-200 rounded-[6px] p-4 cursor-pointer hover:border-gray-300 transition-all duration-200 role-card opacity-70 hover:opacity-100 bg-white';
                        
                        if (badgeProvider) { badgeProvider.classList.remove('hidden'); badgeProvider.classList.add('flex'); }
                        if (badgeClient) { badgeClient.classList.remove('flex'); badgeClient.classList.add('hidden'); }
                    }
                });
            });

            // Top Back Arrow Click Handler
            topBackBtn.addEventListener('click', () => {
                if (currentStep > 1) {
                    goToStep(currentStep - 1);
                }
            });

            // Wizard Step Navigation Logic
            function goToStep(step) {
                currentStep = step;

                if (step === 1) {
                    step1Container.classList.remove('hidden');
                    step2Container.classList.add('hidden');
                    step3Container.classList.add('hidden');
                    step4Container.classList.add('hidden');
                    leftStepIndicator.textContent = 'Step 1 of 4';
                    
                    topBackBtn.classList.remove('inline-flex');
                    topBackBtn.classList.add('hidden');
                } else if (step === 2) {
                    step1Container.classList.add('hidden');
                    step2Container.classList.remove('hidden');
                    step3Container.classList.add('hidden');
                    step4Container.classList.add('hidden');
                    leftStepIndicator.textContent = 'Step 2 of 4';
                    
                    topBackBtn.classList.remove('hidden');
                    topBackBtn.classList.add('inline-flex');

                    // Show correct sub-container based on selected Entity Type
                    const entityType = entityTypeSelect.value;
                    studentFields.classList.add('hidden');
                    businessFields.classList.add('hidden');
                    corporationFields.classList.add('hidden');

                    if (entityType === 'student') {
                        studentFields.classList.remove('hidden');
                    } else if (entityType === 'business') {
                        businessFields.classList.remove('hidden');
                    } else if (entityType === 'corporation') {
                        corporationFields.classList.remove('hidden');
                    }
                } else if (step === 3) {
                    step1Container.classList.add('hidden');
                    step2Container.classList.add('hidden');
                    step3Container.classList.remove('hidden');
                    step4Container.classList.add('hidden');
                    leftStepIndicator.textContent = 'Step 3 of 4';
                    
                    topBackBtn.classList.remove('hidden');
                    topBackBtn.classList.add('inline-flex');
                } else if (step === 4) {
                    step1Container.classList.add('hidden');
                    step2Container.classList.add('hidden');
                    step3Container.classList.add('hidden');
                    step4Container.classList.remove('hidden');
                    leftStepIndicator.textContent = 'Step 4 of 4';
                    
                    topBackBtn.classList.remove('hidden');
                    topBackBtn.classList.add('inline-flex');
                }
            }

            // Step 1 -> Step 2 Validation & Transition
            btnStep1Next.addEventListener('click', () => {
                const phoneNumber = document.getElementById('phone_number').value.trim();
                const address = document.getElementById('address').value.trim();
                const entityType = entityTypeSelect.value;

                if (!phoneNumber) {
                    ScriptlyToast.error('Please enter your mobile phone number.', 'Required Field');
                    return;
                }

                if (!address) {
                    ScriptlyToast.error('Please enter your city or physical location.', 'Required Field');
                    return;
                }
                
                if (!entityType) {
                    ScriptlyToast.error('Please select your entity type.', 'Required Field');
                    return;
                }

                goToStep(2);
            });

            // Step 2 -> Step 3 Validation & Transition
            btnStep2Next.addEventListener('click', () => {
                const entityType = entityTypeSelect.value;

                if (entityType === 'student') {
                    const studentId = document.getElementById('student_id').value.trim();
                    const institution = document.getElementById('institution').value.trim();
                    const department = document.getElementById('department').value.trim();

                    if (!studentId) {
                        ScriptlyToast.error('Please enter your Student ID / Matric Number.', 'Required Field');
                        return;
                    }
                    if (!institution) {
                        ScriptlyToast.error('Please search or type your Institution/University.', 'Required Field');
                        return;
                    }
                    if (!department) {
                        ScriptlyToast.error('Please enter your Department.', 'Required Field');
                        return;
                    }
                } else if (entityType === 'business') {
                    const bizName = document.getElementById('business_name').value.trim();
                    const rcNum = document.getElementById('rc_number').value.trim();
                    const industry = document.getElementById('industry').value;

                    if (!bizName) {
                        ScriptlyToast.error('Please enter your Registered Business Name.', 'Required Field');
                        return;
                    }
                    if (!rcNum) {
                        ScriptlyToast.error('Please enter your CAC Registration / BN Number.', 'Required Field');
                        return;
                    }
                    if (!industry) {
                        ScriptlyToast.error('Please select your Business Industry.', 'Required Field');
                        return;
                    }
                } else if (entityType === 'corporation') {
                    const compName = document.getElementById('corp_company_name').value.trim();
                    const compRc = document.getElementById('corp_rc_number').value.trim();
                    const website = document.getElementById('company_website').value.trim();

                    if (!compName) {
                        ScriptlyToast.error('Please enter your Company Name.', 'Required Field');
                        return;
                    }
                    if (!compRc) {
                        ScriptlyToast.error('Please enter your Corporate RC Number.', 'Required Field');
                        return;
                    }
                    if (!website) {
                        ScriptlyToast.error('Please enter your Company Website URL.', 'Required Field');
                        return;
                    }
                }

                goToStep(3);
            });

            // Step 3 -> Step 4 Validation & Transition
            btnStep3Next.addEventListener('click', () => {
                const acceptedRules = document.getElementById('accepted_rules').checked;
                if (!acceptedRules) {
                    ScriptlyToast.warning('You must read and agree to Scriptly\'s Platform Rules & Code of Conduct.', 'Rules Agreement Required');
                    return;
                }
                goToStep(4);
            });

            // Final Form Submit Handler (AJAX)
            onboardingForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const phoneNumber = document.getElementById('phone_number').value.trim();
                const address = document.getElementById('address').value.trim();
                const selectedRole = document.querySelector('input[name="primary_role"]').value;
                const entityType = entityTypeSelect.value;
                const acceptedRules = document.getElementById('accepted_rules').checked;

                let studentId = '';
                let institution = '';
                let department = '';
                let bizName = '';
                let rcNum = '';
                let industry = '';
                let compWebsite = '';

                if (!phoneNumber || !address || !entityType) {
                    ScriptlyToast.error('Please complete step 1 with phone number, address, and entity type.', 'Incomplete Info');
                    goToStep(1);
                    return;
                }

                if (entityType === 'student') {
                    studentId = document.getElementById('student_id').value.trim();
                    institution = document.getElementById('institution').value.trim();
                    department = document.getElementById('department').value.trim();

                    if (!studentId || !institution || !department) {
                        ScriptlyToast.error('Please complete all academic info fields.', 'Incomplete Info');
                        goToStep(2);
                        return;
                    }
                } else if (entityType === 'business') {
                    bizName = document.getElementById('business_name').value.trim();
                    rcNum = document.getElementById('rc_number').value.trim();
                    industry = document.getElementById('industry').value;

                    if (!bizName || !rcNum || !industry) {
                        ScriptlyToast.error('Please complete all business info fields.', 'Incomplete Info');
                        goToStep(2);
                        return;
                    }
                } else if (entityType === 'corporation') {
                    bizName = document.getElementById('corp_company_name').value.trim();
                    rcNum = document.getElementById('corp_rc_number').value.trim();
                    compWebsite = document.getElementById('company_website').value.trim();

                    if (!bizName || !rcNum || !compWebsite) {
                        ScriptlyToast.error('Please complete all corporate info fields.', 'Incomplete Info');
                        goToStep(2);
                        return;
                    }
                }

                if (!acceptedRules) {
                    ScriptlyToast.warning('You must accept the Platform Rules on step 3.', 'Rules Required');
                    goToStep(3);
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.classList.remove('cursor-pointer');
                submitBtn.classList.add('cursor-not-allowed', 'opacity-75');
                btnText.textContent = 'Saving Onboarding...';
                btnSpinner.classList.remove('hidden');

                try {
                    const response = await fetch('api/auth/complete-onboarding.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            phone_number: phoneNumber,
                            address: address,
                            primary_role: selectedRole,
                            entity_type: entityType,
                            accepted_rules: acceptedRules,
                            student_id: studentId,
                            institution: institution,
                            department: department,
                            business_name: bizName,
                            rc_number: rcNum,
                            industry: industry,
                            company_website: compWebsite
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => {
                            window.location.href = data.redirect || 'app/index';
                        }, 500);
                    } else {
                        showAlert('error', data.message || 'An error occurred.');
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('cursor-not-allowed', 'opacity-75');
                        submitBtn.classList.add('cursor-pointer');
                        btnText.textContent = 'Finish & Enter Dashboard →';
                        btnSpinner.classList.add('hidden');
                    }
                } catch (err) {
                    showAlert('error', 'Network error. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('cursor-not-allowed', 'opacity-75');
                    submitBtn.classList.add('cursor-pointer');
                    btnText.textContent = 'Finish & Enter Dashboard →';
                    btnSpinner.classList.add('hidden');
                }
            });

        });
    </script>

</body>
</html>
