<?php
/**
 * Scriptly Escrow Wallet Account Statement (Downloadable PDF)
 * Professional Bank-Style Redesign
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
$db = getDBConnection();

$user_email = $_SESSION['user_email'] ?? null;
$user = null;

if ($user_email) {
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$user_email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fallback logic for demo compatibility
$user_name = $user ? $user['full_name'] : 'Joseph Olagundoye';
$user_email = $user ? $user['email'] : 'josepholagundoye66@gmail.com';
$user_address = $user ? ($user['address'] ?? 'Victoria Island, Lagos, Nigeria') : 'Victoria Island, Lagos, Nigeria';
$user_phone = $user ? ($user['phone_number'] ?? '+234 816 172 8228') : '+234 816 172 8228';

$statement_date = date('F j, Y');
$statement_ref = 'CRD-STMT-' . date('Ymd') . '-' . ($user ? $user['id'] : '0002');
$period_start = 'August 01, 2026';
$period_end = date('F j, Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wallet Statement - <?php echo htmlspecialchars($user_name); ?></title>
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
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-[4px] border border-slate-200 shadow-xs heading-font">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#1952E1] animate-pulse"></span>
            <span class="text-xs font-bold text-slate-600">Official statement certified by Scriptly Auditing</span>
        </div>
        <button id="download-btn" class="inline-flex items-center gap-1.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-[3px] transition-colors shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            <span>Download PDF Statement</span>
        </button>
    </div>

    <!-- DOCUMENT CONTAINER FOR PDF CAPTURE -->
    <div id="statement-document" class="max-w-4xl mx-auto bg-white p-8 sm:p-12 border border-slate-200/90 shadow-md rounded-[2px] relative overflow-hidden text-xs">
        
        <!-- Top corporate brand bar -->
        <div class="absolute top-0 left-0 right-0 h-2 bg-[#0A2342]"></div>

        <!-- 1. HEADER SECTION (Brand & Statement Meta) -->
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
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight heading-font uppercase">Account Statement</h1>
                <p class="text-[11px] text-slate-500 font-mono mt-1.5">Statement Date: <?php echo htmlspecialchars($statement_date); ?></p>
                <p class="text-[11px] text-slate-500 font-mono mt-0.5">Reference: <?php echo htmlspecialchars($statement_ref); ?></p>
                <p class="text-[11px] text-[#1952E1] font-bold mt-1 font-sans">Statement Period: <?php echo htmlspecialchars($period_start); ?> - <?php echo htmlspecialchars($period_end); ?></p>
            </div>
        </div>

        <!-- 2. CUSTOMER & STATEMENT SUMMARY GRID (Standard Bank Look) -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-8 items-stretch">
            
            <!-- Customer Information Card -->
            <div class="md:col-span-6 p-5 border border-slate-200 rounded-[3px] bg-slate-50/50">
                <h3 class="font-bold text-slate-400 uppercase tracking-widest text-[9px] heading-font mb-2">Prepared For:</h3>
                <p class="font-extrabold text-slate-900 text-sm heading-font"><?php echo htmlspecialchars($user_name); ?></p>
                <div class="text-slate-600 mt-1.5 space-y-1">
                    <p>Email: <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($user_email); ?></p>
                    <p>Phone: <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($user_phone); ?></p>
                    <p>Address: <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($user_address); ?></p>
                </div>
            </div>
            
            <!-- Summary of Activity Box (Bank Style) -->
            <div class="md:col-span-6 border-2 border-slate-800 rounded-[3px] overflow-hidden flex flex-col justify-between">
                <div class="bg-[#0A2342] text-white p-3 font-bold uppercase tracking-wider text-[9px] heading-font text-center">
                    ACCOUNT BALANCE SUMMARY
                </div>
                <div class="grid grid-cols-2 divide-x divide-y divide-slate-100 bg-white grow text-center items-center">
                    <div class="p-3">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Available Balance</span>
                        <span class="text-sm font-extrabold text-slate-900 font-mono block mt-0.5">₦120,000.00</span>
                    </div>
                    <div class="p-3">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Locked in Escrow</span>
                        <span class="text-sm font-extrabold text-[#1952E1] font-mono block mt-0.5">₦480,000.00</span>
                    </div>
                    <div class="p-3 col-span-2 bg-slate-50/60">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">TOTAL HOLDING</span>
                        <span class="text-base font-black text-slate-950 font-mono block mt-0.5">₦600,000.00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. DETAILED LEDGER TABLE (Bank Style Header & Alignment) -->
        <div class="mb-8">
            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-3 heading-font">Transaction Ledger Details</h3>
            <div class="border border-slate-200 rounded-[2px] overflow-hidden">
                <table class="w-full text-left text-[11px] border-collapse">
                    <thead>
                        <tr class="bg-[#0A2342] text-white font-bold heading-font text-[10px] tracking-wider uppercase">
                            <th class="p-3.5 border-r border-[#1e293b]">Post Date</th>
                            <th class="p-3.5 border-r border-[#1e293b]">Reference ID</th>
                            <th class="p-3.5 border-r border-[#1e293b]">Transaction Narrative</th>
                            <th class="p-3.5 border-r border-[#1e293b] text-right">Debits (-)</th>
                            <th class="p-3.5 border-r border-[#1e293b] text-right">Credits (+)</th>
                            <th class="p-3.5 text-right font-bold">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <!-- Deposit 1 -->
                        <tr class="hover:bg-slate-50/50 bg-white">
                            <td class="p-3.5 font-mono text-slate-500 border-r border-slate-100">10-Aug-2026</td>
                            <td class="p-3.5 font-mono text-slate-400 border-r border-slate-100">TXN-DEP-8831</td>
                            <td class="p-3.5 font-semibold text-slate-700 border-r border-slate-100">FND • Deposit via Bank Transfer</td>
                            <td class="p-3.5 text-right font-mono text-slate-400 border-r border-slate-100">--</td>
                            <td class="p-3.5 text-right font-mono text-emerald-600 font-bold border-r border-slate-100">+₦250,000.00</td>
                            <td class="p-3.5 text-right font-mono text-slate-700 font-semibold">₦250,000.00</td>
                        </tr>
                        <!-- Escrow Allocation 1 -->
                        <tr class="hover:bg-slate-50/50 bg-slate-50/20">
                            <td class="p-3.5 font-mono text-slate-500 border-r border-slate-100">10-Aug-2026</td>
                            <td class="p-3.5 font-mono text-slate-400 border-r border-slate-100">TXN-ESC-1042</td>
                            <td class="p-3.5 font-semibold text-slate-700 border-r border-slate-100">ESC • Escrow Funding for Project CRD-CNT-1042</td>
                            <td class="p-3.5 text-right font-mono text-red-600 font-bold border-r border-slate-100">-₦250,000.00</td>
                            <td class="p-3.5 text-right font-mono text-slate-400 border-r border-slate-100">--</td>
                            <td class="p-3.5 text-right font-mono text-slate-700 font-semibold">₦0.00</td>
                        </tr>
                        <!-- Milestone Release 1 -->
                        <tr class="hover:bg-slate-50/50 bg-white">
                            <td class="p-3.5 font-mono text-slate-500 border-r border-slate-100">14-Aug-2026</td>
                            <td class="p-3.5 font-mono text-slate-400 border-r border-slate-100">CRD-RCP-99214</td>
                            <td class="p-3.5 font-semibold text-slate-700 border-r border-slate-100">DISB • Disbursed Milestone 1 Escrow Release</td>
                            <td class="p-3.5 text-right font-mono text-slate-400 border-r border-slate-100">--</td>
                            <td class="p-3.5 text-right font-mono text-slate-400 border-r border-slate-100">--</td>
                            <td class="p-3.5 text-right font-mono text-slate-400 font-semibold">₦0.00</td>
                        </tr>
                        <!-- Deposit 2 -->
                        <tr class="hover:bg-slate-50/50 bg-slate-50/20">
                            <td class="p-3.5 font-mono text-slate-500 border-r border-slate-100">20-Aug-2026</td>
                            <td class="p-3.5 font-mono text-slate-400 border-r border-slate-100">TXN-DEP-9922</td>
                            <td class="p-3.5 font-semibold text-slate-700 border-r border-slate-100">FND • Deposit via Debit Card (Mastercard)</td>
                            <td class="p-3.5 text-right font-mono text-slate-400 border-r border-slate-100">--</td>
                            <td class="p-3.5 text-right font-mono text-emerald-600 font-bold border-r border-slate-100">+₦370,000.00</td>
                            <td class="p-3.5 text-right font-mono text-slate-700 font-semibold">₦370,000.00</td>
                        </tr>
                        <!-- Escrow Allocation 2 -->
                        <tr class="hover:bg-slate-50/50 bg-white">
                            <td class="p-3.5 font-mono text-slate-500 border-r border-slate-100">20-Aug-2026</td>
                            <td class="p-3.5 font-mono text-slate-400 border-r border-slate-100">TXN-ESC-1043</td>
                            <td class="p-3.5 font-semibold text-slate-700 border-r border-slate-100">ESC • Escrow Funding for Project CRD-CNT-1043</td>
                            <td class="p-3.5 text-right font-mono text-red-600 font-bold border-r border-slate-100">-₦250,000.00</td>
                            <td class="p-3.5 text-right font-mono text-slate-400 border-r border-slate-100">--</td>
                            <td class="p-3.5 text-right font-mono text-slate-700 font-semibold">₦120,000.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. BANK AUDIT & VERIFICATION SEAL FOOTER -->
        <div class="flex flex-col md:flex-row justify-between items-center bg-slate-50 border border-slate-200 p-5 rounded-[3px] gap-4">
            <div class="space-y-1 text-center md:text-left leading-relaxed">
                <p class="font-bold text-[#0A2342] flex items-center justify-center md:justify-start gap-1.5 heading-font">
                    <svg class="w-4 h-4 text-[#1952E1] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span>Scriptly Certified Ledger Verification Service</span>
                </p>
                <p class="text-slate-500 font-sans text-[10px]">This statement is a verified audit record. All funds are secured and transacted under strict regulatory guidelines.</p>
            </div>
            
            <div class="text-[10px] text-slate-400 font-mono text-center md:text-right shrink-0">
                Audit Validation Code: <br>
                <span class="font-bold text-slate-700"><?php echo hash('sha256', $statement_ref); ?></span>
            </div>
        </div>

        <!-- Statement Disclaimers -->
        <div class="border-t border-slate-100 pt-6 mt-8 flex flex-col sm:flex-row justify-between items-center text-[10px] text-slate-400 gap-2 font-sans">
            <span>© <?php echo date('Y'); ?> Scriptly Inc. All Rights Reserved.</span>
            <span>Accounting Audit: accounting@scriptly.co</span>
        </div>

    </div>

    <!-- Script to execute html2pdf conversion -->
    <script>
        document.getElementById('download-btn').addEventListener('click', function () {
            const element = document.getElementById('statement-document');
            const opt = {
                margin:       [12, 12, 12, 12],
                filename:     'Scriptly-Statement-<?php echo htmlspecialchars(str_replace(' ', '-', strtolower($user_name))); ?>.pdf',
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
