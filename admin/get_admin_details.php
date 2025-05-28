<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

require_once '../db_connect.php';

try {
    $stmt = $pdo->prepare('SELECT id, email, name FROM admin_signup WHERE id = :id');
    $stmt->execute(['id' => $_GET['id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo json_encode(['success' => true, 'admin' => $admin]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Admin not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
