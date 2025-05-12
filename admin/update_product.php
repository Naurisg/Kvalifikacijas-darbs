<?php
session_start();
header('Content-Type: application/json');

// Pārbauda, vai lietotājs ir autorizējies
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Nav autorizācijas"]);
    exit();
}

// Apstrādā POST pieprasījumu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Izveido savienojumu ar datubāzi
        $db = new PDO('sqlite:../Datubazes/products.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Pārbauda obligātos laukus
        $requiredFields = ['id', 'nosaukums', 'apraksts', 'kategorija', 'cena', 'quantity'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Trūkst obligāta lauka: $field");
            }
        }

        // Saglabā ievadītos datus mainīgajos
        $productId = $_POST['id'];
        $nosaukums = $_POST['nosaukums'];
        $apraksts = $_POST['apraksts'];
        $kategorija = $_POST['kategorija']; 
        $cena = $_POST['cena'];
        $quantity = $_POST['quantity'];
        $sizes = isset($_POST['sizes']) ? implode(',', $_POST['sizes']) : null;
        $removedImages = isset($_POST['removedImages']) ? json_decode($_POST['removedImages'], true) : [];

        // Iegūst pašreizējās bildes no datubāzes
        $stmt = $db->prepare("SELECT bilde FROM products WHERE id = :id");
        $stmt->execute([':id' => $productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            throw new Exception("Produkts nav atrasts");
        }

        $currentImages = $product['bilde'] ? explode(',', $product['bilde']) : [];
        
        // Noņem no saraksta bildes, kuras lietotājs ir izdzēsis
        $remainingImages = array_filter($currentImages, function($image) use ($removedImages) {
            return !in_array($image, $removedImages);
        });

        // Apstrādā jaunās augšupielādētās bildes
        $uploadedImages = [];
        $uploadDir = '../images/products/';

        if (!empty($_FILES['bilde']['name'][0])) {
            // Pārbauda, vai mape eksistē, ja ne - izveido
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Apstrādā katru augšupielādēto failu
            foreach ($_FILES['bilde']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['bilde']['error'][$key] === UPLOAD_ERR_OK) {
                    // Pārbauda faila tipu un izmēru
                    $fileType = mime_content_type($tmpName);
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        continue;
                    }

                    // Maksimālais faila izmērs 5MB
                    if ($_FILES['bilde']['size'][$key] > 5 * 1024 * 1024) {
                        continue;
                    }

                    // Ģenerē unikālu faila nosaukumu
                    $fileName = time() . '_' . uniqid() . '.' . pathinfo($_FILES['bilde']['name'][$key], PATHINFO_EXTENSION);
                    $filePath = $uploadDir . $fileName;

                    // Pārvieto failu uz mērķa mapi
                    if (move_uploaded_file($tmpName, $filePath)) {
                        $uploadedImages[] = 'images/products/' . $fileName;
                    }
                }
            }
        }

        // Apvieno palikušās un jaunās bildes
        $allImages = array_merge($remainingImages, $uploadedImages);
        
        // Pārbauda, vai vismaz viena bilde ir palikusi
        if (empty($allImages)) {
            throw new Exception("Produktam ir jābūt vismaz vienai bildē");
        }

        $imagesString = implode(',', $allImages);

        // Atjaunina produkta datus datubāzē
        $sql = "UPDATE products SET 
                nosaukums = :nosaukums, 
                apraksts = :apraksts, 
                kategorija = :kategorija, 
                cena = :cena, 
                quantity = :quantity, 
                sizes = :sizes, 
                bilde = :bilde 
                WHERE id = :id";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nosaukums' => $nosaukums,
            ':apraksts' => $apraksts,
            ':kategorija' => $kategorija,
            ':cena' => $cena,
            ':quantity' => $quantity,
            ':sizes' => $sizes,
            ':bilde' => $imagesString,
            ':id' => $productId
        ]);

        // Dzēš noņemtās bildes no servera
        foreach ($removedImages as $image) {
            $filePath = '../' . $image;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        // Atgriež veiksmīgu atbildi
        echo json_encode([
            "success" => true, 
            "message" => "Produkts veiksmīgi atjaunināts",
            "images" => $allImages
        ]);
    } catch (Exception $e) {
        // Atgriež kļūdas paziņojumu
        echo json_encode([
            "success" => false, 
            "message" => $e->getMessage(),
            "trace" => $e->getTraceAsString()
        ]);
    }
}
?>