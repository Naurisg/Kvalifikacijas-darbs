<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Nav autorizācijas"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productId = $_POST['id'] ?? null;

    if (!$productId) {
        echo json_encode(["success" => false, "message" => "Nav norādīts produkta ID"]);
        exit();
    }

    try {
        $db = new PDO('sqlite:../Datubazes/products.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Get the image filename before deleting the product
        $stmt = $db->prepare("SELECT bilde FROM products WHERE id = :id");
        $stmt->execute([':id' => $productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete the product from database
        $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $productId]);

        // Delete the associated image file if it exists
        if ($product && $product['bilde']) {
            $imagePath = "../uploads/" . $product['bilde'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        echo json_encode(["success" => true, "message" => "Produkts veiksmīgi dzēsts"]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>
