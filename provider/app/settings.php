<?php
$page_title = 'Pro Profile Settings';
$active_tab = 'settings';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

// Verify Authentication
$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['user_email'] ?? null;

if (empty($user_email)) {
    header('Location: ../login.php');
    exit;
}

try {
    $db = getDBConnection();
    
    // Save Form Handler
    $msg = '';
    $msg_type = 'success';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve inputs
        $full_name = trim($_POST['full_name'] ?? '');
        $phone_number = trim($_POST['phone_number'] ?? '');
        $address = trim($_POST['address'] ?? '');
        
        $student_id = trim($_POST['student_id'] ?? '');
        $institution = trim($_POST['institution'] ?? '');
        $faculty = trim($_POST['faculty'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $expected_completion_year = trim($_POST['expected_completion_year'] ?? '');
        
        $title = trim($_POST['title'] ?? '');
        $hourly_rate = floatval($_POST['hourly_rate'] ?? 0);
        $bio = trim($_POST['bio'] ?? '');
        $skills = trim($_POST['skills'] ?? '');

        if (empty($full_name) || empty($phone_number) || empty($address)) {
            $msg = 'Please fill out all basic contact details.';
            $msg_type = 'error';
        } else {
            // Update users table
            $up_user = $db->prepare("
                UPDATE users SET 
                    full_name = ?, 
                    phone_number = ?, 
                    address = ?,
                    student_id = ?, 
                    institution = ?, 
                    faculty = ?, 
                    department = ?, 
                    expected_completion_year = ?
                WHERE id = ?
            ");
            $up_user->execute([
                $full_name, $phone_number, $address,
                $student_id, $institution, $faculty, $department, $expected_completion_year,
                $user_id
            ]);

            // Check if talent profile exists
            $tp_check = $db->prepare("SELECT id FROM talent_profiles WHERE user_id = ? LIMIT 1");
            $tp_check->execute([$user_id]);
            $has_tp = $tp_check->fetch();

            if ($has_tp) {
                // Update talent_profiles
                $up_tp = $db->prepare("
                    UPDATE talent_profiles SET 
                        title = ?, 
                        hourly_rate = ?, 
                        bio = ?, 
                        skills = ?
                    WHERE user_id = ?
                ");
                $up_tp->execute([$title, $hourly_rate, $bio, $skills, $user_id]);
            } else {
                // Insert new talent profile
                $ins_tp = $db->prepare("
                    INSERT INTO talent_profiles (user_id, title, hourly_rate, bio, skills, rating, job_success_percentage) 
                    VALUES (?, ?, ?, ?, ?, 5.0, 100)
                ");
                $ins_tp->execute([$user_id, $title, $hourly_rate, $bio, $skills]);
            }

            // Sync with session
            $_SESSION['user_name'] = $full_name;
            $_SESSION['user_phone'] = $phone_number;
            $_SESSION['user_address'] = $address;
            $_SESSION['student_id'] = $student_id;
            $_SESSION['institution'] = $institution;
            $_SESSION['faculty'] = $faculty;
            $_SESSION['department'] = $department;
            $_SESSION['expected_completion_year'] = $expected_completion_year;

            $msg = 'Profile settings successfully updated!';
            $msg_type = 'success';
        }
    }

    // Fetch fresh user data with joined profile details
    $u_stmt = $db->prepare("
        SELECT u.*, tp.title as p_title, tp.bio as p_bio, tp.skills as p_skills, tp.hourly_rate
        FROM users u
        LEFT JOIN talent_profiles tp ON u.id = tp.user_id
        WHERE u.id = :uid LIMIT 1
    ");
    $u_stmt->execute([':uid' => $user_id]);
    $user_info = $u_stmt->fetch(PDO::FETCH_ASSOC);

} catch (\Exception $e) {
    $user_info = [];
    $msg = 'Error: ' . $e->getMessage();
    $msg_type = 'error';
}

require_once __DIR__ . '/components/head.php';
?>

<!-- Vertical Navigation -->
<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Content Area -->
<div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
    <?php include __DIR__ . '/components/header.php'; ?>
    <main class="flex-1 px-4 sm:px-8 lg:px-12 py-6 pb-36 sm:pb-16 space-y-7 max-w-[1600px] mx-auto w-full">
    
    <!-- Top Header -->
    

    <!-- Title Bar -->
    <div class="bg-white border border-slate-200/90 rounded-[3px] p-5 sm:p-6 shadow-sm">
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight font-heading">Pro Profile Settings</h1>
        <p class="text-xs text-slate-400 font-medium mt-0.5">Customize your public portfolio page, edit academic details, and adjust hourly rates.</p>
    </div>

    <!-- Alert Message -->
    <?php if (!empty($msg)): ?>
        <div class="p-3.5 rounded-[3px] border text-xs font-bold flex items-center gap-2 shadow-2xs <?php echo $msg_type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800'; ?>">
            <span><?php echo $msg_type === 'success' ? '✓' : '⚠️'; ?></span>
            <span><?php echo htmlspecialchars($msg); ?></span>
        </div>
    <?php endif; ?>

    <!-- Main Settings Form -->
    <form method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start pb-8">
        
        <!-- Left: Forms fields (Span 8) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Section 1: Basic Contact details -->
            <div class="bg-white p-5 sm:p-6 border border-slate-200/90 rounded-[3px] shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Basic Contact Information</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="full_name" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Full Name</label>
                        <input type="text" id="full_name" name="full_name" required value="<?php echo htmlspecialchars($user_info['full_name'] ?? ''); ?>" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white text-slate-700">
                    </div>
                    
                    <div class="space-y-1">
                        <label for="email" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Email Address (Read-only)</label>
                        <input type="email" id="email" readonly value="<?php echo htmlspecialchars($user_info['email'] ?? ''); ?>" class="w-full px-3.5 py-2 bg-slate-100 border border-slate-200 rounded-[3px] text-xs font-medium text-slate-400 focus:outline-none cursor-not-allowed">
                    </div>

                    <div class="space-y-1">
                        <label for="phone_number" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Mobile Phone</label>
                        <input type="tel" id="phone_number" name="phone_number" required value="<?php echo htmlspecialchars($user_info['phone_number'] ?? ''); ?>" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white text-slate-700">
                    </div>

                    <div class="space-y-1">
                        <label for="address" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">City / Location</label>
                        <input type="text" id="address" name="address" required value="<?php echo htmlspecialchars($user_info['address'] ?? ''); ?>" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white text-slate-700">
                    </div>
                </div>
            </div>

            <!-- Section 2: Student Academic details -->
            <div class="bg-white p-5 sm:p-6 border border-slate-200/90 rounded-[3px] shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Academic & Student Verification</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="student_id" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Student ID / Matric Number</label>
                        <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($user_info['student_id'] ?? ''); ?>" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white text-slate-700">
                    </div>

                    <div class="space-y-1">
                        <label for="institution" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Institution / University</label>
                        <input type="text" id="institution" name="institution" list="institutions-list" value="<?php echo htmlspecialchars($user_info['institution'] ?? ''); ?>" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white text-slate-700">
                        <datalist id="institutions-list">
                            <option value="University of Lagos (UNILAG)">
                            <option value="Covenant University">
                            <option value="University of Nigeria, Nsukka (UNN)">
                            <option value="Federal University of Technology, Owerri (FUTO)">
                            <option value="Ahmadu Bello University (ABU)">
                            <option value="Obafemi Awolowo University (OAU)">
                        </datalist>
                    </div>

                    <div class="space-y-1">
                        <label for="faculty" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Faculty</label>
                        <input type="text" id="faculty" name="faculty" value="<?php echo htmlspecialchars($user_info['faculty'] ?? ''); ?>" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white text-slate-700">
                    </div>

                    <div class="space-y-1">
                        <label for="department" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Department</label>
                        <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($user_info['department'] ?? ''); ?>" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white text-slate-700">
                    </div>

                    <div class="space-y-1">
                        <label for="expected_completion_year" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Graduation Year</label>
                        <input type="text" id="expected_completion_year" name="expected_completion_year" value="<?php echo htmlspecialchars($user_info['expected_completion_year'] ?? ''); ?>" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white text-slate-700">
                    </div>
                </div>
            </div>

            <!-- Section 3: Portfolio & Specialization Details -->
            <div class="bg-white p-5 sm:p-6 border border-slate-200/90 rounded-[3px] shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Portfolio Specialization</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1 sm:col-span-2">
                        <label for="title" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Professional Title / Headline</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($user_info['p_title'] ?? ''); ?>" placeholder="e.g. Lead Full-Stack Engineer | Python & Django Specialist" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white text-slate-700">
                    </div>

                    <div class="space-y-1 sm:col-span-2">
                        <label for="skills" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Skill Tags (Comma separated list)</label>
                        <input type="text" id="skills" name="skills" value="<?php echo htmlspecialchars($user_info['p_skills'] ?? ''); ?>" placeholder="e.g. PHP, MySQL, Figma, WordPress, Python" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white text-slate-700">
                    </div>

                    <div class="space-y-1">
                        <label for="hourly_rate" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Desired Hourly Rate (₦)</label>
                        <input type="number" id="hourly_rate" name="hourly_rate" value="<?php echo htmlspecialchars($user_info['hourly_rate'] ?? 0); ?>" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white text-slate-700">
                    </div>
                </div>

                <!-- Professional Biography -->
                <div class="space-y-1">
                    <label for="bio" class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Professional Biography</label>
                    <textarea id="bio" name="bio" rows="6" placeholder="Provide a detailed overview of your qualifications, past projects, code quality benchmarks, and academic expertise..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white text-slate-700 placeholder-slate-400 resize-none"><?php echo htmlspecialchars($user_info['p_bio'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-3.5 bg-[#1952E1] hover:bg-blue-700 text-white rounded-[3px] font-bold text-xs transition-colors shadow-sm cursor-pointer">
                Save & Update Profile Settings
            </button>

        </div>

        <!-- Right: Verification details (Span 4) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Pro Badge Status Card -->
            <div class="bg-white p-5 border border-slate-200/90 rounded-[3px] shadow-sm text-center space-y-4">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider text-left border-b border-slate-100 pb-2">Verification Badges</h3>
                
                <div class="flex flex-col items-center gap-2 pt-2">
                    <div class="w-16 h-16 rounded-full bg-blue-50 border border-blue-200 flex items-center justify-center text-3xl shadow-xs">
                        🎓
                    </div>
                    
                    <h4 class="text-sm font-bold text-slate-900 leading-tight">Verified Service Provider</h4>
                    <span class="text-[9px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-[3px]">
                        <?php echo $is_verified_pro ? 'Active Pro Badge' : 'Pending Verification'; ?>
                    </span>
                </div>

                <p class="text-[10px] text-slate-400 leading-relaxed font-medium">
                    <?php if ($is_verified_pro): ?>
                        Your academic records and assessment results have been verified. You have full access to submit proposals to clients.
                    <?php else: ?>
                        Your profile is currently waiting in the verification queue. Take your time to fill out your details to quicken the review process.
                    <?php endif; ?>
                </p>
            </div>
            
        </div>

    </form>

</main>
</div>
<!-- Mobile Bottom Navigation -->
<?php include __DIR__ . '/components/bottom-nav.php'; ?>

<!-- Scripts -->
<?php include __DIR__ . '/components/footer.php'; ?>

<script>
// Dynamic University Fetch from Hipolabs API
document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('institutions-list');
    const input = document.getElementById('institution');

    async function loadUnis() {
        try {
            const res = await fetch('http://universities.hipolabs.com/search?country=Nigeria');
            if (!res.ok) throw new Error('API error');
            const data = await res.json();
            
            list.innerHTML = '';
            data.sort((a, b) => a.name.localeCompare(b.name));
            data.forEach(uni => {
                const opt = document.createElement('option');
                opt.value = uni.name;
                list.appendChild(opt);
            });
        } catch (e) {
            console.warn('Failed to load universities.', e);
        }
    }

    if (input) {
        input.addEventListener('focus', loadUnis, { once: true });
    }
});
</script>
