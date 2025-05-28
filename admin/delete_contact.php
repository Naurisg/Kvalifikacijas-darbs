<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contactId = $_POST['id'] ?? '';

    if ($contactId) {
        try {
            // Iekļauj datubāzes savienojumu no db_connect.php
            require_once '../db_connect.php';

            $stmt = $pdo->prepare('DELETE FROM contacts WHERE id = :id');
            $stmt->execute([':id' => $contactId]);

            echo json_encode(['success' => true, 'message' => 'Kontakts veiksmīgi dzēsts']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Kļūda dzēšot kontaktu: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Nederīgs kontakta ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Nederīgs pieprasījums']);
}
?>
