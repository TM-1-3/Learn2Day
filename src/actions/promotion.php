<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../database/userclass.php');
require_once(__DIR__ . '/../database/studentclass.php');
require_once(__DIR__ . '/../database/tutorclass.php');

$session = Session::getInstance();
$db = Database::getInstance();

// Check if user is logged in and is an admin
if (!$session->isLoggedIn() || $session->getUser()->type !== 'ADMIN') {
    header('Location: /');
    exit();
}

// Check if the request is POST and has the username
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = trim($_POST['username']);
    
    try {
        $db->beginTransaction();
        
        // Get the user to promote
        $user = User::get_user_by_username($username);
        
        if (!$user) {
            throw new Exception("User not found");
        }
        
        // Check if user is already an admin
        if ($user->type === 'ADMIN') {
            throw new Exception("User is already an admin");
        }
        
        // Get the user's profile data before deletion
        $profileData = null;
        if ($user->type === 'TUTOR') {
            $profile = Tutor::getByUsername($username);
            $profileData = [
                'name' => $profile->name,
                'date_of_birth' => $profile->date_of_birth,
                'profile_image' => $profile->profile_image,
                'description' => $profile->description
            ];
            Tutor::delete($username);
        } elseif ($user->type === 'STUDENT') {
            $profile = Student::getByUsername($username);
            $profileData = [
                'name' => $profile->name,
                'date_of_birth' => $profile->date_of_birth,
                'profile_image' => $profile->profile_image,
                'description' => $profile->description
            ];
            Student::delete($username);
        }
        
        // Update user type to ADMIN
        $user->update($user->username, $user->email, 'ADMIN');
        
        // Insert into ADMIN table if profile data exists
        if ($profileData) {
            $stmt = $db->prepare('
                INSERT INTO ADMIN 
                (ID_ADMIN, NAME, DATE_OF_BIRTH, PROFILE_IMAGE, DESCRIPTION) 
                VALUES (?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $username,
                $profileData['name'],
                $profileData['date_of_birth'],
                $profileData['profile_image'],
                $profileData['description']
            ]);
        }
        
        $db->commit();
        
        // Redirect back to the profile page with success message
        header('Location: /profile.php?id=' . urlencode($username) . '&promote_success=1');
        exit();
    } catch (Exception $e) {
        $db->rollBack();
        // Redirect back with error message
        header('Location: /profile.php?id=' . urlencode($username) . '&promote_error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Invalid request
    header('Location: /');
    exit();
}