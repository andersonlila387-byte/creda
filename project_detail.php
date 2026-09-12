<?php
$project_id = $_GET['id'] ?? 1;

$page_title = "Full-Stack Custom PHP & Tailwind Web Application - Scriptly Marketplace";
$page_description = "View scope requirements, milestone schedule, and submit a proposal for this funded project listing on Scriptly.";
$active_page = "projects";
$breadcrumb = [
    'category' => 'Software Development',
    'title' => 'Full-Stack Custom PHP & Tailwind Web Application',
    'subtitle' => 'Fixed Budget: ₦250,000 • 100% Upfront Funded Milestone Escrow',
    'bg_image' => 'assets/breadcrumbs/legal_bg.jpg'
];
include 'includes/header.php';
?>

    <!-- Project Detail Main Container -->
    <main class="max-w-7xl mx-auto px-3 sm:px-6 py-12">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- Left 8 Cols: Project Details & Scope -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Summary Card -->
                <div class="bg-white rounded-[3px] p-8 border border-gray-200/80 shadow-sm space-y-6">
                    <div class="flex items-center justify-between flex-wrap gap-3 border-b border-gray-100 pb-5">
                        <div class="flex items-center gap-2">
                            <span class="bg-blue-50 text-blue-600 font-extrabold text-xs px-3 py-1 rounded-[3px] uppercase">Software Development</span>
                            <span class="bg-emerald-50 text-emerald-700 font-extrabold text-xs px-3 py-1 rounded-full flex items-center gap-1">🔒 100% Escrow Funded</span>
                        </div>
                        <span class="text-xs text-gray-400 font-medium">Posted 2 hours ago</span>
                    </div>

                    <h1 class="font-serif text-2xl sm:text-4xl font-bold text-brand-dark leading-tight">
                        Full-Stack Custom PHP & Tailwind Web Application
                    </h1>

                    <div class="prose max-w-none text-xs sm:text-sm text-gray-600 leading-relaxed space-y-4">
                        <p>
                            We are seeking a highly skilled, pre-assessed Senior Full-Stack PHP Developer to construct a responsive, modular web application portal with clean MVC architecture, REST API endpoints, and a MySQL relational database.
                        </p>
                        <h4 class="font-bold text-brand-dark text-sm pt-2">Key Scope & Deliverable Requirements:</h4>
                        <ul class="list-disc pl-5 space-y-1.5 text-xs text-gray-600">
                            <li>Clean object-oriented PHP 8.2 backend codebase with secure PDO database access.</li>
                            <li>Responsive Tailwind CSS frontend user interface following 3px border-radius design tokens.</li>
                            <li>Real-time chat integration for live communication and file uploads.</li>
                            <li>Comprehensive API endpoints with JSON validation and authentication guards.</li>
                            <li>Optimized database schema migrations with indexing for high-concurrency queries.</li>
                        </ul>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-700 block mb-2">Required Verified Skills:</span>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-xs font-semibold bg-gray-100 text-gray-700 px-3 py-1 rounded-[3px]">PHP 8.2</span>
                            <span class="text-xs font-semibold bg-gray-100 text-gray-700 px-3 py-1 rounded-[3px]">MySQL</span>
                            <span class="text-xs font-semibold bg-gray-100 text-gray-700 px-3 py-1 rounded-[3px]">Tailwind CSS</span>
                            <span class="text-xs font-semibold bg-gray-100 text-gray-700 px-3 py-1 rounded-[3px]">Real-Time APIs</span>
                            <span class="text-xs font-semibold bg-gray-100 text-gray-700 px-3 py-1 rounded-[3px]">REST API</span>
                        </div>
                    </div>
                </div>

                <!-- Milestone Schedule Breakdown -->
                <div class="bg-white rounded-[3px] p-8 border border-gray-200/80 shadow-sm space-y-6">
                    <h3 class="font-serif text-xl font-bold text-brand-dark">Milestone Escrow Schedule</h3>
                    
                    <div class="space-y-4">
                        <div class="p-4 rounded-[3px] bg-gray-50 border border-gray-200/80 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-brand-dark block">Milestone 01: Database Schema & Authentication System</span>
                                <span class="text-[11px] text-gray-500">Deliverable: Database migration scripts & JWT auth endpoints</span>
                            </div>
                            <span class="font-extrabold text-sm text-brand-dark">₦75,000</span>
                        </div>

                        <div class="p-4 rounded-[3px] bg-gray-50 border border-gray-200/80 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-brand-dark block">Milestone 02: Responsive Portal Frontend & Dashboard UI</span>
                                <span class="text-[11px] text-gray-500">Deliverable: Tailwind user dashboard layouts & forms</span>
                            </div>
                            <span class="font-extrabold text-sm text-brand-dark">₦100,000</span>
                        </div>

                        <div class="p-4 rounded-[3px] bg-gray-50 border border-gray-200/80 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-brand-dark block">Milestone 03: WebSocket Integration & Final Testing</span>
                                <span class="text-[11px] text-gray-500">Deliverable: Live real-time chat & deployment audit</span>
                            </div>
                            <span class="font-extrabold text-sm text-brand-dark">₦75,000</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right 4 Cols: Project Budget & Action Sidebar -->
            <div class="lg:col-span-4 space-y-6">
                
                <div class="bg-white rounded-[3px] p-6 border border-gray-200/80 shadow-sm space-y-6 sticky top-24">
                    <div class="text-center pb-5 border-b border-gray-100">
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block mb-1">Fixed Contract Budget</span>
                        <span class="font-serif text-3xl font-extrabold text-brand-dark">₦250,000</span>
                        <span class="text-xs font-bold text-emerald-600 block mt-1">✓ 100% Funded into Escrow</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between text-gray-600">
                            <span>Project Type:</span>
                            <strong class="text-brand-dark">Fixed Price</strong>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Timeline:</span>
                            <strong class="text-brand-dark">14 Days</strong>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Proposals Submitted:</span>
                            <strong class="text-brand-dark">12 Candidates</strong>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Required Pro Status:</span>
                            <strong class="text-blue-600 font-bold">Verified Score 80%+</strong>
                        </div>
                    </div>

                    <a href="signup?role=provider" class="block w-full py-4 bg-brand-dark text-white rounded-full font-bold text-xs text-center hover:bg-slate-800 transition-all shadow-md">
                        Submit Proposal for Project →
                    </a>
                </div>

            </div>

        </div>

    </main>

<?php include 'includes/footer.php'; ?>
