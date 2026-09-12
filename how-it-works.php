<?php
$page_title = "How Scriptly Works - Verified Service Marketplace Guide";
$page_description = "Learn how Scriptly connects clients with pre-assessed professionals using mandatory skill testing and 100% upfront milestone escrow protection.";
$active_page = "how-it-works";
$breadcrumb = [
    'category' => 'Platform Guide & Workflow',
    'title' => 'How Scriptly Works',
    'subtitle' => 'Understand our 5-step client and service provider process, milestone escrow protection, and audit rules.',
    'bg_image' => 'assets/breadcrumbs/support_bg.jpg'
];
include 'includes/header.php';
?>

    <!-- How It Works Main Container -->
    <main class="max-w-7xl mx-auto px-3 sm:px-6 py-16">
        
        <!-- Workflow Selection Header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-extrabold uppercase tracking-widest text-blue-600 bg-blue-50 px-3 py-1 rounded-[3px]">Transparent Ecosystem</span>
            <h2 class="font-serif text-3xl sm:text-5xl font-bold text-brand-dark mt-3 tracking-tight">Built for trust and quality.</h2>
            <p class="text-gray-600 text-sm sm:text-base mt-3">Choose a path below to see how Scriptly protects both clients and verified service providers.</p>
        </div>

        <!-- 2 Detailed Path Columns (Client vs Provider) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-24">
            
            <!-- Path A: For Clients -->
            <div class="bg-white rounded-[3px] p-8 border border-gray-200/80 shadow-sm space-y-8">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-5">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-serif text-2xl font-bold text-brand-dark">For Clients & Businesses</h3>
                        <span class="text-xs font-semibold text-gray-400">Hire Pre-Assessed Talent Safely</span>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="flex gap-4">
                        <span class="w-8 h-8 rounded-full bg-blue-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0">01</span>
                        <div>
                            <h4 class="font-bold text-base text-brand-dark mb-1">Post a Project Request for Free</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">Define your requirements, timeline, budget range, and category domain. No upfront posting fees.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <span class="w-8 h-8 rounded-full bg-blue-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0">02</span>
                        <div>
                            <h4 class="font-bold text-base text-brand-dark mb-1">Receive Pre-Assessed Proposals</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">Review proposals exclusively from professionals who have passed mandatory category technical tests.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <span class="w-8 h-8 rounded-full bg-blue-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0">03</span>
                        <div>
                            <h4 class="font-bold text-base text-brand-dark mb-1">Fund Upfront Escrow (or Optional Milestones)</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">Deposit full contract funds into Scriptly Escrow before work starts. For larger multi-phase projects, optional stage-by-stage milestones (e.g. paying per page/phase) can be enabled.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <span class="w-8 h-8 rounded-full bg-blue-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0">04</span>
                        <div>
                            <h4 class="font-bold text-base text-brand-dark mb-1">Collaborate in Real-Time</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">Communicate via instant real-time chat, inspect draft files, and monitor delivery progress.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <span class="w-8 h-8 rounded-full bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0">05</span>
                        <div>
                            <h4 class="font-bold text-base text-brand-dark mb-1">10-Day Review, Correction & Release</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">Inspect submitted deliverables with a 10-day correction window. Request revisions or approve to release escrow funds to the provider.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 text-center">
                    <a href="signup?role=client" class="inline-block w-full bg-brand-dark text-white font-bold text-xs py-3.5 rounded-full hover:bg-slate-800 transition-all">
                        Request Service as Client →
                    </a>
                </div>
            </div>

            <!-- Path B: For Service Providers -->
            <div class="bg-white rounded-[3px] p-8 border border-gray-200/80 shadow-sm space-y-8">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-5">
                    <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-serif text-2xl font-bold text-brand-dark">For Service Providers</h3>
                        <span class="text-xs font-semibold text-gray-400">Earn Verified Badge & Get Hired</span>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="flex gap-4">
                        <span class="w-8 h-8 rounded-full bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0">01</span>
                        <div>
                            <h4 class="font-bold text-base text-brand-dark mb-1">Create Account & Select Skill Domain</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">Register your profile and choose the service category matching your expertise.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <span class="w-8 h-8 rounded-full bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0">02</span>
                        <div>
                            <h4 class="font-bold text-base text-brand-dark mb-1">Pass Timed Assessment Challenge</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">Complete domain technical assessment quiz. Score 80%+ to unlock your official Verified Pro badge.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <span class="w-8 h-8 rounded-full bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0">03</span>
                        <div>
                            <h4 class="font-bold text-base text-brand-dark mb-1">Submit Proposals on Open Listings</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">Apply to funded client listings with custom milestone proposals and delivery timelines.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <span class="w-8 h-8 rounded-full bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0">04</span>
                        <div>
                            <h4 class="font-bold text-base text-brand-dark mb-1">Accept Escrow Backed Contracts</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">Begin work with 100% confidence knowing client funds are pre-funded into escrow before line 1 of code.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <span class="w-8 h-8 rounded-full bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0">05</span>
                        <div>
                            <h4 class="font-bold text-base text-brand-dark mb-1">Receive Instant Bank Payouts</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">Get paid immediately upon client approval with zero payment processing delays.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 text-center">
                    <a href="signup?role=provider" class="inline-block w-full bg-[#ffda79] text-brand-dark font-extrabold text-xs py-3.5 rounded-full hover:bg-amber-300 transition-all">
                        Become a Verified Pro →
                    </a>
                </div>
            </div>

        </div>

        <!-- Escrow Security Banner -->
        <div class="bg-[#0A2342] text-white rounded-[3px] p-10 sm:p-14 text-center max-w-4xl mx-auto">
            <h3 class="font-serif text-3xl font-bold mb-3">Escrow Protection & Dispute Resolution</h3>
            <p class="text-gray-300 text-xs sm:text-sm leading-relaxed max-w-2xl mx-auto mb-6">
                In the rare event of a project disagreement, Scriptly's Audit Desk steps in. Admin auditors review WebSocket chat history, milestone requirements, and submitted files to render a binding, audited resolution.
            </p>
            <a href="faq" class="inline-block bg-white text-brand-dark font-bold text-xs px-6 py-3 rounded-full hover:bg-gray-100 transition-colors">
                Read FAQ & Audit Guidelines →
            </a>
        </div>

    </main>

<?php include 'includes/footer.php'; ?>
