<?php
session_start();
header('Content-Type: application/json');

require_once '../db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$index = $data['index'] ?? null;
$quantity = $data['quantity'] ?? null;

if ($index === null || $quantity === null || !isset($_SESSION['cart'][$index]) || $quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Nederīgi dati.']);
    exit();
}

$_SESSION['cart'][$index]['quantity'] = $quantity;

try {
    $cartJson = json_encode($_SESSION['cart']);
    $updateStmt = $pdo->prepare('UPDATE clients SET cart = :cart WHERE id = :user_id');
    $updateStmt->execute([
        ':cart' => $cartJson,
        ':user_id' => $_SESSION['user_id']
    ]);

    echo json_encode(['success' => true, 'message' => 'Daudzums veiksmīgi atjaunināts.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Kļūda: ' . $e->getMessage()]);
}
?>
