<?php
session_start();
header('Content-Type: application/json');

require_once '../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Lūdzu, piesakieties, lai pievienotu produktus grozam.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['id'] ?? null;
$size = $data['size'] ?? 'Nav norādīts'; // Default to 'Nav norādīts'
$quantity = $data['quantity'] ?? 1; // Default to 1

if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'Produkts nav norādīts.']);
    exit();
}

try {
    // Use $pdo from db_connect.php instead of creating a new PDO instance

    $product = [
        'id' => $productId,
        'size' => $size,
        'quantity' => $quantity
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][] = $product;

    // Remove the inner try/catch and use $pdo for the update
    $cartJson = json_encode($_SESSION['cart']);
    $updateStmt = $pdo->prepare('UPDATE clients SET cart = :cart WHERE id = :user_id');
    $updateStmt->execute([
        ':cart' => $cartJson,
        ':user_id' => $_SESSION['user_id']
    ]);

    echo json_encode(['success' => true, 'message' => 'Produkts pievienots grozam.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Kļūda: ' . $e->getMessage()]);
}
?>
