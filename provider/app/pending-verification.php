<?php
$page_title = 'Account Verification';
$active_tab = 'verification';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

// Verify Authentication
$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['user_email'] ?? null;

if (empty($user_email)) {
    header('Location: ../login.php');
    exit;
}

// Fetch user data
try {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT full_name, is_verified_pro, assessment_status, assessment_score, status, verification_status, verification_rejected_reason FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $user_email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || $user['status'] !== 'active') {
        session_destroy();
        header('Location: ../login.php?msg=suspended');
        exit;
    }

    // If already verified pro, direct to dashboard
    if ((bool)$user['is_verified_pro'] || $user['verification_status'] === 'approved') {
        header('Location: index.php');
        exit;
    }

    // If assessment not completed/passed, redirect to assessment
    if ($user['assessment_status'] !== 'passed') {
        header('Location: assessment.php');
        exit;
    }

} catch (\Exception $e) {
    // Fallback
}

$user_name = $user['full_name'] ?? 'Provider';
$score = $user['assessment_score'] ?? 90;
$verification_status = $user['verification_status'] ?? 'unverified';
$rejected_reason = $user['verification_rejected_reason'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-[#EFF2F7]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identity Verification — Cliniconnect Pro</title>
    <meta name="robots" content="noindex, nofollow">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://api.fontshare.com/v2/css?f[]=clash-display@700,600,500,400&f[]=satoshi@900,700,500,400&f[]=cabinet-grotesk@800,700,500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Local CSS -->
    <link rel="stylesheet" href="../../assets/css/tailwind.min.css">
    
    <style>
        body {
            font-family: 'Satoshi', 'Outfit', sans-serif;
            background-color: #EFF2F7;
        }
    </style>
</head>
<body class="min-h-screen bg-[#EFF2F7] flex items-center justify-center p-4 sm:p-6 lg:p-8 antialiased">

    <!-- Card Container -->
    <div class="max-w-lg w-full bg-white border border-slate-200/90 rounded-[3px] shadow-lg p-6 sm:p-8 space-y-6">
        
        <!-- Cliniconnect Pro Branding -->
        <div class="flex flex-col items-center gap-2 text-center">
            <div class="w-10 h-10 bg-[#1952E1] text-white rounded-[3px] flex items-center justify-center font-black text-xl shadow-sm">
                ✓
            </div>
            <div class="flex flex-col mt-1">
                <span class="text-xs font-black tracking-tight text-slate-900 leading-none">Cliniconnect</span>
                <span class="text-[9px] font-bold text-[#1952E1] tracking-widest uppercase mt-0.5 leading-none">PRO PORTAL</span>
            </div>
        </div>

        <?php if ($verification_status === 'pending'): ?>
            <!-- STATUS 1: PENDING VERIFICATION -->
            <div class="text-center space-y-4">
                <div class="inline-flex items-center gap-2 bg-amber-50 text-amber-800 border border-amber-200 px-3.5 py-1.5 rounded-[3px] text-xs font-bold shadow-2xs">
                    <span class="w-2.5 h-2.5 bg-amber-500 rounded-full animate-ping"></span>
                    <span>Verification Under Review</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight font-heading leading-tight pt-2">
                    Thank you for your submission, <br><span class="text-[#1952E1]"><?php echo htmlspecialchars($user_name); ?></span>!
                </h2>
                <p class="text-xs text-slate-500 leading-relaxed font-medium max-w-md mx-auto">
                    You passed the timed Skill Assessment with a score of <strong><?php echo $score; ?>%</strong>. Your profile details, NIN credentials, and liveness video are currently in the moderation review queue.
                </p>
                
                <!-- Details Box -->
                <div class="bg-slate-50 border border-slate-200/60 p-4 rounded-[3px] text-left text-xs space-y-3.5 mt-4">
                    <div class="flex gap-3">
                        <span class="text-lg shrink-0">⏳</span>
                        <div>
                            <h4 class="font-bold text-slate-900">Review Timeline</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Moderators verify identity documentation, selfies, and liveness videos within <strong>24–48 hours</strong>.</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 border-t border-slate-200/50 pt-3">
                        <span class="text-lg shrink-0">💼</span>
                        <div>
                            <h4 class="font-bold text-slate-900">What happens after approval?</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">You will receive an email notification, your Pro Verification Badge will activate, and you will unlock full access to apply for client projects and withdraw earnings.</p>
                        </div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <a href="settings.php" class="flex-1 text-center bg-white hover:bg-slate-50 border border-slate-200/80 text-slate-700 font-extrabold text-xs px-5 py-3 rounded-[3px] transition-colors shadow-2xs">
                        ⚙️ Update Profile Settings
                    </a>
                    <a href="logout.php" class="flex-1 text-center bg-rose-50 hover:bg-rose-100 border border-rose-200/80 text-rose-700 font-extrabold text-xs px-5 py-3 rounded-[3px] transition-colors shadow-2xs">
                        🚪 Log Out
                    </a>
                </div>
            </div>

        <?php else: ?>
            <!-- STATUS 2: UNVERIFIED OR REJECTED (UPLOAD FORM) -->
            <div class="space-y-4">
                <div class="text-center">
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-tight">
                        Verify Your Identity
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                        To maintain a trusted marketplace, please verify your details. This manual liveness and NIN document check ensures you are a real user.
                    </p>
                </div>

                <?php if ($verification_status === 'rejected'): ?>
                    <!-- Rejection Notice -->
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-[3px] text-xs space-y-1">
                        <p class="font-bold">❌ Verification Rejected</p>
                        <p class="text-[11px] text-rose-600 leading-relaxed">
                            <strong>Reason:</strong> <?php echo htmlspecialchars($rejected_reason); ?>
                        </p>
                        <p class="text-[11px] text-rose-500 pt-1 font-medium">Please re-submit your files with the requested improvements.</p>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form id="verification-form" class="space-y-4 pt-2">
                    
                    <!-- NIN -->
                    <div class="space-y-1.5">
                        <label for="nin" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">National Identification Number (NIN)</label>
                        <input type="text" id="nin" name="nin" required maxlength="11" pattern="\d{11}" placeholder="Enter 11-digit NIN" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-[3px] text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#1952E1] focus:bg-white transition-colors">
                        <span class="block text-[10px] text-slate-400 font-medium">Your NIN must be exactly 11 numeric digits.</span>
                    </div>

                    <!-- ID Card / NIN Slip -->
                    <div class="space-y-1.5">
                        <label for="id_card" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Government ID Card / NIN Slip</label>
                        <input type="file" id="id_card" name="id_card" required accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-[3px] file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        <span class="block text-[10px] text-slate-400 font-medium">Supported formats: JPG, PNG, PDF. Max file size: 5MB.</span>
                    </div>

                    <!-- Selfie with ID -->
                    <div class="space-y-1.5">
                        <label for="selfie" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Selfie Holding your ID Card / NIN Slip</label>
                        <input type="file" id="selfie" name="selfie" required accept=".jpg,.jpeg,.png" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-[3px] file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        <span class="block text-[10px] text-slate-400 font-medium">Upload a clear photo showing your face and the ID document next to it. Max file size: 5MB.</span>
                    </div>

                    <!-- HTML5 Biometric Liveness Video Recorder -->
                    <div class="space-y-1.5 border-t border-slate-100 pt-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Biometric Liveness Video Check</label>
                        <p class="text-[10px] text-slate-400 leading-normal font-medium">
                            To prevent account spoofing, you must record a short 4-second video clip. When recording, slowly turn your head to the left, then to the right, and nod.
                        </p>

                        <!-- Video Widget Container -->
                        <div class="relative w-full aspect-video bg-slate-900 border border-slate-200 rounded-[3px] overflow-hidden flex items-center justify-center text-slate-400 text-xs">
                            <video id="webcam-preview" autoplay muted playsinline class="w-full h-full object-cover hidden"></video>
                            <video id="playback-preview" controls class="w-full h-full object-cover hidden"></video>
                            
                            <!-- Static Overlay -->
                            <div id="video-placeholder" class="text-center p-4">
                                <span class="text-2xl block mb-1">📷</span>
                                <span class="font-bold">Camera Preview</span>
                                <p class="text-[10px] text-slate-500 mt-0.5">Click "Enable Camera" below to start</p>
                            </div>

                            <!-- Recording Indicator Overlay -->
                            <div id="recording-overlay" class="absolute top-3 left-3 bg-red-600/90 text-white text-[10px] font-bold px-2 py-0.5 rounded-[3px] items-center gap-1.5 hidden shadow-sm">
                                <span class="w-2.5 h-2.5 bg-white rounded-full animate-ping"></span>
                                <span>REC <span id="timer-text">4s</span></span>
                            </div>
                        </div>

                        <!-- Video Widget Controls -->
                        <div class="flex gap-2">
                            <button type="button" id="btn-camera-toggle" class="flex-1 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[11px] rounded-[3px] transition-colors border border-slate-200/50">
                                🔌 Enable Camera
                            </button>
                            <button type="button" id="btn-record-video" disabled class="flex-1 py-2 bg-red-50 hover:bg-red-100 text-red-700 disabled:opacity-50 disabled:cursor-not-allowed font-extrabold text-[11px] rounded-[3px] transition-colors border border-red-200/50">
                                🔴 Record Liveness (4s)
                            </button>
                        </div>
                    </div>

                    <!-- Status Display -->
                    <div id="form-alert" class="hidden bg-rose-50 border border-rose-200 text-rose-700 text-xs p-3 rounded-[3px] font-semibold"></div>

                    <!-- Submit Button -->
                    <button type="submit" id="btn-submit-verification" class="w-full py-3.5 bg-[#1952E1] hover:bg-blue-700 text-white rounded-[3px] font-black text-xs transition-colors flex items-center justify-center gap-2 shadow-md">
                        <span>Submit Verification Documents</span>
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <div class="text-[10px] text-slate-400 font-medium text-center">
            Need assistance? Reach out to support at <a href="mailto:support@cliniconnect.com" class="text-slate-600 hover:underline">support@cliniconnect.com</a>.
        </div>

    </div>

    <!-- JavaScript block for capturing webcam liveness video -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('verification-form');
            if (!form) return;

            const btnCameraToggle = document.getElementById('btn-camera-toggle');
            const btnRecordVideo = document.getElementById('btn-record-video');
            const webcamPreview = document.getElementById('webcam-preview');
            const playbackPreview = document.getElementById('playback-preview');
            const videoPlaceholder = document.getElementById('video-placeholder');
            const recordingOverlay = document.getElementById('recording-overlay');
            const timerText = document.getElementById('timer-text');
            const formAlert = document.getElementById('form-alert');
            const btnSubmit = document.getElementById('btn-submit-verification');

            let mediaStream = null;
            let mediaRecorder = null;
            let recordedChunks = [];
            let recordedBlob = null;
            let isRecording = false;

            // Enforce digits only in NIN input
            const ninInput = document.getElementById('nin');
            ninInput.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/\D/g, '');
            });

            // Enable Webcam
            btnCameraToggle.addEventListener('click', async () => {
                if (mediaStream) {
                    // Stop stream
                    stopCamera();
                    btnCameraToggle.textContent = '🔌 Enable Camera';
                    btnRecordVideo.disabled = true;
                    webcamPreview.classList.add('hidden');
                    playbackPreview.classList.add('hidden');
                    videoPlaceholder.classList.remove('hidden');
                    return;
                }

                try {
                    mediaStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'user', width: 640, height: 480 },
                        audio: false
                    });
                    
                    webcamPreview.srcObject = mediaStream;
                    webcamPreview.classList.remove('hidden');
                    playbackPreview.classList.add('hidden');
                    videoPlaceholder.classList.add('hidden');
                    
                    btnCameraToggle.textContent = '🔌 Stop Camera';
                    btnRecordVideo.disabled = false;
                } catch (err) {
                    alert('Camera access denied or unavailable. Please grant permission and try again.');
                }
            });

            function stopCamera() {
                if (mediaStream) {
                    mediaStream.getTracks().forEach(track => track.stop());
                    mediaStream = null;
                }
            }

            // Record Video
            btnRecordVideo.addEventListener('click', () => {
                if (!mediaStream) return;
                recordedChunks = [];
                
                // Set MIME Type
                let options = { mimeType: 'video/webm;codecs=vp9' };
                if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                    options = { mimeType: 'video/webm;codecs=vp8' };
                    if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                        options = { mimeType: 'video/webm' };
                    }
                }

                try {
                    mediaRecorder = new MediaRecorder(mediaStream, options);
                } catch (e) {
                    mediaRecorder = new MediaRecorder(mediaStream);
                }

                mediaRecorder.ondataavailable = (event) => {
                    if (event.data.size > 0) {
                        recordedChunks.push(event.data);
                    }
                };

                mediaRecorder.onstop = () => {
                    recordedBlob = new Blob(recordedChunks, { type: mediaRecorder.mimeType || 'video/webm' });
                    
                    // Show playback preview
                    playbackPreview.src = URL.createObjectURL(recordedBlob);
                    playbackPreview.classList.remove('hidden');
                    webcamPreview.classList.add('hidden');
                    
                    btnRecordVideo.textContent = '🔴 Record Liveness (4s)';
                    btnRecordVideo.classList.remove('bg-red-200');
                    btnRecordVideo.disabled = false;
                    recordingOverlay.classList.remove('flex');
                    recordingOverlay.classList.add('hidden');
                    isRecording = false;

                    // Release camera track
                    stopCamera();
                    btnCameraToggle.textContent = '🔌 Enable Camera';
                };

                // Start recording
                mediaRecorder.start();
                isRecording = true;
                btnRecordVideo.disabled = true;
                btnRecordVideo.textContent = 'Recording...';
                recordingOverlay.classList.remove('hidden');
                recordingOverlay.classList.add('flex');

                // Countdown Timer (4 seconds)
                let timeLeft = 4;
                timerText.textContent = timeLeft + 's';
                
                const countdown = setInterval(() => {
                    timeLeft--;
                    timerText.textContent = timeLeft + 's';
                    if (timeLeft <= 0) {
                        clearInterval(countdown);
                        if (mediaRecorder.state !== 'inactive') {
                            mediaRecorder.stop();
                        }
                    }
                }, 1000);
            });

            // Form Submit
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                formAlert.classList.add('hidden');

                const nin = ninInput.value.trim();
                const idCardFile = document.getElementById('id_card').files[0];
                const selfieFile = document.getElementById('selfie').files[0];

                if (!nin || nin.length !== 11) {
                    showError('NIN must be exactly 11 digits.');
                    return;
                }

                if (!idCardFile || !selfieFile) {
                    showError('Please upload both your ID Card and your Selfie.');
                    return;
                }

                if (!recordedBlob) {
                    showError('You must record the 4-second biometric liveness video check.');
                    return;
                }

                // Disable submit button
                btnSubmit.disabled = true;
                btnSubmit.querySelector('span').textContent = 'Uploading files...';

                // Build FormData
                const formData = new FormData();
                formData.append('nin', nin);
                formData.append('id_card', idCardFile);
                formData.append('selfie', selfieFile);
                
                // Add liveness video blob
                const fileExt = recordedBlob.type.includes('mp4') ? 'mp4' : 'webm';
                formData.append('liveness_video', recordedBlob, `liveness_video.${fileExt}`);

                try {
                    const response = await fetch('../../api/provider/submit-verification.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        showError(data.message || 'Verification submission failed. Please try again.');
                        btnSubmit.disabled = false;
                        btnSubmit.querySelector('span').textContent = 'Submit Verification Documents';
                    }
                } catch (err) {
                    showError('An error occurred during network upload. Please check your connection.');
                    btnSubmit.disabled = false;
                    btnSubmit.querySelector('span').textContent = 'Submit Verification Documents';
                }
            });

            function showError(msg) {
                formAlert.textContent = msg;
                formAlert.classList.remove('hidden');
                formAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
</body>
</html>
