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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
}

$request = new Request(
    usernametutor: $tutorusername,
    usernamestudent: $student->username,
    accepted: false,
    date_sent: date('Y-m-d H:i:s'),
    message: $message
);

$request->create();



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write your request</title>
    <link rel="stylesheet" href="">
</head>

<body>
    <h1>
        <?php
        if (isset($_POST['message']) && !empty($_POST['message'])) {
            echo "Request sent to $tutorusername";
        } else {
            echo "Please write a message to $tutorusername";
        }
        ?>
    </h1>
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
    <form method="post" action="">
        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="4" cols="50"></textarea>
        <br>
        <button type="submit" class="request-send">Send Request</button>
        <button type="button" class="cancel" onclick="window.location.href='/profile.php?id=<?= urlencode($tutor->username['ID_TUTOR']) ?>'">Cancel</button>
    </form>
</body>
</html>