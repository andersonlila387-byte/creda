<?php
$page_title = 'Create Service Package';
$active_tab = 'packages';
require_once 'components/head.php'; // Handles auth and sets $user_id, $db, etc.

// GATEWAY LOGIC: Fetch categories where the provider has passed the assessment
$stmt = $db->prepare("
    SELECT c.id, c.name, c.minimum_price 
    FROM provider_assessments pa
    JOIN service_categories c ON pa.category_id = c.id
    WHERE pa.provider_id = ? AND pa.status = 'passed'
");
$stmt->execute([$user_id]);
$passed_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$has_passed_assessments = count($passed_categories) > 0;

// Gather category limits map to use in frontend validation
$min_prices_map = [];
foreach ($passed_categories as $cat) {
    $min_prices_map[$cat['id']] = (int)$cat['minimum_price'];
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a Package — Scriptly</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/tailwind.min.css">
    <link rel="stylesheet" href="../assets/css/scriptly-alerts.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="h-full flex flex-col antialiased text-slate-800">

    <!-- Distraction-Free Header -->
    <header class="bg-white border-b border-slate-200/80 px-6 py-4 flex items-center justify-between shrink-0 shadow-sm z-30">
        <div class="flex items-center gap-4">
            <a href="index.php" class="text-slate-400 hover:text-slate-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight" style="font-family:'Space Grotesk',sans-serif;">Create Service Package</h1>
        </div>
        <button type="button" onclick="saveDraft()" class="text-xs font-bold text-slate-500 hover:text-[#1952E1] transition-colors uppercase tracking-wider">Save as Draft</button>
    </header>

    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 py-8 pb-24">
        
        <?php if (!$has_passed_assessments): ?>
            <!-- LOCKED STATE: No passed assessments -->
            <div class="bg-white rounded-[6px] border border-red-100 p-8 sm:p-12 text-center max-w-xl mx-auto mt-10 shadow-lg space-y-6">
                <div class="w-16 h-16 bg-red-50 text-red-500 border border-red-200 rounded-full flex items-center justify-center mx-auto shadow-sm">
                    <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Unlock Package Wizard</h2>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed font-medium">
                        To maintain standard pricing rates and provider competency, you must pass a skill assessment in your target category before you can build or publish a service package.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="assessment.php" class="inline-flex items-center justify-center gap-1.5 px-6 py-3 bg-[#1952E1] hover:bg-blue-700 text-white font-extrabold text-xs rounded-[3px] shadow-sm transition-colors w-full uppercase tracking-wider">
                        <span>Take Assessment</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        <?php else: ?>

            <!-- WIZARD STEPPER -->
            <nav class="mb-8 overflow-x-auto hide-scrollbar shrink-0 border-b border-slate-200/80 pb-4">
                <ol class="flex items-center min-w-max">
                    <?php 
                    $steps = ['Overview', 'Pricing', 'Description & FAQ', 'Requirements', 'Gallery', 'Publish'];
                    foreach ($steps as $index => $stepName): 
                        $stepNum = $index + 1;
                    ?>
                    <li id="stepper-item-<?= $stepNum ?>" class="flex items-center transition-all duration-300 font-extrabold text-xs mr-6 text-slate-400">
                        <span id="stepper-num-<?= $stepNum ?>" class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] mr-2 bg-slate-100 text-slate-500 font-black">
                            <?= $stepNum ?>
                        </span>
                        <span><?= htmlspecialchars($stepName) ?></span>
                        <?php if ($stepNum < 6): ?>
                            <svg class="w-3.5 h-3.5 ml-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ol>
            </nav>

            <!-- MAIN CONTAINER FOR STEP FORMS -->
            <div class="bg-white rounded-[6px] border border-slate-200/80 shadow-sm p-6 sm:p-10">

                <!-- STEP 1: OVERVIEW -->
                <div id="step-container-1" class="space-y-8 block">
                    <h2 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3 uppercase tracking-wider">Step 1: Service Overview</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-[250px_1fr] gap-6">
                        <div>
                            <label class="block text-xs font-black text-slate-900 uppercase">Package Title</label>
                            <p class="text-[11px] text-slate-400 mt-1 leading-relaxed font-medium">Write a concise, professional summary title. Use keywords buyers would search for.</p>
                        </div>
                        <div>
                            <textarea id="package-title" rows="3" placeholder="I will build a high-performance REST API in Node.js..." class="w-full px-4 py-3 border border-slate-300 rounded-[3px] text-xs font-medium focus:border-[#1952E1] focus:ring-1 focus:ring-[#1952E1] outline-none transition-all resize-none" required></textarea>
                            <div class="flex justify-between mt-1 text-[9px] font-bold text-slate-400 uppercase">
                                <span>Min 15 chars</span>
                                <span>Max 80 chars</span>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="border-slate-100">

                    <div class="grid grid-cols-1 md:grid-cols-[250px_1fr] gap-6">
                        <div>
                            <label class="block text-xs font-black text-slate-900 uppercase">Service Category</label>
                            <p class="text-[11px] text-slate-400 mt-1 leading-relaxed font-medium">Select a category. You are only allowed to publish under domains where you have passed the assessment.</p>
                        </div>
                        <div>
                            <select id="package-category" class="w-full px-4 py-3 border border-slate-300 rounded-[3px] text-xs font-medium focus:border-[#1952E1] focus:ring-1 focus:ring-[#1952E1] outline-none transition-all bg-white" required>
                                <option value="">Select a category...</option>
                                <?php foreach ($passed_categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>">
                                        <?= htmlspecialchars($cat['name']) ?> (Min Price: $<?= (int)$cat['minimum_price'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    <div class="grid grid-cols-1 md:grid-cols-[250px_1fr] gap-6">
                        <div>
                            <label class="block text-xs font-black text-slate-900 uppercase">Search Tags</label>
                            <p class="text-[11px] text-slate-400 mt-1 leading-relaxed font-medium">Tag your service package with keywords. Up to 5 tags separated by commas.</p>
                        </div>
                        <div>
                            <input type="text" id="package-tags" placeholder="e.g. rest api, nodejs, backend, mysql" class="w-full px-4 py-3 border border-slate-300 rounded-[3px] text-xs font-medium focus:border-[#1952E1] focus:ring-1 focus:ring-[#1952E1] outline-none transition-all">
                            <p class="text-[9px] text-slate-400 mt-1.5 font-bold uppercase">Up to 5 comma-separated values.</p>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: PRICING & SCOPE -->
                <div id="step-container-2" class="space-y-8 hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-3 gap-2 sm:gap-0">
                        <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Step 2: Pricing & Scope</h2>
                        <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-2.5 py-1 border border-emerald-100 rounded-[3px] uppercase tracking-wider" id="lbl-min-price-alert">Minimum Category Price: $0</span>
                    </div>

                    <!-- Mobile Package Tier Tab Switcher -->
                    <div class="flex border border-slate-200 rounded-[3px] overflow-hidden lg:hidden shrink-0">
                        <button type="button" onclick="switchMobileTierTab('basic')" id="tab-btn-basic" class="flex-1 py-3 text-[10px] font-black uppercase text-[#1952E1] bg-blue-50/30 border-r border-slate-200">Basic</button>
                        <button type="button" onclick="switchMobileTierTab('standard')" id="tab-btn-standard" class="flex-1 py-3 text-[10px] font-bold uppercase text-slate-400 border-r border-slate-200">Standard</button>
                        <button type="button" onclick="switchMobileTierTab('premium')" id="tab-btn-premium" class="flex-1 py-3 text-[10px] font-bold uppercase text-slate-400">Premium</button>
                    </div>

                    <!-- Tiers Row / Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 divide-y lg:divide-y-0 lg:divide-x divide-slate-100">
                        
                        <!-- Basic Package Column -->
                        <div id="tier-col-basic" class="space-y-6 block lg:pr-3">
                            <div class="bg-slate-50 -mx-6 -mt-6 lg:mx-0 lg:mt-0 p-3 border-b lg:border border-slate-200 lg:rounded-[3px] text-center">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Basic Package</span>
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Tier Title</label>
                                <input type="text" id="basic-title" placeholder="Basic API Setup" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-[3px] text-xs font-semibold focus:border-[#1952E1] outline-none" required>
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Scope / Details</label>
                                <textarea id="basic-description" rows="3" placeholder="Single API route, standard endpoint, JSON return..." class="w-full px-3.5 py-2.5 border border-slate-300 rounded-[3px] text-xs font-medium focus:border-[#1952E1] outline-none resize-none" required></textarea>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Delivery Time</label>
                                <select id="basic-delivery" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-[3px] text-xs font-medium focus:border-[#1952E1] outline-none bg-white" required>
                                    <option value="1">1 Day</option>
                                    <option value="2">2 Days</option>
                                    <option value="3">3 Days</option>
                                    <option value="5">5 Days</option>
                                    <option value="7">7 Days</option>
                                    <option value="14">14 Days</option>
                                    <option value="30">30 Days</option>
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Revisions</label>
                                <select id="basic-revisions" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-[3px] text-xs font-medium focus:border-[#1952E1] outline-none bg-white" required>
                                    <option value="0">0 Revisions</option>
                                    <option value="1">1 Revision</option>
                                    <option value="2">2 Revisions</option>
                                    <option value="3">3 Revisions</option>
                                    <option value="5">5 Revisions</option>
                                    <option value="-1">Unlimited</option>
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Price ($ USD)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-xs font-bold text-slate-400">$</span>
                                    <input type="number" id="basic-price" placeholder="100" class="w-full pl-7 pr-3 py-2.5 border border-slate-300 rounded-[3px] text-xs font-extrabold focus:border-[#1952E1] outline-none" required>
                                </div>
                            </div>
                        </div>

                        <!-- Standard Package Column -->
                        <div id="tier-col-standard" class="space-y-6 hidden lg:block lg:px-4">
                            <div class="bg-slate-50 -mx-6 -mt-6 lg:mx-0 lg:mt-0 p-3 border-b lg:border border-slate-200 lg:rounded-[3px] text-center">
                                <span class="text-[10px] font-black uppercase tracking-wider text-[#1952E1]">Standard Package</span>
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Tier Title</label>
                                <input type="text" id="standard-title" placeholder="Standard Web Backend" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-[3px] text-xs font-semibold focus:border-[#1952E1] outline-none" required>
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Scope / Details</label>
                                <textarea id="standard-description" rows="3" placeholder="Up to 5 REST endpoints, SQL database integration, validation logic..." class="w-full px-3.5 py-2.5 border border-slate-300 rounded-[3px] text-xs font-medium focus:border-[#1952E1] outline-none resize-none" required></textarea>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Delivery Time</label>
                                <select id="standard-delivery" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-[3px] text-xs font-medium focus:border-[#1952E1] outline-none bg-white" required>
                                    <option value="1">1 Day</option>
                                    <option value="3" selected>3 Days</option>
                                    <option value="5">5 Days</option>
                                    <option value="7">7 Days</option>
                                    <option value="14">14 Days</option>
                                    <option value="30">30 Days</option>
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Revisions</label>
                                <select id="standard-revisions" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-[3px] text-xs font-medium focus:border-[#1952E1] outline-none bg-white" required>
                                    <option value="0">0 Revisions</option>
                                    <option value="1">1 Revision</option>
                                    <option value="2">2 Revisions</option>
                                    <option value="3" selected>3 Revisions</option>
                                    <option value="5">5 Revisions</option>
                                    <option value="-1">Unlimited</option>
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Price ($ USD)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-xs font-bold text-slate-400">$</span>
                                    <input type="number" id="standard-price" placeholder="250" class="w-full pl-7 pr-3 py-2.5 border border-slate-300 rounded-[3px] text-xs font-extrabold focus:border-[#1952E1] outline-none" required>
                                </div>
                            </div>
                        </div>

                        <!-- Premium Package Column -->
                        <div id="tier-col-premium" class="space-y-6 hidden lg:block lg:pl-6">
                            <div class="bg-slate-50 -mx-6 -mt-6 lg:mx-0 lg:mt-0 p-3 border-b lg:border border-slate-200 lg:rounded-[3px] text-center">
                                <span class="text-[10px] font-black uppercase tracking-wider text-amber-500">Premium Package</span>
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Tier Title</label>
                                <input type="text" id="premium-title" placeholder="Enterprise Application Solution" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-[3px] text-xs font-semibold focus:border-[#1952E1] outline-none" required>
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Scope / Details</label>
                                <textarea id="premium-description" rows="3" placeholder="Full API suite, JWT authentication, deployment pipelines, tests..." class="w-full px-3.5 py-2.5 border border-slate-300 rounded-[3px] text-xs font-medium focus:border-[#1952E1] outline-none resize-none" required></textarea>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Delivery Time</label>
                                <select id="premium-delivery" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-[3px] text-xs font-medium focus:border-[#1952E1] outline-none bg-white" required>
                                    <option value="1">1 Day</option>
                                    <option value="3">3 Days</option>
                                    <option value="5">5 Days</option>
                                    <option value="7" selected>7 Days</option>
                                    <option value="14">14 Days</option>
                                    <option value="30">30 Days</option>
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Revisions</label>
                                <select id="premium-revisions" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-[3px] text-xs font-medium focus:border-[#1952E1] outline-none bg-white" required>
                                    <option value="0">0 Revisions</option>
                                    <option value="1">1 Revision</option>
                                    <option value="2">2 Revisions</option>
                                    <option value="3">3 Revisions</option>
                                    <option value="5" selected>5 Revisions</option>
                                    <option value="-1">Unlimited</option>
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Price ($ USD)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-xs font-bold text-slate-400">$</span>
                                    <input type="number" id="premium-price" placeholder="500" class="w-full pl-7 pr-3 py-2.5 border border-slate-300 rounded-[3px] text-xs font-extrabold focus:border-[#1952E1] outline-none" required>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- STEP 3: DESCRIPTION & FAQ -->
                <div id="step-container-3" class="space-y-8 hidden">
                    <h2 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3 uppercase tracking-wider">Step 3: Description & FAQ</h2>
                    
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-slate-900 uppercase">Service Description</label>
                        <textarea id="package-description" rows="8" placeholder="Provide a detailed description explaining what is included in your service. Mention your technologies, stack, and deliverables..." class="w-full px-4 py-3 border border-slate-300 rounded-[3px] text-xs font-medium focus:border-[#1952E1] outline-none" required></textarea>
                    </div>

                    <hr class="border-slate-100">

                    <!-- FAQ Section -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-black text-slate-900 uppercase">Frequently Asked Questions (FAQ)</label>
                            <button type="button" onclick="addFaqRow()" class="text-xs font-black text-[#1952E1] hover:text-blue-700 transition-colors uppercase tracking-wider">+ Add FAQ</button>
                        </div>
                        <div id="faq-rows-container" class="space-y-4">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>
                </div>

                <!-- STEP 4: REQUIREMENTS -->
                <div id="step-container-4" class="space-y-8 hidden">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Step 4: Buyer Requirements</h2>
                        <button type="button" onclick="addRequirementRow()" class="text-xs font-black text-[#1952E1] hover:text-blue-700 transition-colors uppercase tracking-wider">+ Add Question</button>
                    </div>
                    
                    <p class="text-xs text-slate-500 font-medium">Add questions to collect information from buyers when they start their orders. E.g. "Please upload your project brief or UI Figma links."</p>

                    <div id="requirements-rows-container" class="space-y-4">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>

                <!-- STEP 5: GALLERY -->
                <div id="step-container-5" class="space-y-8 hidden">
                    <h2 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3 uppercase tracking-wider">Step 5: Media Gallery</h2>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">
                        Upload up to 3 showcase images for your service package portfolio. Images must be JPEG/PNG, under 5MB.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <?php for ($imgNum = 1; $imgNum <= 3; $imgNum++): ?>
                        <div class="border-2 border-dashed border-slate-200 rounded-[6px] p-6 text-center hover:border-[#1952E1] transition-all relative flex flex-col items-center justify-center min-h-[180px]">
                            <input type="file" id="gallery-input-<?= $imgNum ?>" accept="image/*" onchange="previewGalleryImage(this, <?= $imgNum ?>)" class="hidden">
                            
                            <!-- Preview Overlay -->
                            <img id="gallery-preview-<?= $imgNum ?>" class="absolute inset-0 w-full h-full object-cover rounded-[4px] hidden">
                            
                            <!-- Placeholder Elements -->
                            <div id="gallery-placeholder-<?= $imgNum ?>" class="space-y-3">
                                <div class="w-10 h-10 bg-slate-50 border border-slate-200 text-slate-400 rounded-full flex items-center justify-center mx-auto shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                    <?= $imgNum === 1 ? 'Primary Image' : 'Gallery Image ' . $imgNum ?>
                                </div>
                                <button type="button" onclick="document.getElementById('gallery-input-<?= $imgNum ?>').click()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[10px] rounded-[3px] transition-colors uppercase">Browse File</button>
                            </div>

                            <!-- Delete button (visible when loaded) -->
                            <button type="button" id="gallery-delete-<?= $imgNum ?>" onclick="clearGalleryImage(<?= $imgNum ?>)" class="absolute top-2 right-2 bg-red-50 text-red-600 p-1 rounded-full border border-red-200 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all hidden z-10 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- STEP 6: PUBLISH -->
                <div id="step-container-6" class="space-y-8 hidden">
                    <div class="text-center max-w-xl mx-auto py-6 space-y-6">
                        <div class="w-16 h-16 bg-emerald-50 text-emerald-500 border border-emerald-200 rounded-full flex items-center justify-center mx-auto shadow-sm">
                            <svg class="w-8 h-8 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900">Your Package is Ready!</h2>
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed font-medium">
                                Once published, your package will be immediately active on the Scriptly marketplace. Clients will be able to order your tiers, and payments will be secured in escrow.
                            </p>
                        </div>
                        <div class="border border-slate-100 rounded-[6px] p-5 bg-slate-50 text-left space-y-3">
                            <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Package Summary:</h4>
                            <div class="space-y-1 text-xs text-slate-600 font-medium">
                                <p>&bull; <strong>Title:</strong> <span id="summary-title" class="text-slate-800"></span></p>
                                <p>&bull; <strong>Category:</strong> <span id="summary-category" class="text-slate-800"></span></p>
                                <p>&bull; <strong>Pricing:</strong> Basic ($<span id="summary-price-basic"></span>) &bull; Standard ($<span id="summary-price-standard"></span>) &bull; Premium ($<span id="summary-price-premium"></span>)</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2 justify-center">
                            <input type="checkbox" id="chk-terms" class="accent-[#1952E1] w-4 h-4">
                            <label for="chk-terms" class="text-xs text-slate-600 font-semibold cursor-pointer">I agree to Scriptly provider marketplace guidelines.</label>
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation Panel -->
                <div class="flex justify-between items-center pt-8 border-t border-slate-100 mt-8">
                    <button type="button" id="btn-back" onclick="navigateWizard(-1)" class="px-5 py-2.5 border border-slate-200 bg-white text-slate-600 font-extrabold text-xs rounded-[3px] hover:bg-slate-50 transition-colors uppercase tracking-wider disabled:opacity-50">Back</button>
                    <button type="button" id="btn-next" onclick="navigateWizard(1)" class="px-6 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-extrabold text-xs rounded-[3px] shadow-sm transition-colors uppercase tracking-wider">Save & Continue</button>
                </div>

            </div>
            
        <?php endif; ?>

    </main>

    <!-- Scripts -->
    <script src="../assets/js/scriptly-alerts.js"></script>
    <?php if ($has_passed_assessments): ?>
    <script>
    // Seeding PHP maps to JS
    const minPricesMap = <?= json_encode($min_prices_map) ?>;
    
    // Master data object for packaging all data
    const packageData = {
        title: "",
        category_id: "",
        tags: "",
        tiers: {
            basic: { title: "", description: "", delivery: 1, revisions: 0, price: 0 },
            standard: { title: "", description: "", delivery: 3, revisions: 3, price: 0 },
            premium: { title: "", description: "", delivery: 7, revisions: 5, price: 0 }
        },
        description: "",
        faqs: [],
        requirements: [],
        gallery: [null, null, null] // Array of file strings or data URLs
    };

    let currentStep = 1;
    const maxSteps = 6;

    // Initialization
    function initWizard() {
        updateStepVisibility();
        // Add a default FAQ and Requirement row on load
        addFaqRow();
        addRequirementRow();
    }

    // Toggle steps
    function updateStepVisibility() {
        for (let i = 1; i <= maxSteps; i++) {
            const container = document.getElementById(`step-container-${i}`);
            const stepperItem = document.getElementById(`stepper-item-${i}`);
            const stepperNum = document.getElementById(`stepper-num-${i}`);

            if (i === currentStep) {
                container.classList.remove('hidden');
                container.classList.add('block');
                
                stepperItem.classList.remove('text-slate-400', 'text-emerald-500');
                stepperItem.classList.add('text-[#1952E1]');

                stepperNum.className = "w-5 h-5 rounded-full flex items-center justify-center text-[9px] mr-2 bg-[#1952E1] text-white font-black";
            } else {
                container.classList.remove('block');
                container.classList.add('hidden');

                if (i < currentStep) {
                    stepperItem.classList.remove('text-slate-400', 'text-[#1952E1]');
                    stepperItem.classList.add('text-emerald-500');
                    stepperNum.className = "w-5 h-5 rounded-full flex items-center justify-center text-[9px] mr-2 bg-emerald-500 text-white font-black";
                    stepperNum.innerHTML = '<svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
                } else {
                    stepperItem.classList.remove('text-[#1952E1]', 'text-emerald-500');
                    stepperItem.classList.add('text-slate-400');
                    stepperNum.className = "w-5 h-5 rounded-full flex items-center justify-center text-[9px] mr-2 bg-slate-100 text-slate-500 font-black";
                    stepperNum.textContent = i;
                }
            }
        }

        // Configure footer buttons
        document.getElementById('btn-back').disabled = currentStep === 1;
        const nextBtn = document.getElementById('btn-next');
        if (currentStep === maxSteps) {
            nextBtn.textContent = "Publish Package";
            nextBtn.className = "px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-[3px] shadow-sm transition-colors uppercase tracking-wider";
        } else {
            nextBtn.textContent = "Save & Continue";
            nextBtn.className = "px-6 py-2.5 bg-[#1952E1] hover:bg-blue-700 text-white font-extrabold text-xs rounded-[3px] shadow-sm transition-colors uppercase tracking-wider";
        }
    }

    // Step verification & saving
    function validateAndSaveStep() {
        if (currentStep === 1) {
            const title = document.getElementById('package-title').value.trim();
            const categoryId = document.getElementById('package-category').value;
            const tags = document.getElementById('package-tags').value.trim();

            if (title.length < 15 || title.length > 80) {
                ScriptlyToast.error("Title must be between 15 and 80 characters.");
                return false;
            }
            if (!categoryId) {
                ScriptlyToast.error("Please select a category.");
                return false;
            }

            packageData.title = title;
            packageData.category_id = categoryId;
            packageData.tags = tags;

            // Set Step 2 minimum price indicator dynamically
            const minPrice = minPricesMap[categoryId] || 0;
            document.getElementById('lbl-min-price-alert').textContent = `Minimum Category Price: $${minPrice}`;
            
            // Set initial placeholders for pricing values to default minimum
            ['basic', 'standard', 'premium'].forEach((tier, i) => {
                const priceInput = document.getElementById(`${tier}-price`);
                if (!priceInput.value) {
                    priceInput.value = minPrice * (i + 1); // e.g. 100, 200, 300
                }
            });
            return true;
        }

        if (currentStep === 2) {
            const categoryId = packageData.category_id;
            const minPrice = minPricesMap[categoryId] || 0;

            const tiers = ['basic', 'standard', 'premium'];
            for (let i = 0; i < tiers.length; i++) {
                const t = tiers[i];
                const titleVal = document.getElementById(`${t}-title`).value.trim();
                const descVal = document.getElementById(`${t}-description`).value.trim();
                const deliveryVal = parseInt(document.getElementById(`${t}-delivery`).value);
                const revisionsVal = parseInt(document.getElementById(`${t}-revisions`).value);
                const priceVal = parseFloat(document.getElementById(`${t}-price`).value || 0);

                if (!titleVal || !descVal || !priceVal) {
                    ScriptlyToast.error(`Please complete all fields for the ${t.toUpperCase()} tier.`);
                    return false;
                }
                if (priceVal < minPrice) {
                    ScriptlyToast.error(`The ${t.toUpperCase()} tier price ($${priceVal}) cannot be less than the category minimum of $${minPrice}.`);
                    return false;
                }

                packageData.tiers[t] = {
                    title: titleVal,
                    description: descVal,
                    delivery: deliveryVal,
                    revisions: revisionsVal,
                    price: priceVal
                };
            }

            // Ensure standard > basic, premium > standard
            if (packageData.tiers.standard.price <= packageData.tiers.basic.price) {
                ScriptlyToast.error("Standard Tier price must be higher than the Basic Tier price.");
                return false;
            }
            if (packageData.tiers.premium.price <= packageData.tiers.standard.price) {
                ScriptlyToast.error("Premium Tier price must be higher than the Standard Tier price.");
                return false;
            }
            return true;
        }

        if (currentStep === 3) {
            const desc = document.getElementById('package-description').value.trim();
            if (desc.length < 50) {
                ScriptlyToast.error("Description must be at least 50 characters.");
                return false;
            }
            packageData.description = desc;

            // Collect FAQ row inputs
            packageData.faqs = [];
            let valid = true;
            document.querySelectorAll('.faq-row').forEach(row => {
                const q = row.querySelector('.faq-q').value.trim();
                const a = row.querySelector('.faq-a').value.trim();
                if (q && !a) {
                    ScriptlyToast.error("Please fill in the answer for all entered FAQ questions.");
                    valid = false;
                }
                if (q && a) {
                    packageData.faqs.push({ question: q, answer: a });
                }
            });
            return valid;
        }

        if (currentStep === 4) {
            // Collect requirements questions
            packageData.requirements = [];
            let valid = true;
            document.querySelectorAll('.req-row').forEach(row => {
                const q = row.querySelector('.req-text').value.trim();
                const type = row.querySelector('.req-type').value;
                const req = row.querySelector('.req-check').checked ? 1 : 0;
                
                if (q) {
                    packageData.requirements.push({ question_text: q, response_type: type, is_required: req });
                }
            });
            
            if (packageData.requirements.length === 0) {
                ScriptlyToast.error("Please add at least one question so clients know what requirements you need to start.");
                return false;
            }
            return true;
        }

        if (currentStep === 5) {
            // Verify at least the primary image is uploaded
            if (!packageData.gallery[0]) {
                ScriptlyToast.error("Please upload at least the Primary Image (Image 1) for your showcase portfolio.");
                return false;
            }
            return true;
        }

        if (currentStep === 6) {
            const chk = document.getElementById('chk-terms').checked;
            if (!chk) {
                ScriptlyToast.error("You must agree to the marketplace guidelines before publishing.");
                return false;
            }
            return true;
        }

        return true;
    }

    // Load Summary at Step 6 Review
    function loadPublishSummary() {
        const catSelect = document.getElementById('package-category');
        const selectedCatText = catSelect.options[catSelect.selectedIndex].text;

        document.getElementById('summary-title').textContent = packageData.title;
        document.getElementById('summary-category').textContent = selectedCatText;
        document.getElementById('summary-price-basic').textContent = packageData.tiers.basic.price;
        document.getElementById('summary-price-standard').textContent = packageData.tiers.standard.price;
        document.getElementById('summary-price-premium').textContent = packageData.tiers.premium.price;
    }

    // Sequential Wizard Navigation
    async function navigateWizard(direction) {
        if (direction === 1) {
            const ok = validateAndSaveStep();
            if (!ok) return;

            if (currentStep === maxSteps) {
                // Final Submission
                await publishPackage();
                return;
            }

            currentStep++;
            if (currentStep === 6) {
                loadPublishSummary();
            }
        } else {
            currentStep--;
        }
        updateStepVisibility();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Mobile Tier Switcher Toggles
    function switchMobileTierTab(tier) {
        ['basic', 'standard', 'premium'].forEach(t => {
            const btn = document.getElementById(`tab-btn-${t}`);
            const col = document.getElementById(`tier-col-${t}`);
            
            if (t === tier) {
                col.classList.remove('hidden');
                col.classList.add('block');
                
                btn.classList.add('text-[#1952E1]', 'bg-blue-50/30', 'border-[#1952E1]');
                btn.classList.remove('text-slate-400', 'border-transparent');
            } else {
                col.classList.remove('block');
                col.classList.add('hidden');
                
                btn.classList.remove('text-[#1952E1]', 'bg-blue-50/30', 'border-[#1952E1]');
                btn.classList.add('text-slate-400', 'border-transparent');
            }
        });
    }

    // Dynamic FAQ Rows
    function addFaqRow() {
        const container = document.getElementById('faq-rows-container');
        const rowId = Date.now();
        const div = document.createElement('div');
        div.className = "faq-row border border-slate-200 rounded-[4px] p-4 bg-slate-50/50 space-y-3 relative";
        div.id = `faq-row-${rowId}`;
        div.innerHTML = `
            <div class="space-y-1">
                <input type="text" placeholder="Question: e.g. Do you provide the source code file?" class="faq-q w-full px-3 py-2 border border-slate-300 rounded-[3px] text-xs font-bold focus:border-[#1952E1] outline-none">
            </div>
            <div class="space-y-1">
                <textarea placeholder="Answer: e.g. Yes, all delivery files include standard repository code files." rows="2" class="faq-a w-full px-3 py-2 border border-slate-300 rounded-[3px] text-xs font-medium focus:border-[#1952E1] outline-none resize-none"></textarea>
            </div>
            <button type="button" onclick="removeFaqRow(${rowId})" class="absolute top-1 right-2 text-slate-400 hover:text-red-500 font-extrabold text-[10px] uppercase">Remove</button>
        `;
        container.appendChild(div);
    }

    function removeFaqRow(id) {
        document.getElementById(`faq-row-${id}`).remove();
    }

    // Dynamic Requirement Rows
    function addRequirementRow() {
        const container = document.getElementById('requirements-rows-container');
        const rowId = Date.now();
        const div = document.createElement('div');
        div.className = "req-row border border-slate-200 rounded-[4px] p-4 bg-slate-50/50 flex flex-col sm:flex-row items-center gap-3 relative";
        div.id = `req-row-${rowId}`;
        div.innerHTML = `
            <div class="flex-1 w-full space-y-1">
                <input type="text" placeholder="Question: e.g. Please share database access keys or brief..." class="req-text w-full px-3 py-2 border border-slate-300 rounded-[3px] text-xs font-semibold focus:border-[#1952E1] outline-none" required>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <select class="req-type px-3 py-2 border border-slate-300 rounded-[3px] text-xs font-medium focus:border-[#1952E1] outline-none bg-white">
                    <option value="text">Text Response</option>
                    <option value="file">File Upload Required</option>
                </select>
                <label class="flex items-center gap-1.5 text-xs text-slate-600 font-semibold cursor-pointer">
                    <input type="checkbox" class="req-check accent-[#1952E1] w-4 h-4" checked> Required
                </label>
            </div>
            <button type="button" onclick="removeRequirementRow(${rowId})" class="absolute top-1 right-2 text-slate-400 hover:text-red-500 font-extrabold text-[10px] uppercase sm:relative sm:top-auto sm:right-auto">Remove</button>
        `;
        container.appendChild(div);
    }

    function removeRequirementRow(id) {
        document.getElementById(`req-row-${id}`).remove();
    }

    // Gallery Preview Handlers
    function previewGalleryImage(input, index) {
        const file = input.files[0];
        if (!file) return;

        if (file.size > 5 * 1024 * 1024) {
            ScriptlyToast.error("File is too large. Maximum size allowed is 5MB.");
            input.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const dataUrl = e.target.result;
            packageData.gallery[index - 1] = dataUrl;

            // Update UI Previews
            document.getElementById(`gallery-preview-${index}`).src = dataUrl;
            document.getElementById(`gallery-preview-${index}`).classList.remove('hidden');
            document.getElementById(`gallery-placeholder-${index}`).classList.add('hidden');
            document.getElementById(`gallery-delete-${index}`).classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    function clearGalleryImage(index) {
        packageData.gallery[index - 1] = null;
        document.getElementById(`gallery-input-${index}`).value = "";
        
        document.getElementById(`gallery-preview-${index}`).classList.add('hidden');
        document.getElementById(`gallery-placeholder-${index}`).classList.remove('hidden');
        document.getElementById(`gallery-delete-${index}`).classList.add('hidden');
    }

    // API calls to Backend
    async function publishPackage() {
        const btnNext = document.getElementById('btn-next');
        btnNext.disabled = true;
        btnNext.innerHTML = 'Publishing…';

        try {
            const res = await fetch('../../api/provider/save-package.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    ...packageData,
                    status: 'active'
                })
            });

            const result = await res.json();
            if (result.success) {
                ScriptlyToast.success("Your service package is now active on the marketplace!", "Package Published!");
                setTimeout(() => {
                    window.location.href = "index.php";
                }, 2000);
            } else {
                ScriptlyToast.error(result.message || "Failed to publish service package.");
                btnNext.disabled = false;
                btnNext.innerHTML = "Publish Package";
            }
        } catch (err) {
            ScriptlyToast.error("A network error occurred. Please try again.");
            btnNext.disabled = false;
            btnNext.innerHTML = "Publish Package";
        }
    }

    async function saveDraft() {
        try {
            const res = await fetch('../../api/provider/save-package.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    ...packageData,
                    status: 'draft'
                })
            });

            const result = await res.json();
            if (result.success) {
                ScriptlyToast.success("Package draft saved successfully.", "Draft Saved");
            } else {
                ScriptlyToast.error(result.message || "Failed to save draft.");
            }
        } catch (err) {
            ScriptlyToast.error("Error saving draft parameters.");
        }
    }

    // Run Initialization
    initWizard();
    </script>
    <?php endif; ?>
</body>
</html>
