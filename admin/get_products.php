<?php
header('Content-Type: application/json');
session_start();

try {
    $db = new PDO('sqlite:../Datubazes/products.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $category = $_GET['category'] ?? null;

    if ($category) {
        $stmt = $db->prepare("SELECT * FROM products WHERE kategorija = :category");
        $stmt->execute(['category' => $category]);
    } else {
        $stmt = $db->query("SELECT * FROM products");
    }

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'products' => $products]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
