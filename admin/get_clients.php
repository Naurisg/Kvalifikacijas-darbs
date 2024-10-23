<?php
header('Content-Type: application/json'); 

// Savienojums ar SQLite Datubāzi
try {
    $db = new PDO('sqlite:../Datubazes/client_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    

    $result = $db->query('SELECT id, email, name, accept_privacy_policy, created_at FROM clients');
    $clients = [];

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $clients[] = $row;
    }

    echo json_encode(['success' => true, 'clients' => $clients]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Savienojums neizdevās: " . $e->getMessage()]);
}
?>
