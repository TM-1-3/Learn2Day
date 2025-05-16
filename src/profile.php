<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/database/userclass.php';

$session = Session::getInstance();

// Check if user is logged in
if (!$session->isLoggedIn()) {
    header('Location: /login.php');
    exit();
}

// Get the profile ID from URL or use logged-in user's ID
$profile_id = $_GET['id'] ?? $session->getUserId();
$user = User::get_user_by_id((int)$profile_id);
$student = Student::getById((int)$profile_id);

// Check if profile exists
if (!$user || !$student) {
    header('Location: /');
    exit();
}

// Calculate age from date of birth
$age = null;
if (!empty($student->date_of_birth)) {
    $dob = new DateTime($student->date_of_birth);
    $now = new DateTime();
    $age = $dob->diff($now)->y;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($student->name) ?>'s Profile</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .profile-header {
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 20px;
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #e0e0e0;
            margin-right: 30px;
        }
        .profile-info h1 {
            margin: 0;
            color: #2c3e50;
        }
        .profile-info p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        .badge {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            margin-right: 5px;
        }
        .profile-details {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .section-title {
            color: #2c3e50;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
            margin-top: 0;
        }
        .about-section {
            margin-bottom: 30px;
        }
        .edit-profile-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 20px;
            text-decoration: none;
            display: inline-block;
        }
        .edit-profile-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-header">
            <img src="/uploads/profiles/<?= htmlspecialchars($student->profile_image) ?>" 
                 alt="Profile Picture" 
                 class="profile-picture"
                 onerror="this.src='/images/default-profile.jpg'">
            <div class="profile-info">
                <h1><?= htmlspecialchars($student->name) ?></h1>
                <p><?= htmlspecialchars($student->school_institution) ?></p>
                <?php if ($age): ?>
                    <p><?= $age ?> years old</p>
                <?php endif; ?>
                <span class="badge"><?= htmlspecialchars($user->type) ?></span>
                <?php if ($session->getUserId() === (int)$profile_id): ?>
                    <a href="/edit_profile.php" class="edit-profile-btn">Edit Profile</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-details">
            <div class="about-section">
                <h2 class="section-title">About</h2>
                <p><?= !empty($student->description) ? nl2br(htmlspecialchars($student->description)) : 'No description provided.' ?></p>
            </div>

            <div class="personal-info">
                <h2 class="section-title">Personal Information</h2>
                <p><strong>Date of Birth:</strong> <?= htmlspecialchars($student->date_of_birth) ?></p>
                <p><strong>School/Institution:</strong> <?= htmlspecialchars($student->school_institution) ?></p>
                <p><strong>Member Since:</strong> <?= date('F Y', strtotime($user->created_at ?? 'now')) ?></p>
            </div>
        </div>
    </div>
</body>
</html>