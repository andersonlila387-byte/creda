<?php
// Dynamic Talent Dictionary for Public Profiles
$profiles = [
    '1' => [
        'id' => '1',
        'name' => 'Elena Vance',
        'role' => 'Lead UI/UX Architect & Full-Stack Pro',
        'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=400',
        'badge' => 'Pre-Assessed Top Pro (100%)',
        'location' => 'Lagos, Nigeria',
        'rating' => 5.0,
        'reviews_count' => 48,
        'jobs_delivered' => 48,
        'hourly_rate' => '₦15,000',
        'avg_project' => '₦175,000',
        'response_time' => '< 45 mins',
        'on_time_rate' => '100%',
        'bio' => 'Senior UI/UX architect and full-stack software developer with 6+ years of experience designing intuitive, user-centric interfaces and building robust full-stack web applications. Expert in Figma component design systems, PHP MVC frameworks, RESTful APIs, and secure milestone escrow integration.',
        'skills' => ['UI/UX Design', 'Figma Design System', 'PHP MVC', 'MySQL', 'Tailwind CSS', 'REST API', 'JavaScript', 'Turnitin Clean Code'],
        'assessment_scores' => [
            ['test' => 'UI/UX Interactive Prototyping & Design Tokens', 'score' => '100 / 100', 'desc' => 'Figma components, accessibility (WCAG AA), responsive mobile-first layouts'],
            ['test' => 'Full-Stack PHP MVC & API Architecture', 'score' => '98 / 100', 'desc' => 'Secure session authentication, PDO prepared statements, RESTful endpoints'],
            ['test' => 'Database Normalization & Query Efficiency', 'score' => '96 / 100', 'desc' => 'MySQL InnoDB indexing, relationship modeling, transaction integrity']
        ],
        'packages' => [
            ['tier' => 'Starter', 'title' => 'Wireframes & UI Prototype', 'price' => '₦65,000', 'time' => '4 Days', 'features' => ['High-Fidelity Figma Mobile UI (5 Screens)', 'Clickable Interactive Prototype', 'Design System Tokens & Typography', '2 Revision Cycles']],
            ['tier' => 'Standard', 'title' => 'Complete UI System + Frontend View', 'price' => '₦140,000', 'time' => '8 Days', 'popular' => true, 'features' => ['Full Web & Mobile UI (12 Screens)', 'Tailwind CSS HTML Views', 'Component Style Guide & Icons', '14-Day Bug Warranty']],
            ['tier' => 'Premium', 'title' => 'Full-Stack Web App with Escrow', 'price' => '₦240,000', 'time' => '14 Days', 'features' => ['Custom PHP MVC Backend + Database', 'Interactive Tailwind Client Dashboard', 'Milestone Escrow Payment Webhook', 'Full Hand-off & Live Staging']]
        ],
        'portfolio' => [
            ['title' => 'FinTech Mobile Banking App', 'category' => 'UI/UX Design', 'img' => 'assets/services/uiux_design.jpg', 'tech' => 'Figma • Design System • Prototyping'],
            ['title' => 'E-Commerce Escrow Portal', 'category' => 'Web App', 'img' => 'assets/services/software_dev.jpg', 'tech' => 'PHP MVC • MySQL • Tailwind'],
            ['title' => 'Academic Thesis Defense Deck', 'category' => 'Design & Research', 'img' => 'assets/services/report_writing.jpg', 'tech' => 'SPSS Charts • Presentation']
        ],
        'reviews' => [
            ['client' => 'Dr. Alex Morgan', 'org' => 'FinTech Research Lead', 'rating' => 5.0, 'project' => 'Full-Stack PHP & MySQL Web Portal with Escrow', 'date' => 'August 2026', 'quote' => 'Elena delivered our milestone portal 3 days ahead of schedule. The code was exceptionally clean and well-documented. Escrow release was a breeze.'],
            ['client' => 'Tunde Bakare', 'org' => 'CTO, PaySwift Solutions', 'rating' => 5.0, 'project' => 'Figma UI Prototype & Design Tokens', 'date' => 'July 2026', 'quote' => 'Superb Figma design system. Our frontend developers implemented her wireframes without asking a single clarification question.'],
            ['client' => 'Chioma Nwosu', 'org' => 'Postgraduate Researcher, UNILAG', 'rating' => 5.0, 'project' => 'Interactive Data Visualization Dashboard', 'date' => 'June 2026', 'quote' => 'Elena is the most reliable developer on Scriptly. 100% verified assessment score is well-deserved!']
        ]
    ],
    '2' => [
        'id' => '2',
        'name' => 'David Olanrewaju',
        'role' => 'Senior Full-Stack PHP & MySQL Engineer',
        'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=400',
        'badge' => 'Pre-Assessed Top Pro (98%)',
        'location' => 'Abuja, Nigeria',
        'rating' => 4.95,
        'reviews_count' => 32,
        'jobs_delivered' => 32,
        'hourly_rate' => '₦18,000',
        'avg_project' => '₦180,000',
        'response_time' => '< 30 mins',
        'on_time_rate' => '99%',
        'bio' => 'Senior software engineer specializing in backend architecture, relational database schema optimization, and high-concurrency PHP microservices. Built 15+ student management and freelance escrow web platforms with 0% vulnerability audit track records.',
        'skills' => ['PHP 8.2', 'MySQL InnoDB', 'Database Normalization', 'API Security', 'Laravel', 'Docker', 'Redis', 'WebSockets'],
        'assessment_scores' => [
            ['test' => 'Backend Security & CSRF/SQL Injection Defense', 'score' => '100 / 100', 'desc' => 'OWASP Top 10 mitigation, password hashing, parameterized PDO'],
            ['test' => 'High-Concurrency Database Optimization', 'score' => '98 / 100', 'desc' => 'Indexing strategies, query execution plans, InnoDB clustering'],
            ['test' => 'REST API Architecture & Microservices', 'score' => '96 / 100', 'desc' => 'JSON payload validation, status codes, JWT authentication']
        ],
        'packages' => [
            ['tier' => 'Starter', 'title' => 'Database Schema & Auth Core', 'price' => '₦70,000', 'time' => '3 Days', 'features' => ['Normalized MySQL DB Schema (ERD)', 'Secure Session / JWT Auth API', 'Password Hashing & CSRF Protection', 'Sample Seed Data Scripts']],
            ['tier' => 'Standard', 'title' => 'Complete Backend REST API', 'price' => '₦150,000', 'time' => '7 Days', 'popular' => true, 'features' => ['Full CRUD REST API Endpoints', 'Payment Webhook Listeners', 'Automated Unit Test Suite', 'Postman Collection Docs']],
            ['tier' => 'Premium', 'title' => 'Full-Stack MVC System + Deploy', 'price' => '₦250,000', 'time' => '12 Days', 'features' => ['End-to-End System (Frontend + Backend)', 'Dockerized Production Setup', 'CI/CD Automated Deployment', '30-Day Extended Warranty']]
        ],
        'portfolio' => [
            ['title' => 'Automated CI/CD Pipeline', 'category' => 'DevOps', 'img' => 'assets/services/software_dev.jpg', 'tech' => 'Docker • GitHub Actions • PHP'],
            ['title' => 'Multi-Tenant SaaS Backend', 'category' => 'Backend API', 'img' => 'assets/services/data_analytics.jpg', 'tech' => 'MySQL • Redis • JWT'],
            ['title' => 'Payment Gateway Escrow Webhooks', 'category' => 'FinTech', 'img' => 'assets/services/mobile_app.jpg', 'tech' => 'Paystack • Flutterwave • PHP']
        ],
        'reviews' => [
            ['client' => 'Kingsley Chukwuma', 'org' => 'Dev Lead, FinCorp', 'rating' => 5.0, 'project' => 'Docker & GitHub Actions CI/CD Pipeline', 'date' => 'August 2026', 'quote' => 'David is a master of backend architecture. Our deployment pipelines run 4x faster.'],
            ['client' => 'Blessing Adeleke', 'org' => 'Founder, EduPortal', 'rating' => 4.9, 'project' => 'Database Migration & Performance Tuning', 'date' => 'July 2026', 'quote' => 'Resolved our query bottlenecks in less than 48 hours. Exceptional professional.']
        ]
    ],
    '3' => [
        'id' => '3',
        'name' => 'Dr. Olayinka Adebayo',
        'role' => 'Lead Academic Researcher & SPSS Statistician',
        'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=400',
        'badge' => 'Pre-Assessed Top Pro (99%)',
        'location' => 'Ibadan, Nigeria',
        'rating' => 5.0,
        'reviews_count' => 54,
        'jobs_delivered' => 54,
        'hourly_rate' => '₦12,000',
        'avg_project' => '₦120,000',
        'response_time' => '< 20 mins',
        'on_time_rate' => '100%',
        'bio' => 'Senior academic researcher and statistical modeling consultant with 10+ years helping postgraduate students, faculty researchers, and commercial institutions conduct quantitative analysis, SPSS regression, and Turnitin-verified thesis development.',
        'skills' => ['SPSS Data Analysis', 'Academic Writing', 'APA 7th Referencing', 'Turnitin 0% Plagiarism', 'Regression & ANOVA', 'Thesis Chapters 1-5', 'Econometrics'],
        'assessment_scores' => [
            ['test' => 'Advanced SPSS & Hypothesis Testing Challenge', 'score' => '100 / 100', 'desc' => 'Chi-Square, Multiple Regression, T-Tests, Reliability (Cronbach Alpha)'],
            ['test' => 'Academic Citation & Methodology Audit', 'score' => '99 / 100', 'desc' => 'APA 7th, Harvard, IEEE referencing with 0% Turnitin similarity tolerance'],
            ['test' => 'Econometric & Questionnaire Modeling', 'score' => '98 / 100', 'desc' => 'Data cleansing, missing values replacement, structural equation modeling']
        ],
        'packages' => [
            ['tier' => 'Starter', 'title' => 'SPSS Analysis & Descriptive Stats', 'price' => '₦45,000', 'time' => '3 Days', 'features' => ['Survey Data Cleansing & Coding', 'Frequency Tables & Descriptive Charts', 'Reliability (Cronbach Alpha) Test', 'SPSS .SAV & Word Output Tables']],
            ['tier' => 'Standard', 'title' => 'Chapter 4: Results & Discussion', 'price' => '₦95,000', 'time' => '6 Days', 'popular' => true, 'features' => ['Hypothesis Testing (Regression/ANOVA)', 'Full APA 7th Chapter 4 Interpretation', 'Turnitin Similarity Report (<5%)', 'Defense Question Preparation Notes']],
            ['tier' => 'Premium', 'title' => 'Complete Dissertation Support (Ch 1–5)', 'price' => '₦190,000', 'time' => '12 Days', 'features' => ['Full 5-Chapter Comprehensive Thesis', 'Questionnaire Design & Data Analysis', 'Turnitin Official Plagiarism Audit', 'Post-Defense Free Corrections Support']]
        ],
        'portfolio' => [
            ['title' => 'FinTech Adoption in West Africa', 'category' => 'Academic Research', 'img' => 'assets/services/report_writing.jpg', 'tech' => 'SPSS • Regression • APA 7th'],
            ['title' => 'Healthcare Resource Optimization', 'category' => 'Data Analytics', 'img' => 'assets/services/data_analytics.jpg', 'tech' => 'ANOVA • Hypothesis Testing'],
            ['title' => 'Corporate Market Feasibility Study', 'category' => 'Business Report', 'img' => 'assets/services/uiux_design.jpg', 'tech' => 'Financial Modeling • Executive Brief']
        ],
        'reviews' => [
            ['client' => 'Jordi Robert', 'org' => 'Postgraduate Researcher', 'rating' => 5.0, 'project' => 'SPSS Dataset Analysis & Chapter 4 Report', 'date' => 'August 2026', 'quote' => 'Passed my thesis defense with distinction! Dr. Olayinka provided SPSS tables and APA interpretations that my supervisor approved without a single correction.'],
            ['client' => 'Fatima Aliyu', 'org' => 'MBA Candidate, ABU', 'rating' => 5.0, 'project' => 'Market Feasibility Study & Hypothesis Tests', 'date' => 'July 2026', 'quote' => 'Turnitin report was 2% similarity. 100% genuine academic rigour. Highly recommended!']
        ]
    ]
];

// Determine selected profile
$pro_id = isset($_GET['id']) ? trim($_GET['id']) : '1';
$talent = $profiles[$pro_id] ?? $profiles['1'];

$page_title = htmlspecialchars($talent['name']) . " — " . htmlspecialchars($talent['role']) . " • Scriptly Verified Pro";
$page_description = "View " . htmlspecialchars($talent['name']) . "'s verified assessment scores, portfolio deliverables, packages, and client reviews on Scriptly.";
$active_page = "professionals";
include 'includes/header.php';
?>

    <!-- Public Profile Main Container -->
    <main class="max-w-7xl mx-auto px-3 sm:px-6 py-8 sm:py-12">
        
        <!-- Breadcrumb Bar -->
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-6">
            <a href="index.php" class="hover:text-[#1952E1]">Home</a>
            <span>/</span>
            <a href="services.php" class="hover:text-[#1952E1]">Services</a>
            <span>/</span>
            <span class="text-slate-800 font-bold"><?= htmlspecialchars($talent['name']); ?></span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">
            
            <!-- Left 8 Cols: Identity, Scores, Packages & Reviews -->
            <div class="lg:col-span-8 space-y-6 sm:space-y-8">
                
                <!-- 1. Profile Header Hero Card -->
                <div class="bg-white rounded-[3px] p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 sm:gap-6">
                        <div class="relative shrink-0">
                            <img src="<?= htmlspecialchars($talent['avatar']); ?>" alt="<?= htmlspecialchars($talent['name']); ?>" class="w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover border-4 border-blue-50 shadow-md">
                            <span class="absolute bottom-1 right-1 w-4 h-4 bg-emerald-500 rounded-full ring-2 ring-white" title="Online now"></span>
                        </div>
                        <div class="space-y-1.5 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h1 class="font-serif text-2xl sm:text-3xl font-black text-brand-dark"><?= htmlspecialchars($talent['name']); ?></h1>
                                <span class="bg-[#1952E1] text-white font-bold text-xs px-3 py-0.5 rounded-full flex items-center gap-1">
                                    <i class="ph-fill ph-seal-check"></i> Verified Pro
                                </span>
                            </div>
                            <p class="text-sm font-bold text-[#1952E1]"><?= htmlspecialchars($talent['role']); ?></p>
                            <div class="flex items-center gap-4 text-xs text-slate-600 flex-wrap pt-1.5">
                                <span class="flex items-center gap-1"><i class="ph-bold ph-map-pin text-slate-400"></i> <?= htmlspecialchars($talent['location']); ?></span>
                                <span class="flex items-center gap-1 font-bold text-amber-500">★ <?= number_format($talent['rating'], 1); ?> <span class="text-slate-400 font-normal">(<?= $talent['reviews_count']; ?> Verified Reviews)</span></span>
                                <span class="text-emerald-700 font-bold bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-[3px]"><?= htmlspecialchars($talent['badge']); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- About / Bio -->
                    <div class="pt-4 border-t border-slate-100 space-y-2">
                        <h3 class="font-bold text-xs text-slate-700 uppercase tracking-wider">Professional Biography</h3>
                        <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                            <?= htmlspecialchars($talent['bio']); ?>
                        </p>
                    </div>

                    <!-- Verified Skill Tags -->
                    <div class="pt-2">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Verified Skill Domain Tags</h4>
                        <div class="flex flex-wrap gap-1.5">
                            <?php foreach ($talent['skills'] as $skill): ?>
                                <span class="text-xs font-bold bg-blue-50 text-[#1952E1] border border-blue-200 px-3 py-1 rounded-[3px]">
                                    <?= htmlspecialchars($skill); ?> ✓
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- 2. Verified Platform Assessment Test Scores -->
                <div class="bg-white rounded-[3px] p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="font-serif text-xl font-bold text-brand-dark">Verified Assessment Audit Results</h3>
                            <p class="text-xs text-slate-500">Scriptly standardized timed practical challenge scores.</p>
                        </div>
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-[3px]">
                            100% Pre-Assessed
                        </span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <?php foreach ($talent['assessment_scores'] as $test): ?>
                            <div class="p-3.5 rounded-[3px] bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div>
                                    <span class="font-bold text-slate-900 block"><?= htmlspecialchars($test['test']); ?></span>
                                    <span class="text-[11px] text-slate-500"><?= htmlspecialchars($test['desc']); ?></span>
                                </div>
                                <span class="font-extrabold text-sm text-[#1952E1] bg-white border border-blue-200 px-3 py-1 rounded-[3px] shrink-0 self-start sm:self-auto">
                                    <?= htmlspecialchars($test['score']); ?> (Passed)
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 3. Service Packages & Pricing -->
                <div class="bg-white rounded-[3px] p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-6">
                    <div>
                        <h3 class="font-serif text-xl font-bold text-brand-dark">Direct Service Offerings & Milestone Packages</h3>
                        <p class="text-xs text-slate-500">Fixed-price milestone deliverable packages with 100% escrow protection.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <?php foreach ($talent['packages'] as $pkg): ?>
                            <div class="rounded-[3px] p-5 flex flex-col justify-between space-y-4 <?= isset($pkg['popular']) ? 'border-2 border-[#1952E1] bg-blue-50/20 shadow-sm' : 'border border-slate-200 bg-white'; ?>">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold uppercase tracking-wider <?= isset($pkg['popular']) ? 'bg-[#1952E1] text-white px-2 py-0.5 rounded-[2px]' : 'text-slate-500'; ?>">
                                            <?= htmlspecialchars($pkg['tier']); ?>
                                        </span>
                                        <span class="text-[11px] text-slate-500 font-medium"><?= htmlspecialchars($pkg['time']); ?></span>
                                    </div>
                                    <h4 class="font-bold text-sm text-slate-900"><?= htmlspecialchars($pkg['title']); ?></h4>
                                    <div class="text-xl font-black text-[#1952E1]"><?= htmlspecialchars($pkg['price']); ?></div>
                                    
                                    <ul class="space-y-1.5 text-xs text-slate-600 border-t border-slate-100 pt-3">
                                        <?php foreach ($pkg['features'] as $feat): ?>
                                            <li class="flex items-start gap-1.5">
                                                <i class="ph-bold ph-check text-emerald-600 mt-0.5"></i>
                                                <span><?= htmlspecialchars($feat); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>

                                <button type="button" onclick="openDirectHireModal('<?= addslashes($talent['name']); ?>', '<?= addslashes($pkg['title']); ?>', '<?= addslashes($pkg['price']); ?>')" class="w-full py-2.5 text-center text-xs font-bold rounded-[3px] transition-colors <?= isset($pkg['popular']) ? 'bg-[#1952E1] hover:bg-blue-700 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-800'; ?>">
                                    Request Package
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 4. Verified Portfolio Showcase -->
                <div class="bg-white rounded-[3px] p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-6">
                    <h3 class="font-serif text-xl font-bold text-brand-dark">Featured Project Deliverables</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <?php foreach ($talent['portfolio'] as $item): ?>
                            <div class="rounded-[3px] border border-slate-200 overflow-hidden group hover:border-[#1952E1] transition-all flex flex-col justify-between">
                                <div>
                                    <div class="h-36 w-full overflow-hidden bg-slate-100">
                                        <img src="<?= htmlspecialchars($item['img']); ?>" alt="<?= htmlspecialchars($item['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                    <div class="p-3.5 space-y-1">
                                        <span class="text-[10px] font-bold text-[#1952E1] uppercase"><?= htmlspecialchars($item['category']); ?></span>
                                        <h4 class="font-bold text-xs text-slate-900"><?= htmlspecialchars($item['title']); ?></h4>
                                        <p class="text-[11px] text-slate-500 font-medium"><?= htmlspecialchars($item['tech']); ?></p>
                                    </div>
                                </div>
                                <div class="p-3 pt-0">
                                    <button type="button" onclick="alert('Viewing verified deliverable specs for <?= addslashes($item['title']); ?>');" class="w-full text-center text-[11px] font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 py-1.5 rounded-[3px] border border-slate-200">
                                        Inspect Case Study
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 5. Verified Client Reviews -->
                <div class="bg-white rounded-[3px] p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-serif text-xl font-bold text-brand-dark">Verified Escrow Milestone Reviews</h3>
                        <span class="font-bold text-amber-500 text-sm">★ <?= number_format($talent['rating'], 1); ?> / 5.0</span>
                    </div>

                    <div class="space-y-4">
                        <?php foreach ($talent['reviews'] as $rev): ?>
                            <div class="p-4 rounded-[3px] bg-slate-50 border border-slate-200 space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="font-bold text-slate-900"><?= htmlspecialchars($rev['client']); ?></span>
                                        <span class="text-slate-400 text-[11px]"> • <?= htmlspecialchars($rev['org']); ?></span>
                                    </div>
                                    <span class="text-amber-500 font-bold">★ <?= number_format($rev['rating'], 1); ?></span>
                                </div>
                                <p class="text-slate-700 leading-relaxed italic">
                                    “<?= htmlspecialchars($rev['quote']); ?>”
                                </p>
                                <div class="flex items-center justify-between text-[10px] text-slate-400 pt-1 border-t border-slate-100">
                                    <span>Project: <strong><?= htmlspecialchars($rev['project']); ?></strong></span>
                                    <span class="text-emerald-700 font-bold">100% Escrow Released • <?= htmlspecialchars($rev['date']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <!-- Right 4 Cols: Hiring Action Sidebar (Sticky) -->
            <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
                
                <!-- Booking & Rates Card -->
                <div class="bg-white rounded-[3px] p-6 border border-slate-200/90 shadow-sm space-y-6">
                    <div class="text-center pb-5 border-b border-slate-100">
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block mb-1">Standard Rate</span>
                        <span class="font-serif text-3xl font-black text-brand-dark"><?= htmlspecialchars($talent['hourly_rate']); ?><span class="text-xs font-normal text-slate-500">/hr</span></span>
                        <span class="text-xs font-bold text-emerald-700 block mt-1.5 flex items-center justify-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Available for Contract Hire
                        </span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Job Success Rate:</span>
                            <strong class="text-emerald-700 font-bold"><?= htmlspecialchars($talent['on_time_rate']); ?> Success</strong>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Completed Projects:</span>
                            <strong class="text-slate-900"><?= $talent['jobs_delivered']; ?> Delivered</strong>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Average Response Time:</span>
                            <strong class="text-slate-900"><?= htmlspecialchars($talent['response_time']); ?></strong>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Avg Project Size:</span>
                            <strong class="text-[#1952E1] font-bold"><?= htmlspecialchars($talent['avg_project']); ?></strong>
                        </div>
                    </div>

                    <div class="space-y-2.5 pt-2 border-t border-slate-100">
                        <button type="button" onclick="openDirectHireModal('<?= addslashes($talent['name']); ?>', 'Custom Project Request', '<?= addslashes($talent['avg_project']); ?>')" class="w-full py-3.5 bg-[#1952E1] hover:bg-blue-700 text-white rounded-[3px] font-bold text-xs text-center transition-all shadow-sm">
                            Request Custom Project →
                        </button>
                        <a href="app/messages.php" class="w-full block py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-[3px] font-bold text-xs text-center transition-colors">
                            Send Direct Message
                        </a>
                    </div>
                </div>

                <!-- Escrow Protection Guarantee Box -->
                <div class="bg-[#0A2342] text-white p-5 rounded-[3px] space-y-3">
                    <div class="flex items-center gap-2 text-blue-300 text-xs font-bold">
                        <i class="ph-bold ph-shield-check text-lg"></i>
                        <span>100% Escrow Protection</span>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Funds are only released when you review and approve each completed milestone. If requirements are not met, you are covered by the Scriptly Arbitration Desk.
                    </p>
                </div>

            </div>

        </div>

    </main>

    <!-- DIRECT HIRE INQUIRY MODAL -->
    <div id="direct-hire-modal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-[3px] border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-5">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center font-bold">
                        <i class="ph-bold ph-paper-plane-tilt text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Send Project Proposal</h3>
                        <p class="text-xs text-slate-500">Hire with 100% Milestone Escrow Protection</p>
                    </div>
                </div>
                <button type="button" onclick="closeDirectHireModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
            </div>

            <form onsubmit="event.preventDefault(); alert('Proposal sent to ' + document.getElementById('modal-hire-talent-name').textContent + '! They will respond shortly.'); closeDirectHireModal();">
                <div class="space-y-3.5 text-xs">
                    <div class="p-3 bg-slate-50 rounded-[3px] border border-slate-200 flex justify-between items-center">
                        <div>
                            <span class="text-slate-500 block text-[11px]">Selected Talent</span>
                            <strong id="modal-hire-talent-name" class="text-slate-900 font-bold text-xs"><?= htmlspecialchars($talent['name']); ?></strong>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-500 block text-[11px]">Selected Scope</span>
                            <strong id="modal-hire-scope-title" class="text-[#1952E1] font-bold text-xs">Custom Project</strong>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Project Title</label>
                        <input type="text" required placeholder="e.g., Mobile App UI Prototype & Backend API" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] p-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]">
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Deliverable Requirements & Specifications</label>
                        <textarea rows="4" required placeholder="Describe your objectives, deadline expectations, and deliverables..." class="w-full bg-slate-50 border border-slate-200 rounded-[3px] p-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">Estimated Budget (₦)</label>
                            <input type="text" id="modal-hire-budget" required value="<?= htmlspecialchars($talent['avg_project']); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] p-2.5 text-xs font-bold text-[#1952E1]">
                        </div>
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">Target Completion</label>
                            <select class="w-full bg-slate-50 border border-slate-200 rounded-[3px] p-2.5 text-xs font-medium text-slate-700">
                                <option>Within 1 Week</option>
                                <option selected>Within 2 Weeks</option>
                                <option>Within 1 Month</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 mt-4">
                    <button type="button" onclick="closeDirectHireModal()" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 rounded-[3px]">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-xs font-bold bg-[#1952E1] hover:bg-blue-700 text-white rounded-[3px] shadow-sm">
                        Submit Offer to Talent
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
    function openDirectHireModal(name, title, price) {
        document.getElementById('modal-hire-talent-name').textContent = name;
        document.getElementById('modal-hire-scope-title').textContent = title;
        document.getElementById('modal-hire-budget').value = price;
        document.getElementById('direct-hire-modal').classList.remove('hidden');
    }

    function closeDirectHireModal() {
        document.getElementById('direct-hire-modal').classList.add('hidden');
    }
    </script>

<?php include 'includes/footer.php'; ?>
