<?php
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/includes/session.php';

$session = Session::getInstance();

if (!$session->isLoggedIn()) {
    header('Location: /');
    exit();
}

$user_id = $session->getUserId();
$user = $session->getUser();

if (!$user) {
    header('Location: /');
    exit();
}

$errors = [];
$name = '';
$date_of_birth = '';
$description = '';
$school_institution = '';
$profile_image = '';

// Helper function for upload error messages
function uploadErrorToString(int $error_code): string {
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
    ];
    return $errors[$error_code] ?? 'Unknown upload error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    }

    $name = trim($_POST['name'] ?? '');
    $date_of_birth = trim($_POST['date_of_birth'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $school_institution = trim($_POST['school_institution'] ?? '');

    // Validation
    if (empty($name)) {
        $errors[] = 'Name is required';
    } elseif (strlen($name) > 100) {
        $errors[] = 'Name must be less than 100 characters';
    }

    if (empty($date_of_birth)) {
        $errors[] = 'Date of birth is required';
    } else {
        $dob = DateTime::createFromFormat('Y-m-d', $date_of_birth);
        if (!$dob || $dob->format('Y-m-d') !== $date_of_birth) {
            $errors[] = 'Invalid date format for date of birth (use YYYY-MM-DD)';
        }
    }

    if (empty($school_institution)) {
        $errors[] = 'School/Institution is required';
    } elseif (strlen($school_institution) > 100) {
        $errors[] = 'School/Institution must be less than 100 characters';
    }

    if (strlen($description) > 500) {
        $errors[] = 'Description must be less than 500 characters';
    }

    // File upload handling
    $upload_success = false;
    $destination = '';
    
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file_error = $_FILES['profile_image']['error'];
        
        if ($file_error === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['profile_image']['type'];
            $file_size = $_FILES['profile_image']['size'];
            
            if (!in_array($file_type, $allowed_types)) {
                $errors[] = 'Only JPG, PNG, and GIF images are allowed';
            } elseif ($file_size > 2 * 1024 * 1024) {
                $errors[] = 'Image size must be less than 2MB';
            } else {
                $upload_dir = __DIR__ . '/uploads/profiles/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
                $profile_image = 'profile_' . $user_id . '_' . bin2hex(random_bytes(8)) . '.' . $file_ext;
                $destination = $upload_dir . $profile_image;
                
                if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination)) {
                    $errors[] = 'Failed to upload profile image';
                } else {
                    $upload_success = true;
                }
            }
        } else {
            $errors[] = 'File upload error: ' . uploadErrorToString($file_error);
        }
    } else {
        $errors[] = 'Profile image is required';
    }

    if (empty($errors)) {
        try {
            Student::create(
                $user_id,
                $name,
                $date_of_birth,
                $profile_image,
                $description,
                $school_institution
            );
            header('Location: /profile.php?id=' . $user_id);
            exit();
        } catch (Exception $e) {
            if ($upload_success && file_exists($destination)) {
                unlink($destination);
            }
            $errors[] = 'Error creating profile: ' . $e->getMessage();
        }
    }
}

if ($user_id <= 0) {
    die('Invalid user ID');
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Profile</title>
        <link rel="stylesheet" href="createprofile_style.css">
    </head>
    <body>
        <div class="container" id="container">
            <div class="profile-form-container">
                <form action="create_profile.php" method="POST" enctype="multipart/form-data">
                    <h1>Create Profile</h1>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

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
                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                            <span class="upload-text">Click to upload profile image</span>
                            <img id="image-preview" alt="Preview">
                        </div>
                        <input type="file" id="fileInput" class="upload-input" name="profile_image" accept="image/jpeg,image/png,image/gif" required>
                        <button type="button" class="upload-btn" onclick="document.getElementById('fileInput').click()">Choose File</button>
                        <div class="file-info" id="fileInfo">No file chosen</div>
                    </div>

                    <button type="submit" class="S_signUp">Create Profile</button>
                </form>
            </div>
        </div>
        <script src="profile_image.js"></script>
    </body>
</html>