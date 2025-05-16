<?php
declare(strict_types=1);

require_once (__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../database/userclass.php');
require_once(__DIR__ . '/../includes/session.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $user = User::get_user_by_username_password($username, $password);

        if ($user) {
            Session::getInstance()->login($user);
            header('Location: /');
            exit();
        }
    } catch (InvalidArgumentException $e) {

    }

    header('Location: /?login_error=1');
    exit();
}

header('Location: /');
exit();
?>