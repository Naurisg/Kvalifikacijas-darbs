<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Nav atļauts']);
    exit();
}

try {
    $db = new PDO('sqlite:../Datubazes/admin_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    $role = $data['role'] ?? null;

    if (!$id || !$role) {
        throw new Exception("Invalid data provided");
    }

    $stmt = $db->prepare("UPDATE admin_signup SET role = :role WHERE id = :id");
    $stmt->execute([':id' => $id, ':role' => $role]);

    echo json_encode(["success" => true, "message" => "Role updated successfully"]);
    
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
