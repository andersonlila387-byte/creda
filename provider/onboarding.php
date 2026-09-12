<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Must be logged in
if (empty($_SESSION['user_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$db    = getDBConnection();
$stmt  = $db->prepare("SELECT id, full_name, email, primary_role, onboarding_completed, assessment_status FROM users WHERE email = ?");
$stmt->execute([$_SESSION['user_email']]);
$db_user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$db_user) {
    header('Location: login.php');
    exit;
}

// Must be a provider
if ($db_user['primary_role'] !== 'provider') {
    header('Location: ../onboarding.php');
    exit;
}

// Already onboarded — redirect to dashboard
if ($db_user['onboarding_completed']) {
    header('Location: app/index.php');
    exit;
}

$user_name = $db_user['full_name'] ?? 'Provider';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Setup — <?= htmlspecialchars('Scriptly') ?></title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind -->
    <link rel="stylesheet" href="../assets/css/tailwind.min.css">

    <!-- Alerts -->
    <link rel="stylesheet" href="../assets/css/scriptly-alerts.css">

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .step-content { display: none; }
        .step-content.active { display: block; }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #1952E1;
            box-shadow: 0 0 0 3px rgba(25,82,225,0.08);
        }
        
        /* Availability Card Active States */
        .avail-radio:checked + .avail-card {
            border-color: #1952E1;
            background-color: #F0F5FF;
            box-shadow: 0 0 0 1px #1952E1;
        }
        .avail-radio:checked + .avail-card .check-icon {
            opacity: 1;
        }
        .avail-radio:checked + .avail-card .icon-container {
            background-color: #1952E1;
            color: white;
        }
        .avail-radio:checked + .avail-card .card-text {
            color: #1952E1;
        }
    </style>
</head>
<body class="bg-[#EFF2F7] text-slate-900 antialiased min-h-screen">

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">

    <!-- ============================================================
         LEFT COLUMN: Brand Panel
         ============================================================ -->
    <aside class="lg:col-span-4 hidden lg:flex flex-col justify-between bg-[#0A2342] text-white p-8 relative overflow-hidden sticky top-0 h-screen">
        <!-- Background image with heavier overlay for legibility -->
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=1200&auto=format&fit=crop&q=80"
                 alt="" class="w-full h-full object-cover opacity-30">
            <div class="absolute inset-0 bg-gradient-to-b from-[#0A2342]/90 via-[#0A2342]/95 to-[#0A2342]"></div>
        </div>

        <!-- Top logo -->
        <div class="relative z-10">
            <a href="../" class="inline-flex items-center gap-2 text-xl font-extrabold tracking-tight text-white">
                <span><?= htmlspecialchars('Scriptly') ?></span>
                <span class="w-5 h-5 bg-[#1952E1] rounded-full flex items-center justify-center">
                    <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </span>
            </a>
        </div>

        <!-- Center content -->
        <div class="relative z-10 space-y-6 max-w-xs">

            <!-- Welcome greeting (moved here) -->
            <div class="space-y-1">
                <p class="text-[10px] font-extrabold uppercase tracking-[0.15em] text-blue-300">Provider Setup</p>
                <h2 class="text-2xl font-extrabold text-white tracking-tight leading-snug" style="font-family:'Space Grotesk',sans-serif;">
                    Welcome, <?= htmlspecialchars(explode(' ', $user_name)[0]) ?>! 👋
                </h2>
                <p class="text-sm text-slate-300 font-medium leading-relaxed">
                    Let's get your provider profile set up in 3 quick steps.
                </p>
            </div>



            <!-- Step tracker -->
            <div class="space-y-4 pt-2">
                <?php
                $side_steps = [
                    ['title' => 'Professional Details', 'sub' => 'Role, skills & location'],
                    ['title' => 'Service & Rate Info',  'sub' => 'Hourly rate & availability'],
                    ['title' => 'Platform Rules',       'sub' => 'Review and accept terms'],
                ];
                foreach ($side_steps as $si => $ss):
                ?>
                <div class="flex items-center gap-3 side-step-item" data-step="<?= $si + 1 ?>">
                    <div class="w-7 h-7 rounded-full border-2 border-white/40 flex items-center justify-center text-[11px] font-black text-white side-step-circle shrink-0 transition-all duration-300">
                        <?= $si + 1 ?>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white side-step-label transition-colors duration-300"><?= $ss['title'] ?></p>
                        <p class="text-[11px] text-slate-300 font-medium mt-0.5"><?= $ss['sub'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Bottom step count -->
        <div class="relative z-10 border-t border-white/20 pt-4 flex items-center justify-between text-xs text-slate-300 font-medium">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Account Active</span>
            </div>
            <span id="left-step-label">Step 1 of 3</span>
        </div>
    </aside>

    <!-- RIGHT COLUMN: Form Wizard (≈70%) -->
    <main class="lg:col-span-8 flex flex-col bg-white overflow-y-auto min-h-screen">

        <!-- Mobile logo + step dots bar -->
        <div class="border-b border-slate-100 px-6 sm:px-12 pt-8 sm:pt-10 pb-5 flex items-center justify-between">
            <!-- Mobile logo only -->
            <div class="lg:hidden flex items-center gap-2 text-lg font-extrabold text-[#0A2342]">
                <span>Scriptly</span>
                <span class="w-5 h-5 bg-[#1952E1] rounded-full flex items-center justify-center">
                    <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
            <!-- Step progress dots (always visible) -->
            <div class="flex items-center gap-2 ml-auto" id="dot-row-wrap">
                <div class="flex gap-1.5" id="dot-row">
                    <span class="w-6 h-1.5 rounded-full bg-[#1952E1] transition-all duration-300"></span>
                    <span class="w-2 h-1.5 rounded-full bg-slate-200 transition-all duration-300"></span>
                    <span class="w-2 h-1.5 rounded-full bg-slate-200 transition-all duration-300"></span>
                </div>
                <span class="text-[10px] text-slate-400 font-bold ml-1" id="mobile-step-label">Step 1 of 3</span>
            </div>
        </div>

        <!-- ── Form Body ── -->
        <div class="flex-1 px-8 sm:px-12 py-8">
        <div class="max-w-lg w-full space-y-6">

            <!-- Back button -->
            <button type="button" id="btn-back" class="hidden items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors group cursor-pointer">
                <span class="w-7 h-7 rounded-full bg-slate-100 group-hover:bg-slate-200 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </span>
                Back
            </button>

            <!-- Error alert -->
            <div id="form-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-xs font-medium px-4 py-3 rounded-[3px]"></div>

            <!-- ===================================================
                 STEP 1: Professional Details
                 =================================================== -->
            <div id="step-1" class="step-content active space-y-5">
                <div>
                    <h1 class="text-2xl font-extrabold text-[#0A2342] tracking-tight" style="font-family:'Space Grotesk',sans-serif;">Professional Details</h1>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Tell us about your professional background and service area.</p>
                </div>

                <!-- Phone -->
                <div class="space-y-1.5">
                    <label for="phone_number" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-700">Mobile Phone Number</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </span>
                        <input type="tel" id="phone_number" name="phone_number" placeholder="e.g. +234 801 234 5678" autocomplete="tel" class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-[3px] text-sm transition-colors" required>
                    </div>
                </div>

                <!-- Address -->
                <div class="space-y-1.5">
                    <label for="address" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-700">City / Physical Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <input type="text" id="address" name="address" placeholder="e.g. Victoria Island, Lagos, Nigeria" autocomplete="street-address" class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-[3px] text-sm transition-colors" required>
                    </div>
                </div>

                <!-- Primary Skill / Role -->
                <div class="space-y-1.5">
                    <label for="primary_skill" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-700">Primary Service / Skill Domain</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        </span>
                        <select id="primary_skill" name="primary_skill" class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-[3px] text-sm bg-white transition-colors appearance-none" required>
                            <option value="">Select your primary domain…</option>
                            <option value="ui_ux">UI/UX & Product Design</option>
                            <option value="web_backend">Web & Backend Engineering</option>
                            <option value="mobile">Mobile App Development</option>
                            <option value="data_science">Data Science & AI</option>
                            <option value="devops">DevOps & Cloud Infrastructure</option>
                            <option value="cybersecurity">Cybersecurity & Compliance</option>
                            <option value="copywriting">Copywriting & Content</option>
                            <option value="video_editing">Video Editing & Motion</option>
                            <option value="graphics">Graphic Design & Branding</option>
                            <option value="other">Other</option>
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </div>
                </div>

                <button type="button" id="btn-next-1" class="w-full py-3 bg-[#1952E1] hover:bg-blue-700 text-white font-extrabold text-sm rounded-[3px] shadow-sm transition-colors flex items-center justify-center gap-2">
                    Continue
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <!-- ===================================================
                 STEP 2: Service & Rate Info
                 =================================================== -->
            <div id="step-2" class="step-content space-y-5">
                <div>
                    <h1 class="text-2xl font-extrabold text-[#0A2342] tracking-tight" style="font-family:'Space Grotesk',sans-serif;">Service & Rate Info</h1>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Set your hourly rate, years of experience, and availability status.</p>
                </div>

                <!-- Years of Experience -->
                <div class="space-y-1.5">
                    <label for="years_experience" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-700">Years of Experience</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <select id="years_experience" name="years_experience" class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-[3px] text-sm bg-white transition-colors appearance-none" required>
                            <option value="">Select experience level…</option>
                            <option value="0-1">Less than 1 year</option>
                            <option value="1-2">1 – 2 years</option>
                            <option value="2-5">2 – 5 years</option>
                            <option value="5-10">5 – 10 years</option>
                            <option value="10+">10+ years</option>
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </div>
                </div>

                <!-- Availability -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-700">Availability</label>
                    <div class="grid grid-cols-3 gap-3">
                        <?php foreach (['Full-time', 'Part-time', 'Contract'] as $av): ?>
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="availability" value="<?= strtolower(str_replace('-', '_', $av)) ?>" class="avail-radio sr-only">
                            <div class="avail-card flex flex-col items-center gap-1.5 border border-slate-200 rounded-[4px] p-3 hover:border-[#1952E1] hover:bg-blue-50/20 transition-all text-center">
                                
                                <!-- Top right check -->
                                <div class="check-icon absolute top-1.5 right-1.5 opacity-0 text-[#1952E1] transition-opacity">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </div>

                                <!-- Icon Container -->
                                <div class="icon-container w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover:text-[#1952E1] transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <?php if ($av === 'Full-time'): ?>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        <?php elseif ($av === 'Part-time'): ?>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        <?php else: ?>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        <?php endif; ?>
                                    </svg>
                                </div>
                                <span class="card-text text-[11px] font-extrabold text-slate-600 group-hover:text-[#1952E1] transition-colors"><?= $av ?></span>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Short Bio -->
                <div class="space-y-1.5">
                    <label for="bio" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-700">Short Professional Bio <span class="text-slate-400 font-medium normal-case">(optional)</span></label>
                    <textarea id="bio" name="bio" rows="3" placeholder="e.g. I'm a full-stack developer with 5 years of experience building scalable PHP/React applications…" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-[3px] text-sm resize-none transition-colors"></textarea>
                </div>

                <button type="button" id="btn-next-2" class="w-full py-3 bg-[#1952E1] hover:bg-blue-700 text-white font-extrabold text-sm rounded-[3px] shadow-sm transition-colors flex items-center justify-center gap-2">
                    Continue
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <!-- ===================================================
                 STEP 3: Platform Rules & Submit
                 =================================================== -->
            <div id="step-3" class="step-content space-y-6 pb-12">
                <div>
                    <h1 class="text-2xl font-extrabold text-[#0A2342] tracking-tight" style="font-family:'Space Grotesk',sans-serif;">Platform Rules</h1>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Review and accept the provider conduct standards before proceeding.</p>
                </div>

                <!-- Brief Rules Box -->
                <div class="bg-blue-50/50 border border-blue-100 rounded-[6px] p-5">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center shrink-0 text-[#1952E1]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900 mb-1">Our Core Standard</h3>
                            <p class="text-xs text-slate-600 leading-relaxed font-medium mb-3">
                                You agree to abide by our 6 core policies, which include keeping all payments on-platform, honoring the 10-day review period, and maintaining a high standard of professional communication.
                            </p>
                            <button type="button" id="btn-open-rules" class="text-xs font-bold text-[#1952E1] hover:text-blue-700 underline underline-offset-2 transition-colors">
                                Read the 6 Core Rules
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Accept checkbox -->
                <label class="flex items-start gap-3 cursor-pointer group pt-2">
                    <div class="relative mt-0.5">
                        <input type="checkbox" id="accepted_rules" name="accepted_rules" class="sr-only peer">
                        <div class="w-5 h-5 border-2 border-slate-300 rounded-[3px] peer-checked:bg-[#1952E1] peer-checked:border-[#1952E1] transition-colors flex items-center justify-center">
                            <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>
                    <span class="text-xs text-slate-600 font-medium leading-relaxed">I have read, understood, and agree to abide by all provider conduct standards and platform policies.</span>
                </label>

                <!-- Submit -->
                <button type="button" id="btn-submit" class="w-full py-3 bg-[#0A2342] hover:bg-slate-800 text-white font-extrabold text-sm rounded-[3px] shadow-sm transition-colors flex items-center justify-center gap-2" disabled>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Complete Provider Setup
                </button>
            </div>

        </div><!-- /max-w-lg -->
        </div><!-- /form-body -->
    </main>

</div><!-- /grid -->

<!-- Scripts -->
<script src="../assets/js/scriptly-alerts.js"></script>
<script>
(function () {
    const steps     = document.querySelectorAll('.step-content');
    const btnBack   = document.getElementById('btn-back');
    const errBox    = document.getElementById('form-error');
    const leftLabel = document.getElementById('left-step-label');
    const mobileLabel = document.getElementById('mobile-step-label');
    let currentStep = 1;
    const totalSteps = 3;

    // Collected data
    const data = {};

    function showStep(n) {
        steps.forEach(s => s.classList.remove('active'));
        document.getElementById('step-' + n).classList.add('active');
        currentStep = n;
        errBox.classList.add('hidden');
        btnBack.classList.toggle('hidden', n === 1);
        btnBack.classList.toggle('flex', n > 1);
        if (leftLabel)  leftLabel.textContent  = 'Step ' + n + ' of ' + totalSteps;
        if (mobileLabel) mobileLabel.textContent = 'Step ' + n + ' of ' + totalSteps;
        updateDots(n);
        updateSideSteps(n);
    }

    function updateDots(n) {
        const dots = document.querySelectorAll('#dot-row span');
        dots.forEach((d, i) => {
            if (i < n) {
                d.classList.remove('bg-slate-200', 'w-2');
                d.classList.add('bg-[#1952E1]', 'w-6');
            } else {
                d.classList.remove('bg-[#1952E1]', 'w-6');
                d.classList.add('bg-slate-200', 'w-2');
            }
        });
    }

    function updateSideSteps(n) {
        document.querySelectorAll('.side-step-item').forEach(item => {
            const s = parseInt(item.dataset.step);
            const circle = item.querySelector('.side-step-circle');
            const label  = item.querySelector('.side-step-label');
            if (s < n) {
                circle.classList.add('bg-emerald-500', 'border-emerald-500');
                circle.innerHTML = '<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
                label.classList.replace('text-white/60', 'text-emerald-400');
            } else if (s === n) {
                circle.classList.remove('bg-emerald-500', 'border-emerald-500');
                circle.classList.add('border-white', 'text-white');
                circle.innerHTML = s;
                label.classList.replace('text-white/60', 'text-white');
            } else {
                circle.classList.remove('bg-emerald-500', 'border-emerald-500', 'border-white');
                circle.classList.add('border-white/20', 'text-white/50');
                circle.innerHTML = s;
                label.className = 'text-xs font-bold text-white/60 side-step-label transition-colors duration-300';
            }
        });
    }

    function showError(msg) {
        errBox.textContent = msg;
        errBox.classList.remove('hidden');
        errBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Availability radio toggle styling
    document.querySelectorAll('.availability-option').forEach(label => {
        label.addEventListener('click', () => {
            document.querySelectorAll('.availability-option').forEach(l => {
                l.classList.remove('border-[#1952E1]', 'bg-blue-50/40');
            });
            label.classList.add('border-[#1952E1]', 'bg-blue-50/40');
            label.querySelector('input').checked = true;
        });
    });

    // Accept rules — unlock submit
    document.getElementById('accepted_rules').addEventListener('change', function () {
        document.getElementById('btn-submit').disabled = !this.checked;
    });

    // Step 1 → 2
    document.getElementById('btn-next-1').addEventListener('click', () => {
        const phone   = document.getElementById('phone_number').value.trim();
        const address = document.getElementById('address').value.trim();
        const skill   = document.getElementById('primary_skill').value;
        if (!phone)   return showError('Please enter your mobile phone number.');
        if (!address) return showError('Please enter your city or address.');
        if (!skill)   return showError('Please select your primary skill domain.');
        data.phone_number   = phone;
        data.address        = address;
        data.primary_skill  = skill;
        showStep(2);
    });

    // Step 2 → 3
    document.getElementById('btn-next-2').addEventListener('click', () => {
        const exp   = document.getElementById('years_experience').value;
        const avail = document.querySelector('input[name="availability"]:checked');
        if (!exp)   return showError('Please select your years of experience.');
        if (!avail) return showError('Please select your availability.');
        data.years_experience = exp;
        data.availability     = avail.value;
        data.bio              = document.getElementById('bio').value.trim();
        showStep(3);
    });

    // Back
    btnBack.addEventListener('click', () => {
        if (currentStep > 1) showStep(currentStep - 1);
    });

    // Submit
    document.getElementById('btn-submit').addEventListener('click', async () => {
        const btn = document.getElementById('btn-submit');
        data.primary_role    = 'provider';
        data.accepted_rules  = true;

        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Setting up your account…';

        try {
            const res  = await fetch('../api/auth/complete-onboarding.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(data)
            });
            const json = await res.json();
            if (json.success) {
                ScriptlyToast.success('Profile setup complete! Welcome to your provider workspace…', 'Welcome!');
                setTimeout(() => { window.location.href = 'app/index.php'; }, 1600);
            } else {
                showError(json.message || 'Something went wrong. Please try again.');
                btn.disabled = false;
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Complete Provider Setup';
            }
        } catch (err) {
            showError('A network error occurred. Please check your connection.');
            btn.disabled = false;
            btn.innerHTML = 'Complete Provider Setup';
        }
    });

    // Init
    showStep(1);

    // Modal Logic
    const btnOpenRules = document.getElementById('btn-open-rules');
    const btnCloseRules = document.getElementById('btn-close-rules');
    const modalOverlay = document.getElementById('rules-modal-overlay');

    if (btnOpenRules && btnCloseRules && modalOverlay) {
        btnOpenRules.addEventListener('click', () => {
            modalOverlay.classList.remove('hidden');
        });
        btnCloseRules.addEventListener('click', () => {
            modalOverlay.classList.add('hidden');
        });
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                modalOverlay.classList.add('hidden');
            }
        });
    }

})();
</script>

<!-- Rules Modal -->
<div id="rules-modal-overlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white w-full max-w-lg rounded-[6px] shadow-xl overflow-hidden flex flex-col max-h-[80vh]">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-extrabold text-slate-900">Platform Rules & Standards</h3>
            <button id="btn-close-rules" class="text-slate-400 hover:text-slate-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto space-y-4 text-sm text-slate-600 font-medium leading-relaxed">
            <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-[#1952E1]/10 text-[#1952E1] flex items-center justify-center shrink-0 mt-0.5"><span class="text-[10px] font-extrabold">1</span></div>
                <span>You must complete and pass the domain skill assessment quiz before bidding on any project.</span>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-[#1952E1]/10 text-[#1952E1] flex items-center justify-center shrink-0 mt-0.5"><span class="text-[10px] font-extrabold">2</span></div>
                <span>Identity verification (NIN + liveness video) is mandatory before your Verified Pro badge is activated.</span>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-[#1952E1]/10 text-[#1952E1] flex items-center justify-center shrink-0 mt-0.5"><span class="text-[10px] font-extrabold">3</span></div>
                <span>All deliverables must be submitted via the platform messaging system. Off-platform payments are strictly prohibited.</span>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-[#1952E1]/10 text-[#1952E1] flex items-center justify-center shrink-0 mt-0.5"><span class="text-[10px] font-extrabold">4</span></div>
                <span>Milestone payments are held in escrow and released after the 10-day client review window unless a dispute is raised.</span>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-[#1952E1]/10 text-[#1952E1] flex items-center justify-center shrink-0 mt-0.5"><span class="text-[10px] font-extrabold">5</span></div>
                <span>You may not bid on more than 10 projects simultaneously while your KYC is pending.</span>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-[#1952E1]/10 text-[#1952E1] flex items-center justify-center shrink-0 mt-0.5"><span class="text-[10px] font-extrabold">6</span></div>
                <span>Maintaining a minimum 4.0 star rating is required to retain Verified Pro status. You agree to respond to client messages within 48 hours.</span>
            </div>
        </div>
    </div>
</div>
</body>
</html>

