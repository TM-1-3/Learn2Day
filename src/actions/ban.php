<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/userclass.php';
require_once __DIR__ . '/../database/adminclass.php';
require_once __DIR__ . '/../database/studentclass.php';
require_once __DIR__ . '/../database/tutorclass.php';

$session = Session::getInstance();
$db = Database::getInstance();

if (!$session->isLoggedIn() || $session->getUser()->type !== 'ADMIN') {
    header('Location: /');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = trim($_POST['username']);

    try {
        $db->beginTransaction();

        $user = User::get_user_by_username($username);

        if (!$user) {
            throw new Exception("User not found");
        }

        if ($user->type === 'TUTOR') {
            $profile = Tutor::getByUsername($username);
            if ($profile) {
                if (!Tutor::delete($username)) {
                    throw new Exception("Failed to delete tutor profile");
                }
            }
        } elseif ($user->type === 'STUDENT') {
            $profile = Student::getByUsername($username);
            if (!Student::delete($username)) {
                throw new Exception("Failed to delete student profile");
            }
        }

        if (!$user->delete()) {
            throw new Exception("Failed to update user type");
        }


        $db->commit();

        header('Location: /');
        exit();
    } catch (Exception $e) {
        $db->rollBack();
        header('Location: /profile.php?id=' . urlencode($username) . '&deleteerror=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location: /');
    exit();
}
