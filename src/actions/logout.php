<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');

Session::getInstance()->logout();

header('Location: /');
?>