<?php
// display qr code for authenticator login
global $conn;
require_once 'vendor/autoload.php';
use OTPHP\TOTP;
require_once 'connect.php';
// Prepare SQL query with parameters to check if the user already has a secret
//$sql = "SELECT secret, email FROM user WHERE username=?";
//$stmt = $conn->prepare($sql);
//$stmt->bind_param("s", $username);
//$stmt->execute();
//$result = $stmt->get_result();
//
//if ($result === false) {
//    die("Error executing the query: " . $conn->error);
//}
//
//$user = $result->fetch_assoc();
//
//$user_secret = $user['secret'];
//if ($user_secret == ''){
//    function generate_user_secret($length = 16) {
//        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; // Base32 characters
//        $secret = '';
//        for ($i = 0; $i < $length; $i++) {
//            $secret .= $characters[rand(0, strlen($characters) - 1)];
//        }
//        return $secret;
//    }
//    $user_secret = generate_user_secret();
//    $insertSql = "UPDATE user SET secret = ? WHERE username = ?";
//    $stmt = $conn->prepare($insertSql);
//    if (!$stmt) {
//        die("Error preparing statement: " . $conn->error);
//    }
//
//    if (!$stmt->bind_param("ss", $user_secret, $username)) {
//        die("Error binding parameters: " . $stmt->error);
//    }
//    if ($stmt->execute()) {
//        echo "New record created successfully";
//    } else {
//        echo "Error executing query: " . $stmt->error;
//    }
//
//    $stmt->close();
//}
//// Create TOTP object with the user's secret
//$otp = TOTP::create($user_secret);
//$otp->setLabel($user['email']);
//$otp->setIssuer('TouchTree');
//
//// Generate QR code URI for the user to scan
//$grCodeUri = $otp->getQrCodeUri(
//    'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M',
//    '[DATA]'
//);

// Display the QR code for the user to scan
echo $grCodeUri = $_SESSION['grCodeUri'];
echo "<center><img src='{{ $grCodeUri }}' alt='QR Code' class='qr_code'><br></center>";

// Inform the user that 2FA setup is complete
echo "<center>Scan the QR code above with your authenticator app to complete 2FA setup. <br></center>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve input OTP code
    $input_otp = $_POST['otp'];

    // Verify OTP code
    $verification_result = $otp->verify($input_otp);

    // Check verification result
    if ($verification_result) {
        echo "OTP verified successfully!";
        $_SESSION['logged_in'] = true;
        $_SESSION['auth'] = true;
        $insertsql = "UPDATE user SET qr_scanned = 1 WHERE username = ?";
        $stmt = $conn->prepare($insertsql);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        if (!$stmt->bind_param("s", $username)) {
            die("Error binding parameters: " . $stmt->error);
        }
        if ($stmt->execute()) {
            echo "New record created successfully";
        } else {
            echo "Error executing query: " . $stmt->error;
        }

        $stmt->close();
        redirect('home')->send();
    } else {
        $_SESSION['error_message'] = 'Invalid Authentication code. Please try again.';
    }
}
?>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style/stylesheet.css">
    <title>Authorization</title>
</head>
<body>
<center>
<!-- HTML form -->
    <img src="{{ $grCodeUri }}" alt="QR Code">
    <form method="post" action="<?php echo route('auth_code') ?>">
        <label for="otp">Enter 6-digit code:</label><br>
        <input type="text" id="otp" name="otp"><br>
        <input type="submit" value="Submit">
    </form>
    <a href="<?php echo route('auth_redirect');?>"><button>Back</button></a>
    <?php
    if (isset($_SESSION['error_message'])) {
        echo '<p style="color: red;">' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }
    ?>
</center>
</body>
</html>
