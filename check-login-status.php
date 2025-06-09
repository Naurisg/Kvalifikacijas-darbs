<?php
require_once 'session_helper.php';
// Uzsāk drošu sesiju
secure_session_start();

// Noklusējuma atbilde - lietotājs nav ielogojies
$response = array('loggedIn' => false);

// Pārbauda, vai sesijā ir user_id
if (isset($_SESSION['user_id'])) {
    // Pārbauda, vai lietotājs eksistē datubāzē
    global $pdo;
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare('SELECT id FROM clients WHERE id = :id');
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        // Lietotājs eksistē - atzīmē kā ielogojies
        $response['loggedIn'] = true;
    } else {
        // Ja lietotājs nav atrasts, iznīcina sesiju
        $_SESSION = array();
        session_destroy();
    }
}

// Atgriež rezultātu JSON formātā
echo json_encode($response);
?>