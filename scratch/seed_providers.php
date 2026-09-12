<?php
require_once __DIR__ . '/../config/database.php';
$db = getDBConnection();

// Create sample verified provider users if they don't exist
$providers = [
    [
        'id' => 10,
        'full_name' => 'Amina Bello',
        'email' => 'amina.bello@example.com',
        'password_hash' => password_hash('Password123!', PASSWORD_BCRYPT),
        'phone_number' => '+2348031234567',
        'primary_role' => 'provider',
        'entity_type' => 'student',
        'status' => 'active',
        'profile' => [
            'title' => 'Senior Full-Stack PHP & Cloud Architect',
            'bio' => 'Full-stack software engineer building robust PHP MVC web applications, high-concurrency MySQL databases, and secure escrow systems.',
            'skills' => 'PHP, MySQL, Tailwind CSS, Laravel, REST APIs, Vue.js',
            'hourly_rate' => 20000.00,
            'rating' => 4.90,
            'rating_count' => 38,
            'job_success_percentage' => 99,
            'completed_projects' => 42,
            'location' => 'Lagos, Nigeria',
            'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150'
        ]
    ],
    [
        'id' => 11,
        'full_name' => 'Chidi Okonkwo',
        'email' => 'chidi.okonkwo@example.com',
        'password_hash' => password_hash('Password123!', PASSWORD_BCRYPT),
        'phone_number' => '+2348029876543',
        'primary_role' => 'provider',
        'entity_type' => 'business',
        'status' => 'active',
        'profile' => [
            'title' => 'UI/UX Product Designer & Design Systems Lead',
            'bio' => 'Passionate UI/UX designer crafting intuitive web & mobile interfaces, wireframes, interactive Figma prototypes, and micro-animations.',
            'skills' => 'Figma, UI/UX Design, Wireframing, User Research, Prototyping, Design Systems',
            'hourly_rate' => 18000.00,
            'rating' => 4.95,
            'rating_count' => 27,
            'job_success_percentage' => 100,
            'completed_projects' => 29,
            'location' => 'Abuja, Nigeria',
            'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150'
        ]
    ],
    [
        'id' => 12,
        'full_name' => 'Fatima Al-Hassan',
        'email' => 'fatima.alhassan@example.com',
        'password_hash' => password_hash('Password123!', PASSWORD_BCRYPT),
        'phone_number' => '+2348055554433',
        'primary_role' => 'provider',
        'entity_type' => 'corporation',
        'status' => 'active',
        'profile' => [
            'title' => 'Data Analyst & Machine Learning Specialist',
            'bio' => 'Data scientist specializing in Python data pipelines, pandas, BigQuery analytics, statistical modeling, and interactive dashboards.',
            'skills' => 'Python, Pandas, SQL, BigQuery, Machine Learning, PowerBI, Tableau',
            'hourly_rate' => 25000.00,
            'rating' => 4.88,
            'rating_count' => 19,
            'job_success_percentage' => 97,
            'completed_projects' => 23,
            'location' => 'Ibadan, Nigeria',
            'avatar_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=150'
        ]
    ],
    [
        'id' => 13,
        'full_name' => 'Emeka Adeleke',
        'email' => 'emeka.adeleke@example.com',
        'password_hash' => password_hash('Password123!', PASSWORD_BCRYPT),
        'phone_number' => '+2348077778899',
        'primary_role' => 'provider',
        'entity_type' => 'student',
        'status' => 'active',
        'profile' => [
            'title' => 'Mobile App Developer (Flutter & React Native)',
            'bio' => 'Cross-platform mobile developer building performant iOS and Android applications with clean architecture and state management.',
            'skills' => 'Flutter, Dart, React Native, Firebase, iOS, Android, REST APIs',
            'hourly_rate' => 22000.00,
            'rating' => 5.00,
            'rating_count' => 15,
            'job_success_percentage' => 100,
            'completed_projects' => 18,
            'location' => 'Enugu, Nigeria',
            'avatar_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150'
        ]
    ]
];

foreach ($providers as $p) {
    // Check if user exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$p['email']]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$u) {
        $stmt_ins = $db->prepare("INSERT INTO users (id, full_name, email, password_hash, phone_number, primary_role, entity_type, email_verified, onboarding_completed, status) VALUES (?, ?, ?, ?, ?, ?, ?, 1, 1, 'active')");
        $stmt_ins->execute([$p['id'], $p['full_name'], $p['email'], $p['password_hash'], $p['phone_number'], $p['primary_role'], $p['entity_type']]);
        $user_id = $p['id'];
    } else {
        $user_id = $u['id'];
        $db->prepare("UPDATE users SET primary_role = 'provider', status = 'active' WHERE id = ?")->execute([$user_id]);
    }

    // Check or Insert Talent Profile
    $stmt_tp = $db->prepare("SELECT id FROM talent_profiles WHERE user_id = ?");
    $stmt_tp->execute([$user_id]);
    $tp_row = $stmt_tp->fetch(PDO::FETCH_ASSOC);

    $prof = $p['profile'];
    if (!$tp_row) {
        $stmt_tp_ins = $db->prepare("INSERT INTO talent_profiles (user_id, title, bio, skills, hourly_rate, rating, rating_count, job_success_percentage, completed_projects, location, avatar_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_tp_ins->execute([
            $user_id, $prof['title'], $prof['bio'], $prof['skills'], $prof['hourly_rate'], $prof['rating'], $prof['rating_count'], $prof['job_success_percentage'], $prof['completed_projects'], $prof['location'], $prof['avatar_url']
        ]);
    } else {
        $stmt_tp_upd = $db->prepare("UPDATE talent_profiles SET title = ?, bio = ?, skills = ?, hourly_rate = ?, rating = ?, rating_count = ?, job_success_percentage = ?, completed_projects = ?, location = ?, avatar_url = ? WHERE user_id = ?");
        $stmt_tp_upd->execute([
            $prof['title'], $prof['bio'], $prof['skills'], $prof['hourly_rate'], $prof['rating'], $prof['rating_count'], $prof['job_success_percentage'], $prof['completed_projects'], $prof['location'], $prof['avatar_url'], $user_id
        ]);
    }
}

echo "Successfully seeded verified provider users and talent profiles!\n";
