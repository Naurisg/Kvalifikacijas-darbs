<?php
// Apstrādā tikai POST pieprasījumus
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Iegūst kontakta ID no POST datiem
    $contactId = $_POST['id'] ?? '';

    // Pārbauda, vai kontakta ID ir norādīts
    if ($contactId) {
        try {
            // Iekļauj datubāzes savienojumu no db_connect.php
            require_once '../db_connect.php';

            // Sagatavo un izpilda vaicājumu, lai dzēstu kontaktu pēc ID
            $stmt = $pdo->prepare('DELETE FROM contacts WHERE id = :id');
            $stmt->execute([':id' => $contactId]);

            // Ja dzēšana veiksmīga, atgriež pozitīvu atbildi
            echo json_encode(['success' => true, 'message' => 'Kontakts veiksmīgi dzēsts']);
        } catch (PDOException $e) {
            // Apstrādā datubāzes kļūdu
            echo json_encode(['success' => false, 'message' => 'Kļūda dzēšot kontaktu: ' . $e->getMessage()]);
        }
    } else {
        // Ja nav norādīts ID, atgriež kļūdas ziņu
        echo json_encode(['success' => false, 'message' => 'Nederīgs kontakta ID']);
    }
} else {
    // Ja pieprasījuma metode nav POST, atgriež kļūdas ziņu
    echo json_encode(['success' => false, 'message' => 'Nederīgs pieprasījums']);
}
?>
