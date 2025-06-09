<?php
try {
    // Iekļauj datubāzes savienojumu no db_connect.php
    require_once '../db_connect.php';

    // Atlasa visus abonentus no datubāzes, sakārtojot pēc pievienošanas datuma (jaunākie augšā)
    $stmt = $pdo->query('SELECT * FROM subscribers ORDER BY created_at DESC');
    $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Atgriež abonentu sarakstu JSON formātā
    echo json_encode(['success' => true, 'subscribers' => $subscribers]);
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
