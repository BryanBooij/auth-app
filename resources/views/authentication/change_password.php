<?php
//session start needed on every page to redirect users that are NOT logged in back to main page
global $conn;
require_once 'connect.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style/stylesheet.css">
    <title>Change Password</title>
</head>
<body>
<center>
    <h1 class="title">Change Password</h1>
    <?php

    if ($message = session('flash.message')) {
        echo '<p>' . $message . '</p>';
    } ?>
    <?php
    if ($error = error('update_error')) {
        echo '<p>' . $error . '</p>';
    } ?>
    <form method="post" action="<?php echo route('change_password_user') ?>">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username"
               value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>"
               readonly>
        <br><br>
        <label for="oldPassword">Old Password:</label><br>
        <input type="password" id="oldPassword" name="old_password"><br><br>
        <?php
        if ($error = error('old_password')) {
            echo '<p>' . $error . '</p>';
        } ?>
        <label for="newPassword">New Password:</label><br>
        <input type="password" id="newPassword" name="new_password"><br><br>
        <?php
        if ($error = error('new_password')) {
            echo '<p>' . $error . '</p>';
        } ?>
        <input type="submit" value="Submit">
    </form>
    <a href="<?php echo route('home'); ?>">
        <button>Back</button>
    </a>
</center>
</body>
</html>
