<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'c:/xampp/htdocs/creda/config/database.php';
require_once 'c:/xampp/htdocs/creda/config/security.php';

enforceSecurityHeaders();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Authenticate Provider
$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['user_email'] ?? null;

if (empty($user_id) || empty($user_email)) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please log in again.']);
    exit;
}

$inputData = json_decode(file_get_contents('php://input'), true);
if (!$inputData) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input.']);
    exit;
}

$title = trim($inputData['title'] ?? '');
$categoryId = (int)($inputData['category_id'] ?? 0);
$tags = trim($inputData['tags'] ?? '');
$description = trim($inputData['description'] ?? '');
$tiers = $inputData['tiers'] ?? [];
$faqs = $inputData['faqs'] ?? [];
$requirements = $inputData['requirements'] ?? [];
$gallery = $inputData['gallery'] ?? [null, null, null];
$status = trim($inputData['status'] ?? 'draft'); // 'draft' or 'active'

// Validate status enum
if (!in_array($status, ['draft', 'active'])) {
    $status = 'draft';
}

// 1. Basic Validations (Only enforce strict validations if active publish)
if ($status === 'active') {
    if (strlen($title) < 15 || strlen($title) > 80) {
        echo json_encode(['success' => false, 'message' => 'Title must be between 15 and 80 characters.']);
        exit;
    }
    if ($categoryId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Please select a valid category.']);
        exit;
    }
    if (strlen($description) < 50) {
        echo json_encode(['success' => false, 'message' => 'Description must be at least 50 characters long.']);
        exit;
    }
}

try {
    $db = getDBConnection();

    // Verify passed assessment for category
    $chkStmt = $db->prepare("SELECT id FROM provider_assessments WHERE provider_id = ? AND category_id = ? AND status = 'passed'");
    $chkStmt->execute([$user_id, $categoryId]);
    $passed = $chkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$passed && $status === 'active') {
        echo json_encode(['success' => false, 'message' => 'You must pass the assessment for this category before publishing.']);
        exit;
    }

    // Verify minimum category pricing constraint
    $catStmt = $db->prepare("SELECT minimum_price FROM service_categories WHERE id = ?");
    $catStmt->execute([$categoryId]);
    $category = $catStmt->fetch(PDO::FETCH_ASSOC);
    $minPrice = $category ? (float)$category['minimum_price'] : 0.00;

    if ($status === 'active' && !empty($tiers)) {
        foreach (['basic', 'standard', 'premium'] as $t) {
            $price = (float)($tiers[$t]['price'] ?? 0);
            if ($price < $minPrice) {
                echo json_encode(['success' => false, 'message' => "The price for tier '{$t}' cannot be less than the category minimum of ${$minPrice}."]);
                exit;
            }
        }
    }

    $db->beginTransaction();

    // 2. Insert or update the package
    // For simplicity of a draft wizard, we will insert a new package or update if an id is passed (in a real app)
    $stmt = $db->prepare("INSERT INTO packages (provider_id, category_id, title, description, search_tags, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $categoryId, $title, $description, $tags, $status]);
    $packageId = (int)$db->lastInsertId();

    // 3. Save Tiers
    if (!empty($tiers)) {
        $tierStmt = $db->prepare("INSERT INTO package_pricing_tiers (package_id, tier_type, name, description, price, delivery_days, revisions) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach (['basic', 'standard', 'premium'] as $type) {
            $t = $tiers[$type];
            $tierName = trim($t['title'] ?? ucfirst($type) . ' Package');
            $tierDesc = trim($t['description'] ?? '');
            $price = (float)($t['price'] ?? 0);
            $delivery = (int)($t['delivery'] ?? 1);
            $revisions = (int)($t['revisions'] ?? 0);

            $tierStmt->execute([$packageId, $type, $tierName, $tierDesc, $price, $delivery, $revisions]);
        }
    }

    // 4. Save FAQs
    if (!empty($faqs)) {
        $faqStmt = $db->prepare("INSERT INTO package_faqs (package_id, question, answer) VALUES (?, ?, ?)");
        foreach ($faqs as $faq) {
            $q = trim($faq['question'] ?? '');
            $a = trim($faq['answer'] ?? '');
            if (!empty($q) && !empty($a)) {
                $faqStmt->execute([$packageId, $q, $a]);
            }
        }
    }

    // 5. Save Requirements
    if (!empty($requirements)) {
        $reqStmt = $db->prepare("INSERT INTO package_requirements (package_id, requirement_text, response_type, is_mandatory) VALUES (?, ?, ?, ?)");
        foreach ($requirements as $req) {
            $qText = trim($req['question_text'] ?? '');
            $rType = in_array($req['response_type'] ?? 'text', ['text', 'file']) ? $req['response_type'] : 'text';
            $isMand = (int)($req['is_required'] ?? 1);

            if (!empty($qText)) {
                $reqStmt->execute([$packageId, $qText, $rType, $isMand]);
            }
        }
    }

    // 6. Save Gallery Files
    $galleryStmt = $db->prepare("INSERT INTO package_gallery (package_id, file_path, media_type, is_primary) VALUES (?, ?, 'image', ?)");
    foreach ($gallery as $index => $base64Data) {
        if (empty($base64Data)) continue;

        // Decode and write to folder
        $filePath = saveImageFile($base64Data, $packageId, $index + 1);
        if ($filePath) {
            $isPrimary = ($index === 0) ? 1 : 0;
            $galleryStmt->execute([$packageId, $filePath, $isPrimary]);
        }
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Package successfully saved.',
        'package_id' => $packageId
    ]);

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Error saving package data: ' . $e->getMessage()]);
}

/**
 * Decodes base64 string and writes file to disk
 */
function saveImageFile($base64Str, $packageId, $index) {
    if (empty($base64Str)) return null;

    if (preg_match('/^data:image\/(\w+);base64,/', $base64Str, $typeMatches)) {
        $type = strtolower($typeMatches[1]);
        if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return null;
        }

        $data = substr($base64Str, strpos($base64Str, ',') + 1);
        $data = base64_decode($data);
        if ($data === false) {
            return null;
        }

        $uploadDir = 'c:/xampp/htdocs/creda/uploads/gallery/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = 'pkg_' . $packageId . '_' . $index . '_' . time() . '.' . $type;
        $filePath = $uploadDir . $fileName;

        if (file_put_contents($filePath, $data) !== false) {
            return 'uploads/gallery/' . $fileName;
        }
    }

    return null;
}
