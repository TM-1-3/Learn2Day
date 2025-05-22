<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/database/tutorclass.php';
require_once __DIR__ . '/database/userclass.php';

$session = Session::getInstance();
$isLoggedIn = $session->isLoggedIn();
$loginError = isset($_GET['login_error']);

$db = Database::getInstance();

if(!$isLoggedIn){
    header('Location: /');
    exit();
}

$searchQuery = '';
$searchResults = [];
$showAll = true;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
    
    if (!empty($searchQuery)) {
        $showAll = false;

        $stmt = $db->prepare("
            SELECT ID_STUDENT as id, NAME, 'student' as type, PROFILE_IMAGE, DESCRIPTION 
            FROM STUDENT 
            WHERE NAME LIKE ? OR ID_STUDENT LIKE ?
            UNION
            SELECT ID_TUTOR as id, NAME, 'tutor' as type, PROFILE_IMAGE, DESCRIPTION 
            FROM TUTOR 
            WHERE NAME LIKE ? OR ID_TUTOR LIKE ?
            LIMIT 10
        ");
        
        $searchParam = "%$searchQuery%";
        $stmt->execute([$searchParam, $searchParam, $searchParam, $searchParam]);
        $searchResults = $stmt->fetchAll();
    }
}

if ($showAll) {
    $students = [];
    $stmt = $db->prepare('SELECT * FROM STUDENT LIMIT 10');
    $stmt->execute();
    $students = $stmt->fetchAll();

    $tutors = [];
    $stmt = $db->prepare('SELECT * FROM TUTOR LIMIT 10');
    $stmt->execute();
    $tutors = $stmt->fetchAll();
}
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
            <a href="/homepage.php" class="main-page">Learn2Day</a>
        </div>
        <div class="search-bar">
            <form method="GET" action="/homepage.php">
                <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($searchQuery) ?>" />
                <button type="submit" class="search-button">
                    <span class="material-symbols-outlined">search</span>
                </button>
            </form>
            <button class="filter-button">
                <span class="material-symbols-outlined">filter_alt</span>
            </button>
        </div>
        <div class="access-profile">
            <?php if ($isLoggedIn): ?>
                <?php $user = $session->getUser(); ?>
                <button id="profile-button">
                    <span class="material-symbols-outlined">account_circle</span>
                    <?= htmlspecialchars($user->username) ?>
                </button>
                <div id="profile-inner" class="profile">
                    <form action="actions/logout.php" method="post" class="logout-popup">
                        <a href='/profile.php?id=<?= $user->id ?>' class="viewprofile-btn">View Profile</a>
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
                        <?php if ($loginError): ?>
                            <div class="error-message">Invalid username or password</div>
                        <?php endif; ?>
                        <hr size="18">
                        <a href="#" class="reset-link">Reset your password</a>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <h1>Welcome<?= $isLoggedIn ? ' back, ' . htmlspecialchars($user->username) : '' ?>!</h1>
        
        <?php if (!empty($searchQuery)): ?>
            <section class="tutors-section">
                <h2>Search Results for "<?= htmlspecialchars($searchQuery) ?>"</h2>
                <div class="cards-grid">
                    <?php if (!empty($searchResults)): ?>
                        <?php foreach ($searchResults as $user): ?>
                            <div class="card" id="<?= $user['type'] ?><?= htmlspecialchars($user['id']) ?>" 
                                onclick="window.location.href='/profile.php?id=<?= urlencode($user['id']) ?>'"
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
                        <p>No users found matching your search.</p>
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