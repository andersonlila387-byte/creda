<?php
/**
 * DashboardController.php
 * Securely handles data fetching for the Workspace Dashboard using PDO.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
$db = getDBConnection();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    // If somehow reached without session (head.php should block this, but safety first)
    die("Unauthorized Access");
}

// 1. Fetch Active Projects/Jobs (Where the user is either the client or the provider and status is in_progress)
$stmt = $db->prepare("
    SELECT p.id, p.slug, p.title, p.status, p.created_at, u.full_name as client_name
    FROM projects p
    LEFT JOIN users u ON p.client_id = u.id
    WHERE (p.client_id = :uid OR EXISTS (
        SELECT 1 FROM proposals pr WHERE pr.project_id = p.id AND pr.provider_id = :uid2 AND pr.status = 'accepted'
    ))
    AND p.status = 'in_progress'
    ORDER BY p.updated_at DESC LIMIT 4
");
$stmt->execute([':uid' => $user_id, ':uid2' => $user_id]);
$active_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch milestones for each active job
foreach ($active_jobs as &$job) {
    $m_stmt = $db->prepare("SELECT * FROM milestones WHERE project_id = :pid ORDER BY id ASC");
    $m_stmt->execute([':pid' => $job['id']]);
    $job['milestones'] = $m_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate progress
    $total_milestones = count($job['milestones']);
    $completed_milestones = 0;
    foreach ($job['milestones'] as $m) {
        if ($m['status'] == 'paid' || $m['status'] == 'approved') {
            $completed_milestones++;
        }
    }
    $job['progress_percentage'] = $total_milestones > 0 ? round(($completed_milestones / $total_milestones) * 100) : 0;
}
unset($job);

// 2. Fetch Recommended Professionals (Users with provider role and talent profile details)
$stmt = $db->prepare("
    SELECT u.id, u.full_name, u.email, tp.avatar_url, tp.title, tp.bio, tp.skills, tp.rating, tp.job_success_percentage
    FROM users u
    JOIN talent_profiles tp ON u.id = tp.user_id
    WHERE u.primary_role = 'provider' AND u.status = 'active' AND u.id != :uid
    ORDER BY tp.rating DESC, u.id DESC LIMIT 4
");
$stmt->execute([':uid' => $user_id]);
$recommended_talent = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total active providers count
$stmt_count = $db->prepare("SELECT COUNT(*) FROM users WHERE primary_role = 'provider' AND status = 'active'");
$stmt_count->execute();
$total_talent_count = $stmt_count->fetchColumn();

// 3. Fetch Open Work Posted by Clients
$stmt = $db->prepare("
    SELECT p.id, p.slug, p.category, p.title, p.description, p.budget, p.created_at, 
           (SELECT COUNT(*) FROM proposals WHERE project_id = p.id) as proposal_count
    FROM projects p
    WHERE p.status = 'open' AND p.client_id != :uid
    ORDER BY p.created_at DESC LIMIT 4
");
$stmt->execute([':uid' => $user_id]);
$open_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Fetch Direct Messages (Recent unique conversations)
$stmt = $db->prepare("
    SELECT m.id, m.content, m.created_at, m.is_read, m.sender_id, u.full_name as sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.receiver_id = :uid
    ORDER BY m.created_at DESC LIMIT 5
");
$stmt->execute([':uid' => $user_id]);
$recent_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 5. Dashboard Stats
$stats = [
    'active_jobs' => count($active_jobs),
    'proposals' => 0,
    'deliverables' => 0,
    'completed' => 0
];
$stmt = $db->prepare("SELECT COUNT(*) FROM proposals WHERE provider_id = :uid OR project_id IN (SELECT id FROM projects WHERE client_id = :uid2)");
$stmt->execute([':uid' => $user_id, ':uid2' => $user_id]);
$stats['proposals'] = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM projects WHERE status = 'completed' AND (client_id = :uid OR id IN (SELECT project_id FROM proposals WHERE provider_id = :uid2 AND status = 'accepted'))");
$stmt->execute([':uid' => $user_id, ':uid2' => $user_id]);
$stats['completed'] = $stmt->fetchColumn();
