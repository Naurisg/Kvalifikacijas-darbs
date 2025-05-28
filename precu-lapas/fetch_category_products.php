<?php
header('Content-Type: application/json');

require_once '../db_connect.php';

try {
    $category = $_GET['category'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM products WHERE kategorija = :category ORDER BY created_at DESC');
    $stmt->execute(['category' => $category]);
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'products' => $products]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
