<?php
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/database/tutorclass.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/database/qualificationclass.php';

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
$subjects = [];
$languages = [];
$profile_image = '';

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
    $languages = isset($_POST['languages']) ? array_filter($_POST['languages']) : [];
    $subjects = isset($_POST['subjects']) ? array_filter($_POST['subjects']) : [];

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

    if (strlen($description) > 500) {
        $errors[] = 'Description must be less than 500 characters';
    }

    if (empty($subjects)) {
        $errors[] = 'At least one subject is required';
    }

    if (empty($languages)) {
        $errors[] = 'At least one language is required';
    }

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
            if($user->type == 'STUDENT'){
                Student::create(
                    $user->username,
                    $name,
                    $date_of_birth,
                    $profile_image,
                    $description
                );
                foreach ($subjects as $subject) {
                    Qualifications::addStudentSubject($user->username, $subject);
                }
                foreach ($languages as $language) {
                    Qualifications::addStudentLanguage($user->username, $language);
                }
            }
            if($user->type == 'TUTOR'){
                Tutor::create(
                    $user->username,
                    $name,
                    $date_of_birth,
                    $profile_image,
                    $description
                );
                foreach ($subjects as $subject) {
                    Qualifications::addTutorSubject($user->username, $subject);
                }
                foreach ($languages as $language) {
                    Qualifications::addTutorLanguage($user->username, $language);
                }
            }
            header('Location: /profile.php?id=' . urlencode($user->username));
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
        <link rel="stylesheet" href="styles/createprofile.css">
        <link rel="stylesheet" href="styles/index.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    </head>
    
    <?php if($user->type == 'STUDENT'): ?>
        <body style="background-color: #32533D">
    <?php elseif($user->type == 'TUTOR'): ?>
        <body style="background-color: #03254E">
    <?php endif; ?>
        <div class="container" id="container">
            <div class="profile-form-container">
                <form action="create_profile.php" method="POST" enctype="multipart/form-data">
                    <?php if($user->type == 'STUDENT'): ?>
                        <h1 style="color: #32533D">Create Profile</h1>
                    <?php elseif($user->type == 'TUTOR'): ?>
                        <h1 style="color: #03254E">Create Profile</h1>
                    <?php endif; ?>

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

                    <div class="ndp">
                        <div class="name-dob">
                            <div class="name">
                                <input type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($name) ?>" required maxlength="100" />
                            </div>
                            
                            <div class="dob">
                                <input type="date" name="date_of_birth" placeholder="Date of Birth" value="<?= htmlspecialchars($date_of_birth) ?>" required />
                            </div>
                        </div>

                        <div class="image-upload-container">
                            <div class="upload-area" id="uploadArea">
                                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                <span class="upload-text">Click to upload profile image</span>
                                <img id="image-preview" alt="Preview">
                            </div>
                            <input type="file" id="fileInput" class="upload-input" name="profile_image" accept="image/jpeg,image/png,image/gif">
                            <?php if($user->type == 'STUDENT'): ?>
                            <button type="button" class="upload-btnS" onclick="document.getElementById('fileInput').click()">Choose File</button>
                            <?php elseif($user->type == 'TUTOR'): ?>
                            <button type="button" class="upload-btnT" onclick="document.getElementById('fileInput').click()">Choose File</button>
                            <?php endif; ?>
                            <div class="file-info" id="fileInfo">No file chosen</div>
                        </div>
                    </div>

                    <div class="description">
                        <textarea name="description" placeholder="About you..." maxlength="500"><?= htmlspecialchars($description) ?></textarea>
                    </div>
                    
                    <div class="qualifications-section">
                        <h2 style="margin-top: 50px;"><?= $user->type === 'TUTOR' ? 'Your Qualifications' : 'Your Needs' ?></h2>
                        
                        <?php if ($user->type === 'TUTOR'): ?>
                            <!-- Tutor Qualifications -->
                            <div class="form-group">
                                <label>Subjects You Can Teach</label>
                                <div id="tutor-subjects-container">
                                    <div class="subject-entry">
                                        <select name="subjects[]" class="subject-select">
                                            <option value="">Select a subject</option>
                                            <?php foreach (Qualifications::getAllSubjects() as $subject): ?>
                                                <option value="<?= htmlspecialchars($subject) ?>"><?= htmlspecialchars($subject) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <select name="tutor_level[]" class="grade-select">
                                            <option value="">School level</option>
                                            <?php foreach (Qualifications::getAllTutorLevels() as $tutor_level): ?>
                                                    <option value="<?= htmlspecialchars($tutor_level) ?>"><?= htmlspecialchars($tutor_level) ?></option>
                                                <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="remove-btn" onclick="removeSubject(this)">Remove</button>
                                    </div>
                                </div>
                                <button type="button" class="add-btn" onclick="addSubject()">Add Another Subject</button>
                            </div>
                        <?php else: ?>
                            <!-- Student Needs -->
                            <div class="form-group">
                                <label>Subjects You Need Help With</label>
                                <div id="student-subjects-container">
                                    <div class="subject-entry">
                                        <select name="subjects[]" class="subject-select">
                                            <option value="">Select a subject</option>
                                            <?php foreach (Qualifications::getAllSubjects() as $subject): ?>
                                                <option value="<?= htmlspecialchars($subject) ?>"><?= htmlspecialchars($subject) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <select name="student_levels[]" class="grade-select">
                                            <option value="">Grade level</option>
                                            <?php foreach (Qualifications::getAllStudentLevels() as $student_level): ?>
                                                <option value="<?= htmlspecialchars($student_level) ?>"><?= htmlspecialchars($student_level) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="remove-btn" onclick="removeSubject(this)">Remove</button>
                                    </div>
                                </div>
                                <button type="button" class="add-btn" onclick="addSubject()">Add Another Subject</button>
                            </div>
                        <?php endif; ?>

                        <div class="languages">
                            <label>Languages</label>
                            <div id="languages-container">
                                <div class="language-entry">
                                    <select name="languages[]" class="language-select">
                                        <option value="">Select a language</option>
                                        <?php foreach (Qualifications::getAllLanguages() as $language): ?>
                                            <option value="<?= htmlspecialchars($language) ?>" <?= in_array($language, $languages) ? 'selected' : '' ?>><?= htmlspecialchars($language) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="remove-btn" onclick="removeLanguage(this)">Remove</button>
                                </div>
                            </div>
                            <button type="button" class="add-btn" onclick="addLanguage()">Add Another Language</button>
                        </div>
                    </div>

                    <?php if ($user->type == 'STUDENT'): ?>
                    <button type="submit" class="S_signUp">Create Profile</button>
                    <?php elseif ($user->type == 'TUTOR'): ?>
                    <button type="submit" class="T_signUp">Create Profile</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <script src="scripts/profile_image.js"></script>
        <script src="scripts/index_script.js"></script>
        <script src="scripts/createprofile_script.js"></script>
    </body>
</html>