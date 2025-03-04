<?php
header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:../Datubazes/subscribers.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $id = $_POST['id'];
    $stmt = $db->prepare("DELETE FROM subscribers WHERE id = :id");
    $stmt->execute(['id' => $id]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
