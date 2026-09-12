<?php
$page_title = "Help Center & Support - Scriptly Marketplace";
$page_description = "Get help with posting projects, provider verification assessments, milestone escrows, real-time chat, and dispute audit tickets.";
$active_page = "help";
$breadcrumb = [
    'category' => 'Knowledge Base & Guides',
    'title' => 'Help Center & Support',
    'subtitle' => 'Search guides, milestone rules, provider verification guides, and platform tutorials.',
    'bg_image' => 'assets/breadcrumbs/support_bg.jpg'
];
include 'includes/header.php';
?>

    <!-- Help Categories Grid -->
    <main class="max-w-7xl mx-auto px-3 sm:px-6 py-16">
        
        <!-- Search Bar -->
        <div class="relative max-w-xl mx-auto mb-16 -mt-8">
            <input type="text" placeholder="Search guides, milestone rules, verification FAQs..." class="w-full pl-12 pr-4 py-4 rounded-full bg-white text-brand-dark placeholder-gray-400 font-medium text-sm focus:outline-none shadow-lg border border-gray-200">
            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="font-serif text-3xl font-bold text-brand-dark">Browse Support Topics</h2>
            <p class="text-gray-600 text-sm mt-2">Select a topic below to find detailed walkthroughs and platform rules.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
            
            <!-- Category 1 -->
            <div class="bg-white rounded-[3px] p-7 border border-gray-200/80 hover:border-blue-500/50 transition-all duration-300 group">
                <div class="w-12 h-12 rounded-[3px] bg-blue-50 text-blue-600 flex items-center justify-center mb-5 font-bold text-xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <h3 class="font-bold text-xl text-brand-dark mb-2">Getting Started for Clients</h3>
                <p class="text-xs text-gray-600 leading-relaxed mb-4">How to request services, review pre-vetted proposals, deposit milestone escrows, and hire top talent.</p>
                <a href="faq" class="text-xs font-bold text-blue-600 hover:text-blue-700">Read 8 Articles →</a>
            </div>

            <!-- Category 2 -->
            <div class="bg-white rounded-[3px] p-7 border border-gray-200/80 hover:border-emerald-500/50 transition-all duration-300 group">
                <div class="w-12 h-12 rounded-[3px] bg-emerald-50 text-emerald-600 flex items-center justify-center mb-5 font-bold text-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h3 class="font-bold text-xl text-brand-dark mb-2">Provider Verification & Skill Tests</h3>
                <p class="text-xs text-gray-600 leading-relaxed mb-4">Passing technical assessment challenges, earning Verified Pro badges, retake rules, and scoring thresholds.</p>
                <a href="faq" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Read 6 Articles →</a>
            </div>

            <!-- Category 3 -->
            <div class="bg-white rounded-[3px] p-7 border border-gray-200/80 hover:border-amber-500/50 transition-all duration-300 group">
                <div class="w-12 h-12 rounded-[3px] bg-amber-50 text-amber-600 flex items-center justify-center mb-5 font-bold text-xl group-hover:bg-amber-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h3 class="font-bold text-xl text-brand-dark mb-2">Milestone Escrow & Payments</h3>
                <p class="text-xs text-gray-600 leading-relaxed mb-4">Escrow funding security, release approvals, 7-day review auto-timers, bank payouts, and service fees.</p>
                <a href="faq" class="text-xs font-bold text-amber-600 hover:text-amber-700">Read 10 Articles →</a>
            </div>

            <!-- Category 4 -->
            <div class="bg-white rounded-[3px] p-7 border border-gray-200/80 hover:border-purple-500/50 transition-all duration-300 group">
                <div class="w-12 h-12 rounded-[3px] bg-purple-50 text-purple-600 flex items-center justify-center mb-5 font-bold text-xl group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
                <h3 class="font-bold text-xl text-brand-dark mb-2">Real-Time Chat & File Sharing</h3>
                <p class="text-xs text-gray-600 leading-relaxed mb-4">Direct messaging troubleshooting, file attachment limits, typing receipts, and unread counts.</p>
                <a href="faq" class="text-xs font-bold text-purple-600 hover:text-purple-700">Read 5 Articles →</a>
            </div>

            <!-- Category 5 -->
            <div class="bg-white rounded-[3px] p-7 border border-gray-200/80 hover:border-rose-500/50 transition-all duration-300 group">
                <div class="w-12 h-12 rounded-[3px] bg-rose-50 text-rose-600 flex items-center justify-center mb-5 font-bold text-xl group-hover:bg-rose-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l9-4 9 4M3 6v14a2 2 0 002 2h14a2 2 0 002-2V6M3 6l9 6 9-6"></path></svg>
                </div>
                <h3 class="font-bold text-xl text-brand-dark mb-2">Disputes & Audit Desk</h3>
                <p class="text-xs text-gray-600 leading-relaxed mb-4">Opening dispute tickets, submitting contract evidence, revision request limits, and audit outcomes.</p>
                <a href="faq" class="text-xs font-bold text-rose-600 hover:text-rose-700">Read 7 Articles →</a>
            </div>

            <!-- Category 6 -->
            <div class="bg-white rounded-[3px] p-7 border border-gray-200/80 hover:border-indigo-500/50 transition-all duration-300 group">
                <div class="w-12 h-12 rounded-[3px] bg-indigo-50 text-indigo-600 flex items-center justify-center mb-5 font-bold text-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h3 class="font-bold text-xl text-brand-dark mb-2">Account & Security</h3>
                <p class="text-xs text-gray-600 leading-relaxed mb-4">Updating passwords, two-factor authentication, profile settings, and data privacy rights.</p>
                <a href="faq" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">Read 4 Articles →</a>
            </div>

        </div>

        <!-- Still Need Help Box -->
        <div class="bg-[#0A2342] text-white rounded-[3px] p-8 sm:p-12 text-center max-w-4xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="text-left">
                <h3 class="font-serif text-2xl font-bold mb-2">Still need assistance?</h3>
                <p class="text-xs text-gray-300">Our support desk is available 24/7 to inspect contracts and assist with inquiries.</p>
            </div>
            <a href="contact" class="bg-[#ffda79] text-brand-dark font-extrabold text-sm px-7 py-3.5 rounded-full hover:bg-amber-300 transition-all shrink-0">
                Contact Support Desk →
            </a>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
