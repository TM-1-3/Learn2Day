<?php
require_once __DIR__ . '/database/requestclass.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/database/userclass.php';
require_once __DIR__ . '/database/tutorclass.php';
require_once __DIR__ . '/database/studentclass.php';

$session = Session::getInstance();

if (!$session->isLoggedIn()) {
    header('Location: /');
    exit();
}

$student = $session->getUser();

$tutorusername = $_GET['id'] ?? $session->getUser()->username;

$tutor = Tutor::getByUsername($tutorusername);

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    if (!empty($message)) {
        $request = new Request(
            usernametutor: $tutorusername,
            usernamestudent: $student->username,
            accepted: false,
            date_sent: date('Y-m-d H:i:s'),
            message: $message
        );

        $request->create();
        header('Location: /profile.php?id=' . urlencode($tutorusername));
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write your request</title>
    <link rel="stylesheet" href="styles/requestwriter.css">
</head>

<body>
    <div class="container">
    <?php if (!empty($success)): ?>
        <h1>Request sent to <?= htmlspecialchars($tutorusername) ?></h1>
    <?php else: ?>
        <h1>Please write a message to <?= htmlspecialchars($tutorusername) ?></h1>
    <?php endif; ?>
    <div class="profile-header-inner1">
        <img src="/uploads/profiles/<?= htmlspecialchars($tutor->profile_image) ?>"
            alt="Profile Picture"
            class="profile-picture"
            onerror="this.src='/uploads/profiles/default.png'">
        <div class="name-only">
            <div class="name-username">
                <h1 style="color: #03254E"><?= htmlspecialchars($tutor->name) ?></h1>
                <p class="username"> @<?= htmlspecialchars($tutorusername) ?></p>
                <span class="badge" style="background-color: #03254E">Tutor</span>
            </div>
        </div>
    </div>
    <form method="post" action="/requestwriter.php?id=<?= urlencode($tutorusername) ?>">
        <label for="message" id="message-title">Your Message</label>
        <textarea id="message" name="message" rows="4" cols="50"></textarea>
        <br>
        <div class="buttons">
        <button type="submit" class="request-send">Send Request</button>
        <button type="button" class="cancel" onclick="window.location.href='/profile.php?id=<?= urlencode($tutorusername) ?>'">Cancel</button>
        </div>
    </form>
    </div>
</body>
</html>