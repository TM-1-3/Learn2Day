<?php
    include_once(__DIR__ . '/includes/session.php');
    include_once(__DIR__ . '/includes/database.php');
    include_once(__DIR__ . '/database/userclass.php');
    $error = '';
    if (isset($_GET['error'])) {
        $error = htmlspecialchars($_GET['error']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/index_style.css">
    <link rel="stylesheet" href="styles/register_style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
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
                        <a href="#" class="reset-link">Reset your password</a>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </header>
<body>
    <div class="container" id="container">
        <?php if ($error): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>

        <div class="teacher-container Tsign-up-container">
            <form action="../actions/register.php" method="POST">
                <h1>Create Tutor Account</h1>
                <input type="hidden" name="type" value="TUTOR">
                <div class="user"><input type="text" name="username" placeholder="Username" required /></div>
                <div class="email"><input type="email" name="email" placeholder="Email" required /></div>
                <div class="phone-number"><input type="tel" name="phone" placeholder="Phone Number" /></div>
                <div class="password"><input type="password" name="password" placeholder="Password" required /></div>
                <div class="confirm-password"><input type="password" name="confirm_password" placeholder="Confirm Password" required /></div>
                <button type="submit" class="T_signUp">Sign Up</button>
            </form>
        </div>

        <div class="student-container Ssign-up-container">
            <form action="../actions/register.php" method="POST">
                <h1>Create Student Account</h1>
                <input type="hidden" name="type" value="STUDENT">
                <div class="user"><input type="text" name="username" placeholder="Username" required /></div>
                <div class="email"><input type="email" name="email" placeholder="Email" required /></div>
                <div class="phone-number"><input type="tel" name="phone" placeholder="Phone Number" /></div>
                <div class="password"><input type="password" name="password" placeholder="Password" required /></div>
                <div class="confirm-password"><input type="password" name="confirm_password" placeholder="Confirm Password" required /></div>
                <button type="submit" class="S_signUp">Sign Up</button>
            </form>
        </div>

        <div class="overlay-container" id="overlayCon">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Hello, Student!</h1>
                    <p>Student Description</p>
                    <p></p>
                    <p></p>
                    <p></p>
                    <p>To register as a tutor, click below.</p>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Tutor!</h1>
                    <p>Tutor Description</p>
                    <p></p>
                    <p></p>
                    <p></p>
                    <p>To register as a student, click below.</p>
                </div>
            </div>
            <button class="overlay-button" id="overlayBtn">Register as Student</button>
        </div>
    </div>
    <script src="scripts/register_script.js"></script>
    <script src="scripts/index_script.js"></script>
</body>
</html>