<?php 
$page_title = 'Edit Project Listing';
$active_tab = 'projects';
require_once __DIR__ . '/components/head.php'; 

// Fetch project ID or default to active open listing
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if (isset($_SERVER['REQUEST_URI']) && preg_match('#/([A-Za-z0-9_-]+)$#', $_SERVER['REQUEST_URI'], $matches)) {
    $slug = $matches[1];
}

$db = getDBConnection();

// 1. Handle Delete Listing Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && $slug) {
    $d_stmt = $db->prepare("DELETE FROM projects WHERE slug = ? AND client_id = ?");
    $d_stmt->execute([$slug, $_SESSION['user_id'] ?? 1]);
    header("Location: /app/my-projects.php?msg=deleted");
    exit;
}

// 2. Handle Update Form POST Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $slug) {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $budget = isset($_POST['budget']) ? (float)$_POST['budget'] : 0;

    $u_stmt = $db->prepare("UPDATE projects SET title = ?, description = ?, budget = ?, category = ? WHERE slug = ? AND client_id = ?");
    $u_stmt->execute([$title, $description, $budget, $category, $slug, $_SESSION['user_id'] ?? 1]);
    header("Location: /app/my-projects.php?msg=updated");
    exit;
}

// 3. Fetch current project info
$project = null;
if ($slug) {
    $p_stmt = $db->prepare("SELECT * FROM projects WHERE slug = ? AND client_id = ?");
    $p_stmt->execute([$slug, $_SESSION['user_id'] ?? 1]);
    $project = $p_stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Layout Area -->
<main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-hidden relative bg-[#EFF2F7]">
    
    <?php include __DIR__ . '/components/header.php'; ?>

    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 pb-36 md:pb-6 lg:pb-12 scroll-smooth">
        
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- PAGE TITLE & BREADCRUMB BAR -->
            <div class="bg-white p-4 sm:p-6 rounded-[3px] border border-slate-200/90 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Workspace / Manage Listings /</span>
                        <span class="font-mono text-xs font-bold text-[#1952E1] bg-blue-50 px-2 py-0.5 rounded-[3px]"><?php echo htmlspecialchars($slug); ?></span>
                        <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-[3px] border border-emerald-200">
                            Open for Bids (4 Proposals)
                        </span>
                    </div>
                    <h1 class="text-lg sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">Edit Project Listing</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Modify deliverable scope, technical requirements, or milestone budget schedule.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="review-proposals.php?id=<?php echo urlencode($listing_id); ?>" class="text-xs font-bold text-[#1952E1] hover:text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-3.5 py-2 rounded-[3px] transition-colors flex items-center gap-1.5 shadow-2xs">
                        <i class="ph-bold ph-users text-sm"></i>
                        <span>Review 4 Proposals</span>
                    </a>
                    <a href="my-projects.php" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3.5 py-2 rounded-[3px] transition-colors">
                        Cancel
                    </a>
                </div>
            </div>

            <!-- APPLICANT NOTICE WARNING BANNER -->
            <div class="bg-amber-50 border border-amber-200 rounded-[3px] p-4 flex items-start gap-3">
                <div class="w-8 h-8 rounded-[3px] bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 mt-0.5 font-bold">
                    <i class="ph-bold ph-warning text-lg"></i>
                </div>
                <div class="space-y-1 text-xs">
                    <h3 class="font-bold text-amber-900 text-sm">4 Proposals Received for this Listing</h3>
                    <p class="text-amber-800 leading-relaxed">
                        Candidates have already submitted bids based on your current specifications. If you make major adjustments to the milestone scope or budget floor, existing applicants will automatically receive an update notice.
                    </p>
                </div>
            </div>

            <!-- MAIN GRID: FORM (8 COLS) + ESCROW STICKY SUMMARY (4 COLS) -->
            <form method="POST" id="edit-project-form" class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">
                
                <!-- LEFT FORM COLUMN (8 COLS) -->
                <div class="lg:col-span-8 space-y-5 sm:space-y-6">

                    <!-- SECTION 1: PROJECT TITLE & CLASSIFICATION -->
                    <div class="bg-white p-4 sm:p-6 rounded-[3px] border border-slate-200/90 shadow-sm space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center text-xs font-black">1</span>
                                Project Classification & Scope
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Edit category classification and public title.</p>
                        </div>

                        <!-- Project Title -->
                        <div class="space-y-1.5">
                            <label for="project-title" class="block text-xs font-bold text-slate-700">
                                Project Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="project-title" required value="<?php echo htmlspecialchars($project['title'] ?? 'Full-Stack PHP & MySQL Web Portal with Escrow System'); ?>" class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1] transition-all">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
                            <!-- Top-Level Service Category -->
                            <div class="space-y-1.5">
                                <label for="service-category" class="block text-xs font-bold text-slate-700">
                                    Primary Category <span class="text-red-500">*</span>
                                </label>
                                <select name="category" id="service-category" class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]">
                                    <option value="Web Development" <?php echo (($project['category'] ?? '') === 'Web Development' || ($project['category'] ?? '') === 'web') ? 'selected' : ''; ?>>💻 Web Development & Software Engineering</option>
                                    <option value="Writing" <?php echo ($project['category'] ?? '') === 'Writing' ? 'selected' : ''; ?>>📝 Writing, Academic & Research Thesis</option>
                                    <option value="Mobile Development" <?php echo ($project['category'] ?? '') === 'Mobile Development' ? 'selected' : ''; ?>>📱 Mobile App Development (iOS / Android)</option>
                                    <option value="Design" <?php echo ($project['category'] ?? '') === 'Design' ? 'selected' : ''; ?>>🎨 UI/UX & Product Design</option>
                                    <option value="Data Analytics" <?php echo ($project['category'] ?? '') === 'Data Analytics' ? 'selected' : ''; ?>>📊 Data Analysis & Python Analytics</option>
                                </select>
                            </div>

                            <!-- Specific Sub-Service -->
                            <div class="space-y-1.5">
                                <label for="sub-service-select" class="block text-xs font-bold text-slate-700">
                                    Sub-Service Branch <span class="text-red-500">*</span>
                                </label>
                                <select id="sub-service-select" class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]">
                                    <option value="fullstack" selected>Full-Stack Custom PHP MVC Portal</option>
                                    <option value="api">REST API & Microservice Backend</option>
                                    <option value="laravel">Laravel Enterprise Application</option>
                                    <option value="database">Database Architecture & Query Optimization</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <!-- SECTION 2: GRANULAR DELIVERABLE REQUIREMENTS -->
                    <div class="bg-white p-4 sm:p-6 rounded-[3px] border border-slate-200/90 shadow-sm space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center text-xs font-black">2</span>
                                Granular Technical Deliverables
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Ensure transparent requirements for proposal evaluation.</p>
                        </div>

                        <!-- Technical Parameters -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">Development Scope Tier</label>
                                <select class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-3 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:border-[#1952E1]">
                                    <option value="fullstack" selected>Full-Stack Complete System (Frontend + Backend + DB)</option>
                                    <option value="backend">Backend APIs & Authentication Only</option>
                                    <option value="frontend">Frontend UI Implementation Only</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">Post-Launch Support & Warranty</label>
                                <select class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-3 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:border-[#1952E1]">
                                    <option value="14days" selected>Includes 14-Day Bug Warranty (Free)</option>
                                    <option value="30days">Includes 30-Day Extended Support</option>
                                    <option value="none">Hand-off only (No extended support)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Project Description -->
                        <div class="space-y-1.5 pt-2">
                            <label for="project-desc" class="block text-xs font-bold text-slate-700">
                                Detailed Requirements & Deliverable Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" id="project-desc" rows="5" required class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] p-3.5 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1] transition-all"><?php echo htmlspecialchars($project['description'] ?? 'Build a responsive student & client project management dashboard with secure session authentication, escrow milestone locking, Turnitin file upload handling, and clean MySQL database schema architecture.'); ?></textarea>
                        </div>

                        <!-- Required Skill Tags -->
                        <div class="space-y-1.5 pt-2">
                            <label class="block text-xs font-bold text-slate-700">Required Skills & Technologies</label>
                            <div class="flex flex-wrap gap-1.5" id="skills-cloud">
                                <button type="button" class="skill-tag px-2.5 py-1 text-xs font-bold rounded-[3px] bg-blue-50 text-[#1952E1] border border-blue-200">PHP MVC ✓</button>
                                <button type="button" class="skill-tag px-2.5 py-1 text-xs font-bold rounded-[3px] bg-blue-50 text-[#1952E1] border border-blue-200">MySQL ✓</button>
                                <button type="button" class="skill-tag px-2.5 py-1 text-xs font-bold rounded-[3px] bg-blue-50 text-[#1952E1] border border-blue-200">Tailwind CSS ✓</button>
                                <button type="button" class="skill-tag px-2.5 py-1 text-xs font-bold rounded-[3px] bg-blue-50 text-[#1952E1] border border-blue-200">REST API ✓</button>
                                <button type="button" class="skill-tag px-2.5 py-1 text-xs font-bold rounded-[3px] bg-slate-50 text-slate-600 border border-slate-200 hover:border-slate-300">+ Laravel</button>
                                <button type="button" class="skill-tag px-2.5 py-1 text-xs font-bold rounded-[3px] bg-slate-50 text-slate-600 border border-slate-200 hover:border-slate-300">+ React</button>
                            </div>
                        </div>

                    </div>

                    <!-- SECTION 3: MILESTONE SCHEDULE & BUDGET BUILDER -->
                    <div class="bg-white p-4 sm:p-6 rounded-[3px] border border-slate-200/90 shadow-sm space-y-4">
                        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                            <div>
                                <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center text-xs font-black">3</span>
                                    Milestone Budget & Escrow Schedule
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">Funds are held in escrow and released per approved milestone.</p>
                            </div>
                            <span class="text-xs font-bold text-[#1952E1]">Total: ₦180,000</span>
                        </div>

                        <!-- Milestone 1 -->
                        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-[3px] space-y-2">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-800">
                                <span>Milestone 1: Database Schema & Authentication Core</span>
                                <span class="text-[#1952E1]">₦60,000 (33.3%)</span>
                            </div>
                            <p class="text-[11px] text-slate-500">Includes ER diagram, MySQL migrations, user roles & secure registration.</p>
                        </div>

                        <!-- Milestone 2 -->
                        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-[3px] space-y-2">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-800">
                                <span>Milestone 2: Client Dashboard UI & Milestone Tracker</span>
                                <span class="text-[#1952E1]">₦60,000 (33.3%)</span>
                            </div>
                            <p class="text-[11px] text-slate-500">Responsive Tailwind views, file upload widgets & progress steppers.</p>
                        </div>

                        <!-- Milestone 3 -->
                        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-[3px] space-y-2">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-800">
                                <span>Milestone 3: Escrow Payment Gateway Integration & Hand-off</span>
                                <span class="text-[#1952E1]">₦60,000 (33.3%)</span>
                            </div>
                            <p class="text-[11px] text-slate-500">Live webhook testing, documentation report & deployment hand-off.</p>
                        </div>

                    </div>

                </div>

                <!-- RIGHT SIDEBAR: STICKY ACTIONS & ESCROW SAFETY (4 COLS) -->
                <div class="lg:col-span-4 space-y-5 sm:space-y-6 lg:sticky lg:top-6">
                    
                    <!-- Action Panel -->
                    <div class="bg-white p-5 sm:p-6 rounded-[3px] border border-slate-200/90 shadow-sm space-y-4">
                        <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Listing Actions</h3>
                        
                        <div class="space-y-2.5">
                            <button type="submit" class="w-full bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs py-3 rounded-[3px] transition-all shadow-sm">
                                Save Changes & Update Listing
                            </button>
                            
                            <a href="review-proposals.php?id=<?php echo urlencode($listing_id); ?>" class="w-full block text-center bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs py-2.5 rounded-[3px] transition-colors">
                                View Submitted Proposals (4)
                            </a>
                            
                            <button type="button" onclick="if(confirm('Pause this listing to stop receiving new bids?')) alert('Listing paused.');" class="w-full bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 font-bold text-xs py-2.5 rounded-[3px] transition-colors">
                                Pause Listing (Hide from Search)
                            </button>
                            
                            <button type="button" onclick="if(confirm('Permanently close and delete this project listing?')) window.location.href='edit-listing.php?slug=<?php echo urlencode($slug); ?>&action=delete';" class="w-full text-red-600 hover:text-red-700 hover:bg-red-50 font-bold text-xs py-2 rounded-[3px] transition-colors">
                                Delete Listing
                            </button>
                        </div>
                    </div>

                    <!-- Escrow Protection Badge -->
                    <div class="bg-[#0A2342] text-white p-5 rounded-[3px] space-y-3">
                        <div class="flex items-center gap-2 text-blue-300 text-xs font-bold">
                            <i class="ph-bold ph-shield-check text-lg"></i>
                            <span>100% Escrow Protection</span>
                        </div>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            No upfront deposit is charged for editing your open listing. Escrow funds are only locked once you accept an applicant's proposal.
                        </p>
                    </div>

                </div>

            </form>

        </div>

    </div>
</main>

<!-- Main flex container ends -->
</div> 

<?php include __DIR__ . '/components/footer.php'; ?>

