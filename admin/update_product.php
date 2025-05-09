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

        // Pārbauda obligātos laukus
        if (empty($productId) || empty($nosaukums) || empty($apraksts) || empty($kategorija) || empty($cena) || empty($quantity)) {
            throw new Exception("Visi lauki ir obligāti jāaizpilda.");
        }

        // Iegūst pašreizējos attēlus no datubāzes
        $stmt = $db->prepare("SELECT bilde FROM products WHERE id = :id");
        $stmt->execute([':id' => $productId]);
        $oldProduct = $stmt->fetch(PDO::FETCH_ASSOC);
        $currentImages = $oldProduct ? explode(',', $oldProduct['bilde']) : [];

        // Apstrādā attēlu augšupielādi, ja ir pievienoti jauni attēli
        $uploadedImages = [];
        $uploadDir = '../images/products/';

        if (isset($_FILES['bilde']) && !empty($_FILES['bilde']['name'][0])) {
            // Pārliecinās, ka augšupielādes direktorija pastāv
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['bilde']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['bilde']['error'][$key] === 0) {
                    $fileName = time() . '_' . basename($_FILES['bilde']['name'][$key]);
                    $filePath = $uploadDir . $fileName;

                    if (move_uploaded_file($tmp_name, $filePath)) {
                        $uploadedImages[] = 'images/products/' . $fileName;
                    }
                }
            }
        }

        // Apvieno esošos attēlus ar jaunajiem (vai izmanto tikai jaunos, ja tiek aizstāti)
        $allImages = !empty($uploadedImages) ? $uploadedImages : $currentImages;
        
        // $allImages = !empty($uploadedImages) ? $uploadedImages : $currentImages;
        
        $imagesString = implode(',', $allImages);

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
            ':bilde' => $imagesString,
            ':id' => $productId
        ];

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        echo json_encode(["success" => true, "message" => "Produkts veiksmīgi atjaunināts"]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>