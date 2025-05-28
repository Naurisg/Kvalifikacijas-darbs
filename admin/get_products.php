<?php
header('Content-Type: application/json');
session_start();

require_once '../db_connect.php';

try {
    $category = $_GET['category'] ?? null;

    if ($category) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE kategorija = :category");
        $stmt->execute(['category' => $category]);
    } else {
        $stmt = $pdo->query("SELECT * FROM products");
    }

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'products' => $products]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
