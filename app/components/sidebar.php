<!-- =========================================================================
     FULL-HEIGHT VERTICAL NAVIGATION DOCK (Exact UI.HTML Style)
     ========================================================================= -->
<aside id="sidebar" class="hidden md:flex w-20 md:w-20 lg:w-24 h-screen sticky top-0 bg-[#EFF2F7] border-r border-slate-200/60 flex-col items-center justify-between py-6 px-2 shrink-0 select-none z-30">
    
    <!-- Top Infinity / Brand Logo -->
    <a href="../index.php" class="w-12 h-12 bg-white hover:bg-slate-50 rounded-2xl flex items-center justify-center text-slate-900 shadow-[0_2px_8px_rgba(0,0,0,0.04)] border border-slate-200/80 transition-transform hover:scale-105 group" title="Scriptly Home">
        <?php echo renderLogoIcon('w-6 h-6 text-slate-900 group-hover:scale-110 transition-transform'); ?>
    </a>

    <!-- Middle Vertical Navigation Icon Pills -->
    <nav class="flex flex-col items-center gap-2.5 my-auto">
        
        <!-- 1. Dashboard (Home) -->
        <div class="relative group flex items-center justify-center">
            <a href="<?= route_url('index.php') ?>" class="w-11 h-11 rounded-full <?php echo ($active_tab === 'dashboard') ? 'bg-[#1952E1] text-white shadow-lg shadow-blue-500/30' : 'bg-slate-200/70 hover:bg-white text-slate-500 hover:text-slate-900 hover:shadow-sm'; ?> flex items-center justify-center transition-all hover:scale-105" aria-label="Dashboard">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            </a>
            <!-- Floating Tooltip -->
            <span class="absolute left-full ml-3 px-2.5 py-1 bg-slate-900 text-white text-xs font-semibold rounded-lg whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 shadow-xl z-50 border border-slate-700/80 -translate-x-1 group-hover:translate-x-0">
                Dashboard
            </span>
        </div>

        <!-- 2. Post a Project (Plus / Post) -->
        <div class="relative group flex items-center justify-center">
            <a href="<?= route_url('post-project.php') ?>" class="w-11 h-11 rounded-full <?php echo ($active_tab === 'post-project') ? 'bg-[#1952E1] text-white shadow-lg shadow-blue-500/30' : 'bg-slate-200/70 hover:bg-white text-slate-500 hover:text-slate-900 hover:shadow-sm'; ?> flex items-center justify-center transition-all hover:scale-105" aria-label="Post a Project">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </a>
            <span class="absolute left-full ml-3 px-2.5 py-1 bg-slate-900 text-white text-xs font-semibold rounded-lg whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 shadow-xl z-50 border border-slate-700/80 -translate-x-1 group-hover:translate-x-0">
                Post a Project
            </span>
        </div>

        <!-- 3. My Projects (Folder) -->
        <div class="relative group flex items-center justify-center">
            <a href="<?= route_url('my-projects.php') ?>" class="w-11 h-11 rounded-full <?php echo ($active_tab === 'projects' || $active_tab === 'my-projects') ? 'bg-[#1952E1] text-white shadow-lg shadow-blue-500/30' : 'bg-slate-200/70 hover:bg-white text-slate-500 hover:text-slate-900 hover:shadow-sm'; ?> flex items-center justify-center transition-all hover:scale-105" aria-label="My Projects">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
            </a>
            <span class="absolute left-full ml-3 px-2.5 py-1 bg-slate-900 text-white text-xs font-semibold rounded-lg whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 shadow-xl z-50 border border-slate-700/80 -translate-x-1 group-hover:translate-x-0">
                My Projects
            </span>
        </div>

        <!-- 4. Find Talent (Community / Users) -->
        <div class="relative group flex items-center justify-center">
            <a href="<?= route_url('talent.php') ?>" class="w-11 h-11 rounded-full <?php echo ($active_tab === 'talent') ? 'bg-[#1952E1] text-white shadow-lg shadow-blue-500/30' : 'bg-slate-200/70 hover:bg-white text-slate-500 hover:text-slate-900 hover:shadow-sm'; ?> flex items-center justify-center transition-all hover:scale-105" aria-label="Find Talent">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </a>
            <span class="absolute left-full ml-3 px-2.5 py-1 bg-slate-900 text-white text-xs font-semibold rounded-lg whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 shadow-xl z-50 border border-slate-700/80 -translate-x-1 group-hover:translate-x-0">
                Find Talent
            </span>
        </div>

        <!-- 5. Wallet & Escrow (Credit Card) -->
        <div class="relative group flex items-center justify-center">
            <a href="<?= route_url('wallet.php') ?>" class="w-11 h-11 rounded-full <?php echo ($active_tab === 'wallet') ? 'bg-[#1952E1] text-white shadow-lg shadow-blue-500/30' : 'bg-slate-200/70 hover:bg-white text-slate-500 hover:text-slate-900 hover:shadow-sm'; ?> flex items-center justify-center transition-all hover:scale-105" aria-label="Wallet & Escrow">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </a>
            <span class="absolute left-full ml-3 px-2.5 py-1 bg-slate-900 text-white text-xs font-semibold rounded-lg whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 shadow-xl z-50 border border-slate-700/80 -translate-x-1 group-hover:translate-x-0">
                Wallet & Escrow
            </span>
        </div>

        <!-- 6. Settings (Gear) -->
        <div class="relative group flex items-center justify-center">
            <a href="<?= route_url('settings.php') ?>" class="w-11 h-11 rounded-full <?php echo ($active_tab === 'settings') ? 'bg-[#1952E1] text-white shadow-lg shadow-blue-500/30' : 'bg-slate-200/70 hover:bg-white text-slate-500 hover:text-slate-900 hover:shadow-sm'; ?> flex items-center justify-center transition-all hover:scale-105" aria-label="Settings">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </a>
            <span class="absolute left-full ml-3 px-2.5 py-1 bg-slate-900 text-white text-xs font-semibold rounded-lg whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 shadow-xl z-50 border border-slate-700/80 -translate-x-1 group-hover:translate-x-0">
                Settings
            </span>
        </div>

    </nav>

    <!-- Bottom Avatar Portrait & Switcher -->
    <div class="flex flex-col items-center gap-2.5">
        
        <!-- Switch Role Pill -->
        <div class="relative group flex items-center justify-center">
            <a href="<?= route_url('switch-role.php', ['role' => 'provider']) ?>" class="w-9 h-9 rounded-full bg-slate-200/70 hover:bg-white text-slate-500 hover:text-[#1952E1] flex items-center justify-center transition-all hover:shadow-sm" aria-label="Switch to Provider">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </a>
            <span class="absolute left-full ml-3 px-2.5 py-1 bg-slate-900 text-white text-xs font-semibold rounded-lg whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 shadow-xl z-50 border border-slate-700/80 -translate-x-1 group-hover:translate-x-0">
                Become a Provider
            </span>
        </div>

        <!-- User Avatar (Rounded Full) -->
        <div class="relative group flex items-center justify-center">
            <a href="<?= route_url('settings.php') ?>" class="w-11 h-11 rounded-full overflow-hidden ring-2 ring-white shadow-sm cursor-pointer hover:ring-[#1952E1] hover:scale-105 transition-all flex items-center justify-center bg-[#0A2342] text-white font-bold text-xs" aria-label="User Profile">
                <?= htmlspecialchars($initials ?? 'CR'); ?>
            </a>
            <span class="absolute left-full ml-3 px-2.5 py-1 bg-slate-900 text-white text-xs font-semibold rounded-lg whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 shadow-xl z-50 border border-slate-700/80 -translate-x-1 group-hover:translate-x-0">
                <?= htmlspecialchars($user_name ?? 'Profile'); ?>
            </span>
        </div>

    </div>
</aside>



