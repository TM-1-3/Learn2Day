<?php
declare(strict_types=1);
require_once __DIR__ . '/../database/studentclass.php';
require_once __DIR__ . '/../database/tutorclass.php';
require_once __DIR__ . '/../includes/session.php';

$session = Session::getInstance();

if(!$session->isLoggedIn()) {
    die(header('Location: /'));
}

$user = $session->getUser();
$profileType = $user->type;

if($profileType == 'STUDENT'){
    $student = Student::getById($user->id);
    if ($student) {
        $student->update();
    }
} else if($profileType == 'TUTOR'){
    $tutor = Tutor::getById($user->id);
    if ($tutor) {
        $tutor->update();
    }
} else {
    die('Invalid user type');
}

header('Location: /profile.php?id=' . $user->id);
?>