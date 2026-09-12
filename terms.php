<?php
$page_title = "Terms of Service - Scriptly Marketplace";
$page_description = "Read Scriptly's Terms of Service, milestone escrow policies, skill verification rules, dispute audit procedures, and user code of conduct.";
$active_page = "terms";
$breadcrumb = [
    'category' => 'Legal & Governance',
    'title' => 'Terms of Service',
    'subtitle' => 'Effective Date: August 20, 2026 • Please read these terms carefully before utilizing the Scriptly verified marketplace platform.',
    'bg_image' => 'assets/breadcrumbs/legal_bg.jpg'
];
include 'includes/header.php';
?>

    <!-- Main Content Grid -->
    <main class="max-w-6xl mx-auto px-3 sm:px-6 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- Sticky Sidebar Nav -->
            <aside class="lg:col-span-4 hidden lg:block">
                <div class="sticky top-28 bg-white rounded-[3px] p-6 border border-gray-200/80">
                    <h3 class="text-sm font-bold text-brand-dark uppercase tracking-wider mb-4">Table of Contents</h3>
                    <nav class="space-y-2 text-xs font-medium text-gray-600">
                        <a href="#acceptance" class="block hover:text-blue-600 transition-colors py-1">1. Acceptance of Terms</a>
                        <a href="#user-roles" class="block hover:text-blue-600 transition-colors py-1">2. User Roles & Accounts</a>
                        <a href="#verification" class="block hover:text-blue-600 transition-colors py-1">3. Provider Skill Verification</a>
                        <a href="#escrow" class="block hover:text-blue-600 transition-colors py-1">4. Milestone Escrow Protection</a>
                        <a href="#disputes" class="block hover:text-blue-600 transition-colors py-1">5. Dispute Audit Procedures</a>
                        <a href="#fees" class="block hover:text-blue-600 transition-colors py-1">6. Platform Fees & Payouts</a>
                        <a href="#conduct" class="block hover:text-blue-600 transition-colors py-1">7. Non-Circumvention & Conduct</a>
                        <a href="#termination" class="block hover:text-blue-600 transition-colors py-1">8. Account Termination</a>
                    </nav>
                </div>
            </aside>

            <!-- Terms Document Body -->
            <article class="lg:col-span-8 bg-white rounded-[3px] p-8 sm:p-12 border border-gray-200/80 space-y-10 text-sm leading-relaxed text-gray-700">
                
                <section id="acceptance">
                    <h2 class="font-serif text-2xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-100">1. Acceptance of Terms</h2>
                    <p class="mb-3">
                        By registering, accessing, or utilizing the Scriptly platform ("Scriptly", "we", "us", or "our"), you agree to be bound by these Terms of Service. Scriptly operates as a web-based verified service marketplace and project management platform connecting clients with pre-assessed service providers.
                    </p>
                    <p>
                        If you do not agree to all of the terms contained herein, you may not access or use any part of the Scriptly marketplace or its associated real-time communication services.
                    </p>
                </section>

                <section id="user-roles">
                    <h2 class="font-serif text-2xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-100">2. User Roles & Account Security</h2>
                    <p class="mb-3">
                        Scriptly supports three distinct account classifications: <strong>Clients</strong> (project posters), <strong>Service Providers</strong> (freelancers/contractors), and <strong>Administrators</strong>. You are solely responsible for maintaining the confidentiality of your account credentials and password.
                    </p>
                    <ul class="list-disc pl-5 space-y-2 text-xs">
                        <li>You must be at least 18 years of age to register an account on Scriptly.</li>
                        <li>Account sharing or selling verified profiles is strictly prohibited and results in permanent suspension.</li>
                        <li>All user identities are subject to automated and manual verification auditing.</li>
                    </ul>
                </section>

                <section id="verification">
                    <h2 class="font-serif text-2xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-100">3. Service Provider Skill Verification</h2>
                    <p class="mb-3">
                        Scriptly differentiates itself through mandatory provider skill assessments. Service Providers must complete and pass domain-specific technical assessment tests before qualifying to submit project proposals or display an official Verified Pro badge.
                    </p>
                    <p>
                        Attempting to bypass, automate, or cheat assessment tests will result in immediate disqualification and permanent platform ban.
                    </p>
                </section>

                <section id="escrow">
                    <h2 class="font-serif text-2xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-100">4. Milestone Escrow & Payment Protection</h2>
                    <p class="mb-3">
                        All financial transactions on Scriptly occur through our secure Milestone Escrow framework. Clients deposit project milestone funds upfront into escrow before work commences.
                    </p>
                    <ul class="list-disc pl-5 space-y-2 text-xs">
                        <li>Escrow funds are locked securely until the client approves the submitted milestone deliverable.</li>
                        <li>Upon client approval, escrow funds are automatically released to the Service Provider's wallet balance.</li>
                        <li>Clients have a 7-day review window after submission; if unreviewed, milestone auto-approval timers apply.</li>
                    </ul>
                </section>

                <section id="disputes">
                    <h2 class="font-serif text-2xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-100">5. Dispute Resolution & Admin Audit</h2>
                    <p class="mb-3">
                        In the event that milestone deliverables do not meet contract specifications, either party may file a formal dispute ticket. Scriptly's Dispute Resolution Desk will inspect project requirements, deliverable submissions, and chat logs.
                    </p>
                    <p>
                        All decisions rendered by the Scriptly Audit Desk regarding escrow disbursement or refund allocation are final and binding.
                    </p>
                </section>

                <section id="fees">
                    <h2 class="font-serif text-2xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-100">6. Platform Fees & Payouts</h2>
                    <p class="mb-3">
                        Scriptly charges a standard service fee on completed contracts to maintain platform security, verification testing infrastructure, and messaging infrastructure.
                    </p>
                    <p>
                        Service Providers may withdraw approved earnings directly to their bank accounts via supported payout gateways upon reaching payout thresholds.
                    </p>
                </section>

                <section id="conduct">
                    <h2 class="font-serif text-2xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-100">7. Non-Circumvention & Code of Conduct</h2>
                    <p class="mb-3">
                        Users agree not to solicit or accept payments outside the Scriptly platform for work initiated on Scriptly. Off-platform payment solicitation ("circumvention") undermines escrow protection and results in account termination.
                    </p>
                </section>

                <section id="termination">
                    <h2 class="font-serif text-2xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-100">8. Account Termination</h2>
                    <p>
                        Scriptly reserves the right to suspend or terminate accounts that violate these Terms, engage in fraud, or compromise community safety.
                    </p>
                </section>

            </article>

        </div>
    </main>

<?php include 'includes/footer.php'; ?>
