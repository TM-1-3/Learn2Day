<?php

require_once __DIR__ . '/database/requestclass.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/database/userclass.php';
require_once __DIR__ . '/database/tutorclass.php';
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/database/message.php';

$session = Session::getInstance();

if (!$session->isLoggedIn()) {
    header('Location: /');
    exit();
}

$sender = $session->getUser();

$recieverusername = $_GET['id'] ?? $session->getUser()->username;

$reciever = User::get_user_by_username($recieverusername);

$content = '';

$type = $reciever->type;

if ($reciever->type == 'TUTOR') {
    $profile = Tutor::getByUsername($recieverusername);
} else if ($reciever->type == 'STUDENT') {
    $profile = Student::getByUsername($recieverusername);
} else {
    $profile = Admin::getByUsername($recieverusername);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['message'] ?? '');
    if (!empty($content)) {
        $message = new Message(
            sender: $sender->username,
            receiver: $recieverusername,
            date_sent: date('Y-m-d H:i:s'),
            content: $content
        );

        $message->create(
            $sender->username,
            $recieverusername,
            date('Y-m-d H:i:s'),
            $content
        );
        header('Location: /profile.php?id=' . urlencode($recieverusername));
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write your message</title>
    <link rel="stylesheet" href="">
</head>

<body>
    <h1>Please write a message to <?= htmlspecialchars($profile->name) ?></h1>
    <div class="profile-header-inner1">
        <img src="/uploads/profiles/<?= htmlspecialchars($profile->profile_image) ?>"
            alt="Profile Picture"
            class="profile-picture"
            onerror="this.src='/uploads/profiles/default.png'">
        <div class="name-only">
            <div class="name-username">
                <?php if ($type == 'TUTOR') { ?>
                    <h1 style="color: #03254E"><?= htmlspecialchars($profile->name) ?></h1>
                    <p class="username"> @<?= htmlspecialchars($profile->username) ?></p>
                    <span class="badge" style="background-color: #03254E">Tutor</span>
                <?php } elseif ($type == 'STUDENT') { ?>
                    <h1 style="color: #32533D"><?= htmlspecialchars($profile->name) ?></h1>
                    <p class="username"> @<?= htmlspecialchars($profile->username) ?></p>
                    <span class="badge" style="background-color: #32533D">Student</span>
                <?php } else { ?>
                    <h1 style="color: #FFD670"><?= htmlspecialchars($profile->name) ?></h1>
                    <p class="username"> @<?= htmlspecialchars($profile->username) ?></p>
                    <span class="badge" style="background-color: #FFD670">Admin</span>
                <?php } ?>
            </div>
        </div>
    </div>
    <form method="post" action="/messagewriter.php?id=<?= urlencode($recieverusername) ?>">
        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="4" cols="50"></textarea>
        <br>
        <button type="submit" class="request-send">Send Message</button>
        <button type="button" class="cancel" onclick="window.location.href='/profile.php?id=<?= urlencode($recieverusername) ?>'">Cancel</button>
    </form>
</body>


</html>