<?php
header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:../Datubazes/admin_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $db->query('SELECT id, email, name, role, approved, created_at FROM admin_signup ORDER BY created_at DESC');
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'admins' => $admins]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
