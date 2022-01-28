<?php

session_start();

//unset all of the session variables
$_SESSION = array();

//destory the session
session_destroy();

//redirect to the login page
header('location: login.php');
exit;
?>
