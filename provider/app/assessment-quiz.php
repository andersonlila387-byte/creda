<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
enforceSecurityHeaders();

$user_email = $_SESSION['user_email'] ?? null;
if (empty($user_email)) {
    header('Location: ../login.php');
    exit;
}

$db = getDBConnection();
$category_slug = trim($_GET['category'] ?? '');
$subtopic      = trim($_GET['subtopic']  ?? '');

$cat_stmt = $db->prepare("SELECT id, name FROM service_categories WHERE slug = ?");
$cat_stmt->execute([$category_slug]);
$category = $cat_stmt->fetch(PDO::FETCH_ASSOC);

if (!$category || empty($subtopic)) {
    header("Location: assessment.php");
    exit;
}

// Randomly pick 15 from the full pool â€” each exam session is unique
$q_stmt = $db->prepare("SELECT id, question_text, question_type, option_a, option_b, option_c, option_d
                         FROM quiz_questions
                         WHERE category_id = ? AND subtopic = ?
                         ORDER BY RAND() LIMIT 15");
$q_stmt->execute([$category['id'], $subtopic]);
$questions       = $q_stmt->fetchAll(PDO::FETCH_ASSOC);
$total_questions = count($questions);

// Inject head.php for session/role guards only (we override its HTML completely below)
require_once __DIR__ . '/components/head.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Exam â€” Scriptly</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/tailwind.min.css">
    <link rel="stylesheet" href="../../assets/css/scriptly-alerts.css">

    <style>
        /* â”€â”€â”€â”€â”€â”€â”€â”€â”€ Reset & Base â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            overflow: hidden;
            font-family: 'Inter', system-ui, sans-serif;
            background: #F1F5F9;
            color: #0E131F;
            /* Security: no text selection */
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        /* â”€â”€â”€â”€â”€â”€â”€â”€â”€ Full-Page Focus Shell â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        #exam-shell {
            position: fixed;
            inset: 0;
            display: flex;
            flex-direction: column;
            background: #F1F5F9;
            z-index: 9999;
        }

        /* â”€â”€â”€â”€â”€â”€â”€â”€â”€ Top Header â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        #exam-header {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            border-bottom: 1px solid #E2E8F0;
            padding: 14px 24px;
            flex-shrink: 0;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }

        /* Mobile: two-row header */
        @media (max-width: 767px) {
            #exam-header {
                flex-direction: column;
                align-items: stretch;
                padding: 12px 16px;
                gap: 10px;
            }
            /* Top row of header: brand + quit */
            #header-row-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            /* Bottom row of header: full-width timer bar */
            #header-row-timer {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                background: #FEF2F2;
                border: 1px solid #FECACA;
                border-radius: 4px;
                padding: 6px 12px;
            }
            #header-row-timer .timer-label-sm {
                font-size: 11px;
                font-weight: 700;
                color: #B91C1C;
                text-transform: uppercase;
                letter-spacing: 0.04em;
            }
            #header-row-timer #timer-clock {
                font-size: 18px;
                font-weight: 900;
                color: #B91C1C;
                letter-spacing: 0.06em;
                font-variant-numeric: tabular-nums;
            }
            /* Hide desktop timer wrapper on mobile (header timer takes over) */
            #timer-wrapper { display: none !important; }
        }

        /* Desktop: hide the mobile-only second timer row */
        @media (min-width: 768px) {
            #header-row-timer { display: none !important; }
        }

        .exam-brand {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: #0A2342;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .exam-subtopic-badge {
            font-size: 10px;
            font-weight: 800;
            color: #1952E1;
            background: #EFF4FF;
            border: 1px solid #C7D7FD;
            border-radius: 3px;
            padding: 3px 10px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-left: 12px;
        }

        #timer-wrapper {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 4px;
            padding: 6px 14px;
        }

        #timer-clock {
            font-size: 16px;
            font-weight: 900;
            color: #B91C1C;
            letter-spacing: 0.06em;
            font-variant-numeric: tabular-nums;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-submit-sm {
            background: #059669;
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            padding: 7px 14px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            display: none; /* shown on small screens via JS check */
        }
        .btn-submit-sm:hover { background: #047857; }

        .btn-quit {
            font-size: 10px;
            font-weight: 800;
            color: #94A3B8;
            background: none;
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .btn-quit:hover { color: #EF4444; }

        /* â”€â”€â”€â”€â”€â”€â”€â”€â”€ Body: Two-Column Layout â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        #exam-body {
            flex: 1;
            display: flex;
            flex-direction: row;
            gap: 20px;
            padding: 20px 24px;
            min-height: 0;
            overflow: hidden;
        }

        /* â”€â”€ LEFT COLUMN: Question workspace â”€â”€ */
        #col-question {
            flex: 1;
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        #question-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 24px;
            border-bottom: 1px solid #F1F5F9;
            flex-shrink: 0;
        }

        #lbl-question-index {
            font-size: 11px;
            font-weight: 800;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        #lbl-question-type {
            font-size: 10px;
            font-weight: 800;
            background: #EFF4FF;
            color: #1952E1;
            border: 1px solid #C7D7FD;
            border-radius: 3px;
            padding: 3px 10px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        /* Progress bar */
        #progress-track {
            height: 3px;
            background: #F1F5F9;
            flex-shrink: 0;
        }
        #pb-indicator {
            height: 100%;
            background: #1952E1;
            transition: width 0.3s ease;
        }

        /* Scrollable question + options area */
        #question-body {
            flex: 1;
            overflow-y: auto;
            padding: 28px 32px;
        }

        #question-text {
            font-size: 14px;
            font-weight: 600;
            color: #1E293B;
            line-height: 1.75;
            margin-bottom: 24px;
        }

        .code-block {
            font-family: 'Fira Mono', 'Courier New', monospace;
            background: #0F172A;
            color: #F8FAFC;
            padding: 16px 20px;
            border-radius: 4px;
            font-size: 12px;
            line-height: 1.65;
            border: 1px solid #1E293B;
            overflow-x: auto;
            white-space: pre;
            margin-top: 12px;
        }

        #options-divider {
            height: 1px;
            background: #F1F5F9;
            margin-bottom: 20px;
        }

        /* MCQ Option cards */
        .option-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 16px;
            border: 2px solid #E2E8F0;
            border-radius: 4px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
            background: #FAFAFA;
        }
        .option-card:hover { border-color: #93B4FF; background: #F8FAFF; }
        .option-card.selected { border-color: #1952E1; background: #EFF4FF; }

        .option-letter {
            width: 30px;
            height: 30px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 900;
            flex-shrink: 0;
            background: #E2E8F0;
            color: #64748B;
            transition: background 0.15s, color 0.15s;
        }
        .option-card.selected .option-letter { background: #1952E1; color: #fff; }

        .option-text {
            font-size: 13px;
            font-weight: 500;
            color: #334155;
            flex: 1;
            line-height: 1.5;
        }

        .option-radio {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 2px solid #CBD5E1;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .option-card.selected .option-radio { border-color: #1952E1; }
        .option-radio-dot {
            width: 8px;
            height: 8px;
            border-radius: 2px;
            background: #1952E1;
            display: none;
        }
        .option-card.selected .option-radio-dot { display: block; }

        /* Text / Code input */
        .text-input-wrapper { margin-top: 4px; }
        .console-bar {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-bottom: none;
            border-radius: 4px 4px 0 0;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
            font-weight: 800;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .console-textarea {
            width: 100%;
            display: block;
            background: #0F172A;
            color: #F8FAFC;
            font-family: 'Fira Mono', 'Courier New', monospace;
            font-size: 12px;
            padding: 16px;
            border: 1px solid #E2E8F0;
            border-top: none;
            border-radius: 0 0 4px 4px;
            resize: vertical;
            outline: none;
            min-height: 100px;
        }

        /* Footer navigation */
        #question-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 24px;
            border-top: 1px solid #F1F5F9;
            background: #FAFAFA;
            flex-shrink: 0;
        }

        .btn-nav {
            font-size: 11px;
            font-weight: 800;
            padding: 9px 20px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            transition: background 0.15s, opacity 0.15s;
        }
        .btn-nav:disabled { opacity: 0.35; cursor: not-allowed; }
        .btn-prev-nav { background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0; }
        .btn-prev-nav:hover:not(:disabled) { background: #E2E8F0; }
        .btn-next-nav { background: #1952E1; color: #fff; }
        .btn-next-nav:hover:not(:disabled) { background: #1440B3; }
        .btn-finish-nav { background: #059669; color: #fff; }
        .btn-finish-nav:hover:not(:disabled) { background: #047857; }

        /* â”€â”€ RIGHT COLUMN: Sidebar â”€â”€ */
        #col-sidebar {
            width: 300px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            gap: 16px;
            overflow-y: auto;
        }

        .sidebar-card {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 4px;
            padding: 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .sidebar-card-title {
            font-size: 10px;
            font-weight: 800;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid #F1F5F9;
        }

        /* Timer widget in sidebar */
        #sidebar-timer-card {
            padding: 10px 14px;
        }
        #sidebar-timer-card .sidebar-card-title {
            margin-bottom: 6px;
            padding-bottom: 4px;
        }
        #timer-display {
            font-size: 20px;
            font-weight: 900;
            color: #0F172A;
            letter-spacing: 0.04em;
            font-variant-numeric: tabular-nums;
            line-height: 1.1;
        }
        .timer-label {
            font-size: 9px;
            color: #94A3B8;
            font-weight: 600;
            margin-top: 1px;
        }
        .timer-icon-wrap {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .timer-icon-wrap svg {
            width: 16px;
            height: 16px;
        }

        /* Question grid */
        #question-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 6px;
        }
        .grid-btn {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 4px;
            border: 1.5px solid #E2E8F0;
            background: #F8FAFC;
            color: #94A3B8;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: border-color 0.15s, background 0.15s, color 0.15s;
        }
        .grid-btn:hover { border-color: #93B4FF; color: #1952E1; }
        .grid-btn.active { border: 2px solid #1952E1; color: #1952E1; background: #EFF4FF; }
        .grid-btn.answered { border-color: #059669; background: #059669; color: #fff; }

        /* Legend */
        .legend-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 2px;
            flex-shrink: 0;
        }

        /* Submit button */
        .btn-submit-exam {
            width: 100%;
            padding: 13px;
            background: #059669;
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.15s;
        }
        .btn-submit-exam:hover { background: #047857; }
        .btn-submit-exam:disabled { background: #94A3B8; cursor: not-allowed; }

        /* â”€â”€â”€ MOBILE: reordered stacked layout â”€â”€â”€ */
        @media (max-width: 767px) {
            #exam-body {
                flex-direction: column;
                overflow-y: auto;
                overflow-x: hidden;
                padding: 12px 14px 80px 14px; /* bottom padding for submit bar */
                gap: 12px;
            }

            /* Question card comes FIRST */
            #col-question {
                order: 1;
                min-height: 420px;
                flex: none;
            }
            #question-body {
                padding: 18px 18px;
            }

            /* Sidebar comes SECOND but reordered internally */
            #col-sidebar {
                width: 100%;
                order: 2;
                gap: 10px;
            }

            /* Hide sidebar timer card on mobile (timer is in the header) */
            #sidebar-timer-card { display: none !important; }

            /* Question grid: compact horizontal scroll on mobile instead of 5-col grid */
            #question-grid {
                display: flex;
                flex-direction: row;
                flex-wrap: nowrap;
                overflow-x: auto;
                gap: 6px;
                padding-bottom: 4px;
            }
            #question-grid::-webkit-scrollbar { height: 3px; }
            #question-grid::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
            .grid-btn {
                width: 36px;
                height: 36px;
                aspect-ratio: 1;
                flex-shrink: 0;
            }

            /* Legends: horizontal row on mobile */
            #legend-block {
                flex-direction: row !important;
                gap: 14px !important;
                flex-wrap: wrap;
            }

            /* Submit card: full width, sticky at bottom on mobile */
            #sidebar-submit-card {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 12px 16px;
                background: #fff;
                border-top: 1px solid #E2E8F0;
                border-radius: 0 !important;
                box-shadow: 0 -4px 16px rgba(0,0,0,0.08);
                z-index: 100;
            }

            .btn-submit-sm { display: flex !important; }
        }

        /* â”€â”€â”€ Cheat warning modal â”€â”€â”€ */
        #cheat-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15,23,42,0.65);
            backdrop-filter: blur(4px);
            z-index: 99999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        #cheat-modal.show { display: flex; }
        .cheat-box {
            background: #fff;
            border-radius: 4px;
            max-width: 380px;
            width: 100%;
            padding: 28px 24px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            border: 1px solid #FECACA;
        }
        .cheat-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        .cheat-box h3 {
            font-size: 14px;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 10px;
        }
        .cheat-box p {
            font-size: 12px;
            color: #64748B;
            line-height: 1.65;
            margin-bottom: 6px;
        }
        .cheat-remaining {
            font-size: 12px;
            font-weight: 900;
            color: #DC2626;
            margin-bottom: 18px;
        }
        .btn-return {
            width: 100%;
            padding: 11px;
            background: #DC2626;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .btn-return:hover { background: #B91C1C; }

        /* scrollbar styling */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }
    </style>
</head>
<body>

<div id="exam-shell">

    <!-- â•â•â•â•â•â•â•â•â•â•â• TOP HEADER â•â•â•â•â•â•â•â•â•â•â• -->
    <header id="exam-header">

        <!-- Row 1 (always visible): Brand + Desktop timer + Quit -->
        <div id="header-row-top" style="display:flex; align-items:center; justify-content:space-between; width:100%;">
            <div style="display:flex; align-items:center; gap:0;">
                <span class="exam-brand">Scriptly Exam</span>
                <span class="exam-subtopic-badge"><?= htmlspecialchars(strtoupper(str_replace('_', ' ', $subtopic))) ?></span>
            </div>

            <!-- Desktop timer (hidden on mobile via CSS) -->
            <div id="timer-wrapper">
                <svg width="15" height="15" fill="none" stroke="#B91C1C" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span id="timer-clock">30:00</span>
            </div>

            <div class="header-actions">
                <button type="button" class="btn-submit-sm" id="btn-submit-mobile" onclick="submitExam(false)">Submit</button>
                <button type="button" class="btn-quit" id="btn-quit">Quit</button>
            </div>
        </div>

        <!-- Row 2 (mobile only): Full-width prominent timer bar -->
        <div id="header-row-timer">
            <svg width="18" height="18" fill="none" stroke="#B91C1C" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="timer-label-sm">Time Remaining:</span>
            <span id="timer-clock-mobile" style="font-size:22px; font-weight:900; color:#B91C1C; letter-spacing:0.06em; font-variant-numeric:tabular-nums;">30:00</span>
        </div>

    </header>

    <?php if ($total_questions === 0): ?>
    <!-- No questions fallback -->
    <div style="flex:1; display:flex; align-items:center; justify-content:center; padding:40px;">
        <div style="max-width:420px; background:#fff; border:1px solid #E2E8F0; border-radius:4px; padding:40px; text-align:center;">
            <div style="font-size:14px; font-weight:800; color:#0F172A; margin-bottom:10px;">No Questions Found</div>
            <p style="font-size:12px; color:#64748B; margin-bottom:20px; line-height:1.65;">
                There are no questions configured yet for <strong><?= htmlspecialchars(str_replace('_', ' ', $subtopic)) ?></strong>. Please return and choose another topic.
            </p>
            <a href="assessment.php" style="display:inline-block; background:#1952E1; color:#fff; font-size:11px; font-weight:800; padding:10px 24px; border-radius:3px; text-decoration:none; text-transform:uppercase; letter-spacing:0.06em;">Return to Hub</a>
        </div>
    </div>
    <?php else: ?>

    <!-- â•â•â•â•â•â•â•â•â•â•â• TWO-COLUMN BODY â•â•â•â•â•â•â•â•â•â•â• -->
    <div id="exam-body">

        <!-- â”€â”€ LEFT: Question Workspace â”€â”€ -->
        <div id="col-question">

            <!-- Question card header -->
            <div id="question-header">
                <span id="lbl-question-index">Question 1 of <?= $total_questions ?></span>
                <span id="lbl-question-type">Multiple Choice</span>
            </div>

            <!-- Progress bar -->
            <div id="progress-track">
                <div id="pb-indicator" style="width: <?= round(1/$total_questions*100, 2) ?>%"></div>
            </div>

            <!-- Scrollable question + answer area -->
            <div id="question-body">
                <div id="question-text"></div>
                <div id="options-divider"></div>
                <div id="options-container"></div>
            </div>

            <!-- Navigation footer -->
            <div id="question-footer">
                <button type="button" class="btn-nav btn-prev-nav" id="btn-prev" onclick="navigateQuestion(-1)" disabled>â† Previous</button>
                <button type="button" class="btn-nav btn-next-nav" id="btn-next" onclick="navigateQuestion(1)">Next â†’</button>
            </div>
        </div>

        <!-- â”€â”€ RIGHT: Sidebar â”€â”€ -->
        <div id="col-sidebar">

            <!-- Timer widget -->
            <div class="sidebar-card" id="sidebar-timer-card">
                <div class="sidebar-card-title">Time Remaining</div>
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <div id="timer-display">30:00</div>
                        <div class="timer-label">Minutes : Seconds</div>
                    </div>
                    <div class="timer-icon-wrap">
                        <svg width="20" height="20" fill="none" stroke="#DC2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Question grid map -->
            <div class="sidebar-card">
                <div class="sidebar-card-title">Questions Overview</div>
                <div id="question-grid">
                    <?php for ($i = 1; $i <= $total_questions; $i++): ?>
                    <button type="button" class="grid-btn<?= $i === 1 ? ' active' : '' ?>" id="nav-btn-<?= $i ?>" onclick="jumpToQuestion(<?= $i ?>)"><?= $i ?></button>
                    <?php endfor; ?>
                </div>

                <!-- Legend -->
                <div id="legend-block" style="display:flex; flex-direction:column; gap:8px; margin-top:18px; padding-top:14px; border-top:1px solid #F1F5F9;">
                    <div class="legend-row">
                        <span class="legend-dot" style="background:#F8FAFC; border:1.5px solid #E2E8F0;"></span> Unanswered
                    </div>
                    <div class="legend-row">
                        <span class="legend-dot" style="background:#EFF4FF; border:2px solid #1952E1;"></span> Current
                    </div>
                    <div class="legend-row">
                        <span class="legend-dot" style="background:#059669;"></span> Answered
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="sidebar-card" id="sidebar-submit-card">
                <button type="button" class="btn-submit-exam" id="btn-submit-exam" onclick="submitExam(false)">
                    <svg width="16" height="16" fill="none" stroke="#fff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Submit Exam
                </button>
            </div>

        </div>

    </div>
    <?php endif; ?>

</div>

<!-- â•â•â•â•â•â•â•â•â•â•â• CHEAT WARNING MODAL â•â•â•â•â•â•â•â•â•â•â• -->
<div id="cheat-modal">
    <div class="cheat-box">
        <div class="cheat-icon">
            <svg width="24" height="24" fill="none" stroke="#DC2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        </div>
        <h3>Tab Switch Detected!</h3>
        <p>You left the exam window. This is a security violation. Do not switch tabs or minimize the browser during the assessment.</p>
        <p class="cheat-remaining">Violations remaining before auto-fail: <strong id="lbl-violations-remaining">2</strong></p>
        <button class="btn-return" onclick="closeCheatWarning()">Return to Exam</button>
    </div>
</div>

<script src="../../assets/js/scriptly-alerts.js"></script>
<?php if ($total_questions > 0): ?>
<script>
const questions    = <?= json_encode($questions) ?>;
const categoryId   = <?= (int)$category['id'] ?>;
const subtopicSlug = <?= json_encode($subtopic) ?>;
const totalQs      = questions.length;

const responses      = {};
let currentIdx       = 0;
let secondsLeft      = 1800; // 30 minutes
let violationCount   = 0;
const maxViolations  = 3;
let timerInterval    = null;

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ Init â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function init() {
    renderQuestion();
    startTimer();
    setupSecurity();
    // hide preloader if present
    const loader = document.getElementById('page-preloader');
    if (loader) { loader.style.opacity = 0; setTimeout(() => loader.remove(), 400); }
}

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ Timer â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function startTimer() {
    const clockHeader  = document.getElementById('timer-clock');
    const clockMobile  = document.getElementById('timer-clock-mobile');
    const clockSidebar = document.getElementById('timer-display');
    const wrapper      = document.getElementById('timer-wrapper');
    const mobileRow    = document.getElementById('header-row-timer');

    timerInterval = setInterval(() => {
        secondsLeft--;

        if (secondsLeft <= 0) {
            clearInterval(timerInterval);
            const ended = '00:00';
            if (clockHeader)  clockHeader.textContent  = ended;
            if (clockMobile)  clockMobile.textContent  = ended;
            if (clockSidebar) clockSidebar.textContent = ended;
            ScriptlyToast.error("Time's up! Your exam is being submitted automatically.", "Time Expired");
            setTimeout(() => submitExam(true), 1200);
            return;
        }

        const m = String(Math.floor(secondsLeft / 60)).padStart(2, '0');
        const s = String(secondsLeft % 60).padStart(2, '0');
        const display = `${m}:${s}`;

        if (clockHeader)  clockHeader.textContent  = display;
        if (clockMobile)  clockMobile.textContent  = display;
        if (clockSidebar) clockSidebar.textContent = display;

        // Critical warning: < 3 minutes
        if (secondsLeft < 180) {
            if (wrapper)   { wrapper.style.background = '#DC2626'; wrapper.style.borderColor = '#DC2626'; }
            if (clockHeader)  clockHeader.style.color = '#fff';
            if (clockSidebar) { clockSidebar.style.color = '#DC2626'; clockSidebar.style.fontSize = '22px'; }
            if (mobileRow) mobileRow.style.background = '#DC2626';
            if (clockMobile)  { clockMobile.style.color = '#fff'; clockMobile.style.fontSize = '20px'; }
        }
    }, 1000);
}

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ Question Render â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function renderQuestion() {
    const q = questions[currentIdx];

    // â”€â”€ Update grid buttons â”€â”€
    for (let i = 1; i <= totalQs; i++) {
        const btn   = document.getElementById(`nav-btn-${i}`);
        const qId   = questions[i-1].id;
        const done  = responses[qId] !== undefined && responses[qId] !== '';
        const here  = (i-1) === currentIdx;
        btn.className = 'grid-btn' + (here ? ' active' : done ? ' answered' : '');
    }

    // â”€â”€ Header labels â”€â”€
    document.getElementById('lbl-question-index').textContent = `Question ${currentIdx + 1} of ${totalQs}`;
    document.getElementById('pb-indicator').style.width = `${((currentIdx + 1) / totalQs * 100).toFixed(2)}%`;

    const typeLbl = document.getElementById('lbl-question-type');
    typeLbl.textContent = q.option_a ? 'Multiple Choice' : 'Written Answer';
    typeLbl.style.background = q.option_a ? '#EFF4FF' : '#F5F3FF';
    typeLbl.style.color      = q.option_a ? '#1952E1' : '#7C3AED';
    typeLbl.style.borderColor= q.option_a ? '#C7D7FD' : '#DDD6FE';

    // â”€â”€ Question text â”€â”€
    const textEl = document.getElementById('question-text');
    textEl.innerHTML = '';

    const raw = q.question_text;
    // Split into prose and code
    const lines = raw.replace(/\\n/g, '\n').split('\n');
    let prose = [], code = [];
    lines.forEach(line => {
        if (/class |console\.|SELECT |echo |typeof |function |return |if \(|\$[a-zA-Z]/.test(line)) {
            code.push(line);
        } else {
            prose.push(line);
        }
    });

    const pEl = document.createElement('div');
    pEl.style.cssText = 'font-size:14px; font-weight:600; color:#1E293B; line-height:1.75; margin-bottom:' + (code.length ? '16px' : '0');
    pEl.textContent = prose.join(' ').trim();
    textEl.appendChild(pEl);

    if (code.length) {
        const pre = document.createElement('pre');
        pre.className = 'code-block';
        pre.textContent = code.join('\n').trim();
        textEl.appendChild(pre);
    }

    // Reset scroll
    document.getElementById('question-body').scrollTop = 0;

    // â”€â”€ Options â”€â”€
    const optEl = document.getElementById('options-container');
    optEl.innerHTML = '';

    if (q.option_a) {
        const letters = ['A','B','C','D'];
        const opts    = [q.option_a, q.option_b, q.option_c, q.option_d];
        opts.forEach((opt, idx) => {
            if (!opt) return;
            const letter = letters[idx];
            const sel    = responses[q.id] === letter;
            const div    = document.createElement('div');
            div.className = 'option-card' + (sel ? ' selected' : '');
            div.onclick = () => recordMCQAnswer(letter);
            div.innerHTML = `
                <div class="option-letter">${letter}</div>
                <div class="option-text">${opt}</div>
                <div class="option-radio">
                    <div class="option-radio-dot"></div>
                </div>
            `;
            optEl.appendChild(div);
        });
    } else {
        const curVal = responses[q.id] || '';
        optEl.innerHTML = `
            <div class="console-bar">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 9l3 3-3 3m5 0h3"/></svg>
                Console Input
            </div>
            <textarea class="console-textarea" id="text-response-input" rows="5"
                placeholder="Type your answer or corrected code here..."
                oninput="recordTextAnswer(this.value)">${curVal}</textarea>
            <p style="font-size:10px; color:#F59E0B; font-weight:700; margin-top:8px; text-transform:uppercase; letter-spacing:.04em;">âš  Case and spacing are sensitive</p>
        `;
    }

    // â”€â”€ Footer buttons â”€â”€
    document.getElementById('btn-prev').disabled = currentIdx === 0;
    const nextBtn = document.getElementById('btn-next');
    if (currentIdx === totalQs - 1) {
        nextBtn.textContent  = 'Finish Review â†‘';
        nextBtn.className    = 'btn-nav btn-finish-nav';
        nextBtn.onclick      = () => jumpToQuestion(1);
    } else {
        nextBtn.textContent  = 'Next â†’';
        nextBtn.className    = 'btn-nav btn-next-nav';
        nextBtn.onclick      = () => navigateQuestion(1);
    }
}

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ Navigation â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function navigateQuestion(dir) {
    currentIdx = Math.max(0, Math.min(totalQs - 1, currentIdx + dir));
    renderQuestion();
}

function jumpToQuestion(num) {
    currentIdx = num - 1;
    renderQuestion();
}

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ Answer recording â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function recordMCQAnswer(letter) {
    responses[questions[currentIdx].id] = letter;
    renderQuestion();
}

function recordTextAnswer(val) {
    responses[questions[currentIdx].id] = val;
}

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ Security â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function setupSecurity() {
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('keydown', e => {
        if (e.key === 'F12' ||
            (e.ctrlKey && ['c','v','x','u'].includes(e.key.toLowerCase())) ||
            (e.ctrlKey && e.shiftKey && e.key === 'I') ||
            (e.metaKey && ['c','v','x'].includes(e.key.toLowerCase()))) {
            e.preventDefault();
            ScriptlyToast.error("Shortcuts are disabled during the exam.");
        }
    });

    window.addEventListener('blur', () => {
        violationCount++;
        const remaining = maxViolations - violationCount;
        if (remaining <= 0) {
            ScriptlyToast.error("Too many tab switches â€” auto-submitting now.", "Disqualified");
            setTimeout(() => submitExam(true), 1200);
        } else {
            document.getElementById('lbl-violations-remaining').textContent = remaining;
            document.getElementById('cheat-modal').classList.add('show');
        }
    });
}

function closeCheatWarning() {
    document.getElementById('cheat-modal').classList.remove('show');
}

// Quit
document.getElementById('btn-quit').addEventListener('click', () => {
    if (confirm("Quit the exam? This will count as a failed attempt.")) {
        clearInterval(timerInterval);
        window.location.href = 'assessment.php';
    }
});

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ Submit â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
async function submitExam(isAuto = false) {
    if (!isAuto && !confirm("Submit your assessment now? Make sure you have reviewed all questions.")) return;
    clearInterval(timerInterval);

    document.querySelectorAll('#btn-submit-exam, #btn-submit-mobile').forEach(b => {
        if (b) { b.disabled = true; b.textContent = 'Gradingâ€¦'; }
    });

    try {
        const res = await fetch('../../api/provider/submit-quiz.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ category_id: categoryId, subtopic: subtopicSlug, answers: responses })
        });
        const result = await res.json();

        if (result.success) {
            if (result.status === 'passed') {
                ScriptlyToast.success(`Passed! Score: ${result.score}%. Bidding rights unlocked.`, "Assessment Passed âœ“");
            } else {
                ScriptlyToast.error(`Failed. Score: ${result.score}%. You may retake from the dashboard.`, "Assessment Failed");
            }
            setTimeout(() => { window.location.href = 'assessment.php'; }, 2200);
        } else {
            ScriptlyToast.error(result.message || "Submission error. Please try again.");
            document.querySelectorAll('#btn-submit-exam, #btn-submit-mobile').forEach(b => {
                if (b) { b.disabled = false; b.textContent = b.id === 'btn-submit-exam' ? 'Submit Exam' : 'Submit'; }
            });
        }
    } catch (e) {
        ScriptlyToast.error("Network error. Please check your connection.");
        document.querySelectorAll('#btn-submit-exam, #btn-submit-mobile').forEach(b => {
            if (b) { b.disabled = false; b.textContent = b.id === 'btn-submit-exam' ? 'Submit Exam' : 'Submit'; }
        });
    }
}

init();
</script>
<?php endif; ?>
</body>
</html>

