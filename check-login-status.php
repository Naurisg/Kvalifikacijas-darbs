<?php
session_start();
error_log(print_r($_SESSION, true)); // Log session data for debugging

$response = array('loggedIn' => false);

if (isset($_SESSION['user_id'])) {
    $response['loggedIn'] = true;
}

echo json_encode($response);
?>
