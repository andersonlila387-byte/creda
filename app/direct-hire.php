<?php 
/**
 * Scriptly Escrow - Direct Hire & Custom Booking
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();

// Require Login
$client_id = $_SESSION['user_id'] ?? null;
$active_role = $_SESSION['active_role_mode'] ?? ($_SESSION['primary_role'] ?? 'client');
if (!$client_id || $active_role !== 'client') {
    header("Location: ../login.php?redirect=direct-hire.php?" . http_build_query($_GET));
    exit;
}

$provider_id = $_GET['provider_id'] ?? null;

if (!$provider_id) {
    header("Location: talent.php");
    exit;
}

// Fetch Provider Info
$sql = "
    SELECT u.id, u.full_name, u.username, u.email, u.created_at as joined_date,
           tp.title, tp.hourly_rate, tp.rating, tp.rating_count, tp.job_success_percentage, 
           tp.location, tp.avatar_url, tp.bio, tp.skills, tp.turnaround_time
    FROM users u 
    LEFT JOIN talent_profiles tp ON u.id = tp.user_id 
    WHERE u.id = ? LIMIT 1
";
$stmt = $db->prepare($sql);
$stmt->execute([$provider_id]);
$provider = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$provider) {
    die("Invalid provider.");
}

// Default fallbacks
$provider_name = htmlspecialchars($provider['full_name'] ?? 'Specialist');
$provider_title = htmlspecialchars($provider['title'] ?? 'Verified Specialist');
$provider_location = htmlspecialchars($provider['location'] ?? 'Remote');
$provider_rating = number_format((float)($provider['rating'] ?? 5.0), 1);
$provider_rating_count = (int)($provider['rating_count'] ?? 0);
$provider_jss = (int)($provider['job_success_percentage'] ?? 100);
$provider_hourly = (float)($provider['hourly_rate'] ?? 0);
$avatar_url = !empty($provider['avatar_url']) ? $provider['avatar_url'] : "https://ui-avatars.com/api/?name=" . urlencode($provider['full_name']) . "&background=1952E1&color=ffffff&bold=true";

// Handle Direct Booking Submission
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_title = trim($_POST['title'] ?? '');
    $budget = floatval($_POST['budget'] ?? 0);
    $deadline_days = intval($_POST['deadline_days'] ?? 7);
    $description = trim($_POST['description'] ?? '');
    
    if (empty($project_title) || $budget <= 0) {
        $error = "Please provide a valid project title and budget.";
    } else {
        $fee = $budget * 0.05; // 5% escrow fee
        $total = $budget + $fee;
        
        // Handle Optional File Upload
        $file_url = null;
        $original_file_name = null;
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['attachment']['tmp_name'];
            $original_file_name = basename($_FILES['attachment']['name']);
            $file_size = $_FILES['attachment']['size'];
            $file_ext = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));
            
            $max_size = 25 * 1024 * 1024; // 25MB
            $allowed_exts = ['pdf', 'doc', 'docx', 'txt', 'zip', 'png', 'jpg', 'jpeg', 'fig', 'xd', 'csv', 'xlsx'];
            
            if ($file_size > $max_size) {
                $error = "The uploaded file is too large. Maximum size is 25MB.";
            } elseif (!in_array($file_ext, $allowed_exts, true)) {
                $error = "File format not supported. Allowed formats: PDF, DOCX, ZIP, TXT, PNG, JPG.";
            } else {
                $upload_dir = __DIR__ . '/../assets/uploads/contracts/';
                if (!is_dir($upload_dir)) {
                    @mkdir($upload_dir, 0777, true);
                }
                $safe_name = 'brief_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $file_ext;
                $dest_path = $upload_dir . $safe_name;
                if (move_uploaded_file($file_tmp, $dest_path)) {
                    $file_url = '/assets/uploads/contracts/' . $safe_name;
                }
            }
        }

        if (empty($error)) {
            try {
                $db->beginTransaction();
                
                // 1. Create Contract
                $deadline_at = date('Y-m-d H:i:s', strtotime("+{$deadline_days} days"));
                $stmt_c = $db->prepare("INSERT INTO contracts (client_id, provider_id, package_id, title, total_amount, status, deadline_at, created_at, updated_at) VALUES (?, ?, NULL, ?, ?, 'active', ?, NOW(), NOW())");
                $stmt_c->execute([$client_id, $provider_id, $project_title, $total, $deadline_at]);
                $contract_id = $db->lastInsertId();

                // 2. Create Escrow Transaction (Mock Funded)
                $stmt_e = $db->prepare("INSERT INTO escrow_transactions (contract_id, amount, fee_amount, status) VALUES (?, ?, ?, 'funded')");
                $stmt_e->execute([$contract_id, $budget, $fee]);
                
                // 3. Record attached brief in requirements answers
                if (!empty($file_url)) {
                    $stmt_ans = $db->prepare("INSERT INTO contract_requirements_answers (contract_id, requirement_id, answer_text, file_path, submitted_at) VALUES (?, 0, ?, ?, NOW())");
                    $stmt_ans->execute([$contract_id, "Project brief & attached files: " . $original_file_name, $file_url]);
                }

                // 4. Save scope & attachments into direct chat
                if (!empty($description) || !empty($file_url)) {
                    $msg_text = "Project Scope & Requirements:\n\n" . $description;
                    if (!empty($file_url)) {
                        $msg_text .= "\n\nAttached Brief: " . $original_file_name . "\nLink: " . $file_url;
                    }
                    $stmt_m = $db->prepare("INSERT INTO messages (sender_id, receiver_id, content, is_read) VALUES (?, ?, ?, 0)");
                    $stmt_m->execute([$client_id, $provider_id, $msg_text]);
                }
                
                $db->commit();
                
                // Redirect to Contract Details
                header("Location: contract-details.php?id=" . $contract_id . "&success=direct_hire");
                exit;
            } catch (Exception $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                $error = "Booking failed: " . $e->getMessage();
            }
        }
    }
}

$page_title = 'Direct Hire Offer';
$active_tab = 'talent';
require_once __DIR__ . '/components/head.php'; 
?>

<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Layout Area -->
<main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-hidden relative bg-[#EFF2F7]">
    
    <?php include __DIR__ . '/components/header.php'; ?>

    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 mobile-bottom-space md:pb-6 lg:pb-12 scroll-smooth">
        
        <div class="max-w-6xl mx-auto space-y-6">

            <!-- Top Navigation & Protection Badge -->
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <a href="provider-profile.php?id=<?= $provider_id ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-200/90 rounded-[3px] text-xs font-semibold text-slate-700 hover:text-[#1952E1] transition-colors shadow-2xs">
                    <i class="ph-bold ph-arrow-left"></i>
                    <span>Back to Profile</span>
                </a>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2.5 py-1 rounded-[3px]">
                    <i class="ph-fill ph-shield-check text-xs"></i>
                    <span>100% Escrow Protected Booking</span>
                </span>
            </div>

            <!-- Page Header Card -->
            <div class="bg-white p-5 sm:p-6 rounded-[3px] border border-slate-200/90 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Direct Hire Contract</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-xs font-semibold text-[#1952E1]">Custom Booking</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        Send Direct Offer to <?= $provider_name ?>
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Define your deliverables, attachments, and budget. Funds are held safely in Scriptly Escrow until you approve the final delivery.
                    </p>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-[3px] text-xs font-bold text-rose-800 flex items-center gap-2 shadow-xs">
                    <i class="ph-fill ph-warning-circle text-rose-600 text-sm shrink-0"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <!-- 2-COLUMN BOOKING GRID -->
            <form method="POST" id="direct-hire-form" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- LEFT COLUMN: Contract Specification Form (7 Cols) -->
                <div class="lg:col-span-7 space-y-6">
                    
                    <div class="bg-white rounded-[3px] border border-slate-200/90 p-5 sm:p-7 shadow-sm space-y-5">
                        
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Project Deliverables & Terms</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Specify clear requirements to ensure smooth collaboration</p>
                        </div>

                        <!-- 1. Contract Title -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Contract Title <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="title" 
                                required 
                                value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                                placeholder="e.g. Build Custom React Native Mobile Screen Flow" 
                                class="w-full bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-[3px] px-3.5 py-2.5 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#1952E1] focus:ring-1 focus:ring-[#1952E1] transition-all"
                            >
                        </div>

                        <!-- 2. Scope & Instructions -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Detailed Scope & Requirements <span class="text-rose-500">*</span>
                            </label>
                            <textarea 
                                name="description" 
                                rows="5" 
                                required
                                placeholder="Describe the project objectives, required deliverables, technology stack, assets provided, and any specific expectations..." 
                                class="w-full bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-[3px] p-3.5 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#1952E1] focus:ring-1 focus:ring-[#1952E1] transition-all"
                            ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            <p class="text-[11px] text-slate-400 mt-1">
                                This will be recorded on the official contract agreement and sent directly to the specialist.
                            </p>
                        </div>

                        <!-- 3. File Upload Dropzone -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Project Brief & Reference Attachments <span class="text-xs font-normal text-slate-400">(Optional)</span>
                            </label>
                            
                            <div class="relative border-2 border-dashed border-slate-200 hover:border-[#1952E1] bg-slate-50/50 hover:bg-white rounded-[3px] p-5 text-center transition-all group" id="upload-dropzone">
                                <input 
                                    type="file" 
                                    name="attachment" 
                                    id="attachment-input" 
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    accept=".pdf,.doc,.docx,.txt,.zip,.png,.jpg,.jpeg,.fig,.xd,.csv,.xlsx"
                                >
                                
                                <div id="upload-idle-state" class="space-y-2">
                                    <div class="w-10 h-10 rounded-full bg-blue-50 text-[#1952E1] flex items-center justify-center mx-auto transition-transform group-hover:scale-110">
                                        <i class="ph-bold ph-cloud-arrow-up text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">
                                            <span class="text-[#1952E1] hover:underline">Click to upload brief</span> or drag and drop
                                        </p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">PDF, DOCX, ZIP, PNG, JPG up to 25MB</p>
                                    </div>
                                </div>

                                <div id="upload-file-selected" class="hidden items-center justify-between bg-white border border-slate-200 rounded-[3px] p-3 text-left">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center shrink-0">
                                            <i class="ph-bold ph-file-text text-base"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-800 truncate" id="selected-file-name">filename.pdf</p>
                                            <p class="text-[10px] text-slate-400" id="selected-file-size">1.2 MB</p>
                                        </div>
                                    </div>
                                    <button type="button" id="remove-file-btn" class="text-slate-400 hover:text-rose-600 p-1.5 transition-colors relative z-20 cursor-pointer" title="Remove file">
                                        <i class="ph-bold ph-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Budget & Delivery Timeframe Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Agreed Project Budget (₦) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">₦</span>
                                    <input 
                                        type="number" 
                                        name="budget" 
                                        id="budget-input" 
                                        required 
                                        min="1000" 
                                        step="500" 
                                        value="<?= htmlspecialchars($_POST['budget'] ?? '') ?>"
                                        placeholder="50000" 
                                        class="w-full bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-[3px] pl-8 pr-3.5 py-2.5 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#1952E1] focus:ring-1 focus:ring-[#1952E1] transition-all"
                                    >
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">Minimum budget is ₦1,000</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Target Delivery Timeframe
                                </label>
                                <select 
                                    name="deadline_days" 
                                    class="w-full bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-[3px] px-3.5 py-2.5 text-sm font-medium text-slate-900 focus:outline-none focus:border-[#1952E1] transition-all"
                                >
                                    <option value="3" <?= (($_POST['deadline_days'] ?? '') === '3') ? 'selected' : '' ?>>3 Days (Fast delivery)</option>
                                    <option value="7" <?= (($_POST['deadline_days'] ?? '7') === '7') ? 'selected' : '' ?>>7 Days (Standard turnaround)</option>
                                    <option value="14" <?= (($_POST['deadline_days'] ?? '') === '14') ? 'selected' : '' ?>>14 Days (2 Weeks)</option>
                                    <option value="21" <?= (($_POST['deadline_days'] ?? '') === '21') ? 'selected' : '' ?>>21 Days (3 Weeks)</option>
                                    <option value="30" <?= (($_POST['deadline_days'] ?? '') === '30') ? 'selected' : '' ?>>30 Days (1 Month)</option>
                                </select>
                                <p class="text-[11px] text-slate-400 mt-1">Can be extended mutually if needed</p>
                            </div>
                        </div>

                    </div>

                    <!-- Escrow Protection Guarantee Bento -->
                    <div class="bg-blue-50/60 border border-blue-200/80 rounded-[3px] p-4 sm:p-5 flex items-start gap-4">
                        <div class="w-10 h-10 rounded-[3px] bg-blue-100/80 text-[#1952E1] flex items-center justify-center shrink-0">
                            <i class="ph-bold ph-shield-check text-xl"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-xs font-bold text-slate-900">How Scriptly Escrow Protects You</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Your payment is securely placed into escrow upfront. The specialist starts work immediately, but funds are only released to their wallet when you review and confirm the completed project.
                            </p>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: Provider Card & Order Summary (5 Cols) -->
                <div class="lg:col-span-5 space-y-5 lg:sticky lg:top-4">
                    
                    <!-- Selected Specialist Card -->
                    <div class="bg-white rounded-[3px] border border-slate-200/90 shadow-sm overflow-hidden">
                        <div class="bg-slate-50/70 border-b border-slate-100 px-5 py-3.5 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Hiring Professional</span>
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-[2px] border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Available
                            </span>
                        </div>

                        <div class="p-5 space-y-4">
                            <div class="flex items-start gap-4">
                                <img 
                                    src="<?= $avatar_url ?>" 
                                    alt="<?= $provider_name ?>" 
                                    class="w-14 h-14 rounded-full object-cover border border-slate-200 shrink-0 bg-slate-100"
                                >
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5">
                                        <h3 class="font-bold text-base text-slate-900 truncate"><?= $provider_name ?></h3>
                                        <i class="ph-fill ph-seal-check text-[#1952E1] text-sm shrink-0" title="Verified Pro"></i>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-600 truncate mt-0.5"><?= $provider_title ?></p>
                                    <p class="text-[11px] text-slate-400 flex items-center gap-1 mt-1">
                                        <i class="ph-bold ph-map-pin text-slate-400"></i>
                                        <span><?= $provider_location ?></span>
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                                <div class="bg-slate-50 p-2.5 rounded-[2px] text-center border border-slate-100">
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Rating</div>
                                    <div class="text-xs font-black text-slate-800 flex items-center justify-center gap-1 mt-0.5">
                                        <i class="ph-fill ph-star text-amber-500"></i>
                                        <span><?= $provider_rating ?></span>
                                        <span class="text-slate-400 font-normal text-[10px]">(<?= $provider_rating_count ?>)</span>
                                    </div>
                                </div>
                                <div class="bg-slate-50 p-2.5 rounded-[2px] text-center border border-slate-100">
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Job Success</div>
                                    <div class="text-xs font-black text-emerald-700 flex items-center justify-center gap-1 mt-0.5">
                                        <i class="ph-bold ph-chart-line-up"></i>
                                        <span><?= $provider_jss ?>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Escrow Deposit Summary Card -->
                    <div class="bg-white rounded-[3px] border border-slate-200/90 shadow-sm overflow-hidden">
                        <div class="bg-slate-50/70 border-b border-slate-100 px-5 py-3.5">
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Escrow Summary</h3>
                        </div>

                        <div class="p-5 space-y-3.5">
                            
                            <div class="flex items-center justify-between text-xs font-medium text-slate-600">
                                <span>Agreed Project Subtotal</span>
                                <span class="font-bold text-slate-900" id="summary-subtotal">₦0.00</span>
                            </div>

                            <div class="flex items-center justify-between text-xs font-medium text-slate-600">
                                <span class="flex items-center gap-1" title="Covers payment gateway charges and 24/7 dispute protection">
                                    <span>Escrow Protection Fee (5%)</span>
                                    <i class="ph-bold ph-info text-slate-400 text-xs"></i>
                                </span>
                                <span class="font-bold text-slate-900" id="summary-fee">₦0.00</span>
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-black text-slate-900">Total Escrow Deposit</div>
                                    <div class="text-[10px] text-slate-400">Held securely until milestone signoff</div>
                                </div>
                                <div class="text-xl font-black text-[#1952E1]" id="summary-total">₦0.00</div>
                            </div>

                            <!-- Submit Button -->
                            <button 
                                type="submit" 
                                class="w-full mt-4 py-3.5 bg-[#1952E1] hover:bg-blue-700 active:scale-[0.99] text-white font-bold text-sm rounded-[3px] transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <i class="ph-bold ph-lock-key"></i>
                                <span>Fund Escrow & Send Offer</span>
                            </button>

                            <div class="pt-3 border-t border-slate-100 space-y-2 text-[11px] text-slate-500">
                                <div class="flex items-center gap-2">
                                    <i class="ph-bold ph-check-circle text-emerald-600"></i>
                                    <span>Instant full refund if specialist declines</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="ph-bold ph-shield-check text-blue-600"></i>
                                    <span>256-bit encrypted Paystack escrow payment</span>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </form>

        </div>

    </div>

</main>

<?php include __DIR__ . '/components/footer.php'; ?>

<script>
    // Live Financial Calculations
    const budgetInput = document.getElementById('budget-input');
    const subtotalEl = document.getElementById('summary-subtotal');
    const feeEl = document.getElementById('summary-fee');
    const totalEl = document.getElementById('summary-total');

    function updateCalculations() {
        const val = parseFloat(budgetInput.value) || 0;
        const fee = val * 0.05;
        const total = val + fee;

        subtotalEl.innerText = '₦' + val.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        feeEl.innerText = '₦' + fee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        totalEl.innerText = '₦' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    if (budgetInput) {
        budgetInput.addEventListener('input', updateCalculations);
        updateCalculations();
    }

    // File Upload Interactive Dropzone
    const attachmentInput = document.getElementById('attachment-input');
    const idleState = document.getElementById('upload-idle-state');
    const selectedState = document.getElementById('upload-file-selected');
    const fileNameEl = document.getElementById('selected-file-name');
    const fileSizeEl = document.getElementById('selected-file-size');
    const removeBtn = document.getElementById('remove-file-btn');

    function formatBytes(bytes, decimals = 1) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    if (attachmentInput) {
        attachmentInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                fileNameEl.innerText = file.name;
                fileSizeEl.innerText = formatBytes(file.size);
                idleState.classList.add('hidden');
                selectedState.classList.remove('hidden');
                selectedState.classList.add('flex');
            }
        });
    }

    if (removeBtn) {
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            attachmentInput.value = '';
            selectedState.classList.add('hidden');
            selectedState.classList.remove('flex');
            idleState.classList.remove('hidden');
        });
    }
</script>
