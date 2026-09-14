<?php 
/**
 * Scriptly Escrow - Project Board
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
\ = getDBConnection();

// Filters
\ = \['q'] ?? '';
\ = \['category'] ?? '';

// Build Query
\ = "
    SELECT p.*, u.full_name as client_name, u.username as client_username,
           (SELECT COUNT(*) FROM proposals WHERE project_id = p.id) as proposal_count
    FROM projects p
    JOIN users u ON p.client_id = u.id
    WHERE p.status = 'open'
";
\ = [];

if (!empty(\)) {
    \ .= " AND (p.title LIKE ? OR p.description LIKE ?)";
    \[] = '%' . \ . '%';
    \[] = '%' . \ . '%';
}

if (!empty(\)) {
    \ .= " AND p.category = ?";
    \[] = \;
}

\ .= " ORDER BY p.created_at DESC";

\ = \->prepare(\);
\->execute(\);
\ = \->fetchAll(PDO::FETCH_ASSOC);

\ = 'Find Work';
\ = 'projects';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/components/head.php'; ?>
    <title>Find Work - Scriptly</title>
</head>
<body class="bg-[#EFF2F7] text-slate-800 font-sans antialiased min-h-screen flex overflow-hidden">

    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <?php include __DIR__ . '/components/bottom-nav.php'; ?>

    <!-- Main Layout Area -->
    <main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-hidden relative">
        <?php include __DIR__ . '/components/header.php'; ?>

        <!-- Scrollable Content Area -->
        <div class="flex-1 overflow-y-auto w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 pb-[80px] md:pb-6 lg:pb-12 scroll-smooth">
            <div class="max-w-6xl mx-auto space-y-5">
                
                <!-- Search & Filters -->
                <div class="bg-white p-4 sm:p-5 rounded-[3px] border border-slate-200/90 shadow-sm flex flex-col md:flex-row gap-3">
                    <form method="GET" action="projects.php" class="flex-1 flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <i class="ph-bold ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="q" value="<?= htmlspecialchars(\) ?>" placeholder="Search projects by title or keywords..." class="w-full bg-slate-50 border border-slate-200 rounded-[3px] pl-9 pr-3 py-2 text-sm focus:outline-none focus:border-[#1952E1] transition-colors">
                        </div>
                        <select name="category" class="bg-slate-50 border border-slate-200 rounded-[3px] px-3 py-2 text-sm text-slate-700 focus:outline-none focus:border-[#1952E1]">
                            <option value="">All Categories</option>
                            <option value="web-development" <?= \ === 'web-development' ? 'selected' : '' ?>>Web Development</option>
                            <option value="mobile-apps" <?= \ === 'mobile-apps' ? 'selected' : '' ?>>Mobile Apps</option>
                            <option value="ui-ux-design" <?= \ === 'ui-ux-design' ? 'selected' : '' ?>>UI/UX Design</option>
                            <option value="data-science" <?= \ === 'data-science' ? 'selected' : '' ?>>Data Science</option>
                        </select>
                        <button type="submit" class="bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-sm px-5 py-2 rounded-[3px] transition-colors shadow-sm whitespace-nowrap">
                            Search Jobs
                        </button>
                    </form>
                </div>

                <!-- Projects List -->
                <div class="bg-white rounded-[3px] border border-slate-200/90 shadow-sm overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="font-bold text-lg text-slate-900">Available Projects</h2>
                        <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded-[3px]"><?= count(\) ?> Jobs Found</span>
                    </div>

                    <?php if (empty(\)): ?>
                        <div class="p-12 text-center">
                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="ph-bold ph-clipboard-text text-2xl text-slate-300"></i>
                            </div>
                            <h3 class="font-bold text-sm text-slate-900 mb-1">No projects found</h3>
                            <p class="text-xs text-slate-500">Try adjusting your search filters or check back later for new opportunities.</p>
                        </div>
                    <?php else: ?>
                        <div class="divide-y divide-slate-100">
                            <?php foreach (\ as \): ?>
                                <div class="p-4 sm:p-5 hover:bg-slate-50 transition-colors group">
                                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                        <div class="flex-1 min-w-0">
                                            <a href="project-details.php?slug=<?= urlencode(\['slug']) ?>" class="block font-bold text-base text-slate-900 group-hover:text-[#1952E1] transition-colors mb-1 truncate">
                                                <?= htmlspecialchars(\['title']) ?>
                                            </a>
                                            <div class="flex items-center gap-2 text-[11px] text-slate-500 mb-2">
                                                <span class="bg-slate-100 px-2 py-0.5 rounded-[2px] font-semibold uppercase tracking-wider text-slate-600"><?= htmlspecialchars(\['category'] ?: 'Uncategorized') ?></span>
                                                <span>•</span>
                                                <span>Posted <?= date('M j, Y', strtotime(\['created_at'])) ?></span>
                                                <span>•</span>
                                                <span>By <?= htmlspecialchars(\['client_name']) ?></span>
                                            </div>
                                            <p class="text-sm text-slate-600 line-clamp-2 mb-3">
                                                <?= htmlspecialchars(\['description']) ?>
                                            </p>
                                            <div class="flex items-center gap-4 text-xs font-medium text-slate-500">
                                                <div class="flex items-center gap-1.5" title="Experience Level">
                                                    <i class="ph-bold ph-star"></i>
                                                    <span class="capitalize"><?= htmlspecialchars(\['experience_tier'] ?: 'Any') ?> Level</span>
                                                </div>
                                                <div class="flex items-center gap-1.5" title="Proposals Received">
                                                    <i class="ph-bold ph-users"></i>
                                                    <span><?= \['proposal_count'] ?> Proposals</span>
                                                </div>
                                                <?php if (!empty(\['deadline_date'])): ?>
                                                <div class="flex items-center gap-1.5 text-orange-600" title="Deadline">
                                                    <i class="ph-bold ph-clock"></i>
                                                    <span>Due <?= date('M j', strtotime(\['deadline_date'])) ?></span>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="shrink-0 flex flex-row sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-3 sm:w-32 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                                            <div class="text-left sm:text-right">
                                                <div class="text-xs text-slate-500 font-medium mb-0.5">Budget</div>
                                                <div class="font-black text-slate-900 text-lg">
                                                    <?= \['budget'] > 0 ? '?' . number_format(\['budget']) : 'Negotiable' ?>
                                                </div>
                                            </div>
                                            <a href="project-details.php?slug=<?= urlencode(\['slug']) ?>" class="bg-white hover:bg-slate-50 text-[#1952E1] border border-[#1952E1] font-bold text-xs px-4 py-2 rounded-[3px] transition-colors whitespace-nowrap">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
        
    </main>
</div>
</body>
</html>


