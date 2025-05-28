<?php
require_once 'db_connect.php';

try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 4");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'products' => $products]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
