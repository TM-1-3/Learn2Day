<?php
declare(strict_types=1);

require_once(__DIR__ . '/../../includes/session.php');
require_once(__DIR__ . '/../../database/userclass.php');
require_once(__DIR__ . '/../../database/studentclass.php');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $email = $_POST['email'] ?? '';
    $type = 'STUDENT'; // Hardcoded since this is register_student.php

    // Validate inputs
    if (empty($username) || empty($password) || empty($email)) {
        throw new Exception('All fields are required');
    }

    if ($password !== $confirm_password) {
        throw new Exception('Passwords do not match');
    }

    if (strlen($password) < 6) {
        throw new Exception('Password must be at least 6 characters long');
    }

    // Check if user already exists
    $db = Database::getInstance();
    $stmt = $db->prepare('SELECT * FROM users WHERE username = ? OR email = ?');
    $stmt->execute([$username, $email]);
    
    if ($stmt->fetch()) {
        throw new Exception('Username or email already exists');
    }

    // Create user
    $user_id = User::create($username, $password, $email, $type);
    
    // Get the newly created user
    $user = User::get_user_by_username_password($username, $password);
    if (!$user) {
        throw new Exception('Failed to retrieve user after creation');
    }

    // Login the user
    Session::getInstance()->login($user);

    // Redirect to profile creation
    header('Location: /create_profile.php');
    exit();
} catch (Exception $e) {
    header('Location: /register_page.php?error=' . urlencode($e->getMessage()));
    exit();
}
?>