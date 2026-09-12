<?php
$page_title = 'Calendar & Gigs Schedule';
$active_tab = 'interviews';
require_once __DIR__ . '/components/head.php';

// Demo data for upcoming interviews
$meetings = [
    [
        'title' => 'Google Meet Call — Docker CI/CD Integration',
        'client' => 'Kingsley Chukwuma',
        'org' => 'FinCorp Lead Developer',
        'time' => '09:00 AM (Today)',
        'duration' => '30 min',
        'link' => 'https://meet.google.com/abc-defg-hij',
        'status' => 'confirmed'
    ],
    [
        'title' => 'Google Meet Call — PHP Web Portal Review',
        'client' => 'Elena Vance',
        'org' => 'Madira Studios Product Manager',
        'time' => '10:00 AM (Today)',
        'duration' => '30 min',
        'link' => 'https://meet.google.com/xyz-pdqr-wxy',
        'status' => 'confirmed'
    ],
    [
        'title' => 'Google Meet Call — Figma Mockup Alignment',
        'client' => 'Apex Global Tech',
        'org' => 'Product Director',
        'time' => '10:30 AM (Today)',
        'duration' => '30 min',
        'link' => 'https://meet.google.com/mno-rstu-vwx',
        'status' => 'confirmed'
    ]
];
?>

<!-- Vertical Navigation -->
<?php include __DIR__ . '/components/sidebar.php'; ?>

<!-- Main Content Area -->
<div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
    <?php include __DIR__ . '/components/header.php'; ?>
    <main class="flex-1 px-4 sm:px-8 lg:px-12 py-6 space-y-7 max-w-[1600px] mx-auto w-full">
    
    <!-- Top Header -->
    

    <!-- Title Bar -->
    <div class="bg-white border border-slate-200/90 rounded-[3px] p-5 sm:p-6 shadow-sm flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight font-heading">Calendar & Client Meetings</h1>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Track upcoming Google Meet calls, client briefings, and configure weekly booking slots.</p>
        </div>
        <button type="button" onclick="ScriptlyToast.info('Calendar synchronization updated. Checking Google Calendar for new bookings.', 'Sync Calendar')" class="bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs px-5 py-3 rounded-[3px] transition-colors shadow-sm cursor-pointer shrink-0 self-start sm:self-auto flex items-center gap-1.5">
            Sync Calendar
        </button>
    </div>

    <!-- 2 Column Layout: Meetings list & availability settings -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Upcoming calls feed (Span 7) -->
        <div class="lg:col-span-7 space-y-4">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Upcoming Client Calls</h3>
            
            <div class="grid grid-cols-1 gap-4">
                <?php foreach ($meetings as $meet): ?>
                <div class="bg-white p-5 border border-slate-200/90 rounded-[3px] shadow-sm hover:border-[#1952E1] transition-all space-y-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-blue-50 border border-blue-200 flex items-center justify-center text-xs">📹</span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900"><?php echo htmlspecialchars($meet['title']); ?></h4>
                                <p class="text-[10px] text-slate-400 font-medium mt-0.5">With client: <strong class="text-slate-600"><?php echo htmlspecialchars($meet['client']); ?></strong> (<?php echo htmlspecialchars($meet['org']); ?>)</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4 text-[10px] text-slate-400 font-bold pl-10">
                            <span>⏱ <?php echo $meet['time']; ?></span>
                            <span>•</span>
                            <span>⏳ Duration: <?php echo $meet['duration']; ?></span>
                        </div>
                    </div>

                    <div class="shrink-0 flex items-center gap-2 pl-10 sm:pl-0">
                        <span class="text-[9px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-[3px]">
                            <?php echo $meet['status']; ?>
                        </span>
                        
                        <a href="<?php echo $meet['link']; ?>" target="_blank" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[10px] rounded-[3px] transition-colors shadow-2xs flex items-center gap-1">
                            Join Call
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Right: Availability hours configuration (Span 5) -->
        <div class="lg:col-span-5 space-y-4">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Configure Booking Slots</h3>
            
            <div class="bg-white p-5 border border-slate-200/90 rounded-[3px] shadow-sm space-y-4">
                <div class="space-y-1">
                    <h4 class="text-xs font-bold text-slate-900">Set Weekly Available Hours</h4>
                    <p class="text-[10px] text-slate-400 leading-relaxed font-medium">Clients will be able to book 30-minute briefings during these windows directly via your portfolio profile.</p>
                </div>
                
                <form onsubmit="event.preventDefault(); ScriptlyToast.success('Availability hours saved successfully!', 'Settings Updated');" class="space-y-4">
                    <!-- Day range -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">Available Days</label>
                        <div class="flex gap-2">
                            <label class="flex-1 text-center py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-bold text-slate-700 hover:bg-slate-100 cursor-pointer">
                                <input type="checkbox" checked class="hidden"> M
                            </label>
                            <label class="flex-1 text-center py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-bold text-slate-700 hover:bg-slate-100 cursor-pointer">
                                <input type="checkbox" checked class="hidden"> T
                            </label>
                            <label class="flex-1 text-center py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-bold text-slate-700 hover:bg-slate-100 cursor-pointer">
                                <input type="checkbox" checked class="hidden"> W
                            </label>
                            <label class="flex-1 text-center py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-bold text-slate-700 hover:bg-slate-100 cursor-pointer">
                                <input type="checkbox" checked class="hidden"> T
                            </label>
                            <label class="flex-1 text-center py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-bold text-slate-700 hover:bg-slate-100 cursor-pointer">
                                <input type="checkbox" checked class="hidden"> F
                            </label>
                        </div>
                    </div>

                    <!-- Time slots -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">From Time</label>
                            <input type="time" value="09:00" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white text-slate-700">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">To Time</label>
                            <input type="time" value="17:00" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-medium focus:outline-none focus:border-[#1952E1] focus:bg-white text-slate-700">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#1952E1] hover:bg-blue-700 text-white font-bold text-xs py-2.5 rounded-[3px] transition-colors shadow-2xs cursor-pointer">
                        Save Availability
                    </button>
                </form>
            </div>
        </div>

    </div>

</main>
</div>
<!-- Mobile Bottom Navigation -->
<?php include __DIR__ . '/components/bottom-nav.php'; ?>

<!-- Scripts -->
<?php include __DIR__ . '/components/footer.php'; ?>
