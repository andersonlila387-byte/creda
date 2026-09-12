<?php
$page_title = "Contact Us - Scriptly Support Desk";
$page_description = "Get in touch with Scriptly support desk, dispute resolution auditors, or operations team for inquiries and contract support.";
$active_page = "contact";
$breadcrumb = [
    'category' => 'Get In Touch',
    'title' => 'Contact Scriptly Support',
    'subtitle' => 'Have questions about milestone escrows, provider assessments, or dispute resolution? Our team is here to assist.',
    'bg_image' => 'assets/breadcrumbs/legal_bg.jpg'
];
include 'includes/header.php';
?>

    <!-- Contact Content Layout -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-10 sm:py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
            
            <!-- Left Side: Contact Cards -->
            <div class="lg:col-span-5 space-y-5 sm:space-y-6">
                
                <div class="bg-white rounded-[3px] p-5 sm:p-7 border border-gray-200/80 shadow-xs">
                    <div class="w-10 h-10 rounded-[3px] bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg text-brand-dark mb-1">Email Support</h3>
                    <p class="text-xs text-gray-500 mb-3">Our dedicated support desk responds within 2 hours.</p>
                    <a href="mailto:support@scriptly.com" class="text-sm font-bold text-blue-600 hover:underline break-all">support@scriptly.com</a>
                </div>

                <div class="bg-white rounded-[3px] p-5 sm:p-7 border border-gray-200/80 shadow-xs">
                    <div class="w-10 h-10 rounded-[3px] bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg text-brand-dark mb-1">Dispute & Audit Desk</h3>
                    <p class="text-xs text-gray-500 mb-3">For contract escrow disputes or milestone verification tickets.</p>
                    <a href="mailto:disputes@scriptly.com" class="text-sm font-bold text-emerald-600 hover:underline break-all">disputes@scriptly.com</a>
                </div>

                <div class="bg-white rounded-[3px] p-5 sm:p-7 border border-gray-200/80 shadow-xs">
                    <div class="w-10 h-10 rounded-[3px] bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg text-brand-dark mb-1">Headquarters Location</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Scriptly Platform Admin Operations Center<br>Victoria Island, Lagos, Nigeria.</p>
                </div>

            </div>

            <!-- Right Side: Contact Form -->
            <div class="lg:col-span-7 bg-white rounded-[3px] p-5 sm:p-8 md:p-12 border border-gray-200/80 shadow-xs">
                <h2 class="font-serif text-2xl font-bold text-brand-dark mb-2">Send Us a Message</h2>
                <p class="text-xs text-gray-500 mb-8">Fill out the form below and an auditor or support specialist will follow up shortly.</p>

                <!-- Simulated PHP Form Handler Notification Banner -->
                <?php if($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold p-4 rounded-[3px] mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Thank you! Your message has been received. Our support team will respond within 2 hours.</span>
                </div>
                <?php endif; ?>

                <form action="contact" method="POST" class="space-y-6">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Full Name</label>
                            <input type="text" name="full_name" required placeholder="e.g. Alex Johnson" class="w-full px-4 py-3 rounded-[3px] border border-gray-300 text-sm focus:outline-none focus:border-blue-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="email" required placeholder="alex@example.com" class="w-full px-4 py-3 rounded-[3px] border border-gray-300 text-sm focus:outline-none focus:border-blue-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Account Role</label>
                            <select name="user_role" class="w-full px-4 py-3 rounded-[3px] border border-gray-300 text-sm text-gray-700 focus:outline-none focus:border-blue-600 bg-white">
                                <option value="client">Client (Project Poster)</option>
                                <option value="provider">Service Provider (Freelancer)</option>
                                <option value="guest">Prospective User / Guest</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Inquiry Topic</label>
                            <select name="inquiry_topic" class="w-full px-4 py-3 rounded-[3px] border border-gray-300 text-sm text-gray-700 focus:outline-none focus:border-blue-600 bg-white">
                                <option value="general">General Platform Inquiry</option>
                                <option value="escrow">Milestone Escrow & Payments</option>
                                <option value="verification">Provider Assessment & Verification</option>
                                <option value="dispute">Contract Dispute Audit</option>
                                <option value="technical">Technical Support / Chat Bug</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Subject</label>
                        <input type="text" name="subject" required placeholder="Brief description of your request" class="w-full px-4 py-3 rounded-[3px] border border-gray-300 text-sm focus:outline-none focus:border-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Message</label>
                        <textarea name="message" rows="5" required placeholder="Provide details about your question or contract reference ID..." class="w-full px-4 py-3 rounded-[3px] border border-gray-300 text-sm focus:outline-none focus:border-blue-600"></textarea>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-brand-dark text-white rounded-full font-bold text-sm hover:bg-slate-800 transition-all cursor-pointer">
                        Send Message →
                    </button>

                </form>
            </div>

        </div>
    </main>

<?php include 'includes/footer.php'; ?>
