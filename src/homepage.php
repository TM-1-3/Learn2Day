<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/database/tutorclass.php';
require_once __DIR__ . '/database/userclass.php';
require_once __DIR__ . '/database/qualificationclass.php';

$session = Session::getInstance();
$isLoggedIn = $session->isLoggedIn();
$loginError = isset($_GET['login_error']);

$db = Database::getInstance();

if (!$isLoggedIn) {
    header('Location: /');
    exit();
}

$user = $session->getUser();

if ($user->type == 'ADMIN') {
    header('Location: /admindashboard.php');
    exit();
}

$searchQuery = '';
$searchResults = [];
$showAll = true;
$selectedSubjects = [];
$selectedLanguages = [];
$selectedLevels = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || isset($_GET['subjects']) || isset($_GET['languages']) || isset($_GET['levels']))) {
    $searchQuery = trim($_GET['search'] ?? '');
    $selectedSubjects = $_GET['subjects'] ?? [];
    $selectedLanguages = $_GET['languages'] ?? [];
    $selectedLevels = $_GET['levels'] ?? [];

    $showAll = false;

    // Tutors query
    $query = "
        SELECT T.ID_TUTOR as id, T.NAME, 'tutor' as type, T.PROFILE_IMAGE, T.DESCRIPTION, U.USERNAME
        FROM TUTOR T
        JOIN USERS U ON T.ID_TUTOR = U.USERNAME
        WHERE 1=1
    ";
    $params = [];
    if (!empty($searchQuery)) {
        $query .= " AND (T.NAME LIKE ? OR U.USERNAME LIKE ?)";
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

    // Students query
    $query_students = "
        SELECT S.ID_STUDENT as id, S.NAME, 'student' as type, S.PROFILE_IMAGE, S.DESCRIPTION, U.USERNAME
        FROM STUDENT S
        JOIN USERS U ON S.ID_STUDENT = U.USERNAME
        WHERE 1=1
    ";
    $params_students = [];
    if (!empty($searchQuery)) {
        $query_students .= " AND (S.NAME LIKE ? OR U.USERNAME LIKE ?)";
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

if ($showAll) {
    $stmt = $db->prepare("
        SELECT T.ID_TUTOR, T.NAME, T.PROFILE_IMAGE, T.DESCRIPTION
        FROM TUTOR T
    ");
    $stmt->execute();
    $tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$allSubjects = Qualifications::getAllSubjects();
$allLanguages = Qualifications::getAllLanguages();
$allLevels = Qualifications::getAllTutorLevels();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Learn2Day</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/homepage.css">
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
        <h1>Welcome<?= $isLoggedIn ? ' back, ' . htmlspecialchars($user->username) : '' ?>!</h1>

        <?php if (!empty($selectedSubjects) || !empty($selectedLanguages) || !empty($selectedLevels)): ?>
            <div class="active-filters">
                <strong>Active filters:</strong>
                <?php foreach ($selectedSubjects as $subject): ?>
                    <span class="filter-tag">
                        <?= htmlspecialchars($subject) ?>
                        <a href="?" class="remove-filter">×</a>
                    </span>
                <?php endforeach; ?>
                <?php foreach ($selectedLanguages as $lang): ?>
                    <span class="filter-tag">
                        <?= htmlspecialchars($lang) ?>
                        <a href="?" class="remove-filter">×</a>
                    </span>
                <?php endforeach; ?>
                <?php foreach ($selectedLevels as $level): ?>
                    <span class="filter-tag">
                        <?= htmlspecialchars($level) ?>
                        <a href="?" class="remove-filter">×</a>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($searchQuery) || !empty($selectedSubjects) || !empty($selectedLanguages) || !empty($selectedLevels)): ?>
            <section class="tutors-section">
                <h2>Search Results <?= !empty($searchQuery) ? 'for "' . htmlspecialchars($searchQuery) . '"' : '' ?></h2>
                <div class="cards-grid">
                    <?php if (!empty($searchResults)): ?>
                        <?php foreach ($searchResults as $user): ?>
                            <div class="card" id="<?= $user['type'] ?><?= htmlspecialchars($user['id']) ?>"
                                onclick="window.location.href='/profile.php?id=<?= urlencode($user['USERNAME']) ?>'"
                                style="cursor: pointer;">
                                <div class="container">
                                    <div class="details">
                                        <div class="content-wrapper">
                                            <img class="img" src="/uploads/profiles/<?= htmlspecialchars($user['PROFILE_IMAGE']) ?>"
                                                alt="<?= htmlspecialchars($user['NAME']) ?>"
                                                onerror="this.src='/uploads/profiles/default.png'">
                                            <div class="text-content">
                                                <h2 class="title"><?= htmlspecialchars($user['NAME']) ?></h2>
                                                <div class="subtitle-container">
                                                    <div class="subtitles"><?= ucfirst($user['type']) ?></div>
                                                </div>
                                                <?php if (!empty($user['DESCRIPTION'])): ?>
                                                    <p class="description"><?= htmlspecialchars($user['DESCRIPTION']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No tutors or students found matching your criteria.</p>
                    <?php endif; ?>
                </div>
            </section>
        <?php else: ?>
            <section class="tutors-section">
                <h2>Available Tutors</h2>
                <div class="cards-grid">
                    <?php foreach ($tutors as $tutor): ?>
                        <div class="card" id="tutor<?= htmlspecialchars($tutor['ID_TUTOR']) ?>"
                            onclick="window.location.href='/profile.php?id=<?= urlencode($tutor['ID_TUTOR']) ?>'"
                            style="cursor: pointer;">
                            <div class="container">
                                <div class="details">
                                    <div class="content-wrapper">
                                        <img class="img" src="/uploads/profiles/<?= htmlspecialchars($tutor['PROFILE_IMAGE']) ?>"
                                            alt="<?= htmlspecialchars($tutor['NAME']) ?>"
                                            onerror="this.src='/uploads/profiles/default.png'">
                                        <div class="text-content">
                                            <h2 class="title"><?= htmlspecialchars($tutor['NAME']) ?></h2>
                                            <div class="subtitle-container">
                                                <div class="subtitles">Tutor</div>
                                            </div>
                                            <?php if (!empty($tutor['DESCRIPTION'])): ?>
                                                <p class="description"><?= htmlspecialchars($tutor['DESCRIPTION']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <script src="scripts/homepage_script.js"></script>
</body>

</html>