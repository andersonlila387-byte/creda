<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Scriptly Verified Marketplace</title>
    
    <!-- Primary SEO Meta Tags -->
    <meta name="title" content="Forgot Password - Scriptly Verified Marketplace">
    <meta name="description" content="Recover your Scriptly account password via secure email verification.">
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
        
        <!-- LEFT COLUMN: Visual Brand & Security Showcase (lg:col-span-5) -->
        <aside class="lg:col-span-5 hidden lg:flex flex-col justify-between text-white p-8 xl:p-10 relative overflow-hidden bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&q=80&w=1200');">
            <!-- Overlay for text readability -->
            <div class="absolute inset-0 bg-gray-900/60 pointer-events-none z-0"></div>

            <!-- Top Brand Logo -->
            <div class="relative z-10">
                <a href="./" class="flex items-center space-x-2 text-2xl font-extrabold tracking-tight text-white">
                    <span>Scriptly</span>
                    <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-600 text-white rounded-full text-[10px] font-bold">✓</span>
                </a>
            </div>

            <!-- Middle Value Proposition Card -->
            <div class="relative z-10 max-w-sm space-y-5">
                <span class="bg-[#ffda79] text-brand-dark font-extrabold text-[11px] px-3 py-1 rounded-full uppercase tracking-wider shadow">
                    Account Security
                </span>
                <h2 class="font-serif text-3xl font-bold tracking-tight text-white leading-tight">
                    Secure, tokenized password recovery.
                </h2>
                <blockquote class="bg-white/10 backdrop-blur-md rounded-[3px] p-4 border border-white/15 text-xs leading-relaxed text-gray-200">
                    "Your account security is our top priority. Reset links are single-use, cryptographically generated, and expire in 60 minutes."
                    <footer class="mt-2 text-[11px] font-bold text-[#ffda79]">— Scriptly Security Desk</footer>
                </blockquote>
            </div>

            <!-- Bottom Badges -->
            <div class="relative z-10 pt-4 border-t border-white/15 flex items-center justify-between text-xs text-gray-400 font-medium">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Single-Use Tokens</span>
                </div>
                <span>Encrypted Verification</span>
            </div>
        </aside>

        <!-- RIGHT COLUMN: Compact Form Workspace (lg:col-span-7) -->
        <main class="lg:col-span-7 flex flex-col justify-between p-6 sm:p-10 bg-white overflow-y-auto">
            
            <!-- Top Navigation Bar -->
            <div class="flex items-center justify-between mb-6 text-xs">
                <a href="login" class="inline-flex items-center text-gray-500 hover:text-brand-dark font-semibold transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Back to Login</span>
                </a>
            </div>

            <!-- Compact Form Wrapper (max-w-sm) -->
            <div class="max-w-sm w-full mx-auto my-auto space-y-5">
                
                <!-- Form Header -->
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-[3px]">Password Recovery</span>
                    <h1 class="font-serif text-2xl sm:text-3xl font-bold text-brand-dark mt-1.5 tracking-tight">Forgot your password?</h1>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Enter your registered email address and we'll send you a password reset link. Please check your <strong class="text-brand-dark">Inbox</strong> and <strong class="text-blue-600">Spam / Junk</strong> folder.</p>
                </div>

                <!-- Custom Alert Toast Banner -->
                <div id="custom-alert" class="hidden transform transition-all duration-300 p-3.5 rounded-[3px] border text-xs font-bold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <span id="alert-icon" class="text-sm"></span>
                        <span id="alert-message"></span>
                    </div>
                    <button id="close-alert-btn" class="text-current opacity-70 hover:opacity-100 text-sm focus:outline-none">&times;</button>
                </div>

                <!-- Debug Local Test Action Button (Appears on local success) -->
                <div id="debug-test-container" class="hidden bg-blue-50 border border-blue-200 rounded-[3px] p-3 text-center">
                    <p class="text-[11px] font-semibold text-blue-900 mb-2">Local Development Mode Enabled:</p>
                    <a id="debug-test-link" href="#" class="inline-block bg-blue-600 text-white font-bold text-xs px-4 py-2 rounded-full hover:bg-blue-700 transition-colors">
                        Simulate Email Click & Reset Password →
                    </a>
                </div>

                <!-- Compact AJAX Form -->
                <form id="forgot-form" class="space-y-4" novalidate>
                    
                    <!-- Email Address with Mail Icon -->
                    <div>
                        <label for="email" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-1">Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </span>
                            <input type="email" id="email" name="email" autocomplete="email" required placeholder="name@example.com" class="w-full pl-10 pr-4 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium transition-colors">
                        </div>
                    </div>

                    <button type="submit" id="submit-btn" class="w-full py-3 bg-brand-dark text-white rounded-full font-bold text-xs hover:bg-slate-800 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-md mt-2">
                        <span id="btn-text">Send Reset Instructions →</span>
                        <svg id="btn-spinner" class="w-4 h-4 text-white animate-spin hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>

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

    <!-- JavaScript Form Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const forgotForm = document.getElementById('forgot-form');
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnSpinner = document.getElementById('btn-spinner');
            
            const customAlert = document.getElementById('custom-alert');
            const alertIcon = document.getElementById('alert-icon');
            const alertMessage = document.getElementById('alert-message');
            const closeAlertBtn = document.getElementById('close-alert-btn');

            const debugTestContainer = document.getElementById('debug-test-container');
            const debugTestLink = document.getElementById('debug-test-link');

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

            forgotForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                submitBtn.disabled = true;
                submitBtn.classList.remove('cursor-pointer');
                submitBtn.classList.add('cursor-not-allowed', 'opacity-75');
                btnText.textContent = 'Sending...';
                btnSpinner.classList.remove('hidden');
                customAlert.classList.add('hidden');
                debugTestContainer.classList.add('hidden');

                const formData = {
                    email: document.getElementById('email').value
                };

                try {
                    const response = await fetch('api/auth/forgot-password.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    const data = await response.json();

                    if (data.success) {
                        showAlert('success', data.message);
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('cursor-not-allowed', 'opacity-75');
                        submitBtn.classList.add('cursor-pointer');
                        btnText.textContent = 'Send Reset Instructions →';
                        btnSpinner.classList.add('hidden');

                        if (data.debug_reset_link) {
                            debugTestLink.href = data.debug_reset_link;
                            debugTestContainer.classList.remove('hidden');
                        }
                    } else {
                        showAlert('error', data.message || 'An error occurred. Please try again.');
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('cursor-not-allowed', 'opacity-75');
                        submitBtn.classList.add('cursor-pointer');
                        btnText.textContent = 'Send Reset Instructions →';
                        btnSpinner.classList.add('hidden');
                    }
                } catch (err) {
                    showAlert('error', 'Network error. Please check your connection.');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('cursor-not-allowed', 'opacity-75');
                    submitBtn.classList.add('cursor-pointer');
                    btnText.textContent = 'Send Reset Instructions →';
                    btnSpinner.classList.add('hidden');
                }
            });
        });
    </script>

</body>
</html>
