<?php
// Determine Breadcrumb Hierarchy
$tab_names = [
    'dashboard' => ['name' => 'Dashboard', 'url' => route_url('index.php')],
    'projects' => ['name' => 'Projects & Contracts', 'url' => route_url('my-projects.php')],
    'post-project' => ['name' => 'Post Project', 'url' => route_url('post-project.php')],
    'talent' => ['name' => 'Find Talent', 'url' => route_url('talent.php')],
    'wallet' => ['name' => 'Escrow Wallet', 'url' => route_url('wallet.php')],
    'messages' => ['name' => 'Direct Messages', 'url' => route_url('messages.php')],
    'settings' => ['name' => 'Settings', 'url' => route_url('settings.php')]
];
$current_tab_info = $tab_names[$active_tab ?? 'dashboard'] ?? ['name' => 'Workspace', 'url' => route_url('index.php')];

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($db)) {
    require_once __DIR__ . '/../../config/database.php';
    $db = getDBConnection();
}
$user_id = $_SESSION['user_id'] ?? 1;

// Get dynamic count of unread messages
$msg_count_stmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$msg_count_stmt->execute([$user_id]);
$hdr_unread_msgs = (int)$msg_count_stmt->fetchColumn();

// Get dynamic notifications list and count
$notif_count_stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$notif_count_stmt->execute([$user_id]);
$hdr_unread_notifs = (int)$notif_count_stmt->fetchColumn();

$notif_list_stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$notif_list_stmt->execute([$user_id]);
$header_notifications = $notif_list_stmt->fetchAll(PDO::FETCH_ASSOC);

// Resolve username / initials for dropdown
$u_stmt = $db->prepare("SELECT full_name, email FROM users WHERE id = ?");
$u_stmt->execute([$user_id]);
$u_row = $u_stmt->fetch(PDO::FETCH_ASSOC);
$user_name = $u_row['full_name'] ?? 'User';
$user_email = $u_row['email'] ?? '';

$parts = explode(' ', $user_name);
$initials = '';
foreach ($parts as $p) {
    if (!empty($p)) $initials .= strtoupper($p[0]);
}
$initials = substr($initials, 0, 2);
?>
<!-- Sticky Top Header (Clean Minimal Style) -->
<header id="top-header" class="sticky top-0 z-20 bg-[#EFF2F7]/95 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center shrink-0 min-h-[60px]">
    
    <!-- Left: Dynamic Clickable Workspace Breadcrumb -->
    <nav class="flex items-center gap-1.5 sm:gap-2 text-xs font-bold" aria-label="Breadcrumb">
        <a href="<?= route_url('index.php') ?>" class="text-slate-400 hover:text-slate-700 transition-colors uppercase tracking-wider hidden sm:inline-flex items-center gap-1">
            <i class="ph-bold ph-house text-sm"></i>
            <span>Workspace</span>
        </a>
        <span class="text-slate-300 hidden sm:inline">/</span>
        
        <?php if (($active_tab ?? 'dashboard') !== 'dashboard'): ?>
            <a href="<?= htmlspecialchars($current_tab_info['url']) ?>" class="text-slate-500 hover:text-slate-800 transition-colors hidden md:inline truncate max-w-[140px]">
                <?= htmlspecialchars($current_tab_info['name']) ?>
            </a>
            <span class="text-slate-300 hidden md:inline">/</span>
        <?php endif; ?>

        <h1 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-1.5 truncate max-w-[220px] sm:max-w-xs md:max-w-md">
            <span class="truncate"><?= htmlspecialchars($page_title ?? 'Overview') ?></span>
            <span class="w-1.5 h-1.5 rounded-full bg-[#1952E1] shrink-0"></span>
        </h1>
    </nav>

    <!-- Right: Action Icons & Profile Dropdown -->
    <div class="flex items-center gap-2.5 sm:gap-3">

        <!-- Messages Button -->
        <div class="relative">
            <a href="<?= route_url('messages.php') ?>" class="w-9 h-9 sm:w-10 sm:h-10 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-full flex items-center justify-center text-slate-700 hover:text-[#1952E1] transition-colors relative shadow-sm" title="Direct Messages">
                <i class="ph-bold ph-chat-circle-dots text-lg"></i>
                <?php if ($hdr_unread_msgs > 0): ?>
                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-amber-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center ring-2 ring-white"><?= $hdr_unread_msgs ?></span>
                <?php endif; ?>
            </a>
        </div>

        <!-- Notification Dropdown -->
        <div class="relative">
            <button id="notify-btn" class="w-9 h-9 sm:w-10 sm:h-10 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-full flex items-center justify-center text-slate-700 hover:text-[#1952E1] transition-colors relative shadow-sm" title="Notifications">
                <i class="ph-bold ph-bell text-lg"></i>
                <?php if ($hdr_unread_notifs > 0): ?>
                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-[#1952E1] text-white text-[9px] font-bold rounded-full flex items-center justify-center ring-2 ring-white"><?= $hdr_unread_notifs ?></span>
                <?php endif; ?>
            </button>
            
            <!-- Dropdown Menu -->
            <div id="notify-dropdown" class="hidden absolute right-0 mt-2 w-72 sm:w-80 bg-white border border-slate-200/90 rounded-[3px] shadow-lg z-50 overflow-hidden">
                <div class="p-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                    <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700">Notifications</h3>
                    <?php if ($hdr_unread_notifs > 0): ?>
                        <span class="bg-[#1952E1] text-white text-[10px] font-bold px-2 py-0.5 rounded-full"><?= $hdr_unread_notifs ?> new</span>
                    <?php endif; ?>
                </div>
                <div class="max-h-64 overflow-y-auto divide-y divide-slate-100">
                    <?php if (empty($header_notifications)): ?>
                        <div class="p-4 text-center text-slate-400 text-xs">No notifications yet.</div>
                    <?php else: ?>
                        <?php foreach($header_notifications as $hn): ?>
                        <a href="<?= route_url('notifications.php') ?>" class="block p-3 hover:bg-slate-50 transition-colors">
                            <p class="text-xs font-semibold text-slate-800 line-clamp-2"><?= htmlspecialchars($hn['title'] ?? 'Notification') ?></p>
                            <span class="text-[10px] text-slate-400 mt-1 block"><?= date('M j, g:i a', strtotime($hn['created_at'])) ?></span>
                        </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <a href="<?= route_url('notifications.php') ?>" class="block p-2.5 text-center text-xs font-semibold text-[#1952E1] hover:bg-slate-50 border-t border-slate-100 transition-colors">
                    View all notifications →
                </a>
            </div>
        </div>

        <!-- Profile Dropdown -->
        <div class="relative">
            <button id="profile-btn" class="flex items-center gap-2 p-1 rounded-full hover:bg-slate-200/50 transition-colors focus:outline-none">
                <div class="w-9 h-9 rounded-full bg-[#0A2342] text-white flex items-center justify-center font-bold text-xs ring-2 ring-white shadow-sm">
                    <?= htmlspecialchars($initials ?? 'CR'); ?>
                </div>
                <div class="hidden md:flex flex-col text-left pr-1">
                    <span class="text-xs font-bold text-slate-900 leading-tight"><?= htmlspecialchars($user_name ?? 'Client'); ?></span>
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide">Client</span>
                </div>
                <i class="ph-bold ph-caret-down text-xs text-slate-400 hidden sm:block"></i>
            </button>
            
            <!-- Dropdown Menu -->
            <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-52 bg-white border border-slate-200/90 rounded-[3px] shadow-lg z-50 overflow-hidden">
                <div class="p-3 border-b border-slate-100 bg-slate-50">
                    <p class="font-bold text-xs text-slate-900 truncate"><?= htmlspecialchars($user_name ?? 'Client Account'); ?></p>
                    <p class="text-[11px] text-slate-500 font-medium truncate mt-0.5"><?= htmlspecialchars($user_email ?? ''); ?></p>
                </div>
                <div class="p-1 space-y-0.5">
                    <a href="<?= route_url('settings.php') ?>" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-[3px] transition-colors">
                        <i class="ph-bold ph-user text-sm text-slate-400"></i> My Profile
                    </a>
                    <a href="<?= route_url('wallet.php') ?>" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-[3px] transition-colors">
                        <i class="ph-bold ph-wallet text-sm text-slate-400"></i> Wallet & Escrow
                    </a>
                    <a href="<?= route_url('my-projects.php') ?>" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-[3px] transition-colors">
                        <i class="ph-bold ph-folder-simple text-sm text-slate-400"></i> My Projects
                    </a>
                    <a href="<?= route_url('switch-role.php', ['role' => 'provider']) ?>" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-[#1952E1] hover:bg-blue-50 rounded-[3px] transition-colors">
                        <i class="ph-bold ph-arrows-left-right text-sm text-[#1952E1]"></i> Switch to Provider
                    </a>
                </div>
                <div class="border-t border-slate-100 p-1">
                    <a href="<?= route_url('logout.php') ?>" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 rounded-[3px] transition-colors">
                        <i class="ph-bold ph-sign-out text-sm"></i> Logout
                    </a>
                </div>
            </div>
        </div>

    </div>
</header>


