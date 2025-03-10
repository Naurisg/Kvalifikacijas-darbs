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

    try {
        $db = new PDO('sqlite:../Datubazes/client_signup.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Delete the client from database
        $stmt = $db->prepare("DELETE FROM clients WHERE id = :id");
        $stmt->execute([':id' => $clientId]);

        echo json_encode(["success" => true, "message" => "Klients veiksmīgi dzēsts"]);
        header("Location: admin-panelis.php");
        exit();
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Datubāzes kļūda: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
