<?php
require_once __DIR__ . '/database/requestclass.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/database/userclass.php';
require_once __DIR__ . '/database/tutorclass.php';
require_once __DIR__ . '/database/studentclass.php';

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
                <a href="/messages.php" class="viewprofile-btn">Messages</a>
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
        <h1>My Requests</h1>
        <?php if (empty($requests)): ?>
            <p>No requests found.</p>
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
                            <td>
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
                                    <span style="color:green;">Accepted</span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
    <script src="/scripts/homepage_script.js"></script>
</body>
</html>


