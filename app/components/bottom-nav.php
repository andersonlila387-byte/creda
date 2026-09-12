<!-- ==========================================================================
     MOBILE BOTTOM DOCK NAVIGATION (5 Items Only, Screens < 768px)
     ========================================================================== -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 px-2 py-1.5 shadow-[0_-4px_16px_rgba(0,0,0,0.06)] select-none">
    <div class="grid grid-cols-5 items-center justify-around text-center w-full max-w-md mx-auto">
        
        <!-- 1. Home / Dashboard -->
        <a href="<?= route_url('index.php') ?>" class="flex flex-col items-center justify-center py-1 text-[10px] font-bold transition-colors <?php echo $active_tab === 'dashboard' ? 'text-[#1952E1]' : 'text-slate-500 hover:text-slate-900'; ?>">
            <div class="w-8 h-8 rounded-[3px] <?php echo $active_tab === 'dashboard' ? 'bg-blue-50 text-[#1952E1]' : 'text-slate-500'; ?> flex items-center justify-center">
                <i class="ph-bold ph-squares-four text-lg"></i>
            </div>
            <span class="mt-0.5">Home</span>
        </a>

        <!-- 2. Projects -->
        <a href="<?= route_url('my-projects.php') ?>" class="flex flex-col items-center justify-center py-1 text-[10px] font-bold transition-colors <?php echo $active_tab === 'projects' ? 'text-[#1952E1]' : 'text-slate-500 hover:text-slate-900'; ?>">
            <div class="w-8 h-8 rounded-[3px] <?php echo $active_tab === 'projects' ? 'bg-blue-50 text-[#1952E1]' : 'text-slate-500'; ?> flex items-center justify-center">
                <i class="ph-bold ph-folder-simple text-lg"></i>
            </div>
            <span class="mt-0.5">Projects</span>
        </a>

        <!-- 3. Center Elevated Quick Post CTA Button (Rounded Circular Floating Action Button) -->
        <a href="<?= route_url('post-project.php') ?>" class="flex flex-col items-center justify-center -mt-5 group" title="Post a Project">
            <div class="w-12 h-12 bg-[#1952E1] hover:bg-blue-700 text-white rounded-full flex items-center justify-center shadow-lg shadow-blue-600/35 border-2 border-white ring-2 ring-blue-100 transition-transform active:scale-95 group-hover:scale-105">
                <i class="ph-bold ph-plus text-xl"></i>
            </div>
            <span class="text-[10px] font-bold text-[#1952E1] mt-0.5">Post</span>
        </a>

        <!-- 4. Find Talent -->
        <a href="<?= route_url('talent.php') ?>" class="flex flex-col items-center justify-center py-1 text-[10px] font-bold transition-colors <?php echo $active_tab === 'talent' ? 'text-[#1952E1]' : 'text-slate-500 hover:text-slate-900'; ?>">
            <div class="w-8 h-8 rounded-[3px] <?php echo $active_tab === 'talent' ? 'bg-blue-50 text-[#1952E1]' : 'text-slate-500'; ?> flex items-center justify-center">
                <i class="ph-bold ph-users text-lg"></i>
            </div>
            <span class="mt-0.5">Talent</span>
        </a>

        <!-- 5. More Menu Trigger (Slides out half-screen modal) -->
        <button type="button" id="mobile-more-trigger" class="flex flex-col items-center justify-center py-1 text-[10px] font-bold text-slate-500 hover:text-slate-900 transition-colors cursor-pointer" aria-label="Open More Options">
            <div class="w-8 h-8 rounded-[3px] text-slate-500 flex items-center justify-center">
                <i class="ph-bold ph-dots-three-outline-vertical text-lg"></i>
            </div>
            <span class="mt-0.5">More</span>
        </button>

    </div>
</nav>

<!-- ==========================================================================
     HALF-SCREEN SLIDE-UP "MORE" BOTTOM SHEET MODAL (Mobile View Only)
     ========================================================================== -->
<div id="mobile-more-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 opacity-0 pointer-events-none transition-opacity duration-300 md:hidden"></div>

<div id="mobile-more-sheet" class="fixed left-0 right-0 bottom-0 z-50 bg-white rounded-t-[14px] shadow-2xl border-t border-slate-200 transform translate-y-full transition-transform duration-300 ease-out max-h-[55vh] flex flex-col md:hidden select-none">
    
    <!-- Top Drag Pill -->
    <div class="pt-2.5 pb-1 flex justify-center cursor-pointer" id="mobile-more-handle">
        <div class="w-10 h-1 bg-slate-300 rounded-full"></div>
    </div>

    <!-- Sheet Header -->
    <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-sm text-slate-900">More Menus</h3>
            <p class="text-[11px] text-slate-400">Additional workspace actions & tools</p>
        </div>
        <button type="button" id="mobile-more-close" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-[3px] transition-colors" aria-label="Close modal">
            <i class="ph-bold ph-x text-sm"></i>
        </button>
    </div>

    <!-- Scrollable Content List of Left-Out Menus -->
    <div class="overflow-y-auto p-4 space-y-3 flex-1">
        
        <!-- Menu Grid -->
        <div class="grid grid-cols-2 gap-2.5">
            
            <!-- 1. Wallet & Escrow -->
            <a href="<?= route_url('wallet.php') ?>" class="flex items-center gap-3 p-3 bg-slate-50 hover:bg-white border border-slate-200/80 hover:border-[#1952E1] rounded-[3px] transition-all group shadow-sm">
                <div class="w-8 h-8 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center shrink-0">
                    <i class="ph-bold ph-wallet text-base"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="text-xs font-bold text-slate-800 group-hover:text-[#1952E1] truncate">Escrow Wallet</div>
                    <div class="text-[10px] text-slate-400 truncate">Balances & Deposits</div>
                </div>
            </a>

            <!-- 2. Direct Messages -->
            <a href="<?= route_url('messages.php') ?>" class="flex items-center gap-3 p-3 bg-slate-50 hover:bg-white border border-slate-200/80 hover:border-[#1952E1] rounded-[3px] transition-all group shadow-sm">
                <div class="w-8 h-8 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center shrink-0 relative">
                    <i class="ph-bold ph-chat-circle-dots text-base"></i>
                    <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-amber-500 text-white text-[8px] font-bold rounded-full flex items-center justify-center">2</span>
                </div>
                <div class="overflow-hidden">
                    <div class="text-xs font-bold text-slate-800 group-hover:text-[#1952E1] truncate">Messages</div>
                    <div class="text-[10px] text-slate-400 truncate">Candidate Chats</div>
                </div>
            </a>

            <!-- 3. Profile & Settings -->
            <a href="<?= route_url('settings.php') ?>" class="flex items-center gap-3 p-3 bg-slate-50 hover:bg-white border border-slate-200/80 hover:border-[#1952E1] rounded-[3px] transition-all group shadow-sm">
                <div class="w-8 h-8 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center shrink-0">
                    <i class="ph-bold ph-gear-six text-base"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="text-xs font-bold text-slate-800 group-hover:text-[#1952E1] truncate">Settings</div>
                    <div class="text-[10px] text-slate-400 truncate">Account & KYC</div>
                </div>
            </a>

            <!-- 4. Switch Account Role -->
            <a href="<?= route_url('switch-role.php', ['role' => 'provider']) ?>" class="flex items-center gap-3 p-3 bg-slate-50 hover:bg-white border border-slate-200/80 hover:border-[#1952E1] rounded-[3px] transition-all group shadow-sm">
                <div class="w-8 h-8 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center shrink-0">
                    <i class="ph-bold ph-arrows-left-right text-base"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="text-xs font-bold text-[#1952E1] truncate">Switch Role</div>
                    <div class="text-[10px] text-slate-400 truncate">To Provider</div>
                </div>
            </a>

        </div>

        <!-- Log Out Action Card -->
        <div class="pt-2 border-t border-slate-100">
            <a href="../logout.php" class="flex items-center justify-center gap-2 p-2.5 w-full bg-red-50 hover:bg-red-100 text-red-600 rounded-[3px] text-xs font-bold transition-colors">
                <i class="ph-bold ph-sign-out text-base"></i>
                <span>Log Out of Account</span>
            </a>
        </div>

    </div>
</div>


