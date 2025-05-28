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
        // Iekļauj datubāzes savienojumu no db_connect.php
        require_once '../db_connect.php';

        // Get the image filename before deleting the product
        $stmt = $pdo->prepare("SELECT bilde FROM products WHERE id = :id");
        $stmt->execute([':id' => $productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete the product from database
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $productId]);

        // Delete the associated image files if they exist
        if ($product && $product['bilde']) {
            $images = explode(',', $product['bilde']);
            foreach ($images as $image) {
                $imagePath = '../' . $image;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }

        echo json_encode(["success" => true, "message" => "Produkts veiksmīgi dzēsts"]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>
