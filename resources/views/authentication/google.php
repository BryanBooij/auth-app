<?php
//this is the only file outside of the controller as google can be annoying sometimes
global $conn;
require base_path('/vendor/autoload.php');
include 'autoload.php';
include_once 'connect.php';

//google connection
$client = new Google_Client();
$client->setAuthConfig(base_path('/secret/client_secret.json')); // login credentials for Google connection
$client->setRedirectUri('http://localhost/framework/google'); // has to be full url or doesn't work this url has to be registered in google cloud console
$client->addScope(['openid', 'profile', 'email']);

if (isset($_GET['code'])) {
    $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']); // get access token from Google
    if (isset($accessToken['error'])) {
        unset($_GET['code']);
        redirect('login')->send(); //redirect login
        exit();
    }
    $accessToken = $client->getAccessToken(); // get google access token for access to application

    $googleOAuthService = new Google_Service_Oauth2($client);
    $userInfo = $googleOAuthService->userinfo->get();
    $email = $userInfo->getEmail(); // store google email in local variable for database registration
    $name = strtolower($userInfo->getGivenName()); // google users also have a username on their account that is called getGivenName. lowercase string for easier access for users (optional)

    function generateRandomPassword($length = 12) {
        //generate random password for google users this funtion is optional
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        // password length will be 12 this is also optional and can be changed at any time.
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }

    function generate_user_secret($length = 16) {
        // generate user secret this function is necessary for the authenticator app function to work.
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; // Base32 secret
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $secret;
    }

    // create temp password for Google users
    $randomPassword = generateRandomPassword();
    $hashed_password = password_hash($randomPassword, PASSWORD_DEFAULT); // hash password for protection
    // create user_secret for authenticator qr
    $user_secret = generate_user_secret();

    // Check if the email already exists in the database
    $checkUserQuery = "SELECT * FROM user WHERE email = ?";
    $checkStmt = $conn->prepare($checkUserQuery);
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $userEmail = $email;
    $password = $hashed_password;
    $secret = $user_secret;

    // checks if user already exists if so redirect with correct tokens
    if ($checkResult->num_rows > 0) {
        session('auth.access_token', $accessToken);
        session('auth.logged_in', true);
        session('auth.username', $userEmail);
        session('auth.password', $password);
        redirect('auth_redirect')->send();
        exit();
    } else {
        // user doesn't exist create new user
        // Insert the user into the database
        $insertSql = "INSERT INTO user (username, display_username, email, password, secret) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        if (!$stmt->bind_param("sssss", $userEmail, $name, $userEmail, $password, $secret)) {
            die("Error binding parameters: " . $stmt->error);
        }

        if ($stmt->execute()) {
            echo "New record created successfully";
        } else {
            echo "Error executing query: " . $stmt->error;
        }

        $stmt->close();
    }
    // close database connection
    $conn->close();
    session('auth.access_token', $accessToken);
    session('auth.logged_in', true);
    session('auth.username', $userEmail);
    session('auth.password', $password);
    //send_email() this function is optional
    redirect('auth_redirect')->send();
    session('auth.logged_in', true);
} else {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit();
}