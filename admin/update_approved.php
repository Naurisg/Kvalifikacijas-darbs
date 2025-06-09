<?php
header('Content-Type: application/json');

// Apstrādā tikai POST pieprasījumus
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Iegūst admina ID un apstiprinājuma statusu no POST datiem
    $admin_id = $_POST['id'] ?? null;
    $approved = $_POST['approved'] ?? null;

    // Pārbauda, vai abi lauki ir norādīti
    if ($admin_id === null || $approved === null) {
        echo json_encode(["success" => false, "message" => "Invalid data provided."]);
        exit();
    }

    // Iekļauj datubāzes pieslēguma failu
    require_once '../db_connect.php';

    try {
        // Atjaunina admina apstiprinājuma statusu datubāzē
        $sql = "UPDATE admin_signup SET approved = :approved WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':approved', $approved, PDO::PARAM_INT);
        $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);

        // Ja izdevās atjaunināt, atgriež veiksmīgu atbildi
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Apstiprinātais statuss ir veiksmīgi atjaunināts."]);
        } else {
            echo json_encode(["success" => false, "message" => "Neizdevās atjaunināt apstiprināto statusu."]);
        }
    } catch (PDOException $e) {
        // Apstrādā datubāzes kļūdu
        echo json_encode(["success" => false, "message" => "Datubāzes kļūda: " . $e->getMessage()]);
    }
} else {
    // Ja pieprasījuma metode nav POST, atgriež kļūdu
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
