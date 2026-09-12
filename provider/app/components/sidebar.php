<!-- Full-Height Left Vertical Navigation Dock for Provider -->
<aside id="sidebar" class="hidden md:flex w-20 lg:w-24 h-screen sticky top-0 bg-[#EFF2F7] border-r border-slate-200/60 flex-col items-center justify-between py-6 px-2 shrink-0 select-none z-30">
    
    <!-- Top Infinity / Loop Logo -->
    <a href="index.php" class="w-12 h-12 bg-white hover:bg-slate-50 rounded-2xl flex items-center justify-center text-brandDark shadow-[0_2px_8px_rgba(0,0,0,0.04)] border border-slate-200/80 transition-transform hover:scale-105 group" title="Scriptly Provider">
        <svg class="w-6 h-6 text-slate-900 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18.178 8c5.096 0 5.096 8 0 8-2.69 0-4.7-2.115-6.178-4-1.478-1.885-3.488-4-6.178-4-5.096 0-5.096 8 0 8 2.69 0 4.7-2.115 6.178-4 1.478-1.885 3.488-4 6.178-4z"></path>
        </svg>
    </a>

    <!-- Middle Vertical Navigation Icon Pills -->
    <nav class="flex flex-col items-center gap-2.5 my-auto">
        
        <!-- 1. Home / Dashboard -->
        <a href="index.php" class="w-11 h-11 rounded-full flex items-center justify-center transition-all <?php echo $active_tab === 'dashboard' ? 'bg-[#1952E1] text-white shadow-lg shadow-blue-500/30 hover:scale-105' : 'bg-slate-200/70 hover:bg-white text-slate-500 hover:text-slate-900 hover:shadow-sm'; ?>" title="Dashboard">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
        </a>

        <!-- 2. Skill Assessment & Quizzes -->
        <a href="assessment.php" class="w-11 h-11 rounded-full flex items-center justify-center transition-all <?php echo $active_tab === 'assessment' ? 'bg-[#1952E1] text-white shadow-lg shadow-blue-500/30' : 'bg-slate-200/70 hover:bg-white text-slate-500 hover:text-slate-900 hover:shadow-sm'; ?> relative" title="Skill Assessment">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
            <?php if (!$is_verified_pro): ?>
                <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-amber-500 rounded-full ring-2 ring-[#EFF2F7] animate-ping"></span>
            <?php endif; ?>
        </a>

        <!-- 3. Active Contracts & Deliverables -->
        <a href="contracts.php" class="w-11 h-11 rounded-full flex items-center justify-center transition-all <?php echo $active_tab === 'contracts' ? 'bg-[#1952E1] text-white shadow-lg shadow-blue-500/30' : 'bg-slate-200/70 hover:bg-white text-slate-500 hover:text-slate-900 hover:shadow-sm'; ?>" title="Contracts">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
        </a>

        <!-- 4. Browse Jobs & Proposals -->
        <a href="jobs.php" class="w-11 h-11 rounded-full flex items-center justify-center transition-all <?php echo $active_tab === 'jobs' ? 'bg-[#1952E1] text-white shadow-lg shadow-blue-500/30' : 'bg-slate-200/70 hover:bg-white text-slate-500 hover:text-slate-900 hover:shadow-sm'; ?>" title="Jobs">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
        </a>

        <!-- 5. Service Packages (Create/Manage) -->
        <a href="create-package.php" class="w-11 h-11 rounded-full flex items-center justify-center transition-all <?php echo $active_tab === 'packages' ? 'bg-[#1952E1] text-white shadow-lg shadow-blue-500/30' : 'bg-slate-200/70 hover:bg-white text-slate-500 hover:text-slate-900 hover:shadow-sm'; ?>" title="Post Service Package">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </a>

        <!-- 6. Escrow Earnings & Wallet -->
        <a href="earnings.php" class="w-11 h-11 rounded-full flex items-center justify-center transition-all <?php echo $active_tab === 'earnings' ? 'bg-[#1952E1] text-white shadow-lg shadow-blue-500/30' : 'bg-slate-200/70 hover:bg-white text-slate-500 hover:text-slate-900 hover:shadow-sm'; ?>" title="Earnings">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        </a>

        <!-- 7. Settings (Gear) -->
        <a href="settings.php" class="w-11 h-11 rounded-full flex items-center justify-center transition-all <?php echo $active_tab === 'settings' ? 'bg-[#1952E1] text-white shadow-lg shadow-blue-500/30' : 'bg-slate-200/70 hover:bg-white text-slate-500 hover:text-slate-900 hover:shadow-sm'; ?>" title="Settings">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </a>

    </nav>

    <!-- Bottom Profile Avatar & Switcher -->
    <div class="flex flex-col items-center gap-2.5">
        
        <!-- Switch Role Pill -->
        <a href="../../app/switch-role.php?role=client" class="w-9 h-9 rounded-full bg-slate-200/70 hover:bg-white text-slate-500 hover:text-[#1952E1] flex items-center justify-center transition-all hover:shadow-sm" title="Switch to Client Workspace">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
        </a>

        <!-- User Avatar (Rounded Full) -->
        <a href="settings.php" class="w-11 h-11 rounded-full overflow-hidden ring-2 ring-white shadow-sm flex items-center justify-center bg-gradient-to-tr from-blue-600 to-indigo-600 text-white text-xs font-black hover:ring-blue-500 hover:scale-105 transition-all" title="<?php echo htmlspecialchars($user_name); ?> (Provider Settings)">
            <?php echo htmlspecialchars($initials); ?>
        </a>
    </div>

</aside>
