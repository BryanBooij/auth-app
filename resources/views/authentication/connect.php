<?php
//connection to local database change to online database if needed
require 'config/google.php';
require 'application.php';
// database login information for connection

$servername = config("google.servername");
$Gusername = config("google.usernamelocalhost");
$Gpassword = config("google.passwordlocalhost");
$database = config("google.database");

$conn = new mysqli($servername, $Gusername, $Gpassword, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

