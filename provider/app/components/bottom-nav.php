<!-- ==========================================================================
     MOBILE BOTTOM DOCK NAVIGATION (5 Items Only, Screens < 768px)
     ========================================================================== -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 px-2 py-1.5 shadow-[0_-4px_16px_rgba(0,0,0,0.06)] select-none">
    <div class="grid grid-cols-5 items-center justify-around text-center w-full max-w-md mx-auto">
        
        <a href="index.php" class="flex flex-col items-center justify-center py-1 text-[10px] font-bold transition-colors <?php echo $active_tab === 'dashboard' ? 'text-[#1952E1]' : 'text-slate-500 hover:text-slate-900'; ?>">
            <div class="w-8 h-8 rounded-[3px] <?php echo $active_tab === 'dashboard' ? 'bg-blue-50 text-[#1952E1]' : 'text-slate-500'; ?> flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            </div>
            <span class="mt-0.5">Home</span>
        </a>

        <!-- 2. Contracts -->
        <a href="contracts.php" class="flex flex-col items-center justify-center py-1 text-[10px] font-bold transition-colors <?php echo $active_tab === 'contracts' ? 'text-[#1952E1]' : 'text-slate-500 hover:text-slate-900'; ?>">
            <div class="w-8 h-8 rounded-[3px] <?php echo $active_tab === 'contracts' ? 'bg-blue-50 text-[#1952E1]' : 'text-slate-500'; ?> flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
            </div>
            <span class="mt-0.5">Contracts</span>
        </a>

        <!-- 3. Center Elevated Quick Jobs CTA Button (Rounded Circular Floating Action Button) -->
        <a href="jobs.php" class="flex flex-col items-center justify-center -mt-5 group" title="Browse Jobs & Proposals">
            <div class="w-12 h-12 bg-[#1952E1] hover:bg-blue-700 text-white rounded-full flex items-center justify-center shadow-lg shadow-blue-600/35 border-2 border-white ring-2 ring-blue-100 transition-transform active:scale-95 group-hover:scale-105">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <span class="text-[10px] font-bold text-[#1952E1] mt-0.5">Find Jobs</span>
        </a>

        <!-- 4. Earnings / Wallet -->
        <a href="earnings.php" class="flex flex-col items-center justify-center py-1 text-[10px] font-bold transition-colors <?php echo $active_tab === 'earnings' ? 'text-[#1952E1]' : 'text-slate-500 hover:text-slate-900'; ?>">
            <div class="w-8 h-8 rounded-[3px] <?php echo $active_tab === 'earnings' ? 'bg-blue-50 text-[#1952E1]' : 'text-slate-500'; ?> flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <span class="mt-0.5">Earnings</span>
        </a>

        <!-- 5. More Menu Trigger (Slides out half-screen modal) -->
        <button type="button" id="mobile-more-trigger" class="flex flex-col items-center justify-center py-1 text-[10px] font-bold text-slate-500 hover:text-slate-900 transition-colors cursor-pointer" aria-label="Open More Options">
            <div class="w-8 h-8 rounded-[3px] text-slate-500 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </div>
            <span class="mt-0.5">More</span>
        </button>

    </div>
</nav>

<!-- ==========================================================================
     HALF-SCREEN SLIDE-UP "MORE" BOTTOM SHEET MODAL (Mobile View Only)
     ========================================================================= -->
<div id="mobile-more-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 opacity-0 pointer-events-none transition-opacity duration-300 md:hidden"></div>

<div id="mobile-more-sheet" class="fixed left-0 right-0 bottom-0 z-50 bg-white rounded-t-[14px] shadow-2xl border-t border-slate-200 transform translate-y-full transition-transform duration-300 ease-out max-h-[55vh] flex flex-col md:hidden select-none">
    
    <!-- Drag Handle -->
    <div class="pt-2.5 pb-1 flex justify-center cursor-pointer" id="mobile-more-handle">
        <div class="w-10 h-1 bg-slate-300 rounded-full"></div>
    </div>

    <!-- Sheet Header -->
    <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-sm text-slate-900">More Options</h3>
            <p class="text-[11px] text-slate-400">Additional actions & provider portals</p>
        </div>
        <button type="button" id="mobile-more-close" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-[3px] transition-colors" aria-label="Close modal">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- Scrollable Content -->
    <div class="overflow-y-auto p-4 space-y-3 flex-1">
        
        <!-- Menu Grid -->
        <div class="grid grid-cols-2 gap-2.5">
            
            <!-- 1. Skill Assessment -->
            <a href="assessment.php" class="flex items-center gap-3 p-3 bg-slate-50 hover:bg-white border border-slate-200/80 hover:border-[#1952E1] rounded-[3px] transition-all group shadow-sm">
                <div class="w-8 h-8 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                </div>
                <div class="overflow-hidden">
                    <div class="text-xs font-bold text-slate-800 group-hover:text-[#1952E1] truncate">Assessment</div>
                    <div class="text-[10px] text-slate-400 truncate">Skills Quizzes</div>
                </div>
            </a>



            <!-- 2. Post Service Package -->
            <a href="create-package.php" class="flex items-center gap-3 p-3 bg-slate-50 hover:bg-white border border-slate-200/80 hover:border-[#1952E1] rounded-[3px] transition-all group shadow-sm">
                <div class="w-8 h-8 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div class="overflow-hidden">
                    <div class="text-xs font-bold text-slate-800 group-hover:text-[#1952E1] truncate">Post Service</div>
                    <div class="text-[10px] text-slate-400 truncate">Create Packages</div>
                </div>
            </a>

            <!-- 3. Profile & Settings -->
            <a href="settings.php" class="flex items-center gap-3 p-3 bg-slate-50 hover:bg-white border border-slate-200/80 hover:border-[#1952E1] rounded-[3px] transition-all group shadow-sm">
                <div class="w-8 h-8 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div class="overflow-hidden">
                    <div class="text-xs font-bold text-slate-800 group-hover:text-[#1952E1] truncate">Settings</div>
                    <div class="text-[10px] text-slate-400 truncate">Profile & Security</div>
                </div>
            </a>

            <!-- 4. Switch Account Role -->
            <a href="../../app/switch-role.php?role=client" class="flex items-center gap-3 p-3 bg-slate-50 hover:bg-white border border-slate-200/80 hover:border-[#1952E1] rounded-[3px] transition-all group shadow-sm">
                <div class="w-8 h-8 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-[#1952E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </div>
                <div class="overflow-hidden">
                    <div class="text-xs font-bold text-[#1952E1] truncate">Switch Role</div>
                    <div class="text-[10px] text-slate-400 truncate">To Client View</div>
                </div>
            </a>

        </div>

        <!-- Log Out Action Card -->
        <div class="pt-2 border-t border-slate-100">
            <a href="logout.php" class="flex items-center justify-center gap-2 p-2.5 w-full bg-red-50 hover:bg-red-100 text-red-600 rounded-[3px] text-xs font-bold transition-colors">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span>Log Out of Account</span>
            </a>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const trigger = document.getElementById('mobile-more-trigger');
    const close = document.getElementById('mobile-more-close');
    const backdrop = document.getElementById('mobile-more-backdrop');
    const sheet = document.getElementById('mobile-more-sheet');

    function openMore() {
        backdrop.classList.remove('pointer-events-none', 'opacity-0');
        backdrop.classList.add('opacity-100');
        sheet.classList.remove('translate-y-full');
    }

    function closeMore() {
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0', 'pointer-events-none');
        sheet.classList.add('translate-y-full');
    }

    if (trigger && sheet && backdrop) {
        trigger.addEventListener('click', openMore);
        close.addEventListener('click', closeMore);
        backdrop.addEventListener('click', closeMore);
    }
});
</script>
