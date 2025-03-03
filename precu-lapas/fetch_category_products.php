<?php
header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:../Datubazes/products.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $category = $_GET['category'] ?? '';
    $stmt = $db->prepare('SELECT * FROM products WHERE kategorija = :category ORDER BY created_at DESC');
    $stmt->execute(['category' => $category]);
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'products' => $products]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
