<?php
$page_title = "Become a Verified Professional - Scriptly Marketplace";
$page_description = "Pass skill verification challenges, earn your official Verified Pro badge, and access high-budget escrow-protected projects on Scriptly.";
$active_page = "become-a-pro";
$breadcrumb = [
    'category' => 'Talent Onboarding & Verification',
    'title' => 'Become a Verified Professional',
    'subtitle' => 'Join an elite community of pre-assessed service providers getting hired by top clients.',
    'bg_image' => 'assets/breadcrumbs/legal_bg.jpg'
];
include 'includes/header.php';
?>

    <!-- Become a Pro Main Container -->
    <main class="max-w-7xl mx-auto px-3 sm:px-6 py-16">
        
        <!-- Hero Benefit Banner -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center mb-24">
            <div class="lg:col-span-6 space-y-6">
                <span class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1 rounded-[3px]">Pro Talent Onboarding</span>
                <h2 class="font-serif text-3xl sm:text-5xl font-bold text-brand-dark tracking-tight leading-tight">
                    Turn your verified skills into premium contracts.
                </h2>
                <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                    Tired of competing against thousands of unverified accounts on low-quality bidding platforms? Scriptly levels the playing field by requiring mandatory technical skill assessments.
                </p>
                <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                    Once you pass, your profile gains an official **Verified Pro** badge—giving clients immediate confidence to hire you at top hourly and project rates.
                </p>
                <div class="pt-2">
                    <a href="signup?role=provider" class="inline-block bg-[#ffda79] text-brand-dark font-extrabold text-sm px-8 py-4 rounded-full hover:bg-amber-300 transition-all hover:scale-105 shadow-md">
                        Start Verification Test →
                    </a>
                </div>
            </div>

            <div class="lg:col-span-6">
                <div class="rounded-[3px] overflow-hidden border border-gray-200/80 shadow-xl relative group">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=1000" alt="Verified Professional Working" class="w-full h-[420px] object-cover group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0A2342]/85 via-transparent to-transparent"></div>
                    <div class="absolute bottom-6 left-6 right-6 text-white">
                        <span class="bg-blue-600 text-white font-extrabold text-xs px-3 py-1 rounded-full uppercase">✓ Pre-Assessed Talent</span>
                        <h3 class="font-serif text-2xl font-bold mt-2">100% Upfront Escrow Security</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 Key Advantages Grid -->
        <div class="mb-24">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-extrabold uppercase tracking-widest text-blue-600">Why Join Scriptly</span>
                <h2 class="font-serif text-3xl sm:text-5xl font-bold text-brand-dark mt-2 tracking-tight">The Verified Pro Advantage</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <div class="bg-white rounded-[3px] p-7 border border-gray-200/80 hover:border-blue-500/50 transition-all duration-300">
                    <div class="w-12 h-12 rounded-[3px] bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xl mb-5">🎯</div>
                    <h3 class="font-bold text-lg text-brand-dark mb-2">No Lowball Bidding Wars</h3>
                    <p class="text-xs text-gray-600 leading-relaxed">Compete only against pre-assessed talent who meet technical quality thresholds.</p>
                </div>

                <div class="bg-white rounded-[3px] p-7 border border-gray-200/80 hover:border-emerald-500/50 transition-all duration-300">
                    <div class="w-12 h-12 rounded-[3px] bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl mb-5">🔒</div>
                    <h3 class="font-bold text-lg text-brand-dark mb-2">Guaranteed Escrow Payouts</h3>
                    <p class="text-xs text-gray-600 leading-relaxed">Contract funds are 100% pre-funded before work begins. Payments release automatically upon approval.</p>
                </div>

                <div class="bg-white rounded-[3px] p-7 border border-gray-200/80 hover:border-purple-500/50 transition-all duration-300">
                    <div class="w-12 h-12 rounded-[3px] bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-xl mb-5">⚡</div>
                    <h3 class="font-bold text-lg text-brand-dark mb-2">Instant Real-Time Chat</h3>
                    <p class="text-xs text-gray-600 leading-relaxed">Collaborate with clients in real-time with live typing indicators and instant file sharing.</p>
                </div>

                <div class="bg-white rounded-[3px] p-7 border border-gray-200/80 hover:border-amber-500/50 transition-all duration-300">
                    <div class="w-12 h-12 rounded-[3px] bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xl mb-5">🏆</div>
                    <h3 class="font-bold text-lg text-brand-dark mb-2">Public Verified Badge</h3>
                    <p class="text-xs text-gray-600 leading-relaxed">Display your domain assessment test score and verified credentials on your public profile.</p>
                </div>

            </div>
        </div>

        <!-- 3-Step Verification Roadmap -->
        <div class="bg-[#0A2342] text-white rounded-[3px] p-10 sm:p-16 mb-24">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="bg-[#ffda79] text-brand-dark font-extrabold text-xs px-3 py-1 rounded-full uppercase">3-Step Path</span>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold mt-4 tracking-tight">How to Earn Your Verified Badge</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
                
                <div class="bg-white/10 backdrop-blur-md rounded-[3px] p-6 border border-white/15">
                    <span class="text-xs font-extrabold text-[#ffda79] uppercase block mb-2">Step 01</span>
                    <h3 class="font-bold text-lg mb-2 text-white">Create Account</h3>
                    <p class="text-xs text-gray-300 leading-relaxed">Register your free Scriptly user account and select your service domain category.</p>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-[3px] p-6 border border-white/15">
                    <span class="text-xs font-extrabold text-[#ffda79] uppercase block mb-2">Step 02</span>
                    <h3 class="font-bold text-lg mb-2 text-white">Take Assessment Quiz</h3>
                    <p class="text-xs text-gray-300 leading-relaxed">Complete the 20-minute timed category assessment test to prove your technical competence.</p>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-[3px] p-6 border border-white/15">
                    <span class="text-xs font-extrabold text-emerald-400 uppercase block mb-2">Step 03</span>
                    <h3 class="font-bold text-lg mb-2 text-white">Earn Verified Pro Badge</h3>
                    <p class="text-xs text-gray-300 leading-relaxed">Score 80%+ to unlock your badge and start submitting proposals on funded client listings.</p>
                </div>

            </div>
        </div>

        <!-- Bottom CTA -->
        <div class="text-center max-w-3xl mx-auto">
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-brand-dark mb-4">Ready to show your verified expertise?</h2>
            <p class="text-gray-600 text-base mb-8">Join thousands of verified professionals earning on Scriptly today.</p>
            <a href="signup?role=provider" class="inline-block bg-brand-dark text-white font-bold text-sm px-10 py-4 rounded-full hover:bg-slate-800 transition-all shadow-md">
                Become a Professional →
            </a>
        </div>

    </main>

<?php include 'includes/footer.php'; ?>
