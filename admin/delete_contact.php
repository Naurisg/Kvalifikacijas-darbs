<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contactId = $_POST['id'] ?? '';

    if ($contactId) {
        try {
            $pdo = new PDO('sqlite:../Datubazes/kontakti.db');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare('DELETE FROM contacts WHERE id = :id');
            $stmt->execute(['id' => $contactId]);

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
