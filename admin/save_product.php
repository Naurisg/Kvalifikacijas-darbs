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
    
    // Add missing columns if they don't exist
    $db->exec('PRAGMA foreign_keys = OFF;'); // Disable foreign key constraints temporarily
    $db->exec('CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nosaukums TEXT,
        apraksts TEXT,
        bilde TEXT,
        kategorija TEXT,
        cena DECIMAL(10,2),
        quantity INTEGER DEFAULT 0,
        sizes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )');
    // Check if the `quantity` column exists, and add it if it doesn't
    $columns = $db->query("PRAGMA table_info(products)")->fetchAll(PDO::FETCH_COLUMN, 1);
    if (!in_array('quantity', $columns)) {
        $db->exec('ALTER TABLE products ADD COLUMN quantity INTEGER DEFAULT 0');
    }
    if (!in_array('sizes', $columns)) {
        $db->exec('ALTER TABLE products ADD COLUMN sizes TEXT');
    }

    $nosaukums = $_POST['nosaukums'];
    $apraksts = $_POST['apraksts'];
    $kategorija = $_POST['kategorija'];
    $cena = $_POST['cena'];
    $quantity = $_POST['quantity'];
    $sizes = isset($_POST['sizes']) ? implode(',', $_POST['sizes']) : null;

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
        
        $stmt = $db->prepare('INSERT INTO products (nosaukums, apraksts, bilde, kategorija, cena, quantity, sizes) VALUES (:nosaukums, :apraksts, :bilde, :kategorija, :cena, :quantity, :sizes)');
        
        $stmt->execute([
            ':nosaukums' => $nosaukums,
            ':apraksts' => $apraksts,
            ':bilde' => $bilde_path,
            ':kategorija' => $kategorija,
            ':cena' => $cena,
            ':quantity' => $quantity,
            ':sizes' => $sizes
        ]);
        
        echo json_encode(["success" => true, "message" => "Product added successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to upload image"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
