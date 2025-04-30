<?php
header('Content-Type: application/json');
session_start();

// Pārbauda, vai lietotājs ir autorizēts
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit();
}

try {
    // Izveido savienojumu ar datubāzi
    $db = new PDO('sqlite:../Datubazes/products.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Pievieno trūkstošās kolonnas, ja tās neeksistē
    $db->exec('PRAGMA foreign_keys = OFF;'); // Uz laiku atslēdz ārējo atslēgu ierobežojumus
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
    // Pārbauda, vai eksistē kolonna `quantity`, ja ne, pievieno to
    $columns = $db->query("PRAGMA table_info(products)")->fetchAll(PDO::FETCH_COLUMN, 1);
    if (!in_array('quantity', $columns)) {
        $db->exec('ALTER TABLE products ADD COLUMN quantity INTEGER DEFAULT 0');
    }
    if (!in_array('sizes', $columns)) {
        $db->exec('ALTER TABLE products ADD COLUMN sizes TEXT');
    }

    // Iegūst datus no POST pieprasījuma
    $nosaukums = $_POST['nosaukums'];
    $apraksts = $_POST['apraksts'];
    $kategorija = $_POST['kategorija']; // Pārliecinās, ka tas atbilst "KrasosanasApgerbs"
    $cena = $_POST['cena'];
    $quantity = $_POST['quantity'];
    $sizes = isset($_POST['sizes']) ? implode(',', $_POST['sizes']) : null;

    // Apstrādā attēlu augšupielādi
    $target_dir = "../images/products/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true); // Izveido direktoriju, ja tas neeksistē
    }
    
    $file = $_FILES['bilde'];
    $image_paths = [];

    // Apstrādā vairāku attēlu augšupielādi
    if (is_array($file['name'])) {
        for ($i = 0; $i < count($file['name']); $i++) {
            if ($file['error'][$i] === UPLOAD_ERR_OK) {
                $fileName = time() . '_' . uniqid() . '_' . basename($file['name'][$i]);
                $target_file = $target_dir . $fileName;
                if (move_uploaded_file($file['tmp_name'][$i], $target_file)) {
                    $image_paths[] = "images/products/" . $fileName; // Saglabā augšupielādētā attēla ceļu
                }
            }
        }
    } else {
        // Rezerves variants vienam failam
        if ($file['error'] === UPLOAD_ERR_OK) {
            $fileName = time() . '_' . uniqid() . '_' . basename($file['name']);
            $target_file = $target_dir . $fileName;
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $image_paths[] = "images/products/" . $fileName;
            }
        }
    }

    if (count($image_paths) > 0) {
        // Saglabā visus attēlu ceļus kā komatu atdalītu virkni
        $bilde_path = implode(',', $image_paths);

        // Sagatavo SQL pieprasījumu produkta pievienošanai
        $stmt = $db->prepare('INSERT INTO products (nosaukums, apraksts, bilde, kategorija, cena, quantity, sizes) VALUES (:nosaukums, :apraksts, :bilde, :kategorija, :cena, :quantity, :sizes)');

        // Izpilda pieprasījumu ar datiem
        $stmt->execute([
            ':nosaukums' => $nosaukums,
            ':apraksts' => $apraksts,
            ':bilde' => $bilde_path,
            ':kategorija' => $kategorija, // Pārliecinās, ka tas atbilst izvēlnei add_product.php
            ':cena' => $cena,
            ':quantity' => $quantity,
            ':sizes' => $sizes
        ]);

        // Atgriež veiksmīgu atbildi
        echo json_encode(["success" => true, "message" => "Product added successfully"]);
    } else {
        // Atgriež kļūdu, ja attēlu augšupielāde neizdevās
        echo json_encode(["success" => false, "message" => "Failed to upload image"]);
    }
} catch (PDOException $e) {
    // Atgriež kļūdu, ja rodas problēma ar datubāzi
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
