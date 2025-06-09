<?php
header('Content-Type: application/json');

// Iekļauj datubāzes pieslēguma failu
require_once '../db_connect.php';

try {
    // Atlasa visus administratorus no datubāzes, sakārtojot pēc izveides datuma (jaunākie augšā)
    $stmt = $pdo->query('SELECT id, email, name, role, approved, created_at FROM admin_signup ORDER BY created_at DESC');
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Atgriež adminu sarakstu JSON formātā
    echo json_encode(['success' => true, 'admins' => $admins]);
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
