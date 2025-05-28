<?php
header('Content-Type: application/json'); 

require_once '../db_connect.php';

try {
    $result = $pdo->query('SELECT id, email, name, accept_privacy_policy, created_at FROM clients');
    $clients = [];

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $clients[] = $row;
    }

    echo json_encode(['success' => true, 'clients' => $clients]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Savienojums neizdevās: " . $e->getMessage()]);
}
?>
