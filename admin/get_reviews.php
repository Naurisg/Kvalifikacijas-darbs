<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    // Iekļauj datubāzes savienojumu no db_connect.php
    require_once '../db_connect.php';

    $stmt = $pdo->prepare('SELECT user_id, order_id, review_text, images, rating FROM reviews');
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [];

    foreach ($reviews as $review) {
        $userStmt = $pdo->prepare('SELECT name, email FROM clients WHERE id = :user_id');
        $userStmt->execute([':user_id' => $review['user_id']]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);

        $images = json_decode($review['images'], true);
        if (!is_array($images)) {
            $images = [];
        }

        $result[] = [
            'user_id' => $review['user_id'],
            'order_id' => $review['order_id'],
            'user_name' => $user['name'] ?? 'Nezināms',
            'user_email' => $user['email'] ?? 'Nezināms',
            'review_text' => $review['review_text'],
            'images' => $images,
            'rating' => floatval($review['rating']),
            'created_at' => '',
        ];
    }

    echo json_encode(['success' => true, 'reviews' => $result]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
