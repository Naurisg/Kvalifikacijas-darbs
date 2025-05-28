<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Nav atļauts']);
    exit();
}

require_once '../db_connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    $role = $data['role'] ?? null;

    if (!$id || !$role) {
        throw new Exception("Invalid data provided");
    }

    $stmt = $pdo->prepare("UPDATE admin_signup SET role = :role WHERE id = :id");
    $stmt->execute([':id' => $id, ':role' => $role]);

    echo json_encode(["success" => true, "message" => "Role updated successfully"]);
    
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
