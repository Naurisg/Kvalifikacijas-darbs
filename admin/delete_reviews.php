<?php
// Sāk sesiju, lai pārbaudītu autorizāciju
session_start();

// Pārbauda, vai lietotājs ir autorizēts (ir user_id sesijā)
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Apstrādā tikai POST pieprasījumus
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

// Iegūst lietotāja un pasūtījuma ID no POST datiem
$user_id = $_POST['user_id'] ?? null;
$order_id = $_POST['order_id'] ?? null;

// Pārbauda, vai abi parametri ir norādīti
if (!$user_id || !$order_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit();
}

try {
    // Iekļauj datubāzes savienojumu no db_connect.php
    require_once '../db_connect.php';

    // Iegūst attēlu JSON datus, kas saistīti ar dzēšamo atsauksmi
    // un dzēš tos no servera
    $selectStmt = $pdo->prepare('SELECT images FROM reviews WHERE user_id = :user_id AND order_id = :order_id');
    $selectStmt->execute([':user_id' => $user_id, ':order_id' => $order_id]);
    $row = $selectStmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Ja atsauksmei ir attēli, dzēš tos no servera
        $images = json_decode($row['images'], true);
        if (is_array($images)) {
            foreach ($images as $image) {
                $imagePath = __DIR__ . '/../review_images/' . basename($image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }
    }

    // Dzēš atsauksmi no datubāzes pēc user_id un order_id
    $stmt = $pdo->prepare('DELETE FROM reviews WHERE user_id = :user_id AND order_id = :order_id');
    $stmt->execute([':user_id' => $user_id, ':order_id' => $order_id]);

    // Ja dzēšana veiksmīga, atgriež pozitīvu atbildi
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Review deleted successfully']);
    } else {
        // Ja atsauksme netika atrasta
        echo json_encode(['success' => false, 'message' => 'Review not found']);
    }
} catch (Exception $e) {
    // Apstrādā servera kļūdu
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
