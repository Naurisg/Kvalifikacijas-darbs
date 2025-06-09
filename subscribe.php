<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

// Iegūst e-pastu no POST datiem
$email = $_POST['email'] ?? '';

try {
    // Pārbauda, vai e-pasts jau ir abonentu sarakstā
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM subscribers WHERE email = :email");
    $checkStmt->execute(['email' => $email]);

    if ($checkStmt->fetchColumn() > 0) {
        // Ja e-pasts jau eksistē, atgriež kļūdas ziņu
        echo json_encode(['success' => false, 'message' => 'Šis e-pasts jau ir reģistrēts!']);
        exit;
    }

    // Ievieto jaunu e-pastu abonentu sarakstā
    $stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (:email)");
    $stmt->execute(['email' => $email]);

    // Atgriež veiksmes ziņu
    echo json_encode(['success' => true, 'message' => 'Veiksmīgi abonēts!']);
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
