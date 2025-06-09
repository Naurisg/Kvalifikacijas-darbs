<?php
header('Content-Type: application/json');
// Sāk sesiju, lai pārbaudītu autorizāciju
session_start();

// Pārbauda, vai lietotājs ir autorizēts (ir user_id sesijā)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

// Iekļauj datubāzes pieslēguma failu
require_once '../db_connect.php';

try {
    // Sagatavo vaicājumu, lai iegūtu admina datus pēc ID
    $stmt = $pdo->prepare('SELECT id, email, name FROM admin_signup WHERE id = :id');
    $stmt->execute(['id' => $_GET['id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Ja admins atrasts, atgriež datus
    if ($admin) {
        echo json_encode(['success' => true, 'admin' => $admin]);
    } else {
        // Ja admins nav atrasts
        echo json_encode(['success' => false, 'message' => 'Admin not found']);
    }
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
