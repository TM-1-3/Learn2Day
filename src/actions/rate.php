<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/ratingclass.php';
require_once __DIR__ . '/../database/tutorclass.php';
require_once __DIR__ . '/../database/studentclass.php';

$session = Session::getInstance();
if (!$session->isLoggedIn()) {
    header('Location: /register_page.php');
    exit();
}

$user = $session->getUser();

if ($user->type !== 'STUDENT') {
    http_response_code(403);
    echo 'Only students can rate tutors.';
    exit();
}

$tutor_username = $_GET['tutor'] ?? null;
if (!$tutor_username) {
    echo 'No tutor specified.';
    exit();
}

if ($tutor_username === $user->username) {
    echo 'You cannot rate yourself.';
    exit();
}

$tutor = Tutor::getByUsername($tutor_username);
if (!$tutor) {
    echo 'Tutor not found.';
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment = trim($_POST['comment'] ?? '');
    if ($rating < 0 || $rating > 5 || ($rating * 10) % 5 !== 0) {
        $error = 'Invalid rating. Must be 0, 0.5, 1, ..., 5.';
    } else {
        try {
            $newRating = new Rating(null, $tutor_username, $user->username, $rating, $comment);
            $newRating->create();
            $success = 'Rating submitted!';
        } catch (Exception $e) {
            $error = 'Could not submit rating: ' . $e->getMessage();
        }
    }
}
?>
