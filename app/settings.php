<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();
$user_id = $_SESSION['user_id'] ?? 1;

// Fetch user info
$user_stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$user_info = $user_stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $full_name = trim($first_name . ' ' . $last_name);
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        
        $institution = trim($_POST['institution'] ?? null);
        $faculty = trim($_POST['faculty'] ?? null);
        $department = trim($_POST['department'] ?? null);
        $study_level = trim($_POST['study_level'] ?? null);
        $expected_completion_year = trim($_POST['expected_completion_year'] ?? null);
        $business_name = trim($_POST['business_name'] ?? null);
        $rc_number = trim($_POST['rc_number'] ?? null);
        $company_website = trim($_POST['company_website'] ?? null);
        $industry = trim($_POST['industry'] ?? null);
        $bio = trim($_POST['bio'] ?? null);
        
        if (!empty($full_name)) {
            $stmt = $db->prepare("UPDATE users SET 
                full_name = ?, 
                phone_number = ?, 
                address = ?, 
                institution = ?, 
                faculty = ?, 
                department = ?, 
                study_level = ?, 
                expected_completion_year = ?, 
                business_name = ?,
                rc_number = ?,
                company_website = ?,
                industry = ?,
                bio = ? 
                WHERE id = ?");
            $stmt->execute([
                $full_name, 
                $phone, 
                $address, 
                $institution, 
                $faculty, 
                $department, 
                $study_level, 
                $expected_completion_year, 
                $business_name,
                $rc_number,
                $company_website,
                $industry,
                $bio, 
                $user_id
            ]);
            
            // Update Session state if it corresponds to current user
            if ($user_id == ($_SESSION['user_id'] ?? null)) {
                $_SESSION['user_phone'] = $phone;
                $_SESSION['user_address'] = $address;
                $_SESSION['institution'] = $institution;
                $_SESSION['faculty'] = $faculty;
                $_SESSION['department'] = $department;
                $_SESSION['study_level'] = $study_level;
                $_SESSION['expected_completion_year'] = $expected_completion_year;
                $_SESSION['business_name'] = $business_name;
                $_SESSION['rc_number'] = $rc_number;
                $_SESSION['company_website'] = $company_website;
                $_SESSION['industry'] = $industry;
            }
            
            header('Location: settings.php?success=1');
            exit;
        }
    }
    
    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        
        if (password_verify($current, $user_info['password_hash'])) {
            $hash = password_hash($new, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$hash, $user_id]);
            header('Location: settings.php?success=password');
            exit;
        } else {
            header('Location: settings.php?error=invalid_current');
            exit;
        }
    }
}

$page_title = 'Account Settings & Profile';
$active_tab = 'settings';
require_once __DIR__ . '/components/head.php'; 
?>

<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Layout Area -->
<main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-y-auto bg-[#EFF2F7] mobile-bottom-space md:pb-8">
    
    <?php include __DIR__ . '/components/header.php'; ?>

    <!-- Main Settings Canvas -->
    <div class="max-w-6xl w-full mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        
        <?php if (isset($_GET['success'])): ?>
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[3px] text-xs font-bold text-emerald-800 flex items-center gap-2">
                <i class="ph-fill ph-check-circle text-emerald-600 text-sm"></i>
                <span>Settings updated successfully!</span>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-[3px] text-xs font-bold text-rose-800 flex items-center gap-2">
                <i class="ph-fill ph-warning-circle text-rose-600 text-sm"></i>
                <span>Error updating settings. Please verify inputs.</span>
            </div>
        <?php endif; ?>

        <!-- Page Top Header Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Account & Profile Settings</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Manage your student profile, verification status, escrow payments, and security.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[3px] bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold shadow-2xs">
                    <i class="ph-fill ph-shield-check text-emerald-600 text-sm"></i>
                    <span>Verified <?= htmlspecialchars(ucfirst($user_info['entity_type'] ?? 'student')) ?> <?= htmlspecialchars(ucfirst($user_info['primary_role'] ?? 'client')) ?></span>
                </span>
            </div>
        </div>

        <!-- Settings Main Grid (Vertical Tabs on Desktop, Scrollable Pills on Mobile) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- LEFT NAVIGATION COLUMN (Tabs Dock) -->
            <aside class="lg:col-span-4 bg-white rounded-[3px] border border-slate-200/90 shadow-2xs overflow-hidden">
                
                <!-- User Quick Summary Card -->
                <?php 
                    $tab_avatar = "https://ui-avatars.com/api/?name=" . urlencode($user_info['full_name']) . "&background=f1f5f9&color=0f172a&bold=true";
                ?>
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center gap-3.5 bg-slate-50/50">
                    <div class="relative shrink-0">
                        <img id="tab-nav-avatar" src="<?= $tab_avatar ?>" alt="Avatar" class="w-12 h-12 rounded-full object-cover border-2 border-slate-200">
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 rounded-full ring-2 ring-white" title="Active"></span>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-sm font-bold text-slate-900 truncate"><?= htmlspecialchars($user_info['full_name']) ?></h2>
                        <p class="text-xs text-slate-500 truncate"><?= htmlspecialchars(ucfirst($user_info['primary_role'])) ?> Member</p>
                        <span class="text-[10px] font-bold text-[#1952E1] mt-0.5 block">User ID: CRD-USR-<?= $user_info['id'] ?></span>
                    </div>
                </div>

                <!-- Navigation Tabs List -->
                <?php 
                    $entity_type = strtolower($user_info['entity_type'] ?? 'student');
                    $is_corporate = in_array($entity_type, ['business', 'corporation']);
                ?>
                <nav class="p-2 space-y-1" id="settings-tab-list">
                    
                    <!-- 1. Profile Tab -->
                    <button type="button" class="settings-nav-btn active w-full flex items-center justify-between px-3.5 py-2.5 rounded-[3px] text-xs font-bold text-left transition-colors bg-[#1952E1] text-white" data-target="tab-profile">
                        <div class="flex items-center gap-2.5">
                            <i class="ph-bold <?= $is_corporate ? 'ph-buildings' : 'ph-user-circle' ?> text-base"></i>
                            <span><?= $is_corporate ? 'Personal & Company Info' : 'Personal & Academic Info' ?></span>
                        </div>
                        <i class="ph-bold ph-caret-right text-xs opacity-70"></i>
                    </button>

                    <!-- 2. Verification Tab -->
                    <button type="button" class="settings-nav-btn w-full flex items-center justify-between px-3.5 py-2.5 rounded-[3px] text-xs font-bold text-slate-700 hover:bg-slate-50 text-left transition-colors" data-target="tab-verification">
                        <div class="flex items-center gap-2.5">
                            <i class="ph-bold ph-identification-card text-base"></i>
                            <span><?= $is_corporate ? 'Corporate CAC & Verification' : 'Student ID & Verification' ?></span>
                        </div>
                        <span class="bg-emerald-50 text-emerald-800 text-[10px] px-1.5 py-0.5 rounded-[3px] font-bold">Verified</span>
                    </button>

                    <!-- 3. Escrow & Wallet Preferences -->
                    <button type="button" class="settings-nav-btn w-full flex items-center justify-between px-3.5 py-2.5 rounded-[3px] text-xs font-bold text-slate-700 hover:bg-slate-50 text-left transition-colors" data-target="tab-payments">
                        <div class="flex items-center gap-2.5">
                            <i class="ph-bold ph-wallet text-base"></i>
                            <span>Escrow & Billing Preferences</span>
                        </div>
                        <i class="ph-bold ph-caret-right text-xs text-slate-400"></i>
                    </button>

                    <!-- 4. Security & 2FA Tab -->
                    <button type="button" class="settings-nav-btn w-full flex items-center justify-between px-3.5 py-2.5 rounded-[3px] text-xs font-bold text-slate-700 hover:bg-slate-50 text-left transition-colors" data-target="tab-security">
                        <div class="flex items-center gap-2.5">
                            <i class="ph-bold ph-lock-key text-base"></i>
                            <span>Security & Active Sessions</span>
                        </div>
                        <span class="bg-blue-50 text-[#1952E1] text-[10px] px-1.5 py-0.5 rounded-[3px] font-bold">2FA Enabled</span>
                    </button>

                    <!-- 5. Notifications Tab -->
                    <button type="button" class="settings-nav-btn w-full flex items-center justify-between px-3.5 py-2.5 rounded-[3px] text-xs font-bold text-slate-700 hover:bg-slate-50 text-left transition-colors" data-target="tab-notifications">
                        <div class="flex items-center gap-2.5">
                            <i class="ph-bold ph-bell-simple text-base"></i>
                            <span>Notifications & Reminders</span>
                        </div>
                        <i class="ph-bold ph-caret-right text-xs text-slate-400"></i>
                    </button>

                </nav>

                <!-- Escrow Security Notice Box -->
                <div class="p-3.5 m-2 bg-blue-50/70 border border-blue-200 rounded-[3px] space-y-1.5 text-xs text-slate-700">
                    <div class="flex items-center gap-1.5 text-[#1952E1] font-bold">
                        <i class="ph-fill ph-shield-check text-sm"></i>
                        <span>100% Escrow Protection</span>
                    </div>
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        <?= $is_corporate ? 'Funds deposited for corporate deliverables, software development, or custom services remain securely locked in escrow until milestones are completed.' : 'Funds deposited for project reports or software remain locked in escrow until deliverables meet your requirements.' ?>
                    </p>
                </div>

            </aside>

            <!-- RIGHT CONTENT AREA (Tab Panes) -->
            <section class="lg:col-span-8 space-y-6">
                
                <!-- ==================================================================
                     TAB 1: PERSONAL & ACADEMIC INFO
                     ================================================================== -->
                <div id="tab-profile" class="settings-pane space-y-6">
                    
                    <div class="bg-white rounded-[3px] border border-slate-200/90 shadow-2xs p-5 sm:p-6 space-y-6">
                        
                        <div class="border-b border-slate-100 pb-4">
                            <h3 class="text-base font-bold text-slate-900"><?= $is_corporate ? 'Personal & Corporate Entity Profile' : 'Personal & Academic Profile' ?></h3>
                            <p class="text-xs text-slate-500 mt-0.5"><?= $is_corporate ? 'Update your personal details, registered business name, CAC RC number, and industry.' : 'Update your contact details, university, and project specialization.' ?></p>
                        </div>

                        <!-- Profile Avatar Uploader -->
                        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4">
                            <div class="relative shrink-0">
                                <img id="preview-profile-avatar" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150" alt="Amina Bello" class="w-20 h-20 rounded-full object-cover border-2 border-slate-200">
                                <button type="button" onclick="document.getElementById('avatar-file-input').click()" class="absolute bottom-0 right-0 w-7 h-7 rounded-full bg-[#1952E1] text-white flex items-center justify-center shadow-xs hover:bg-blue-700 transition-colors" title="Change Photo">
                                    <i class="ph-bold ph-camera text-xs"></i>
                                </button>
                                <input type="file" id="avatar-file-input" class="hidden" accept="image/*" onchange="previewAvatar(this)">
                            </div>
                            
                            <div class="space-y-1 text-center sm:text-left">
                                <h4 class="text-xs font-bold text-slate-900">Profile Photo</h4>
                                <p class="text-xs text-slate-500">Upload a clear square photo. JPG, PNG or WEBP (Max 2MB).</p>
                                <div class="flex items-center justify-center sm:justify-start gap-2 pt-1">
                                    <button type="button" onclick="document.getElementById('avatar-file-input').click()" class="px-3 py-1.5 text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-[3px] transition-colors">
                                        Upload New
                                    </button>
                                    <button type="button" onclick="resetAvatar()" class="px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50 rounded-[3px] transition-colors">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Form Fields Grid -->
                        <?php 
                            $names = explode(' ', $user_info['full_name'] ?? 'User');
                            $first_name = $names[0];
                            $last_name = isset($names[1]) ? implode(' ', array_slice($names, 1)) : '';
                        ?>
                        <form id="profile-info-form" method="POST" action="settings.php" class="space-y-4 pt-2">
                            <input type="hidden" name="update_profile" value="1">
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700">First Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="first_name" value="<?= htmlspecialchars($first_name) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700">Last Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="last_name" value="<?= htmlspecialchars($last_name) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700">Email Address <span class="text-red-500">*</span></label>
                                    <input type="email" readonly value="<?= htmlspecialchars($user_info['email']) ?>" class="w-full bg-slate-100 border border-slate-200 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-500 focus:outline-none cursor-not-allowed">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700">Phone Number (WhatsApp Active) <span class="text-red-500">*</span></label>
                                    <input type="tel" name="phone" value="<?= htmlspecialchars($user_info['phone_number'] ?? '') ?>" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]">
                                </div>
                            </div>

                             <!-- Profile Entity Fields (Dynamic for Student vs Business/Corporation) -->
                             <?php if ($is_corporate): ?>
                             <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-[3px] space-y-4">
                                 <div class="flex items-center gap-1.5 text-xs font-bold text-slate-900 border-b border-slate-200/60 pb-2">
                                     <i class="ph-bold ph-buildings text-[#1952E1]"></i>
                                     <span>Company & Business Information</span>
                                 </div>

                                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                     <div class="space-y-1.5">
                                         <label class="block text-xs font-bold text-slate-700">Registered Business / Company Name</label>
                                         <input type="text" name="business_name" value="<?= htmlspecialchars($user_info['business_name'] ?? '') ?>" placeholder="e.g. Cliniconnect Solutions Ltd" class="w-full bg-white border border-slate-200 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
                                     </div>

                                     <div class="space-y-1.5">
                                         <label class="block text-xs font-bold text-slate-700">CAC Registration / RC Number</label>
                                         <input type="text" name="rc_number" value="<?= htmlspecialchars($user_info['rc_number'] ?? '') ?>" placeholder="e.g. RC 987654 or BN 1234567" class="w-full bg-white border border-slate-200 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
                                     </div>
                                 </div>

                                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                     <div class="space-y-1.5">
                                         <label class="block text-xs font-bold text-slate-700">Industry / Sector</label>
                                         <select name="industry" class="w-full bg-white border border-slate-200 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
                                             <option value="" disabled <?= empty($user_info['industry']) ? 'selected' : '' ?>>Select business industry</option>
                                             <?php 
                                             $industries = ['Technology & IT', 'Education & E-learning', 'Marketing & Creative', 'Healthcare & Wellness', 'Finance & Consulting', 'Retail & E-commerce'];
                                             foreach($industries as $ind): 
                                             ?>
                                                 <option value="<?= $ind ?>" <?= ($user_info['industry'] ?? '') === $ind ? 'selected' : '' ?>><?= $ind ?></option>
                                             <?php endforeach; ?>
                                         </select>
                                     </div>

                                     <div class="space-y-1.5">
                                         <label class="block text-xs font-bold text-slate-700">Company Website URL</label>
                                         <input type="url" name="company_website" value="<?= htmlspecialchars($user_info['company_website'] ?? '') ?>" placeholder="e.g. https://example.com" class="w-full bg-white border border-slate-200 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
                                     </div>
                                 </div>
                             </div>
                             <?php else: ?>
                             <!-- Academic Profile Details (Visible for students) -->
                             <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-[3px] space-y-4">
                                 <div class="flex items-center gap-1.5 text-xs font-bold text-slate-900 border-b border-slate-200/60 pb-2">
                                     <i class="ph-bold ph-graduation-cap text-[#1952E1]"></i>
                                     <span>Academic & Institution Information</span>
                                 </div>

                                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                     <div class="space-y-1.5">
                                         <label class="block text-xs font-bold text-slate-700">Student ID / Matric Number</label>
                                         <input type="text" name="student_id" value="<?= htmlspecialchars($user_info['student_id'] ?? '') ?>" placeholder="e.g. 190407082" class="w-full bg-white border border-slate-200 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
                                     </div>

                                     <div class="space-y-1.5">
                                         <label class="block text-xs font-bold text-slate-700">Institution / University</label>
                                         <input type="text" name="institution" id="institution" list="institutions-list" value="<?= htmlspecialchars($user_info['institution'] ?? '') ?>" class="w-full bg-white border border-slate-200 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]" placeholder="Search or type your university...">
                                         <datalist id="institutions-list">
                                             <option value="University of Lagos (UNILAG)">
                                             <option value="Covenant University">
                                             <option value="University of Nigeria, Nsukka (UNN)">
                                             <option value="Federal University of Technology, Owerri (FUTO)">
                                             <option value="Ahmadu Bello University (ABU)">
                                             <option value="Obafemi Awolowo University (OAU)">
                                         </datalist>
                                     </div>
                                 </div>

                                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                     <div class="space-y-1.5">
                                         <label class="block text-xs font-bold text-slate-700">Faculty</label>
                                         <input type="text" name="faculty" value="<?= htmlspecialchars($user_info['faculty'] ?? '') ?>" placeholder="e.g. Science" class="w-full bg-white border border-slate-200 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
                                     </div>

                                     <div class="space-y-1.5">
                                         <label class="block text-xs font-bold text-slate-700">Department</label>
                                         <input type="text" name="department" value="<?= htmlspecialchars($user_info['department'] ?? '') ?>" placeholder="e.g. Computer Science" class="w-full bg-white border border-slate-200 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
                                     </div>
                                 </div>

                                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                     <div class="space-y-1.5">
                                         <label class="block text-xs font-bold text-slate-700">Study Level / Category</label>
                                         <select name="study_level" class="w-full bg-white border border-slate-200 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
                                             <option value="undergraduate" <?= ($user_info['study_level'] ?? '') === 'undergraduate' ? 'selected' : '' ?>>Undergraduate</option>
                                             <option value="postgraduate" <?= ($user_info['study_level'] ?? '') === 'postgraduate' ? 'selected' : '' ?>>Postgraduate</option>
                                             <option value="phd" <?= ($user_info['study_level'] ?? '') === 'phd' ? 'selected' : '' ?>>Ph.D. Researcher</option>
                                             <option value="independent" <?= ($user_info['study_level'] ?? '') === 'independent' ? 'selected' : '' ?>>Independent Project Owner</option>
                                         </select>
                                     </div>

                                     <div class="space-y-1.5">
                                         <label class="block text-xs font-bold text-slate-700">Expected Year of Completion</label>
                                         <input type="text" name="expected_completion_year" value="<?= htmlspecialchars($user_info['expected_completion_year'] ?? '') ?>" class="w-full bg-white border border-slate-200 rounded-[3px] px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
                                     </div>
                                 </div>
                             </div>
                             <?php endif; ?>

                             <!-- Bio & Project Needs -->
                             <div class="space-y-1.5">
                                 <label class="block text-xs font-bold text-slate-700">Project Focus & Bio Summary</label>
                                 <textarea name="bio" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] p-3 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]" placeholder="Briefly describe your project or research interests..."><?= htmlspecialchars($user_info['bio'] ?? '') ?></textarea>
                             </div>

                            <!-- Save Button -->
                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                                <button type="submit" class="px-5 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs rounded-[3px] transition-colors shadow-sm">
                                    Save Profile Changes
                                </button>
                            </div>

                        </form>

                    </div>

                </div>

                <!-- ==================================================================
                     TAB 2: IDENTITY & ENTITY VERIFICATION
                     ================================================================== -->
                <div id="tab-verification" class="settings-pane hidden space-y-6">
                    
                    <div class="bg-white rounded-[3px] border border-slate-200/90 shadow-2xs p-5 sm:p-6 space-y-6">
                        
                        <div class="border-b border-slate-100 pb-4">
                            <h3 class="text-base font-bold text-slate-900"><?= $is_corporate ? 'Corporate CAC & Identity Verification' : 'Student & Identity Verification' ?></h3>
                            <p class="text-xs text-slate-500 mt-0.5"><?= $is_corporate ? 'Verify your corporate standing with your CAC Registration Certificate and Authorized Signatory NIN.' : 'Verify your identity with your Student ID card or National ID (NIN) to ensure trust and escrow safety.' ?></p>
                        </div>

                        <!-- Verification Status Banner -->
                        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[3px] flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="ph-fill ph-check-circle text-lg"></i>
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-xs font-bold text-emerald-900"><?= $is_corporate ? 'Corporate Entity Verified' : 'Student Identity Verified' ?></h4>
                                <p class="text-xs text-emerald-800 leading-relaxed">
                                    <?= $is_corporate ? 'Your Corporate CAC Certificate and Business registration have been verified. You have full access to escrow-backed project hiring and corporate badges.' : 'Your University Student ID Card and NIN document have been verified. You have full access to escrow-backed project hiring and verified researcher badges.' ?>
                                </p>
                                <span class="text-[10px] font-bold text-emerald-700 block pt-0.5">Verified on: June 12, 2026 • <?= $is_corporate ? 'Validated with CAC Registry (' . htmlspecialchars($user_info['rc_number'] ?? 'RC 987654') . ')' : 'Validated with UNILAG Registry' ?></span>
                            </div>
                        </div>

                        <!-- Uploaded Documents Preview Grid -->
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold text-slate-900">Verified Documents</h4>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                
                                <?php if ($is_corporate): ?>
                                    <!-- Document 1: CAC Registration Certificate -->
                                    <div class="p-3.5 bg-slate-50 rounded-[3px] border border-slate-200/90 flex items-center justify-between">
                                        <div class="flex items-center gap-2.5 truncate">
                                            <i class="ph-bold ph-file-text text-xl text-[#1952E1] shrink-0"></i>
                                            <div class="truncate">
                                                <span class="text-xs font-bold text-slate-800 block truncate">CAC Certificate of Incorporation</span>
                                                <span class="text-[10px] text-slate-400">RC: <?= htmlspecialchars($user_info['rc_number'] ?? '987654') ?> • PDF (1.8 MB)</span>
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-[3px] shrink-0 ml-2">✓ Approved</span>
                                    </div>
                                <?php else: ?>
                                    <!-- Document 1: Student ID Card -->
                                    <div class="p-3.5 bg-slate-50 rounded-[3px] border border-slate-200/90 flex items-center justify-between">
                                        <div class="flex items-center gap-2.5 truncate">
                                            <i class="ph-bold ph-identification-badge text-xl text-[#1952E1] shrink-0"></i>
                                            <div class="truncate">
                                                <span class="text-xs font-bold text-slate-800 block truncate">UNILAG Student ID Card</span>
                                                <span class="text-[10px] text-slate-400">Matric: <?= htmlspecialchars($user_info['student_id'] ?? '190407082') ?> • PDF (1.2 MB)</span>
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-[3px] shrink-0 ml-2">✓ Approved</span>
                                    </div>
                                <?php endif; ?>

                                <!-- Document 2: NIN Slip / National ID -->
                                <div class="p-3.5 bg-slate-50 rounded-[3px] border border-slate-200/90 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5 truncate">
                                        <i class="ph-bold ph-shield-check text-xl text-emerald-600 shrink-0"></i>
                                        <div class="truncate">
                                            <span class="text-xs font-bold text-slate-800 block truncate">NIN Digital Verification Slip</span>
                                            <span class="text-[10px] text-slate-400">NIN: *******9821 • PDF (850 KB)</span>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-[3px] shrink-0 ml-2">✓ Matched</span>
                                </div>

                            </div>
                        </div>

                        <!-- Academic Safety & Escrow Assurance -->
                        <div class="p-4 bg-slate-50 rounded-[3px] border border-slate-200 space-y-2">
                            <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                <i class="ph-fill ph-lock-key text-[#1952E1]"></i>
                                <span>Academic Quality & Non-Plagiarism Guarantee</span>
                            </h4>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                All project deliverables (source code, project reports, SPSS datasets) are protected by Scriptly's 100% Escrow Guarantee. Freelancers agree to provide original, plagiarism-free work compatible with Turnitin standard submissions.
                            </p>
                        </div>

                        <!-- Update / Re-upload Option -->
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between flex-wrap gap-2">
                            <span class="text-xs text-slate-500">Need to update your university document or student level?</span>
                            <button type="button" onclick="alert('Verification documents can be updated by uploading new clear scans.');" class="px-4 py-2 text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-[3px] transition-colors">
                                Re-upload Updated Document
                            </button>
                        </div>

                    </div>

                </div>

                <!-- ==================================================================
                     TAB 3: ESCROW WALLET & PAYMENT PREFERENCES
                     ================================================================== -->
                <div id="tab-payments" class="settings-pane hidden space-y-6">
                    
                    <div class="bg-white rounded-[3px] border border-slate-200/90 shadow-2xs p-5 sm:p-6 space-y-6">
                        
                        <div class="border-b border-slate-100 pb-4">
                            <h3 class="text-base font-bold text-slate-900">Escrow & Billing Preferences</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Manage how you fund project milestones and configure payment receipts.</p>
                        </div>

                        <!-- Default Funding Methods -->
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold text-slate-900">Saved Escrow Funding Methods</h4>
                            
                            <div class="space-y-2">
                                <!-- Card 1: Paystack Mastercard -->
                                <div class="p-3.5 bg-slate-50 rounded-[3px] border border-slate-200/90 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-7 bg-slate-900 text-white rounded-[3px] flex items-center justify-center font-bold text-[10px] shrink-0">
                                            CARD
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-slate-800 block">Mastercard ending in 4128</span>
                                            <span class="text-[10px] text-slate-400">Expires 08/28 • Paystack Tokenized</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="bg-blue-50 text-[#1952E1] text-[10px] font-bold px-2 py-0.5 rounded-[3px]">Default</span>
                                        <button type="button" onclick="alert('Card options updated.');" class="text-xs text-slate-400 hover:text-slate-700 font-bold">Edit</button>
                                    </div>
                                </div>

                                <!-- Card 2: Dedicated Virtual Account -->
                                <div class="p-3.5 bg-slate-50 rounded-[3px] border border-slate-200/90 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-7 bg-[#1952E1] text-white rounded-[3px] flex items-center justify-center font-bold text-[10px] shrink-0">
                                            BANK
                                        </div>
                                        <div>
                                            <?php if (!empty($user_info['virtual_account_number'])): ?>
                                                <span class="text-xs font-bold text-slate-800 block"><?= htmlspecialchars($user_info['virtual_bank_name']) ?> Virtual NUBAN</span>
                                                <span class="text-[10px] text-slate-400">Acct: <?= htmlspecialchars($user_info['virtual_account_number']) ?> • Auto-instant funding</span>
                                            <?php else: ?>
                                                <span class="text-xs font-bold text-slate-800 block">Dedicated Virtual Bank Account</span>
                                                <span class="text-[10px] text-slate-400">No account linked yet. Visit the Wallet page to activate.</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($user_info['virtual_account_number'])): ?>
                                        <button type="button" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($user_info['virtual_account_number']) ?>'); alert('Virtual bank account details copied to clipboard!');" class="text-xs font-bold text-[#1952E1] hover:underline">
                                            Copy Account
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Escrow Milestone Approval Thresholds -->
                        <div class="space-y-3 pt-2">
                            <h4 class="text-xs font-bold text-slate-900">Milestone Review & Inspection Defaults</h4>
                            
                            <div class="p-4 bg-slate-50 rounded-[3px] border border-slate-200 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xs font-bold text-slate-800 block">Auto-Approval Countdown Reminder</span>
                                        <span class="text-[11px] text-slate-500">Receive SMS and Email reminders before the 7-day review window ends.</span>
                                    </div>
                                    <input type="checkbox" checked class="w-4 h-4 text-[#1952E1] rounded-[3px] focus:ring-0">
                                </div>

                                <div class="flex items-center justify-between border-t border-slate-200/60 pt-3">
                                    <div>
                                        <span class="text-xs font-bold text-slate-800 block">Automatic Escrow PDF Receipts</span>
                                        <span class="text-[11px] text-slate-500">Email official payment vouchers to amina.bello@student.unilag.edu.ng.</span>
                                    </div>
                                    <input type="checkbox" checked class="w-4 h-4 text-[#1952E1] rounded-[3px] focus:ring-0">
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                            <button type="button" onclick="showSavedAlert('Escrow and payment preferences updated!')" class="px-5 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs rounded-[3px] transition-colors shadow-sm">
                                Save Payment Settings
                            </button>
                        </div>

                    </div>

                </div>

                <!-- ==================================================================
                     TAB 4: SECURITY & ACTIVE SESSIONS
                     ================================================================== -->
                <div id="tab-security" class="settings-pane hidden space-y-6">
                    
                    <div class="bg-white rounded-[3px] border border-slate-200/90 shadow-2xs p-5 sm:p-6 space-y-6">
                        
                        <div class="border-b border-slate-100 pb-4">
                            <h3 class="text-base font-bold text-slate-900">Security & Two-Factor Authentication</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Manage your account credentials, 2FA protection, and active login sessions.</p>
                        </div>

                        <!-- Change Password Form -->
                        <form id="change-password-form" class="space-y-4">
                            <h4 class="text-xs font-bold text-slate-900">Change Password</h4>
                            
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">Current Password</label>
                                <input type="password" placeholder="••••••••••••" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] px-3.5 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700">New Password</label>
                                    <input type="password" placeholder="Minimum 8 characters" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] px-3.5 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700">Confirm New Password</label>
                                    <input type="password" placeholder="Re-enter new password" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] px-3.5 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white focus:border-[#1952E1]">
                                </div>
                            </div>

                            <div class="flex items-center justify-end">
                                <button type="button" onclick="showSavedAlert('Password successfully updated!')" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-[3px] transition-colors">
                                    Update Password
                                </button>
                            </div>
                        </form>

                        <!-- Two-Factor Authentication (2FA) -->
                        <div class="pt-4 border-t border-slate-100 space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">Two-Factor Authentication (2FA)</h4>
                                    <p class="text-xs text-slate-500">Protect high-value escrow releases and logins with one-time verification codes.</p>
                                </div>
                                <span class="bg-emerald-50 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded-[3px]">Active</span>
                            </div>

                            <div class="p-3.5 bg-slate-50 rounded-[3px] border border-slate-200 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <i class="ph-bold ph-shield-check text-xl text-[#1952E1]"></i>
                                    <div>
                                        <span class="text-xs font-bold text-slate-800 block">Email OTP & Authenticator App</span>
                                        <span class="text-[10px] text-slate-400">Codes sent to amina.bello@student.unilag.edu.ng</span>
                                    </div>
                                </div>
                                <button type="button" onclick="alert('Configuring 2FA Authenticator settings...');" class="text-xs font-bold text-[#1952E1] hover:underline">
                                    Re-configure
                                </button>
                            </div>
                        </div>

                        <!-- Active Browser Sessions -->
                        <div class="pt-4 border-t border-slate-100 space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-slate-900">Active Login Sessions</h4>
                                <button type="button" onclick="alert('All other devices have been logged out.');" class="text-xs font-bold text-red-600 hover:underline">
                                    Log Out All Other Devices
                                </button>
                            </div>

                            <div class="divide-y divide-slate-100 border border-slate-200 rounded-[3px] overflow-hidden">
                                
                                <!-- Session 1: Current Session -->
                                <div class="p-3 bg-slate-50/50 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-3">
                                        <i class="ph-bold ph-laptop text-lg text-slate-600"></i>
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-bold text-slate-800">Chrome on macOS (Current Device)</span>
                                                <span class="bg-emerald-50 text-emerald-800 text-[9px] font-bold px-1.5 py-0.2 rounded-[3px]">This Browser</span>
                                            </div>
                                            <span class="text-[10px] text-slate-400">Lagos, Nigeria • IP: 102.89.44.12 • Active Now</span>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400">Online</span>
                                </div>

                                <!-- Session 2: Mobile Phone -->
                                <div class="p-3 bg-white flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-3">
                                        <i class="ph-bold ph-device-mobile text-lg text-slate-600"></i>
                                        <div>
                                            <span class="font-bold text-slate-800 block">Safari on iPhone 14 Pro</span>
                                            <span class="text-[10px] text-slate-400">Lagos, Nigeria • IP: 102.89.44.18 • 2 hours ago</span>
                                        </div>
                                    </div>
                                    <button type="button" onclick="alert('Session revoked for iPhone.');" class="text-xs font-bold text-slate-500 hover:text-red-600">
                                        Revoke
                                    </button>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>

                <!-- ==================================================================
                     TAB 5: NOTIFICATIONS & MILESTONE REMINDERS
                     ================================================================== -->
                <div id="tab-notifications" class="settings-pane hidden space-y-6">
                    
                    <div class="bg-white rounded-[3px] border border-slate-200/90 shadow-2xs p-5 sm:p-6 space-y-6">
                        
                        <div class="border-b border-slate-100 pb-4">
                            <h3 class="text-base font-bold text-slate-900">Notification & Alert Preferences</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Control how and when you receive project milestone updates and escrow receipts.</p>
                        </div>

                        <!-- Notification Settings Matrix -->
                        <div class="space-y-4 divide-y divide-slate-100">
                            
                            <!-- 1. Milestone Submissions -->
                            <div class="pt-3 first:pt-0 flex items-start justify-between gap-4">
                                <div class="space-y-0.5">
                                    <h4 class="text-xs font-bold text-slate-900">Deliverable Submissions & Code Uploads</h4>
                                    <p class="text-xs text-slate-500">Get notified immediately when your hired freelancer uploads milestone files or reports.</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" checked class="sr-only peer">
                                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#1952E1]"></div>
                                    </label>
                                </div>
                            </div>

                            <!-- 2. Auto-Approval Countdown -->
                            <div class="pt-3 flex items-start justify-between gap-4">
                                <div class="space-y-0.5">
                                    <h4 class="text-xs font-bold text-slate-900">7-Day Inspection Countdown Alerts</h4>
                                    <p class="text-xs text-slate-500">Remind me 48 hours and 24 hours before a deliverable automatically approves and disburses escrow.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#1952E1]"></div>
                                </label>
                            </div>

                            <!-- 3. Direct Messages & Calls -->
                            <div class="pt-3 flex items-start justify-between gap-4">
                                <div class="space-y-0.5">
                                    <h4 class="text-xs font-bold text-slate-900">Direct Chat Messages & Video Call Schedules</h4>
                                    <p class="text-xs text-slate-500">Receive in-app alerts and email notifications for candidate direct messages and Google Meet invites.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#1952E1]"></div>
                                </label>
                            </div>

                            <!-- 4. Escrow Financial Receipts -->
                            <div class="pt-3 flex items-start justify-between gap-4">
                                <div class="space-y-0.5">
                                    <h4 class="text-xs font-bold text-slate-900">Escrow Deposits & Release Confirmations</h4>
                                    <p class="text-xs text-slate-500">Send PDF receipts and deposit confirmations to your email whenever wallet transactions occur.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#1952E1]"></div>
                                </label>
                            </div>

                        </div>

                        <!-- Save Notifications Button -->
                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                            <button type="button" onclick="showSavedAlert('Notification preferences saved!')" class="px-5 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs rounded-[3px] transition-colors shadow-sm">
                                Save Notification Preferences
                            </button>
                        </div>

                    </div>

                </div>

            </section>

        </div>

    </div>

</main>

<!-- Main flex container ends -->
</div>

<!-- ==========================================================================
     JAVASCRIPT: TAB SWITCHING & INTERACTIVE PREVIEWS
     ========================================================================== -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // Tab switching logic
    const tabBtns = document.querySelectorAll('.settings-nav-btn');
    const panes = document.querySelectorAll('.settings-pane');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');

            // Reset all tab buttons
            tabBtns.forEach(b => {
                b.classList.remove('active', 'bg-[#1952E1]', 'text-white');
                b.classList.add('text-slate-700', 'hover:bg-slate-50');
            });

            // Activate current tab button
            btn.classList.add('active', 'bg-[#1952E1]', 'text-white');
            btn.classList.remove('text-slate-700', 'hover:bg-slate-50');

            // Toggle panes
            panes.forEach(pane => {
                if (pane.id === targetId) {
                    pane.classList.remove('hidden');
                } else {
                    pane.classList.add('hidden');
                }
            });
        });
    });

    // Dynamic University Fetch from Hipolabs API
    const institutionsList = document.getElementById('institutions-list');
    const institutionInput = document.getElementById('institution');

    async function fetchUniversities() {
        try {
            const response = await fetch('http://universities.hipolabs.com/search?country=Nigeria');
            if (!response.ok) throw new Error('API issue');
            const data = await response.json();
            
            if (institutionsList) {
                // Clear existing options
                institutionsList.innerHTML = '';
                
                // Sort names alphabetically
                data.sort((a, b) => a.name.localeCompare(b.name));
                
                data.forEach(uni => {
                    const option = document.createElement('option');
                    option.value = uni.name;
                    institutionsList.appendChild(option);
                });
            }
        } catch (err) {
            console.warn('Failed to fetch from Hipolabs API, using default list.', err);
        }
    }

    if (institutionInput) {
        institutionInput.addEventListener('focus', fetchUniversities, { once: true });
    }

});

// Global Helpers
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-profile-avatar').src = e.target.result;
            const navAvatar = document.getElementById('tab-nav-avatar');
            if (navAvatar) navAvatar.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function resetAvatar() {
    const defaultAvatar = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150';
    document.getElementById('preview-profile-avatar').src = defaultAvatar;
    const navAvatar = document.getElementById('tab-nav-avatar');
    if (navAvatar) navAvatar.src = defaultAvatar;
}

function showSavedAlert(msg) {
    alert(msg);
}
</script>

<?php include __DIR__ . '/components/footer.php'; ?>



