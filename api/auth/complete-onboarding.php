<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

enforceSecurityHeaders();

// Detect if request is AJAX (JSON payload or headers)
$isAjax = false;
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$httpAccept = $_SERVER['HTTP_ACCEPT'] ?? '';
$xRequestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

if (strpos($contentType, 'application/json') !== false || 
    strpos($httpAccept, 'application/json') !== false || 
    strtolower($xRequestedWith) === 'xmlhttprequest') {
    $isAjax = true;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    } else {
        header('Location: ../../onboarding');
    }
    exit;
}

$inputData = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$phone_number = trim($inputData['phone_number'] ?? '');
$address = trim($inputData['address'] ?? '');
$primary_role = trim($inputData['primary_role'] ?? 'client');
$entity_type = trim($inputData['entity_type'] ?? 'student');
$accepted_rules = isset($inputData['accepted_rules']) ? (bool)$inputData['accepted_rules'] : false;

// Student details
$student_id = trim($inputData['student_id'] ?? '');
$institution = trim($inputData['institution'] ?? '');
$faculty = trim($inputData['faculty'] ?? '');
$department = trim($inputData['department'] ?? '');
$study_level = trim($inputData['study_level'] ?? '');
$expected_completion_year = trim($inputData['expected_completion_year'] ?? '');

// Business / Corporation details
$business_name = trim($inputData['business_name'] ?? '');
$rc_number = trim($inputData['rc_number'] ?? '');
$company_website = trim($inputData['company_website'] ?? '');
$industry = trim($inputData['industry'] ?? '');
$year_established = trim($inputData['year_established'] ?? '');

$user_email = $_SESSION['user_email'] ?? $_SESSION['pending_user']['email'] ?? '';

if (empty($user_email)) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No active user session found. Please log in or verify email first.']);
    } else {
        header('Location: ../../login');
    }
    exit;
}

if (empty($phone_number) || empty($address) || empty($entity_type) || !$accepted_rules) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please fill out phone number, location address, and accept platform rules.']);
    } else {
        header('Location: ../../onboarding');
    }
    exit;
}

if ($primary_role !== 'provider') {
    if ($entity_type === 'student') {
        if (empty($student_id) || empty($institution) || empty($department)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Please fill out Student ID, Institution, and Department.']);
            } else {
                header('Location: ../../onboarding');
            }
            exit;
        }
        $business_name = null;
        $rc_number = null;
        $company_website = null;
        $industry = null;
        $year_established = null;
    } elseif ($entity_type === 'business') {
        if (empty($business_name) || empty($rc_number) || empty($industry)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Please fill out Business Name, Registration Number, and Industry.']);
            } else {
                header('Location: ../../onboarding');
            }
            exit;
        }
        $student_id = null;
        $institution = null;
        $faculty = null;
        $department = null;
        $study_level = null;
        $expected_completion_year = null;
        $company_website = null;
        $year_established = null;
    } elseif ($entity_type === 'corporation') {
        if (empty($business_name) || empty($rc_number) || empty($company_website)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Please fill out Company Name, RC Number, and Company Website.']);
            } else {
                header('Location: ../../onboarding');
            }
            exit;
        }
        $student_id = null;
        $institution = null;
        $faculty = null;
        $department = null;
        $study_level = null;
        $expected_completion_year = null;
        $industry = null;
        $year_established = null;
    } else {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid entity type selected.']);
        } else {
            header('Location: ../../onboarding');
        }
        exit;
    }
} else {
    // It is a provider, just nullify these client-specific fields or leave them as is.
    // Provider onboarding sets its own fields.
}

try {
    $db = getDBConnection();

    // Update user record in MySQL: set phone_number, address, primary_role, entity_type, academic details, business details and onboarding_completed = 1
    $stmt = $db->prepare("UPDATE users SET 
        phone_number = :phone, 
        address = :address, 
        primary_role = :role, 
        entity_type = :entity, 
        student_id = :student_id,
        institution = :institution,
        faculty = :faculty,
        department = :department,
        study_level = :study_level,
        expected_completion_year = :expected_completion_year,
        business_name = :business_name,
        rc_number = :rc_number,
        company_website = :company_website,
        industry = :industry,
        year_established = :year_established,
        bio = :bio,
        onboarding_completed = 1 
        WHERE email = :email");
        
    $stmt->execute([
        ':phone' => $phone_number,
        ':address' => $address,
        ':role' => $primary_role,
        ':entity' => $entity_type,
        ':student_id' => $student_id,
        ':institution' => $institution,
        ':faculty' => $faculty,
        ':department' => $department,
        ':study_level' => $study_level,
        ':expected_completion_year' => $expected_completion_year,
        ':business_name' => $business_name,
        ':rc_number' => $rc_number,
        ':company_website' => $company_website,
        ':industry' => $industry,
        ':year_established' => $year_established,
        ':bio' => trim($inputData['bio'] ?? ''),
        ':email' => $user_email
    ]);

    // Update Session State to reflect completed onboarding
    $_SESSION['user_phone'] = $phone_number;
    $_SESSION['user_address'] = $address;
    $_SESSION['user_role'] = $primary_role;
    $_SESSION['entity_type'] = $entity_type;
    $_SESSION['student_id'] = $student_id;
    $_SESSION['institution'] = $institution;
    $_SESSION['faculty'] = $faculty;
    $_SESSION['department'] = $department;
    $_SESSION['study_level'] = $study_level;
    $_SESSION['expected_completion_year'] = $expected_completion_year;
    $_SESSION['onboarding_completed'] = true;
    $_SESSION['user_logged_in'] = true;

    // Get user id if not in session
    $user_id = $_SESSION['user_id'] ?? $_SESSION['pending_user']['id'] ?? null;
    if (!$user_id) {
        $u_stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $u_stmt->execute([$user_email]);
        $user_id = $u_stmt->fetchColumn();
        $_SESSION['user_id'] = $user_id;
    }

    if ($user_id) {
        // Clear any existing notifications for this user just in case
        $db->prepare("DELETE FROM notifications WHERE user_id = ?")->execute([$user_id]);

        // Define demo notifications
        $demo_notifs = [];
        
        if ($entity_type === 'student') {
            $demo_notifs[] = [
                'message' => "Welcome to Scriptly! Your student profile at " . $institution . " is pending verification.",
                'type' => 'info',
                'link' => 'settings.php'
            ];
        } else {
            $demo_notifs[] = [
                'message' => "Welcome to Scriptly! Your profile has been registered and is pending verification.",
                'type' => 'info',
                'link' => 'settings.php'
            ];
        }

        $demo_notifs[] = [
            'message' => 'Milestone 1 Deliverable Submitted for Review by David Olanrewaju',
            'type' => 'milestone',
            'link' => 'my-projects.php'
        ];

        $demo_notifs[] = [
            'message' => 'Elena Vance submitted a proposal for "Full-Stack PHP & MySQL Web Portal"',
            'type' => 'proposal',
            'link' => 'my-projects.php'
        ];

        $demo_notifs[] = [
            'message' => 'Kingsley Chukwuma sent a message: "I have pushed the automated Docker Compose scripts..."',
            'type' => 'message',
            'link' => 'messages.php'
        ];

        // Insert them
        $insert_notif = $db->prepare("INSERT INTO notifications (user_id, message, type, link) VALUES (?, ?, ?, ?)");
        foreach ($demo_notifs as $dn) {
            $insert_notif->execute([
                $user_id,
                $dn['message'],
                $dn['type'],
                $dn['link']
            ]);
        }
    }



    $target_redirect = ($primary_role === 'provider') ? 'provider/app/index.php' : 'app/index';

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Onboarding complete! Welcome to Scriptly.',
            'redirect' => $target_redirect
        ]);
        exit;
    } else {
        header('Location: ../../' . $target_redirect);
        exit;
    }

} catch (\Exception $e) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Onboarding error: ' . $e->getMessage()]);
    } else {
        header('Location: ../../onboarding');
    }
    exit;
}
