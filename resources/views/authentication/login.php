<?php
// login page
//require 'dependencies.php';
require 'autoload.php';
require 'application.php';
require 'vendor/autoload.php';

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/stylesheet.css">
    <title>loginform</title>

</head>
<body>
<center>
    <form action="<?php echo route('loginUser');?>" method="post">
        <h1>Sign in</h1>
        <img src="img/companyLogo.png" alt="logo" width="400" height="120"><br><BR>
        <div class="username_or_email">
            <label for="username_or_email" class="username_or_email">Username or Email: </label>
            <input type="text" name="username_or_email" id="username_or_email">
        </div>
        <div class="password">
            <label for="password" class="password">Password: </label>
            <input type="password" id="password" name="password">
        </div>
        <div>
            <button type="submit">Login</button>
        </div>
    </form>
    <div class="login">
        <a href="<?php echo route('register');?>"><button>Register</button></a>
        <span class="whitespace"></span>
        <a href="<?php echo route('google');?>"><img src="img/google-logo.png" alt="google-logo" class="google-logo" width="40" height="40"></a>
    </div>
    <?php
    if (error('message')) {
        echo '<p style="color: red;">' . error('message') . '</p>';
    }
    ?>
</center>
</body>
</html>
