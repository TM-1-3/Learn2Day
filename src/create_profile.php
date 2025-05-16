<?php

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Profile</title>
        <link rel="stylesheet" href="register_style.css">
    </head>
    <body>
        <div class="container" id="container">
            <div class="profile-form-container">
                <form action="../actions/profilecreate.php" method="POST" enctype="multipart/form-data">
                    <h1>Create Profile</h1>
                    <input type="hidden" name="type" value="profile">
                    
                    <div class="name">
                        <input type="text" name="name" placeholder="Name" required />
                    </div>
                    <div class="enrollment">
                        <input type="text" name="enrollment" placeholder="Institution" required />
                    </div>
                    <div class="course">
                        <input type="text" name="course" placeholder="Course" required />
                    </div>
                    <div class="language">
                        <input type="text" name="language" placeholder="Language" required />
                    </div>
                    <div class="image-upload-container">
                    <div class="upload-area" id="uploadArea">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <span class="upload-text">Click to upload image</span>
                        <img id="image-preview" alt="Preview">
                    </div>
                    <input type="file" id="fileInput" class="upload-input" name="image" accept="image/*" required>
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



