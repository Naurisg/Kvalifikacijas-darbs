<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Nav autorizācijas"]);
    exit();
}

try {
    $db = new PDO('sqlite:../Datubazes/products.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create products table if it doesn't exist
    $db->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nosaukums TEXT NOT NULL,
        apraksts TEXT,
        bilde TEXT,
        kategorija TEXT,
        cena DECIMAL(10,2),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Handle file upload
    $uploadDir = '../uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = '';
    if (isset($_FILES['bilde']) && $_FILES['bilde']['error'] === 0) {
        $fileName = time() . '_' . $_FILES['bilde']['name'];
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['bilde']['tmp_name'], $filePath)) {
            $fileName = $fileName;
        } else {
            throw new Exception("Failed to upload file");
        }
    }

    $sql = "INSERT INTO products (nosaukums, apraksts, bilde, kategorija, cena) 
            VALUES (:nosaukums, :apraksts, :bilde, :kategorija, :cena)";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':nosaukums' => $_POST['nosaukums'],
        ':apraksts' => $_POST['apraksts'],
        ':bilde' => $fileName,
        ':kategorija' => $_POST['kategorija'],
        ':cena' => $_POST['cena']
    ]);

    echo json_encode(["success" => true, "message" => "Produkts veiksmīgi pievienots"]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
