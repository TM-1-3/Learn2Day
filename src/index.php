<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/database/tutorclass.php';
require_once __DIR__ . '/database/userclass.php';

$session = Session::getInstance();
$isLoggedIn = $session->isLoggedIn();
$loginError = isset($_SESSION['login_error']);
unset($_SESSION['login_error']);

if ($isLoggedIn) {
    $user = $session->getUser();
    if ($user->type == 'ADMIN') {
        header('Location: /admindashboard.php');
    } else {
        header('Location: /homepage.php');
    }
}

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
    <link rel="stylesheet" href="styles/index.css">
</head>

<body>
    <header class="access-profile">
        <div class="profile">
            <div class="name">
                <a href="#first-section" style="text-decoration:none;"><span style="color: #03254E;">Learn</span><span style="color: black;">2</span><span style="color: #32533D;">Day</span></a>
            </div>
            <div class="sections">
                <a href="#teacher-mode" style="color: #03254E; text-decoration: none; font-size: 18px;">Teacher Mode</a>
                <a href="#student-mode" style="color: #32533D; text-decoration: none;font-size: 18px;">Student Mode</a>
                <button type="submit" id="log-btn" class="log-btn" style="background-color: #535353;">Log In</button>
                <div id="popup-overlay" class="<?= $loginError ? 'open' : '' ?>">
                    <div id="profile-inner" class="<?= $loginError ? 'open' : '' ?>">
                        <form action="/actions/login.php" method="post" class="login-popup">
                            <span style="color: black; font-size: 30px; font-weight: bold">Welcome back!</span>
                            <br>
                            <input type="text" name="username" placeholder="Username" required />
                            <input type="password" name="password" placeholder="Password" required />
                            <?php if ($loginError): ?>
                                <div class="error-message">Invalid username or password</div>
                            <?php endif; ?>
                            <button type="submit" id="login-btn" class="login-btn" style="background-color: #535353;">Log In</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main>
        <div id="first-section" class="first-section">
            <div class="background-image"></div>
            <h1 class="slogan">Because knowledge<br>can't wait!</h1>
            <div class="sign-up">
                <span style="color: black; font-size: 16px; font-weight: 600;">You don't have an account?</span>
                <span style="color: black; font-size: 16px; font-weight: 600;">Explore the powerful tools for personalized learning.</span>
                <div class="modes">
                    <a href="#teacher-mode" class="teach-btn" style="background-color: #03254E;">Teacher Mode</a>
                    <a href="#student-mode" class="stud-btn" style="background-color: #32533D;">Student Mode</a>
                </div>
            </div>
        </div>
        <div id="teacher-mode" class="teacher-mode">
            <div class="image-stack">
                <div class="green-blob"></div>
                <div class="teacher-img"></div>
            </div>
            <div class="teacher-text">
                <h1>Teacher Mode</h1>
                <p>DESCRIÇÃO</p>
                <a href='/register_page.php' class="Tsignup-btn" style="background-color: #03254E;">Sign Up</a>
            </div>
        </div>
        <div id="student-mode" class="student-mode">
            <div class="student-text">
                <h1>Student Mode</h1>
                <p>DESCRIÇÃO</p>
                <a href='/register_page.php' class="Ssignup-btn" style="background-color: #32533D;">Sign Up</a>
            </div>
            <div class="image-stack">
                <div class="blue-blob"></div>
                <div class="student-img"></div>
            </div>
        </div>
        <footer class="rights">
            <p>© 2025 Learn2Day. All rights reserved.</p>
        </footer>
    </main>
    <script src="scripts/index_script.js"></script>
</body>

</html>