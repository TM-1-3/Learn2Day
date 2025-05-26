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

$allSubjects = Qualifications::getAllSubjects();
$allLanguages = Qualifications::getAllLanguages();
$allLevels = Qualifications::getAllTutorLevels();

$searchResults = [];
$selectedSubjects = $_GET['subjects'] ?? [];
$selectedLanguages = $_GET['languages'] ?? [];
$selectedLevels = $_GET['levels'] ?? [];
$searchQuery = trim($_GET['search'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($searchQuery || $selectedSubjects || $selectedLanguages || $selectedLevels)) {
    $db = Database::getInstance();
    // Tutors
    $query = "SELECT T.ID_TUTOR as id, T.NAME, 'tutor' as type, T.PROFILE_IMAGE, T.DESCRIPTION, U.USERNAME
        FROM TUTOR T
        JOIN USERS U ON T.ID_TUTOR = U.USERNAME
        WHERE 1=1";
    $params = [];
    if ($searchQuery) {
        $query .= " AND (T.NAME LIKE ? OR U.USERNAME LIKE ? )";
        $searchParam = "%$searchQuery%";
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    if (!empty($selectedSubjects)) {
        $subjectPlaceholders = implode(',', array_fill(0, count($selectedSubjects), '?'));
        $query .= " AND T.ID_TUTOR IN (SELECT TUTOR FROM TUTOR_SUBJECT WHERE SUBJECT IN ($subjectPlaceholders))";
        foreach ($selectedSubjects as $subject) {
            $params[] = $subject;
        }
    }
    if (!empty($selectedLanguages)) {
        $langPlaceholders = implode(',', array_fill(0, count($selectedLanguages), '?'));
        $query .= " AND T.ID_TUTOR IN (SELECT TUTOR FROM TUTOR_LANGUAGE WHERE LANGUAGE IN ($langPlaceholders))";
        foreach ($selectedLanguages as $lang) {
            $params[] = $lang;
        }
    }
    if (!empty($selectedLevels)) {
        $levelPlaceholders = implode(',', array_fill(0, count($selectedLevels), '?'));
        $query .= " AND T.ID_TUTOR IN (SELECT TUTOR FROM TUTOR_SUBJECT WHERE TUTOR_LEVEL IN ($levelPlaceholders))";
        foreach ($selectedLevels as $level) {
            $params[] = $level;
        }
    }
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $tutorResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Students
    $query_students = "SELECT S.ID_STUDENT as id, S.NAME, 'student' as type, S.PROFILE_IMAGE, S.DESCRIPTION, U.USERNAME
        FROM STUDENT S
        JOIN USERS U ON S.ID_STUDENT = U.USERNAME
        WHERE 1=1";
    $params_students = [];
    if ($searchQuery) {
        $query_students .= " AND (S.NAME LIKE ? OR U.USERNAME LIKE ? )";
        $searchParam = "%$searchQuery%";
        $params_students[] = $searchParam;
        $params_students[] = $searchParam;
    }
    if (!empty($selectedSubjects)) {
        $subjectPlaceholders = implode(',', array_fill(0, count($selectedSubjects), '?'));
        $query_students .= " AND S.ID_STUDENT IN (SELECT STUDENT FROM STUDENT_SUBJECT WHERE SUBJECT IN ($subjectPlaceholders))";
        foreach ($selectedSubjects as $subject) {
            $params_students[] = $subject;
        }
    }
    if (!empty($selectedLanguages)) {
        $langPlaceholders = implode(',', array_fill(0, count($selectedLanguages), '?'));
        $query_students .= " AND S.ID_STUDENT IN (SELECT STUDENT FROM STUDENT_LANGUAGE WHERE LANGUAGE IN ($langPlaceholders))";
        foreach ($selectedLanguages as $lang) {
            $params_students[] = $lang;
        }
    }
    if (!empty($selectedLevels)) {
        $levelPlaceholders = implode(',', array_fill(0, count($selectedLevels), '?'));
        $query_students .= " AND S.ID_STUDENT IN (SELECT STUDENT FROM STUDENT_SUBJECT WHERE STUDENT_LEVEL IN ($levelPlaceholders))";
        foreach ($selectedLevels as $level) {
            $params_students[] = $level;
        }
    }
    $stmt_students = $db->prepare($query_students);
    $stmt_students->execute($params_students);
    $studentResults = $stmt_students->fetchAll(PDO::FETCH_ASSOC);
    $searchResults = array_merge($tutorResults, $studentResults);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($profile->name) ?>'s Profile</title>
    <link rel="stylesheet" href="/styles/profile.css">
    <link rel="stylesheet" href="/styles/homepage.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body>
    <header class="header">
        <div class="site-name">
            <a href="/homepage.php" class="main-page" style="text-decoration:none;"><span style="color: #03254E;">Learn</span><span style="color: black;">2</span><span style="color: #32533D;">Day</span></a>
        </div>
        <form method="GET" action="/homepage.php" class="search-form">
            <div class="search-bar">
                <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($searchQuery) ?>" />
                <button type="submit" class="search-button">
                    <span class="material-symbols-outlined">search</span>
                </button>
                <div class="filter-dropdown">
                    <button type="button" class="filter-button">
                        <span class="material-symbols-outlined">filter_alt</span>
                    </button>
                    <div class="filter-options">
                        <div class="filter-subject">
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
                        <div class="filter-languages">
                            <h4>Filter by Language</h4>
                            <?php
                            foreach ($allLanguages as $language): ?>
                                <label>
                                    <input type="checkbox" name="languages[]" value="<?= htmlspecialchars($language) ?>"
                                        <?= (isset($_GET['languages']) && in_array($language, $_GET['languages'])) ? 'checked' : '' ?>>
                                    <?= htmlspecialchars($language) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <div class="filter-levels">
                            <h4>Filter by Level</h4>
                            <?php
                            foreach ($allLevels as $level): ?>
                                <label>
                                    <input type="checkbox" name="levels[]" value="<?= htmlspecialchars($level) ?>"
                                        <?= (isset($_GET['levels']) && in_array($level, $_GET['levels'])) ? 'checked' : '' ?>>
                                    <?= htmlspecialchars($level) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="access-profile">
            <?php $user = $session->getUser(); ?>
            <button id="profile-button">
                <span class="material-symbols-outlined">account_circle</span>
                <?= htmlspecialchars($user->username) ?>
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
                                <?php if ($profile_type == 'Student'): ?>
                                    <h1 style="color: #32533D"><?= htmlspecialchars($profile->name) ?></h1>
                                <?php elseif($profile_type == 'Tutor'): ?>
                                    <h1 style="color: #03254E"><?= htmlspecialchars($profile->name) ?></h1>
                                <?php elseif($profile_type == 'Admin'): ?>
                                    <h1 style="color: #FFD670"><?= htmlspecialchars($profile->name) ?></h1>
                                <?php endif; ?>
                                <p class="username"> @<?= htmlspecialchars($profile_username) ?></p>
                                <?php if ($profile_type == 'Student'): ?>
                                    <span class="badge" style="background-color: #32533D">Student</span>
                                <?php elseif($profile_type == 'Tutor'): ?>
                                    <span class="badge" style="background-color: #03254E">Tutor</span>
                                <?php elseif($profile_type == 'Admin'): ?>
                                    <span class="badge" style="background-color: #FFD670">Admin</span>
                                <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="profile-info">
                            <?php if ($myuser->type === 'ADMIN' && strtoupper($profile_type) !== 'ADMIN'): ?>
                                <div class="admin-feats">
                                <form action="/actions/ban.php" method="post" class="delete-user-form">
                                    <input type="hidden" name="username" value="<?= htmlspecialchars($profile_username) ?>">
                                    <?php if (strtoupper($profile_type) === 'STUDENT'): ?>
                                        <button type="submit" class="delete-user-btn" style="background-color: #32533D">Ban User</button>
                                    <?php elseif (strtoupper($profile_type) === 'TUTOR'): ?>
                                        <button type="submit" class="delete-user-btn" style="background-color: #03254E">Ban User</button>
                                    <?php endif; ?>
                                </form>
                                <form action="/actions/promotion.php" method="post" class="promote-user-form">
                                    <input type="hidden" name="username" value="<?= htmlspecialchars($profile_username) ?>">
                                    <?php if (strtoupper($profile_type) === 'STUDENT'): ?>
                                        <button type="submit" class="promote-user-btn" style="background-color: #32533D">Promote to Admin</button>
                                    <?php elseif (strtoupper($profile_type) === 'TUTOR'): ?>
                                        <button type="submit" class="promote-user-btn" style="background-color: #03254E">Promote to Admin</button>
                                    <?php endif; ?>
                                </form>
                                </div>
                            <?php endif; ?>
                        <?php if ($session->getUser()->username === $profile_username): ?>
                            <?php if ($user->type == 'STUDENT'): ?>
                                <a href="/edit_profile.php" class="edit-profile-btn" style="background-color: #32533D">Edit Profile</a>
                            <?php elseif($user->type == 'TUTOR'): ?>
                                <a href="/edit_profile.php" class="edit-profile-btn" style="background-color: #03254E">Edit Profile</a>
                            <?php elseif($user->type == 'ADMIN'): ?>
                                <a href="/edit_profile.php" class="edit-profile-btn" style="background-color: #FFD670">Edit Profile</a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($myuser->username !== $profile_username): ?>
                            <?php if($myuser->type == 'STUDENT' && strtoupper($profile_type) == 'TUTOR'): ?>
                                <div class="ask-profile-btn">Ask For Tutoring</div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>    
                <div class="profile-header2">
                    <div class="profile-header-inner2">
                        <div class="other-flex-wrapper">
                            <div class="profile-details-container">
                                <div class="age">
                                    <?php if ($profile_type == 'Student'): ?>
                                        <div class="profile-details" style="background-color: #32533D">Age</div>
                                    <?php elseif($profile_type == 'Tutor'): ?>
                                        <div class="profile-details" style="background-color: #03254E">Age</div>
                                    <?php endif; ?>
                                    <div class="profile-description1">
                                        <?php if ($profile_type !== 'Admin'): ?>
                                            <?php if ($age): ?>
                                                <p><?= $age ?> years old</p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="member">
                                    <?php if ($profile_type == 'Student'): ?>
                                        <div class="profile-details" style="background-color: #32533D; width: 140px;">Member since</div>
                                    <?php elseif($profile_type == 'Tutor'): ?>
                                        <div class="profile-details" style="background-color: #03254E; width: 140px;">Member since</div>
                                    <?php endif; ?>
                                    <div class="profile-description1">
                                        <?php if ($profile_type !== 'Admin'): ?>
                                        <p><?= date('F Y', strtotime($user->created_at ?? 'now')) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    </div>
                                </div>
                                <?php if ($profile_type == 'Student'): ?>
                                    <div class="profile-details" style="background-color: #32533D">About</div>
                                <?php elseif($profile_type == 'Tutor'): ?>
                                    <div class="profile-details" style="background-color: #03254E">About</div>
                                <?php elseif($profile_type == 'Admin'): ?>
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