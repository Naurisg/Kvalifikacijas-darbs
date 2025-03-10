<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

try {
    $db = new PDO('sqlite:../Datubazes/client_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $db->prepare('SELECT id, email, name FROM clients WHERE id = :id');
    $stmt->execute(['id' => $_GET['id']]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($client) {
        echo json_encode(['success' => true, 'client' => $client]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Client not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
