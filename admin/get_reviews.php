<?php
// Sāk sesiju, lai pārbaudītu autorizāciju
session_start();

// Pārbauda, vai lietotājs ir autorizēts (ir user_id sesijā)
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    // Iekļauj datubāzes savienojumu no db_connect.php
    require_once '../db_connect.php';

    // Atlasa visas atsauksmes no datubāzes
    $stmt = $pdo->prepare('SELECT review_id AS id, user_id, order_id, review_text, images, rating FROM reviews');
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [];

    // Caur katru atsauksmi papildina ar lietotāja vārdu un e-pastu
    foreach ($reviews as $review) {
        // Iegūst lietotāja vārdu un e-pastu pēc user_id
        $userStmt = $pdo->prepare('SELECT name, email FROM clients WHERE id = :user_id');
        $userStmt->execute([':user_id' => $review['user_id']]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);

        // Pārveido attēlu JSON uz masīvu
        $images = json_decode($review['images'], true);
        if (!is_array($images)) {
            $images = [];
        }

        // Sagatavo atsauksmes informāciju gala masīvam
        $result[] = [
            'id' => $review['id'],
            'user_id' => $review['user_id'],
            'order_id' => $review['order_id'],
            'user_name' => $user['name'] ?? 'Nezināms',
            'user_email' => $user['email'] ?? 'Nezināms',
            'review_text' => $review['review_text'],
            'images' => $images,
            'rating' => floatval($review['rating']),
            'created_at' => '', // Ja nepieciešams, var papildināt ar datumu
        ];
    }

    // Atgriež atsauksmju sarakstu JSON formātā
    echo json_encode(['success' => true, 'reviews' => $result]);

} catch (Exception $e) {
    // Apstrādā servera kļūdu
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
