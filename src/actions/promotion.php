<?php

declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../database/userclass.php');
require_once(__DIR__ . '/../database/studentclass.php');
require_once(__DIR__ . '/../database/tutorclass.php');
require_once(__DIR__ . '/../database/adminclass.php');

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

        if ($user->type === 'ADMIN') {
            throw new Exception("User is already an admin");
        }

        $profileData = null;
        if ($user->type === 'TUTOR') {
            $profile = Tutor::getByUsername($username);
            if ($profile) {
                $profileData = [
                    'name' => $profile->name,
                    'date_of_birth' => $profile->date_of_birth,
                    'profile_image' => $profile->profile_image,
                    'description' => $profile->description
                ];
                if (!Tutor::delete($username)) {
                    throw new Exception("Failed to delete tutor profile");
                }
            }
        } elseif ($user->type === 'STUDENT') {
            $profile = Student::getByUsername($username);
            if ($profile) {
                $profileData = [
                    'name' => $profile->name,
                    'date_of_birth' => $profile->date_of_birth,
                    'profile_image' => $profile->profile_image,
                    'description' => $profile->description
                ];
                if (!Student::delete($username)) {
                    throw new Exception("Failed to delete student profile");
                }
            }
        }

        if (!$user->update($user->username, $user->email, 'ADMIN')) {
            throw new Exception("Failed to update user type");
        }

        if ($profileData && !Admin::create(
            $username,
            $profileData['name'],
            $profileData['date_of_birth'],
            $profileData['profile_image'],
            $profileData['description']
        )) {
            throw new Exception("Failed to create admin profile");
        }

        $db->commit();

        header('Location: /profile.php?id=' . urlencode($username) . '&promote_success=1');
        exit();
    } catch (Exception $e) {
        $db->rollBack();
        header('Location: /profile.php?id=' . urlencode($username) . '&promote_error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location: /');
    exit();
}
