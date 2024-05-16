<?php
//session start needed on every page to redirect users that are NOT logged in back to main page
session('logged_in', true);

?>


<html lang="en">
<head>
    <?php echo view('partials.header')->render() ?>

    <title>Authorization</title>
</head>
<body>

<div style="text-align: center;">
    <!-- HTML authentication form -->
    <div class="qr_form">
        <form method="post" action="<?php echo route('auth_code') ?>">
            <label for="otp">Enter 6 digit code:</label><br>
            <input type="text" id="otp" name="otp"><br>
            <input type="submit" value="Submit">
        </form>

        <a href="<?php echo route('auth_redirect'); ?>">
            <button>Back</button>
        </a><br>

        <a href="<?php echo route('send_qr_email') ?>">Send QR Email</a>
    </div>

    <?php

    // error('abc')
    // in controller: view() / redirect()->with_errors(['abc' => 'verkeerd'])

    if (isset($_SESSION['error_message'])) {
        echo '<p style="color: red;">' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }
    ?>
</div>
</body>
</html>
