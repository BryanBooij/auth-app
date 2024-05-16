<?php
use PHPMailer\PHPMailer\PHPMailer;

//Load Composer's autoloader
require 'vendor/autoload.php';
require 'config/google.php';
require 'dependencies.php';
require 'application.php';

// send email function for Google login users
function sendEmail($email, $randomPassword, $username)
{
    global $config;
    $mail = new PHPMailer(true);

    try {
        $mail_FROM = 'bryan@touchtree.tech';
        $user_RCPT = $email;
        $user_password = $randomPassword;
        $user_name = $email;
        $display_name = $username;

        // SMTP server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.elasticemail.com';
        $mail->SMTPAuth = true;
        $mail->Username = config("google.username");
        $mail->Password = config("google.password");
        $mail->SMTPSecure = config("google.smtpSecure");
        $mail->Port = config("google.port");

        // Sender and recipient settings
        $mail->setFrom($mail_FROM);
        $mail->addAddress($user_RCPT);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Successful login';
        $mail->Body = "<p>Successful login. <br> Your username is: " . $user_name . " <br> Your display name is: " . $display_name . " <br> Your password is: " . $user_password . " <br> you can now change you're password</p>";

        // Send email + error message if needed
        $mail->send();
        echo 'Email sent successfully.';
    } catch (Exception $e) {
        echo 'Failed to send email. Error: ' . $mail->ErrorInfo;
    }
    return 0;
}
