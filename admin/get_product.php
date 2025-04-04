<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Nav autorizācijas"]);
    exit();
}

if (isset($_GET['id'])) {
    try {
        $db = new PDO('sqlite:../Datubazes/products.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $_GET['id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Ensure the category value matches the dropdown in productedit.html
        echo json_encode(["success" => true, "product" => $product]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>
