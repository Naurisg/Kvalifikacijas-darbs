<?php
header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:../Datubazes/products.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $result = $db->query('SELECT * FROM products');
    $products = $result->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'products' => $products]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Savienojums neizdevās: " . $e->getMessage()]);
}
?>
