<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/database/tutorclass.php';
require_once __DIR__ . '/database/userclass.php';
require_once __DIR__ . '/database/qualificationclass.php';

$db = Database::getInstance();

$session = Session::getInstance();
if(!$session->isLoggedIn()) {
    header('Location: /');
    exit;
}

$user = $session->getUser();
if($user->type !== 'ADMIN'){
    header('Location: /homepage.php');
    exit;
}

$totalUsers = User::countAllUsers();
$totalTutors = Tutor::countAllTutors();
$totalStudents = Student::countAllStudents();

$searchQuery = '';
$searchResults = [];
$showAll = true;
$selectedSubjects = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || isset($_GET['subjects']))) {
    $searchQuery = trim($_GET['search'] ?? '');
    $selectedSubjects = $_GET['subjects'] ?? [];
    
    $showAll = false;

    $query = "
        SELECT T.ID_TUTOR as id, T.NAME, 'tutor' as type, T.PROFILE_IMAGE, T.DESCRIPTION, U.USERNAME, U.IS_ACTIVE
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
        SELECT S.ID_STUDENT as id, S.NAME, 'student' as type, S.PROFILE_IMAGE, S.DESCRIPTION, U.USERNAME, U.IS_ACTIVE
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

    $searchResults = array_merge($tutorResults, $studentResults);
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
            <a href="/homepage.php" class="main-page">Learn2Day</a>
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

        <?php if(!$showAll && !empty($searchResults)): ?>
            <div class="search-results">
                <h3>Search Results</h3>
                <div class="results-grid">
                    <?php foreach($searchResults as $result): ?>
                        <div class="user-card">
                            <div class="user-image">
                                <?php if(!empty($result['PROFILE_IMAGE'])): ?>
                                    <img src="<?= htmlspecialchars($result['PROFILE_IMAGE']) ?>" alt="Profile image">
                                <?php else: ?>
                                    <span class="material-symbols-outlined default-avatar">account_circle</span>
                                <?php endif; ?>
                            </div>
                            <div class="user-info">
                                <h4><?= htmlspecialchars($result['NAME']) ?></h4>
                                <p class="username">@<?= htmlspecialchars($result['USERNAME']) ?></p>
                                <p class="type"><?= ucfirst($result['type']) ?></p>
                                <p class="status <?= $result['IS_ACTIVE'] ? 'active' : 'inactive' ?>">
                                    <?= $result['IS_ACTIVE'] ? 'Active' : 'Inactive' ?>
                                </p>
                                <a href="/profile.php?user=<?= htmlspecialchars($result['USERNAME']) ?>" class="view-btn">View Profile</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="/scripts/admin.js"></script>
    <script src="/scripts/homepage_script.js"></script>
</body>
</html>

