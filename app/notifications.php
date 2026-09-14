<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();
$user_id = $_SESSION['user_id'] ?? 1;

// GET Action - Mark all read
if (isset($_GET['action']) && $_GET['action'] === 'mark_all_read') {
    $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->execute([$user_id]);
    header('Location: notifications.php');
    exit;
}

// POST Action - Mark single read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_read') {
    $notif_id = (int)($_POST['id'] ?? 0);
    if ($notif_id) {
        $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        $stmt->execute([$notif_id, $user_id]);
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

// Fetch all notifications
$notif_stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$notif_stmt->execute([$user_id]);
$notifications = $notif_stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate counts
$unread_count = 0;
$milestone_count = 0;
$proposal_count = 0;
$message_count = 0;
$dispute_count = 0;

foreach ($notifications as $n) {
    if (!(int)$n['is_read']) $unread_count++;
    if ($n['type'] === 'milestone' || $n['type'] === 'escrow') $milestone_count++;
    elseif ($n['type'] === 'proposal') $proposal_count++;
    elseif ($n['type'] === 'message') $message_count++;
    elseif ($n['type'] === 'dispute') $dispute_count++;
}

$page_title = 'Notification Center';
$active_tab = 'notifications';
require_once __DIR__ . '/components/head.php'; 
?>

<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Layout Area -->
<main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-hidden relative bg-[#EFF2F7]">
    
    <?php include __DIR__ . '/components/header.php'; ?>

    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 pb-[80px] md:pb-6 lg:pb-12 scroll-smooth">
        
        <div class="max-w-5xl mx-auto space-y-5 sm:space-y-6">

            <!-- PAGE TITLE & TOP ACTION BAR -->
            <div class="bg-white p-4 sm:p-6 rounded-[3px] border border-slate-200/90 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg sm:text-2xl font-bold text-slate-900 tracking-tight">Notification Center</h1>
                        <span id="unread-counter-badge" class="bg-blue-50 text-[#1952E1] text-xs font-bold px-2.5 py-0.5 rounded-[3px] border border-blue-200">
                            <?= $unread_count ?> Unread
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Real-time alerts regarding your project bids, milestone submissions, escrow deposits, and direct messages.</p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="notifications.php?action=mark_all_read" id="mark-all-read-btn" class="text-xs font-bold text-[#1952E1] hover:text-blue-800 bg-blue-50/70 hover:bg-blue-100/70 border border-blue-200 px-3.5 py-2 rounded-[3px] transition-colors flex items-center gap-1.5 shadow-2xs">
                        <i class="ph-bold ph-check-circle text-sm"></i>
                        <span>Mark All Read</span>
                    </a>
                    <a href="settings.php" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 border border-slate-200 px-3.5 py-2 rounded-[3px] transition-colors flex items-center gap-1.5 shadow-2xs">
                        <i class="ph-bold ph-gear text-sm"></i>
                        <span class="hidden sm:inline">Preferences</span>
                    </a>
                </div>
            </div>

            <!-- CATEGORY FILTER TABS -->
            <div class="bg-white p-3.5 sm:p-4 rounded-[3px] border border-slate-200/90 shadow-sm">
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" id="notification-tabs">
                    <button class="notif-tab active px-3 py-1.5 text-xs font-bold rounded-[3px] bg-[#1952E1] text-white shadow-sm whitespace-nowrap" data-filter="all">
                        All Alerts (<?= count($notifications) ?>)
                    </button>
                    <button class="notif-tab px-3 py-1.5 text-xs font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-colors whitespace-nowrap" data-filter="unread">
                        Unread Only (<?= $unread_count ?>)
                    </button>
                    <button class="notif-tab px-3 py-1.5 text-xs font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-colors whitespace-nowrap" data-filter="milestone">
                        Milestones & Escrow (<?= $milestone_count ?>)
                    </button>
                    <button class="notif-tab px-3 py-1.5 text-xs font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-colors whitespace-nowrap" data-filter="proposal">
                        Proposals & Bids (<?= $proposal_count ?>)
                    </button>
                    <button class="notif-tab px-3 py-1.5 text-xs font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-colors whitespace-nowrap" data-filter="message">
                        Direct Messages (<?= $message_count ?>)
                    </button>
                    <button class="notif-tab px-3 py-1.5 text-xs font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-colors whitespace-nowrap" data-filter="dispute">
                        Arbitration Desk (<?= $dispute_count ?>)
                    </button>
                </div>
            </div>

            <!-- NOTIFICATIONS FEED CONTAINER -->
            <div class="space-y-4" id="notifications-feed">
                <?php if (empty($notifications)): ?>
                    <div class="bg-white p-8 text-center text-xs text-slate-400 rounded-[3px] border border-slate-200/90 shadow-sm">
                        You have no alerts at this time.
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notif): 
                        $is_unread = !(int)$notif['is_read'];
                        $n_link = htmlspecialchars($notif['link'] ?: '#');
                        $n_msg = htmlspecialchars($notif['message']);
                        $n_time = date('M d, Y H:i', strtotime($notif['created_at']));
                        $n_icon = 'ph-bold ph-bell';
                        $n_bg = 'bg-blue-50 text-[#1952E1]';
                        
                        if ($notif['type'] === 'message') {
                            $n_icon = 'ph-bold ph-chat-circle-dots';
                            $n_bg = 'bg-indigo-50 text-indigo-600';
                        } elseif ($notif['type'] === 'milestone' || $notif['type'] === 'escrow') {
                            $n_icon = 'ph-bold ph-file-arrow-up';
                            $n_bg = 'bg-amber-50 text-amber-600';
                        } elseif ($notif['type'] === 'dispute') {
                            $n_icon = 'ph-bold ph-scales';
                            $n_bg = 'bg-red-50 text-red-600';
                        }
                    ?>
                    <div class="notif-item <?= $is_unread ? 'unread bg-blue-50/20 border-2 border-blue-300' : 'read bg-white border border-slate-200/90 opacity-90' ?> p-4 sm:p-5 rounded-[3px] shadow-sm space-y-3 transition-all" data-category="<?= htmlspecialchars($notif['type'] ?? 'info') ?>" data-unread="<?= $is_unread ? 'true' : 'false' ?>" data-id="<?= $notif['id'] ?>">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-[3px] <?= $n_bg ?> flex items-center justify-center font-bold text-lg shrink-0 mt-0.5">
                                    <i class="<?= $n_icon ?> text-xl"></i>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200 px-2 py-0.5 rounded-[3px]">
                                            <?= htmlspecialchars(ucfirst($notif['type'] ?? 'Alert')) ?>
                                        </span>
                                        <span class="text-xs text-slate-400 font-medium"><?= $n_time ?></span>
                                        <?php if ($is_unread): ?>
                                            <span class="w-2 h-2 rounded-full bg-[#1952E1] notif-dot" title="Unread"></span>
                                        <?php endif; ?>
                                    </div>
                                    <h2 class="text-sm font-bold text-slate-900 leading-snug">
                                        <?= $n_msg ?>
                                    </h2>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0 self-start sm:self-auto w-full sm:w-auto pt-1 sm:pt-0">
                                <a href="<?= $n_link ?>" class="flex-1 sm:flex-none text-center text-xs font-bold bg-[#1952E1] hover:bg-blue-700 text-white px-3.5 py-2 rounded-[3px] transition-colors shadow-2xs">
                                    View Details
                                </a>
                                <?php if ($is_unread): ?>
                                    <button type="button" onclick="markItemReadServer(this, <?= $notif['id'] ?>)" class="p-2 text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 rounded-[3px] border border-slate-200" title="Mark as Read">
                                        <i class="ph-bold ph-check text-sm"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>

    </div>
</main>

<!-- Main flex container ends -->
</div> 

<script>
// Category Tab Filtering
document.querySelectorAll('.notif-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.notif-tab').forEach(t => {
            t.classList.remove('active', 'bg-[#1952E1]', 'text-white', 'shadow-sm');
            t.classList.add('bg-slate-100', 'text-slate-600');
        });
        this.classList.add('active', 'bg-[#1952E1]', 'text-white', 'shadow-sm');
        this.classList.remove('bg-slate-100', 'text-slate-600');

        const filter = this.getAttribute('data-filter');
        document.querySelectorAll('.notif-item').forEach(item => {
            if (filter === 'all') {
                item.style.display = 'block';
            } else if (filter === 'unread') {
                item.style.display = item.getAttribute('data-unread') === 'true' ? 'block' : 'none';
            } else {
                item.style.display = item.getAttribute('data-category') === filter ? 'block' : 'none';
            }
        });
    });
});

// Mark Single Item as Read on Server
function markItemReadServer(btn, notifId) {
    const formData = new FormData();
    formData.append('action', 'mark_read');
    formData.append('id', notifId);
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const item = btn.closest('.notif-item');
            if (item) {
                item.setAttribute('data-unread', 'false');
                item.classList.remove('unread', 'border-2', 'border-blue-300', 'bg-blue-50/20');
                item.classList.add('read', 'border-slate-200/90', 'opacity-90');
                const dot = item.querySelector('.notif-dot');
                if (dot) dot.remove();
                btn.remove();
                updateUnreadBadge();
            }
        }
    });
}

// Mark All as Read
function markAllAsRead() {
    document.querySelectorAll('.notif-item[data-unread="true"]').forEach(item => {
        item.setAttribute('data-unread', 'false');
        item.classList.remove('unread', 'border-2', 'border-amber-400', 'border-blue-300', 'bg-blue-50/20');
        item.classList.add('read', 'border-slate-200/90', 'opacity-90');
        const dot = item.querySelector('.notif-dot');
        if (dot) dot.remove();
        const checkBtn = item.querySelector('button[title="Mark as Read"]');
        if (checkBtn) checkBtn.remove();
    });
    updateUnreadBadge();
}

function updateUnreadBadge() {
    const unreadCount = document.querySelectorAll('.notif-item[data-unread="true"]').length;
    const badge = document.getElementById('unread-counter-badge');
    if (badge) {
        if (unreadCount > 0) {
            badge.textContent = unreadCount + ' Unread';
        } else {
            badge.textContent = 'All Caught Up';
            badge.className = 'bg-emerald-50 text-emerald-700 text-xs font-bold px-2.5 py-0.5 rounded-[3px] border border-emerald-200';
        }
    }
}
</script>

<?php include __DIR__ . '/components/footer.php'; ?>


