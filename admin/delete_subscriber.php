<?php
header('Content-Type: application/json');

try {
    // Iekļauj datubāzes savienojumu no db_connect.php
    require_once '../db_connect.php';

    // Iegūst abonenta ID no POST datiem
    $id = $_POST['id'];

    // Sagatavo un izpilda vaicājumu, lai dzēstu abonentu pēc ID
    $stmt = $pdo->prepare("DELETE FROM subscribers WHERE id = :id");
    $stmt->execute([':id' => $id]);

    // Ja dzēšana veiksmīga, atgriež pozitīvu atbildi
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
