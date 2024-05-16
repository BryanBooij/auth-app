<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style/stylesheet.css">
    <title>Register</title>
</head>
<body>
<center>
    <h2>Register</h2>
    <?php
    // Check if there's an error message in the URL parameters
    if(isset($_GET['error'])) {
        $error_message = $_GET['error'];
        // Display the error message to the user
        echo '<p style="color: red;">' . htmlspecialchars($error_message) . "</p>";
    }
    ?>
    <form action="<?php echo route('register_progress');?>" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="password_repeat">Repeat Password:</label>
        <input type="password" id="password_repeat" name="password_repeat" required><br>

        <input type="submit" value="Register">
    </form>
    <a href="<?php echo route('login');?>"><button>Back</button></a>
</center>
</body>
</html>
