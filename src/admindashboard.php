<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/database/tutorclass.php';
require_once __DIR__ . '/database/userclass.php';
require_once __DIR__ . '/database/qualificationclass.php';
require_once __DIR__ . '/database/adminclass.php';

$db = Database::getInstance();

$session = Session::getInstance();
if (!$session->isLoggedIn()) {
    header('Location: /');
    exit;
}

$user = $session->getUser();
if ($user->type !== 'ADMIN') {
    header('Location: /homepage.php');
    exit;
}

$totalUsers = User::countAllUsers();
$totalTutors = Tutor::countAllTutors();
$totalStudents = Student::countAllStudents();
$totalAdmins = Admin::countAllAdmins();

$searchQuery = '';
$searchResults = [];
$showAll = true;
$selectedSubjects = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || isset($_GET['subjects']))) {
    $searchQuery = trim($_GET['search'] ?? '');
    $selectedSubjects = $_GET['subjects'] ?? [];

    $showAll = false;


    $query = "
    SELECT T.ID_TUTOR as id, T.NAME, 'TUTOR' as type, T.PROFILE_IMAGE, T.DESCRIPTION, U.USERNAME
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

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $tutorResults = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $query_students = "
        SELECT S.ID_STUDENT as id, S.NAME, 'STUDENT' as type, S.PROFILE_IMAGE, S.DESCRIPTION, U.USERNAME
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

    $stmt_students = $db->prepare($query_students);
    $stmt_students->execute($params_students);
    $studentResults = $stmt_students->fetchAll(PDO::FETCH_ASSOC);

    $query_admins = "
        SELECT A.ID_ADMIN as id, A.NAME, 'ADMIN' as type, A.PROFILE_IMAGE, A.DESCRIPTION, U.USERNAME
        FROM ADMIN A
        JOIN USERS U ON A.ID_ADMIN = U.USERNAME
        WHERE 1=1
    ";

    $params_admins = [];

    if (!empty($searchQuery)) {
        $query_admins .= " AND (A.NAME LIKE ? OR U.USERNAME LIKE ?)";
        $searchParam = "%$searchQuery%";
        $params_admins[] = $searchParam;
        $params_admins[] = $searchParam;
    }

    $stmt_admins = $db->prepare($query_admins);
    $stmt_admins->execute($params_admins);
    $adminResults = $stmt_admins->fetchAll(PDO::FETCH_ASSOC);

    $searchResults = array_merge($tutorResults, $studentResults, $adminResults);
}

if ($showAll) {
    $stmt = $db->prepare("
        SELECT T.ID_TUTOR, T.NAME, T.PROFILE_IMAGE, T.DESCRIPTION, U.USERNAME
        FROM TUTOR T
        JOIN USERS U ON T.ID_TUTOR = U.USERNAME
    ");
    $stmt->execute();
    $tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$allSubjects = Qualifications::getAllSubjects();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/styles/admin.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>

<body>
    <header class="header">
        <div class="site-name">
            <a href="/admindashboard.php" class="main-page">Learn2Day</a>
        </div>
        <div class="search-bar">
            <form method="GET" action="/admindashboard.php">
                <input type="text" name="search" placeholder="Search users..." value="<?= htmlspecialchars($searchQuery) ?>" />
                <button type="submit" class="search-button">
                    <span class="material-symbols-outlined">search</span>
                </button>
                <div class="filter-dropdown">
                    <button type="button" class="filter-button">
                        <span class="material-symbols-outlined">filter_alt</span>
                    </button>
                    <div class="filter-options">
                        <h4>Filter by Subject</h4>
                        <?php foreach ($allSubjects as $subject): ?>
                            <label>
                                <input type="checkbox" name="subjects[]" value="<?= htmlspecialchars($subject) ?>"
                                    <?= (isset($_GET['subjects']) && in_array($subject, $_GET['subjects'])) ? 'checked' : '' ?>>
                                <?= htmlspecialchars($subject) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </form>
        </div>
        <div class="access-profile">
            <button id="profile-button">
                <span class="material-symbols-outlined">account_circle</span>
                <?= htmlspecialchars($user->username) ?>
            </button>
            <div id="profile-inner" class="profile">
                <form action="actions/logout.php" method="post" class="logout-popup">
                    <a href='/profile.php?id=<?= $user->username ?>' class="viewprofile-btn">View Profile</a>
                    <hr size="18">
                    <button type="submit" class="logout-btn">Log Out</button>
                </form>
            </div>
        </div>
    </header>

    <main>
        <h1>Admin Dashboard</h1>
        <h2>Welcome, <?= htmlspecialchars($user->username) ?>!</h2>

        <div class="admin-info">
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <p><?= $totalUsers ?></p>
                </div>
                <div class="stat-card">
                    <h3>Tutors</h3>
                    <p><?= $totalTutors ?></p>
                </div>
                <div class="stat-card">
                    <h3>Students</h3>
                    <p><?= $totalStudents ?></p>
                </div>
            </div>

            <div class="chart-container">
                <div class="piechart">
                    <h3>User Distribution</h3>
                    <canvas id="userDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <?php if (!empty($selectedSubjects)): ?>
            <div class="active-filters">
                <strong>Active filters:</strong>
                <?php foreach ($selectedSubjects as $subject): ?>
                    <span class="filter-tag">
                        <?= htmlspecialchars($subject) ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['subjects' => array_diff($_GET['subjects'], [$subject])])) ?>"
                            class="remove-filter">×</a>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!$showAll): ?>
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
                        <p>No users found matching your criteria.</p>
                    <?php endif; ?>
                </div>
            </section>
        <?php else: ?>
            <section class="tutors-section">
                <h2>Available Tutors</h2>
                <div class="cards-grid">
                    <?php foreach ($tutors as $tutor): ?>
                        <div class="card" id="tutor<?= htmlspecialchars($tutor['ID_TUTOR']) ?>"
                            onclick="window.location.href='/profile.php?id=<?= urlencode($tutor['USERNAME']) ?>'"
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
    <script>
        window.totalTutors = <?= $totalTutors ?>;
        window.totalStudents = <?= $totalStudents ?>;
        window.totalAdmins = <?= $totalAdmins ?>;
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="/scripts/admin.js"></script>
    <script src="/scripts/homepage_script.js"></script>
</body>

</html>