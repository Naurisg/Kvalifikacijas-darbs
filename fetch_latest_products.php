<?php
require_once 'db_connect.php';

// Atlasa 4 jaunākos produktus no datubāzes
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 4");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Atgriež produktus JSON formātā
    echo json_encode(['success' => true, 'products' => $products]);
} catch(PDOException $e) {
    // Apstrādā kļūdu un atgriež kļūdas ziņu JSON formātā
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
