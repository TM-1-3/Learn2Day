<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/database/userclass.php';

$session = Session::getInstance();
$isLoggedIn = $session->isLoggedIn();
$loginError = isset($_GET['login_error']);

$db = Database::getInstance();

$students = [];
$stmt = $db->prepare('SELECT * FROM STUDENT LIMIT 10');
$stmt->execute();
$students = $stmt->fetchAll();

$tutors = [];
$stmt = $db->prepare('SELECT * FROM TUTOR LIMIT 10');
$stmt->execute();
$tutors = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Learn2Day</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index_style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body>
    <header class="header">
        <div class="site-name">
            <a href="/" class="main-page">Learn2Day</a>
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
            <?php if ($isLoggedIn): ?>
                <?php $user = $session->getUser(); ?>
                <button id="profile-button">
                    <span class="material-symbols-outlined">account_circle</span>
                    <?= htmlspecialchars($user->username) ?>
                </button>
                <div id="profile-inner" class="profile">
                    <a href="/profile.php" class="profile-link">My Profile</a>
                    <form action="/actions/logout.php" method="post">
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
                        <a href="#" class="reset-link">Reset your password</a>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <h1>Welcome<?= $isLoggedIn ? ' back, ' . htmlspecialchars($user->username) : '' ?>!</h1>
        
        <section class="tutors-section">
            <h2>Available Tutors</h2>
            <div class="cards-grid">
                <?php foreach ($tutors as $tutor): ?>
                    <div class="card" id="tutor<?= htmlspecialchars($tutor['ID_TUTOR']) ?>">
                        <div class="container">
                            <div class="details">
                                <div class="content-wrapper">
                                    <img class="img" src="/uploads/profiles/<?= htmlspecialchars($tutor['PROFILE_IMAGE']) ?>" 
                                         alt="<?= htmlspecialchars($tutor['NAME']) ?>"
                                         onerror="this.src='/images/default-profile.jpg'">
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

        <?php if ($isLoggedIn && $user->type === 'TUTOR'): ?>
            <section class="students-section">
                <h2>Students</h2>
                <div class="cards-grid">
                    <?php foreach ($students as $student): ?>
                        <div class="card" id="student<?= htmlspecialchars($student['ID_STUDENT']) ?>">
                            <div class="container">
                                <div class="details">
                                    <div class="content-wrapper">
                                        <img class="img" src="/uploads/profiles/<?= htmlspecialchars($student['PROFILE_IMAGE']) ?>" 
                                             alt="<?= htmlspecialchars($student['NAME']) ?>"
                                             onerror="this.src='/images/default-profile.jpg'">
                                        <div class="text-content">
                                            <h2 class="title"><?= htmlspecialchars($student['NAME']) ?></h2>
                                            <div class="subtitle-container">
                                                <div class="subtitles"><?= htmlspecialchars($student['SCHOOL_INSTITUTION']) ?></div>
                                            </div>
                                            <?php if (!empty($student['DESCRIPTION'])): ?>
                                                <p class="description"><?= htmlspecialchars($student['DESCRIPTION']) ?></p>
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

    <script src="index_script.js"></script>

</body>
</html>