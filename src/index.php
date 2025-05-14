<?php
require_once('fetchinfo.php');
require_once('includes/session.php');
$db = connection();
$students = getStudents($db);
$tutors = getTutors($db);

$session = Session::getInstance();
$isLoggedIn = $session->isLoggedIn();
$loginError = isset($_GET['login_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Learn2Day</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index_style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
</head>
<body>
    <header class="header">
        <div class="site-name">
            <button class="main-page"> Learn2Day </button>
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search..." />
            <button class="search-button">
                <span class="material-symbols-outlined"> search </span>
            </button>
            <button class="filter-button">
                <span class="material-symbols-outlined"> filter_alt </span>
            </button>
        </div>
        <div class="access-profile">
            <?php if ($isLoggedIn): ?>
                <button id="profile-button">
                    <span class="material-symbols-outlined"> account_circle </span>
                    <?= htmlspecialchars($_SESSION['user']['username']) ?>
                </button>
                <div id="profile-inner" class="profile">
                    <form action="actions/logout.php" method="post">
                        <button type="submit" class="logout-btn">Log Out</button>
                    </form>
                </div>
            <?php else: ?>
                <button id="profile-button">
                    <span class="material-symbols-outlined"> account_circle </span>
                </button>
                <div id="profile-inner" class="profile">
                    <form action="actions/login.php" method="post" class="login-popup">
                        <input type="text" name="username" placeholder="Username" required />
                        <input type="password" name="password" placeholder="Password" required />
                        <button type="submit" class="login-btn">Log In</button>
                        <div class="divider">or</div>
                        <a href='register_page.php'><button type="button" class="signup-btn">Sign Up</button></a>
                        <hr size="18">
                        <?php if ($loginError): ?>
                            <div class="error-message">Invalid username or password</div>
                        <?php endif; ?>
                        <a href="#" class="reset-link">Reset your password</a>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <h1>Welcome<?= $isLoggedIn ? ' back, ' . htmlspecialchars($_SESSION['user']['username']) : '' ?>!</h1>
    <div class="list">
        <div id="root"></div>
        <script type="text/babel">
            const Card = ({ id, title, description, imgSrc, subtitles}) => {
                return (
                    <div className="card" id={id}>
                        <div className="container">
                            <div className="details">
                                <div className="content-wrapper">
                                    {imgSrc && <img className="img" src={imgSrc} alt={title || "Card image"} />}
                                    <div className="text-content">
                                        {title && <h2 className="title">{title}</h2>}
                                        <div className="subtitle-container">
                                            {subtitles?.map((subtitle, idx) => (
                                                <div key={idx} className="subtitles">
                                                    {subtitle}
                                                </div>
                                            ))}
                                        </div>
                                        {description && <p className="description">{description}</p>}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                );
            };
            const studentsData = [
                <?php foreach($students as $student): ?>
                {
                    id: "student<?= $student['ID_STUDENT'] ?>",
                    title: "<?= addslashes($student['NAME']) ?>",
                    description: "<?= addslashes($student['DESCRIPTION']) ?>",
                    imgSrc: "<?= addslashes($student['PROFILE_IMAGE']) ?>",
                    subtitles: ["<?= addslashes($student['SCHOOL_INSTITUTION']) ?>"]
                },
                <?php endforeach; ?>
            ];

            const tutorsData = [
                <?php foreach($tutors as $tutor): ?>
                {
                    id: "tutor<?= $tutor['ID_TUTOR'] ?>",
                    title: "<?= addslashes($tutor['NAME']) ?>",
                    description: "<?= addslashes($tutor['DESCRIPTION']) ?>",
                    imgSrc: "<?= addslashes($tutor['PROFILE_IMAGE']) ?>",
                    subtitles: ["Tutor"]
                },
                <?php endforeach; ?>
            ];

            const root = ReactDOM.createRoot(document.getElementById('root'));
            root.render(
                <div className="cards-grid">
                    {tutorsData.map((tutor, index) => (
                        <Card
                            key={tutor.id}
                            id={tutor.id}
                            title={tutor.title}
                            description={tutor.description}
                            imgSrc={tutor.imgSrc}
                            subtitles={tutor.subtitles}
                        />
                    ))}
                </div>
            );
        </script>
    </div>
    <script src="index_script.js"></script>
</body>
</html>