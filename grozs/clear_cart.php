<?php
session_start();
header('Content-Type: application/json');

// Iekļauj datubāzes pieslēguma failu
require_once '../db_connect.php';

// Pārbauda, vai lietotājs ir autorizējies (ir user_id sesijā)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Lūdzu, piesakieties, lai notīrītu grozu.']);
    exit();
}

// Notīra grozu sesijā
$_SESSION['cart'] = [];

try {
    // Atjaunina klienta grozu datubāzē (iestata tukšu masīvu)
    $updateStmt = $pdo->prepare('UPDATE clients SET cart = :cart WHERE id = :user_id');
    $updateStmt->execute([
        ':cart' => json_encode([]),
        ':user_id' => $_SESSION['user_id']
    ]);

    // Atgriež veiksmīgu atbildi
    echo json_encode(['success' => true, 'message' => 'Grozs veiksmīgi notīrīts.']);
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(['success' => false, 'message' => 'Kļūda: ' . $e->getMessage()]);
}
?>
