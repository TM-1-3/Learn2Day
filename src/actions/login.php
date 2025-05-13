<?php
declare(strict_types=1);

require_once (__DIR__ . '/../includes/database.php');

require_once(__DIR__ . '/../database/userclass.php');

$username = $_POST['username'];
$password = $_POST['password'];

$user = User::get_customer_by_username_password($username, $password);

if($user) {
    Session::getInstance()->login($user);
    header('Location: /');
} else {
    header('Location: /?login_error=1');
}
exit();



header('Location: /');
?>