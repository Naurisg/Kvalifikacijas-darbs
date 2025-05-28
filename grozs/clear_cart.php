<?php
session_start();
header('Content-Type: application/json');

require_once '../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Lūdzu, piesakieties, lai notīrītu grozu.']);
    exit();
}

$_SESSION['cart'] = [];

try {
    $updateStmt = $pdo->prepare('UPDATE clients SET cart = :cart WHERE id = :user_id');
    $updateStmt->execute([
        ':cart' => json_encode([]),
        ':user_id' => $_SESSION['user_id']
    ]);

    echo json_encode(['success' => true, 'message' => 'Grozs veiksmīgi notīrīts.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Kļūda: ' . $e->getMessage()]);
}
?>
