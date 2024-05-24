<?php
// logs out google user
$access_token=$_SESSION['access_token'];

// unset tokens to properly logout user
unset($_SESSION['access_token']);
unset($_SESSION['userData']);

$client = new Google_Client();

// revoke google token
$client->revokeToken($access_token);

// destroy session
session_destroy();
redirect('login')->send();