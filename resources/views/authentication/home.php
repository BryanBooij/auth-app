<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style/stylesheet.css">
    <title>Home</title>
</head>
<body>
<center>
<h1 class="title">Successfully logged in!</h1>
<a href="<?php echo route('change_password');?>"><button>Change password</button></a><br>
<?php
// if statements to see which access tokens to logout
if (isset($_SESSION['access_token'])) {
    echo '<a href="' . route('google_logout') . '"><button>Logout</button></a>';
} else {
    echo '<a href="' . route('logout') . '"><button>Logout</button></a>';
}
?>
</center>
</body>
</html>
