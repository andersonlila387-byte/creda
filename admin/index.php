<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/brand.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';

// Verify Admin Session
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';

try {
    $db = getDBConnection();
    
    // Process Actions (Approve/Reject)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $target_user_id = intval($_POST['user_id'] ?? 0);
        $action = $_POST['action'];
        
        // Fetch target user email and name first
        $u_stmt = $db->prepare("SELECT email, full_name FROM users WHERE id = ? LIMIT 1");
        $u_stmt->execute([$target_user_id]);
        $tgt_user = $u_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tgt_user) {
            if ($action === 'approve') {
                // Update user verification status
                $up = $db->prepare("UPDATE users SET primary_role = 'provider', is_verified_pro = 1, verification_status = 'approved', verification_rejected_reason = NULL WHERE id = ?");
                $up->execute([$target_user_id]);
                
                // Initialize default talent profile if not exists
                $prof_stmt = $db->prepare("SELECT id FROM talent_profiles WHERE user_id = ? LIMIT 1");
                $prof_stmt->execute([$target_user_id]);
                if ($prof_stmt->rowCount() == 0) {
                    $ins_prof = $db->prepare("INSERT INTO talent_profiles (user_id, hourly_rate, title, location) VALUES (?, 2500, 'Medical Service Provider', 'Nigeria')");
                    $ins_prof->execute([$target_user_id]);
                }
                
                // Send Approved Email
                require_once __DIR__ . '/../includes/emails/verification_result_email.php';
                sendVerificationApprovedEmail($tgt_user['email'], $tgt_user['full_name']);
                
                header('Location: index.php?msg=approved');
                exit;
                
            } elseif ($action === 'reject') {
                $reason = trim($_POST['reason'] ?? 'Uploaded identity documents are blurry or incomplete.');
                if (empty($reason)) {
                    $reason = 'Uploaded identity documents are blurry or incomplete.';
                }
                
                // Update status to rejected
                $up = $db->prepare("UPDATE users SET is_verified_pro = 0, verification_status = 'rejected', verification_rejected_reason = ? WHERE id = ?");
                $up->execute([$reason, $target_user_id]);
                
                // Send Rejected Email
                require_once __DIR__ . '/../includes/emails/verification_result_email.php';
                sendVerificationRejectedEmail($tgt_user['email'], $tgt_user['full_name'], $reason);
                
                header('Location: index.php?msg=rejected');
                exit;
            }
        }
    }
    
    // Fetch Pending Verifications
    $stmt = $db->query("SELECT id, full_name, email, phone_number, nin, id_card_url, selfie_url, liveness_video_url, registration_ip, last_login_ip, created_at FROM users WHERE verification_status = 'pending' ORDER BY created_at ASC");
    $pending_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $error_msg = "Database error: " . $e->getMessage();
}

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderation Console — Cliniconnect Admin</title>
    <meta name="robots" content="noindex, nofollow">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://api.fontshare.com/v2/css?f[]=clash-display@700,600,500,400&f[]=satoshi@900,700,500,400&display=swap" rel="stylesheet">
    
    <!-- Local CSS -->
    <link rel="stylesheet" href="../assets/css/tailwind.min.css">
    
    <style>
        body {
            font-family: 'Satoshi', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="min-h-screen antialiased">

    <!-- Header Navigation -->
    <header class="bg-[#0A2342] text-white py-4 px-6 sm:px-8 flex justify-between items-center shadow-md">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-600 text-white rounded-[3px] flex items-center justify-center font-bold text-base">
                🛡️
            </div>
            <div>
                <h1 class="text-sm font-black tracking-tight leading-none">Cliniconnect</h1>
                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Security & Moderation Panel</span>
            </div>
        </div>
        <div class="flex items-center gap-4 text-xs">
            <span class="text-slate-300 font-medium">Log in as: <strong class="text-white"><?php echo htmlspecialchars($admin_username); ?></strong></span>
            <a href="logout.php" class="bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded-[3px] font-bold transition-colors">Log Out</a>
        </div>
    </header>

    <!-- Main Workspace -->
    <main class="max-w-7xl mx-auto p-6 sm:p-8 space-y-6">
        
        <!-- Welcome banner / stats -->
        <div class="bg-white border border-slate-200/90 rounded-[3px] p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-lg font-black text-slate-900 tracking-tight">Identity Verification Queue</h2>
                <p class="text-xs text-slate-500 mt-0.5">Inspect physical credentials, verification selfies, and head-movement video clips below.</p>
            </div>
            <div class="bg-[#EFF2F7] border border-slate-200/60 px-4 py-2 rounded-[3px] text-xs font-bold text-slate-700 flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-blue-600 rounded-full animate-pulse"></span>
                <span>Pending Review Count: <?php echo count($pending_list ?? []); ?></span>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($msg === 'approved'): ?>
            <div class="bg-green-50 border border-green-200 text-green-800 text-xs p-4 rounded-[3px] font-bold">
                ✓ Provider account approved successfully. Verification email sent.
            </div>
        <?php elseif ($msg === 'rejected'): ?>
            <div class="bg-amber-50 border border-amber-200 text-amber-800 text-xs p-4 rounded-[3px] font-bold">
                ❌ Verification request rejected. Rejection reason and instruction email sent.
            </div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs p-4 rounded-[3px] font-bold">
                ⚠️ <?php echo htmlspecialchars($error_msg); ?>
            </div>
        <?php endif; ?>

        <!-- Queue Table -->
        <div class="bg-white border border-slate-200/90 rounded-[3px] shadow-sm overflow-hidden">
            <?php if (empty($pending_list)): ?>
                <div class="p-12 text-center text-slate-400 text-xs font-medium space-y-2">
                    <span class="text-3xl block">🎉</span>
                    <span class="font-bold text-slate-600 block">Queue is Empty!</span>
                    <span>All service providers are verified or approved. Check back later.</span>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-wider">
                                <th class="p-4 w-64">Provider Info</th>
                                <th class="p-4 w-60">Credentials (NIN & Documents)</th>
                                <th class="p-4 w-72">Biometric Liveness Check</th>
                                <th class="p-4 w-60 text-right">Moderation Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($pending_list as $row): 
                                $id_url = getPortalUrl('client', $row['id_card_url']);
                                $selfie_url = getPortalUrl('client', $row['selfie_url']);
                                $video_url = getPortalUrl('client', $row['liveness_video_url']);
                            ?>
                                <tr class="align-top hover:bg-slate-50/50 transition-colors">
                                    
                                    <!-- Provider Info -->
                                    <td class="p-4 space-y-2">
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm"><?php echo htmlspecialchars($row['full_name']); ?></h4>
                                            <span class="text-slate-400 font-medium"><?php echo htmlspecialchars($row['email']); ?></span>
                                        </div>
                                        <div class="text-[10px] font-semibold text-slate-500 space-y-0.5">
                                            <p>📞 Phone: <?php echo htmlspecialchars($row['phone_number'] ?? 'N/A'); ?></p>
                                            <p>📍 Reg IP: <span class="font-mono text-slate-700 bg-slate-100 px-1 rounded-sm"><?php echo htmlspecialchars($row['registration_ip'] ?? 'N/A'); ?></span></p>
                                            <p>📍 Login IP: <span class="font-mono text-slate-700 bg-slate-100 px-1 rounded-sm"><?php echo htmlspecialchars($row['last_login_ip'] ?? 'N/A'); ?></span></p>
                                            <p>📅 Date: <?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></p>
                                        </div>
                                    </td>

                                    <!-- Credentials (NIN & Documents) -->
                                    <td class="p-4 space-y-3">
                                        <div class="bg-blue-50 border border-blue-200 text-blue-900 px-3 py-1.5 rounded-[3px] inline-block font-mono font-bold text-xs">
                                            NIN: <?php echo htmlspecialchars($row['nin']); ?>
                                        </div>
                                        
                                        <!-- Document Thumbnails -->
                                        <div class="grid grid-cols-2 gap-2">
                                            <a href="<?php echo $id_url; ?>" target="_blank" class="block border border-slate-200 rounded-[3px] overflow-hidden bg-slate-100 hover:opacity-85 transition-opacity" title="Open ID Document">
                                                <div class="h-20 flex items-center justify-center relative">
                                                    <?php if (pathinfo($row['id_card_url'], PATHINFO_EXTENSION) === 'pdf'): ?>
                                                        <span class="text-2xl">📄</span>
                                                    <?php else: ?>
                                                        <img src="<?php echo $id_url; ?>" class="w-full h-full object-cover">
                                                    <?php endif; ?>
                                                </div>
                                                <span class="block text-center text-[9px] bg-slate-50 border-t border-slate-100 py-1 font-bold text-slate-600">ID Scan ↗</span>
                                            </a>

                                            <a href="<?php echo $selfie_url; ?>" target="_blank" class="block border border-slate-200 rounded-[3px] overflow-hidden bg-slate-100 hover:opacity-85 transition-opacity" title="Open Selfie">
                                                <div class="h-20 flex items-center justify-center relative">
                                                    <img src="<?php echo $selfie_url; ?>" class="w-full h-full object-cover">
                                                </div>
                                                <span class="block text-center text-[9px] bg-slate-50 border-t border-slate-100 py-1 font-bold text-slate-600">Selfie holding ID ↗</span>
                                            </a>
                                        </div>
                                    </td>

                                    <!-- Biometric Liveness Video -->
                                    <td class="p-4">
                                        <div class="w-full max-w-xs border border-slate-200 rounded-[3px] overflow-hidden bg-slate-900 aspect-video shadow-2xs">
                                            <video src="<?php echo $video_url; ?>" controls playsinline class="w-full h-full object-contain"></video>
                                        </div>
                                        <span class="block text-[10px] text-slate-400 font-medium mt-1">Play to verify head/face movements (turning left, right, nod).</span>
                                    </td>

                                    <!-- Moderation Actions -->
                                    <td class="p-4">
                                        <div class="space-y-4 text-right">
                                            
                                            <!-- Approve Form -->
                                            <form method="POST" class="inline-block">
                                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" onclick="return confirm('Are you sure you want to approve this provider?')" class="bg-green-600 hover:bg-green-700 text-white font-black px-4 py-2 rounded-[3px] transition-colors shadow-2xs">
                                                    ✓ Approve Pro Profile
                                                </button>
                                            </form>
                                            
                                            <hr class="border-slate-100">

                                            <!-- Reject Form -->
                                            <form method="POST" class="space-y-1.5 text-left border border-rose-100 bg-rose-50/30 p-2.5 rounded-[3px]">
                                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="action" value="reject">
                                                
                                                <label class="block text-[9px] font-bold text-rose-800 uppercase tracking-wider">Reason for Rejection</label>
                                                <textarea name="reason" placeholder="Explain why..." required class="w-full p-2 bg-white border border-rose-200 rounded-[3px] text-[11px] font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-rose-400 transition-colors h-14 resize-none"></textarea>
                                                
                                                <button type="submit" onclick="return confirm('Reject verification for this user?')" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-extrabold px-3 py-1.5 rounded-[3px] text-center text-[10px] uppercase transition-colors shadow-2xs">
                                                    🛑 Reject Submission
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </main>

</body>
</html>
