<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/database/userclass.php';

$session = Session::getInstance();

// Get the requested user ID from URL parameter
$requested_user_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// If no ID specified and user is logged in, show their own profile
if (!$requested_user_id && $session->isLoggedIn()) {
    $requested_user_id = $session->getUserId();
}

// If still no ID, redirect to home
if (!$requested_user_id) {
    header('Location: /');
    exit();
}

$student = null;
$user = null;
$errors = [];
$is_own_profile = ($session->isLoggedIn() && $requested_user_id === $session->getUserId());

try {
    // Get student profile
    $student = Student::getById($requested_user_id);
    
    if (!$student) {
        $errors[] = 'Profile not found';
    } else {
        // Get user information
        $user_data = User::get_user_by_id($requested_user_id);
        if ($user_data) {
            $user = new User((int)$user_data['id'], $user_data['username']);
        }
    }
} catch (Exception $e) {
    $errors[] = 'Error loading profile: ' . $e->getMessage();
}

// Calculate age from date of birth
$age = null;
if ($student) {
    $dob = new DateTime($student->date_of_birth);
    $now = new DateTime();
    $age = $now->diff($dob)->y;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $student ? htmlspecialchars($student->name) . "'s Profile" : 'Profile' ?></title>
    <link rel="stylesheet" href="profile_style.css">
    <style>
        /* ... (keep your existing styles) ... */
        .profile-actions {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($student && $user): ?>
            <div class="profile-header">
                <img src="/uploads/profiles/<?= htmlspecialchars($student->profile_image) ?>" 
                     alt="Profile Image" 
                     class="profile-image"
                     onerror="this.src='/images/default-profile.png'">
                <div class="profile-info">
                    <h1 class="profile-name"><?= htmlspecialchars($student->name) ?></h1>
                    <p class="profile-username">@<?= htmlspecialchars($user->username) ?></p>
                    <?php if ($age): ?>
                        <p>Age: <?= $age ?></p>
                    <?php endif; ?>
                    
                    <div class="profile-actions">
                        <?php if ($is_own_profile): ?>
                            <a href="/edit_profile.php" class="edit-profile-btn">Edit Profile</a>
                        <?php else: ?>
                            <button class="message-btn">Send Message</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="profile-details">
                <div class="detail-row">
                    <span class="detail-label">Date of Birth:</span>
                    <span class="detail-value"><?= htmlspecialchars($student->date_of_birth) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">School/Institution:</span>
                    <span class="detail-value"><?= htmlspecialchars($student->school_institution) ?></span>
                </div>
            </div>

            <?php if (!empty($student->description)): ?>
                <div class="profile-description">
                    <h3>About Me</h3>
                    <p><?= nl2br(htmlspecialchars($student->description)) ?></p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p>Profile not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>