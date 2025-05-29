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
    // Pārbauda, vai produkts pastāv un iegūst tā pieejamo daudzumu
    $stmt = $pdo->prepare('SELECT quantity FROM products WHERE id = :id');
    $stmt->execute([':id' => $productId]);
    $productRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$productRow) {
        echo json_encode(['success' => false, 'message' => 'Produkts nav atrasts.']);
        exit();
    }

    $availableStock = (int)$productRow['quantity'];

    // Saskaita, cik daudz šis produkts jau ir grozā
    $alreadyInCart = 0;
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    foreach ($_SESSION['cart'] as $cartItem) {
        if ($cartItem['id'] == $productId) {
            $alreadyInCart += (int)($cartItem['quantity'] ?? 1);
        }
    }

    // Pārbauda, vai pieprasītais daudzums nepārsniedz pieejamo daudzumu
    if ($alreadyInCart + $quantity > $availableStock) {
        echo json_encode([
            'success' => false,
            'message' => 'Nav pietiekami daudz preču noliktavā. Maksimālais pieejamais daudzums: ' . max(0, $availableStock - $alreadyInCart)
        ]);
        exit();
    }

    $product = [
        'id' => $productId,
        'size' => $size,
        'quantity' => $quantity
    ];

    $_SESSION['cart'][] = $product;

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
