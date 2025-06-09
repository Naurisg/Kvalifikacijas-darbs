<?php
header('Content-Type: application/json');

try {
    // Iekļauj datubāzes savienojumu no db_connect.php
    require_once '../db_connect.php';

    // Atlasa visus kontaktus no datubāzes un pārsauc laukus uz angļu valodu
    $stmt = $pdo->query('SELECT id, vards AS name, uzvards AS surname, epasts AS email, zina AS message FROM contacts');
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Atgriež kontaktu sarakstu JSON formātā
    echo json_encode(['success' => true, 'contacts' => $contacts]);
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
