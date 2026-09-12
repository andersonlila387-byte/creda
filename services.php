<?php
$page_title = "Service Categories - Scriptly Verified Marketplace";
$page_description = "Explore Scriptly's directory of verified professional services.";
$active_page = "services";
$breadcrumb = [
    'category' => 'Marketplace Catalog',
    'title' => 'Explore Verified Services',
    'subtitle' => 'Connect with pre-assessed talent across specialized domain categories with 100% milestone escrow protection.',
    'bg_image' => 'assets/breadcrumbs/legal_bg.jpg'
];

require_once __DIR__ . '/config/database.php';
$db = getDBConnection();

$category_filter = $_GET['category'] ?? '';
$search_query = $_GET['q'] ?? '';

// Fetch all active packages with provider info and starting price
$sql = "
    SELECT 
        p.id as package_id, p.title, p.search_tags, p.created_at,
        u.id as provider_id, u.full_name as provider_name, u.username as provider_username,
        tp.rating as provider_rating, tp.completed_projects, tp.avatar_url,
        c.name as category_name, c.slug as category_slug,
        (SELECT price FROM package_pricing_tiers WHERE package_id = p.id AND tier_type = 'basic' LIMIT 1) as starting_price,
        (SELECT file_path FROM package_gallery WHERE package_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
    FROM packages p
    JOIN users u ON p.provider_id = u.id
    LEFT JOIN talent_profiles tp ON u.id = tp.user_id
    LEFT JOIN service_categories c ON p.category_id = c.id
    WHERE p.status = 'active'
";

$params = [];

if ($category_filter) {
    $sql .= " AND c.slug = ?";
    $params[] = $category_filter;
}

if ($search_query) {
    $sql .= " AND (p.title LIKE ? OR p.search_tags LIKE ? OR u.full_name LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories for sidebar filter
$cats = $db->query("SELECT name, slug FROM service_categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

    <main class="max-w-7xl mx-auto px-3 sm:px-6 py-12">
        <!-- Live Filter & Search Toolbar -->
        <div class="bg-white rounded-[3px] p-6 border border-gray-200/80 shadow-sm mb-12 flex flex-col md:flex-row items-center justify-between gap-4">
            <form action="services.php" method="GET" class="relative w-full md:w-96">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="q" placeholder="Search service packages..." value="<?php echo htmlspecialchars($search_query); ?>" class="w-full pl-10 pr-4 py-2.5 rounded-[3px] border border-gray-300 text-xs focus:outline-none focus:border-blue-600 font-medium">
                <?php if($category_filter): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
                <?php endif; ?>
            </form>

            <div class="flex items-center gap-3 w-full md:w-auto overflow-x-auto pb-1 md:pb-0 no-scrollbar">
                <a href="services.php" class="px-4 py-2 rounded-full text-xs font-bold shrink-0 transition-colors <?php echo empty($category_filter) ? 'bg-[#1952E1] text-white shadow-sm' : 'text-gray-600 hover:text-brand-dark bg-gray-100 hover:bg-gray-200'; ?>">All Categories</a>
                <?php foreach($cats as $c): ?>
                    <a href="services.php?category=<?php echo urlencode($c['slug']); ?>" class="px-4 py-2 rounded-full text-xs font-bold shrink-0 transition-colors <?php echo $category_filter === $c['slug'] ? 'bg-[#1952E1] text-white shadow-sm' : 'text-gray-600 hover:text-brand-dark bg-gray-100 hover:bg-gray-200'; ?>">
                        <?php echo htmlspecialchars($c['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Package Grid -->
        <div class="mb-16">
            <?php if (empty($packages)): ?>
                <div class="bg-white border border-slate-200 rounded-[3px] p-12 text-center shadow-sm">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-slate-800 font-bold mb-1">No services found</h3>
                    <p class="text-sm text-slate-500">We couldn't find any service packages matching your filters.</p>
                    <?php if($category_filter || $search_query): ?>
                        <a href="services.php" class="inline-block mt-4 text-sm text-[#1952E1] font-bold hover:underline">Clear all filters</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
                    <?php foreach($packages as $pkg): ?>
                        <div class="bg-white border border-gray-200 rounded-[3px] overflow-hidden shadow-sm hover:border-[#1952E1] transition-all duration-300 group flex flex-col">
                            
                            <!-- Thumbnail -->
                            <a href="app/service-details.php?id=<?php echo $pkg['package_id']; ?>" class="block relative aspect-video bg-slate-100 overflow-hidden">
                                <?php if($pkg['primary_image']): ?>
                                    <img src="app/<?php echo htmlspecialchars($pkg['primary_image']); ?>" alt="Package Thumbnail" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <?php else: ?>
                                    <!-- Fallback thumbnail -->
                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                <?php endif; ?>
                                <div class="absolute top-3 left-3 bg-[#0A2342] text-white text-[11px] font-bold px-2.5 py-1 rounded-[3px] shadow-sm">
                                    <?php echo htmlspecialchars($pkg['category_name']); ?>
                                </div>
                            </a>

                            <!-- Content -->
                            <div class="p-5 flex flex-col flex-1">
                                <!-- Provider Info -->
                                <div class="flex items-center gap-2 mb-3">
                                    <img src="<?php echo $pkg['avatar_url'] ? 'app/' . $pkg['avatar_url'] : 'assets/images/default-avatar.png'; ?>" class="w-7 h-7 rounded-full object-cover bg-slate-100 border border-gray-200" alt="Avatar">
                                    <a href="app/provider-profile.php?id=<?php echo $pkg['provider_id']; ?>" class="text-xs font-bold text-slate-800 hover:text-[#1952E1] truncate">
                                        <?php echo htmlspecialchars($pkg['provider_name']); ?>
                                    </a>
                                    <!-- True Verification logic check -->
                                    <svg class="w-3.5 h-3.5 text-blue-500 -ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </div>

                                <!-- Title -->
                                <a href="app/service-details.php?id=<?php echo $pkg['package_id']; ?>" class="block flex-1 mb-3">
                                    <h2 class="text-[15px] font-bold text-slate-900 leading-snug group-hover:text-[#1952E1] transition-colors line-clamp-2" title="<?php echo htmlspecialchars($pkg['title']); ?>">
                                        <?php echo htmlspecialchars($pkg['title']); ?>
                                    </h2>
                                </a>

                                <div class="flex items-center gap-1 text-xs font-bold <?php echo $pkg['provider_rating'] >= 4.5 ? 'text-amber-500' : 'text-slate-600'; ?>">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <span><?php echo $pkg['provider_rating'] ? number_format($pkg['provider_rating'], 1) : 'New'; ?></span>
                                    <span class="text-slate-400 font-normal ml-0.5">(<?php echo $pkg['completed_projects'] ?: 0; ?> reviews)</span>
                                </div>
                            </div>

                            <!-- Footer Pricing -->
                            <div class="px-5 py-3.5 border-t border-gray-100 flex items-center justify-between bg-slate-50/50 mt-auto">
                                <div>
                                    <span class="text-[10px] font-medium text-slate-500 block uppercase tracking-wider">Starting at</span>
                                    <span class="text-sm font-extrabold text-brand-dark">₦<?php echo number_format($pkg['starting_price'] ?? 0); ?></span>
                                </div>
                                <a href="app/service-details.php?id=<?php echo $pkg['package_id']; ?>" class="text-xs font-bold text-white bg-[#1952E1] hover:bg-blue-700 px-3.5 py-1.5 rounded-[3px] transition-colors">
                                    View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </main>

<?php include 'includes/footer.php'; ?>