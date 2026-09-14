<?php 
/**
 * Scriptly Escrow - Post a New Project Listing
 * Step-by-Step Wizard Multi-Step Form with Database Taxonomy
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();

// Fetch Dynamic Taxonomy & Pricing from Database
$tax_stmt = $db->query("SELECT * FROM services_taxonomy ORDER BY id ASC");
$categories_list = $tax_stmt->fetchAll(PDO::FETCH_ASSOC);

$sub_stmt = $db->query("SELECT * FROM sub_services ORDER BY id ASC");
$sub_services_list = $sub_stmt->fetchAll(PDO::FETCH_ASSOC);

// Map taxonomies into JavaScript-friendly format
$taxonomy_map = [];
foreach ($categories_list as $cat) {
    $key = $cat['category_key'];
    $taxonomy_map[$key] = [
        'title' => 'Recommended Minimum Baseline: ₦' . number_format($cat['baseline_price']),
        'desc' => $cat['baseline_desc'],
        'floor' => (float)$cat['baseline_price'],
        'options' => []
    ];
}
foreach ($sub_services_list as $sub) {
    $key = $sub['category_key'];
    if (isset($taxonomy_map[$key])) {
        $taxonomy_map[$key]['options'][] = [
            'value' => $sub['subservice_key'],
            'label' => $sub['subservice_label']
        ];
    }
}

// Slugify helper function
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

// Handle Form POST Action
$success_msg = null;
$error_msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $category = isset($_POST['category']) ? trim($_POST['category']) : '';
        $experience_tier = isset($_POST['experience_tier']) ? trim($_POST['experience_tier']) : 'intermediate';
        $deadline_date = isset($_POST['deadline_date']) ? trim($_POST['deadline_date']) : null;
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        
        // Compute total budget from milestones
        $total_budget = 0;
        if (isset($_POST['milestone_amount']) && is_array($_POST['milestone_amount'])) {
            foreach ($_POST['milestone_amount'] as $amt) {
                $total_budget += (float)$amt;
            }
        }
        if ($total_budget == 0) {
            $total_budget = 50000; // default minimum fallback
        }

        // Handle file upload
        $reference_file_path = null;
        if (isset($_FILES['reference_file']) && $_FILES['reference_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['reference_file']['tmp_name'];
            $fileName = $_FILES['reference_file']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/../assets/uploads/projects/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            $dest_path = $uploadFileDir . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $reference_file_path = '/assets/uploads/projects/' . $newFileName;
            }
        }

        // Generate unique slug
        $slug = slugify($title) . '-' . time();
        $client_id = $_SESSION['user_id'] ?? 1; // Fallback to user 1 (Joseph) for testing

        // Insert project listing
        $proj_sql = "INSERT INTO projects (slug, client_id, category, title, description, status, budget, experience_tier, deadline_date, reference_file) VALUES (?, ?, ?, ?, ?, 'open', ?, ?, ?, ?)";
        $proj_stmt = $db->prepare($proj_sql);
        $proj_stmt->execute([
            $slug,
            $client_id,
            $category,
            $title,
            $description,
            $total_budget,
            $experience_tier,
            $deadline_date,
            $reference_file_path
        ]);
        
        $project_id = $db->lastInsertId();

        // Insert Milestones
        if (isset($_POST['milestone_title']) && is_array($_POST['milestone_title'])) {
            $ins_m = $db->prepare("INSERT INTO milestones (project_id, title, amount, completion_percentage, status, due_date) VALUES (?, ?, ?, 0, 'pending', ?)");
            foreach ($_POST['milestone_title'] as $index => $m_title) {
                $m_title = trim($m_title) ?: ('Milestone ' . ($index + 1));
                $m_amt = (float)$_POST['milestone_amount'][$index];
                $m_due = !empty($_POST['milestone_due'][$index]) ? $_POST['milestone_due'][$index] : date('Y-m-d', strtotime('+30 days'));
                
                $ins_m->execute([
                    $project_id,
                    $m_title,
                    $m_amt,
                    $m_due
                ]);
            }
        }

        header("Location: /app/my-projects.php?msg=updated");
        exit;

    } catch (Exception $e) {
        $error_msg = "Error creating project listing: " . $e->getMessage();
    }
}

$page_title = 'Post a Project';
$active_tab = 'post-project';
require_once __DIR__ . '/components/head.php'; 
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
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Workspace / Create Listing</span>
                    </div>
                    <h1 class="text-lg sm:text-2xl font-bold text-slate-900 tracking-tight mt-0.5">Create a New Project</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Design your deliverables step-by-step and configure upfront escrow protections.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="my-projects.php" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3.5 py-2 rounded-[3px] transition-colors">
                        Cancel & Return
                    </a>
                </div>
            </div>

            <?php if ($error_msg): ?>
                <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-[3px] text-xs font-bold">
                    ⚠️ <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

            <!-- MAIN GRID: FORM (8 COLS) + ESCROW STICKY SUMMARY (4 COLS) -->
            <form id="post-project-form" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">
                
                <!-- LEFT FORM COLUMN (8 COLS) - Multi-step layout -->
                <div class="lg:col-span-8 space-y-5">

                    <!-- STEPPER PROGRESS BAR -->
                    <div class="bg-white p-4 rounded-[3px] border border-slate-200/90 shadow-sm">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-400 select-none md:px-4">
                            
                            <!-- Step 1 Indicator -->
                            <div class="step-indicator active flex items-center gap-2 text-[#1952E1] font-extrabold" id="step-ind-1">
                                <span class="w-6 h-6 rounded-full border-2 border-[#1952E1] flex items-center justify-center font-bold text-[11px] bg-blue-50">1</span>
                                <span class="hidden sm:inline">Overview</span>
                            </div>
                            <div class="flex-1 h-0.5 mx-2 bg-slate-200" id="step-line-1"></div>

                            <!-- Step 2 Indicator -->
                            <div class="step-indicator flex items-center gap-2" id="step-ind-2">
                                <span class="w-6 h-6 rounded-full border-2 border-slate-300 flex items-center justify-center font-bold text-[11px]">2</span>
                                <span class="hidden sm:inline">Scope</span>
                            </div>
                            <div class="flex-1 h-0.5 mx-2 bg-slate-200" id="step-line-2"></div>

                            <!-- Step 3 Indicator -->
                            <div class="step-indicator flex items-center gap-2" id="step-ind-3">
                                <span class="w-6 h-6 rounded-full border-2 border-slate-300 flex items-center justify-center font-bold text-[11px]">3</span>
                                <span class="hidden sm:inline">Milestones</span>
                            </div>
                            <div class="flex-1 h-0.5 mx-2 bg-slate-200" id="step-line-3"></div>

                            <!-- Step 4 Indicator -->
                            <div class="step-indicator flex items-center gap-2" id="step-ind-4">
                                <span class="w-6 h-6 rounded-full border-2 border-slate-300 flex items-center justify-center font-bold text-[11px]">4</span>
                                <span class="hidden sm:inline">Reference Files</span>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 1 CARD: PROJECT CLASSIFICATION & OVERVIEW -->
                    <div id="step-card-1" class="step-card bg-white p-4 sm:p-6 rounded-[3px] border border-slate-200/90 shadow-sm space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center text-xs font-black">1</span>
                                Project Classification & Overview
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Select service specifications to load compliance price baseline filters.</p>
                        </div>

                        <!-- Project Title -->
                        <div class="space-y-1.5">
                            <label for="project-title" class="block text-xs font-bold text-slate-700">
                                Project Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="project-title" required placeholder="e.g. Full-Stack E-Commerce Portal or Professional CV Design & Optimization" class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1] transition-all">
                            <p class="text-[11px] text-slate-400">Describe the main technology, document parameters, or deliverables expected.</p>
                        </div>

                        <!-- Dynamic Service Category Selector (DB Driven) -->
                        <div class="space-y-1.5 pt-2">
                            <label for="service-category" class="block text-xs font-bold text-slate-700">
                                Service Classification Category <span class="text-red-500">*</span>
                            </label>
                            <select name="category" id="service-category" class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]">
                                <?php foreach ($categories_list as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat['category_key']); ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Dynamic Sub-Service Selector -->
                        <div class="space-y-1.5 pt-2">
                            <label for="sub-service-select" class="block text-xs font-bold text-slate-700">
                                Specific Scope Branch <span class="text-red-500">*</span>
                            </label>
                            <select name="subservice" id="sub-service-select" class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]">
                                <!-- Dynamically loaded via JS -->
                            </select>
                        </div>

                        <!-- Form Navigation Actions -->
                        <div class="flex justify-end pt-3 border-t border-slate-100">
                            <button type="button" onclick="goToStep(2)" class="px-5 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs rounded-[3px] transition-colors shadow-sm">
                                Save & Continue
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2 CARD: TECHNICAL SCOPE & TIMELINES -->
                    <div id="step-card-2" class="step-card hidden bg-white p-4 sm:p-6 rounded-[3px] border border-slate-200/90 shadow-sm space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center text-xs font-black">2</span>
                                Technical Scope & Timelines
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Specify deadline dates and define the experience level of the talent needed.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Experience Tier Preferred -->
                            <div class="space-y-1.5">
                                <label for="experience_tier" class="block text-xs font-bold text-slate-700">Required Talent Experience Tier <span class="text-red-500">*</span></label>
                                <select name="experience_tier" id="experience_tier" class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
                                    <option value="entry">🐣 Entry Level (Junior / Budget-Friendly)</option>
                                    <option value="intermediate" selected>💼 Intermediate (Experienced Developer / Writer)</option>
                                    <option value="expert">🏆 Expert / Verified Agency (Pro Developer)</option>
                                </select>
                            </div>

                            <!-- Target Completion Date -->
                            <div class="space-y-1.5">
                                <label for="deadline_date" class="block text-xs font-bold text-slate-700">Project Deadline Date <span class="text-red-500">*</span></label>
                                <input type="date" name="deadline_date" id="deadline_date" required value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-3.5 py-2 py-2.5 text-xs font-medium text-slate-850 focus:outline-none focus:border-[#1952E1]">
                            </div>
                        </div>

                        <!-- Technical Specification Containers (Toggle visibility based on category selection) -->
                        <div id="writing-scope-fields" class="space-y-4 hidden">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700">Document Scope / Page Count</label>
                                    <select class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-3 py-2 text-xs font-medium text-slate-700 focus:outline-none">
                                        <option value="1-5">1 – 5 Pages (Brief Summary)</option>
                                        <option value="6-15">6 – 15 Pages (Standard Project)</option>
                                        <option value="50+" selected>50+ Pages (Full Thesis)</option>
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700">Citation Style</label>
                                    <select class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-3 py-2 text-xs font-medium text-slate-700 focus:outline-none">
                                        <option value="apa7" selected>APA 7th Edition</option>
                                        <option value="harvard">Harvard Referencing</option>
                                        <option value="ieee">IEEE Format</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="web-scope-fields" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700">Development Scope Tier</label>
                                    <select class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-3 py-2 text-xs font-medium text-slate-700 focus:outline-none">
                                        <option value="fullstack" selected>Full-Stack System (Frontend + Backend + DB)</option>
                                        <option value="backend">Backend & Database APIs Only</option>
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700">Compliance Protocol</label>
                                    <select class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] px-3 py-2 text-xs font-medium text-slate-700 focus:outline-none">
                                        <option value="clean_code" selected>0% Similarity (Turnitin Verified)</option>
                                        <option value="standard">Standard functional delivery</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Form Navigation Actions -->
                        <div class="flex justify-between pt-3 border-t border-slate-100">
                            <button type="button" onclick="goToStep(1)" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-[3px] transition-colors">
                                Go Back
                            </button>
                            <button type="button" onclick="goToStep(3)" class="px-5 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs rounded-[3px] transition-colors shadow-sm">
                                Save & Continue
                            </button>
                        </div>
                    </div>

                    <!-- STEP 3 CARD: ESCROW MILESTONES & BUDGETS -->
                    <div id="step-card-3" class="step-card hidden bg-white p-4 sm:p-6 rounded-[3px] border border-slate-200/90 shadow-sm space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                            <div>
                                <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center text-xs font-black">3</span>
                                    Milestone Budget & Escrow Schedule
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">Partition the budget across deliverables. Add custom milestones below.</p>
                            </div>
                            <button type="button" id="add-milestone-btn" class="text-xs font-bold text-[#1952E1] hover:text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-3.5 py-1.5 rounded-[3px] transition-colors flex items-center gap-1.5 shrink-0">
                                <i class="ph-bold ph-plus text-sm"></i>
                                <span>Add Milestone</span>
                            </button>
                        </div>

                        <!-- Dynamic Milestone Item Rows -->
                        <div id="milestones-container" class="space-y-3">
                            <!-- Default Milestone Row -->
                            <div class="milestone-row bg-slate-50 border border-slate-200/90 p-3.5 sm:p-4 rounded-[3px] space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                        <span class="w-5 h-5 bg-white border border-slate-200 rounded-[3px] flex items-center justify-center text-[10px] font-bold text-[#1952E1]">1</span>
                                        Milestone 1
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                    <div class="sm:col-span-6">
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Deliverable Title / Objective</label>
                                        <input type="text" name="milestone_title[]" value="Core Backend Architecture & API Setup" required class="milestone-title w-full bg-white border border-slate-200 rounded-[3px] px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
                                    </div>
                                    <div class="grid grid-cols-2 sm:col-span-6 sm:grid sm:grid-cols-2 gap-2.5 sm:gap-3">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Target Date</label>
                                            <input type="date" name="milestone_due[]" value="<?php echo date('Y-m-d', strtotime('+15 days')); ?>" required class="milestone-due w-full bg-white border border-slate-200 rounded-[3px] px-2.5 sm:px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Amount (₦)</label>
                                            <input type="number" name="milestone_amount[]" value="0" min="0" step="5000" placeholder="e.g. 50000" required class="milestone-amount w-full bg-white border border-slate-200 rounded-[3px] px-2.5 sm:px-3 py-2 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#1952E1]">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Escrow deposit settings -->
                        <div class="space-y-2 pt-2 border-t border-slate-100">
                            <label class="block text-xs font-bold text-slate-700">Choose Escrow Funding Mode <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="flex items-start gap-3 p-3.5 rounded-[3px] border-2 border-[#1952E1] bg-blue-50/30 cursor-pointer transition-all">
                                    <input type="radio" name="escrow_mode" value="full" checked class="mt-0.5 text-[#1952E1] focus:ring-0">
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 block">100% Full Upfront Deposit</span>
                                        <span class="text-[11px] text-slate-500 mt-0.5 block">Recommended. Attracts top talent by locking the complete budget into escrow.</span>
                                    </div>
                                </label>
                                <label class="flex items-start gap-3 p-3.5 rounded-[3px] border border-slate-200 bg-slate-50 hover:bg-white cursor-pointer transition-all">
                                    <input type="radio" name="escrow_mode" value="milestone" class="mt-0.5 text-[#1952E1] focus:ring-0">
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 block">Milestone 1 Upfront Deposit</span>
                                        <span class="text-[11px] text-slate-500 mt-0.5 block">Fund Milestone 1 now. Secure and lock subsequent milestones step-by-step.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Form Navigation Actions -->
                        <div class="flex justify-between pt-3 border-t border-slate-100">
                            <button type="button" onclick="goToStep(2)" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-[3px] transition-colors">
                                Go Back
                            </button>
                            <button type="button" onclick="goToStep(4)" class="px-5 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs rounded-[3px] transition-colors shadow-sm">
                                Save & Continue
                            </button>
                        </div>
                    </div>

                    <!-- STEP 4 CARD: DESCRIPTION & REFERENCE FILES -->
                    <div id="step-card-4" class="step-card hidden bg-white p-4 sm:p-6 rounded-[3px] border border-slate-200/90 shadow-sm space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center text-xs font-black">4</span>
                                Description & Reference Guidelines File
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Describe requirements and attach reference documents (format instructions, prompt lists).</p>
                        </div>

                        <!-- Detailed Description -->
                        <div class="space-y-1.5">
                            <label for="project-desc" class="block text-xs font-bold text-slate-700">
                                Detailed Requirements Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" id="project-desc" rows="5" required placeholder="Describe user flows, expectations, programming languages, page count, styling rubrics..." class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] p-3.5 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#1952E1] transition-all"></textarea>
                        </div>

                        <!-- Drag and Drop Reference Guidelines File Upload -->
                        <div class="space-y-1.5 pt-2">
                            <label for="reference_file" class="block text-xs font-bold text-slate-700">Project reference upload (.pdf, .docx, .zip)</label>
                            <div class="border-2 border-dashed border-slate-300 hover:border-[#1952E1] transition-colors rounded-[3px] p-6 text-center cursor-pointer bg-slate-50/50" onclick="document.getElementById('reference_file').click()">
                                <i class="ph-bold ph-cloud-arrow-up text-3xl text-slate-400 mb-2 block mx-auto"></i>
                                <span class="text-xs font-bold text-slate-700 block">Click to select file or drag & drop</span>
                                <span class="text-[10px] text-slate-450 mt-1 block">PDF, DOCX, ZIP files up to 25MB</span>
                                <input type="file" name="reference_file" id="reference_file" class="hidden" onchange="updateFileLabel(this)">
                                <span id="file-upload-name" class="text-xs font-bold text-[#1952E1] bg-blue-50 border border-blue-200 px-2 py-0.5 rounded mt-2 inline-block hidden"></span>
                            </div>
                        </div>

                        <!-- Form Navigation Actions -->
                        <div class="flex justify-between pt-3 border-t border-slate-100">
                            <button type="button" onclick="goToStep(3)" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-[3px] transition-colors">
                                Go Back
                            </button>
                            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-[3px] transition-colors shadow-sm flex items-center gap-1.5">
                                <i class="ph-bold ph-paper-plane-tilt text-sm"></i>
                                <span>Publish Project & Open Bids</span>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- RIGHT STICKY ESCROW SUMMARY COLUMN (4 COLS) - Stays active & sticky -->
                <div class="lg:col-span-4 space-y-4 lg:sticky lg:top-4">

                    <!-- Summary Card -->
                    <div class="bg-white p-5 rounded-[3px] border border-slate-200/90 shadow-sm space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="font-bold text-sm text-slate-900">Escrow Contract Summary</h3>
                            <p class="text-[11px] text-slate-400">Real-time budget & escrow protection</p>
                        </div>

                        <!-- Total Contract Budget -->
                        <div class="bg-slate-50 p-4 rounded-[3px] border border-slate-200/80 space-y-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Total Escrow Budget</span>
                            <div class="text-2xl sm:text-3xl font-black text-[#1952E1]" id="total-budget-display">
                                ₦0
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-600 pt-1 border-t border-slate-200/60">
                                <span>Scheduled Milestones</span>
                                <span class="font-bold text-slate-900" id="total-milestones-count">1 Milestone</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-600">
                                <span>Scriptly Service Fee</span>
                                <span class="font-bold text-emerald-750">₦0 (0% Free)</span>
                            </div>
                        </div>

                        <!-- Price Floor Compliance Status Banner -->
                        <div id="price-status-badge" class="p-2.5 rounded-[3px] bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold flex items-center gap-2">
                            <i class="ph-bold ph-check-circle text-base"></i>
                            <span>Budget Meets Recommended Baseline</span>
                        </div>

                        <!-- Recommended baseline explainer banner -->
                        <div id="price-floor-banner" class="p-3 bg-slate-50 border border-slate-200 rounded-[3px] flex items-start gap-2.5">
                            <i class="ph-bold ph-shield-check text-[#1952E1] text-base shrink-0 mt-0.5"></i>
                            <div class="text-[11px] text-slate-600 leading-normal">
                                <strong id="baseline-title">Recommended Minimum Baseline: ₦180,000</strong>
                                <p id="baseline-desc" class="text-[10px] text-slate-500 mt-0.5">Full-Stack Complete Web Application standard fair compensation.</p>
                            </div>
                        </div>

                        <!-- 100% Escrow Guarantee Seal -->
                        <div class="p-3 bg-blue-50/50 rounded-[3px] border border-blue-200/80 flex items-start gap-2.5">
                            <i class="ph-bold ph-lock-key text-[#1952E1] text-base shrink-0 mt-0.5"></i>
                            <div class="text-[11px] text-slate-600">
                                <strong>Escrow Protected</strong>: Funds remain locked and are only released when you inspect and approve completed deliverables.
                            </div>
                        </div>

                    </div>

                    <!-- Need Help Card -->
                    <div class="bg-white p-4 rounded-[3px] border border-slate-200/90 shadow-sm text-center">
                        <p class="text-xs text-slate-500">Need help writing requirements or estimating milestones?</p>
                        <a href="messages.php" class="text-xs font-bold text-[#1952E1] hover:underline mt-1 inline-block">Consult a Scriptly Specialist →</a>
                    </div>

                </div>

            </form>

        </div>

    </div>
</main>

<!-- Main flex container ends -->
</div> 

<!-- JAVASCRIPT: HIERARCHY TAXONOMY, STEP SWITCHING & DYNAMIC MILESTONE CALCULATOR -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // PHP Taxonomy loaded dynamically from the Database!
    const serviceTaxonomy = <?php echo json_encode($taxonomy_map); ?>;

    const categorySelect = document.getElementById('service-category');
    const subServiceSelect = document.getElementById('sub-service-select');
    const baselineTitle = document.getElementById('baseline-title');
    const baselineDesc = document.getElementById('baseline-desc');
    
    const writingScope = document.getElementById('writing-scope-fields');
    const webScope = document.getElementById('web-scope-fields');

    const milestonesContainer = document.getElementById('milestones-container');
    const addMilestoneBtn = document.getElementById('add-milestone-btn');
    const totalDisplay = document.getElementById('total-budget-display');
    const milestonesCount = document.getElementById('total-milestones-count');
    const priceStatusBadge = document.getElementById('price-status-badge');

    // Stepper navigation logic
    window.goToStep = function(stepNum) {
        // Step Validation checks
        if (stepNum > 1) {
            const titleInput = document.getElementById('project-title');
            if (titleInput && !titleInput.value.trim()) {
                alert("Please fill in a descriptive Project Title to proceed.");
                titleInput.focus();
                return;
            }
        }
        if (stepNum > 2) {
            const deadlineInput = document.getElementById('deadline_date');
            if (deadlineInput && !deadlineInput.value) {
                alert("Please set a valid Project Deadline Date.");
                deadlineInput.focus();
                return;
            }
        }
        if (stepNum > 3) {
            const amts = document.querySelectorAll('.milestone-amount');
            let hasZero = false;
            amts.forEach(input => {
                if (parseFloat(input.value) <= 0 || !input.value) {
                    hasZero = true;
                }
            });
            if (hasZero) {
                alert("Please provide valid, non-zero amounts for all milestone phases.");
                return;
            }
        }

        // Hide all cards
        document.querySelectorAll('.step-card').forEach(card => card.classList.add('hidden'));
        
        // Show target step card
        document.getElementById('step-card-' + stepNum).classList.remove('hidden');

        // Update Stepper Indicators
        for (let i = 1; i <= 4; i++) {
            const ind = document.getElementById('step-ind-' + i);
            const line = document.getElementById('step-line-' + i);
            if (i < stepNum) {
                // Completed step
                ind.className = "step-indicator flex items-center gap-2 text-emerald-600 font-extrabold cursor-pointer";
                ind.querySelector('span').className = "w-6 h-6 rounded-full border-2 border-emerald-500 bg-emerald-50 flex items-center justify-center font-bold text-[11px]";
                ind.querySelector('span').textContent = "✓";
                if (line) line.className = "flex-1 h-0.5 mx-2 bg-emerald-500";
            } else if (i === stepNum) {
                // Active step
                ind.className = "step-indicator flex items-center gap-2 text-[#1952E1] font-extrabold";
                ind.querySelector('span').className = "w-6 h-6 rounded-full border-2 border-[#1952E1] bg-blue-50 flex items-center justify-center font-bold text-[11px]";
                ind.querySelector('span').textContent = i;
                if (line) line.className = "flex-1 h-0.5 mx-2 bg-slate-200";
            } else {
                // Future step
                ind.className = "step-indicator flex items-center gap-2 text-slate-400";
                ind.querySelector('span').className = "w-6 h-6 rounded-full border-2 border-slate-300 flex items-center justify-center font-bold text-[11px]";
                ind.querySelector('span').textContent = i;
                if (line) line.className = "flex-1 h-0.5 mx-2 bg-slate-200";
            }
        }
    };

    // Update uploader label on file selection
    window.updateFileLabel = function(input) {
        const fileLabel = document.getElementById('file-upload-name');
        if (input.files && input.files.length > 0) {
            fileLabel.textContent = "📄 " + input.files[0].name;
            fileLabel.classList.remove('hidden');
        } else {
            fileLabel.classList.add('hidden');
        }
    };

    // Make Completed Indicators Clickable to navigate backwards
    document.querySelectorAll('.step-indicator').forEach((ind, index) => {
        ind.addEventListener('click', () => {
            const clickedStep = index + 1;
            // Let them navigate to any step they've already validated
            const activeCard = document.querySelector('.step-card:not(.hidden)');
            const activeStepNum = parseInt(activeCard.id.split('-').pop());
            if (clickedStep < activeStepNum) {
                goToStep(clickedStep);
            }
        });
    });

    // Milestone Budget Recalculator
    function recalcBudget() {
        const rows = document.querySelectorAll('.milestone-row');
        let total = 0;
        rows.forEach((row) => {
            const amountInput = row.querySelector('.milestone-amount');
            if (amountInput) {
                const amt = parseFloat(amountInput.value) || 0;
                total += amt;
            }
        });

        if (totalDisplay) totalDisplay.textContent = '₦' + total.toLocaleString();
        if (milestonesCount) milestonesCount.textContent = rows.length + (rows.length === 1 ? ' Milestone' : ' Milestones');

        // Validate budget against recommended floor
        const currentCat = categorySelect ? categorySelect.value : 'web';
        const currentFloor = (serviceTaxonomy[currentCat] && serviceTaxonomy[currentCat].floor) || 50000;

        if (priceStatusBadge) {
            if (total < currentFloor) {
                priceStatusBadge.className = 'p-2.5 rounded-[3px] bg-amber-50 text-amber-800 border border-amber-300 text-xs font-bold flex items-center gap-2';
                priceStatusBadge.innerHTML = '<i class="ph-bold ph-warning text-base"></i><span>Budget Below Recommended Baseline (₦' + currentFloor.toLocaleString() + ')</span>';
            } else {
                priceStatusBadge.className = 'p-2.5 rounded-[3px] bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold flex items-center gap-2';
                priceStatusBadge.innerHTML = '<i class="ph-bold ph-check-circle text-base"></i><span>Budget Meets Recommended Baseline</span>';
            }
        }
    }

    // Load subservices and baselines on category change
    function updateCategoryScope() {
        if (!categorySelect || !subServiceSelect) return;
        const cat = categorySelect.value;
        const config = serviceTaxonomy[cat] || serviceTaxonomy.web;

        // Build dynamic options
        subServiceSelect.innerHTML = '';
        config.options.forEach(opt => {
            const option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.label;
            subServiceSelect.appendChild(option);
        });

        // Update baseline description cards
        if (baselineTitle) baselineTitle.textContent = config.title;
        if (baselineDesc) baselineDesc.textContent = config.desc;

        // Toggle custom scope cards
        if (writingScope) writingScope.classList.toggle('hidden', cat !== 'writing' && cat !== 'cv_resume');
        if (webScope) webScope.classList.toggle('hidden', cat !== 'web' && cat !== 'mobile' && cat !== 'portfolio');

        recalcBudget();
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', updateCategoryScope);
    }

    const attachMilestoneEvents = (row) => {
        const amountInput = row.querySelector('.milestone-amount');
        if (amountInput) {
            amountInput.addEventListener('input', recalcBudget);
        }

        const removeBtn = row.querySelector('.remove-milestone-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                row.remove();
                
                // Re-sequence number badges
                document.querySelectorAll('.milestone-row').forEach((r, idx) => {
                    r.querySelector('.w-5').textContent = idx + 1;
                });
                recalcBudget();
            });
        }
    };

    // Add Milestone Phase Action
    if (addMilestoneBtn && milestonesContainer) {
        addMilestoneBtn.addEventListener('click', () => {
            const rowCount = document.querySelectorAll('.milestone-row').length + 1;
            const newRow = document.createElement('div');
            newRow.className = 'milestone-row bg-slate-50 border border-slate-200/90 p-3.5 sm:p-4 rounded-[3px] space-y-3';
            newRow.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <span class="w-5 h-5 bg-white border border-slate-200 rounded-[3px] flex items-center justify-center text-[10px] font-bold text-[#1952E1]">${rowCount}</span>
                        Milestone ${rowCount}
                    </span>
                    <button type="button" class="remove-milestone-btn text-[11px] font-bold text-red-600 hover:text-red-700 transition-colors">Remove</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-6">
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Deliverable Title / Objective</label>
                        <input type="text" name="milestone_title[]" placeholder="e.g. Code Review, System Deployment & Training" required class="milestone-title w-full bg-white border border-slate-200 rounded-[3px] px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
                    </div>
                    <div class="grid grid-cols-2 sm:col-span-6 sm:grid sm:grid-cols-2 gap-2.5 sm:gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Target Date</label>
                            <input type="date" name="milestone_due[]" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" required class="milestone-due w-full bg-white border border-slate-200 rounded-[3px] px-2.5 sm:px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Amount (₦)</label>
                            <input type="number" name="milestone_amount[]" value="50000" min="5000" step="5000" required class="milestone-amount w-full bg-white border border-slate-200 rounded-[3px] px-2.5 sm:px-3 py-2 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#1952E1]">
                        </div>
                    </div>
                </div>
            `;
            milestonesContainer.appendChild(newRow);
            attachMilestoneEvents(newRow);
            recalcBudget();
        });
    }

    document.querySelectorAll('.milestone-row').forEach(attachMilestoneEvents);
    updateCategoryScope();
});
</script>

<?php include __DIR__ . '/components/footer.php'; ?>

