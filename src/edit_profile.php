<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/database/tutorclass.php';

$session = Session::getInstance();
$isLoggedIn = $session->isLoggedIn();

if (!$isLoggedIn) {
    header('Location: /');
    exit();
}

$user = $session->getUser();
$errors = [];
$success = false;

if ($user->type === 'STUDENT') {
    $profile = Student::getById($user->id);
} else if ($user->type === 'TUTOR') {
    $profile = Tutor::getById($user->id);
} else {
    die('Invalid user type');
}

if (!$profile) {
    die('Profile not found');
}

$name = $profile->name ?? '';
$date_of_birth = $profile->date_of_birth ?? '';
$school_institution = $profile->school_institution ?? '';
$description = $profile->description ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $name = trim($_POST['name'] ?? '');
        $date_of_birth = trim($_POST['date_of_birth'] ?? '');
        $school_institution = trim($_POST['school_institution'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            $errors[] = 'Name is required';
        }
        if (empty($date_of_birth)) {
            $errors[] = 'Date of birth is required';
        }
        if (empty($school_institution)) {
            $errors[] = 'School/Institution is required';
        }

        $profile_image = $profile->profile_image;
        
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/profiles/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExt = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('profile_') . '.' . $fileExt;
            $targetPath = $uploadDir . $fileName;

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = mime_content_type($_FILES['profile_image']['tmp_name']);
            
            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {

                    if ($profile->profile_image !== 'uploads/profiles/default.png' && file_exists($uploadDir . $profile->profile_image)) {
                        unlink($uploadDir . $profile->profile_image);
                    }
                    $profile_image = $fileName;
                } else {
                    $errors[] = 'Failed to upload image';
                }
            } else {
                $errors[] = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
            }
        }

        if (empty($errors)) {

            if ($user->type === 'STUDENT') {
                $student = new Student(
                    $user->id,
                    $name,
                    $date_of_birth,
                    $profile_image,
                    $description,
                    $school_institution
                );
                $student->update();
            } else if ($user->type === 'TUTOR') {
                $tutor = new Tutor(
                    $user->id,
                    $name,
                    $date_of_birth,
                    $profile_image,
                    $description,
                    $school_institution
                );
                $tutor->update();
            }
            
            $success = true;

            header("Location: /profile.php?id=" . $user->id);
            exit();
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Profile</title>
        <link rel="stylesheet" href="styles/editprofile_style.css">
        <link rel="stylesheet" href="styles/homepage.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    </head>
    
    <body>
        <div class="container" id="container">
            <div class="profile-form-container">
            <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
                <h1>Edit Profile</h1>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                
                <div class="name">
                    <input type="text" name="name" placeholder="Full Name" value="<?= htmlspecialchars($name) ?>" required maxlength="100" />
                </div>
                
                <div class="dob">
                    <input type="date" name="date_of_birth" placeholder="Date of Birth" value="<?= htmlspecialchars($date_of_birth) ?>" required />
                </div>
                
                <div class="institution">
                    <input type="text" name="school_institution" placeholder="School/Institution" value="<?= htmlspecialchars($school_institution) ?>" required maxlength="100" />
                </div>
                
                <div class="description">
                    <textarea name="description" placeholder="About you..." maxlength="500"><?= htmlspecialchars($description) ?></textarea>
                </div>
                
                <div class="image-upload-container">
                    <div class="upload-area" id="uploadArea">
                        <img id="image-preview" src="/uploads/profiles/<?= htmlspecialchars($profile->profile_image) ?>" 
                            onerror="this.src='/uploads/profiles/default.png'">
                    </div>
                    <input type="file" id="fileInput" class="upload-input" name="profile_image" accept="image/jpeg,image/png,image/gif">
                    <?php
                    if($user->type == 'TUTOR'): ?>
                    <button type="button" class="upload-btnT" onclick="document.getElementById('fileInput').click()">Choose File</button>
                    <?php
                    elseif($user->type == 'STUDENT'): ?>
                    <button type="button" class="upload-btnS" onclick="document.getElementById('fileInput').click()">Choose File</button>
                    <?php endif; ?>
                    <div class="file-info" id="fileInfo">Current: <?= htmlspecialchars($profile->profile_image) ?></div>
                </div>
                <?php
                if($user->type == 'TUTOR'): ?>
                <button type="submit" class="T_signUp">Save Changes</button>
                <?php 
                elseif($user->type == 'STUDENT'): ?>
                <button type="submit" class="S_signUp">Save Changes</button>
                <?php endif; ?>
            </form>
            </div>
        </div>
        <script src="scripts/profile_image.js"></script>
        <script src="scripts/homepage_script.js"></script>
    </body>
</html>

