<?php
header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:Datubazes/subscribers.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // pārbauda vai epasts jau eksistē kas nosūtijis abonēšanas pieprasijumu
    $email = $_POST['email'];
    $checkStmt = $db->prepare("SELECT COUNT(*) FROM subscribers WHERE email = :email");
    $checkStmt->execute(['email' => $email]);
    
    if ($checkStmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Šis e-pasts jau ir reģistrēts!']);
        exit;
    }
    
    $stmt = $db->prepare("INSERT INTO subscribers (email) VALUES (:email)");
    $stmt->execute(['email' => $email]);
    
    echo json_encode(['success' => true, 'message' => 'Veiksmīgi abonēts!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
