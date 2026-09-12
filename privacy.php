<?php
$page_title = "Privacy Policy - Scriptly Marketplace";
$page_description = "Learn how Scriptly protects your personal information, skill assessment test confidentiality, message encryption, and data security.";
$active_page = "privacy";
$breadcrumb = [
    'category' => 'Data Protection & Privacy',
    'title' => 'Privacy Policy',
    'subtitle' => 'Effective Date: August 20, 2026 • Your data security, assessment privacy, and confidentiality are our highest priorities.',
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
                    <h3 class="text-sm font-bold text-brand-dark uppercase tracking-wider mb-4">Privacy Navigation</h3>
                    <nav class="space-y-2 text-xs font-medium text-gray-600">
                        <a href="#info-collect" class="block hover:text-blue-600 transition-colors py-1">1. Information We Collect</a>
                        <a href="#how-used" class="block hover:text-blue-600 transition-colors py-1">2. How We Use Information</a>
                        <a href="#assessment-privacy" class="block hover:text-blue-600 transition-colors py-1">3. Assessment Data Privacy</a>
                        <a href="#realtime-security" class="block hover:text-blue-600 transition-colors py-1">4. Real-Time Chat & Security</a>
                        <a href="#cookies" class="block hover:text-blue-600 transition-colors py-1">5. Cookies & Tracking</a>
                        <a href="#data-rights" class="block hover:text-blue-600 transition-colors py-1">6. Your Rights & Data Export</a>
                    </nav>
                </div>
            </aside>

            <!-- Document Body -->
            <article class="lg:col-span-8 bg-white rounded-[3px] p-8 sm:p-12 border border-gray-200/80 space-y-10 text-sm leading-relaxed text-gray-700">
                
                <section id="info-collect">
                    <h2 class="font-serif text-2xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-100">1. Information We Collect</h2>
                    <p class="mb-3">
                        We collect personal and professional information necessary to facilitate project posting, provider verification, milestone escrow processing, and communication.
                    </p>
                    <ul class="list-disc pl-5 space-y-2 text-xs">
                        <li><strong>Account Details:</strong> Full name, email address, password hash (Argon2id/Bcrypt), role, country.</li>
                        <li><strong>Provider Profile & Verification:</strong> Identification credentials, portfolio samples, domain skills, assessment test submissions.</li>
                        <li><strong>Financial Data:</strong> Milestone payment deposits, payout bank details processed securely via PCI-compliant gateways.</li>
                    </ul>
                </section>

                <section id="how-used">
                    <h2 class="font-serif text-2xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-100">2. How We Use Information</h2>
                    <p class="mb-3">
                        Your information is strictly utilized to deliver marketplace functionality, enforce milestone escrow contracts, compute verified skill badges, and prevent fraud.
                    </p>
                    <p>
                        We do NOT sell, rent, or trade user data or assessment results to third-party advertisers.
                    </p>
                </section>

                <section id="assessment-privacy">
                    <h2 class="font-serif text-2xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-100">3. Provider Assessment Data Privacy</h2>
                    <p class="mb-3">
                        Service Provider assessment test attempts and raw question answers are kept confidential. Only calculated overall verification scores and earned Verified Pro badges are publicly displayed on provider profiles.
                    </p>
                </section>

                <section id="realtime-security">
                    <h2 class="font-serif text-2xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-100">4. Real-Time Chat & Communication Security</h2>
                    <p class="mb-3">
                        Messages transmitted across Scriptly's real-time messaging services are encrypted in transit. Chat logs are maintained securely for milestone audit inspection in case of contract disputes.
                    </p>
                </section>

                <section id="cookies">
                    <h2 class="font-serif text-2xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-100">5. Cookies & Session Storage</h2>
                    <p class="mb-3">
                        We use HTTP-only, secure session cookies and CSRF security tokens to preserve authentication states and prevent cross-site request forgery attacks.
                    </p>
                </section>

                <section id="data-rights">
                    <h2 class="font-serif text-2xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-100">6. Your Rights & Data Rights Requests</h2>
                    <p class="mb-3">
                        You have the right to request access to your personal data, request corrections, or request account closure and data deletion by contacting <a href="mailto:privacy@scriptly.com" class="text-blue-600 underline">privacy@scriptly.com</a>.
                    </p>
                </section>

            </article>

        </div>
    </main>

<?php include 'includes/footer.php'; ?>
