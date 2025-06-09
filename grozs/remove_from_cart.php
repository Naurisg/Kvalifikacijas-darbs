<?php
session_start();
header('Content-Type: application/json');

// Iekļauj datubāzes pieslēguma failu
require_once '../db_connect.php';

// Nolasām POST datus (JSON formātā)
$data = json_decode(file_get_contents('php://input'), true);
$index = $data['index'] ?? null;

// Pārbauda, vai indekss ir norādīts un vai grozā eksistē šis produkts
if ($index === null || !isset($_SESSION['cart'][$index])) {
    echo json_encode(['success' => false, 'message' => 'Produkts nav atrasts grozā.']);
    exit();
}

// Noņem produktu no groza pēc indeksa
unset($_SESSION['cart'][$index]);
// Pārsakārto masīvu, lai nebūtu tukšu indeksu
$_SESSION['cart'] = array_values($_SESSION['cart']);

try {
    // Saglabā atjaunināto grozu arī datubāzē klienta profilā
    $cartJson = json_encode($_SESSION['cart']);
    $updateStmt = $pdo->prepare('UPDATE clients SET cart = :cart WHERE id = :user_id');
    $updateStmt->execute([
        ':cart' => $cartJson,
        ':user_id' => $_SESSION['user_id']
    ]);

    // Atgriež veiksmīgu atbildi
    echo json_encode(['success' => true, 'message' => 'Produkts veiksmīgi noņemts no groza.']);
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(['success' => false, 'message' => 'Kļūda: ' . $e->getMessage()]);
}
?>
