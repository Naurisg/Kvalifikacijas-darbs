<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit();
}

try {
    $db = new PDO('sqlite:../Datubazes/products.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create products table if it doesn't exist
    $db->exec('CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nosaukums TEXT,
        apraksts TEXT,
        bilde TEXT,
        kategorija TEXT,
        cena DECIMAL(10,2),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )');

    $nosaukums = $_POST['nosaukums'];
    $apraksts = $_POST['apraksts'];
    $kategorija = $_POST['kategorija'];
    $cena = $_POST['cena'];

    // Handle file upload
    $target_dir = "../images/products/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file = $_FILES['bilde'];
    $fileName = time() . '_' . basename($file['name']);
    $target_file = $target_dir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        $bilde_path = "images/products/" . $fileName;
        
        $stmt = $db->prepare('INSERT INTO products (nosaukums, apraksts, bilde, kategorija, cena) VALUES (:nosaukums, :apraksts, :bilde, :kategorija, :cena)');
        
        $stmt->execute([
            ':nosaukums' => $nosaukums,
            ':apraksts' => $apraksts,
            ':bilde' => $bilde_path,
            ':kategorija' => $kategorija,
            ':cena' => $cena
        ]);
        
        echo json_encode(["success" => true, "message" => "Product added successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to upload image"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
