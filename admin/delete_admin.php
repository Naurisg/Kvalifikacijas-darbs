<?php
header('Content-Type: application/json');

// Iekļauj datubāzes pieslēguma failu
require_once '../db_connect.php';

try {
    // Iegūst admina ID no POST datiem
    $id = $_POST['id'] ?? null;
    
    // Pārbauda, vai ID ir norādīts
    if (!$id) {
        throw new Exception('ID nav norādīts');
    }
    
    // Sagatavo un izpilda vaicājumu, lai dzēstu administratoru pēc ID
    $stmt = $pdo->prepare('DELETE FROM admin_signup WHERE id = :id');
    $stmt->execute(['id' => $id]);
    
    // Ja administrators tika dzēsts, atgriež veiksmīgu atbildi
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Administrators veiksmīgi dzēsts']);
    } else {
        // Ja administrators netika atrasts
        echo json_encode(['success' => false, 'message' => 'Administrators netika atrasts']);
    }
    
} catch (Exception $e) {
    // Apstrādā kļūdas un atgriež kļūdas ziņojumu
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>