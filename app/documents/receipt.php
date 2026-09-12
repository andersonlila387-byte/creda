<?php
/**
 * Scriptly Escrow Milestone Payment Receipt (Downloadable PDF)
 * Professional Bank-Style Redesign
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
$db = getDBConnection();

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : null;
$milestone_num = isset($_GET['milestone']) ? (int)$_GET['milestone'] : 1;
$contract = null;
$client = null;
$freelancer = null;

if ($slug) {
    // Fetch project
    $stmt = $db->prepare("SELECT * FROM projects WHERE slug = ?");
    $stmt->execute([$slug]);
    $contract = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($contract) {
    // Fetch client
    $c_stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $c_stmt->execute([$contract['client_id']]);
    $client = $c_stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch hired freelancer from accepted proposals
    $f_stmt = $db->prepare("
        SELECT u.* FROM proposals p 
        JOIN users u ON p.provider_id = u.id 
        WHERE p.project_id = ? AND p.status = 'accepted' 
        LIMIT 1
    ");
    $f_stmt->execute([$contract['id']]);
    $freelancer = $f_stmt->fetch(PDO::FETCH_ASSOC);
}

// Fallback logic for demo compatibility
$project_title = $contract ? $contract['title'] : 'Full-Stack PHP Web Portal with Escrow System';
$client_name = $client ? $client['full_name'] : 'Joseph Olagundoye';
$client_email = $client ? $client['email'] : 'joseph.ola@example.com';
$freelancer_name = $freelancer ? $freelancer['full_name'] : 'David Olanrewaju';
$freelancer_email = $freelancer ? $freelancer['email'] : 'david.ola@example.com';

// Mock/Fallback Milestone details based on URL query
if ($milestone_num === 1) {
    $milestone_title = "Milestone 1: Database Schema & Authentication Middleware";
    $amount = 100000;
    $receipt_id = "CRD-RCP-99214";
    $payment_date = "August 14, 2026";
} else {
    $milestone_title = "Milestone 2: RESTful Endpoints & Turnitin Clean Code";
    $amount = 150000;
    $receipt_id = "CRD-RCP-99215";
    $payment_date = "August 21, 2026";
}

$platform_fee = $amount * 0.05; // 5% platform service fee
$net_amount = $amount - $platform_fee;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt - <?php echo htmlspecialchars($receipt_id); ?></title>
    <!-- Google Fonts: Space Grotesk (headings) & Inter (UI/numbers) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/tailwind.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body {
            background-color: #F1F5F9;
            font-family: 'Inter', sans-serif;
        }
        .heading-font {
            font-family: 'Space Grotesk', sans-serif;
        }
        .mono-num {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }
    </style>
</head>
<body class="py-10 px-4 sm:px-6 lg:px-8 text-slate-800 antialiased">

    <!-- Top Action Bar (Hides in PDF) -->
    <div class="max-w-3xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-[4px] border border-slate-200 shadow-xs heading-font">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-xs font-bold text-slate-600">Disbursed successfully from Escrow Wallet</span>
        </div>
        <button id="download-btn" class="inline-flex items-center gap-1.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-[3px] transition-colors shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            <span>Download PDF Receipt</span>
        </button>
    </div>

    <!-- DOCUMENT CONTAINER FOR PDF CAPTURE -->
    <div id="receipt-document" class="max-w-3xl mx-auto bg-white p-8 sm:p-10 border border-slate-200/90 shadow-md rounded-[2px] relative overflow-hidden text-xs">
        
        <!-- Top corporate brand bar -->
        <div class="absolute top-0 left-0 right-0 h-2 bg-[#0A2342]"></div>

        <!-- 1. HEADER SECTION (Brand & Receipt ID) -->
        <div class="flex flex-col sm:flex-row justify-between items-start border-b border-slate-200 pb-6 mb-8 gap-6">
            <div>
                <?php echo renderLogoFull('flex items-center space-x-2 text-2xl font-black tracking-tight text-slate-900 mb-2 heading-font', 'w-7 h-7'); ?>
                <div class="text-slate-500 leading-relaxed font-sans text-[11px] mt-2">
                    <p class="font-bold text-slate-700">Scriptly Technologies Ltd.</p>
                    <p>Financial Services Division</p>
                    <p>Victoria Island, Lagos, Nigeria</p>
                </div>
            </div>
            
            <div class="text-left sm:text-right shrink-0">
                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded-[3px] heading-font tracking-wide uppercase mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>PAID & RELEASED</span>
                </span>
                <h1 class="text-xs font-bold uppercase tracking-widest text-slate-400 heading-font">Receipt Reference</h1>
                <p class="text-sm font-mono font-bold text-slate-900 mt-1"><?php echo htmlspecialchars($receipt_id); ?></p>
                <p class="text-[11px] text-slate-500 font-mono mt-1">Payment Date: <?php echo htmlspecialchars($payment_date); ?></p>
            </div>
        </div>

        <!-- 2. CLIENT & FREELANCER METADATA GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 items-stretch">
            <div class="p-5 border border-slate-200 rounded-[3px] bg-slate-50/50 leading-relaxed">
                <h3 class="font-bold text-slate-400 uppercase tracking-widest text-[9px] heading-font mb-2">Billed To (Client):</h3>
                <p class="font-extrabold text-slate-900 text-sm heading-font"><?php echo htmlspecialchars($client_name); ?></p>
                <div class="text-slate-600 mt-1.5 space-y-1">
                    <p>Email: <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($client_email); ?></p>
                    <p>Payment Source: <span class="font-semibold text-slate-800">Scriptly Escrow Wallet</p>
                </div>
            </div>
            <div class="p-5 border border-slate-200 rounded-[3px] bg-slate-50/50 leading-relaxed">
                <h3 class="font-bold text-slate-400 uppercase tracking-widest text-[9px] heading-font mb-2">Payee (Freelancer):</h3>
                <p class="font-extrabold text-slate-900 text-sm heading-font"><?php echo htmlspecialchars($freelancer_name); ?></p>
                <div class="text-slate-600 mt-1.5 space-y-1">
                    <p>Email: <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($freelancer_email); ?></p>
                    <p>Status: <span class="font-mono text-emerald-800 font-bold">✓ Payout Cleared</span></p>
                </div>
            </div>
        </div>

        <!-- 3. LEDGER TABLE (Bank Style Header & Alignment) -->
        <div class="mb-8">
            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-3 heading-font">Transaction Details</h3>
            <div class="border border-slate-200 rounded-[2px] overflow-hidden">
                <table class="w-full text-left text-[11px] border-collapse">
                    <thead>
                        <tr class="bg-[#0A2342] text-white font-bold heading-font text-[10px] tracking-wider uppercase">
                            <th class="p-3.5 border-r border-[#1e293b] w-1/2">Milestone Description / Narrative</th>
                            <th class="p-3.5 border-r border-[#1e293b] text-right">Gross Amount</th>
                            <th class="p-3.5 border-r border-[#1e293b] text-right">Platform Fee (5%)</th>
                            <th class="p-3.5 text-right font-bold">Net Disbursed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-4 border-r border-slate-100">
                                <p class="font-bold text-slate-900 text-[12px] heading-font"><?php echo htmlspecialchars($milestone_title); ?></p>
                                <p class="text-[10px] text-slate-500 mt-1 font-sans">Project: <?php echo htmlspecialchars($project_title); ?></p>
                                <p class="text-[9px] text-slate-400 mt-0.5 font-sans">Protection protocol: Plagiarism-Free (Turnitin 0% verified)</p>
                            </td>
                            <td class="p-4 text-right font-mono text-slate-700 border-r border-slate-100">₦<?php echo number_format($amount, 2); ?></td>
                            <td class="p-4 text-right font-mono text-slate-400 border-r border-slate-100">-₦<?php echo number_format($platform_fee, 2); ?></td>
                            <td class="p-4 text-right font-mono text-emerald-700 font-extrabold">₦<?php echo number_format($net_amount, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. TOTAL BREAKDOWN SUMMARY -->
        <div class="flex justify-end mb-10">
            <div class="w-full sm:w-1/2 space-y-2.5 text-[11px] border-t border-slate-200 pt-4">
                <div class="flex justify-between text-slate-500">
                    <span class="font-sans">Subtotal Milestone Budget:</span>
                    <span class="font-mono">₦<?php echo number_format($amount, 2); ?></span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span class="font-sans">Plagiarism Scan & Escrow Auditing Fee:</span>
                    <span class="font-mono">-₦<?php echo number_format($platform_fee, 2); ?></span>
                </div>
                <div class="flex justify-between font-black text-slate-900 border-t border-slate-200 pt-3 text-xs heading-font">
                    <span>Disbursed Payout Total:</span>
                    <span class="font-mono text-[#1952E1] text-[14px]">₦<?php echo number_format($net_amount, 2); ?></span>
                </div>
            </div>
        </div>

        <!-- 5. VERIFICATION NOTICE -->
        <div class="flex flex-col md:flex-row justify-between items-center bg-slate-50 border border-slate-200 p-5 rounded-[3px] gap-4">
            <div class="space-y-1 text-center md:text-left leading-relaxed">
                <p class="font-bold text-[#0A2342] flex items-center justify-center md:justify-start gap-1.5 heading-font">
                    <svg class="w-4 h-4 text-[#1952E1] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span>Scriptly Escrow Protection Shield Certified</span>
                </p>
                <p class="text-slate-500 font-sans text-[10px]">This is a verified transaction record. Released funds have been disbursed from secure escrow reserves.</p>
            </div>
            
            <div class="text-[10px] text-slate-400 font-mono text-center md:text-right shrink-0">
                Receipt Security Hash: <br>
                <span class="font-bold text-slate-700"><?php echo hash('sha256', $receipt_id . $amount . $payment_date); ?></span>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-slate-100 pt-6 mt-8 flex flex-col sm:flex-row justify-between items-center text-[10px] text-slate-400 gap-2 font-sans">
            <span>© <?php echo date('Y'); ?> Scriptly Inc. All Rights Reserved.</span>
            <span>Support Email: billing@scriptly.co</span>
        </div>

    </div>

    <!-- Script to execute html2pdf conversion -->
    <script>
        document.getElementById('download-btn').addEventListener('click', function () {
            const element = document.getElementById('receipt-document');
            const opt = {
                margin:       [12, 12, 12, 12],
                filename:     'Scriptly-Receipt-<?php echo htmlspecialchars($receipt_id); ?>.pdf',
                image:        { type: 'jpeg', quality: 0.99 },
                html2canvas:  { scale: 2.5, useCORS: true, letterRendering: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // Generate and save the PDF
            html2pdf().set(opt).from(element).save();
        });
    </script>
</body>
</html>
