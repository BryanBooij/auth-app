<?php
require_once 'connect.php';
global $conn;
function generate_user_secret($length = 16) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; // Base32 characters
    $secret = '';
    for ($i = 0; $i < $length; $i++) {
        $secret .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $secret;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $password_repeat = $_POST["password_repeat"];

    // Check if passwords match
    if ($password !== $password_repeat) {
        $error_message = "Passwords do not match.";
        header("Location: register.php?error=" . urlencode($error_message));
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $user_secret = generate_user_secret();
    // checks if username exists or not $count will be 1 is user exists
    function usernameExists($username) {
        global $conn;
        $stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return $count > 0;
    }
    $usernameExists = usernameExists($username);

    if ($usernameExists) {
        $error_message = "Username already exists. Use a different one";
    } else {
        // Insert the new user into the database
        $sql = "INSERT INTO user (username, display_username, email, password, secret) VALUES ('$username', '$username', '$email',  '$hashed_password', '$user_secret')";
        $error_message = "Email already exists. Use a different one";
    }

    if ($conn->query($sql) === TRUE) {
        $_SESSION['logged_in'] = true;
        session('auth.username', $username);
        session('auth.password', $password);
        redirect('auth_redirect')->send();
    } else {
        redirect('register');
    }
}

$conn->close();