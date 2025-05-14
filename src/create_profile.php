<?php

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Profile</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class = "container" id="container">
            <div class = "profile-form-container">
                <form action = "../actions/profilecreate.php" method = "POST">
                    <h1>Create Profile</h1>
                    <input type="hidden" name="type" value="profile">
                    <div class="name"><input type="text" name="name" placeholder="Name" required /></div>
                    <div class="enrollment"><input type="text" name="enrollment" placeholder="Institution" required /></div>
                    <div class="course"><input type="text" name="course" placeholder="Course" required /></div>
                    <div class="language"><input type="text" name="language" placeholder="Language" required /></div>
    
</html>



