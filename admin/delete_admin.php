<?php
header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:../Datubazes/admin_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $id = $_POST['id'] ?? null;
    
    if (!$id) {
        throw new Exception('ID nav norādīts');
    }
    
    $stmt = $db->prepare('DELETE FROM admin_signup WHERE id = :id');
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
