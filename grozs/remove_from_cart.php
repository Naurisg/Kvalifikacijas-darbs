<?php
session_start();
header('Content-Type: application/json');

require_once '../db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$index = $data['index'] ?? null;

if ($index === null || !isset($_SESSION['cart'][$index])) {
    echo json_encode(['success' => false, 'message' => 'Produkts nav atrasts grozā.']);
    exit();
}

unset($_SESSION['cart'][$index]);
$_SESSION['cart'] = array_values($_SESSION['cart']);

try {
    // Use $pdo from db_connect.php instead of creating a new PDO instance
    $cartJson = json_encode($_SESSION['cart']);
    $updateStmt = $pdo->prepare('UPDATE clients SET cart = :cart WHERE id = :user_id');
    $updateStmt->execute([
        ':cart' => $cartJson,
        ':user_id' => $_SESSION['user_id']
    ]);

    echo json_encode(['success' => true, 'message' => 'Produkts veiksmīgi noņemts no groza.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Kļūda: ' . $e->getMessage()]);
}
?>
