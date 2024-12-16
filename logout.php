<?php
session_start(); // Start the session

// Unset all session variables
if (isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']); // Unset user_id
}

if (isset($_SESSION['username'])) {
    unset($_SESSION['username']); // Unset username
}

// Destroy the session completely
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
