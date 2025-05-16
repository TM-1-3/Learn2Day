<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/database/tutorclass.php';
require_once __DIR__ . '/database/userclass.php';

$session = Session::getInstance();

if (!$session->isLoggedIn()) {
    header('Location: /login.php');
    exit();
}

$profile_id = $_GET['id'] ?? $session->getUserId();
$user = User::get_user_by_id((int)$profile_id);

if (!$user) {
    header('Location: /');
    exit();
}

$profile = null;
$profile_type = '';
if ($user->type === 'STUDENT') {
    $profile = Student::getById((int)$profile_id);
    $profile_type = 'Student';
} elseif ($user->type === 'TUTOR') {
    $profile = Tutor::getById((int)$profile_id);
    $profile_type = 'Tutor';
}

if (!$profile) {
    header('Location: /');
    exit();
}

$age = null;
if (!empty($profile->date_of_birth)) {
    $dob = new DateTime($profile->date_of_birth);
    $now = new DateTime();
    $age = $dob->diff($now)->y;
}

$subjects = [];
$degrees = [];
if ($user->type === 'TUTOR') {
    /*$subjects = Tutor::getSubjects((int)$profile_id);
    $degrees = Tutor::getDegrees((int)$profile_id);*/
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($profile->name) ?>'s Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="container">
        <div class="profile-header">
            <img src="/uploads/profiles/<?= htmlspecialchars($profile->profile_image) ?>" 
                 alt="Profile Picture" 
                 class="profile-picture"
                 onerror="this.src='/images/default-profile.jpg'">
            <div class="profile-info">
                <h1><?= htmlspecialchars($profile->name) ?></h1>
                <?php if ($profile_type === 'Student'): ?>
                    <p><?= htmlspecialchars($profile->school_institution) ?></p>
                <?php endif; ?>
                <?php if ($age): ?>
                    <p><?= $age ?> years old</p>
                <?php endif; ?>
                <span class="badge <?= $profile_type === 'Tutor' ? 'tutor-badge' : '' ?>">
                    <?= htmlspecialchars($profile_type) ?>
                </span>
                <?php if ($session->getUserId() === (int)$profile_id): ?>
                    <a href="/edit_profile.php" class="edit-profile-btn">Edit Profile</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-details">
            <div class="about-section">
                <h2 class="section-title">About</h2>
                <p><?= !empty($profile->description) ? nl2br(htmlspecialchars($profile->description)) : 'No description provided.' ?></p>
            </div>

            <div class="personal-info">
                <h2 class="section-title">Personal Information</h2>
                <p><strong>Date of Birth:</strong> <?= htmlspecialchars($profile->date_of_birth) ?></p>
                <?php if ($profile_type === 'Student'): ?>
                    <p><strong>School/Institution:</strong> <?= htmlspecialchars($profile->school_institution) ?></p>
                <?php endif; ?>
                <p><strong>Member Since:</strong> <?= date('F Y', strtotime($user->created_at ?? 'now')) ?></p>
            </div>

            <?php if ($profile_type === 'Tutor' && !empty($subjects)): ?>
                <div class="skills-section">
                    <h2 class="section-title">Subjects</h2>
                    <div class="skills-list">
                        <?php foreach ($subjects as $subject): ?>
                            <div class="skill-item">
                                <?= htmlspecialchars($subject['SUBJECT']) ?> 
                                (Grade <?= htmlspecialchars($subject['GRADE']) ?>)
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($profile_type === 'Tutor' && !empty($degrees)): ?>
                <div class="degrees-section">
                    <h2 class="section-title">Education</h2>
                    <div class="degrees-list">
                        <?php foreach ($degrees as $degree): ?>
                            <div class="degree-item">
                                <?= htmlspecialchars($degree['DEGREE']) ?> 
                                from <?= htmlspecialchars($degree['UNIVERSITY']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>