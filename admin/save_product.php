<?php
header('Content-Type: application/json');
session_start();

// Pārbauda, vai lietotājs ir autorizēts
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Neautorizēta piekļuve"]);
    exit();
}

// Pārbauda, vai pieprasījums ir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Nepareiza pieprasījuma metode"]);
    exit();
}

try {
    // Izveido savienojumu ar datubāzi
    $db = new PDO('sqlite:../Datubazes/products.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Pārbauda un izveido tabulu, ja tā neeksistē
    $db->exec('PRAGMA foreign_keys = OFF;');
    $db->exec('CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nosaukums TEXT NOT NULL,
        apraksts TEXT NOT NULL,
        bilde TEXT NOT NULL,
        kategorija TEXT NOT NULL,
        cena DECIMAL(10,2) NOT NULL,
        quantity INTEGER DEFAULT 0 NOT NULL,
        sizes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )');

    // Pārbauda un pievieno trūkstošās kolonnas
    $columns = $db->query("PRAGMA table_info(products)")->fetchAll(PDO::FETCH_COLUMN, 1);
    if (!in_array('quantity', $columns)) {
        $db->exec('ALTER TABLE products ADD COLUMN quantity INTEGER DEFAULT 0 NOT NULL');
    }
    if (!in_array('sizes', $columns)) {
        $db->exec('ALTER TABLE products ADD COLUMN sizes TEXT');
    }

    // Validē obligātos laukus
    $required_fields = ['nosaukums', 'apraksts', 'kategorija', 'cena', 'quantity'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(["success" => false, "message" => "Lauks '{$field}' ir obligāts"]);
            exit();
        }
    }

    // Iegūst datus no POST pieprasījuma
    $nosaukums = trim($_POST['nosaukums']);
    $apraksts = trim($_POST['apraksts']);
    $kategorija = $_POST['kategorija'];
    $cena = (float)$_POST['cena'];
    $quantity = (int)$_POST['quantity'];
    $sizes = isset($_POST['sizes']) ? implode(',', $_POST['sizes']) : null;

    // Pārbauda, vai ir augšupielādēti attēli
    if (!isset($_FILES['bilde']) || (is_array($_FILES['bilde']['name']) && count($_FILES['bilde']['name']) === 0)) {
        echo json_encode(["success" => false, "message" => "Vismaz viena bilde ir obligāta"]);
        exit();
    }

    // Sagatavo direktoriju attēliem
    $target_dir = "../images/products/";
    if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            echo json_encode(["success" => false, "message" => "Nevar izveidot attēlu mapi"]);
            exit();
        }
    }

    // Apstrādā attēlu augšupielādi
    $file = $_FILES['bilde'];
    $image_paths = [];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if (is_array($file['name'])) {
        // Apstrādā vairākus attēlus
        for ($i = 0; $i < count($file['name']); $i++) {
            if ($file['error'][$i] !== UPLOAD_ERR_OK) {
                continue; // Izlaiž neveiksmīgos augšupielādes mēģinājumus
            }

            // Pārbauda faila tipu un izmēru
            $file_type = mime_content_type($file['tmp_name'][$i]);
            if (!in_array($file_type, $allowed_types)) {
                continue;
            }
            if ($file['size'][$i] > $max_size) {
                continue;
            }

            // Ģenerē unikālu faila nosaukumu
            $file_ext = pathinfo($file['name'][$i], PATHINFO_EXTENSION);
            $file_name = time() . '_' . uniqid() . '.' . strtolower($file_ext);
            $target_file = $target_dir . $file_name;

            // Pārvieto failu uz mērķa mapi
            if (move_uploaded_file($file['tmp_name'][$i], $target_file)) {
                $image_paths[] = "images/products/" . $file_name;
            }
        }
    } else {
        // Apstrādā vienu attēlu (rezerves variants)
        if ($file['error'] === UPLOAD_ERR_OK) {
            $file_type = mime_content_type($file['tmp_name']);
            if (in_array($file_type, $allowed_types) && $file['size'] <= $max_size) {
                $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $file_name = time() . '_' . uniqid() . '.' . strtolower($file_ext);
                $target_file = $target_dir . $file_name;
                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    $image_paths[] = "images/products/" . $file_name;
                }
            }
        }
    }

    // Pārbauda, vai ir vismaz viens augšupielādēts attēls
    if (empty($image_paths)) {
        echo json_encode(["success" => false, "message" => "Neizdevās augšupielādēt attēlus vai attēli neatbilst prasībām"]);
        exit();
    }

    // Saglabā visus attēlu ceļus kā komatu atdalītu virkni
    $bilde_path = implode(',', $image_paths);

    // Sagatavo SQL pieprasījumu produkta pievienošanai
    $stmt = $db->prepare('INSERT INTO products (nosaukums, apraksts, bilde, kategorija, cena, quantity, sizes) 
                         VALUES (:nosaukums, :apraksts, :bilde, :kategorija, :cena, :quantity, :sizes)');

    // Izpilda pieprasījumu ar datiem
    $stmt->execute([
        ':nosaukums' => $nosaukums,
        ':apraksts' => $apraksts,
        ':bilde' => $bilde_path,
        ':kategorija' => $kategorija,
        ':cena' => $cena,
        ':quantity' => $quantity,
        ':sizes' => $sizes
    ]);

    // Atgriež veiksmīgu atbildi
    echo json_encode([
        "success" => true, 
        "message" => "Produkts veiksmīgi pievienots",
        "images" => $image_paths // Atgriež augšupielādēto attēlu sarakstu
    ]);

} catch (PDOException $e) {
    // Atgriež kļūdu, ja rodas problēma ar datubāzi
    echo json_encode(["success" => false, "message" => "Datubāzes kļūda: " . $e->getMessage()]);
} catch (Exception $e) {
    // Atgriež citas kļūdas
    echo json_encode(["success" => false, "message" => "Kļūda: " . $e->getMessage()]);
}
?>