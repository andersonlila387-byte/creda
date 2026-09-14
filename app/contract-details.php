<?php 
/**
 * Scriptly Escrow - Client Contract / Order Details
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();

$client_id = $_SESSION['user_id'] ?? null;
if (!$client_id) {
    header("Location: ../login.php");
    exit;
}

$contract_id = $_GET['id'] ?? null;
if (!$contract_id) {
    die("Contract ID required.");
}

// Fetch Contract Details
$sql = "
    SELECT c.*, p.title as package_title, 
           u.full_name as provider_name, u.avatar_url as provider_avatar,
           (SELECT COUNT(*) FROM package_requirements WHERE package_id = c.package_id) as total_reqs
    FROM contracts c
    JOIN packages p ON c.package_id = p.id
    JOIN users u ON c.provider_id = u.id
    WHERE c.id = ? AND c.client_id = ?
";
$stmt = $db->prepare($sql);
$stmt->execute([$contract_id, $client_id]);
$contract = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contract) {
    die("Contract not found or access denied.");
}

$status = $contract['status'];

// Handle Requirements Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $status === 'awaiting_requirements') {
    $db->beginTransaction();
    try {
        $req_stmt = $db->prepare("SELECT id, response_type FROM package_requirements WHERE package_id = ?");
        $req_stmt->execute([$contract['package_id']]);
        $requirements = $req_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $ans_stmt = $db->prepare("INSERT INTO contract_requirements_answers (contract_id, requirement_id, answer_text, file_path) VALUES (?, ?, ?, ?)");
        
        foreach ($requirements as $req) {
            $rid = $req['id'];
            $ans_text = $_POST['req_' . $rid] ?? '';
            $file_path = null;
            // Simplified file upload logic for MVP
            if ($req['response_type'] === 'file' && isset($_FILES['file_' . $rid]) && $_FILES['file_' . $rid]['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['file_' . $rid]['name'], PATHINFO_EXTENSION);
                $name = md5(time().$rid) . '.' . $ext;
                $dest = __DIR__ . '/../assets/uploads/requirements/' . $name;
                @mkdir(dirname($dest), 0777, true);
                if (move_uploaded_file($_FILES['file_' . $rid]['tmp_name'], $dest)) {
                    $file_path = '/assets/uploads/requirements/' . $name;
                }
            }
            $ans_stmt->execute([$contract_id, $rid, $ans_text, $file_path]);
        }
        
        // Update contract status
        $db->prepare("UPDATE contracts SET status = 'requirements_submitted', updated_at = NOW() WHERE id = ?")
           ->execute([$contract_id]);
        
        $db->commit();
        header("Location: contract-details.php?id=" . $contract_id . "&success=requirements_submitted");
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Failed to submit requirements: " . $e->getMessage();
    }
}

// Fetch requirements to display if awaiting
if ($status === 'awaiting_requirements') {
    $req_stmt = $db->prepare("SELECT * FROM package_requirements WHERE package_id = ? ORDER BY id ASC");
    $req_stmt->execute([$contract['package_id']]);
    $package_reqs = $req_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/components/head.php'; ?>
    <title><?php echo htmlspecialchars($contract['title']); ?> - Scriptly Order</title>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col md:flex-row">

    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <?php include __DIR__ . '/components/bottom-nav.php'; ?>

    <main class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include __DIR__ . '/components/header.php'; ?>

        <div class="p-4 md:p-8 pt-20 pb-[80px] md:pt-8 md:pb-8 max-w-5xl mx-auto w-full">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 pb-6 border-b border-slate-200">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Order #<?php echo str_pad($contract['id'], 6, '0', STR_PAD_LEFT); ?></span>
                        
                        <?php if($status === 'awaiting_requirements'): ?>
                            <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-[3px]">Awaiting Requirements</span>
                        <?php elseif($status === 'requirements_submitted'): ?>
                            <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded-[3px]">Awaiting Provider Start</span>
                        <?php elseif($status === 'active'): ?>
                            <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded-[3px] animate-pulse">In Progress</span>
                        <?php endif; ?>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 leading-tight"><?php echo htmlspecialchars($contract['title']); ?></h1>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wide">Total Amount Locked</div>
                    <div class="text-2xl font-black text-slate-900">₦<?php echo number_format($contract['total_amount']); ?></div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Left Column -->
                <div class="flex-1 space-y-6">
                    
                    <?php if (isset($error)): ?>
                        <div class="bg-red-50 text-red-600 p-4 rounded-[3px] border border-red-200 text-sm font-bold">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['success']) && $_GET['success'] === 'checkout'): ?>
                        <div class="bg-emerald-50 text-emerald-700 p-4 rounded-[3px] border border-emerald-200 shadow-sm flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <div>
                                <h4 class="text-sm font-bold">Payment Secured in Escrow</h4>
                                <p class="text-xs font-medium text-emerald-600 mt-1">Your funds are safe. Please submit the requirements below so the provider can start working.</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Requirements Submission Form -->
                    <?php if ($status === 'awaiting_requirements'): ?>
                        <div class="bg-white border border-slate-200 rounded-[3px] p-6 shadow-sm">
                            <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider mb-2">Submit Requirements</h2>
                            <p class="text-sm text-slate-500 mb-6">The provider needs the following information to start your order. The countdown timer will begin once the provider accepts your requirements.</p>

                            <?php if(empty($package_reqs)): ?>
                                <!-- Provider didn't specify requirements, just show a general button -->
                                <form method="POST">
                                    <p class="text-sm text-slate-700 font-medium mb-6">The provider didn't specify any strict requirements, but you can click below to notify them you're ready.</p>
                                    <button type="submit" class="px-6 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-sm rounded-[3px] shadow-sm transition-colors">Start Order</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                                    <?php foreach($package_reqs as $index => $req): ?>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-900 mb-2">
                                            <?php echo ($index+1).". ".htmlspecialchars($req['requirement_text']); ?>
                                            <?php if($req['is_mandatory']): ?><span class="text-red-500">*</span><?php endif; ?>
                                        </label>
                                        
                                        <?php if($req['response_type'] === 'text'): ?>
                                            <textarea name="req_<?php echo $req['id']; ?>" rows="3" class="w-full bg-white border border-slate-300 rounded-[3px] px-4 py-2 text-sm focus:border-[#1952E1] focus:ring-1 focus:ring-[#1952E1] outline-none transition-shadow" <?php echo $req['is_mandatory'] ? 'required' : ''; ?> placeholder="Type your answer here..."></textarea>
                                        <?php elseif($req['response_type'] === 'file'): ?>
                                            <input type="file" name="file_<?php echo $req['id']; ?>" class="w-full bg-white border border-slate-300 rounded-[3px] px-4 py-2 text-sm file:mr-4 file:py-1 file:px-3 file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-[#1952E1] file:rounded-[3px] hover:file:bg-blue-100 transition-colors" <?php echo $req['is_mandatory'] ? 'required' : ''; ?>>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                    
                                    <div class="pt-4 border-t border-slate-100">
                                        <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-[#1952E1] hover:bg-blue-700 text-white font-black text-sm rounded-[3px] shadow-md transition-colors">Submit Requirements & Start</button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>

                    <!-- Post-Requirements / Active View -->
                    <?php elseif (in_array($status, ['requirements_submitted', 'active', 'completed'])): ?>
                        
                        <?php if ($status === 'requirements_submitted'): ?>
                        <div class="bg-blue-50 border border-blue-200 rounded-[3px] p-8 text-center shadow-sm">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                <svg class="w-8 h-8 text-[#1952E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h2 class="text-lg font-black text-slate-900 mb-2">Requirements Submitted</h2>
                            <p class="text-sm text-slate-600 max-w-md mx-auto">We've notified the provider. The escrow countdown timer will begin as soon as they acknowledge the requirements and click "Start Project".</p>
                        </div>
                        <?php endif; ?>

                        <?php if ($status === 'active'): ?>
                        <!-- THE ESCROW COUNTDOWN TIMER -->
                        <div class="bg-slate-900 border border-slate-800 rounded-[3px] p-8 text-center shadow-lg relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-1 bg-[#1952E1]"></div>
                            <h2 class="text-sm font-extrabold text-slate-400 uppercase tracking-widest mb-6">Time Left to Deliver</h2>
                            
                            <div class="flex items-center justify-center gap-4 sm:gap-8 font-mono">
                                <div class="flex flex-col items-center">
                                    <div class="text-4xl sm:text-5xl font-black text-white" id="cd-days">00</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-2">Days</div>
                                </div>
                                <div class="text-3xl text-slate-600 font-black pb-6">:</div>
                                <div class="flex flex-col items-center">
                                    <div class="text-4xl sm:text-5xl font-black text-white" id="cd-hours">00</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-2">Hours</div>
                                </div>
                                <div class="text-3xl text-slate-600 font-black pb-6">:</div>
                                <div class="flex flex-col items-center">
                                    <div class="text-4xl sm:text-5xl font-black text-white" id="cd-mins">00</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-2">Mins</div>
                                </div>
                                <div class="text-3xl text-slate-600 font-black pb-6 hidden sm:block">:</div>
                                <div class="flex flex-col items-center hidden sm:flex">
                                    <div class="text-4xl sm:text-5xl font-black text-[#1952E1]" id="cd-secs">00</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-2">Secs</div>
                                </div>
                            </div>
                            
                            <div class="mt-8 text-sm text-slate-300 font-medium bg-slate-800/50 inline-block px-4 py-2 rounded-full">
                                Delivery Expected by: <span class="font-bold text-white"><?php echo date('F j, Y, g:i a', strtotime($contract['deadline_at'])); ?></span>
                            </div>

                            <script>
                                // Timer Logic
                                const deadline = new Date("<?php echo date('Y-m-d\TH:i:s', strtotime($contract['deadline_at'])); ?>").getTime();
                                const timer = setInterval(function() {
                                    const now = new Date().getTime();
                                    const t = deadline - now;
                                    
                                    if (t < 0) {
                                        clearInterval(timer);
                                        document.getElementById("cd-days").innerHTML = "00";
                                        document.getElementById("cd-hours").innerHTML = "00";
                                        document.getElementById("cd-mins").innerHTML = "00";
                                        if(document.getElementById("cd-secs")) document.getElementById("cd-secs").innerHTML = "00";
                                        return;
                                    }
                                    
                                    const days = Math.floor(t / (1000 * 60 * 60 * 24));
                                    const hours = Math.floor((t % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                    const mins = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
                                    const secs = Math.floor((t % (1000 * 60)) / 1000);
                                    
                                    document.getElementById("cd-days").innerHTML = days < 10 ? '0'+days : days;
                                    document.getElementById("cd-hours").innerHTML = hours < 10 ? '0'+hours : hours;
                                    document.getElementById("cd-mins").innerHTML = mins < 10 ? '0'+mins : mins;
                                    if(document.getElementById("cd-secs")) document.getElementById("cd-secs").innerHTML = secs < 10 ? '0'+secs : secs;
                                }, 1000);
                            </script>
                        </div>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>

                <!-- Right Column (Sidebar) -->
                <div class="w-full lg:w-80 shrink-0 space-y-6">
                    <!-- Provider Info -->
                    <div class="bg-white border border-slate-200 rounded-[3px] p-6 shadow-sm flex flex-col items-center text-center">
                        <img src="<?php echo $contract['provider_avatar'] ?: '../assets/images/default-avatar.png'; ?>" class="w-20 h-20 rounded-full object-cover mb-4">
                        <h3 class="text-sm font-black text-slate-900"><?php echo htmlspecialchars($contract['provider_name']); ?></h3>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-4">Provider</p>
                        <a href="messages.php" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-[3px] transition-colors shadow-2xs border border-slate-200">Message Provider</a>
                    </div>
                    
                    <!-- Contract Metadata -->
                    <div class="bg-white border border-slate-200 rounded-[3px] p-5 shadow-sm space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Order Date</span>
                            <span class="font-semibold text-slate-900"><?php echo date('M j, Y', strtotime($contract['created_at'])); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Escrow ID</span>
                            <span class="font-mono font-bold text-slate-700">ESC-<?php echo str_pad($contract['id'], 6, '0', STR_PAD_LEFT); ?></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        
        <?php include __DIR__ . '/components/footer.php'; ?>
    </main>
</body>
</html>


