<?php
$page_title = 'Admin Login';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/brand.php';
require_once __DIR__ . '/../config/security.php';

// If already logged in, redirect to dashboard
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-[#EFF2F7]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal — Cliniconnect</title>
    <meta name="robots" content="noindex, nofollow">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://api.fontshare.com/v2/css?f[]=clash-display@700,600,500,400&f[]=satoshi@900,700,500,400&display=swap" rel="stylesheet">
    
    <!-- Local CSS -->
    <link rel="stylesheet" href="../assets/css/tailwind.min.css">
    
    <style>
        body {
            font-family: 'Satoshi', sans-serif;
            background-color: #EFF2F7;
        }
    </style>
</head>
<body class="min-h-screen bg-[#EFF2F7] flex items-center justify-center p-4">

    <!-- Card Container -->
    <div class="max-w-md w-full bg-white border border-slate-200/90 rounded-[3px] shadow-lg p-6 sm:p-8 space-y-6">
        
        <!-- Branding -->
        <div class="flex flex-col items-center gap-2 text-center">
            <div class="w-10 h-10 bg-[#0A2342] text-white rounded-[3px] flex items-center justify-center font-black text-xl shadow-sm">
                🛡️
            </div>
            <div class="flex flex-col mt-1">
                <span class="text-xs font-black tracking-tight text-slate-900 leading-none">Cliniconnect</span>
                <span class="text-[9px] font-bold text-slate-500 tracking-widest uppercase mt-0.5 leading-none">ADMIN SUBSYSTEM</span>
            </div>
        </div>

        <div class="text-center">
            <h2 class="text-lg font-black text-slate-900 tracking-tight">Administrative Authentication</h2>
            <p class="text-[11px] text-slate-400 mt-0.5">Authorized security personnel only</p>
        </div>

        <!-- Alert -->
        <div id="login-alert" class="hidden bg-rose-50 border border-rose-200 text-rose-700 text-xs p-3 rounded-[3px] font-semibold"></div>

        <!-- Form -->
        <form id="login-form" class="space-y-4">
            <div class="space-y-1">
                <label for="username_or_email" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Admin Username or Email</label>
                <input type="text" id="username_or_email" name="username_or_email" required placeholder="admin@cliniconnect.com" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#0A2342] focus:bg-white transition-colors">
            </div>

            <div class="space-y-1">
                <label for="password" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Security Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#0A2342] focus:bg-white transition-colors">
            </div>

            <button type="submit" id="btn-submit" class="w-full py-3 bg-[#0A2342] hover:bg-slate-800 text-white rounded-[3px] font-black text-xs transition-colors flex items-center justify-center gap-2 shadow-md">
                <span id="btn-text">Authenticate Credentials</span>
            </button>
        </form>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('login-form');
            const alertBox = document.getElementById('login-alert');
            const submitBtn = document.getElementById('btn-submit');
            const btnText = document.getElementById('btn-text');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                alertBox.classList.add('hidden');
                
                submitBtn.disabled = true;
                btnText.textContent = 'Verifying Authenticity...';

                const formData = {
                    username_or_email: document.getElementById('username_or_email').value.trim(),
                    password: document.getElementById('password').value
                };

                const apiEndpoint = '<?php echo getPortalUrl("client", "api/auth/admin-login.php"); ?>';

                try {
                    const response = await fetch(apiEndpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    const data = await response.json();

                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        alertBox.textContent = data.message || 'Authentication failed.';
                        alertBox.classList.remove('hidden');
                        submitBtn.disabled = false;
                        btnText.textContent = 'Authenticate Credentials';
                    }
                } catch (err) {
                    alertBox.textContent = 'An error occurred connecting to the security server.';
                    alertBox.classList.remove('hidden');
                    submitBtn.disabled = false;
                    btnText.textContent = 'Authenticate Credentials';
                }
            });
        });
    </script>
</body>
</html>
