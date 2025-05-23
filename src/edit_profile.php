<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/database/studentclass.php';
require_once __DIR__ . '/database/tutorclass.php';
require_once __DIR__ . '/database/userclass.php';
require_once __DIR__ . '/database/qualificationclass.php';

$session = Session::getInstance();
$user = $session->getUser();

if (!$session->isLoggedIn()) {
    header('Location: /');
    exit();
}

// Get profile based on user type
if ($user->type === 'STUDENT') {
    $profile = Student::getByUsername($user->username);
} elseif ($user->type === 'TUTOR') {
    $profile = Tutor::getByUsername($user->username);
} else {
    header('Location: /');
    exit();
}

if (!$profile) {
    header('Location: /');
    exit();
}

$oldusername = $user->username;

// Initialize variables
$name = $profile->name;
$date_of_birth = $profile->date_of_birth;
$description = $profile->description ?? '';
$profile_image = $profile->profile_image;
$errors = [];
$username = $user->username;
$email = $user->email;

// Get qualifications
if ($user->type === 'TUTOR') {
    $qualifications = Qualifications::getTutorQualifications($user->username);
    $subjects = $qualifications['subjects'] ?? [];
    $languages = $qualifications['languages'] ?? [];
} else {
    $qualifications = Qualifications::getStudentNeeds($user->username);
    $subjects = $qualifications['subjects'] ?? [];
    $languages = $qualifications['languages'] ?? [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['name'] ?? '');
    $new_date_of_birth = trim($_POST['date_of_birth'] ?? '');
    $new_description = trim($_POST['description'] ?? '');
    $new_username = trim($_POST['username'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($new_name)) {
        $errors['name'] = 'Name cannot be empty.';
    }
    if (empty($new_date_of_birth)) {
        $errors['date_of_birth'] = 'Date of Birth cannot be empty.';
    }
    if (empty($new_username)) {
        $errors['username'] = 'Username cannot be empty.';
    } elseif ($new_username !== $user->username && User::get_user_by_username($new_username)) {
        $errors['username'] = 'Username already exists.';
    }
    if (empty($new_email)) {
        $errors['email'] = 'Email cannot be empty.';
    } elseif ($new_email !== $user->email && User::get_user_by_email($new_email)) {
        $errors['email'] = 'Email already exists.';
    }
    if (!empty($new_password) && $new_password !== $confirm_password) {
        $errors['password'] = 'Passwords do not match.';
    }

    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/profiles/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $image_ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $new_image_name = $new_username . '.' . $image_ext; // Use new username
        $new_image_path = $upload_dir . $new_image_name;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $new_image_path)) {
            $profile_image = '/uploads/profiles/' . $new_image_name;
        } else {
            $errors['profile_image'] = 'Failed to upload image.';
        }
    }

    if (empty($errors)) {
        $old_username = $user->username;

        // Update User class properties in the session object
        $user->username = $new_username;
        $user->email = $new_email;

        // Update User in database
        $user_updated = $user->update($new_username, $new_email, $user->type);

        // Crucial: If username changed, update the profile object's username property
        // BEFORE calling its update() method or any Qualification update methods.
        if ($old_username !== $new_username) {
            $profile->username = $new_username;
        }


        // Update profile (Student/Tutor) properties in the object
        $profile->name = $new_name;
        $profile->date_of_birth = $new_date_of_birth;
        $profile->description = $new_description;
        $profile->profile_image = $profile_image;

        // Update Student/Tutor profile in database using the instance method
        $profile_updated = $profile->update($old_username);


        // Handle password update if provided
        if (!empty($new_password)) {
            User::updatePassword($user->id, $new_password);
        }

        // Handle qualifications update (subjects, languages, degrees, levels)
        $qualifications_updated = true; // Assume true unless an update fails

        // Use $new_username for all Qualification updates
        if ($user->type === 'TUTOR') {
            Qualifications::deleteTutorSubjects($new_username);
            if (isset($_POST['subjects']) && is_array($_POST['subjects'])) {
                foreach ($_POST['subjects'] as $subject) {
                    if (!empty($subject)) {
                        Qualifications::addTutorSubject($new_username, $subject);
                    }
                }
            }
            Qualifications::deleteTutorLanguages($new_username);
            if (isset($_POST['languages']) && is_array($_POST['languages'])) {
                foreach ($_POST['languages'] as $language) {
                    if (!empty($language)) {
                        Qualifications::addTutorLanguage($new_username, $language);
                    }
                }
            }
        } elseif ($user->type === 'STUDENT') {
            Qualifications::deleteStudentSubjects($new_username);
            if (isset($_POST['subjects']) && is_array($_POST['subjects'])) {
                foreach ($_POST['subjects'] as $subject_grade) {
                    $parts = explode('|', $subject_grade);
                    if (count($parts) === 2 && !empty($parts[0])) {
                        Qualifications::addStudentSubject($new_username, $parts[0], $parts[1]);
                    }
                }
            }
            Qualifications::deleteStudentLanguages($new_username);
            if (isset($_POST['languages']) && is_array($_POST['languages'])) {
                foreach ($_POST['languages'] as $language) {
                    if (!empty($language)) {
                        Qualifications::addStudentLanguage($new_username, $language);
                    }
                }
            }
        }

        // Re-fetch the user object from the database to ensure session is up-to-date
        // This is crucial to load all the latest changes, including the ID if it's based on username.
        $updated_user = User::get_user_by_username($new_username);
        if ($updated_user) {
            $session->login($updated_user); // Re-login with the fresh user object
        }

        if ($user_updated && $profile_updated && $qualifications_updated) {
            $_SESSION['success_message'] = 'Profile updated successfully!';
            header('Location: /profile.php?id=' . urlencode($new_username)); // Redirect with the new username
            exit();
        } else {
            $errors['db'] = 'Failed to update profile. Please try again.';
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
    <link rel="stylesheet" href="/styles/editprofile_style.css">
    <link rel="stylesheet" href="/styles/homepage.css">
</head>
<body>
    <?php if($user->type == 'STUDENT'): ?>
        <body style="background-color: #32533D">
    <?php elseif($user->type == 'TUTOR'): ?>
        <body style="background-color: #03254E">
    <?php endif; ?>
    <main>
        <div id="container" class="container">
        <div class="edit-profile-container">
            <?php if($user->type == 'STUDENT'): ?>
                <h1 style="color: #32533D">Edit Profile</h1>
            <?php elseif($user->type == 'TUTOR'): ?>
                <h1 style="color: #03254E">Edit Profile</h1>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success-message">
                    <p><?= htmlspecialchars($_SESSION['success_message']) ?></p>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
                <div class="undpp">
                <div class="username-email">
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                </div>
                <div class="new-password">
                    <input type="password" id="password" name="password" placeholder="New Password (optional)">
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm New Password (optional)">
                </div>
                <div class="name-dob">
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
                    <input type="date" id="date-of-birth" name="date-of-birth" value="<?= htmlspecialchars($date_of_birth) ?>" required>
                </div>
                <div class="image-upload-container">
                    <div class="upload-area" id="uploadArea">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <img id="image-preview" alt="Preview" style="display:<?= $profile_image ? 'block' : 'none' ?>;" src="<?= $profile_image ? htmlspecialchars((strpos($profile_image, '/') === 0 ? $profile_image : '/uploads/profiles/' . $profile_image)) : '' ?>">
                    </div>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*">
                    <?php if($user->type == 'STUDENT'): ?>
                    <button type="button" class="upload-btnS" onclick="document.getElementById('profile_image').click()">Choose File</button>
                    <?php elseif($user->type == 'TUTOR'): ?>
                    <button type="button" class="upload-btnT" onclick="document.getElementById('profile_image').click()">Choose File</button>
                    <?php endif; ?>
                </div>
                </div>
                <div class="description">
                    <textarea id="description" name="description"><?= htmlspecialchars($description) ?></textarea>
                </div>

                <?php if ($user->type === 'TUTOR'): ?>
                    <div class="form-group">
                        <label>Subjects Taught:</label>
                        <div id="subjects-container">
                            <?php if (!empty($subjects)): ?>
                                <?php foreach ($subjects as $subject): ?>
                                    <div class="subject-entry">
                                        <select name="subjects[]" class="subject-select">
                                            <option value="">Select a subject</option>
                                            <?php foreach (Qualifications::getAllSubjects() as $all_subject): ?>
                                                <option value="<?= htmlspecialchars($all_subject) ?>" <?= ($all_subject === $subject['SUBJECT']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($all_subject) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="remove-btn" onclick="removeSubject(this)">Remove</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="subject-entry">
                                    <select name="subjects[]" class="subject-select">
                                        <option value="">Select a subject</option>
                                        <?php foreach (Qualifications::getAllSubjects() as $subject): ?>
                                            <option value="<?= htmlspecialchars($subject) ?>"><?= htmlspecialchars($subject) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="remove-btn" onclick="removeSubject(this)">Remove</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="add-btn" onclick="addSubject()">Add Subject</button>
                    </div>

                    <div class="form-group">
                        <label>Languages Spoken:</label>
                        <div id="languages-container">
                            <?php if (!empty($languages)): ?>
                                <?php foreach ($languages as $language): ?>
                                    <div class="language-entry">
                                        <select name="languages[]" class="language-select">
                                            <option value="">Select a language</option>
                                            <?php foreach (Qualifications::getAllLanguages() as $all_language): ?>
                                                <option value="<?= htmlspecialchars($all_language) ?>" <?= ($all_language === $language) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($all_language) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="remove-btn" onclick="removeLanguage(this)">Remove</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="language-entry">
                                    <select name="languages[]" class="language-select">
                                        <option value="">Select a language</option>
                                        <?php foreach (Qualifications::getAllLanguages() as $language): ?>
                                            <option value="<?= htmlspecialchars($language) ?>"><?= htmlspecialchars($language) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="remove-btn" onclick="removeLanguage(this)">Remove</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="add-btn" onclick="addLanguage()">Add Language</button>
                    </div>
                <?php elseif ($user->type === 'STUDENT'): ?>
                    <div class="form-group">
                        <label>Subjects of Interest:</label>
                        <div id="subjects-container">
                            <?php if (!empty($subjects)): ?>
                                <?php foreach ($subjects as $subject): ?>
                                    <div class="subject-entry">
                                        <select name="subjects[]" class="subject-select">
                                            <option value="">Select a subject</option>
                                            <?php foreach (Qualifications::getAllSubjects() as $all_subject): ?>
                                                <option value="<?= htmlspecialchars($all_subject) ?>|<?= htmlspecialchars($subject['GRADE']) ?>" <?= ($all_subject === $subject['SUBJECT']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($all_subject) ?> (Grade <?= htmlspecialchars($subject['GRADE']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="remove-btn" onclick="removeSubject(this)">Remove</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="subject-entry">
                                    <select name="subjects[]" class="subject-select">
                                        <option value="">Select a subject</option>
                                        <?php foreach (Qualifications::getAllSubjects() as $subject): ?>
                                            <option value="<?= htmlspecialchars($subject) ?>|"><?= htmlspecialchars($subject) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" name="grades[]" placeholder="Grade (e.g., 10)">
                                    <button type="button" class="remove-btn" onclick="removeSubject(this)">Remove</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="add-btn" onclick="addSubject()">Add Subject</button>
                    </div>

                    <div class="form-group">
                        <label>Languages to Learn:</label>
                        <div id="languages-container">
                            <?php if (!empty($languages)): ?>
                                <?php foreach ($languages as $language): ?>
                                    <div class="language-entry">
                                        <select name="languages[]" class="language-select">
                                            <option value="">Select a language</option>
                                            <?php foreach (Qualifications::getAllLanguages() as $all_language): ?>
                                                <option value="<?= htmlspecialchars($all_language) ?>" <?= ($all_language === $language) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($all_language) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="remove-btn" onclick="removeLanguage(this)">Remove</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="language-entry">
                                    <select name="languages[]" class="language-select">
                                        <option value="">Select a language</option>
                                        <?php foreach (Qualifications::getAllLanguages() as $language): ?>
                                            <option value="<?= htmlspecialchars($language) ?>"><?= htmlspecialchars($language) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="remove-btn" onclick="removeLanguage(this)">Remove</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="add-btn" onclick="addLanguage()">Add Language</button>
                    </div>
                <?php endif; ?>

                <button type="submit" class="submit-btn">Save Changes</button>
            </form>
        </div>
        </div>
    </main>

    <script>
        // ... (your existing JavaScript functions for addSubject, removeSubject, addLanguage, removeLanguage) ...
        function addSubject() {
            const container = document.getElementById('subjects-container');
            const newEntry = document.createElement('div');
            newEntry.className = 'subject-entry';
            newEntry.innerHTML = `
                <select name="subjects[]" class="subject-select">
                    <option value="">Select a subject</option>
                    <?php foreach (Qualifications::getAllSubjects() as $subject): ?>
                        <option value="<?= htmlspecialchars($subject) ?>"><?= htmlspecialchars($subject) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($user->type === 'STUDENT'): ?>
                    <input type="text" name="grades[]" placeholder="Grade (e.g., 10)">
                <?php endif; ?>
                <button type="button" class="remove-btn" onclick="removeSubject(this)">Remove</button>
            `;
            container.appendChild(newEntry);
        }

        function removeSubject(button) {
            const container = document.getElementById('subjects-container');
            if (container.children.length > 1) {
                button.parentElement.remove();
            } else {
                alert('You must have at least one subject');
            }
        }

        function addLanguage() {
            const container = document.getElementById('languages-container');
            const newEntry = document.createElement('div');
            newEntry.className = 'language-entry';
            newEntry.innerHTML = `
                <select name="languages[]" class="language-select">
                    <option value="">Select a language</option>
                    <?php foreach (Qualifications::getAllLanguages() as $language): ?>
                        <option value=\"<?= htmlspecialchars($language) ?>\"><?= htmlspecialchars($language) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type=\"button\" class=\"remove-btn\" onclick=\"removeLanguage(this)\">Remove</button>
            `;
            container.appendChild(newEntry);
        }

        function removeLanguage(button) {
            const container = document.getElementById('languages-container');
            if (container.children.length > 1) {
                button.parentElement.remove();
            } else {
                alert('You must have at least one language');
            }
        }

        function addSubject() {
            const container = document.getElementById('subjects-container');
            const newEntry = document.createElement('div');
            newEntry.className = 'subject-entry';
            newEntry.innerHTML = `
                <select name="subjects[]" class="subject-select">
                    <option value="">Select a subject</option>
                    <?php foreach (Qualifications::getAllSubjects() as $subject): ?>
                        <option value="<?= htmlspecialchars($subject) ?>|"><?= htmlspecialchars($subject) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="grades[]" placeholder="Grade (e.g., 10)">
                <button type="button" class="remove-btn" onclick="removeSubject(this)">Remove</button>
            `;
            container.appendChild(newEntry);
        }
    </script>
</body>
</html>