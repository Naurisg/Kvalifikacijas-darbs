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
        $quantity = $_POST['quantity'];
        $sizes = isset($_POST['sizes']) ? implode(',', $_POST['sizes']) : null;

        // Validate required fields
        if (empty($productId) || empty($nosaukums) || empty($apraksts) || empty($kategorija) || empty($cena) || empty($quantity)) {
            throw new Exception("Visi lauki ir obligāti jāaizpilda.");
        }

        // Handle image upload if new image is provided
        if (isset($_FILES['bilde']) && $_FILES['bilde']['error'] === 0) {
            $fileName = time() . '_' . basename($_FILES['bilde']['name']);
            $uploadDir = '../images/products/';
            $filePath = $uploadDir . $fileName;

            // Ensure the upload directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Get old image to delete
            $stmt = $db->prepare("SELECT bilde FROM products WHERE id = :id");
            $stmt->execute([':id' => $productId]);
            $oldProduct = $stmt->fetch(PDO::FETCH_ASSOC);

            if (move_uploaded_file($_FILES['bilde']['tmp_name'], $filePath)) {
                // Delete old image
                if ($oldProduct && $oldProduct['bilde']) {
                    $oldImagePath = '../' . $oldProduct['bilde'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $sql = "UPDATE products SET nosaukums = :nosaukums, apraksts = :apraksts, 
                        kategorija = :kategorija, cena = :cena, quantity = :quantity, sizes = :sizes, bilde = :bilde 
                        WHERE id = :id";
                $params = [
                    ':nosaukums' => $nosaukums,
                    ':apraksts' => $apraksts,
                    ':kategorija' => $kategorija,
                    ':cena' => $cena,
                    ':quantity' => $quantity,
                    ':sizes' => $sizes,
                    ':bilde' => 'images/products/' . $fileName,
                    ':id' => $productId
                ];
            } else {
                throw new Exception("Neizdevās augšupielādēt jauno attēlu.");
            }
        } else {
            $sql = "UPDATE products SET nosaukums = :nosaukums, apraksts = :apraksts, 
                    kategorija = :kategorija, cena = :cena, quantity = :quantity, sizes = :sizes WHERE id = :id";
            $params = [
                ':nosaukums' => $nosaukums,
                ':apraksts' => $apraksts,
                ':kategorija' => $kategorija,
                ':cena' => $cena,
                ':quantity' => $quantity,
                ':sizes' => $sizes,
                ':id' => $productId
            ];
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        echo json_encode(["success" => true, "message" => "Produkts veiksmīgi atjaunināts"]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>
