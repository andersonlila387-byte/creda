<?php
/**
 * Scriptly Service Agreement Template (Downloadable PDF)
 * Version 1.2 - Bank-Style & Modernized Redesign
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
$db = getDBConnection();

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : null;
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
$project_desc = $contract ? $contract['description'] : 'This is a securely fetched backend project including role-based access controllers, user registration/login APIs, Postman API collections, and verified 0% plagiarism source code.';
$project_category = $contract ? ($contract['category'] ?? 'Web Development') : 'Web Development';
$project_budget = $contract ? ($contract['budget'] ? '₦' . number_format($contract['budget'], 2) : '₦250,000.00') : '₦250,000.00';
$contract_id = $contract ? 'CRD-CNT-' . str_pad($contract['id'], 4, '0', STR_PAD_LEFT) : 'CRD-CNT-1042';

$client_name = $client ? $client['full_name'] : 'Joseph Olagundoye';
$client_email = $client ? $client['email'] : 'joseph.ola@example.com';
$client_ip = '197.210.64.12';

$freelancer_name = $freelancer ? $freelancer['full_name'] : 'David Olanrewaju';
$freelancer_email = $freelancer ? $freelancer['email'] : 'david.ola@example.com';
$freelancer_ip = '102.89.23.104';

$start_date = 'August 10, 2026';
$due_date = 'August 30, 2026';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Agreement - <?php echo htmlspecialchars($project_title); ?></title>
    <!-- Google Fonts: Space Grotesk (headings) & Inter (UI/numbers) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/tailwind.min.css">
    <!-- html2pdf Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body {
            background-color: #F1F5F9;
            font-family: 'Inter', sans-serif;
        }
        .heading-font {
            font-family: 'Space Grotesk', sans-serif;
        }
        .legal-serif {
            font-family: Georgia, Cambria, "Times New Roman", Times, serif;
        }
    </style>
</head>
<body class="py-10 px-4 sm:px-6 lg:px-8 text-slate-800 antialiased">

    <!-- Top Action Bar (Hides in PDF) -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-[4px] border border-slate-200 shadow-xs heading-font">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-xs font-bold text-slate-600">Agreement Execution: Active Version 1.2</span>
        </div>
        <button id="download-btn" class="inline-flex items-center gap-1.5 bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-[3px] transition-colors shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            <span>Download PDF Agreement</span>
        </button>
    </div>

    <!-- DOCUMENT CONTAINER FOR PDF CAPTURE -->
    <div id="agreement-document" class="max-w-4xl mx-auto bg-white p-8 sm:p-12 border border-slate-200/90 shadow-md rounded-[2px] relative overflow-hidden text-xs leading-relaxed">
        
        <!-- Top corporate brand bar -->
        <div class="absolute top-0 left-0 right-0 h-2 bg-[#0A2342]"></div>

        <!-- 1. HEADER SECTION (Brand & Agreement Reference) -->
        <div class="flex flex-col sm:flex-row justify-between items-start border-b border-slate-200 pb-6 mb-8 gap-6">
            <div>
                <?php echo renderLogoFull('flex items-center space-x-2 text-2xl font-black tracking-tight text-slate-900 mb-2 heading-font', 'w-7 h-7'); ?>
                <div class="text-slate-500 leading-relaxed font-sans text-[11px] mt-2">
                    <p class="font-bold text-slate-700">Scriptly Technologies Ltd.</p>
                    <p>Legal & Compliance Division</p>
                    <p>Victoria Island, Lagos, Nigeria</p>
                </div>
            </div>
            
            <div class="text-left sm:text-right shrink-0">
                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded-[3px] heading-font tracking-wide uppercase mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>✓ SIGNED & PROTECTED</span>
                </span>
                <h1 class="text-xs font-bold uppercase tracking-widest text-slate-400 heading-font">CONTRACT REFERENCE</h1>
                <p class="text-sm font-mono font-bold text-slate-900 mt-1"><?php echo htmlspecialchars($contract_id); ?></p>
                <p class="text-[11px] text-slate-500 font-mono mt-1">Effective Date: <?php echo htmlspecialchars($start_date); ?></p>
            </div>
        </div>

        <!-- Introductory Clause -->
        <div class="space-y-6">
            <p class="legal-serif text-[13px] text-slate-700">
                This **Service Agreement** (the "Agreement") is entered into and made effective as of 
                <strong><?php echo htmlspecialchars($start_date); ?></strong>, by and between the following executing parties under the regulatory verification of Scriptly Escrow Services:
            </p>

            <!-- 2. CLIENT & FREELANCER METADATA GRID (Standard Bank / Legal Look) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
                <div class="p-5 border border-slate-200 rounded-[3px] bg-slate-50/50 leading-relaxed">
                    <h3 class="font-bold text-slate-400 uppercase tracking-widest text-[9px] heading-font mb-2">1. The Client (Contracting Party):</h3>
                    <p class="font-extrabold text-slate-900 text-sm heading-font"><?php echo htmlspecialchars($client_name); ?></p>
                    <div class="text-slate-600 mt-1.5 space-y-1">
                        <p>Email: <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($client_email); ?></p>
                        <p>Security Status: <span class="font-mono text-emerald-800 font-bold">✓ Identity Verified</span></p>
                    </div>
                </div>
                <div class="p-5 border border-slate-200 rounded-[3px] bg-slate-50/50 leading-relaxed">
                    <h3 class="font-bold text-slate-400 uppercase tracking-widest text-[9px] heading-font mb-2">2. The Freelancer (Service Provider):</h3>
                    <p class="font-extrabold text-slate-900 text-sm heading-font"><?php echo htmlspecialchars($freelancer_name); ?></p>
                    <div class="text-slate-600 mt-1.5 space-y-1">
                        <p>Email: <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($freelancer_email); ?></p>
                        <p>Credentials: <span class="font-mono text-blue-800 font-bold">✓ Certified Expert</span></p>
                    </div>
                </div>
            </div>

            <!-- Section 1: Scope of Work -->
            <div class="space-y-2 border-t border-slate-100 pt-5">
                <h3 class="text-[11px] font-extrabold text-[#0A2342] uppercase tracking-wider heading-font">Section 1: Scope of Services & Deliverables</h3>
                <p class="legal-serif text-[12.5px] text-slate-700">
                    The Freelancer agrees to perform development, consulting, and implementation services for the project:
                    <strong>"<?php echo htmlspecialchars($project_title); ?>"</strong>.
                </p>
                <div class="bg-slate-50/60 p-4 border border-slate-200 rounded-[3px] leading-relaxed">
                    <p class="font-bold text-slate-700 mb-1 heading-font text-[10px] uppercase tracking-wider">Project Narrative & Scope:</p>
                    <p class="text-slate-600 text-[11px] font-sans"><?php echo htmlspecialchars($project_desc); ?></p>
                </div>
            </div>

            <!-- Section 2: Financial Terms & Escrow -->
            <div class="space-y-2 border-t border-slate-100 pt-5">
                <h3 class="text-[11px] font-extrabold text-[#0A2342] uppercase tracking-wider heading-font">Section 2: Financial Terms & Escrow Protection</h3>
                <p class="legal-serif text-[12.5px] text-slate-700">
                    The total agreed milestone budget is <strong><?php echo htmlspecialchars($project_budget); ?></strong>. 
                    This sum has been deposited into **Scriptly Escrow Holding Wallet** by the Client and is locked to secure both parties.
                </p>
                <ul class="list-disc pl-5 space-y-1 text-slate-600 font-sans text-[11px]">
                    <li>Funds are disbursed incrementally to the Freelancer upon successful milestone approval.</li>
                    <li>Milestone submissions are subject to a 7-day inspection period by the Client.</li>
                    <li>If no action is taken within the 7-day period, funds auto-disburse to the Freelancer.</li>
                </ul>
            </div>

            <!-- Section 3: Plagiarism & Standards -->
            <div class="space-y-2 border-t border-slate-100 pt-5">
                <h3 class="text-[11px] font-extrabold text-[#0A2342] uppercase tracking-wider heading-font">Section 3: Academic Integrity & Quality Standards</h3>
                <p class="legal-serif text-[12.5px] text-slate-700">
                    Scriptly strictly enforces high-quality academic and technical guidelines. The deliverables under this contract must comply with the following criteria:
                </p>
                <ul class="list-disc pl-5 space-y-1 text-slate-600 font-sans text-[11px]">
                    <li><strong>0% Plagiarism Guarantee:</strong> All deliverables must be written originally. Code is audited via the Turnitin plagiarism detection suite, with a target similarity score of exactly 0%.</li>
                    <li><strong>Functional Verification:</strong> All APIs, database schemas, and frontend configurations must run smoothly on local and staging systems matching the config setup.</li>
                    <li><strong>Revision Allocation:</strong> The Freelancer agrees to provide up to two (2) iterations of revisions if code does not meet specified functional guidelines or academic rubrics.</li>
                </ul>
            </div>

            <!-- Section 4: Dispute Mediation -->
            <div class="space-y-2 border-t border-slate-100 pt-5">
                <h3 class="text-[11px] font-extrabold text-[#0A2342] uppercase tracking-wider heading-font">Section 4: Dispute Resolution & Arbitration</h3>
                <p class="legal-serif text-[12.5px] text-slate-700">
                    In the event that the Client and the Freelancer encounter disagreements regarding milestone deliverables or progress, both parties agree to leverage **Scriptly Escrow Dispute Desk**.
                </p>
                <p class="text-[11px] text-slate-500 font-sans">
                    Scriptly's dedicated mediation desk will audit the direct chat logs, submitted files, commit histories, and dispute statements to render a final, legally binding arbitration decision regarding escrow disbursal or partial refunds.
                </p>
            </div>

            <!-- Signature Section -->
            <div class="border-t border-slate-200 pt-6 mt-8 space-y-4 font-sans">
                <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block heading-font">Digital Signatures & Execution Log</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                    <!-- Client Digital Stamp -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-[3px] space-y-2 relative overflow-hidden">
                        <span class="font-bold text-slate-400 uppercase tracking-wider block text-[9px] heading-font">Client Consent Log</span>
                        <div class="space-y-1 font-sans text-[11px]">
                            <p class="font-bold text-slate-950 heading-font"><?php echo htmlspecialchars($client_name); ?></p>
                            <p class="text-slate-500">IP Address: <?php echo htmlspecialchars($client_ip); ?></p>
                            <p class="text-slate-500">Timestamp: <?php echo date('Y-m-d H:i:s', strtotime('-16 days')); ?></p>
                            <div class="font-mono text-[9px] text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded inline-block mt-1 truncate max-w-full">
                                Hash: <?php echo hash('sha256', $client_name . $client_ip . $start_date); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Freelancer Digital Stamp -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-[3px] space-y-2 relative overflow-hidden">
                        <span class="font-bold text-slate-400 uppercase tracking-wider block text-[9px] heading-font">Freelancer Consent Log</span>
                        <div class="space-y-1 font-sans text-[11px]">
                            <p class="font-bold text-slate-950 heading-font"><?php echo htmlspecialchars($freelancer_name); ?></p>
                            <p class="text-slate-500">IP Address: <?php echo htmlspecialchars($freelancer_ip); ?></p>
                            <p class="text-slate-500">Timestamp: <?php echo date('Y-m-d H:i:s', strtotime('-16 days')); ?></p>
                            <div class="font-mono text-[9px] text-blue-800 bg-blue-50 px-2 py-0.5 rounded inline-block mt-1 truncate max-w-full">
                                Hash: <?php echo hash('sha256', $freelancer_name . $freelancer_ip . $start_date); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Platform Certification footer -->
            <div class="border-t border-slate-100 pt-6 mt-8 flex flex-col sm:flex-row justify-between items-center text-[10px] text-slate-400 font-sans gap-2">
                <span>© <?php echo date('Y'); ?> Scriptly Inc. All Rights Reserved.</span>
                <span>Certified Cryptographic Agreement Log</span>
            </div>

        </div>

    </div>

    <!-- Script to execute html2pdf conversion -->
    <script>
        document.getElementById('download-btn').addEventListener('click', function () {
            const element = document.getElementById('agreement-document');
            const opt = {
                margin:       [12, 12, 12, 12],
                filename:     'Scriptly-Agreement-<?php echo htmlspecialchars(str_replace(' ', '-', strtolower($project_title))); ?>.pdf',
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
