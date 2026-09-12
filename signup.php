<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Scriptly Verified Marketplace</title>
    
    <!-- Primary SEO Meta Tags -->
    <meta name="title" content="Sign Up - Scriptly Verified Marketplace">
    <meta name="description" content="Create your free Scriptly account to request services, collaborate on projects, or become a verified professional.">
    <meta name="robots" content="index, follow">

    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Load Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800&display=swap" rel="stylesheet">
    
    <!-- Scriptly Custom Alerts & Toast Stylesheet -->
    <link rel="stylesheet" href="assets/css/scriptly-alerts.css">
    
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
        /* Custom Fancy Vertical Y Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
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
<body class="bg-brand-bg text-brand-dark font-sans antialiased selection:bg-blue-200 selection:text-blue-900">

    <!-- 2-Column Split Screen Auth Layout (No Public Header/Footer) -->
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12 overflow-hidden">
        
        <!-- LEFT COLUMN: Visual Brand & Testimonial Showcase (lg:col-span-5) -->
        <aside class="lg:col-span-5 hidden lg:flex flex-col justify-between text-white p-8 xl:p-10 relative overflow-hidden bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=1200');">
            <!-- Overlay for text readability -->
            <div class="absolute inset-0 bg-gray-900/60 pointer-events-none z-0"></div>

            <!-- Top Brand Logo -->
            <div class="relative z-10">
                <a href="./" class="flex items-center space-x-2 text-2xl font-extrabold tracking-tight text-white">
                    <span>Scriptly</span>
                    <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-600 text-white rounded-full text-[10px] font-bold">✓</span>
                </a>
            </div>

            <!-- Middle Value Proposition & Quote Card -->
            <div class="relative z-10 max-w-sm space-y-5">
                <span class="bg-[#ffda79] text-brand-dark font-extrabold text-[11px] px-3 py-1 rounded-full uppercase tracking-wider shadow">
                    Verified Quality Assurance
                </span>
                <h2 class="font-serif text-3xl font-bold tracking-tight text-white leading-tight">
                    Hire with 100% confidence. Get paid with zero delay.
                </h2>
                <blockquote class="bg-white/10 backdrop-blur-md rounded-[3px] p-4 border border-white/15 text-xs leading-relaxed text-gray-200">
                    "Scriptly's mandatory skill assessment testing and milestone escrow protection eliminated all quality risks for our team."
                    <footer class="mt-2 text-[11px] font-bold text-[#ffda79]">— Tech Director, Lagos</footer>
                </blockquote>
            </div>

            <!-- Bottom Trust Badges -->
            <div class="relative z-10 pt-4 border-t border-white/15 flex items-center justify-between text-xs text-gray-400 font-medium">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Pre-Assessed Talent</span>
                </div>
                <span>Milestone Protected</span>
            </div>
        </aside>

        <!-- RIGHT COLUMN: Compact Form Workspace (lg:col-span-7) -->
        <main class="lg:col-span-7 flex flex-col justify-between p-6 sm:p-10 bg-white overflow-y-auto">
            
            <!-- Top Navigation Bar -->
            <div class="flex items-center justify-between mb-4 text-xs">
                <a href="./" class="inline-flex items-center text-gray-500 hover:text-brand-dark font-semibold transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Back to Home</span>
                </a>
            </div>

            <!-- Compact Form Wrapper (max-w-sm for sleek sizing) -->
            <div class="max-w-sm w-full mx-auto my-auto space-y-4">
                
                <!-- Form Header -->
                <div>
                    <h1 class="font-serif text-2xl sm:text-3xl font-bold text-brand-dark mt-1.5 tracking-tight">Create your account</h1>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Join Scriptly to request services, post projects, or offer verified skills.</p>
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
                    <span class="bg-white px-3 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute">Or register with email</span>
                </div>

                <!-- Custom Alert Toast Banner -->
                <div id="custom-alert" class="hidden transform transition-all duration-300 p-3.5 rounded-[3px] border text-xs font-bold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <span id="alert-icon" class="text-sm"></span>
                        <span id="alert-message"></span>
                    </div>
                    <button id="close-alert-btn" class="text-current opacity-70 hover:opacity-100 text-sm focus:outline-none">&times;</button>
                </div>

                <!-- Single Step Registration Form -->
                <form id="signup-form" class="space-y-3.5" novalidate>
                    
                    <!-- Full Name -->
                    <div>
                        <label for="full_name" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Full Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </span>
                            <input type="text" id="full_name" name="full_name" autocomplete="name" required placeholder="e.g. Alex Johnson" class="w-full pl-9 pr-3 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium transition-colors">
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </span>
                            <input type="email" id="email" name="email" autocomplete="email" required placeholder="name@example.com" class="w-full pl-9 pr-3 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium transition-colors">
                        </div>
                    </div>

                    <!-- Password with Lock Icon & Interactive Eye Toggle -->
                    <div>
                        <label for="password" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </span>
                            <input type="password" id="password" name="password" autocomplete="new-password" required placeholder="At least 8 characters" class="w-full pl-9 pr-9 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium transition-colors">
                            
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

                    <div class="flex items-start gap-2 pt-1">
                        <input type="checkbox" id="terms_agreed" name="terms_agreed" class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="terms_agreed" class="text-[11px] text-gray-600 leading-normal">
                            I agree to Scriptly's <a href="terms" target="_blank" class="text-blue-600 font-bold hover:underline">Terms of Service</a> and <a href="privacy" target="_blank" class="text-blue-600 font-bold hover:underline">Privacy Policy</a>.
                        </label>
                    </div>

                    <button type="submit" id="submit-btn" class="w-full py-3 bg-brand-dark text-white rounded-full font-bold text-xs hover:bg-slate-800 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-md mt-2">
                        <span id="btn-text">Create Account →</span>
                        <svg id="btn-spinner" class="w-4 h-4 text-white animate-spin hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>

                </form>

                <div class="pt-3 border-t border-gray-100 text-center text-xs text-gray-500 font-medium">
                    Already have a Scriptly account? <a href="login" class="text-blue-600 font-bold hover:underline ml-0.5">Log in →</a>
                </div>

            </div>

            <!-- Footer Baseline -->
            <div class="mt-6 text-center text-[11px] text-gray-400 font-medium">
                © <?php echo date('Y'); ?> Scriptly Platform Inc. • Developed by <span class="text-brand-dark font-bold">Scriptly Team</span>
            </div>

        </main>

    </div>

    <!-- Scriptly Custom Alerts & Toast Subsystem -->
    <script src="assets/js/scriptly-alerts.js"></script>

    <!-- JavaScript Handlers -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const signupForm = document.getElementById('signup-form');
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnSpinner = document.getElementById('btn-spinner');
            
            const passwordInput = document.getElementById('password');
            const togglePasswordBtn = document.getElementById('toggle-password');
            const eyeIconShow = document.getElementById('eye-icon-show');
            const eyeIconHide = document.getElementById('eye-icon-hide');

            const googleBtn = document.getElementById('google-btn');
            const customAlert = document.getElementById('custom-alert');
            const alertIcon = document.getElementById('alert-icon');
            const alertMessage = document.getElementById('alert-message');
            const closeAlertBtn = document.getElementById('close-alert-btn');

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

            // Google OAuth trigger
            googleBtn.addEventListener('click', () => {
                showAlert('info', 'Google Single Sign-On initialization. Redirecting to Google Auth portal...');
            });

            // Password Eye Toggle
            togglePasswordBtn.addEventListener('click', () => {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                eyeIconShow.classList.toggle('hidden', isPassword);
                eyeIconHide.classList.toggle('hidden', !isPassword);
            });

            // Single Step AJAX Form Submission
            signupForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const fullName = document.getElementById('full_name').value.trim();
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value;
                const termsAgreed = document.getElementById('terms_agreed').checked;

                if (!fullName) {
                    showAlert('error', 'Please enter your full name.');
                    return;
                }

                if (!email || !email.includes('@')) {
                    showAlert('error', 'Please enter a valid email address.');
                    return;
                }

                if (password.length < 8) {
                    showAlert('error', 'Password must be at least 8 characters.');
                    return;
                }

                if (!termsAgreed) {
                    showAlert('error', 'You must agree to the Terms of Service & Privacy Policy.');
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.classList.remove('cursor-pointer');
                submitBtn.classList.add('cursor-not-allowed', 'opacity-75');
                btnText.textContent = 'Creating Account...';
                btnSpinner.classList.remove('hidden');
                customAlert.classList.add('hidden');

                const formData = {
                    full_name: fullName,
                    email: email,
                    password: password,
                    terms_agreed: termsAgreed,
                    role: 'client'
                };

                try {
                    const response = await fetch('api/auth/register.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    const data = await response.json();

                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => {
                            const redirect = data.redirect || 'verify-email.php';
                            const host = window.location.host;
                            const protocol = window.location.protocol + '//';
                            const isLocal = window.location.pathname.includes('/creda/');
                            let targetUrl = '';
                            if (isLocal) {
                                targetUrl = protocol + host + '/creda/' + redirect;
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
                        }, 1500);
                    } else {
                        showAlert('error', data.message || 'An error occurred. Please try again.');
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('cursor-not-allowed', 'opacity-75');
                        submitBtn.classList.add('cursor-pointer');
                        btnText.textContent = 'Create Account →';
                        btnSpinner.classList.add('hidden');
                    }
                } catch (err) {
                    showAlert('error', 'Network error. Please check your connection.');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('cursor-not-allowed', 'opacity-75');
                    submitBtn.classList.add('cursor-pointer');
                    btnText.textContent = 'Create Account →';
                    btnSpinner.classList.add('hidden');
                }
            });
        });
    </script>

</body>
</html>

