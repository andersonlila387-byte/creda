<?php 
$page_title = 'My Projects';
$active_tab = 'projects';
require_once __DIR__ . '/components/head.php';
require_once __DIR__ . '/controllers/MyProjectsController.php'; 
?>

<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Layout Area -->
<main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-hidden relative bg-[#EFF2F7]">
    
    <?php include __DIR__ . '/components/header.php'; ?>

    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 mobile-bottom-space md:pb-6 lg:pb-12 scroll-smooth">
        
        <div class="max-w-7xl mx-auto space-y-5 sm:space-y-6">

            <!-- PAGE TITLE & TOP ACTION BAR -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-6 rounded-[3px] border border-slate-200/90 shadow-sm">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg sm:text-2xl font-bold text-slate-900 tracking-tight">Project Management Desk</h1>
                        <span class="bg-blue-50 text-[#1952E1] text-xs font-bold px-2 py-0.5 rounded-[3px]"><?= $stats['total'] ?> Total</span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Track milestone deliverables, review applicant proposals, and manage escrow contracts.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="post-project.php" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-[3px] transition-colors shadow-sm">
                        <i class="ph-bold ph-plus-circle text-base"></i>
                        <span>Post a Project</span>
                    </a>
                </div>
            </div>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
                <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-[3px] text-xs flex items-center gap-2">
                    <i class="ph-bold ph-trash text-base"></i>
                    <span class="font-bold">Project listing has been permanently deleted.</span>
                </div>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-[3px] text-xs flex items-center gap-2">
                    <i class="ph-bold ph-check-circle text-base"></i>
                    <span class="font-bold">Project listing specifications updated successfully.</span>
                </div>
            <?php endif; ?>

            <!-- 4-COLUMN WORKSPACE METRICS -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                
                <!-- Metric 1: Active Contracts -->
                  <div class="bg-white p-3.5 sm:p-5 rounded-[3px] border border-slate-200/90 shadow-sm flex flex-col justify-between">
                      <div class="flex items-center justify-between text-slate-500 mb-2">
                          <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-500">Active</span>
                          <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center">
                              <i class="ph-bold ph-briefcase text-xs sm:text-sm"></i>
                          </div>
                      </div>
                      <div>
                          <span class="text-xl sm:text-3xl font-black text-slate-900 block leading-tight"><?= $stats['active'] ?></span>
                          <span class="text-[10px] sm:text-[11px] text-[#1952E1] font-semibold mt-0.5 block truncate">Contracts in progress</span>
                    </div>
                </div>

                <!-- Metric 2: Deliverables in Review -->
                  <div class="bg-white p-3.5 sm:p-5 rounded-[3px] border border-amber-200 bg-amber-50/20 shadow-sm flex flex-col justify-between">
                      <div class="flex items-center justify-between text-slate-500 mb-2">
                          <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-amber-700">Review</span>
                          <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-[3px] bg-amber-100 text-amber-700 flex items-center justify-center">
                              <i class="ph-bold ph-warning-circle text-xs sm:text-sm"></i>
                          </div>
                      </div>
                      <div>
                          <span class="text-xl sm:text-3xl font-black text-slate-900 block leading-tight"><?= $stats['review'] ?></span>
                          <span class="text-[10px] sm:text-[11px] text-amber-600 font-semibold mt-0.5 block truncate">Action required</span>
                    </div>
                </div>

                <!-- Metric 3: Open Listings & Proposals -->
                  <div class="bg-white p-3.5 sm:p-5 rounded-[3px] border border-slate-200/90 shadow-sm flex flex-col justify-between">
                      <div class="flex items-center justify-between text-slate-500 mb-2">
                          <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-500">Listings</span>
                          <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-[3px] bg-slate-100 text-slate-700 flex items-center justify-center">
                              <i class="ph-bold ph-users text-xs sm:text-sm"></i>
                          </div>
                      </div>
                      <div>
                          <span class="text-xl sm:text-3xl font-black text-slate-900 block leading-tight"><?= $stats['open'] ?></span>
                          <span class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 block truncate"><?= $stats['bids_received'] ?> bids received</span>
                    </div>
                </div>

                <!-- Metric 4: Completed Projects -->
                  <div class="bg-white p-3.5 sm:p-5 rounded-[3px] border border-slate-200/90 shadow-sm flex flex-col justify-between">
                      <div class="flex items-center justify-between text-slate-500 mb-2">
                          <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-500">Done</span>
                          <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-[3px] bg-emerald-50 text-emerald-600 flex items-center justify-center">
                              <i class="ph-bold ph-check-circle text-xs sm:text-sm"></i>
                          </div>
                      </div>
                      <div>
                          <span class="text-xl sm:text-3xl font-black text-slate-900 block leading-tight"><?= $stats['completed'] ?></span>
                          <span class="text-[10px] sm:text-[11px] text-emerald-600 font-semibold mt-0.5 block truncate">Successfully closed</span>
                    </div>
                </div>

            </div>

            <!-- SEARCH, TABS & FILTER CONTROLS -->
            <div class="bg-white p-4 sm:p-5 rounded-[3px] border border-slate-200/90 shadow-sm space-y-3 sm:space-y-4">
                
                <!-- Search & Dropdown Filters (Responsive Stack & Compact Selects) -->
                <div class="flex flex-col md:flex-row gap-2.5 sm:gap-3 items-stretch md:items-center justify-between">
                    
                    <!-- Search Input -->
                    <div class="relative w-full md:flex-1 md:max-w-md">
                        <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-sm"></i>
                        <input type="text" id="project-search-input" placeholder="Search by title, milestone, or freelancer..." class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] pl-9 pr-3 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#1952E1] transition-all">
                    </div>

                    <!-- Category & Sorting Dropdowns (2-Column Grid on Mobile) -->
                    <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 w-full md:w-auto shrink-0">
                        <select class="w-full sm:w-auto bg-slate-50 border border-slate-200/90 rounded-[3px] px-2.5 sm:px-3 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:border-[#1952E1] truncate">
                            <option value="all">All Categories</option>
                            <option value="development">Web & Mobile Dev</option>
                            <option value="design">UI/UX Design</option>
                            <option value="data">Data & BI</option>
                            <option value="devops">DevOps & Cloud</option>
                        </select>

                        <select class="w-full sm:w-auto bg-slate-50 border border-slate-200/90 rounded-[3px] px-2.5 sm:px-3 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:border-[#1952E1] truncate">
                            <option value="newest">Sort: Recent</option>
                            <option value="due">Sort: Earliest Due</option>
                            <option value="progress">Sort: Progress</option>
                        </select>
                    </div>

                </div>

                <!-- Interactive Lifecycle Tabs (Smooth Horizontal Scroll) -->
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar border-t border-slate-100 pt-3">
                    <button class="project-tab active px-3 py-1.5 text-xs font-bold rounded-[3px] bg-[#1952E1] text-white shadow-sm whitespace-nowrap" data-tab="all">
                        All (<?= $stats['total'] ?>)
                    </button>
                    <button class="project-tab px-3 py-1.5 text-xs font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-colors whitespace-nowrap" data-tab="active">
                        Active (<?= $stats['active'] ?>)
                    </button>
                    <button class="project-tab px-3 py-1.5 text-xs font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-colors whitespace-nowrap" data-tab="review">
                        In Review (<?= $stats['review'] ?>)
                    </button>
                    <button class="project-tab px-3 py-1.5 text-xs font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-colors whitespace-nowrap" data-tab="open">
                        Open Listings (<?= $stats['open'] ?>)
                    </button>
                    <button class="project-tab px-3 py-1.5 text-xs font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-colors whitespace-nowrap" data-tab="draft">
                        Drafts (0)
                    </button>
                    <button class="project-tab px-3 py-1.5 text-xs font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-colors whitespace-nowrap" data-tab="completed">
                        Completed (<?= $stats['completed'] ?>)
                    </button>
                </div>

            </div>

            <!-- PROJECTS LISTINGS STACK (2 IN A ROW ON DESKTOP) -->
            <?php if(empty($my_projects)): ?>
                <div class="w-full flex flex-col items-center justify-center py-16 bg-white border-2 border-dashed border-slate-300 rounded-[3px]">
                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-3 shadow-sm border border-slate-200">
                        <i class="ph-bold ph-folder-open text-2xl text-slate-400"></i>
                    </div>
                    <h3 class="font-bold text-sm text-slate-900 mb-1.5">No projects found</h3>
                    <p class="text-[11px] text-slate-500 text-center max-w-[280px] mb-4">You do not have any personal projects, contracts, or drafts at the moment.</p>
                    <a href="post-project.php" class="bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-[3px] transition-colors shadow-sm inline-flex items-center gap-1.5">
                        <i class="ph-bold ph-plus"></i> Post a Project
                    </a>
                </div>
            <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 items-stretch" id="projects-stack">
                    <?php foreach($my_projects as $proj): 
                        // Determine Data Status for JS Tabs
                        $data_status = 'active';
                        if ($proj['status'] === 'open') $data_status = 'open';
                        if ($proj['status'] === 'completed') $data_status = 'completed';
                        if ($proj['has_review']) $data_status = 'review';
                    ?>
                    
                    <div class="project-card bg-white rounded-[3px] border border-slate-200/90 p-5 sm:p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between space-y-5 relative overflow-hidden" data-status="<?= $data_status ?>">
                        
                        <?php if($data_status === 'review'): ?>
                            <div class="absolute top-0 left-0 right-0 h-1 bg-amber-400"></div>
                        <?php elseif($data_status === 'active'): ?>
                            <div class="absolute top-0 left-0 right-0 h-1 bg-[#1952E1]"></div>
                        <?php elseif($data_status === 'completed'): ?>
                            <div class="absolute top-0 left-0 right-0 h-1 bg-emerald-500"></div>
                        <?php endif; ?>

                        <div class="space-y-4">
                            <!-- 1. Header Meta Bar -->
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <div class="flex items-center gap-2">
                                    <?php if($data_status === 'review'): ?>
                                        <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-800 border border-amber-300 text-[11px] font-bold px-2.5 py-0.5 rounded-[3px]">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                            In Review
                                        </span>
                                    <?php elseif($data_status === 'active'): ?>
                                        <span class="inline-flex items-center gap-1.5 bg-blue-50 text-[#1952E1] border border-blue-200 text-[11px] font-bold px-2.5 py-0.5 rounded-[3px]">
                                            <span class="w-2 h-2 rounded-full bg-[#1952E1] animate-pulse"></span>
                                            Active Dev
                                        </span>
                                    <?php elseif($data_status === 'open'): ?>
                                        <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 border border-slate-300 text-[11px] font-bold px-2.5 py-0.5 rounded-[3px]">
                                            Open Listing
                                        </span>
                                    <?php elseif($data_status === 'completed'): ?>
                                        <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-800 border border-emerald-300 text-[11px] font-bold px-2.5 py-0.5 rounded-[3px]">
                                            Completed
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-[3px]">
                                    <i class="ph-bold ph-shield-check text-emerald-600 text-xs"></i>
                                    <span class="text-[11px] font-bold text-slate-800">$<?= number_format($proj['calculated_budget'], 2) ?> Total</span>
                                </div>
                            </div>

                            <!-- 2. Project Title & Scope -->
                            <div>
                                <h2 class="text-base sm:text-lg font-bold text-slate-900 hover:text-[#1952E1] transition-colors leading-snug cursor-pointer">
                                    <?= htmlspecialchars($proj['title']) ?>
                                </h2>
                                <p class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                                    <span>Category: <strong class="text-slate-700"><?= htmlspecialchars($proj['category'] ?? 'General') ?></strong></span>
                                </p>
                            </div>

                            <!-- 3. Progress / Provider / Bids Info -->
                            <?php if($data_status === 'open'): ?>
                                <div class="p-3 bg-slate-50/80 border border-slate-200/80 rounded-[3px] flex items-center justify-between gap-3">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-slate-900"><?= $proj['proposal_count'] ?> Candidate Proposals</span>
                                    </div>
                                    <a href="review-proposals.php?slug=<?= urlencode($proj['slug']) ?>" class="text-xs font-bold bg-[#0A2342] hover:bg-black text-white px-3 py-1.5 rounded-[3px] transition-colors shrink-0">
                                        Review Bids
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="p-3 bg-slate-50/80 border border-slate-200/80 rounded-[3px] flex flex-col gap-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-semibold text-slate-600">Contract Progress</span>
                                        <span class="text-[11px] font-bold text-[#1952E1]"><?= $proj['progress_percentage'] ?>%</span>
                                    </div>
                                    <div class="h-1.5 rounded-[2px] bg-slate-200 w-full overflow-hidden">
                                        <div class="h-full bg-[#1952E1] rounded-[2px]" style="width: <?= $proj['progress_percentage'] ?>%"></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>

                        <!-- 5. Card Bottom Actions -->
                        <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3 text-[11px] text-slate-500 font-medium">
                                <span>Posted: <?= date('M j, Y', strtotime($proj['created_at'])) ?></span>
                            </div>
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <?php if ($proj['status'] === 'open'): ?>
                                    <a href="edit-listing.php?slug=<?= urlencode($proj['slug']) ?>" class="flex-1 sm:flex-none text-center text-xs font-bold bg-white text-[#1952E1] border border-[#1952E1] hover:bg-blue-50 px-4 py-2 rounded-[3px] transition-colors shadow-2xs">
                                        Edit / Delete
                                    </a>
                                <?php else: ?>
                                    <a href="<?= !empty($proj['contract_id']) ? 'contract-details.php?id=' . $proj['contract_id'] : 'project-details.php?slug=' . urlencode($proj['slug']) ?>" class="flex-1 sm:flex-none text-center text-xs font-bold bg-white text-[#1952E1] border border-[#1952E1] hover:bg-blue-50 px-4 py-2 rounded-[3px] transition-colors shadow-2xs">
                                        Manage Contract
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                    <?php endforeach; ?>
            </div>
                <?php endif; ?>

        </div>

    </div>
</main>

<script>
// Project Lifecycle Tab Filtering Logic
function switchProjectTab(tabKey) {
    document.querySelectorAll('.project-tab').forEach(tab => {
        if (tab.dataset.tab === tabKey) {
            tab.classList.add('active', 'bg-[#1952E1]', 'text-white', 'shadow-sm');
            tab.classList.remove('bg-slate-100', 'text-slate-600');
        } else {
            tab.classList.remove('active', 'bg-[#1952E1]', 'text-white', 'shadow-sm');
            tab.classList.add('bg-slate-100', 'text-slate-600');
        }
    });

    document.querySelectorAll('.project-card').forEach(card => {
        const status = card.dataset.status;
        if (tabKey === 'all') {
            card.style.display = 'block';
        } else if (tabKey === 'active') {
            card.style.display = status === 'active' ? 'block' : 'none';
        } else if (tabKey === 'review') {
            card.style.display = status === 'review' ? 'block' : 'none';
        } else if (tabKey === 'open') {
            card.style.display = status === 'open' ? 'block' : 'none';
        } else if (tabKey === 'draft') {
            card.style.display = status === 'draft' ? 'block' : 'none';
        } else if (tabKey === 'completed') {
            card.style.display = status === 'completed' ? 'block' : 'none';
        }
    });
}

document.querySelectorAll('.project-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        switchProjectTab(tab.dataset.tab);
    });
});

// Auto-activate tab from URL parameter (?tab=open or #open-listings-section)
const urlParams = new URLSearchParams(window.location.search);
const initialTab = urlParams.get('tab');
if (initialTab) {
    switchProjectTab(initialTab);
}
</script>

<!-- Main flex container ends -->
</div> 

<?php include __DIR__ . '/components/footer.php'; ?>



