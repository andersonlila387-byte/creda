<?php
/**
 * MyProjectsController.php
 * Securely handles data fetching for the My Projects desk using PDO.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
$db = getDBConnection();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("Unauthorized Access");
}

// 1. Fetch All Personal Projects (User is client OR user is hired provider)
$stmt = $db->prepare("
    SELECT p.*, 
           c.full_name as client_name,
           (SELECT c2.id FROM contracts c2 WHERE c2.client_id = p.client_id AND c2.title = p.title ORDER BY c2.id DESC LIMIT 1) as contract_id,
           (SELECT COUNT(*) FROM proposals pr WHERE pr.project_id = p.id) as proposal_count,
           (SELECT u.full_name FROM proposals pr JOIN users u ON pr.provider_id = u.id WHERE pr.project_id = p.id AND pr.status = 'accepted' LIMIT 1) as hired_provider_name
    FROM projects p
    LEFT JOIN users c ON p.client_id = c.id
    WHERE p.client_id = :uid 
       OR EXISTS (SELECT 1 FROM proposals pr WHERE pr.project_id = p.id AND pr.provider_id = :uid2 AND pr.status = 'accepted')
    ORDER BY p.updated_at DESC
");
$stmt->execute([':uid' => $user_id, ':uid2' => $user_id]);
$all_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

$my_projects = [];
$stats = [
    'active' => 0,
    'review' => 0, // Milestones in review
    'open' => 0,
    'completed' => 0,
    'total' => count($all_projects),
    'bids_received' => 0
];

foreach ($all_projects as $proj) {
    // Fetch milestones for this project
    $m_stmt = $db->prepare("SELECT * FROM milestones WHERE project_id = :pid ORDER BY id ASC");
    $m_stmt->execute([':pid' => $proj['id']]);
    $milestones = $m_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $proj['milestones'] = $milestones;
    
    // Calculate progress
    $total_m = count($milestones);
    $completed_m = 0;
    $has_review = false;
    $locked_escrow = 0;
    $total_budget = 0;

    foreach ($milestones as $m) {
        $total_budget += $m['amount'];
        if ($m['status'] == 'paid' || $m['status'] == 'approved') {
            $completed_m++;
        }
        if ($m['status'] == 'in_review') {
            $has_review = true;
        }
        if ($m['status'] == 'funded') {
            $locked_escrow += $m['amount'];
        }
    }
    
    $proj['progress_percentage'] = $total_m > 0 ? round(($completed_m / $total_m) * 100) : 0;
    $proj['locked_escrow'] = $locked_escrow;
    $proj['calculated_budget'] = $total_budget > 0 ? $total_budget : $proj['budget'];
    $proj['has_review'] = $has_review;
    
    // Categorize for stats
    if ($proj['status'] === 'in_progress') {
        $stats['active']++;
    } elseif ($proj['status'] === 'open') {
        $stats['open']++;
        if ($proj['client_id'] == $user_id) {
            $stats['bids_received'] += $proj['proposal_count'];
        }
    } elseif ($proj['status'] === 'completed') {
        $stats['completed']++;
    }
    
    if ($has_review) {
        $stats['review']++;
    }
    
    $my_projects[] = $proj;
}
?>
