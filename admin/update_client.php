<?php
header('Content-Type: application/json'); 

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    error_log(print_r($_POST, true));

    $clientId = $_POST['id'] ?? null;
    $email = $_POST['email'] ?? null; 
    $name = $_POST['name'] ?? null;
    $oldPassword = $_POST['oldPassword'] ?? null;
    $newPassword = $_POST['password'] ?? null;

    // Basic validation
    if (empty($clientId) || empty($email) || empty($name)) {
        echo json_encode(["success" => false, "message" => "Aizpildiet visus laukus."]);
        exit();
    }

    $sql = "UPDATE clients SET email = :email, name = :name";
    $params = [
        ':email' => $email,
        ':name' => $name,
        ':id' => $clientId
    ];

    if (!empty($newPassword)) {
        // Verify old password
        $stmt = $pdo->prepare('SELECT password FROM clients WHERE id = :id');
        $stmt->execute([':id' => $clientId]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$client || !password_verify($oldPassword, $client['password'])) {
            echo json_encode(["success" => false, "message" => "Nepareiza pašreizējā parole"]);
            exit();
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql .= ", password = :password";
        $params[':password'] = $hashedPassword;
    }

    $sql .= " WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute($params)) {
        echo json_encode(["success" => true, "message" => "Informācija atjaunota veiksmīgi."]);
    } else {
        echo json_encode(["success" => false, "message" => "Notikusi kļūda, mēģiniet vēlreiz."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
