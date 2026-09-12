<?php 
/**
 * Scriptly Escrow - Package Details (Fiverr-style Gig Page)
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();

$package_id = $_GET['id'] ?? null;
if (!$package_id) {
    header("Location: services.php");
    exit;
}

// Fetch Package
$sql = "
    SELECT p.*, 
           u.id as provider_id, u.full_name, u.username, u.email,
           tp.rating, tp.completed_projects, tp.avatar_url, tp.bio, tp.location,
           c.name as category_name, c.slug as category_slug
    FROM packages p
    JOIN users u ON p.provider_id = u.id
    LEFT JOIN talent_profiles tp ON u.id = tp.user_id
    LEFT JOIN service_categories c ON p.category_id = c.id
    WHERE p.id = ? AND p.status = 'active'
";
$stmt = $db->prepare($sql);
$stmt->execute([$package_id]);
$package = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$package) {
    header("Location: services.php?error=notfound");
    exit;
}

// Fetch Pricing Tiers
$stmt = $db->prepare("SELECT * FROM package_pricing_tiers WHERE package_id = ? ORDER BY FIELD(tier_type, 'basic', 'standard', 'premium')");
$stmt->execute([$package_id]);
$tiers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Gallery
$stmt = $db->prepare("SELECT * FROM package_gallery WHERE package_id = ? ORDER BY is_primary DESC, created_at ASC");
$stmt->execute([$package_id]);
$gallery = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch FAQs
$stmt = $db->prepare("SELECT * FROM package_faqs WHERE package_id = ? ORDER BY id ASC");
$stmt->execute([$package_id]);
$faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$active_tab = 'services';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/components/head.php'; ?>
    <title><?php echo htmlspecialchars($package['title']); ?> - Scriptly</title>
    <style>
        .tier-tab.active {
            border-bottom: 2px solid #1952E1;
            color: #1952E1;
        }
        .tier-content { display: none; }
        .tier-content.active { display: block; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col md:flex-row">

    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <?php include __DIR__ . '/components/bottom-nav.php'; ?>

    <main class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include __DIR__ . '/components/header.php'; ?>

        <div class="p-4 md:p-8 pt-20 md:pt-8 max-w-6xl mx-auto w-full">
            
            <!-- Breadcrumbs -->
            <nav class="flex text-[11px] font-bold text-slate-400 mb-6">
                <a href="services.php" class="hover:text-[#1952E1]">Services</a>
                <span class="mx-2">/</span>
                <a href="services.php?category=<?php echo $package['category_slug']; ?>" class="hover:text-[#1952E1]"><?php echo htmlspecialchars($package['category_name']); ?></a>
            </nav>

            <div class="flex flex-col lg:flex-row gap-10">
                
                <!-- Left Column (Main Content) -->
                <div class="flex-1 min-w-0">
                    
                    <h1 class="text-2xl md:text-3xl font-black text-slate-900 leading-tight mb-4"><?php echo htmlspecialchars($package['title']); ?></h1>
                    
                    <!-- Short Provider Summary -->
                    <div class="flex items-center gap-3 mb-6">
                        <img src="<?php echo $package['avatar_url'] ?: '../assets/images/default-avatar.png'; ?>" class="w-8 h-8 rounded-full object-cover bg-slate-200">
                        <div class="text-sm font-bold text-slate-800">
                            <a href="provider-profile.php?id=<?php echo $package['provider_id']; ?>" class="hover:text-[#1952E1]"><?php echo htmlspecialchars($package['full_name']); ?></a>
                        </div>
                        <?php if($package['rating']): ?>
                        <div class="flex items-center gap-1 text-sm font-bold text-amber-500 border-l border-slate-300 pl-3">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <span><?php echo number_format($package['rating'], 1); ?></span>
                            <span class="text-slate-400 font-normal ml-1">(<?php echo $package['completed_projects']; ?> orders)</span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Gallery -->
                    <div class="bg-slate-200 rounded-[3px] aspect-video w-full mb-8 overflow-hidden relative">
                        <?php if(!empty($gallery)): ?>
                            <img src="<?php echo htmlspecialchars($gallery[0]['file_path']); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-400">
                                <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-sm font-bold">No images provided</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- About This Package -->
                    <h2 class="text-lg font-black text-slate-900 mb-4">About This Service</h2>
                    <div class="prose prose-sm prose-slate max-w-none mb-10">
                        <?php echo nl2br(htmlspecialchars($package['description'])); ?>
                    </div>

                    <!-- About Provider Profile Card -->
                    <h2 class="text-lg font-black text-slate-900 mb-4">About The Provider</h2>
                    <div class="bg-white border border-slate-200 rounded-[3px] p-6 mb-10 shadow-sm flex flex-col sm:flex-row gap-6 items-start">
                        <img src="<?php echo $package['avatar_url'] ?: '../assets/images/default-avatar.png'; ?>" class="w-24 h-24 rounded-full object-cover bg-slate-100">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900"><a href="provider-profile.php?id=<?php echo $package['provider_id']; ?>" class="hover:text-[#1952E1]"><?php echo htmlspecialchars($package['full_name']); ?></a></h3>
                            <p class="text-sm text-slate-500 mb-3">@<?php echo htmlspecialchars($package['username']); ?></p>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                                <div>
                                    <span class="block text-[11px] text-slate-400 font-bold uppercase">From</span>
                                    <span class="font-semibold text-slate-700"><?php echo htmlspecialchars($package['location'] ?: 'Unknown'); ?></span>
                                </div>
                                <div>
                                    <span class="block text-[11px] text-slate-400 font-bold uppercase">Member Since</span>
                                    <span class="font-semibold text-slate-700"><?php echo date('M Y', strtotime($package['created_at'])); ?></span>
                                </div>
                            </div>
                            <div class="text-sm text-slate-600 line-clamp-3"><?php echo htmlspecialchars($package['bio'] ?: 'No bio provided.'); ?></div>
                        </div>
                    </div>

                    <!-- FAQs -->
                    <?php if(!empty($faqs)): ?>
                    <h2 class="text-lg font-black text-slate-900 mb-4">FAQ</h2>
                    <div class="space-y-3 mb-10">
                        <?php foreach($faqs as $faq): ?>
                        <details class="bg-white border border-slate-200 rounded-[3px] group shadow-sm">
                            <summary class="font-bold text-sm text-slate-800 p-4 cursor-pointer marker:text-transparent flex justify-between items-center select-none">
                                <?php echo htmlspecialchars($faq['question']); ?>
                                <svg class="w-5 h-5 text-slate-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </summary>
                            <div class="px-4 pb-4 text-sm text-slate-600 border-t border-slate-100 pt-3">
                                <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                            </div>
                        </details>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                </div>

                <!-- Right Column (Pricing Widget) -->
                <div class="w-full lg:w-96 shrink-0 relative">
                    <div class="sticky top-24 bg-white border border-slate-200 rounded-[3px] shadow-lg overflow-hidden">
                        
                        <!-- Tabs -->
                        <div class="flex border-b border-slate-200 bg-slate-50 text-xs font-bold uppercase tracking-wider text-center">
                            <?php foreach($tiers as $index => $tier): ?>
                                <button class="flex-1 py-4 text-slate-500 hover:text-slate-800 tier-tab transition-colors <?php echo $index === 0 ? 'active' : ''; ?>" data-target="tier-<?php echo $tier['tier_type']; ?>">
                                    <?php echo ucfirst($tier['tier_type']); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <!-- Tab Contents -->
                        <div class="p-6">
                            <?php foreach($tiers as $index => $tier): ?>
                            <div id="tier-<?php echo $tier['tier_type']; ?>" class="tier-content <?php echo $index === 0 ? 'active' : ''; ?>">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-lg font-bold text-slate-900"><?php echo htmlspecialchars($tier['name']); ?></h3>
                                    <div class="text-2xl font-black text-slate-900">₦<?php echo number_format($tier['price']); ?></div>
                                </div>
                                
                                <p class="text-sm text-slate-600 mb-6 h-16 overflow-hidden"><?php echo htmlspecialchars($tier['description']); ?></p>
                                
                                <div class="flex items-center gap-4 text-xs font-bold text-slate-700 mb-6">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <?php echo $tier['delivery_days']; ?> Days Delivery
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        <?php echo $tier['revisions'] == -1 ? 'Unlimited' : $tier['revisions']; ?> Revisions
                                    </div>
                                </div>
                                
                                <!-- Placeholder redirect to checkout for MVP phase -->
                                <a href="checkout.php?package_id=<?php echo $package_id; ?>&tier=<?php echo $tier['tier_type']; ?>" class="block w-full py-3.5 bg-[#1952E1] hover:bg-blue-700 text-white text-center font-bold text-sm rounded-[3px] transition-colors shadow-md">
                                    Continue (₦<?php echo number_format($tier['price']); ?>)
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        
        <?php include __DIR__ . '/components/footer.php'; ?>
    </main>

    <script>
        // Tab switching logic for pricing widget
        document.querySelectorAll('.tier-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tier-tab').forEach(t => t.classList.remove('active', 'bg-white'));
                document.querySelectorAll('.tier-content').forEach(c => c.classList.remove('active'));
                
                tab.classList.add('active', 'bg-white');
                document.getElementById(tab.getAttribute('data-target')).classList.add('active');
            });
        });
    </script>
</body>
</html>
