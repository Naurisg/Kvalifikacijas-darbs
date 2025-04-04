<?php
session_start();
header('Content-Type: application/json');

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
    $db = new PDO('sqlite:../Datubazes/products.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->prepare('SELECT id, nosaukums, cena, bilde FROM products WHERE id = :id');
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $product['size'] = $size;
        $product['quantity'] = $quantity;

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $_SESSION['cart'][] = $product;

        $clientDb = new PDO('sqlite:../Datubazes/client_signup.db'); 
        $clientDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $cartJson = json_encode($_SESSION['cart']);
        $updateStmt = $clientDb->prepare('UPDATE clients SET cart = :cart WHERE id = :user_id');
        $updateStmt->execute([
            ':cart' => $cartJson,
            ':user_id' => $_SESSION['user_id']
        ]);

        echo json_encode(['success' => true, 'message' => 'Produkts pievienots grozam.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Produkts nav atrasts.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Kļūda: ' . $e->getMessage()]);
}
?>
