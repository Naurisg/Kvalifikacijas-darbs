<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

$user_id = $_POST['user_id'] ?? null;
$order_id = $_POST['order_id'] ?? null;

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

    $stmt = $pdo->prepare('DELETE FROM reviews WHERE user_id = :user_id AND order_id = :order_id');
    $stmt->execute([':user_id' => $user_id, ':order_id' => $order_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Review deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Review not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
