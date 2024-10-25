<?php
header('Content-Type: application/json'); 


error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $db = new PDO('sqlite:../Datubazes/client_signup.db'); 
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $e->getMessage()]);
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    error_log(print_r($_POST, true));

    $clientId = $_GET['id'] ?? null;
    $email = $_POST['email'] ?? null; 
    $name = $_POST['name'] ?? null;

    // Basic validation
    if (empty($clientId) || empty($email) || empty($name)) {
        echo json_encode(["success" => false, "message" => "Aizpildiet visus laukus."]);
        exit();
    }

    $sql = "UPDATE clients SET email = :email, name = :name WHERE id = :id";
    $stmt = $db->prepare($sql);

    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':id', $clientId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Informācija atjaunota veiksmīgi."]);
    } else {
        echo json_encode(["success" => false, "message" => "Notikusi kļūda, mēģiniet vēlreiz."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
