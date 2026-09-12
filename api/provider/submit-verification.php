<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/emails/admin_alert_email.php';

enforceSecurityHeaders();

// Verify Authentication
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please log in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$nin = trim($_POST['nin'] ?? '');

// 1. Enforce NIN syntax (strictly 11 digits)
if (!preg_match('/^\d{11}$/', $nin)) {
    echo json_encode(['success' => false, 'message' => 'Invalid NIN format. The National Identification Number must be exactly 11 numeric digits.']);
    exit;
}

try {
    $db = getDBConnection();

    // 2. Check if NIN is already used by another user
    $chk_stmt = $db->prepare("SELECT id FROM users WHERE nin = ? AND id != ? LIMIT 1");
    $chk_stmt->execute([$nin, $user_id]);
    if ($chk_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'This National Identification Number (NIN) is already registered under another account.']);
        exit;
    }

    // Fetch user details for the email alert
    $u_stmt = $db->prepare("SELECT full_name, email FROM users WHERE id = ? LIMIT 1");
    $u_stmt->execute([$user_id]);
    $user = $u_stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User profile not found.']);
        exit;
    }

    // 3. Process File Uploads
    $upload_dir = __DIR__ . '/../../uploads/verification/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Helper function for secure file uploads
    function handleUpload($file_key, $user_id, $allowed_exts, $max_size_mb, $label) {
        if (!isset($_FILES[$file_key]) || $_FILES[$file_key]['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => "Please upload a valid $label file."]);
            exit;
        }

        $file = $_FILES[$file_key];
        $file_size = $file['size'];
        $file_tmp = $file['tmp_name'];
        $original_name = basename($file['name']);
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        // In case of liveness video Blob, it might not have extension or might be .webm
        if ($file_key === 'liveness_video' && empty($ext)) {
            $ext = 'webm';
        }

        // Validate File Size
        if ($file_size > $max_size_mb * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => "$label exceeds the maximum allowed size of {$max_size_mb}MB."]);
            exit;
        }

        // Validate File Extension
        if (!in_array($ext, $allowed_exts)) {
            echo json_encode(['success' => false, 'message' => "Invalid file extension for $label. Allowed types: " . implode(', ', $allowed_exts)]);
            exit;
        }

        // Create secure filename
        $new_filename = $file_key . '_' . $user_id . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dest_path = __DIR__ . '/../../uploads/verification/' . $new_filename;

        if (!move_uploaded_file($file_tmp, $dest_path)) {
            echo json_encode(['success' => false, 'message' => "Failed to save the uploaded $label."]);
            exit;
        }

        // Return path relative to the root directory for database storage
        return 'uploads/verification/' . $new_filename;
    }

    // Execute uploads
    $id_card_url = handleUpload('id_card', $user_id, ['jpg', 'jpeg', 'png', 'pdf'], 5, 'ID Card / NIN Slip Document');
    $selfie_url = handleUpload('selfie', $user_id, ['jpg', 'jpeg', 'png'], 5, 'Selfie image holding ID');
    $liveness_video_url = handleUpload('liveness_video', $user_id, ['webm', 'mp4', 'avi', 'mov'], 15, 'Biometric Liveness Video');

    // 4. Update Database
    $up_stmt = $db->prepare("UPDATE users SET 
        nin = ?, 
        id_card_url = ?, 
        selfie_url = ?, 
        liveness_video_url = ?, 
        verification_status = 'pending', 
        verification_rejected_reason = NULL 
        WHERE id = ?");
    $up_stmt->execute([$nin, $id_card_url, $selfie_url, $liveness_video_url, $user_id]);

    // Update session flags
    $_SESSION['assessment_status'] = 'passed';
    $_SESSION['is_verified_pro'] = 0;

    // 5. Send Alert Email to Administrators
    sendAdminVerificationAlertEmail($user['full_name'], $user['email'], $nin);

    echo json_encode([
        'success' => true,
        'message' => 'Verification files submitted successfully! Your account is now under review by our administration team.'
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Upload error: ' . $e->getMessage()]);
    exit;
}
