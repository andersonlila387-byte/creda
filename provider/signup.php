<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/brand.php';

// Store preference that the user is signing up as a Provider
$_SESSION['auth_role_preference'] = 'provider';

// Active session verification & automatic redirect
if (!empty($_SESSION['user_logged_in']) && !empty($_SESSION['user_id'])) {
    if (empty($_SESSION['user_verified'])) {
        header("Location: " . getPortalUrl('provider', 'verify-email.php'));
        exit;
    }
    if (isset($_SESSION['onboarding_completed']) && !$_SESSION['onboarding_completed']) {
        header("Location: " . getPortalUrl('client', 'onboarding.php'));
        exit;
    }
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'provider') {
        if (!empty($_SESSION['is_verified_pro'])) {
            header("Location: " . getPortalUrl('provider', 'app/index.php'));
        } else {
            if (isset($_SESSION['assessment_status']) && $_SESSION['assessment_status'] === 'passed') {
                header("Location: " . getPortalUrl('provider', 'app/pending-verification.php'));
            } else {
                header("Location: " . getPortalUrl('provider', 'app/assessment.php'));
            }
        }
        exit;
    }
    header("Location: " . getPortalUrl('client', 'app/index.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-[#EFF2F7]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Sign Up - Scriptly</title>
    
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800&display=swap" rel="stylesheet">
    
    <!-- Local CSS -->
    <link rel="stylesheet" href="../assets/css/tailwind.min.css">
    
    <!-- Custom Toast Alert Stylesheet -->
    <link rel="stylesheet" href="../assets/css/scriptly-alerts.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8f7f5;
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
                    <path d="M18.178 8c5.096 0 5.096 8 0 8-2.69 0-4.7-2.115-6.178-4-1.478-1.885-3.488-4-6.178-4-5.096 0-5.096 8 0 8 2.69 0 4.7-2.115 6.178-4 1.478-1.885-3.488-4-6.178-4-5.096 0-5.096 8 0 8 2.69 0 4.7-2.115 6.178-4 1.478-1.885-3.488-4-6.178-4z"></path>
                </svg>
            </div>
        </div>
    </div>
    <style>
        @keyframes preloader-orbit { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @keyframes preloader-pulse { 0%, 100% { transform: scale(1); opacity: 0.95; } 50% { transform: scale(1.15); opacity: 1; } }
    </style>

    <!-- 2-Column Split Screen Auth Layout (Matching Client Side Layout) -->
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12 overflow-hidden">
        
        <!-- LEFT COLUMN: Visual Brand & Pro Testimonial Showcase (lg:col-span-5) -->
        <aside class="lg:col-span-5 hidden lg:flex flex-col justify-between text-white p-8 xl:p-10 relative overflow-hidden bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1200');">
            <!-- Overlay for text readability -->
            <div class="absolute inset-0 bg-gray-900/60 pointer-events-none z-0"></div>

            <!-- Top Brand Logo -->
            <div class="relative z-10">
                <a href="index.php" class="flex items-center space-x-2 text-2xl font-extrabold tracking-tight text-white group">
                    <span>Scriptly</span>
                    <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-600 text-white rounded-full text-[10px] font-bold">✓</span>
                </a>
            </div>

            <!-- Middle Value Proposition & Quote Card -->
            <div class="relative z-10 max-w-sm space-y-5">
                <span class="bg-blue-600 text-white font-extrabold text-[10px] px-3 py-1 rounded-full uppercase tracking-wider shadow border border-blue-500/20">
                    Provider Workspace
                </span>
                <h2 class="font-serif text-3xl font-bold tracking-tight text-white leading-tight">
                    Vetted Skill Badges, Secure Escrows, and Enforced Price Floors.
                </h2>
                <blockquote class="bg-white/10 backdrop-blur-md rounded-[3px] p-4 border border-white/15 text-xs leading-relaxed text-slate-200">
                    "Passing the assessment immediately unlocked high-paying client contracts. I never worry about lowballing or unpaid work anymore."
                    <footer class="mt-2 text-[11px] font-bold text-blue-400">— David Alao, Student Developer</footer>
                </blockquote>
            </div>

            <!-- Bottom Trust Badges -->
            <div class="relative z-10 pt-4 border-t border-white/15 flex items-center justify-between text-xs text-gray-400 font-medium">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Direct Bank Withdrawals</span>
                </div>
                <span>Pre-Vetted Auditing</span>
            </div>
        </aside>

        <!-- RIGHT COLUMN: Compact Form Workspace (lg:col-span-7) -->
        <main class="lg:col-span-7 flex flex-col justify-between p-6 sm:p-10 bg-white overflow-y-auto">
            
            <!-- Top Navigation Bar -->
            <div class="flex items-center justify-between mb-4 text-xs">
                <a href="index.php" class="inline-flex items-center text-gray-500 hover:text-brand-dark font-semibold transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Back to Home</span>
                </a>
            </div>

            <!-- Compact Form Wrapper -->
            <div class="max-w-sm w-full mx-auto my-auto space-y-4">
                
                <!-- Form Header -->
                <div>
                    <h1 class="font-serif text-2xl sm:text-3xl font-bold text-brand-dark mt-1.5 tracking-tight">Apply as a Provider</h1>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Create your profile, pass assessment tests, and start earning.</p>
                </div>

                <!-- Google Sign Up Button -->
                <button type="button" id="google-btn" class="w-full py-2.5 border border-gray-300 rounded-[3px] text-xs font-bold text-gray-700 bg-white hover:bg-gray-50 transition-all flex items-center justify-center gap-2.5 shadow-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Continue with Google</span>
                </button>

                <!-- Divider -->
                <div class="relative flex items-center justify-center my-2">
                    <div class="border-t border-gray-200 w-full"></div>
                    <span class="bg-white px-3 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute">Or apply with email</span>
                </div>

                <!-- Custom Alert Toast Banner (No Emojis) -->
                <div id="custom-alert" class="hidden transform transition-all duration-300 p-3.5 rounded-[3px] border text-xs font-bold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <span id="alert-icon" class="text-sm"></span>
                        <span id="alert-message"></span>
                    </div>
                    <button type="button" id="close-alert-btn" class="text-current opacity-70 hover:opacity-100 text-sm focus:outline-none">&times;</button>
                </div>

                <!-- Compact Form with Input Icons -->
                <form id="signup-form" class="space-y-4" novalidate>
                    
                    <!-- Full Name -->
                    <div>
                        <label for="full_name" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Full Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </span>
                            <input type="text" id="full_name" name="full_name" required placeholder="e.g. David Alao" class="w-full pl-10 pr-4 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium transition-colors">
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </span>
                            <input type="email" id="email" name="email" autocomplete="username" required placeholder="name@example.com" class="w-full pl-10 pr-4 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium transition-colors">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </span>
                            <input type="password" id="password" name="password" autocomplete="new-password" required placeholder="Min. 8 characters" class="w-full pl-10 pr-10 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium transition-colors">
                            
                            <!-- Interactive Eye Toggle Button -->
                            <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none" title="Toggle password visibility">
                                <svg id="eye-icon-show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="eye-icon-hide" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10c0 0 5.5 7 9 7s9-7 9-7M12 17v3M7.5 15.5l-2 2.5M16.5 15.5l2 2.5M4.5 12.5l-2.5 1.5M19.5 12.5l2.5 1.5"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Terms agreement checkbox -->
                    <div class="flex items-start gap-2.5 pt-1">
                        <input type="checkbox" id="terms_agreed" name="terms_agreed" required class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="terms_agreed" class="text-[11px] text-gray-600 leading-normal font-medium">I agree to the platform Terms of Service and escrow guidelines.</label>
                    </div>

                    <button type="submit" id="submit-btn" class="w-full py-3 bg-[#0A2342] text-white rounded-full font-bold text-xs hover:bg-slate-800 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-md mt-2">
                        <span id="btn-text">Submit Application</span>
                        <svg id="btn-spinner" class="w-4 h-4 text-white animate-spin hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>

                </form>

                <div class="pt-4 border-t border-gray-100 text-center text-xs text-gray-500 font-medium">
                    Already have a provider account? <a href="login.php" class="text-blue-600 font-bold hover:underline ml-0.5">Log in here →</a>
                </div>

            </div>

            <!-- Footer Baseline -->
            <div class="mt-8 text-center text-[11px] text-gray-400 font-medium">
                © <?php echo date('Y'); ?> Scriptly Platform Inc. • Developed by <span class="text-brand-dark font-bold">Scriptly Team</span>
            </div>

        </main>

    </div>

    <!-- Scriptly Custom Alerts & Toast Subsystem -->
    <script src="../assets/js/scriptly-alerts.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        const eyeShow = document.getElementById('eye-icon-show');
        const eyeHide = document.getElementById('eye-icon-hide');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                eyeShow.classList.toggle('hidden', isPassword);
                eyeHide.classList.toggle('hidden', !isPassword);
            });
        }

        const customAlert = document.getElementById('custom-alert');
        const alertIcon = document.getElementById('alert-icon');
        const alertMessage = document.getElementById('alert-message');
        const closeAlertBtn = document.getElementById('close-alert-btn');

        const showAlert = (type, message) => {
            customAlert.className = "transform transition-all duration-300 p-3.5 rounded-[3px] border text-xs font-bold flex items-center justify-between shadow-sm";
            if (type === 'success') {
                customAlert.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-800');
                alertIcon.innerHTML = `<svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
            } else if (type === 'info') {
                customAlert.classList.add('bg-blue-50', 'border-blue-200', 'text-blue-800');
                alertIcon.innerHTML = `<svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
            } else {
                customAlert.classList.add('bg-rose-50', 'border-rose-200', 'text-rose-800');
                alertIcon.innerHTML = `<svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
            }
            alertMessage.textContent = message;
            customAlert.classList.remove('hidden');
        };

        if (closeAlertBtn) {
            closeAlertBtn.addEventListener('click', () => {
                customAlert.classList.add('hidden');
            });
        }

        const googleBtn = document.getElementById('google-btn');
        if (googleBtn) {
            googleBtn.addEventListener('click', () => {
                showAlert('info', 'Google Single Sign-On initialization. Redirecting to Google Auth portal...');
            });
        }

        const signupForm = document.getElementById('signup-form');
        const submitBtn = document.getElementById('submit-btn');
        const btnText = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');

        signupForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const fullName = document.getElementById('full_name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const termsAgreed = document.getElementById('terms_agreed').checked;

            if (password.length < 8) {
                showAlert('error', 'Password must be at least 8 characters.');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.classList.add('cursor-not-allowed', 'opacity-75');
            btnText.textContent = 'Submitting...';
            btnSpinner.classList.remove('hidden');
            customAlert.classList.add('hidden');

            const formData = {
                full_name: fullName,
                email: email,
                password: password,
                terms_agreed: termsAgreed,
                role: 'provider'
            };

            try {
                const response = await fetch('../api/auth/register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('success', 'Application registered! Loading verification code step...');
                    setTimeout(() => {
                        window.location.href = '<?php echo getPortalUrl("provider", "verify-email.php"); ?>';
                    }, 1500);
                } else {
                    showAlert('error', data.message || 'An error occurred. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('cursor-not-allowed', 'opacity-75');
                    btnSpinner.classList.add('hidden');
                    btnText.textContent = 'Submit Pro Application';
                }
            } catch (err) {
                showAlert('error', 'Network error. Please check your internet connection.');
                submitBtn.disabled = false;
                submitBtn.classList.remove('cursor-not-allowed', 'opacity-75');
                btnSpinner.classList.add('hidden');
                btnText.textContent = 'Submit Pro Application';
            }
        });


        // Fade-out Preloader
        window.addEventListener('load', () => {
            const preloader = document.getElementById('page-preloader');
            if (preloader) {
                preloader.style.opacity = '0';
                setTimeout(() => preloader.style.display = 'none', 300);
            }
        });
    });
    </script>

</body>
</html>
