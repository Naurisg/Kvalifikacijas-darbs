<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Nav autorizācijas"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clientId = $_POST['client_id'] ?? null;

    if (!$clientId) {
        echo json_encode(["success" => false, "message" => "Nav norādīts klienta ID"]);
        exit();
    }

require_once '../db_connect.php';

    try {
        // Delete the client from database
        $stmt = $pdo->prepare("DELETE FROM clients WHERE id = :id");
        $stmt->execute([':id' => $clientId]);

        header("Location: admin-panelis.php");
        exit();
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Datubāzes kļūda: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>