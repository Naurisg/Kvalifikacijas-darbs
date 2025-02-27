<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Nav autorizācijas"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $db = new PDO('sqlite:../Datubazes/products.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $productId = $_POST['id'];
        $nosaukums = $_POST['nosaukums'];
        $apraksts = $_POST['apraksts'];
        $kategorija = $_POST['kategorija'];
        $cena = $_POST['cena'];

        // Handle image upload if new image is provided
        if (isset($_FILES['bilde']) && $_FILES['bilde']['error'] === 0) {
            $fileName = time() . '_' . $_FILES['bilde']['name'];
            $uploadDir = '../uploads/';
            $filePath = $uploadDir . $fileName;

            // Get old image to delete
            $stmt = $db->prepare("SELECT bilde FROM products WHERE id = :id");
            $stmt->execute([':id' => $productId]);
            $oldProduct = $stmt->fetch(PDO::FETCH_ASSOC);

            if (move_uploaded_file($_FILES['bilde']['tmp_name'], $filePath)) {
                // Delete old image
                if ($oldProduct && $oldProduct['bilde']) {
                    $oldImagePath = $uploadDir . $oldProduct['bilde'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $sql = "UPDATE products SET nosaukums = :nosaukums, apraksts = :apraksts, 
                        kategorija = :kategorija, cena = :cena, bilde = :bilde 
                        WHERE id = :id";
                $params = [
                    ':nosaukums' => $nosaukums,
                    ':apraksts' => $apraksts,
                    ':kategorija' => $kategorija,
                    ':cena' => $cena,
                    ':bilde' => $fileName,
                    ':id' => $productId
                ];
            }
        } else {
            $sql = "UPDATE products SET nosaukums = :nosaukums, apraksts = :apraksts, 
                    kategorija = :kategorija, cena = :cena WHERE id = :id";
            $params = [
                ':nosaukums' => $nosaukums,
                ':apraksts' => $apraksts,
                ':kategorija' => $kategorija,
                ':cena' => $cena,
                ':id' => $productId
            ];
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        echo json_encode(["success" => true, "message" => "Produkts veiksmīgi atjaunināts"]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>
