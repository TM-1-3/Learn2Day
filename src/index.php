<?php
require_once('fetchinfo.php');
$db = connection();
$students = getStudents($db);
$tutors = getTutors($db);
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
            <button id="profile-button">
                <span class="material-symbols-outlined"> account_circle </span>
            </button>
            <div id="profile-inner" class="profile">
                <div class="login-popup">
                    <input type="text" placeholder="E-mail" />
                    <input type="password" placeholder="Password" />
                    <button class="login-btn">Log In</button>
                    <div class="divider">or</div>
                    <a href='register_page.html'><button class="signup-btn">Sign Up</button></a>
                    <hr size="18">
                    <a href="#" class="reset-link">Reset your password</a>
                </div>
            </div>
        </div>
    </header>
    <h1>Welcome!</h1>
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