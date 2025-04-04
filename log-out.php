<?php
session_start();
session_unset(); // Clear all session variables
session_destroy(); // Destroy the session
setcookie(session_name(), '', time() - 3600, '/'); // Clear the session cookie
header('Location: index.php'); // Redirect to the homepage
exit();
?>
