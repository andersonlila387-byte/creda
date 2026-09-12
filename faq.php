<?php
$page_title = "Frequently Asked Questions (FAQ) - Scriptly Marketplace";
$page_description = "Find clear answers to common questions regarding student project milestones, escrow deposits, Turnitin plagiarism standards, and verified talent.";
$active_page = "faq";
$breadcrumb = [
    'category' => 'Knowledge Base & Support',
    'title' => 'Frequently Asked Questions',
    'subtitle' => 'Everything you need to know about provider verification, milestone escrows, real-time chat, and payments on Scriptly.',
    'bg_image' => 'assets/breadcrumbs/support_bg.jpg'
];
include 'includes/header.php';
?>

    <!-- Main FAQ Container (Hostinger Dark Navy 2-Column UI with Metric Cards) -->
    <main class="w-full bg-[#0A2342] text-white py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-16">
            
            <!-- Instant Search Input & Header -->
            <div class="text-center max-w-3xl mx-auto space-y-6">
                <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-white tracking-tight">
                    Frequently Asked Questions
                </h1>

                <!-- Instant Search Input Bar -->
                <div class="relative max-w-xl mx-auto">
                    <input type="text" id="faq-search" placeholder="Type a keyword e.g. escrow, Turnitin, verification, payout..." class="w-full pl-12 pr-4 py-3.5 rounded-[3px] bg-slate-900/90 text-white placeholder-slate-400 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-[#1952E1] border border-white/20 shadow-lg">
                    <svg class="w-5 h-5 text-blue-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- 2-Column Side-by-Side FAQ Grid (Hostinger Layout) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-start" id="faq-grid-container">
                
                <!-- COLUMN 1: ESCROW SAFETY & MILESTONES -->
                <div class="space-y-6 faq-column">
                    <!-- Column Header with Mini Accent Bars -->
                    <div class="space-y-3 border-b border-white/10 pb-4">
                        <div class="flex items-center gap-1.5 text-[#1952E1]">
                            <span class="w-2.5 h-4 bg-[#1952E1] rounded-[1px]"></span>
                            <span class="w-2.5 h-4 bg-blue-400 rounded-[1px]"></span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                            Escrow Safety & Milestone Protection
                        </h2>
                    </div>

                    <!-- Column 1 FAQ Items -->
                    <div class="space-y-2 divide-y divide-white/10">
                        
                        <!-- Item 1.1 -->
                        <div class="faq-accordion-item pt-4 first:pt-0">
                            <button type="button" class="faq-trigger w-full flex items-center justify-between text-left py-3 text-base sm:text-lg font-bold text-white hover:text-blue-300 transition-colors">
                                <span>How does the milestone escrow protect my funds?</span>
                                <span class="faq-icon text-xl font-mono text-blue-400 shrink-0 ml-4">−</span>
                            </button>
                            <div class="faq-body text-xs sm:text-sm text-slate-300 leading-relaxed pt-1 pb-4 space-y-2">
                                <p>When you award a contract or project, your payment is deposited directly into Scriptly's secure escrow. The funds are held safely and are never released to the freelancer until you inspect the submitted files and confirm they meet your rubrics.</p>
                                <a href="about" class="inline-flex items-center text-xs font-bold text-blue-400 hover:text-blue-300 pt-1">
                                    <span>Learn more about Escrow</span>
                                    <i class="ph-bold ph-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Item 1.2 -->
                        <div class="faq-accordion-item pt-4">
                            <button type="button" class="faq-trigger w-full flex items-center justify-between text-left py-3 text-base sm:text-lg font-bold text-white hover:text-blue-300 transition-colors">
                                <span>What is the 7-day inspection window?</span>
                                <span class="faq-icon text-xl font-mono text-blue-400 shrink-0 ml-4">+</span>
                            </button>
                            <div class="faq-body hidden text-xs sm:text-sm text-slate-300 leading-relaxed pt-1 pb-4 space-y-2">
                                <p>Upon deliverable upload, you have a full 7-day inspection window to test source code, check SPSS datasets, or review chapters. If you request revisions, the auto-approval timer pauses immediately until revised files are delivered.</p>
                                <a href="about" class="inline-flex items-center text-xs font-bold text-blue-400 hover:text-blue-300 pt-1">
                                    <span>Learn more about Inspections</span>
                                    <i class="ph-bold ph-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Item 1.3 -->
                        <div class="faq-accordion-item pt-4">
                            <button type="button" class="faq-trigger w-full flex items-center justify-between text-left py-3 text-base sm:text-lg font-bold text-white hover:text-blue-300 transition-colors">
                                <span>Can I request revisions on submitted work?</span>
                                <span class="faq-icon text-xl font-mono text-blue-400 shrink-0 ml-4">+</span>
                            </button>
                            <div class="faq-body hidden text-xs sm:text-sm text-slate-300 leading-relaxed pt-1 pb-4 space-y-2">
                                <p>Yes. If deliverables require adjustment, click "Request Revision" inside the contract workspace and list your correction rubric. The freelancer must upload revised assets before payment can proceed.</p>
                            </div>
                        </div>

                        <!-- Item 1.4 -->
                        <div class="faq-accordion-item pt-4">
                            <button type="button" class="faq-trigger w-full flex items-center justify-between text-left py-3 text-base sm:text-lg font-bold text-white hover:text-blue-300 transition-colors">
                                <span>How does dispute audit resolution work?</span>
                                <span class="faq-icon text-xl font-mono text-blue-400 shrink-0 ml-4">+</span>
                            </button>
                            <div class="faq-body hidden text-xs sm:text-sm text-slate-300 leading-relaxed pt-1 pb-4 space-y-2">
                                <p>If consensus is not reached after revisions, you can open a dispute ticket. Scriptly’s Arbitration Desk reviews the contract requirements, uploaded files, and chat transcripts to render a fair judgment or full refund within 48 to 72 hours.</p>
                                <a href="contact" class="inline-flex items-center text-xs font-bold text-blue-400 hover:text-blue-300 pt-1">
                                    <span>Learn more about Dispute Mediation</span>
                                    <i class="ph-bold ph-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- COLUMN 2: QUALITY, TURNITIN & TALENT VERIFICATION -->
                <div class="space-y-6 faq-column">
                    <!-- Column Header with Mini Accent Bars -->
                    <div class="space-y-3 border-b border-white/10 pb-4">
                        <div class="flex items-center gap-1.5 text-[#1952E1]">
                            <span class="w-2.5 h-4 bg-[#1952E1] rounded-[1px]"></span>
                            <span class="w-2.5 h-4 bg-blue-400 rounded-[1px]"></span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                            Verified Quality & Academic Integrity
                        </h2>
                    </div>

                    <!-- Column 2 FAQ Items -->
                    <div class="space-y-2 divide-y divide-white/10">
                        
                        <!-- Item 2.1 -->
                        <div class="faq-accordion-item pt-4 first:pt-0">
                            <button type="button" class="faq-trigger w-full flex items-center justify-between text-left py-3 text-base sm:text-lg font-bold text-white hover:text-blue-300 transition-colors">
                                <span>How are freelancers assessed before joining?</span>
                                <span class="faq-icon text-xl font-mono text-blue-400 shrink-0 ml-4">−</span>
                            </button>
                            <div class="faq-body text-xs sm:text-sm text-slate-300 leading-relaxed pt-1 pb-4 space-y-2">
                                <p>Every service provider must pass rigorous technical and domain assessment exams (e.g. PHP MVC, MySQL, Python, Academic Writing rubrics) scored above 80% and complete government ID / NIN verification before they can submit proposals.</p>
                                <a href="talent" class="inline-flex items-center text-xs font-bold text-blue-400 hover:text-blue-300 pt-1">
                                    <span>Explore Pre-Assessed Talent</span>
                                    <i class="ph-bold ph-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Item 2.2 -->
                        <div class="faq-accordion-item pt-4">
                            <button type="button" class="faq-trigger w-full flex items-center justify-between text-left py-3 text-base sm:text-lg font-bold text-white hover:text-blue-300 transition-colors">
                                <span>Is original, plagiarism-free work guaranteed?</span>
                                <span class="faq-icon text-xl font-mono text-blue-400 shrink-0 ml-4">+</span>
                            </button>
                            <div class="faq-body hidden text-xs sm:text-sm text-slate-300 leading-relaxed pt-1 pb-4 space-y-2">
                                <p>Yes. All project reports, software source code, and dissertations must strictly comply with Turnitin standard submissions. Freelancers pledge 0% unauthorized AI generation and 100% original academic integrity on every deliverable.</p>
                                <a href="about" class="inline-flex items-center text-xs font-bold text-blue-400 hover:text-blue-300 pt-1">
                                    <span>Learn more about Turnitin Compliance</span>
                                    <i class="ph-bold ph-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Item 2.3 -->
                        <div class="faq-accordion-item pt-4">
                            <button type="button" class="faq-trigger w-full flex items-center justify-between text-left py-3 text-base sm:text-lg font-bold text-white hover:text-blue-300 transition-colors">
                                <span>How do freelancers withdraw their earnings?</span>
                                <span class="faq-icon text-xl font-mono text-blue-400 shrink-0 ml-4">+</span>
                            </button>
                            <div class="faq-body hidden text-xs sm:text-sm text-slate-300 leading-relaxed pt-1 pb-4 space-y-2">
                                <p>Once milestone funds are approved and released by the client, earnings land instantly in the freelancer's wallet balance. Freelancers can withdraw directly to their verified Nigerian bank accounts in seconds via Paystack NUBAN transfers.</p>
                            </div>
                        </div>

                        <!-- Item 2.4 -->
                        <div class="faq-accordion-item pt-4">
                            <button type="button" class="faq-trigger w-full flex items-center justify-between text-left py-3 text-base sm:text-lg font-bold text-white hover:text-blue-300 transition-colors">
                                <span>How does real-time chat and video calling work?</span>
                                <span class="faq-icon text-xl font-mono text-blue-400 shrink-0 ml-4">+</span>
                            </button>
                            <div class="faq-body hidden text-xs sm:text-sm text-slate-300 leading-relaxed pt-1 pb-4 space-y-2">
                                <p>Scriptly features an integrated WhatsApp-style direct chat interface with live voice notes, code snippet sharing, document attachments, and 1-click Google Meet video call scheduling directly inside your workspace.</p>
                                <a href="signup?role=client" class="inline-flex items-center text-xs font-bold text-blue-400 hover:text-blue-300 pt-1">
                                    <span>Learn more about Messaging</span>
                                    <i class="ph-bold ph-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- 4 Hostinger-Style Solid Stat Metric Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 pt-6">
                
                <!-- Stat Card 1 -->
                <div class="bg-[#1952E1] p-6 sm:p-7 rounded-[3px] border border-blue-400/20 shadow-lg text-white space-y-2 hover:-translate-y-1 transition-transform">
                    <span class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight block">100%</span>
                    <p class="text-xs sm:text-sm font-bold text-blue-100">Milestone Escrow Protection</p>
                </div>

                <!-- Stat Card 2 -->
                <div class="bg-[#1952E1] p-6 sm:p-7 rounded-[3px] border border-blue-400/20 shadow-lg text-white space-y-2 hover:-translate-y-1 transition-transform">
                    <span class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight block">0%</span>
                    <p class="text-xs sm:text-sm font-bold text-blue-100">Turnitin Plagiarism Tolerance</p>
                </div>

                <!-- Stat Card 3 -->
                <div class="bg-[#1952E1] p-6 sm:p-7 rounded-[3px] border border-blue-400/20 shadow-lg text-white space-y-2 hover:-translate-y-1 transition-transform">
                    <span class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight block">1,420+</span>
                    <p class="text-xs sm:text-sm font-bold text-blue-100">Pre-Assessed Freelancers</p>
                </div>

                <!-- Stat Card 4 -->
                <div class="bg-[#1952E1] p-6 sm:p-7 rounded-[3px] border border-blue-400/20 shadow-lg text-white space-y-2 hover:-translate-y-1 transition-transform">
                    <span class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight block">98.6%</span>
                    <p class="text-xs sm:text-sm font-bold text-blue-100">On-Time Project Delivery</p>
                </div>

            </div>

        </div>
    </main>

    <script>
        // FAQ Accordion Toggle Logic (Hostinger Style +/-)
        document.querySelectorAll('.faq-trigger').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const item = trigger.closest('.faq-accordion-item');
                const body = item.querySelector('.faq-body');
                const icon = trigger.querySelector('.faq-icon');
                
                if (body.classList.contains('hidden')) {
                    body.classList.remove('hidden');
                    icon.textContent = '−';
                } else {
                    body.classList.add('hidden');
                    icon.textContent = '+';
                }
            });
        });

        // Instant Filter Search
        const searchInput = document.getElementById('faq-search');
        if(searchInput) {
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                document.querySelectorAll('.faq-accordion-item').forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if(text.includes(query)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    </script>

<?php include 'includes/footer.php'; ?>
