<?php
require_once __DIR__ . '/database/requestclass.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/database/userclass.php';
require_once __DIR__ . '/database/tutorclass.php';
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/includes/database.php';

$session = Session::getInstance();
if (!$session->isLoggedIn()) {
    header('Location: /register_page.php');
    exit();
}
$user = $session->getUser();

// Fetch requests for the logged-in user
$requests = [];
if ($user->type === 'STUDENT') {
    $requests = Request::getByStudent($user->username);
} elseif ($user->type === 'TUTOR') {
    $requests = Request::getByTutor($user->username);
}

// Fetch tutors and students using STUDENT_TUTOR table
$db = Database::getInstance();
$tutors = [];
$students = [];
if ($user->type === 'STUDENT') {
    $stmt = $db->prepare('
        SELECT T.ID_TUTOR, T.NAME, T.PROFILE_IMAGE, T.DESCRIPTION
        FROM STUDENT_TUTOR ST
        JOIN TUTOR T ON ST.TUTOR = T.ID_TUTOR
        WHERE ST.STUDENT = ?
    ');
    $stmt->execute([$user->username]);
    $tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($user->type === 'TUTOR') {
    $stmt = $db->prepare('
        SELECT S.ID_STUDENT, S.NAME, S.PROFILE_IMAGE, S.DESCRIPTION
        FROM STUDENT_TUTOR ST
        JOIN STUDENT S ON ST.STUDENT = S.ID_STUDENT
        WHERE ST.TUTOR = ?
    ');
    $stmt->execute([$user->username]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$profile_image = 'default.png';
if ($user->type === 'STUDENT') {
    $profile = Student::getByUsername($user->username);
    if ($profile && !empty($profile->profile_image)) {
        $profile_image = $profile->profile_image;
    }
} elseif ($user->type === 'TUTOR') {
    $profile = Tutor::getByUsername($user->username);
    if ($profile && !empty($profile->profile_image)) {
        $profile_image = $profile->profile_image;
    }
} elseif ($user->type === 'ADMIN') {
    $profile = Admin::getByUsername($user->username);
    if ($profile && !empty($profile->profile_image)) {
        $profile_image = $profile->profile_image;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Requests</title>
    <link rel="stylesheet" href="/styles/homepage.css">
    <link rel="stylesheet" href="/styles/viewrequests.css">
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
            </div>
        </form>
        <div class="notifications">
            <?php $user = $session->getUser(); ?>
            <button id="notification-button">
                <span class="material-symbols-outlined">notifications</span>
            </button>
            <div id="notification-inner" class="notification-popup">
                <?php if ($user->type === 'STUDENT'): ?>
                    <a href="/viewrequests.php?id=<?=htmlspecialchars($session->getUserUsername()) ?>" class="viewprofile-btn">View Requests</a>
                <?php elseif ($user->type === 'TUTOR'): ?>
                    <a href="/viewrequests.php?id=<?=htmlspecialchars($session->getUserUsername()) ?>" class="viewprofile-btn">View Requests</a>
                <?php endif; ?>
                <hr size="5">
                <a href="/viewmessages.php?id=<?=htmlspecialchars($session->getUserUsername()) ?>" class="viewprofile-btn">Messages</a>
        </div>
        <div class="access-profile">
            <?php $user = $session->getUser(); ?>
            <button id="profile-button">
                @<?= htmlspecialchars($user->username) ?>
                <span class="material-symbols-outlined" style="display: flex; align-items: center;">
                    <img class="profile-header-img" src="/uploads/profiles/<?= htmlspecialchars($profile_image) ?>"
                         alt="Profile Image">
                </span>
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
        <div class="tables">
            <div class="container1">
                <?php if ($user->type === 'TUTOR'): ?>
                    <h1 style="color: #03254E">My Requests</h1>
                <?php elseif ($user->type === 'STUDENT'): ?>
                    <h1 style="color: #32533D">My Requests</h1>
                <?php endif; ?>
                <?php if (empty($requests)): ?>
                    <p style:«="color: black;">No requests found.</p>
                <?php else: ?>
                <table class="requests-table">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>To</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <?php if ($user->type === 'TUTOR'): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($requests as $req): ?>
                        <tr>
                            <td><?= htmlspecialchars($req->usernamestudent) ?></td>
                            <td><?= htmlspecialchars($req->usernametutor) ?></td>
                            <td><?= htmlspecialchars($req->message) ?></td>
                            <td><?= $req->accepted ? 'Accepted' : 'Pending' ?></td>
                            <td><?= htmlspecialchars($req->date_sent) ?></td>
                            <?php if ($user->type === 'TUTOR'): ?>
                                <td class="buttons">
                                    <?php if (!$req->accepted): ?>
                                        <form action="/actions/accept.php" method="post" style="display:inline;">
                                            <input type="hidden" name="request_id" value="<?= htmlspecialchars($req->id) ?>">
                                            <button type="submit" name="action" value="accept" class="accept-btn">Accept</button>
                                        </form>
                                        <form action ="/actions/deny.php" method="post" style="display:inline;">
                                            <input type="hidden" name="request_id" value="<?= htmlspecialchars($req->id) ?>">
                                            <button type="submit" name="action" value="deny" class="deny-btn">Deny</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="accepted">Accepted</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
            <div class="container2">
                <?php if ($user->type === 'TUTOR'): ?>
                    <h1 style="color: #03254E">My Students</h1>
                    <?php if (empty($students)): ?>
                        <p style="color: black">No students found.</p>
                    <?php else: ?>
                        <div class="cards-grid">
                            <?php foreach ($students as $student): ?>
                                <div class="cards" id="student<?= htmlspecialchars($student['ID_STUDENT']) ?>"
                                    onclick="window.location.href='/profile.php?id=<?= urlencode($student['ID_STUDENT']) ?>'"
                                    style="cursor: pointer;">
                                    <div class="cont">
                                        <div class="details">
                                            <div class="content-wrapper">
                                                <img class="img" src="/uploads/profiles/<?= htmlspecialchars($student['PROFILE_IMAGE']) ?>"
                                                    alt="<?= htmlspecialchars($student['NAME']) ?>"
                                                    onerror="this.src='/uploads/profiles/default.png'">
                                                <div class="text-content">
                                                    <h2 class="title"><?= htmlspecialchars($student['NAME']) ?></h2>
                                                    <div class="subtitle-container">
                                                        <div class="subtitles" style="background-color: #32533D;">Student</div>
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
                    <?php endif; ?>
                    <?php elseif ($user->type === 'STUDENT'): ?>
                        <h1 style="color: #32533D">My Tutors</h1>
                    <?php if (empty($tutors)): ?>
                        <p style="color: black">No tutors found.</p>
                    <?php else: ?>
                        <div class="cards-grid">
                            <?php foreach ($tutors as $tutor): ?>
                                <div class="cards" id="tutor<?= htmlspecialchars($tutor['ID_TUTOR']) ?>"
                                    onclick="window.location.href='/profile.php?id=<?= urlencode($tutor['ID_TUTOR']) ?>'"
                                    style="cursor: pointer;">
                                    <div class="cont">
                                        <div class="details">
                                            <div class="content-wrapper">
                                                <img class="img" src="/uploads/profiles/<?= htmlspecialchars($tutor['PROFILE_IMAGE']) ?>"
                                                    alt="<?= htmlspecialchars($tutor['NAME']) ?>"
                                                    onerror="this.src='/uploads/profiles/default.png'">
                                                <div class="text-content">
                                                    <h2 class="title"><?= htmlspecialchars($tutor['NAME']) ?></h2>
                                                    <div class="subtitle-container">
                                                        <div class="subtitles" style="background-color: #03254E">Tutor</div>
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
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <script src="/scripts/homepage_script.js"></script>
</body>
</html>


