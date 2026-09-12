<!-- Provider Top Header Bar with Notifications & Quick Tools -->
<header id="top-header" class="sticky top-0 z-20 bg-[#EFF2F7]/95 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center shrink-0 min-h-[60px] select-none w-full">
    
    <!-- Left: Dynamic Clickable Workspace Breadcrumb -->
    <nav class="flex items-center gap-1.5 sm:gap-2 text-xs font-bold" aria-label="Breadcrumb">
        <a href="index.php" class="text-slate-400 hover:text-slate-700 transition-colors uppercase tracking-wider hidden sm:inline-flex items-center gap-1">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
            <span>Workspace</span>
        </a>
        <span class="text-slate-300 hidden sm:inline lg:hidden">/</span>
        
        <h1 class="text-xs sm:text-sm font-bold text-slate-900 flex lg:hidden items-center gap-1.5 truncate max-w-[220px] sm:max-w-xs md:max-w-md">
            <span class="truncate"><?= htmlspecialchars($page_title ?? 'Overview') ?></span>
            <span class="w-1.5 h-1.5 rounded-full bg-[#1952E1] shrink-0"></span>
        </h1>
    </nav>

    <!-- Center: Redesigned Interactive Schedule Capsule Bar (hidden on mobile/tablet, shown on lg screens) -->
    <div class="hidden lg:flex items-center gap-3 bg-[#0A2342] text-white rounded-full py-1.5 pl-4 pr-1.5 shadow-md border border-slate-700/30 max-w-2xl w-full justify-between select-none">
        
        <!-- Live Call Pulse Indicator -->
        <div class="flex items-center gap-2 shrink-0">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400">Live Call</span>
            <span class="text-slate-550 font-light">|</span>
            <span class="text-[11px] font-bold text-slate-100 font-sans">09:00 - 09:45 AM</span>
        </div>

        <!-- Meeting Details (Client Info & Connected Dot) -->
        <div class="flex items-center gap-2 bg-slate-800/80 border border-slate-700/60 rounded-full px-3 py-1 shrink-0">
            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80" alt="Client" class="w-5 h-5 rounded-full border border-slate-600 object-cover">
            <span class="text-[11px] font-bold text-slate-100 whitespace-nowrap">Joseph J.</span>
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0" title="Connected"></span>
        </div>

        <!-- Upcoming Call Indicator -->
        <div class="flex items-center gap-2 text-slate-300 pr-1 shrink-0">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Next</span>
            <span class="text-[11px] font-semibold">10:00 AM</span>
            <div class="flex -space-x-1.5">
                <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=100&auto=format&fit=crop&q=80" class="w-4 h-4 rounded-full border border-slate-800 object-cover">
                <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&auto=format&fit=crop&q=80" class="w-4 h-4 rounded-full border border-slate-800 object-cover">
            </div>
        </div>

        <!-- Right Expand Calendar Arrow (Diagonal Up-Right) -->
        <a href="interviews.php" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-white flex items-center justify-center transition-all hover:scale-105 border border-slate-700/60 shadow-xs" title="Open Interview Calendar">
            <svg class="w-4 h-4 text-slate-350" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H10M17 7V14"></path></svg>
        </a>

    </div>

    <!-- Right Header Tools Cluster: Notifications, Messages, and Profile Dropdown -->
    <div class="flex items-center gap-2.5 sm:gap-3">
        
        <!-- Messages Button -->
        <a href="messages.php" class="w-9 h-9 sm:w-10 sm:h-10 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-full flex items-center justify-center text-slate-600 shadow-[0_2px_8px_rgba(0,0,0,0.03)] transition-transform hover:scale-105 shrink-0 relative" title="Messages">
            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <span class="absolute top-2 right-2.5 w-2.5 h-2.5 bg-rose-500 rounded-full ring-2 ring-white"></span>
        </a>

        <!-- Notifications Dropdown Button -->
        <div class="relative">
            <button id="provider-header-notif-btn" class="w-9 h-9 sm:w-10 sm:h-10 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-full flex items-center justify-center text-slate-700 hover:text-[#1952E1] transition-colors relative shadow-sm" title="Notifications">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span class="absolute top-2 right-2.5 w-2.5 h-2.5 bg-rose-500 rounded-full ring-2 ring-white"></span>
            </button>

            <!-- Notifications Dropdown Card -->
            <div id="provider-header-notif-dropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-[3px] shadow-2xl border border-slate-200/90 py-3 px-4 z-50 animate-in fade-in slide-in-from-top-2 duration-200">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                    <h4 class="text-xs font-black text-slate-900 font-heading">Notifications</h4>
                    <span class="text-[10px] font-bold text-blue-600 hover:underline cursor-pointer">Mark all as read</span>
                </div>
                <div class="space-y-2 py-2 max-h-72 overflow-y-auto">
                    <!-- Notification Item 1 -->
                    <div class="p-2.5 bg-blue-50/60 hover:bg-blue-50 rounded-[3px] flex items-start gap-3 transition-colors cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 text-sm mt-0.5">
                            <i class="ph-bold ph-briefcase"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-extrabold text-slate-900 leading-snug">Interview Scheduled</p>
                            <p class="text-[11px] text-slate-500 truncate">Google Meet call at 09:00 AM with Ted Company.</p>
                            <span class="text-[9px] text-slate-400 font-bold">15m ago</span>
                        </div>
                    </div>
                    <!-- Notification Item 2 -->
                    <div class="p-2.5 hover:bg-slate-50 rounded-[3px] flex items-start gap-3 transition-colors cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 text-sm mt-0.5">
                            <i class="ph-bold ph-credit-card"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-extrabold text-slate-900 leading-snug">Payment Cleared</p>
                            <p class="text-[11px] text-slate-500 truncate">$2,100 transferred to your available balance.</p>
                            <span class="text-[9px] text-slate-400 font-bold">1d ago</span>
                        </div>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100 text-center">
                    <a href="notifications.php" class="text-xs font-bold text-slate-600 hover:text-blue-600">View All Notifications →</a>
                </div>
            </div>
        </div>

        <!-- Profile Dropdown -->
        <div class="relative">
            <button id="provider-profile-btn" class="flex items-center gap-2 p-1 rounded-full hover:bg-slate-200/50 transition-colors focus:outline-none">
                <div class="w-9 h-9 rounded-full bg-[#0A2342] text-white flex items-center justify-center font-bold text-xs ring-2 ring-white shadow-sm">
                    <?= htmlspecialchars($initials ?? 'PR'); ?>
                </div>
                <div class="hidden md:flex flex-col text-left pr-1">
                    <span class="text-xs font-bold text-slate-900 leading-tight"><?= htmlspecialchars($user_name ?? 'Provider'); ?></span>
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide">Provider</span>
                </div>
                <svg class="w-3 h-3 text-slate-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            
            <!-- Dropdown Menu -->
            <div id="provider-profile-dropdown" class="hidden absolute right-0 mt-2 w-52 bg-white border border-slate-200/90 rounded-[3px] shadow-lg z-50 overflow-hidden">
                <div class="p-3 border-b border-slate-100 bg-slate-50">
                    <p class="font-bold text-xs text-slate-900 truncate"><?= htmlspecialchars($user_name ?? 'Provider Account'); ?></p>
                    <p class="text-[11px] text-slate-500 font-medium truncate mt-0.5"><?= htmlspecialchars($user_email ?? ''); ?></p>
                </div>
                <div class="p-1 space-y-0.5">
                    <a href="settings.php" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-[3px] transition-colors">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span>My Profile</span>
                    </a>
                    <a href="earnings.php" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-[3px] transition-colors">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Earnings Wallet</span>
                    </a>
                    <a href="contracts.php" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-[3px] transition-colors">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                        <span>Contracts</span>
                    </a>
                    <a href="../../app/switch-role.php?role=client" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-[#1952E1] hover:bg-blue-50 rounded-[3px] transition-colors">
                        <svg class="w-4 h-4 text-[#1952E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        <span>Switch to Client</span>
                    </a>
                </div>
                <div class="border-t border-slate-100 p-1">
                    <a href="logout.php" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 rounded-[3px] transition-colors">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

</header>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const pNotifBtn = document.getElementById('provider-header-notif-btn');
    const pNotifDropdown = document.getElementById('provider-header-notif-dropdown');
    const pProfileBtn = document.getElementById('provider-profile-btn');
    const pProfileDropdown = document.getElementById('provider-profile-dropdown');

    if (pNotifBtn && pNotifDropdown) {
        pNotifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            pNotifDropdown.classList.toggle('hidden');
            if (pProfileDropdown) pProfileDropdown.classList.add('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!pNotifDropdown.contains(e.target) && !pNotifBtn.contains(e.target)) {
                pNotifDropdown.classList.add('hidden');
            }
        });
    }

    if (pProfileBtn && pProfileDropdown) {
        pProfileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            pProfileDropdown.classList.toggle('hidden');
            if (pNotifDropdown) pNotifDropdown.classList.add('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!pProfileDropdown.contains(e.target) && !pProfileBtn.contains(e.target)) {
                pProfileDropdown.classList.add('hidden');
            }
        });
    }
});
</script>
