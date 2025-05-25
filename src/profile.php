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
$profile_type = '';

if ($user->type === 'STUDENT') {
    $profile = Student::getByUsername($profile_username);
    $profile_type = 'Student';
} elseif ($user->type === 'TUTOR') {
    $profile = Tutor::getByUsername($profile_username);
    $profile_type = 'Tutor';
} elseif ($user->type === 'ADMIN') {
    $profile = Admin::getByUsername($profile_username);
    $profile_type = 'Admin';
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
        <div class="name">
            <a href="/homepage.php" class="main-page" style="text-decoration:none;"><span style="color: #03254E;">Learn</span><span style="color: black;">2</span><span style="color: #32533D;">Day</span></a>
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
                <form action="actions/logout.php" method="post" class="logout-popup">
                <a href='/profile.php?id=<?= htmlspecialchars($session->getUserUsername()) ?>' class="viewprofile-btn">View Profile</a>
                    <hr size="18">
                    <button type="submit" class="logout-btn">Log Out</button>
                </form>
            </div>
        </div>
    </header>
        <main>
            <div class="container">
                <h1 class="profile-title">Profile of <?= htmlspecialchars($profile->name) ?></h1>
                <div class="profile-container">
                <div class="profile-header1">
                    <div class="profile-header-inner1">
                        <img src="/uploads/profiles/<?= htmlspecialchars($profile->profile_image) ?>" 
                        alt="Profile Picture" 
                        class="profile-picture"
                        onerror="this.src='/uploads/profiles/default.png'">
                        <div class="name-only">
                            <div class="name-username">
                                <?php if ($user->type == 'STUDENT'): ?>
                                    <h1 style="color: #32533D"><?= htmlspecialchars($profile->name) ?></h1>
                                <?php elseif($user->type == 'TUTOR'): ?>
                                    <h1 style="color: #03254E"><?= htmlspecialchars($profile->name) ?></h1>
                                <?php endif; ?>
                                <p class="username"> @<?= htmlspecialchars($user->username) ?></p>
                                <span class="badge <?= $profile_type === 'Tutor' ? 'tutor-badge' : '' ?>">
                                    <?= htmlspecialchars($profile_type) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                         <div class="profile-info">
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
                        <?php if ($session->getUser()->username === $profile_username): ?>
                            <?php if ($user->type == 'STUDENT'): ?>
                                <a href="/edit_profile.php" class="edit-profile-btn" style="background-color: #32533D">Edit Profile</a>
                            <?php elseif($user->type == 'TUTOR'): ?>
                                <a href="/edit_profile.php" class="edit-profile-btn" style="background-color: #03254E">Edit Profile</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>    
                <div class="profile-header2">
                    <div class="profile-header-inner2">
                        <div class="other-flex-wrapper">
                            <div class="profile-details-container">
                                <div class="age">
                                    <?php if ($user->type == 'STUDENT'): ?>
                                        <div class="profile-details" style="background-color: #32533D">Age</div>
                                    <?php elseif($user->type == 'TUTOR'): ?>
                                        <div class="profile-details" style="background-color: #03254E">Age</div>
                                    <?php endif; ?>
                                    <div class="profile-description1">
                                        <?php if ($age): ?>
                                            <p><?= $age ?> years old</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="member">
                                    <?php if ($user->type == 'STUDENT'): ?>
                                        <div class="profile-details" style="background-color: #32533D; width: 150px;">Member since</div>
                                    <?php elseif($user->type == 'TUTOR'): ?>
                                        <div class="profile-details" style="background-color: #03254E; width: 150px;">Member since</div>
                                    <?php endif; ?>
                                    <div class="profile-description1">
                                        <p><?= date('F Y', strtotime($user->created_at ?? 'now')) ?></p>
                                    </div>
                                    </div>
                                </div>
                                <?php if ($user->type == 'STUDENT'): ?>
                                    <div class="profile-details" style="background-color: #32533D">About</div>
                                <?php elseif($user->type == 'TUTOR'): ?>
                                    <div class="profile-details" style="background-color: #03254E">About</div>
                                <?php elseif($user->type == 'ADMIN'): ?>
                                    <div class="profile-details" style="background-color: #FFD670">About</div>
                                <?php endif; ?>
                                <div class="profile-description2">
                                    <?= !empty($profile->description) ? nl2br(htmlspecialchars($profile->description)) : 'No description provided.' ?>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
                <?php
                $subjectImages = [
                'Portuguese' => '/images/portuguese.gif',
                'Math' => '/images/math.gif',
                'Physics and Chemistry' => '/images/physics.gif',
                'Natural Sciences' => '/images/naturalSciences.gif',
                'Biology and Geology' => '/images/biology.gif',
                'History' => '/images/history.gif',
                'English' => '/images/english.gif',
                'French' => '/images/french.gif',
                'Spanish' => '/images/spanish.gif',
                'German' => '/images/german.gif',
                'Social and Environmental Studies' => '/images/social.gif',
                'History and Geography of Portugal' => '/images/historyPortugal.gif',
                'Geography' => '/images/geography.gif',
                'Philosophy' => '/images/philosophy.gif',
                'Economics' => '/images/economics.gif',
                'Drawing' => '/images/drawing.gif',
                'Mathematics Applied to Social Sciences' => '/images/mathSocial.gif',
                'History and Culture of the Arts' => '/images/historyArt.gif',
                'Descriptive Geometry' => '/images/geometry.gif'
                ];
                ?>
                <?php if (!empty($subjects)): ?>
                    <div class="about-container">
                        <h2 class="section-title">Subjects</h2>
                        <div class="about-section subject-cards-container">
                            <?php foreach ($subjects as $subject): 
                                $name = $subject['SUBJECT'] ?? $subject['subject'] ?? '';
                                $grade = $subject['GRADE'] ?? $subject['STUDENT_LEVEL'] ?? $subject['LEVEL'] ?? $subject['TUTOR_LEVEL'] ?? '';
                                $imagePath = $subjectImages[$name] ?? '/images/subjects/default.jpg'; // fallback image
                            ?>
                            <div class="subject-card">
                                <div class="subject-name" style="margin-bottom: 0px;">
                                    <?= htmlspecialchars($name) ?>
                                </div>
                                <?php if (!empty($grade)): ?>
                                    <div class="subject-grade" style="font-size: 15px; margin-top: 0; margin-bottom: 2px;">
                                        <?= htmlspecialchars($grade) ?>
                                    </div>
                                <?php endif; ?>
                                <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($name) ?> image" class="subject-image" />
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php
                $languageFlags = [
                    'English' => 'https://cdn-icons-png.flaticon.com/512/206/206592.png',
                    'Spanish' => 'https://cdn-icons-png.flaticon.com/512/330/330557.png',
                    'French' => 'https://cdn-icons-png.flaticon.com/512/206/206657.png',
                    'German' => 'https://cdn-icons-png.flaticon.com/512/330/330523.png',
                    'Mandarin' => 'https://cdn-icons-png.flaticon.com/512/206/206818.png',
                    'Japanese' => 'https://cdn-icons-png.flaticon.com/512/206/206789.png',
                    'Portuguese' => 'https://cdn-icons-png.flaticon.com/512/206/206628.png',
                    'Russian' => 'https://cdn-icons-png.flaticon.com/512/940/940307.png',
                    'Arabic' => 'https://cdn-icons-png.flaticon.com/512/330/330552.png',
                    'Italian' => 'https://cdn-icons-png.flaticon.com/512/330/330672.png'
                ];
                ?>
                <?php if (!empty($languages)): ?>
                    <div class="about-container">
                        <h2 class="section-title">Languages</h2>
                        <div class="about-section subject-cards-container">
                            <?php foreach ($languages as $language): 
                                $flagUrl = $languageFlags[$language] ?? null;
                            ?>
                                <div class="language-card">
                                    <div class="subject-name"><?= htmlspecialchars($language) ?></div>
                                    <img src="<?= htmlspecialchars($flagUrl) ?>" alt="<?= htmlspecialchars($language) ?> image" class="subject-image" />
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <footer class="rights">
            <p>© 2025 Learn2Day. All rights reserved.</p>
        </footer>
    </main>
    <script src="scripts/homepage_script.js"></script>
</body>
</html>