<?php

require_once __DIR__ . '/database/requestclass.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/database/userclass.php';
require_once __DIR__ . '/database/tutorclass.php';
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/database/adminclass.php';
require_once __DIR__ . '/database/message.php';

$session = Session::getInstance();
if (!$session->isLoggedIn()) {
    header('Location: /register_page.php');
    exit();
}
$user = $session->getUser();

// Fetch requests for the logged-in user
$messagessent = [];
$messagesreceived = [];
$messagessent = Message::getMessagesSent($user->username);
$messagesreceived = Message::getMessagesReceived($user->username);


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
        <h1>My Messages</h1>
        <div class="messages-section">
            <div class="messages-table-container">
                <h2>Messages Sent</h2>
                <?php if (empty($messagessent)): ?>
                    <p>No sent messages.</p>
                <?php else: ?>
                <table class="messages-table">
                    <thead>
                        <tr>
                            <th>To</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messagessent as $msg): ?>
                        <tr>
                            <td><?= htmlspecialchars($msg->receiver) ?></td>
                            <td><?= nl2br(htmlspecialchars($msg->content)) ?></td>
                            <td><?= htmlspecialchars($msg->timestamp ?? $msg->date_sent ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
            <div class="messages-table-container">
                <h2>Messages Received</h2>
                <?php if (empty($messagesreceived)): ?>
                    <p>No received messages.</p>
                <?php else: ?>
                <table class="messages-table">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messagesreceived as $msg): ?>
                        <tr>
                            <td><?= htmlspecialchars($msg->sender) ?></td>
                            <td><?= nl2br(htmlspecialchars($msg->content)) ?></td>
                            <td><?= htmlspecialchars($msg->timestamp ?? $msg->date_sent ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <script src="/scripts/homepage_script.js"></script>
</body>
</html>







