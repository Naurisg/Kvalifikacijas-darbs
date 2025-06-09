<?php
session_start();
header('Content-Type: application/json');

// Pārbauda, vai lietotājs ir autorizēts (ir user_id sesijā)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Nav atļauts']);
    exit();
}

// Iekļauj datubāzes pieslēguma failu
require_once '../db_connect.php';

try {
    // Nolasa JSON ievadi no pieprasījuma ķermeņa
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    $role = $data['role'] ?? null;

    // Pārbauda, vai abi lauki ir norādīti
    if (!$id || !$role) {
        throw new Exception("Invalid data provided");
    }

    // Atjaunina admina lomu datubāzē pēc ID
    $stmt = $pdo->prepare("UPDATE admin_signup SET role = :role WHERE id = :id");
    $stmt->execute([':id' => $id, ':role' => $role]);

    // Atgriež veiksmīgu atbildi
    echo json_encode(["success" => true, "message" => "Role updated successfully"]);
    
} catch (Exception $e) {
    // Apstrādā kļūdu un atgriež kļūdas ziņu
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
