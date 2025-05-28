<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

// pārbauda vai epasts jau eksistē kas nosūtijis abonēšanas pieprasijumu
$email = $_POST['email'] ?? '';

try {
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM subscribers WHERE email = :email");
    $checkStmt->execute(['email' => $email]);

    if ($checkStmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Šis e-pasts jau ir reģistrēts!']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (:email)");
    $stmt->execute(['email' => $email]);

    echo json_encode(['success' => true, 'message' => 'Veiksmīgi abonēts!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
