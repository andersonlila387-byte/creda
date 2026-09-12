<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();

$user_id = $_SESSION['user_id'] ?? 1;

// Resolve Logged-in User Info
$me_stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$me_stmt->execute([$user_id]);
$me_info = $me_stmt->fetch(PDO::FETCH_ASSOC);

// Handle AJAX POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    
    if ($action === 'send_message') {
        $rid = (int)($_POST['receiver_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        if ($rid && !empty($content)) {
            $stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, content, is_read) VALUES (?, ?, ?, 0)");
            $stmt->execute([$user_id, $rid, $content]);
            echo json_encode(['success' => true, 'message_id' => $db->lastInsertId()]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Missing fields']);
        }
        exit;
    }
    
    if ($action === 'send_offer') {
        $rid = (int)($_POST['receiver_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $amount = (float)($_POST['amount'] ?? 0);
        $days = (int)($_POST['days'] ?? 0);
        
        if ($rid && !empty($title) && $amount > 0 && $days > 0) {
            $db->beginTransaction();
            try {
                // Insert into custom_offers
                $stmt = $db->prepare("INSERT INTO custom_offers (sender_id, receiver_id, title, amount, delivery_days, status) VALUES (?, ?, ?, ?, ?, 'pending')");
                $stmt->execute([$user_id, $rid, $title, $amount, $days]);
                $offer_id = $db->lastInsertId();
                
                // Embed custom offer inside the chat window
                $offer_marker = "[CUSTOM_OFFER:" . $offer_id . "]";
                $msg_stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, content, is_read) VALUES (?, ?, ?, 0)");
                $msg_stmt->execute([$user_id, $rid, $offer_marker]);
                
                $db->commit();
                echo json_encode(['success' => true, 'offer_id' => $offer_id]);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid offer inputs']);
        }
        exit;
    }
    
    if ($action === 'accept_offer') {
        $offer_id = (int)($_POST['offer_id'] ?? 0);
        if ($offer_id) {
            $db->beginTransaction();
            try {
                // Load the offer
                $stmt = $db->prepare("SELECT * FROM custom_offers WHERE id = ? FOR UPDATE");
                $stmt->execute([$offer_id]);
                $offer = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$offer || $offer['status'] !== 'pending') {
                    throw new Exception('Offer is no longer active or pending.');
                }
                
                // Check balance
                if ($me_info['balance'] < $offer['amount']) {
                    echo json_encode(['success' => false, 'error' => 'insufficient_funds', 'required' => $offer['amount'] - $me_info['balance']]);
                    $db->rollBack();
                    exit;
                }
                
                // Deduct from client balance
                $up_stmt = $db->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
                $up_stmt->execute([$offer['amount'], $user_id]);
                
                // Create project listing
                $p_stmt = $db->prepare("INSERT INTO projects (client_id, category, title, description, budget, status, experience_tier) VALUES (?, 'Custom Job', ?, ?, ?, 'in_progress', 'Intermediate')");
                $p_stmt->execute([$user_id, $offer['title'], 'Accepted Custom Offer: ' . $offer['title'], $offer['amount']]);
                $project_id = $db->lastInsertId();
                
                // Create proposal (accepted)
                $pr_stmt = $db->prepare("INSERT INTO proposals (project_id, provider_id, amount, duration_days, cover_letter, status) VALUES (?, ?, ?, ?, 'Custom offer accepted.', 'accepted')");
                $pr_stmt->execute([$project_id, $offer['sender_id'], $offer['amount'], $offer['delivery_days']]);
                
                // Create milestone
                $m_stmt = $db->prepare("INSERT INTO milestones (project_id, provider_id, title, amount, status) VALUES (?, ?, ?, ?, 'pending')");
                $m_stmt->execute([$project_id, $offer['sender_id'], 'Milestone Escrow: ' . $offer['title'], $offer['amount']]);
                
                // Lock in escrow transaction log
                $t_stmt = $db->prepare("INSERT INTO transactions (user_id, amount, type, description) VALUES (?, ?, 'escrow_lock', ?)");
                $t_stmt->execute([$user_id, -$offer['amount'], 'Escrow locked for Custom Offer: ' . $offer['title']]);
                
                // Update offer status
                $o_stmt = $db->prepare("UPDATE custom_offers SET status = 'accepted', project_id = ? WHERE id = ?");
                $o_stmt->execute([$project_id, $offer_id]);
                
                // Send success message to chat thread
                $sys_msg = "✅ I accepted your custom offer! The project escrow of ₦" . number_format($offer['amount']) . " is funded and the contract is active.";
                $msg_stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, content, is_read) VALUES (?, ?, ?, 0)");
                $msg_stmt->execute([$user_id, $offer['sender_id'], $sys_msg]);
                
                $db->commit();
                echo json_encode(['success' => true, 'project_id' => $project_id]);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Missing offer id']);
        }
        exit;
    }

    if ($action === 'fund_wallet') {
        // Direct deposit from chat if balance was insufficient
        $amount = (float)($_POST['amount'] ?? 0);
        if ($amount > 0) {
            $db->beginTransaction();
            try {
                // Update client balance
                $up_stmt = $db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                $up_stmt->execute([$amount, $user_id]);
                
                // Log transaction
                $t_stmt = $db->prepare("INSERT INTO transactions (user_id, amount, type, reference, description) VALUES (?, ?, 'deposit', ?, 'Deposit to wallet via Inline Paystack')");
                $ref = 'pay_inline_' . time();
                $t_stmt->execute([$user_id, $amount, $ref]);
                
                $db->commit();
                echo json_encode(['success' => true, 'new_balance' => $me_info['balance'] + $amount]);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid deposit amount']);
        }
        exit;
    }
}

// Resolve recipient slug
$receiver_slug = $_GET['user'] ?? null;
$recipient = null;
if ($receiver_slug) {
    $r_stmt = $db->prepare("SELECT id, full_name, username, primary_role FROM users WHERE username = ?");
    $r_stmt->execute([$receiver_slug]);
    $recipient = $r_stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all conversations for the inbox pane
$inbox_query = "
    SELECT u.id, u.full_name, u.username, u.primary_role,
           (SELECT content FROM messages WHERE (sender_id = :uid1 AND receiver_id = u.id) OR (sender_id = u.id AND receiver_id = :uid2) ORDER BY created_at DESC LIMIT 1) as last_message,
           (SELECT created_at FROM messages WHERE (sender_id = :uid3 AND receiver_id = u.id) OR (sender_id = u.id AND receiver_id = :uid4) ORDER BY created_at DESC LIMIT 1) as last_time,
           (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = :uid5 AND is_read = 0) as unread_count
    FROM users u
    WHERE u.id != :uid6 AND (
        EXISTS (SELECT 1 FROM messages WHERE sender_id = :uid7 AND receiver_id = u.id) OR
        EXISTS (SELECT 1 FROM messages WHERE sender_id = u.id AND receiver_id = :uid8) OR
        u.id = :selected_id
    )
    ORDER BY last_time DESC, u.id DESC
";
$inbox_stmt = $db->prepare($inbox_query);
$inbox_stmt->execute([
    ':uid1' => $user_id,
    ':uid2' => $user_id,
    ':uid3' => $user_id,
    ':uid4' => $user_id,
    ':uid5' => $user_id,
    ':uid6' => $user_id,
    ':uid7' => $user_id,
    ':uid8' => $user_id,
    ':selected_id' => $recipient ? $recipient['id'] : 0
]);
$conversations = $inbox_stmt->fetchAll(PDO::FETCH_ASSOC);

$total_unread = 0;
$unread_threads_count = 0;
foreach ($conversations as $c) {
    $total_unread += (int)$c['unread_count'];
    if ((int)$c['unread_count'] > 0) {
        $unread_threads_count++;
    }
}

$all_count = count($conversations);

$act_stmt = $db->prepare("
    SELECT COUNT(DISTINCT u.id)
    FROM users u
    JOIN proposals pr ON pr.provider_id = u.id AND pr.status = 'accepted'
    JOIN projects p ON pr.project_id = p.id AND p.status = 'in_progress'
    WHERE p.client_id = ?
");
$act_stmt->execute([$user_id]);
$active_count = (int)$act_stmt->fetchColumn();

// If there is no recipient selected but conversations exist, default to the first conversation
if (!$recipient && !empty($conversations)) {
    $recipient = $conversations[0];
}

// Fetch message history for selected recipient
$messages = [];
if ($recipient) {
    $m_stmt = $db->prepare("
        SELECT m.*, u.full_name as sender_name 
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE (m.sender_id = :uid AND m.receiver_id = :rid)
           OR (m.sender_id = :rid2 AND m.receiver_id = :uid2)
        ORDER BY m.created_at ASC
    ");
    $m_stmt->execute([
        ':uid' => $user_id,
        ':rid' => $recipient['id'],
        ':rid2' => $recipient['id'],
        ':uid2' => $user_id
    ]);
    $messages = $m_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark messages as read
    $up_stmt = $db->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0");
    $up_stmt->execute([$recipient['id'], $user_id]);
}

$page_title = 'Messages & Collaboration';
$active_tab = 'messages';
require_once __DIR__ . '/components/head.php'; 
?>

<style>
/* Pure Immersive WhatsApp / Telegram Mobile Takeover (Over both header & bottom nav) */
@media (max-width: 767px) {
    #mobile-bottom-nav, header#top-header {
        display: none !important;
    }
    body, html {
        height: 100dvh !important;
        overflow: hidden !important;
    }
    #chat-canvas-wrapper {
        height: 100dvh !important;
        position: fixed !important;
        inset: 0 !important;
        z-index: 50 !important;
    }
    /* Strictly Prevent iOS Safari Input Auto-Zoom on Focus */
    #message-text-input, #chat-search-input, input, textarea, select {
        font-size: 16px !important;
        touch-action: manipulation;
    }
}
</style>

<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Layout Area -->
<main class="flex-1 flex flex-col h-full w-full min-w-0 overflow-hidden relative bg-[#EFF2F7]">
    
    <?php include __DIR__ . '/components/header.php'; ?>

    <!-- Chat Hub Canvas (2-Column Split View on Desktop / Full Screen Takeover on Mobile) -->
    <div id="chat-canvas-wrapper" class="flex-1 flex overflow-hidden w-full h-[calc(100vh-65px)] md:h-[calc(100vh-70px)] bg-white md:bg-[#EFF2F7]">
        
        <!-- LEFT COLUMN: CONVERSATION LIST (WhatsApp Style Inbox) -->
        <aside id="chat-inbox-pane" class="w-full md:w-80 lg:w-96 bg-white border-r border-slate-200/90 flex flex-col shrink-0 h-full overflow-hidden transition-all duration-300">
            
            <!-- Inbox Header & Search -->
            <div class="p-3.5 sm:p-4 border-b border-slate-100 space-y-3 shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <!-- Back to Dashboard / Previous Page Arrow Button -->
                        <a href="index.php" class="w-8 h-8 rounded-[3px] bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors shrink-0" title="Back to Dashboard">
                            <i class="ph-bold ph-arrow-left text-sm"></i>
                        </a>
                        <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">Chats</h1>
                        <?php if ($total_unread > 0): ?>
                            <span class="bg-blue-50 text-[#1952E1] text-[11px] font-bold px-2 py-0.5 rounded-[3px]"><?= $total_unread ?> Unread</span>
                        <?php endif; ?>
                    </div>
                    <span class="text-[11px] text-emerald-800 font-bold bg-emerald-50 px-2 py-0.5 rounded-[3px] flex items-center gap-1">
                        <i class="ph-fill ph-shield-check text-emerald-600"></i> Escrow Safe
                    </span>
                </div>

                <!-- Search Input (With iOS No-Zoom fix) -->
                <div class="relative">
                    <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-sm"></i>
                    <input type="text" id="chat-search-input" placeholder="Search chats or talent..." class="w-full bg-slate-50 border border-slate-200/90 rounded-[3px] pl-9 pr-3 py-2 text-sm md:text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#1952E1] no-scrollbar">
                </div>

                <!-- Quick Filter Tabs -->
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar">
                    <button class="chat-tab active px-2.5 py-1 text-[11px] font-bold rounded-[3px] bg-[#1952E1] text-white whitespace-nowrap" data-filter="all">
                        All (<?= $all_count ?>)
                    </button>
                    <button class="chat-tab px-2.5 py-1 text-[11px] font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors whitespace-nowrap" data-filter="active">
                        Active (<?= $active_count ?>)
                    </button>
                    <button class="chat-tab px-2.5 py-1 text-[11px] font-bold rounded-[3px] bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors whitespace-nowrap" data-filter="unread">
                        Unread (<?= $unread_threads_count ?>)
                    </button>
                </div>
            </div>

            <!-- Conversation Scroll List (Hidden Scrollbar) -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100 no-scrollbar" id="conversations-container" style="-webkit-overflow-scrolling: touch;">
                
                <?php if (empty($conversations)): ?>
                    <div class="p-6 text-center text-xs text-slate-400">
                        No active conversations yet.
                    </div>
                <?php else: ?>
                    <?php foreach ($conversations as $conv): 
                        $isActive = ($recipient && $recipient['id'] == $conv['id']);
                        $avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($conv['full_name']) . "&background=f1f5f9&color=0f172a&bold=true";
                        $unread = (int)$conv['unread_count'];
                    ?>
                    <a href="/creda/app/messages/user/<?= htmlspecialchars($conv['username'] ?: $conv['id']) ?>" 
                       class="conv-item block p-3.5 sm:p-4 hover:bg-slate-50 transition-colors cursor-pointer <?= $isActive ? 'active bg-blue-50/40 border-l-4 border-l-[#1952E1]' : 'border-l-4 border-l-transparent' ?>" 
                       data-user-id="<?= $conv['id'] ?>"
                       data-username="<?= htmlspecialchars($conv['username'] ?: $conv['id']) ?>">
                        <div class="flex items-start gap-3">
                            <div class="relative shrink-0">
                                <img src="<?= $avatar_url ?>" alt="<?= htmlspecialchars($conv['full_name']) ?>" class="w-11 h-11 rounded-full object-cover border-2 border-slate-200">
                                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full ring-2 ring-white" title="Online Now"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <h3 class="text-xs sm:text-sm font-bold text-slate-900 truncate"><?= htmlspecialchars($conv['full_name']) ?></h3>
                                        <i class="ph-fill ph-check-circle text-[#1952E1] text-xs shrink-0"></i>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-medium whitespace-nowrap shrink-0">
                                        <?= $conv['last_time'] ? date('H:i', strtotime($conv['last_time'])) : '' ?>
                                    </span>
                                </div>
                                <span class="bg-blue-50 text-[#1952E1] text-[10px] font-bold px-1.5 py-0.5 rounded-[3px] inline-block mb-1">
                                    <?= htmlspecialchars(ucfirst($conv['primary_role'])) ?>
                                </span>
                                <p class="text-xs text-slate-700 truncate font-semibold">
                                    <?= htmlspecialchars($conv['last_message'] ?: 'No messages yet') ?>
                                </p>
                            </div>
                            <?php if ($unread > 0): ?>
                                <span class="w-2.5 h-2.5 bg-[#1952E1] rounded-full shrink-0 mt-2"></span>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

        </aside>

        <!-- RIGHT COLUMN: ACTIVE CONVERSATION THREAD & WORKSPACE -->
        <section id="chat-thread-pane" class="hidden md:flex flex-1 flex-col h-full bg-[#EFF2F7] min-w-0 overflow-hidden relative">
            
            <!-- Thread Top Header Bar (Compact on mobile) -->
            <div class="bg-white px-3 sm:px-5 py-2.5 sm:py-3 border-b border-slate-200/90 flex items-center justify-between gap-2 shrink-0 shadow-2xs z-10">
                
                <div id="open-profile-drawer-btn" class="flex items-center gap-2 sm:gap-2.5 min-w-0 cursor-pointer group hover:opacity-90 transition-all" title="Click to view candidate info & rating">
                    <!-- Mobile Back to Inbox Button -->
                    <button type="button" id="back-to-inbox-btn" class="md:hidden w-7 h-7 flex items-center justify-center rounded-[3px] bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors shrink-0 mr-0.5" onclick="event.stopPropagation();">
                        <i class="ph-bold ph-arrow-left text-sm"></i>
                    </button>

                    <div class="relative shrink-0">
                        <?php 
                            $rec_avatar = "https://ui-avatars.com/api/?name=" . urlencode($recipient['full_name'] ?? 'User') . "&background=f1f5f9&color=0f172a&bold=true";
                        ?>
                        <img id="active-chat-avatar" src="<?= $rec_avatar ?>" alt="Avatar" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover border-2 border-slate-200 group-hover:border-[#1952E1] transition-colors">
                        <span class="absolute bottom-0 right-0 w-2 h-2 sm:w-2.5 sm:h-2.5 bg-emerald-500 rounded-full ring-2 ring-white"></span>
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-1">
                            <h2 id="active-chat-name" class="text-xs sm:text-sm font-bold text-slate-900 truncate group-hover:text-[#1952E1] transition-colors"><?= htmlspecialchars($recipient['full_name'] ?? 'No Thread Selected') ?></h2>
                            <i class="ph-fill ph-check-circle text-[#1952E1] text-xs shrink-0" title="KYC Verified Pro"></i>
                            <span class="hidden sm:inline-block bg-emerald-50 text-emerald-800 text-[9px] font-bold px-1 py-0.2 rounded-[3px]">Online</span>
                        </div>
                        <p id="active-chat-contract" class="text-[10px] sm:text-[11px] text-slate-500 truncate flex items-center gap-1">
                            <span><?= htmlspecialchars(ucfirst($recipient['primary_role'] ?? 'Member')) ?></span>
                        </p>
                    </div>
                </div>

                <!-- Right Header Actions (Fiverr Custom Offer & Google Meet Bridge) -->
                <div class="flex items-center gap-1 sm:gap-2 shrink-0">
                    
                    <!-- Schedule Platform Google Call -->
                    <button type="button" id="schedule-call-btn" class="px-2 sm:px-3 py-1 sm:py-1.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-[#1952E1] font-bold text-[11px] sm:text-xs rounded-[3px] transition-colors flex items-center gap-1" title="Schedule Platform Meeting">
                        <i class="ph-bold ph-video-camera text-sm"></i>
                        <span class="hidden sm:inline">Schedule Call</span>
                    </button>

                    <!-- Create / Finalize Custom Offer -->
                    <button type="button" id="custom-offer-btn" class="px-2 sm:px-3.5 py-1 sm:py-1.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-[11px] sm:text-xs rounded-[3px] transition-colors flex items-center gap-1 shadow-sm" title="Create Custom Escrow Offer">
                        <i class="ph-bold ph-handshake text-sm"></i>
                        <span class="hidden sm:inline">Custom Offer</span>
                    </button>
                    
                </div>

            </div>

            <!-- Anti-Circumvention Notice Bar -->
            <div class="bg-amber-50/90 border-b border-amber-200 px-3 sm:px-5 py-1 text-[10px] sm:text-[11px] text-amber-900 font-medium flex items-center justify-between shrink-0">
                <div class="flex items-center gap-1 truncate">
                    <i class="ph-fill ph-shield-warning text-amber-700 text-xs shrink-0"></i>
                    <span class="truncate">Keep all calls, files, and payments inside Scriptly for 100% Escrow Protection.</span>
                </div>
                <span class="hidden md:inline font-bold text-amber-800 shrink-0">Protected</span>
            </div>

            <!-- CHAT STREAM MESSAGES (Scrollable, Compact on Mobile) -->
            <div class="flex-1 overflow-y-auto p-2.5 sm:p-4 space-y-2.5 sm:space-y-3.5 no-scrollbar min-h-0" id="chat-messages-container" style="-webkit-overflow-scrolling: touch; overscroll-behavior: contain;">
                
                <!-- System Timestamp Divider -->
                <div class="text-center my-1.5">
                    <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-white/80 px-2 py-0.5 rounded-[3px] border border-slate-200/80 shadow-2xs">
                        Active Messaging Thread
                    </span>
                </div>

                <?php if (empty($messages)): ?>
                    <div class="p-6 text-center text-xs text-slate-400">
                        No messages yet. Send a message to start the conversation!
                    </div>
                <?php else: ?>
                    <?php 
                    // Pre-fetch custom offers to display inline
                    $offers = [];
                    if ($recipient) {
                        $o_stmt = $db->prepare("SELECT * FROM custom_offers WHERE (sender_id = :uid AND receiver_id = :rid) OR (sender_id = :rid2 AND receiver_id = :uid2)");
                        $o_stmt->execute([
                            ':uid' => $user_id,
                            ':rid' => $recipient['id'],
                            ':rid2' => $recipient['id'],
                            ':uid2' => $user_id
                        ]);
                        $raw_offers = $o_stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($raw_offers as $ro) {
                            $offers[$ro['id']] = $ro;
                        }
                    }
                    ?>
                    <?php foreach ($messages as $msg): 
                        $isMe = ($msg['sender_id'] == $user_id);
                        $time = date('H:i', strtotime($msg['created_at']));
                        
                        // Check for Custom Offer marker
                        if (preg_match('/^\[CUSTOM_OFFER:(\d+)\]$/', $msg['content'], $matches)) {
                            $offer_id = (int)$matches[1];
                            $offer = $offers[$offer_id] ?? null;
                            if ($offer):
                                $offer_title = htmlspecialchars($offer['title']);
                                $offer_amount = number_format($offer['amount']);
                                $offer_status = $offer['status'];
                    ?>
                                <!-- Fiverr-Style Custom Offer Card -->
                                <div class="max-w-xs sm:max-w-md mx-auto my-3 bg-white border border-slate-200/90 rounded-[3px] shadow-sm overflow-hidden space-y-2.5 p-4 border-t-4 border-t-[#1952E1]">
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-6 h-6 rounded-[3px] bg-blue-50 text-[#1952E1] flex items-center justify-center text-xs font-bold">
                                                <i class="ph-bold ph-handshake"></i>
                                            </span>
                                            <h3 class="text-xs font-bold text-slate-900">Custom Project Offer</h3>
                                        </div>
                                        <span class="bg-blue-50 text-[#1952E1] text-[9px] font-bold px-1.5 py-0.5 rounded-[3px] uppercase"><?= $offer_status ?></span>
                                    </div>
                                    <p class="text-xs text-slate-700 font-semibold leading-relaxed">
                                        <?= $offer_title ?>
                                    </p>
                                    <div class="flex items-center justify-between text-xs py-2 px-3 bg-slate-50 border border-slate-200/60 rounded-[3px]">
                                        <span>Budget: <strong>₦<?= $offer_amount ?></strong></span>
                                        <span>Delivery: <strong><?= $offer['delivery_days'] ?> Days</strong></span>
                                    </div>
                                    <div class="pt-1 text-center">
                                        <?php if ($offer_status === 'pending'): ?>
                                            <?php if (!$isMe): // Hired user received the offer, show Accept button ?>
                                                <button type="button" onclick="acceptCustomOffer(<?= $offer_id ?>, <?= $offer['amount'] ?>);" class="w-full text-center text-xs font-bold bg-[#1952E1] hover:bg-blue-700 text-white py-2 rounded-[3px] transition-colors shadow-sm">
                                                    Accept & Fund Escrow
                                                </button>
                                            <?php else: ?>
                                                <span class="text-[10px] text-slate-400">Waiting for client response...</span>
                                            <?php endif; ?>
                                        <?php elseif ($offer_status === 'accepted'): ?>
                                            <span class="text-xs text-emerald-700 font-bold flex items-center justify-center gap-1">
                                                <i class="ph-fill ph-check-circle"></i> Escrow Active & Funded
                                            </span>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400 italic">Offer closed</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                    <?php 
                                continue;
                            endif;
                        }
                        
                        // Render standard message
                        if ($isMe):
                    ?>
                            <!-- Client Message (Right) -->
                            <div class="flex items-start justify-end gap-2 ml-auto max-w-[90%] sm:max-w-md">
                                <div class="space-y-0.5 text-right">
                                    <div class="bg-[#1952E1] text-white px-3 py-2 rounded-[10px] md:rounded-[3px] shadow-2xs text-xs text-left leading-relaxed">
                                        <?= nl2br(htmlspecialchars($msg['content'])) ?>
                                    </div>
                                    <div class="flex items-center justify-end gap-1 text-[9px] text-slate-400 mr-1">
                                        <span><?= $time ?></span>
                                        <span class="text-[#1952E1] font-bold">✓✓</span>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Freelancer Message (Left) -->
                            <div class="flex items-start gap-2 max-w-[90%] sm:max-w-md">
                                <img src="<?= $rec_avatar ?>" alt="Avatar" class="w-6 h-6 sm:w-8 sm:h-8 rounded-full object-cover shrink-0 mt-0.5">
                                <div class="space-y-0.5">
                                    <div class="bg-white border border-slate-200/90 px-3 py-2 rounded-[10px] md:rounded-[3px] shadow-2xs text-xs text-slate-800 leading-relaxed">
                                        <?= nl2br(htmlspecialchars($msg['content'])) ?>
                                    </div>
                                    <span class="text-[9px] text-slate-400 block ml-1"><?= $time ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

            <!-- CHAT COMPOSER & MULTIMEDIA INPUT BAR (Fixed at bottom on Mobile) -->
            <div class="bg-white p-2 sm:p-3 border-t border-slate-200/90 shrink-0 space-y-1.5 sticky bottom-0 z-20 pb-[max(0.5rem,env(safe-area-inset-bottom))] shadow-xs">
                
                <!-- Real-time Multi-Lingual Profanity & Contact Guard Warning (Hidden by Default) -->
                <div id="safety-warning-banner" class="hidden p-2 bg-rose-50 border border-rose-200 rounded-[3px] text-[11px] text-rose-800 font-bold flex items-center justify-between shadow-xs">
                    <span class="flex items-center gap-1">
                        <i class="ph-fill ph-warning-circle text-sm text-rose-600 shrink-0"></i>
                        <span id="safety-warning-text">Direct contact info (phone/email) or abusive terms are prohibited under platform terms.</span>
                    </span>
                    <button type="button" onclick="document.getElementById('safety-warning-banner').classList.add('hidden')" class="text-rose-600 hover:text-rose-800 text-xs font-bold ml-1">✕</button>
                </div>

                <!-- Voice Recording State Banner -->
                <div id="voice-recording-banner" class="hidden p-2 bg-red-50 border border-red-200 rounded-full sm:rounded-[3px] flex items-center justify-between shadow-sm animate-pulse">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 bg-red-600 rounded-full animate-ping"></span>
                        <span class="text-[11px] font-bold text-red-700">Recording Audio... <span id="recording-timer" class="font-mono ml-1">0:03</span></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button type="button" id="cancel-recording-btn" class="px-2 py-0.5 text-[11px] font-bold text-slate-600 hover:text-slate-900 bg-white rounded-full border border-slate-200">Cancel</button>
                        <button type="button" id="finish-recording-btn" class="px-2.5 py-0.5 text-[11px] font-bold text-white bg-[#1952E1] hover:bg-blue-700 rounded-full shadow-xs">Send Audio</button>
                    </div>
                </div>

                <!-- WhatsApp Input Capsule & Action Button Row -->
                <form id="chat-composer-form" class="flex items-end gap-1.5 sm:gap-2">
                    
                    <!-- Hidden File Selectors -->
                    <input type="file" id="file-upload-input" class="hidden" multiple onchange="handleFileUpload(this)">
                    <input type="file" id="image-upload-input" class="hidden" accept="image/*" onchange="handleFileUpload(this)">

                    <!-- Main WhatsApp Capsule (White pill container) -->
                    <div class="flex-1 bg-slate-50 border border-slate-200/90 rounded-full md:rounded-[3px] px-3 py-1 flex items-center gap-1.5 shadow-2xs focus-within:bg-white focus-within:border-[#1952E1] transition-colors">
                        
                        <!-- Emoji Picker Button -->
                        <button type="button" onclick="alert('Emoji picker will open on mobile keyboard');" class="text-slate-400 hover:text-slate-600 transition-colors shrink-0 p-0.5" title="Insert Emoji">
                            <i class="ph-bold ph-smiley text-lg"></i>
                        </button>

                        <!-- Expanding Message Textarea (Zero scrollbar, iOS zoom protected) -->
                        <textarea id="message-text-input" rows="1" placeholder="Type a message..." class="flex-1 bg-transparent border-0 px-1 py-1 text-base md:text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-0 resize-none max-h-24 overflow-y-auto leading-relaxed no-scrollbar" style="font-size: 16px !important;"></textarea>

                        <!-- Attach Documents (Paperclip) -->
                        <button type="button" onclick="document.getElementById('file-upload-input').click()" class="text-slate-400 hover:text-slate-600 transition-colors shrink-0 p-0.5" title="Attach Document / Zip">
                            <i class="ph-bold ph-paperclip text-lg"></i>
                        </button>

                        <!-- Attach Photos / Camera -->
                        <button type="button" onclick="document.getElementById('image-upload-input').click()" class="text-slate-400 hover:text-slate-600 transition-colors shrink-0 p-0.5" title="Attach Photo / Camera">
                            <i class="ph-bold ph-camera text-lg"></i>
                        </button>

                    </div>

                    <!-- Right WhatsApp Circular Action Button (Morphs between Mic and Send) -->
                    <div class="shrink-0">
                        
                        <!-- 1. Voice Note Mic Button (Shown when textarea is empty) -->
                        <button type="button" id="record-voice-btn" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#1952E1] hover:bg-blue-700 active:scale-95 text-white flex items-center justify-center shadow-md transition-all shrink-0" title="Hold or Tap to Record Voice Note">
                            <i class="ph-fill ph-microphone text-lg sm:text-xl"></i>
                        </button>

                        <!-- 2. Send Message Button (Shown when text is typed) -->
                        <button type="submit" id="send-msg-btn" class="hidden w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#1952E1] hover:bg-blue-700 active:scale-95 text-white flex items-center justify-center shadow-md transition-all shrink-0 pb-0.5" title="Send Message">
                            <i class="ph-bold ph-paper-plane-right text-lg sm:text-xl"></i>
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </div>
</main>

<!-- Main flex container ends -->
</div> 

<!-- ==========================================================================
     FIVERR-STYLE CUSTOM ESCROW OFFER CREATOR MODAL
     ========================================================================== -->
<div id="offer-modal-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 opacity-0 pointer-events-none transition-opacity duration-300 flex items-center justify-center p-3 sm:p-4">
    <div id="offer-modal" class="bg-white rounded-[3px] border border-slate-200 shadow-2xl max-w-lg w-full p-5 sm:p-6 transform scale-95 transition-transform duration-300 space-y-4">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                    <i class="ph-bold ph-handshake text-[#1952E1]"></i>
                    Create Custom Escrow Offer
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Finalize agreed price and milestones with the talent.</p>
            </div>
            <button type="button" id="close-offer-modal" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-[3px] transition-colors">
                <i class="ph-bold ph-x text-sm"></i>
            </button>
        </div>

        <!-- Offer Scope Description -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">Custom Offer Scope / Deliverables</label>
            <textarea id="offer-scope-desc" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] p-3 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]" placeholder="Specify exactly what is agreed for this order..."></textarea>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <!-- Agreed Total Amount -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Agreed Total Price (₦)</label>
                <input type="number" id="offer-total-amount" value="250000" min="5000" step="5000" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] px-3 py-2 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#1952E1]">
            </div>

            <!-- Delivery Deadline Days -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Delivery Timeline</label>
                <select id="offer-timeline-select" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
                    <option value="3">3 Calendar Days</option>
                    <option value="7">7 Calendar Days</option>
                    <option value="14" selected>14 Calendar Days</option>
                    <option value="30">30 Calendar Days</option>
                </select>
            </div>
        </div>

        <!-- Payment First Rule Notice -->
        <div class="p-3 bg-blue-50/70 border border-blue-200 rounded-[3px] flex items-start gap-2 text-xs text-slate-700">
            <i class="ph-bold ph-info text-[#1952E1] text-base shrink-0 mt-0.5"></i>
            <span><strong>Payment-First Activation</strong>: The contract countdown timer only starts once escrow deposit is confirmed. Once deposited, this listing will be delisted from the public marketplace.</span>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
            <button type="button" id="cancel-offer-btn" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-[3px] transition-colors">
                Cancel
            </button>
            <button type="button" onclick="submitCustomOffer();" class="px-4 py-2 text-xs font-bold bg-[#1952E1] hover:bg-blue-700 text-white rounded-[3px] transition-colors shadow-sm">
                Send Custom Offer & Activate Escrow
            </button>
        </div>

    </div>
</div>

<!-- ==========================================================================
     SCHEDULE GOOGLE MEET MEETING MODAL
     ========================================================================== -->
<div id="call-modal-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 opacity-0 pointer-events-none transition-opacity duration-300 flex items-center justify-center p-3 sm:p-4">
    <div id="call-modal" class="bg-white rounded-[3px] border border-slate-200 shadow-2xl max-w-lg w-full p-5 sm:p-6 transform scale-95 transition-transform duration-300 space-y-4">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                    <i class="ph-bold ph-video-camera text-[#1952E1]"></i>
                    Schedule In-Platform Google Meeting
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Generate a secure video conference link without sharing personal phone numbers.</p>
            </div>
            <button type="button" id="close-call-modal" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-[3px] transition-colors">
                <i class="ph-bold ph-x text-sm"></i>
            </button>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">Meeting Topic / Agenda</label>
            <input type="text" id="meeting-topic" value="Project Requirements & Scope Alignment" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Date</label>
                <input type="date" value="2026-08-22" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Time</label>
                <input type="time" value="14:00" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]">
            </div>
        </div>

        <div class="p-3 bg-amber-50/80 border border-amber-200 rounded-[3px] flex items-center gap-2 text-xs text-amber-900 font-medium">
            <i class="ph-fill ph-shield-warning text-amber-700 text-base shrink-0"></i>
            <span>Do not share off-platform contact details during the call to keep your escrow insurance active.</span>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
            <button type="button" id="cancel-call-btn" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-[3px] transition-colors">
                Cancel
            </button>
            <button type="button" onclick="generateMeetingLink();" class="px-4 py-2 text-xs font-bold bg-[#1952E1] hover:bg-blue-700 text-white rounded-[3px] transition-colors shadow-sm">
                Generate & Post Meeting Link
            </button>
        </div>

    </div>
</div>

<!-- ==========================================================================
     REVISION REQUEST FEEDBACK MODAL
     ========================================================================== -->
<div id="revision-modal-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 opacity-0 pointer-events-none transition-opacity duration-300 flex items-center justify-center p-3 sm:p-4">
    <div id="revision-modal" class="bg-white rounded-[3px] border border-slate-200 shadow-2xl max-w-lg w-full p-5 sm:p-6 transform scale-95 transition-transform duration-300 space-y-4">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                    <i class="ph-bold ph-arrow-counter-clockwise text-amber-600"></i>
                    Request Deliverable Revision
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Specify necessary changes before milestone escrow release.</p>
            </div>
            <button type="button" id="close-revision-modal" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-[3px] transition-colors">
                <i class="ph-bold ph-x text-sm"></i>
            </button>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">Detailed Feedback & Required Corrections <span class="text-red-500">*</span></label>
            <textarea id="revision-feedback-text" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-[3px] p-3 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#1952E1]" placeholder="Please adjust the password reset endpoint response structure according to section 4 of the spec..."></textarea>
        </div>

        <div class="p-3 bg-blue-50/70 border border-blue-200 rounded-[3px] text-xs text-slate-700">
            <span>Submitting revision requests automatically pauses the 7-day auto-approval timer until a revised deliverable is uploaded.</span>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
            <button type="button" id="cancel-revision-btn" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-[3px] transition-colors">
                Cancel
            </button>
            <button type="button" onclick="submitRevisionFeedback();" class="px-4 py-2 text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white rounded-[3px] transition-colors shadow-sm">
                Submit Revision Request
            </button>
        </div>

<!-- ==========================================================================
     CANDIDATE PUBLIC PROFILE & REPUTATION SLIDEOUT DRAWER
     ========================================================================== -->
<div id="profile-drawer-backdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 opacity-0 pointer-events-none transition-opacity duration-300">
    <aside id="profile-drawer" class="absolute top-0 right-0 h-full w-full max-w-sm sm:max-w-md bg-white shadow-2xl border-l border-slate-200 flex flex-col transform translate-x-full transition-transform duration-300 ease-in-out z-50 overflow-hidden">
        
        <!-- Drawer Header -->
        <div class="p-3.5 sm:p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80 shrink-0">
            <h3 class="text-xs sm:text-sm font-black text-slate-900 flex items-center gap-1.5">
                <i class="ph-bold ph-user-circle text-base text-[#1952E1]"></i>
                <span>Freelancer Public Profile</span>
            </h3>
            <button type="button" id="close-profile-drawer-btn" class="w-7 h-7 flex items-center justify-center rounded-[3px] bg-slate-200/70 hover:bg-slate-300 text-slate-700 transition-colors" title="Close Profile">
                <i class="ph-bold ph-x text-sm"></i>
            </button>
        </div>

        <!-- Scrollable Profile Body -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4 no-scrollbar">
            
            <!-- Profile Hero Card -->
            <div class="text-center space-y-2 pb-4 border-b border-slate-100">
                <div class="relative inline-block">
                    <img id="drawer-avatar" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200" alt="David Olanrewaju" class="w-20 h-20 rounded-full object-cover border-4 border-slate-100 shadow-md mx-auto">
                    <span class="absolute bottom-1 right-1 w-4 h-4 bg-emerald-500 rounded-full ring-2 ring-white" title="Online Now"></span>
                </div>
                
                <div>
                    <h2 id="drawer-name" class="text-base sm:text-lg font-black text-slate-900 tracking-tight flex items-center justify-center gap-1.5">
                        <span>David Olanrewaju</span>
                        <i class="ph-fill ph-check-circle text-[#1952E1] text-base" title="Scriptly KYC Identity Verified"></i>
                    </h2>
                    <p id="drawer-title" class="text-xs font-bold text-slate-600 mt-0.5">Senior Full-Stack PHP & MySQL Engineer</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">📍 Abuja, Nigeria • Member since Jan 2024</p>
                </div>

                <!-- Verified Badges & Rates -->
                <div class="flex items-center justify-center gap-1.5 flex-wrap pt-1">
                    <span class="bg-emerald-50 text-emerald-800 border border-emerald-200 text-[10px] font-bold px-2 py-0.5 rounded-[3px] flex items-center gap-1">
                        <i class="ph-fill ph-shield-check text-xs"></i> KYC ID Verified
                    </span>
                    <span class="bg-blue-50 text-[#1952E1] border border-blue-200 text-[10px] font-bold px-2 py-0.5 rounded-[3px]">
                        Pre-Assessed Top 1%
                    </span>
                    <span class="bg-slate-100 text-slate-700 text-[10px] font-bold px-2 py-0.5 rounded-[3px]">
                        ₦15,000 / hr
                    </span>
                </div>

                <!-- Bio Summary -->
                <p class="text-xs text-slate-600 leading-relaxed text-left bg-slate-50 p-2.5 rounded-[3px] border border-slate-200/80 mt-2">
                    Specialized in enterprise PHP MVC architectures, custom escrow integrations, scalable RESTful API design, and high-performance MySQL databases with 7+ years of experience.
                </p>
            </div>

            <!-- Reputation & Verified Public Metrics Grid -->
            <div class="space-y-1.5">
                <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Public Reputation & Performance</h4>
                <div class="grid grid-cols-2 gap-2">
                    <div class="p-2.5 bg-slate-50 rounded-[3px] border border-slate-200/80">
                        <span class="text-[10px] font-bold text-slate-500 block">Overall Rating</span>
                        <div class="flex items-center gap-1 mt-0.5">
                            <i class="ph-fill ph-star text-amber-500 text-sm"></i>
                            <span class="text-sm font-black text-slate-900">4.9</span>
                            <span class="text-[10px] text-slate-400 font-medium">(29 reviews)</span>
                        </div>
                    </div>
                    <div class="p-2.5 bg-slate-50 rounded-[3px] border border-slate-200/80">
                        <span class="text-[10px] font-bold text-slate-500 block">Job Success Score</span>
                        <span class="text-sm font-black text-emerald-700 mt-0.5 block">98% High Success</span>
                    </div>
                    <div class="p-2.5 bg-slate-50 rounded-[3px] border border-slate-200/80">
                        <span class="text-[10px] font-bold text-slate-500 block">Completed Projects</span>
                        <span class="text-sm font-black text-slate-900 mt-0.5 block">14 Platform Contracts</span>
                    </div>
                    <div class="p-2.5 bg-slate-50 rounded-[3px] border border-slate-200/80">
                        <span class="text-[10px] font-bold text-slate-500 block">On-Time Delivery</span>
                        <span class="text-sm font-black text-slate-900 mt-0.5 block">100% Guaranteed</span>
                    </div>
                </div>
            </div>

            <!-- Scriptly Pre-Assessment Exam Scores -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Scriptly Verified Exam Scores</h4>
                    <span class="text-[10px] font-bold text-[#1952E1]">Verified Assessment</span>
                </div>
                <div class="grid grid-cols-2 gap-1.5">
                    <div class="p-2 bg-slate-50 rounded-[3px] border border-slate-200 flex items-center justify-between text-xs">
                        <span class="text-slate-700 font-medium truncate">PHP MVC Frameworks</span>
                        <strong class="text-[#1952E1] font-black shrink-0 ml-1">96%</strong>
                    </div>
                    <div class="p-2 bg-slate-50 rounded-[3px] border border-slate-200 flex items-center justify-between text-xs">
                        <span class="text-slate-700 font-medium truncate">MySQL Optimization</span>
                        <strong class="text-[#1952E1] font-black shrink-0 ml-1">98%</strong>
                    </div>
                    <div class="p-2 bg-slate-50 rounded-[3px] border border-slate-200 flex items-center justify-between text-xs">
                        <span class="text-slate-700 font-medium truncate">RESTful API Design</span>
                        <strong class="text-[#1952E1] font-black shrink-0 ml-1">95%</strong>
                    </div>
                    <div class="p-2 bg-slate-50 rounded-[3px] border border-slate-200 flex items-center justify-between text-xs">
                        <span class="text-slate-700 font-medium truncate">Tailwind & CSS</span>
                        <strong class="text-[#1952E1] font-black shrink-0 ml-1">94%</strong>
                    </div>
                </div>
            </div>

            <!-- Recent Client Reviews & Ratings -->
            <div class="space-y-1.5">
                <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Recent Client Reviews (29)</h4>
                
                <div class="space-y-2">
                    <!-- Review 1 -->
                    <div class="p-2.5 bg-slate-50 rounded-[3px] border border-slate-200/80 space-y-1">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1">
                                <span class="text-amber-500 text-xs">★★★★★</span>
                                <span class="text-xs font-bold text-slate-800">5.0</span>
                            </div>
                            <span class="text-[10px] text-slate-400">2 weeks ago</span>
                        </div>
                        <p class="text-xs text-slate-600 italic">"Exceptional PHP developer! Delivered the escrow ledger and webhook handlers 2 days ahead of schedule. Highly recommended."</p>
                        <span class="text-[10px] font-bold text-slate-500 block">— Fintech Labs Nigeria (₦350k Project)</span>
                    </div>

                    <!-- Review 2 -->
                    <div class="p-2.5 bg-slate-50 rounded-[3px] border border-slate-200/80 space-y-1">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1">
                                <span class="text-amber-500 text-xs">★★★★★</span>
                                <span class="text-xs font-bold text-slate-800">4.9</span>
                            </div>
                            <span class="text-[10px] text-slate-400">1 month ago</span>
                        </div>
                        <p class="text-xs text-slate-600 italic">"Super clean code architecture and seamless API integration. Very proactive communicator."</p>
                        <span class="text-[10px] font-bold text-slate-500 block">— Apex Logistics (₦200k Project)</span>
                    </div>
                </div>
            </div>

            <!-- Current Active Contract Summary -->
            <div class="p-3 bg-blue-50/70 border border-blue-200 rounded-[3px] space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-900 flex items-center gap-1">
                        <i class="ph-bold ph-file-text text-[#1952E1]"></i>
                        <span>Current Contract with You</span>
                    </span>
                    <span class="bg-blue-100 text-[#1952E1] text-[10px] font-bold px-1.5 py-0.2 rounded-[3px]">Milestone 2</span>
                </div>
                <p class="text-xs text-slate-700 font-medium leading-tight">
                    Full-Stack PHP & MySQL Web Portal with Escrow System
                </p>
                <div class="flex items-center justify-between text-xs font-bold pt-1 text-slate-900 border-t border-blue-200/60">
                    <span>Locked Escrow:</span>
                    <span class="text-[#1952E1] font-black">₦150,000</span>
                </div>
            </div>

            <!-- Drawer Bottom Actions -->
            <div class="space-y-2 pt-2 border-t border-slate-100">
                <a href="services.php" class="w-full py-2 text-center text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-[3px] transition-colors block">
                    View Full Directory Profile
                </a>
                <button type="button" onclick="alert('Downloading verified credentials and rating history (PDF)...');" class="w-full py-2.5 text-center text-xs font-bold bg-[#1952E1] hover:bg-blue-700 text-white rounded-[3px] transition-colors block shadow-sm">
                    Download Verified Credentials (PDF)
                </button>
            </div>

        </div>

    </aside>
</div>

<!-- ==========================================================================
     JAVASCRIPT: MESSAGING LOGIC, MULTI-LINGUAL FILTER, AND MODALS
     ========================================================================== -->
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    
    const messagesContainer = document.getElementById('chat-messages-container');
    const composerForm = document.getElementById('chat-composer-form');
    const messageInput = document.getElementById('message-text-input');
    const safetyBanner = document.getElementById('safety-warning-banner');
    const safetyText = document.getElementById('safety-warning-text');

    // Multi-Lingual Profanity & Contact Info Filter Dictionary (English, Pidgin, Igbo, Yoruba, Hausa)
    const restrictedKeywords = [
        // English Abusive
        'fool', 'idiot', 'stupid', 'bastard', 'scammer', 'fraudster', 'bitch', 'moron',
        // Nigerian Pidgin & Slang
        'mumu', 'ode', 'werey', 'olodo', 'kolo', 'craze', 'madman', 'goat',
        // Igbo Abusive
        'onye iberibe', 'onye ala', 'anuofia', 'efulefu', 'nkita',
        // Yoruba Abusive
        'oloshi', 'asinwin', 'oloriburuku', 'aja', 'dada',
        // Hausa Abusive
        'mahaukaci', 'wawa', 'baba', 'dabba'
    ];

    // Phone / WhatsApp / External Contact Regular Expressions
    const phonePattern = /(\+?234|0)[789][01]\d{8}/;
    const generalPhonePattern = /\b\d{4}[-\s]?\d{3}[-\s]?\d{4}\b/;
    const emailPattern = /\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,7}\b/;
    const contactKeywordPattern = /(whatsapp|telegram|ig handle|phone number|call me on|send account number|pay to my bank|send to opay|send to palmpay)/i;

    function checkContentSafety(text) {
        const lowerText = text.toLowerCase();
        
        // Check for Abusive Language
        for (let word of restrictedKeywords) {
            if (lowerText.includes(word)) {
                return {
                    safe: false,
                    reason: `Abusive word detected ('${word}'). Abusive language is strictly prohibited and leads to account suspension.`
                };
            }
        }

        // Check for Contact / Anti-Circumvention Violations
        if (phonePattern.test(text) || generalPhonePattern.test(text)) {
            return {
                safe: false,
                reason: 'Phone number detected. Off-platform contact sharing is restricted to safeguard your 100% Escrow Protection.'
            };
        }

        if (emailPattern.test(text)) {
            return {
                safe: false,
                reason: 'Email address detected. Please keep all communication inside the platform.'
            };
        }

        if (contactKeywordPattern.test(text)) {
            return {
                safe: false,
                reason: 'External contact or outside bank payment sharing detected. All contracts must be processed through platform escrow.'
            };
        }

        return { safe: true };
    }

    // Scroll to bottom
    const scrollToBottom = () => {
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    };
    scrollToBottom();

    // WhatsApp Morphing: Mic Button vs Send Button Toggle
    const recordVoiceBtn = document.getElementById('record-voice-btn');
    const sendMsgBtn = document.getElementById('send-msg-btn');
    const voiceBanner = document.getElementById('voice-recording-banner');
    const recordingTimer = document.getElementById('recording-timer');
    const cancelRecordingBtn = document.getElementById('cancel-recording-btn');
    const finishRecordingBtn = document.getElementById('finish-recording-btn');
    let recordInterval = null;
    let recordSeconds = 0;

    function updateActionButtons() {
        if (messageInput && recordVoiceBtn && sendMsgBtn) {
            const hasText = messageInput.value.trim().length > 0;
            if (hasText) {
                recordVoiceBtn.classList.add('hidden');
                sendMsgBtn.classList.remove('hidden');
            } else {
                sendMsgBtn.classList.add('hidden');
                recordVoiceBtn.classList.remove('hidden');
            }
        }
    }

    // Auto-expand textarea & toggle action buttons
    if (messageInput) {
        messageInput.addEventListener('input', () => {
            messageInput.style.height = 'auto';
            messageInput.style.height = Math.min(messageInput.scrollHeight, 110) + 'px';
            updateActionButtons();
        });

        messageInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                composerForm.dispatchEvent(new Event('submit'));
            }
        });
    }

    // Composer Submit Handler
    if (composerForm) {
        composerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const text = messageInput.value.trim();
            if (!text) return;

            // Security Check
            const safetyCheck = checkContentSafety(text);
            if (!safetyCheck.safe) {
                if (safetyBanner && safetyText) {
                    safetyText.textContent = safetyCheck.reason;
                    safetyBanner.classList.remove('hidden');
                }
                return;
            }

            if (safetyBanner) safetyBanner.classList.add('hidden');

            // Send AJAX to DB
            const formData = new FormData();
            formData.append('action', 'send_message');
            formData.append('receiver_id', '<?= $recipient ? $recipient['id'] : 0 ?>');
            formData.append('content', text);

            fetch('', {
                method: 'POST',
                body: formData
            });

            // Append Client Message
            const now = new Date();
            const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            const msgDiv = document.createElement('div');
            msgDiv.className = 'flex items-start justify-end gap-2.5 ml-auto max-w-xl';
            msgDiv.innerHTML = `
                <div class="space-y-1 text-right">
                    <div class="bg-[#1952E1] text-white p-3.5 rounded-[3px] shadow-2xs text-xs text-left leading-relaxed">
                        ${text.replace(/\n/g, '<br>')}
                    </div>
                    <div class="flex items-center justify-end gap-1 text-[10px] text-slate-400 mr-1">
                        <span>${timeStr}</span>
                        <span class="text-[#1952E1] font-bold">✓ Sent</span>
                    </div>
                </div>
            `;
            messagesContainer.appendChild(msgDiv);
            messageInput.value = '';
            messageInput.style.height = 'auto';
            updateActionButtons();
            scrollToBottom();
        });
    }

    // WhatsApp Voice Note Recording Logic
    if (recordVoiceBtn && voiceBanner) {
        recordVoiceBtn.addEventListener('click', () => {
            voiceBanner.classList.remove('hidden');
            recordSeconds = 0;
            if (recordingTimer) recordingTimer.textContent = '0:00';
            
            if (recordInterval) clearInterval(recordInterval);
            recordInterval = setInterval(() => {
                recordSeconds++;
                const mins = Math.floor(recordSeconds / 60);
                const secs = (recordSeconds % 60).toString().padStart(2, '0');
                if (recordingTimer) recordingTimer.textContent = `${mins}:${secs}`;
            }, 1000);
        });
    }

    if (cancelRecordingBtn && voiceBanner) {
        cancelRecordingBtn.addEventListener('click', () => {
            if (recordInterval) clearInterval(recordInterval);
            voiceBanner.classList.add('hidden');
        });
    }

    if (finishRecordingBtn && voiceBanner) {
        finishRecordingBtn.addEventListener('click', () => {
            if (recordInterval) clearInterval(recordInterval);
            voiceBanner.classList.add('hidden');

            const now = new Date();
            const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const finalDuration = recordingTimer ? recordingTimer.textContent : '0:12';
            
            const voiceDiv = document.createElement('div');
            voiceDiv.className = 'flex items-start justify-end gap-2.5 ml-auto max-w-md';
            voiceDiv.innerHTML = `
                <div class="space-y-1 w-full text-right">
                    <div class="bg-[#1952E1] text-white p-3 rounded-[3px] shadow-2xs flex items-center justify-between gap-3">
                        <button type="button" class="w-8 h-8 rounded-full bg-white text-[#1952E1] flex items-center justify-center text-sm shadow-xs shrink-0" onclick="togglePlayVoice(this)">
                            <i class="ph-fill ph-play"></i>
                        </button>
                        <div class="flex-1 space-y-1 text-left">
                            <div class="flex items-center gap-0.5 h-4">
                                <span class="w-1 bg-white h-3 rounded-full"></span>
                                <span class="w-1 bg-white h-4 rounded-full"></span>
                                <span class="w-1 bg-blue-300 h-2 rounded-full"></span>
                                <span class="w-1 bg-white h-4 rounded-full"></span>
                                <span class="w-1 bg-blue-300 h-1.5 rounded-full"></span>
                                <span class="w-1 bg-white h-3 rounded-full"></span>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-blue-100 font-medium">
                                <span>Voice Note</span>
                                <span>${finalDuration}</span>
                            </div>
                        </div>
                    </div>
                    <span class="text-[10px] text-slate-400 block mr-1">${timeStr} • ✓ Sent</span>
                </div>
            `;
            messagesContainer.appendChild(voiceDiv);
            scrollToBottom();
        });
    }

    // Mobile Inbox & Thread Switching
    const inboxPane = document.getElementById('chat-inbox-pane');
    const threadPane = document.getElementById('chat-thread-pane');
    const backBtn = document.getElementById('back-to-inbox-btn');
    const convItems = document.querySelectorAll('.conv-item');

    convItems.forEach(item => {
        item.addEventListener('click', () => {
            convItems.forEach(c => {
                c.classList.remove('active', 'bg-blue-50/40', 'border-l-[#1952E1]');
                c.classList.add('border-l-transparent');
            });
            item.classList.add('active', 'bg-blue-50/40', 'border-l-[#1952E1]');
            item.classList.remove('border-l-transparent');

            // On Mobile: show thread
            if (window.innerWidth < 768) {
                inboxPane.classList.add('hidden');
                threadPane.classList.remove('hidden');
                threadPane.classList.add('flex');
                setTimeout(() => {
                    scrollToBottom();
                }, 50);
            }
        });
    });

    if (backBtn) {
        backBtn.addEventListener('click', () => {
            threadPane.classList.add('hidden');
            threadPane.classList.remove('flex');
            inboxPane.classList.remove('hidden');
        });
    }

    // Modal Helpers
    const setupModal = (openBtnId, modalBackdropId, modalId, closeBtnId, cancelBtnId) => {
        const openBtn = document.getElementById(openBtnId);
        const backdrop = document.getElementById(modalBackdropId);
        const modal = document.getElementById(modalId);
        const closeBtn = document.getElementById(closeBtnId);
        const cancelBtn = document.getElementById(cancelBtnId);

        const openModal = () => {
            if (backdrop && modal) {
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                modal.classList.remove('scale-95');
                modal.classList.add('scale-100');
            }
        };

        const closeModal = () => {
            if (backdrop && modal) {
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                modal.classList.remove('scale-100');
                modal.classList.add('scale-95');
            }
        };

        if (openBtn) openBtn.addEventListener('click', openModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
        if (backdrop) {
            backdrop.addEventListener('click', (e) => {
                if (e.target === backdrop) closeModal();
            });
        }
    };

    setupModal('custom-offer-btn', 'offer-modal-backdrop', 'offer-modal', 'close-offer-modal', 'cancel-offer-btn');
    setupModal('schedule-call-btn', 'call-modal-backdrop', 'call-modal', 'close-call-modal', 'cancel-call-btn');
    setupModal(null, 'revision-modal-backdrop', 'revision-modal', 'close-revision-modal', 'cancel-revision-btn');

    // Candidate Profile Slideout Drawer Logic
    const profileDrawerBtn = document.getElementById('open-profile-drawer-btn');
    const profileBackdrop = document.getElementById('profile-drawer-backdrop');
    const profileDrawer = document.getElementById('profile-drawer');
    const closeProfileBtn = document.getElementById('close-profile-drawer-btn');

    const openProfileDrawer = () => {
        if (profileBackdrop && profileDrawer) {
            profileBackdrop.classList.remove('opacity-0', 'pointer-events-none');
            profileDrawer.classList.remove('translate-x-full');
            profileDrawer.classList.add('translate-x-0');
        }
    };

    const closeProfileDrawer = () => {
        if (profileBackdrop && profileDrawer) {
            profileBackdrop.classList.add('opacity-0', 'pointer-events-none');
            profileDrawer.classList.remove('translate-x-0');
            profileDrawer.classList.add('translate-x-full');
        }
    };

    const profileTriggers = document.querySelectorAll('.open-profile-trigger, #open-profile-drawer-btn');
    profileTriggers.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            openProfileDrawer();
        });
    });
    if (closeProfileBtn) closeProfileBtn.addEventListener('click', closeProfileDrawer);
    if (profileBackdrop) {
        profileBackdrop.addEventListener('click', (e) => {
            if (e.target === profileBackdrop) closeProfileDrawer();
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeProfileDrawer();
        }
    });
});

// Global functions for inline handlers
function togglePlayVoice(btn) {
    const icon = btn.querySelector('i');
    if (icon.classList.contains('ph-play')) {
        icon.className = 'ph-fill ph-pause';
        setTimeout(() => {
            icon.className = 'ph-fill ph-play';
        }, 3000);
    } else {
        icon.className = 'ph-fill ph-play';
    }
}

function handleFileUpload(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        alert(`Attached document '${file.name}' (${(file.size / 1024 / 1024).toFixed(2)} MB). Ready to send in chat.`);
    }
}

function openRevisionModal() {
    const backdrop = document.getElementById('revision-modal-backdrop');
    const modal = document.getElementById('revision-modal');
    if (backdrop && modal) {
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.remove('scale-95');
        modal.classList.add('scale-100');
    }
}

function submitRevisionFeedback() {
    const text = document.getElementById('revision-feedback-text').value;
    if (!text.trim()) {
        alert('Please specify the required revision details.');
        return;
    }
    alert('Revision request sent to talent. The 7-day auto-approval timer has been paused until revised assets are submitted.');
    document.getElementById('revision-modal-backdrop').classList.add('opacity-0', 'pointer-events-none');
}

function confirmReleaseEscrow(amount) {
    if (confirm(`Are you sure you want to approve this deliverable and release ₦${amount.toLocaleString()} from escrow to David Olanrewaju?`)) {
        alert(`Escrow funds (₦${amount.toLocaleString()}) have been successfully transferred to talent's wallet!`);
    }
}

function submitCustomOffer() {
    const scope = document.getElementById('offer-scope-desc').value.trim();
    const amount = document.getElementById('offer-total-amount').value;
    const days = document.getElementById('offer-timeline-select').value;
    
    if (!scope || !amount || !days) {
        alert('Please fill out all custom offer fields.');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'send_offer');
    formData.append('receiver_id', '<?= $recipient ? $recipient['id'] : 0 ?>');
    formData.append('title', scope);
    formData.append('amount', amount);
    formData.append('days', days);
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Custom Escrow Offer created! The talent will receive the offer in-chat.');
            location.reload();
        } else {
            alert('Error creating offer: ' + data.error);
        }
    });
}

function acceptCustomOffer(offerId, amount) {
    const formData = new FormData();
    formData.append('action', 'accept_offer');
    formData.append('offer_id', offerId);
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Custom Offer accepted and escrow funded successfully!');
            location.reload();
        } else if (data.error === 'insufficient_funds') {
            const required = data.required;
            if (confirm(`Your wallet balance is insufficient by ₦${required.toLocaleString()}. Would you like to pay securely now via Paystack to fund this escrow?`)) {
                // Initialize Paystack Inline popup checkout
                let handler = PaystackPop.setup({
                    key: 'pk_test_a0d84fde90a887b415a77033cb2188ff6e65a0db', // Demo public test key
                    email: '<?= htmlspecialchars($me_info['email']) ?>',
                    amount: required * 100, // In kobo
                    currency: 'NGN',
                    callback: function(response) {
                        // Deposit funded successfully, confirm on database
                        const fundData = new FormData();
                        fundData.append('action', 'fund_wallet');
                        fundData.append('amount', required);
                        
                        fetch('', {
                            method: 'POST',
                            body: fundData
                        })
                        .then(r => r.json())
                        .then(fundRes => {
                            if (fundRes.success) {
                                // Balance updated, retry accepting custom offer
                                acceptCustomOffer(offerId, amount);
                            } else {
                                alert('Error verifying payment.');
                            }
                        });
                    },
                    onClose: function() {
                        alert('Payment was cancelled.');
                    }
                });
                handler.openIframe();
            }
        } else {
            alert('Error accepting offer: ' + data.error);
        }
    });
}

function generateMeetingLink() {
    alert('Platform Video Meeting link generated and shared in chat! Reminder: Keep all discussions on-platform to protect your escrow guarantee.');
    document.getElementById('call-modal-backdrop').classList.add('opacity-0', 'pointer-events-none');
}
</script>

<?php include __DIR__ . '/components/footer.php'; ?>
