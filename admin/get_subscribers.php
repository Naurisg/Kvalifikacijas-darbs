<?php
try {
    // Iekļauj datubāzes savienojumu no db_connect.php
    require_once '../db_connect.php';

    $stmt = $pdo->query('SELECT * FROM subscribers ORDER BY created_at DESC');
    $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'subscribers' => $subscribers]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
