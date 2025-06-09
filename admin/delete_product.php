<?php
// Sāk sesiju, lai pārbaudītu autorizāciju
session_start();
header('Content-Type: application/json');

// Pārbauda, vai lietotājs ir autorizēts (ir user_id sesijā)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Nav autorizācijas"]);
    exit();
}

// Apstrādā tikai POST pieprasījumus
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Iegūst produkta ID no POST datiem
    $productId = $_POST['id'] ?? null;

    // Pārbauda, vai produkta ID ir norādīts
    if (!$productId) {
        echo json_encode(["success" => false, "message" => "Nav norādīts produkta ID"]);
        exit();
    }

    try {
        // Iekļauj datubāzes savienojumu no db_connect.php
        require_once '../db_connect.php';

        // Pirms produkta dzēšanas iegūst attēla faila nosaukumu
        $stmt = $pdo->prepare("SELECT bilde FROM products WHERE id = :id");
        $stmt->execute([':id' => $productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Dzēš produktu no datubāzes pēc ID
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $productId]);

        // Ja produktam ir attēli, dzēš arī tos no servera
        if ($product && $product['bilde']) {
            $images = explode(',', $product['bilde']);
            foreach ($images as $image) {
                $imagePath = '../' . $image;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }

        // Atgriež veiksmīgu atbildi
        echo json_encode(["success" => true, "message" => "Produkts veiksmīgi dzēsts"]);
    } catch (PDOException $e) {
        // Apstrādā datubāzes kļūdu
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>
