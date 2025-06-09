<?php
session_start();
header('Content-Type: application/json');

// Iekļauj datubāzes pieslēguma failu
require_once '../db_connect.php';

// Nolasām POST datus (JSON formātā)
$data = json_decode(file_get_contents('php://input'), true);
$index = $data['index'] ?? null;
$quantity = $data['quantity'] ?? null;

// Pārbauda, vai dati ir korekti un vai grozā eksistē šis produkts
if ($index === null || $quantity === null || !isset($_SESSION['cart'][$index]) || $quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Nederīgi dati.']);
    exit();
}

// Atjaunina produkta daudzumu grozā pēc indeksa
$_SESSION['cart'][$index]['quantity'] = $quantity;

try {
    // Saglabā atjaunināto grozu arī datubāzē klienta profilā
    $cartJson = json_encode($_SESSION['cart']);
    $updateStmt = $pdo->prepare('UPDATE clients SET cart = :cart WHERE id = :user_id');
    $updateStmt->execute([
        ':cart' => $cartJson,
        ':user_id' => $_SESSION['user_id']
    ]);

    // Atgriež veiksmīgu atbildi
    echo json_encode(['success' => true, 'message' => 'Daudzums veiksmīgi atjaunināts.']);
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(['success' => false, 'message' => 'Kļūda: ' . $e->getMessage()]);
}
?>
