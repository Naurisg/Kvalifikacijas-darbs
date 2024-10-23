<?php
session_start();

// Izbeidz sesiju
session_unset(); // Notīrīt sesijas mainīgos
session_destroy(); // Iznicina sesiju

// Pārvirza uz admin login lapu
header("Location: adminlogin.html");
exit();
?>
