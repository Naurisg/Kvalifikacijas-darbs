<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = $_POST['id'] ?? null;
    $approved = $_POST['approved'] ?? null;

    if ($admin_id === null || $approved === null) {
        echo json_encode(["success" => false, "message" => "Invalid data provided."]);
        exit();
    }

require_once '../db_connect.php';

try {
    $sql = "UPDATE admin_signup SET approved = :approved WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':approved', $approved, PDO::PARAM_INT);
    $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Apstiprinātais statuss ir veiksmīgi atjaunināts."]);
    } else {
        echo json_encode(["success" => false, "message" => "Neizdevās atjaunināt apstiprināto statusu."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Datubāzes kļūda: " . $e->getMessage()]);
}
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
