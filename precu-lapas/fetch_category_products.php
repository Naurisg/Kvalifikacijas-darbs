<?php
header('Content-Type: application/json');

// Iekļauj datubāzes pieslēguma failu
require_once '../db_connect.php';

try {
    // Iegūst kategoriju no GET parametriem
    $category = $_GET['category'] ?? '';
    // Atlasa visus produktus pēc kategorijas, sakārtojot pēc izveides datuma (jaunākie augšā)
    $stmt = $pdo->prepare('SELECT * FROM products WHERE kategorija = :category ORDER BY created_at DESC');
    $stmt->execute(['category' => $category]);
    
    // Iegūst visus produktus masīvā
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Atgriež produktu sarakstu JSON formātā
    echo json_encode(['success' => true, 'products' => $products]);
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
