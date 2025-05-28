<?php
header('Content-Type: application/json');

require_once '../db_connect.php';

try {
    $id = $_POST['id'] ?? null;
    
    if (!$id) {
        throw new Exception('ID nav norādīts');
    }
    
    $stmt = $pdo->prepare('DELETE FROM admin_signup WHERE id = :id');
    $stmt->execute(['id' => $id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Administrators veiksmīgi dzēsts']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Administrators netika atrasts']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
