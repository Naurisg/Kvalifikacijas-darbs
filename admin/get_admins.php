<?php
header('Content-Type: application/json');

require_once '../db_connect.php';

try {
    $stmt = $pdo->query('SELECT id, email, name, role, approved, created_at FROM admin_signup ORDER BY created_at DESC');
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'admins' => $admins]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
