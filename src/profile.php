<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/database/tutorclass.php';
require_once __DIR__ . '/database/userclass.php';
require_once __DIR__ . '/database/qualificationclass.php';

$session = Session::getInstance();

if (!$session->isLoggedIn()) {
    header('Location: /register_page.php');
    exit();
}

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
    <link href="https://fonts.googleapis.com/css2?family=Acme&display=swap" rel="stylesheet">
</head>
<body>
<header class="header">
         <div class="name">
                <a href="#first-section" style="text-decoration:none;"><span style="color: #03254E;">Learn</span><span style="color: black;">2</span><span style="color: #32533D;">Day</span></a>
            </div>
        <div class="search-bar">
            <input type="text" placeholder="Search..." />
            <button class="search-button">
                <span class="material-symbols-outlined">search</span>
            </button>
            <button class="filter-button">
                <span class="material-symbols-outlined">filter_alt</span>
            </button>
        </div>
        <div class="access-profile">
            <?php if ($session->isLoggedIn()): ?>
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
            <?php else: ?>
                <button id="profile-button">
                    <span class="material-symbols-outlined">account_circle</span>
                </button>
                <div id="profile-inner" class="profile">
                    <form action="/actions/login.php" method="post" class="login-popup">
                        <input type="text" name="username" placeholder="Username" required />
                        <input type="password" name="password" placeholder="Password" required />
                        <button type="submit" class="login-btn">Log In</button>
                        <div class="divider">or</div>
                        <a href='/register_page.php'><button type="button" class="signup-btn">Sign Up</button></a>
                        <a href="#" class="reset-link">Reset your password</a>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <main>
    <div class="container">
        <div class="profile-header">
            <img src="/uploads/profiles/<?= htmlspecialchars($profile->profile_image) ?>" 
                alt="Profile Picture" 
                class="profile-picture"
                onerror="this.src='/uploads/profiles/default.png'">
            <div class="name-only">
                <span class="badge <?= $profile_type === 'Tutor' ? 'tutor-badge' : '' ?>">
                    <?= htmlspecialchars($profile_type) ?>
                </span>
                <div class="name-username">
                    <h1><?= htmlspecialchars($profile->name) ?></h1>
                    <p class="username"> @<?= htmlspecialchars($user->username) ?></p>
                </div>
                <?php if ($session->getUser()->username === $profile_username): ?>
                    <a href="/edit_profile.php" class="edit-profile-btn">Edit Profile</a>
                <?php endif; ?>
            </div>
        </div>    
        <div class="other-flex-wrapper">
            <div class="about-container info-left">
                <h2 class="section-title">About</h2>
                <p class="about-section">
                    <?= !empty($profile->description) ? nl2br(htmlspecialchars($profile->description)) : 'No description provided.' ?>
                </p>
            </div>
            <div class="image-stack">
                <img src="/images/blue_blob.png" alt="Blue Blob" class="blue-blob">
                <img src="/images/teacher_description.png" alt="Information Icon" class="description-image">
            </div>
        </div>    
        <div class="about-flex-wrapper">
            <div class="image-stack">
                <img src="/images/blue_blob.png" alt="Blue Blob" class="blue-blob">
                <img src="/images/teacher_info.png" alt="Information Icon" class="info-image">
            </div>
            <div class="about-container info-right">
                <h2 class="section-title">Information</h2>
                <div class="about-section">
                    <?php if ($age): ?>
                        <p><strong>Age:</strong> <?= $age ?> years old</p>
                    <?php endif; ?>
                    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($profile->date_of_birth) ?></p>
                    <p><strong>Member Since:</strong> <?= date('F Y', strtotime($user->created_at ?? 'now')) ?></p>
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
    'Phylosophy' => '/images/phylosophy.gif',
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
                $name = $subject['SUBJECT'];
                $imagePath = $subjectImages[$name] ?? '/images/subjects/default.jpg'; // fallback image
            ?>
                <div class="subject-card">
                    <div class="subject-name"><?= htmlspecialchars($name) ?></div>
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
                <div class="subject-card">
                    <div class="subject-name"><?= htmlspecialchars($language) ?></div>
                    <img src="<?= htmlspecialchars($flagUrl) ?>" alt="<?= htmlspecialchars($language) ?> image" class="subject-image" />
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
        </div>
    </div>
            </main>
    <footer class="rights">
            <p>© 2025 Learn2Day. All rights reserved.</p>
    </footer>
    <script src="scripts/homepage_script.js"></script>
</body>
</html>