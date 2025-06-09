<?php
// Uzstāda admin sesijas nosaukumu un sāk sesiju
session_name('admin_session');
session_start();

// Pārbauda, vai lietotājs ir ielogojies (vai ir user_id sesijā)
if (!isset($_SESSION['user_id'])) {
    // Ja pieprasījums ir AJAX (XMLHttpRequest), atgriež JSON ar kļūdu
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "Nav autorizācijas"]);
    } else {
        // Ja nav AJAX, pāradresē uz admina login lapu
        header("Location: adminlogin.html");
    }
    exit();
}
?>
