<?php
// Sāk sesiju, lai pārbaudītu autorizāciju
session_start();
header('Content-Type: application/json');

// Pārbauda, vai lietotājs ir autorizēts (ir user_id sesijā)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Nav autorizācijas"]);
    exit();
}

// Apstrādā tikai POST pieprasījumus
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Iegūst klienta ID no POST datiem
    $clientId = $_POST['client_id'] ?? null;

    // Pārbauda, vai klienta ID ir norādīts
    if (!$clientId) {
        echo json_encode(["success" => false, "message" => "Nav norādīts klienta ID"]);
        exit();
    }

    // Iekļauj datubāzes pieslēguma failu
    require_once '../db_connect.php';

    try {
        // Dzēš klientu no datubāzes pēc ID
        $stmt = $pdo->prepare("DELETE FROM clients WHERE id = :id");
        $stmt->execute([':id' => $clientId]);

        // Pāradresē atpakaļ uz admin paneli pēc dzēšanas
        header("Location: admin-panelis.php");
        exit();
    } catch (PDOException $e) {
        // Apstrādā datubāzes kļūdu
        echo json_encode(["success" => false, "message" => "Datubāzes kļūda: " . $e->getMessage()]);
    }
} else {
    // Ja pieprasījuma metode nav POST, atgriež kļūdu
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>