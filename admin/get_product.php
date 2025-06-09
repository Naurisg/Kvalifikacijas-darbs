<?php
// Uzstāda admin sesijas nosaukumu un sāk sesiju
session_name('admin_session');
session_start();
header('Content-Type: application/json');

// Pārbauda, vai ir norādīts produkta ID GET parametros
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit();
}

$product_id = $_GET['id'];

try {
    // Iekļauj datubāzes savienojumu no db_connect.php
    require_once '../db_connect.php';

    // Sagatavo vaicājumu, lai iegūtu produktu pēc ID
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Ja produkts atrasts, atgriež to JSON formātā
    if ($product) {
        echo json_encode(['success' => true, 'product' => $product]);
    } else {
        // Ja produkts nav atrasts
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
