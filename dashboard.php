<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/database.php';

// Check if user is logged in
$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['user_email'] ?? null;

if (empty($user_email)) {
    header('Location: login');
    exit;
}

$user = null;
try {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $user_email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (\Exception $e) {
    // Fallback
}

$user_name = $user['full_name'] ?? $_SESSION['user_name'] ?? 'Member';
$user_role = $user['primary_role'] ?? $_SESSION['user_role'] ?? 'client';
$phone_number = $user['phone_number'] ?? $_SESSION['user_phone'] ?? 'N/A';
$address = $user['address'] ?? $_SESSION['user_address'] ?? 'N/A';
$onboarding_done = (bool)($user['onboarding_completed'] ?? $_SESSION['onboarding_completed'] ?? false);

include 'includes/header.php';
?>

<!-- User Dashboard Container -->
<main class="min-h-screen bg-brand-bg py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Welcome Hero Banner -->
        <div class="bg-brand-dark text-white rounded-[6px] p-6 sm:p-8 mb-8 shadow-md relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 bg-blue-600/30 border border-blue-400/30 text-blue-200 text-xs font-bold px-3 py-1 rounded-full mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Account Verified & Active</span>
                    </div>
                    <h1 class="font-serif text-2xl sm:text-3xl font-bold tracking-tight text-white">
                        Welcome back, <?php echo htmlspecialchars($user_name); ?>!
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-300 mt-1 max-w-xl">
                        Your Scriptly account is fully onboarded as a <strong><?php echo ucfirst($user_role); ?></strong>. You can now post project listings or apply for contracts.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <?php if ($user_role === 'provider'): ?>
                        <a href="become-a-pro" class="px-5 py-3 bg-[#ffda79] text-brand-dark font-extrabold text-xs rounded-full hover:bg-amber-300 transition-all shadow-sm">
                            Take Skill Assessment Quiz →
                        </a>
                    <?php else: ?>
                        <a href="projects" class="px-5 py-3 bg-blue-600 text-white font-extrabold text-xs rounded-full hover:bg-blue-700 transition-all shadow-sm">
                            Post New Project →
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Dashboard Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <!-- Card 1: Account Status -->
            <div class="bg-white p-6 rounded-[6px] border border-gray-200 shadow-sm space-y-3">
                <div class="flex items-center justify-between text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <span>Account Verification</span>
                    <span class="text-emerald-600">Verified ✓</span>
                </div>
                <div class="text-xl font-extrabold text-brand-dark">Email & Profile Complete</div>
                <p class="text-xs text-gray-500">All security steps completed. Your profile is ready for marketplace activity.</p>
            </div>

            <!-- Card 2: Contact Info -->
            <div class="bg-white p-6 rounded-[6px] border border-gray-200 shadow-sm space-y-3">
                <div class="flex items-center justify-between text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <span>Onboarding Contact</span>
                    <span class="text-blue-600">Saved</span>
                </div>
                <div class="text-sm font-bold text-brand-dark space-y-1">
                    <div>📱 Phone: <span class="font-normal text-gray-600"><?php echo htmlspecialchars($phone_number); ?></span></div>
                    <div>📍 Address: <span class="font-normal text-gray-600"><?php echo htmlspecialchars($address); ?></span></div>
                </div>
                <p class="text-xs text-gray-500">Your location and phone number are securely stored.</p>
            </div>

            <!-- Card 3: Primary Goal -->
            <div class="bg-white p-6 rounded-[6px] border border-gray-200 shadow-sm space-y-3">
                <div class="flex items-center justify-between text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <span>Primary Goal</span>
                    <span class="text-amber-600 font-bold"><?php echo ucfirst($user_role); ?></span>
                </div>
                <div class="text-xl font-extrabold text-brand-dark">
                    <?php echo $user_role === 'provider' ? 'Offer Services' : 'Request Services'; ?>
                </div>
                <p class="text-xs text-gray-500">
                    <?php echo $user_role === 'provider' ? 'Complete skill quizzes to earn verified status badges.' : 'Post projects with milestone escrow protection.'; ?>
                </p>
            </div>

        </div>

        <!-- Quick Action Directory Links -->
        <div class="bg-white p-6 rounded-[6px] border border-gray-200 shadow-sm">
            <h2 class="font-serif text-lg font-bold text-brand-dark mb-4">Quick Marketplace Actions</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <a href="projects" class="p-4 border border-gray-200 rounded-[4px] hover:border-blue-600 hover:bg-blue-50/50 transition-all group">
                    <div class="font-extrabold text-xs text-brand-dark group-hover:text-blue-600 flex items-center justify-between mb-1">
                        <span>Browse Projects</span>
                        <span>→</span>
                    </div>
                    <p class="text-[11px] text-gray-500">View active client projects & funded listings.</p>
                </a>

                <a href="services" class="p-4 border border-gray-200 rounded-[4px] hover:border-blue-600 hover:bg-blue-50/50 transition-all group">
                    <div class="font-extrabold text-xs text-brand-dark group-hover:text-blue-600 flex items-center justify-between mb-1">
                        <span>Services Catalog</span>
                        <span>→</span>
                    </div>
                    <p class="text-[11px] text-gray-500">Explore service categories & pre-set packages.</p>
                </a>

                <a href="professionals" class="p-4 border border-gray-200 rounded-[4px] hover:border-blue-600 hover:bg-blue-50/50 transition-all group">
                    <div class="font-extrabold text-xs text-brand-dark group-hover:text-blue-600 flex items-center justify-between mb-1">
                        <span>Verified Pros</span>
                        <span>→</span>
                    </div>
                    <p class="text-[11px] text-gray-500">Find talent with verified skill test badges.</p>
                </a>

                <a href="how-it-works" class="p-4 border border-gray-200 rounded-[4px] hover:border-blue-600 hover:bg-blue-50/50 transition-all group">
                    <div class="font-extrabold text-xs text-brand-dark group-hover:text-blue-600 flex items-center justify-between mb-1">
                        <span>Escrow Rules</span>
                        <span>→</span>
                    </div>
                    <p class="text-[11px] text-gray-500">Learn about 10-day review & escrow safety.</p>
                </a>

            </div>
        </div>

    </div>
</main>

<?php include 'includes/footer.php'; ?>
