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
    <link href="https://fonts.googleapis.com/css2?family=Poetsen+One&display=swap" rel="stylesheet">
</head>
<body>
    <header class="access-profile">
        <div id="profile-inner" class="profile">
            <form action="/actions/login.php" method="post" class="login">
                <input type="text" name="username" class="user" placeholder="Username" required />
                <input type="password" name="password" class="password" placeholder="Password" required />
                <button type="submit" class="login-btn">Log In</button>
                <a href='/register_page.php' class="signup-btn">Sign Up</a>
                <?php if ($loginError): ?>
                    <div class="error-message">Invalid username or password.</div>
                <?php endif; ?>
            </form>
        </div>
    </header>
    <main>
        <div class="background-image"></div>
        <div class="name">
            <span style="color: #03254E;">Learn</span><span style="color: black;">2</span><span style="color: #32533D;">Day</span>
        </div>
        <div class="modes">
            <div class="tutor">1. Descrição kinda do tutor</div>
            <div class="student">2. Descrição kinda do student</div>
        </div>
    </main>

</body>
</html>