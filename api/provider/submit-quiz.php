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

// Verify session provider
$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['user_email'] ?? null;

if (empty($user_id) || empty($user_email)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Session expired.']);
    exit;
}

$inputData = json_decode(file_get_contents('php://input'), true);
if (!$inputData) {
    echo json_encode(['success' => false, 'message' => 'Invalid input parameters.']);
    exit;
}

$categoryId = (int)($inputData['category_id'] ?? 0);
$subtopic = trim($inputData['subtopic'] ?? '');
$userAnswers = $inputData['answers'] ?? []; // Array of [question_id => answer_value]

if ($categoryId <= 0 || empty($subtopic)) {
    echo json_encode(['success' => false, 'message' => 'Category and sub-topic parameters are required.']);
    exit;
}

try {
    $db = getDBConnection();

    // 1. Fetch the correct answers for all questions in this exam
    $stmt = $db->prepare("SELECT id, correct_answer, question_type FROM quiz_questions WHERE category_id = ? AND subtopic = ? ORDER BY id ASC LIMIT 15");
    $stmt->execute([$categoryId, $subtopic]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($questions) === 0) {
        echo json_encode(['success' => false, 'message' => 'No quiz questions found for this topic.']);
        exit;
    }

    $totalQuestions = count($questions);
    $correctCount = 0;

    // 2. Grade responses
    foreach ($questions as $q) {
        $qId = $q['id'];
        $correct = trim(strtolower($q['correct_answer']));
        $userAns = isset($userAnswers[$qId]) ? trim(strtolower($userAnswers[$qId])) : '';

        if ($q['question_type'] === 'text_input') {
            // Text inputs: check if the string matches closely (case-insensitive, trimmed)
            // Remove common duplicate characters/spaces to prevent minor format failures
            $cleanCorrect = preg_replace('/\s+/', ' ', $correct);
            $cleanUserAns = preg_replace('/\s+/', ' ', $userAns);
            if ($cleanCorrect === $cleanUserAns) {
                $correctCount++;
            }
        } else {
            // MCQ: direct letter check
            if ($correct === $userAns) {
                $correctCount++;
            }
        }
    }

    $scorePercentage = (int)round(($correctCount / $totalQuestions) * 100);
    $status = ($scorePercentage >= 80) ? 'passed' : 'failed';

    // 3. Log attempt
    $logStmt = $db->prepare("INSERT INTO provider_quiz_attempts (provider_id, category_id, subtopic, score, status) VALUES (?, ?, ?, ?, ?)");
    $logStmt->execute([$user_id, $categoryId, $subtopic, $scorePercentage, $status]);

    // 4. Upsert/Insert into provider_assessments for this category
    $checkStmt = $db->prepare("SELECT id, score, status FROM provider_assessments WHERE provider_id = ? AND category_id = ?");
    $checkStmt->execute([$user_id, $categoryId]);
    $existingAssessment = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingAssessment) {
        $existingScore = (int)$existingAssessment['score'];
        $existingStatus = $existingAssessment['status'];

        // Update if the new score is higher or if they finally passed
        if ($scorePercentage > $existingScore || ($status === 'passed' && $existingStatus !== 'passed')) {
            $updateStmt = $db->prepare("UPDATE provider_assessments SET score = ?, status = ?, taken_at = CURRENT_TIMESTAMP WHERE id = ?");
            $updateStmt->execute([max($scorePercentage, $existingScore), ($status === 'passed' || $existingStatus === 'passed') ? 'passed' : 'failed', $existingAssessment['id']]);
        }
    } else {
        $insertStmt = $db->prepare("INSERT INTO provider_assessments (provider_id, category_id, score, status) VALUES (?, ?, ?, ?)");
        $insertStmt->execute([$user_id, $categoryId, $scorePercentage, $status]);
    }

    // 5. Backwards Compatibility: Update the main users table with the overall status & best score
    // Retrieve the highest score of any category they have taken
    $bestStmt = $db->prepare("SELECT MAX(score) as best_score, 
                             SUM(CASE WHEN status = 'passed' THEN 1 ELSE 0 END) as passed_count 
                             FROM provider_assessments WHERE provider_id = ?");
    $bestStmt->execute([$user_id]);
    $bestStats = $bestStmt->fetch(PDO::FETCH_ASSOC);

    $overallBestScore = $bestStats['best_score'] ?? $scorePercentage;
    $overallStatus = ($bestStats['passed_count'] > 0) ? 'passed' : 'failed';

    $updateUserStmt = $db->prepare("UPDATE users SET assessment_score = ?, assessment_status = ? WHERE id = ?");
    $updateUserStmt->execute([$overallBestScore, $overallStatus, $user_id]);

    echo json_encode([
        'success' => true,
        'score' => $scorePercentage,
        'status' => $status,
        'correct' => $correctCount,
        'total' => $totalQuestions
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database grading execution failed: ' . $e->getMessage()]);
}
