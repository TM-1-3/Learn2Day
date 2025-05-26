<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/requestclass.php';

$session = Session::getInstance();
if (!$session->isLoggedIn()) {
    header('Location: /register_page.php');
    exit();
}
$user = $session->getUser();

if ($user->type !== 'TUTOR') {
    http_response_code(403);
    echo 'Forbidden';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    $request = Request::getById($request_id);
    if ($request && $request->usernametutor === $user->username) {
        $request->deny();
    }
}

header('Location: /viewrequests.php');
exit();
?>