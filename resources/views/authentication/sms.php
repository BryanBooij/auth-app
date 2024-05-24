<?php
global $conn, $phoneNumber;
include 'connect.php';
$username = session('auth.username');
require_once 'vendor/autoload.php';
use Twilio\Rest\Client;
require 'config/google.php';
require 'dependencies.php';
require 'application.php';
global $fullNumber


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style/stylesheet.css">
    <title>Verification Code</title>
</head>
<body>
<center>
<h2>Enter Verification Code</h2>
<form action="<?php echo route('validate_code');?>" method="post">
    <input type="hidden" name="phone" value="<?php echo htmlspecialchars($fullNumber); ?>"> <!-- needs to be added otherwise the number doesn't get saved and put in the query-->
    <label for="verification_code">Verification Code:</label><br>
    <input type="text" id="verification_code" name="verification_code"><br><br>
    <input type="submit" value="Submit">
</form>

<?php
if (session('error_code')) {
    echo '<center><p style="color: red;">' . session('error_code') . '</center></p>';
}
?>

</center>
</body>
</html>
