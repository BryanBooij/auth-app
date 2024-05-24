<?php
global $conn;
require_once 'connect.php';

$qr = session('qr_scanned');
$number = session('number');

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="public/css/stylesheet.css">
    <title>Authenticator redirects</title>
</head>
<body>
<center>
    <h1 class="title">Authenticator</h1>
    <div class="redirect">
        <img src="img/auth-logo.png" alt="auth-logo" width="150" height="150"><br><br>
        <div class="redirect_links">
            <?php
            // checks if qr is scanned or not, based on result show a different button
            if ($qr == 0) {
                echo '<a href="' . route('qr_auth') . '"><button class="btn btn-primary d-inline-flex align-items-center" type="button">I havent connected my Authenticator yet</button></a><br>';
            } else {
                echo '<a href="' . route('auth') . '"><button class="btn btn-outline-secondary d-inline-flex align-items-center" type="button">i have connected Authenticator to this account</button></a>';
            }
            // checks if user already has a registered number
            if ($number == '') {
                echo '<br><a href="' . route('number') . '"><button class="btn btn-primary d-inline-flex align-items-center" type="button">input number</button></a><br>';
            } else {
                echo '<br><a href="' . route('sms') . '"><button class="btn btn-primary d-inline-flex align-items-center" type="button">send sms code</button></a><br>';
            }

            ?>
        </div>
        <img src="img/auth-logo-google.png" alt="auth-logo" width="150" height="150">
    </div>
</center>
</body>
</html>
