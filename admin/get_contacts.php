<?php
header('Content-Type: application/json');

try {
    $pdo = new PDO('sqlite:../Datubazes/kontakti.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query('SELECT id, vards AS name, uzvards AS surname, epasts AS email, zina AS message FROM contacts');
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'contacts' => $contacts]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
