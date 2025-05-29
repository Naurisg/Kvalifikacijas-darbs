<?php
session_start();
session_unset(); // Iztīra visas sesijas
session_destroy(); // Nodzēš sesiju
setcookie(session_name(), '', time() - 3600, '/'); // Clear the session cookie
header('Location: index'); // Aizmet uz sākuma lapu
exit();
?>
