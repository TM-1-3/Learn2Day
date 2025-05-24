<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/database/tutorclass.php';
require_once __DIR__ . '/database/userclass.php';
require_once __DIR__ . '/database/qualificationclass.php';
require_once __DIR__ . '/database/adminclass.php';

$session = Session::getInstance();

if (!$session->isLoggedIn()) {
    header('Location: /register_page.php');
    exit();
}

$myuser = $session->getUser();

$profile_username = $_GET['id'] ?? $session->getUser()->username;
$user = User::get_user_by_username($profile_username);

if (!$user) {
    header('Location: /');
    exit();
}

$profile = null;
$profile_type = $user->type;

if ($user->type === 'STUDENT') {
    $profile = Student::getByUsername($profile_username);
} elseif ($user->type === 'TUTOR') {
    $profile = Tutor::getByUsername($profile_username);
} elseif ($user->type === 'ADMIN') {
    $profile = Admin::getByUsername($profile_username);
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
$languages = [];
if ($user->type === 'TUTOR') {
    $qual = Qualifications::getTutorQualifications($profile_username);
    $subjects = $qual['subjects'] ?? [];
    $languages = $qual['languages'] ?? [];
} elseif ($user->type === 'STUDENT') {
    $qual = Qualifications::getStudentNeeds($profile_username);
    $subjects = $qual['subjects'] ?? [];
    $languages = $qual['languages'] ?? [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($profile->name) ?>'s Profile</title>
    <link rel="stylesheet" href="styles/profile.css">
    <link rel="stylesheet" href="styles/homepage.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>

<body>
    <header class="header">
        <div class="site-name">
            <a href="/homepage.php" class="main-page">Learn2Day</a>
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search..." />
            <button class="search-button">
                <span class="material-symbols-outlined">search</span>
            </button>
            <div class="filter-dropdown">
                <button type="button" class="filter-button">
                    <span class="material-symbols-outlined">filter_alt</span>
                </button>
                <div class="filter-options">
                    <h4>Filter by Subject</h4>
                    <?php
                    foreach ($allSubjects as $subject): ?>
                        <label>
                            <input type="checkbox" name="subjects[]" value="<?= htmlspecialchars($subject) ?>"
                                <?= (isset($_GET['subjects']) && in_array($subject, $_GET['subjects'])) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($subject) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="access-profile">

            <?php $current_user = $session->getUser(); ?>
            <button id="profile-button">
                <span class="material-symbols-outlined">account_circle</span>
                <?= htmlspecialchars($current_user->username) ?>
            </button>
            <div id="profile-inner" class="profile">
                <div class="logout-popup">
                    <a href='/profile.php?id=<?= htmlspecialchars($session->getUserUsername()) ?>' class="viewprofile-btn">View Profile</a>
                    <hr size="18">
                    <form action="actions/logout.php" method="post">
                        <button type="submit" class="logout-btn">Log Out</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="profile-header">
            <img src="/uploads/profiles/<?= htmlspecialchars($profile->profile_image) ?>"
                alt="Profile Picture"
                class="profile-picture"
                onerror="this.src='/uploads/profiles/default.png'">
            <div class="profile-info">
                <h1><?= htmlspecialchars($profile->name) ?></h1>
                <p class="username">@<?= htmlspecialchars($user->username) ?></p>
                <?php if ($age): ?>
                    <p><?= $age ?> years old</p>
                <?php endif; ?>
                <span class="badge <?= strtolower($profile_type) ?>-badge">
                    <?= htmlspecialchars($profile_type) ?>
                </span>
                <?php if ($session->getUser()->username === $profile_username): ?>
                    <a href="/edit_profile.php" class="edit-profile-btn">Edit Profile</a>
                <?php endif; ?>
                <?php if ($myuser->type == 'ADMIN' && $user->type !== 'ADMIN'): ?>
                    <form action="/actions/ban.php" method="post" class="delete-user-form">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($profile_username) ?>">
                        <button type="submit" class="delete-user-btn">Ban User</button>
                    </form>
                    <form action="/actions/promotion.php" method="post" class="promote-user-form">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($profile_username) ?>">
                        <button type="submit" class="promote-user-btn">Promote to Admin</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-details">
            <div class="about-section">
                <h2 class="section-title">About</h2>
                <p><?= !empty($profile->description) ? nl2br(htmlspecialchars($profile->description)) : 'No description provided.' ?></p>
            </div>

            <?php if ($profile_type !== 'ADMIN'): ?>
                <div class="personal-info">
                    <h2 class="section-title">Personal Information</h2>
                    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($profile->date_of_birth) ?></p>
                    <p><strong>Member Since:</strong> <?= date('F Y', strtotime($user->created_at ?? 'now')) ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($subjects)): ?>
                <div class="skills-section">
                    <h2 class="section-title">Subjects</h2>
                    <div class="skills-list">
                        <?php foreach ($subjects as $subject): ?>
                            <div class="skill-item">
                                <?= htmlspecialchars($subject['SUBJECT'] ?? $subject['subject'] ?? '') ?>
                                <?php if (isset($subject['GRADE']) || isset($subject['STUDENT_LEVEL'])): ?>
                                    (Grade <?= htmlspecialchars($subject['GRADE'] ?? $subject['STUDENT_LEVEL']) ?>)
                                <?php elseif (isset($subject['LEVEL']) || isset($subject['TUTOR_LEVEL'])): ?>
                                    (<?= htmlspecialchars($subject['LEVEL'] ?? $subject['TUTOR_LEVEL']) ?>)
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($languages)): ?>
                <div class="languages-section">
                    <h2 class="section-title">Languages</h2>
                    <div class="languages-list">
                        <?php foreach ($languages as $language): ?>
                            <div class="language-item">
                                <?= htmlspecialchars($language) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="scripts/homepage_script.js"></script>
</body>

</html>