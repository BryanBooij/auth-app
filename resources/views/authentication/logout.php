<?php
// logout user
//session_start();

$_SESSION = array();

session_destroy();

redirect('login')->send();
exit;
