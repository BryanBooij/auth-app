<?php

namespace App\Http\Controllers;
require 'vendor/autoload.php';
require base_path('/vendor/autoload.php');
include 'autoload.php';

use App\Http\Requests\ChangePasswordUserRequest;
use App\Services\DatabaseService;
use Framework\Component\View;
use Framework\Http\RedirectResponse;
use Framework\Http\Request;
use Framework\Routing\Controller;
use Framework\Session\Session;
use Google_Client;
use Google_Service_Oauth2;
use OTPHP\TOTP;
use PHPMailer\PHPMailer\PHPMailer;
use Twilio\Rest\Client;


class AuthenticatorController extends Controller
{
    private DatabaseService $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    /**
     * Default view.
     *
     * @param Request $request
     * @return View
     */
    public function login(Request $request): View
    {
        return view('authentication/login');
    }

    public function auth(Request $request): View
    {
        $send_qr_email = $request->get('send_qr_email');
        $email = $request->get('email');

        if ($send_qr_email) {
            if ($email) {
                $is_email_send = $this->send_qr_email_method($email);

                if (!$is_email_send) {
                    return view('authentication/auth', [
                        'message' => 'Login failed using this qr email of iets'
                    ]);
                }

                return view('authentication/auth', [
                    'message' => 'Successful login'
                ]);
            }
        }

        return view('authentication/auth');
    }

    public function auth_redirect(Request $request): View
    {
        return view('authentication/auth_redirect');
    }

    public function change_password(Request $request): View
    {
        return view('authentication/change_password');
    }

    public function change_password_submit(ChangePasswordUserRequest $request): RedirectResponse
    {
        $username = $request->post('username');
        $old_password = $request->post('old_password');
        $new_password = $request->post('new_password');

        $conn = $this->databaseService->conn;

        // Sanitize inputs
        $username = mysqli_real_escape_string($conn, $username);
        $old_password = mysqli_real_escape_string($conn, $old_password);
        $new_password = mysqli_real_escape_string($conn, $new_password);

        // Fetch user from the database
        $sql = "SELECT * FROM user WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $hashed_password = $row['password'];

            // Verify old password
            if (password_verify($old_password, $hashed_password)) {
                // Hash the new password
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the password
                $update_sql = "UPDATE user SET password = '$hashed_new_password' WHERE username = '$username'";
                if (mysqli_query($conn, $update_sql)) {
                    //echo "<div class='message success'>Password updated successfully!</div>";
                    return redirect('change_password')
                        ->with('message', 'Password updated successfully!');
                } else {
                    $_SESSION['error_message'] = "<div class='message error'>Error updating password: " . mysqli_error($conn) . "</div>";
                    return redirect('change_password')
                        ->with_errors([
                            'update_error' => 'Error updating password: ' . mysqli_error($conn)
                        ]);
                }
            } else {
                return redirect('change_password')
                    ->with('message', 'Invalid old password!');
            }
        } else {
            return redirect('change_password')
                ->with('message', 'Invalid username!');
        }
    }

    public function connect(Request $request): View
    {
        return view('authentication/connect');
    }

    public function google(Request $request): View
    {
        return View('authentication/google');
    }

    public function google_logout(Request $request): View
    {
        return view('authentication/google_logout');
    }

    public function home(Request $request): View
    {
        return view('authentication/home');
    }

    public function login_user(Request $request): RedirectResponse
    {
        $usernameOrEmail = $request->post('username_or_email');
        $password = $request->post('password');

        $stmt = $this->databaseService->conn->prepare("SELECT username, password FROM user WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        $stmt->bind_result($username, $hashedPassword);
        $stmt->fetch();
        $stmt->close();
        // Verify the password
        if ($hashedPassword !== null && password_verify($password, $hashedPassword)) {
            session('auth.logged_in', true);
            session('auth.username', $username);

            return redirect('auth_redirect');
        } else {
            return redirect('login')->with_errors([
                'message' => 'Invalid login credentials'
            ]);
        }
    }

    public function auth_code(Request $request): RedirectResponse
    {
        $username = session('auth.username');
        $sql = "SELECT secret, email, qr_scanned FROM user WHERE username=?";
        $stmt = $this->databaseService->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            die("Error executing the query: " . $this->databaseService->conn->error);
        }

        $user = $result->fetch_assoc();
        $user_secret = $user['secret'];
        $email = $user['email'];
        $secret = $user_secret;
        $qr_scanned = $user['qr_scanned'];

//creates from user_secret the correct code
        $otp = TOTP::create($secret);



//checks if user input is correct
        $input_otp = $_POST['otp'];
        $verification_result = $otp->verify($input_otp);
        if ($verification_result) {
            $insertsql = "UPDATE user SET qr_scanned = 1 WHERE username = ?";
            $stmt = $this->databaseService->conn->prepare($insertsql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            echo "OTP verified successfully!";
            $_SESSION['logged_in'] = true;
            $_SESSION['auth'] = true;
            return redirect('home');
        } elseif ($qr_scanned == 1) {
            $_SESSION['error_message'] = 'Invalid Authentication code. Please try again.';
            return redirect('auth');
        } else{
            $_SESSION['error_message'] = 'Invalid Authentication code. Please try again.';
            return redirect('qr_auth');
        }
    }

    public function logout(Request $request): View
    {
        return view('authentication/logout');
    }

    public function qr_auth(Request $request): View
    {
        $username = session('auth.username');
        $sql = "SELECT secret, email FROM user WHERE username=?";
        $stmt = $this->databaseService->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result === false) {
            die("Error executing the query: " . $this->databaseService->conn->error);
        }

        $user = $result->fetch_assoc();

        $user_secret = $user['secret'];
        if ($user_secret == ''){
            function generate_user_secret($length = 16) {
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; // Base32 characters
                $secret = '';
                for ($i = 0; $i < $length; $i++) {
                    $secret .= $characters[rand(0, strlen($characters) - 1)];
                }
                return $secret;
            }
            $user_secret = generate_user_secret();
            $insertSql = "UPDATE user SET secret = ? WHERE username = ?";
            $stmt = $this->databaseService->conn->prepare($insertSql);
            if (!$stmt) {
                die("Error preparing statement: " . $this->databaseService->conn->error);
            }

            if (!$stmt->bind_param("ss", $user_secret, $username)) {
                die("Error binding parameters: " . $stmt->error);
            }
            if ($stmt->execute()) {
                echo "New record created successfully";
            } else {
                echo "Error executing query: " . $stmt->error;
            }

            $stmt->close();
        }
// Create TOTP object with the user's secret
        $otp = TOTP::create($user_secret);
        $otp->setLabel($user['email']);
        $otp->setIssuer('TouchTree');

// Generate QR code URI for the user to scan
        $grCodeUri = $otp->getQrCodeUri(
            'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M',
            '[DATA]'
        );

        $_SESSION['grCodeUri'] = $grCodeUri;
        session()->flash('grCodeUri', $grCodeUri);

        return view('authentication/qr_auth', ['grCodeUri' => $grCodeUri]);
    }

    public function register(Request $request): View
    {
        return view('authentication/register');
    }

    public function generate_user_secret($length = 16)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; // Base32 characters
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $secret;
    }

    public function register_progress(Request $request): RedirectResponse
    {
        $username = session("auth.username");
        $email = session('auth.email');
        $password = session("auth.password");
        $password_repeat = session("auth.password_repeat");

        // Check if passwords match
        if ($password !== $password_repeat) {
            $error_message = "Passwords do not match.";

            return redirect('register')->with_errors([
                'message' => $error_message
            ]);
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $user_secret = $this->generate_user_secret();
        // checks if username exists or not $count will be 1 is user exists
        function usernameExists($username)
        {
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

        if ($this->databaseService->conn->query($sql) === TRUE) {
            session("auth.username", $username);
            session("auth.password", $password);
            session('auth.logged_in', true);
            return redirect('auth_redirect')->with_errors([
                'message' => $error_message
            ]);
        }

        $this->databaseService->conn->close();

        return redirect('register');
    }

    public function send_email(Request $request): RedirectResponse
    {
        function sendEmail($email, $randomPassword, $username): RedirectResponse
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
            return view('authentication/auth');
        }
        return view('authentication/auth');
    }

    public function send_qr_email_method($email): bool
    {
        //fucker
        return 0;
    }

    public function send_qr_email(Request $request): View
    {
        $email = session('auth.username');
        $conn = $this->databaseService->conn;
        require 'config/google.php';
        $username = session('auth.username');
        $mail = new PHPMailer(true);
        $sql = "SELECT secret, email FROM user WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            die("Error executing the query: " . $conn->error);
        }

        $user = $result->fetch_assoc();
        $user_secret = $user['secret'];
        $otp = TOTP::create($user_secret);
        $otp->setLabel($user['email']);
        $otp->setIssuer('TouchTree');

        // create new qr code from user_secret
        $grCodeUri = $otp->getQrCodeUri(
            'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M',
            '[DATA]'
        );

        try {
            $mail_FROM = config("google.username");
            $user_RCPT = $email; //get user email
            // SMTP server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.elasticemail.com';
            $mail->SMTPAuth = true;
            $mail->Username = config('google.username');
            $mail->Password = config('google.password');
            $mail->SMTPSecure = config("google.smtpSecure");
            $mail->Port = config('google.port');

            // Sender and recipient settings
            $mail->setFrom($mail_FROM);
            $mail->addAddress($user_RCPT);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'New qr code';
            $mail->Body = "<p>Requested new qr code<br><br><img src='{$grCodeUri}' alt='QR Code' class='qr_code'><br></p>";

            // Send email + error message if needed
            $mail->send();
            echo 'Email sent successfully.';
            return view('authentication/auth');
        } catch (Exception $e) {
            echo 'Failed to send email. Error: ' . $mail->ErrorInfo;
            return view('authentication/auth');
        }
    }

    public function number(Request $request): View
    {
        return view('authentication/number');
    }

    public function validate_code(Request $request): RedirectResponse
    {
        $phoneNumber = session('auth.phoneNumber');
        if ($request->post('verification_code')) {
            $userVerificationCode = trim($_POST['verification_code']);

            if ($userVerificationCode == session('verification_code')) {
                $this->updateUserPhoneNumber($phoneNumber);
                $_SESSION['auth'] = true;
                session('logged_in', true);
                return redirect('home');
            } else {
                session('error_code', 'Verification code is incorrect');
                return redirect('sms');
            }
        }

        return redirect('authentication/home');
    }

    public function sms(Request $request): View
    {
        // Call the number_validation method
        $this->number_validation($request);

        return view('authentication/sms');
    }


    public function number_validation(Request $request): RedirectResponse
    {
        $accountSid = config("google.smsAccountSid");
        $authToken = config("google.smsAuthToken");
        $twilio = new Client($accountSid, $authToken);

        // Get the phone number and country code from the request
        $number = $request->post('phone');
        $region = $request->post('country');
        $fullNumber = $region . $number;

        if ($this->validatePhoneNumber($fullNumber, $twilio, $request)) {
            session(['auth.phoneNumber' => $fullNumber]);
            return redirect('sms');
        } else {
            session('error_number', 'Invalid number');
            return redirect('number');
        }
    }

    public function validatePhoneNumber($phoneNumber, $twilio, Request $request): Bool
    {
        if (preg_match('/^\+?\d{1,3}\s?\(?\d{1,4}\)?[-.\s]?\d{1,10}$/', $phoneNumber)) {
            $verificationCode = $this->generateVerificationCode();
            session('verification_code', $verificationCode);
            $message = "Your verification code is: $verificationCode";

            try {
                $twilio->messages->create(
                    $phoneNumber,
                    [
                        "body" => $message,
                        "from" => "TouchTree"
                    ]
                );
                echo "<center>Verification code sent successfully to $phoneNumber</center>";
                return 1;
//                return redirect('home');
            } catch (Exception $e) {
                session("message", "An error occurred while sending SMS to $phoneNumber. Please check the number and try again.");
                return 0;
//                return redirect('number');
            }
        } else {
            session("message", "Invalid phone number");
            return 0;
//            return redirect('number');
        }
    }

    public function updateUserPhoneNumber($phoneNumber)
    {
        $username = session('auth.username');
        $stmt = $this->databaseService->conn->prepare("UPDATE user SET number = ? WHERE username = ?");
        $stmt->bind_param("ss", $phoneNumber, $username);
        $stmt->execute();
        $stmt->close();
    }

    private function generateVerificationCode()
    {
        return mt_rand(100000, 999999);
    }
}

