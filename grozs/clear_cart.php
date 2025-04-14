<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Lūdzu, piesakieties, lai notīrītu grozu.']);
    exit();
}

$_SESSION['cart'] = [];

try {
    $clientDb = new PDO('sqlite:../Datubazes/client_signup.db'); 
    $clientDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $updateStmt = $clientDb->prepare('UPDATE clients SET cart = :cart WHERE id = :user_id');
    $updateStmt->execute([
        ':cart' => json_encode([]),
        ':user_id' => $_SESSION['user_id']
    ]);

    echo json_encode(['success' => true, 'message' => 'Grozs veiksmīgi notīrīts.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Kļūda: ' . $e->getMessage()]);
}
?>
