<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "Nav autorizācijas"]);
    } else {
        header("Location: adminlogin.html");
    }
    exit();
}
?>
