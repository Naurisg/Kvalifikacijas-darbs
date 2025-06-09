<?php
header('Content-Type: application/json'); 

// Iekļauj datubāzes pieslēguma failu
require_once '../db_connect.php';

try {
    // Atlasa visus klientus no datubāzes
    $result = $pdo->query('SELECT id, email, name, accept_privacy_policy, created_at FROM clients');
    $clients = [];

    // Pievieno katru klientu masīvā
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $clients[] = $row;
    }

    // Atgriež klientu sarakstu JSON formātā
    echo json_encode(['success' => true, 'clients' => $clients]);
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(["success" => false, "message" => "Savienojums neizdevās: " . $e->getMessage()]);
}
?>
