<?php
session_start(); // Start session

// Unset all session variables
$_SESSION = array();

// Destroy session
session_destroy();

// Redirect to login page in BlogApp folder
header("Location: ../login.php");
exit();
?>
