<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/brand.php';

$pending_email = $_SESSION['pending_user']['email'] ?? $_SESSION['user_email'] ?? 'your registered email';
$debug_otp = $_SESSION['pending_user']['otp_code'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - Cliniconnect Provider Portal</title>
    
    <!-- Primary SEO Meta Tags -->
    <meta name="title" content="Verify Email - Cliniconnect Provider Portal">
    <meta name="description" content="Verify your email address with your 6-digit OTP code to activate your Cliniconnect Provider account.">
    <meta name="robots" content="noindex, nofollow">

    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800&display=swap" rel="stylesheet">
    
    <!-- Central Alerts Stylesheet -->
    <link rel="stylesheet" href="../assets/css/scriptly-alerts.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
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

    <!-- 2-Column Split Screen Auth Layout -->
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12 overflow-hidden">
        
        <!-- LEFT COLUMN: Visual Brand & Security Showcase -->
        <aside class="lg:col-span-5 hidden lg:flex flex-col justify-between text-white p-8 xl:p-10 relative overflow-hidden bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&q=80&w=1200');">
            <!-- Overlay for text readability -->
            <div class="absolute inset-0 bg-gray-900/60 pointer-events-none z-0"></div>

            <div class="relative z-10">
                <a href="../" class="flex items-center space-x-2 text-2xl font-extrabold tracking-tight text-white">
                    <span>Cliniconnect Pro</span>
                    <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-600 text-white rounded-full text-[10px] font-bold">✓</span>
                </a>
            </div>

            <div class="relative z-10 max-w-sm space-y-5">
                <span class="bg-[#ffda79] text-brand-dark font-extrabold text-[11px] px-3 py-1 rounded-full uppercase tracking-wider shadow">
                    Step 2: Account Verification
                </span>
                <h2 class="font-serif text-3xl font-bold tracking-tight text-white leading-tight">
                    Verify your email to unlock the Provider Workspace.
                </h2>
                <blockquote class="bg-white/10 backdrop-blur-md rounded-[3px] p-4 border border-white/15 text-xs leading-relaxed text-gray-200">
                    "Email verification ensures that our health/care providers are authentic, maintaining patient trust and platform integrity."
                    <footer class="mt-2 text-[11px] font-bold text-[#ffda79]">— Cliniconnect Verification Desk</footer>
                </blockquote>
            </div>

            <div class="relative z-10 pt-4 border-t border-white/15 flex items-center justify-between text-xs text-gray-400 font-medium">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Identity Protected</span>
                </div>
                <span>Step 2 of 3</span>
            </div>
        </aside>

        <!-- RIGHT COLUMN: Compact Form Workspace -->
        <main class="lg:col-span-7 flex flex-col justify-between p-6 sm:p-10 bg-white overflow-y-auto">
            
            <!-- Top Navigation Bar -->
            <div class="flex items-center justify-between mb-6 text-xs">
                <a href="signup.php" class="inline-flex items-center text-gray-500 hover:text-brand-dark font-semibold transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Back to Signup</span>
                </a>
            </div>

            <!-- Compact Form Wrapper -->
            <div class="max-w-sm w-full mx-auto my-auto space-y-5">
                
                <!-- Form Header -->
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-[3px]">Verification Required</span>
                    <h1 class="font-serif text-2xl sm:text-3xl font-bold text-brand-dark mt-1.5 tracking-tight">Enter 6-digit code</h1>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                        We sent a 6-digit verification code to <strong class="text-brand-dark"><?php echo htmlspecialchars($pending_email); ?></strong>. Please check your <strong class="text-brand-dark">Inbox</strong> and <strong class="text-blue-600">Spam / Junk</strong> folder.
                    </p>
                </div>

                <!-- Custom Alert Toast Banner -->
                <div id="custom-alert" class="hidden transform transition-all duration-300 p-3.5 rounded-[3px] border text-xs font-bold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <span id="alert-icon" class="text-sm"></span>
                        <span id="alert-message"></span>
                    </div>
                    <button id="close-alert-btn" class="text-current opacity-70 hover:opacity-100 text-sm focus:outline-none">&times;</button>
                </div>

                <!-- 6-Digit OTP Inputs Form -->
                <form id="otp-form" class="space-y-5" novalidate>
                    
                    <div class="flex justify-between gap-1.5 sm:gap-2" id="otp-inputs-container">
                        <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="otp-input w-11 sm:w-12 h-16 text-center font-extrabold text-xl sm:text-2xl rounded-[3px] border border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all shadow-sm" required>
                        <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="otp-input w-11 sm:w-12 h-16 text-center font-extrabold text-xl sm:text-2xl rounded-[3px] border border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all shadow-sm" required>
                        <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="otp-input w-11 sm:w-12 h-16 text-center font-extrabold text-xl sm:text-2xl rounded-[3px] border border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all shadow-sm" required>
                        <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="otp-input w-11 sm:w-12 h-16 text-center font-extrabold text-xl sm:text-2xl rounded-[3px] border border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all shadow-sm" required>
                        <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="otp-input w-11 sm:w-12 h-16 text-center font-extrabold text-xl sm:text-2xl rounded-[3px] border border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all shadow-sm" required>
                        <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="otp-input w-11 sm:w-12 h-16 text-center font-extrabold text-xl sm:text-2xl rounded-[3px] border border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all shadow-sm" required>
                    </div>

                    <button type="submit" id="submit-btn" class="w-full py-3.5 bg-brand-dark text-white rounded-full font-bold text-xs hover:bg-slate-800 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-md">
                        <span id="btn-text">Verify Email & Continue →</span>
                        <svg id="btn-spinner" class="w-4 h-4 text-white animate-spin hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>

                </form>

                <!-- Resend Code Bar -->
                <div class="text-center text-xs text-gray-500 font-medium pt-2">
                    Didn't receive the email? 
                    <button type="button" id="resend-btn" class="inline-flex items-center gap-1.5 text-blue-600 font-bold hover:underline ml-1 cursor-pointer transition-all">
                        <span id="resend-text">Resend Code</span>
                        <svg id="resend-spinner" class="w-3.5 h-3.5 text-blue-600 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                    <span id="timer-text" class="text-gray-400 font-normal ml-1"></span>
                </div>

                <div class="text-center text-xs font-semibold pt-4 border-t border-gray-100 mt-2">
                    <a href="logout.php" class="text-gray-400 hover:text-brand-dark transition-colors">Log out / Sign in with a different account</a>
                </div>

            </div>

            <!-- Footer Baseline -->
            <div class="mt-8 text-center text-[11px] text-gray-400 font-medium">
                © <?php echo date('Y'); ?> Cliniconnect Pro • Developed by <span class="text-brand-dark font-bold">Cliniconnect Team</span>
            </div>

        </main>

    </div>

    <!-- Custom Alerts JS Subsystem -->
    <script src="../assets/js/scriptly-alerts.js"></script>

    <!-- JavaScript Auto-Focusing OTP & AJAX Form Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const otpForm = document.getElementById('otp-form');
            const otpInputs = document.querySelectorAll('.otp-input');
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnSpinner = document.getElementById('btn-spinner');

            const resendBtn = document.getElementById('resend-btn');
            const timerText = document.getElementById('timer-text');

            const customAlert = document.getElementById('custom-alert');
            const alertIcon = document.getElementById('alert-icon');
            const alertMessage = document.getElementById('alert-message');
            const closeAlertBtn = document.getElementById('close-alert-btn');

            // Auto-focus first input
            if (otpInputs.length > 0) {
                otpInputs[0].focus();
            }

            // OTP Input auto-advance
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    const value = e.target.value;
                    if (value.length > 0 && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !input.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });

                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
                    if (/^\d{6}$/.test(pasteData)) {
                        pasteData.split('').forEach((char, i) => {
                            if (otpInputs[i]) otpInputs[i].value = char;
                        });
                        otpInputs[otpInputs.length - 1].focus();
                    }
                });
            });

            function showAlert(type, message) {
                customAlert.className = 'transform transition-all duration-300 p-3.5 rounded-[3px] border text-xs font-bold flex items-center justify-between shadow-sm';
                if (type === 'success') {
                    customAlert.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-800');
                    alertIcon.textContent = '✓';
                } else if (type === 'error') {
                    customAlert.classList.add('bg-rose-50', 'border-rose-200', 'text-rose-800');
                    alertIcon.textContent = '⚠️';
                } else {
                    customAlert.classList.add('bg-blue-50', 'border-blue-200', 'text-blue-800');
                    alertIcon.textContent = 'ℹ️';
                }
                alertMessage.textContent = message;
                customAlert.classList.remove('hidden');
            }

            closeAlertBtn.addEventListener('click', () => {
                customAlert.classList.add('hidden');
            });

            // Resend Code Handler
            let countdown = 0;
            resendBtn.addEventListener('click', async () => {
                if (countdown > 0 || resendBtn.disabled) return;

                resendBtn.disabled = true;
                resendBtn.classList.remove('cursor-pointer');
                resendBtn.classList.add('opacity-60', 'cursor-not-allowed');

                const resendText = document.getElementById('resend-text');
                const resendSpinner = document.getElementById('resend-spinner');

                if (resendText) resendText.textContent = 'Sending email...';
                if (resendSpinner) resendSpinner.classList.remove('hidden');

                try {
                    const response = await fetch('../api/auth/resend-otp.php');
                    const data = await response.json();

                    if (resendSpinner) resendSpinner.classList.add('hidden');

                    if (data.success) {
                        showAlert('success', data.message);

                        countdown = 60;
                        if (resendText) resendText.textContent = 'Resend Code';

                        const timer = setInterval(() => {
                            countdown--;
                            timerText.textContent = `(${countdown}s)`;
                            if (countdown <= 0) {
                                clearInterval(timer);
                                timerText.textContent = '';
                                resendBtn.disabled = false;
                                resendBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                                resendBtn.classList.add('cursor-pointer');
                            }
                        }, 1000);
                    } else {
                        showAlert('error', data.message);
                        if (resendText) resendText.textContent = 'Resend Code';
                        resendBtn.disabled = false;
                        resendBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                        resendBtn.classList.add('cursor-pointer');
                    }
                } catch (err) {
                    if (resendSpinner) resendSpinner.classList.add('hidden');
                    if (resendText) resendText.textContent = 'Resend Code';
                    showAlert('error', 'Network error. Please try again.');
                    resendBtn.disabled = false;
                    resendBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                    resendBtn.classList.add('cursor-pointer');
                }
            });

            // Submit OTP Form Handler
            otpForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                let fullOtp = '';
                otpInputs.forEach(input => fullOtp += input.value.trim());

                if (fullOtp.length < 6) {
                    showAlert('error', 'Please enter all 6 digits of your verification code.');
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.classList.remove('cursor-pointer');
                submitBtn.classList.add('cursor-not-allowed', 'opacity-75');
                btnText.textContent = 'Verifying Code...';
                btnSpinner.classList.remove('hidden');
                customAlert.classList.add('hidden');

                try {
                    const response = await fetch('../api/auth/verify-otp.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ otp_code: fullOtp })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => {
                            const redirect = data.redirect || 'onboarding.php';
                            const host = window.location.host;
                            const protocol = window.location.protocol + '//';
                            const isLocal = window.location.pathname.includes('/creda/');
                            let targetUrl = '';
                            if (isLocal) {
                                // Local subfolders: mapping onboarding -> onboarding.php
                                const cleanRedirect = (redirect === 'onboarding') ? 'onboarding.php' : redirect;
                                targetUrl = protocol + host + '/creda/' + cleanRedirect;
                            } else {
                                const parts = host.split('.');
                                const parentDomain = (parts.length >= 2) ? parts.slice(-2).join('.') : host;
                                if (redirect.startsWith('provider/')) {
                                    targetUrl = protocol + 'provider.' + parentDomain + '/' + redirect.replace('provider/', '');
                                } else if (redirect.startsWith('admin/')) {
                                    targetUrl = protocol + 'admin.' + parentDomain + '/' + redirect.replace('admin/', '');
                                } else {
                                    targetUrl = protocol + parentDomain + '/' + redirect;
                                }
                            }
                            window.location.href = targetUrl;
                        }, 1200);
                    } else {
                        showAlert('error', data.message || 'Verification failed. Please check your code.');
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('cursor-not-allowed', 'opacity-75');
                        submitBtn.classList.add('cursor-pointer');
                        btnText.textContent = 'Verify Email & Continue →';
                        btnSpinner.classList.add('hidden');
                    }
                } catch (err) {
                    showAlert('error', 'Network error. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('cursor-not-allowed', 'opacity-75');
                    submitBtn.classList.add('cursor-pointer');
                    btnText.textContent = 'Verify Email & Continue →';
                    btnSpinner.classList.add('hidden');
                }
            });
        });
    </script>

</body>
</html>
