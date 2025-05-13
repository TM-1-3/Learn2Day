<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/userclass.php');

$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];
$type = $_POST['type'];

Customer::create($username, $password, $email, $type);

header('Location: /');
?>