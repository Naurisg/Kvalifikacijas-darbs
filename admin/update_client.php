<?php
header('Content-Type: application/json'); 

// Parāda visas kļūdas (izstrādes laikā)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iekļauj datubāzes pieslēguma failu
require_once '../db_connect.php';

// Apstrādā tikai POST pieprasījumus
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Izvada POST datus uz error log (debugam)
    error_log(print_r($_POST, true));

    // Iegūst formas laukus no POST datiem
    $clientId = $_POST['id'] ?? null;
    $email = $_POST['email'] ?? null; 
    $name = $_POST['name'] ?? null;
    $oldPassword = $_POST['oldPassword'] ?? null;
    $newPassword = $_POST['password'] ?? null;

    // Pārbauda, vai visi obligātie lauki ir aizpildīti
    if (empty($clientId) || empty($email) || empty($name)) {
        echo json_encode(["success" => false, "message" => "Aizpildiet visus laukus."]);
        exit();
    }

    // Sagatavo SQL vaicājumu klienta datu atjaunināšanai
    $sql = "UPDATE clients SET email = :email, name = :name";
    $params = [
        ':email' => $email,
        ':name' => $name,
        ':id' => $clientId
    ];

    // Ja tiek mainīta parole, pārbauda vai vecā parole ir pareiza
    if (!empty($newPassword)) {
        // Iegūst klienta pašreizējo paroli no datubāzes
        $stmt = $pdo->prepare('SELECT password FROM clients WHERE id = :id');
        $stmt->execute([':id' => $clientId]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        // Ja parole nav pareiza, atgriež kļūdu
        if (!$client || !password_verify($oldPassword, $client['password'])) {
            echo json_encode(["success" => false, "message" => "Nepareiza pašreizējā parole"]);
            exit();
        }

        // Šifrē jauno paroli un pievieno SQL vaicājumam
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql .= ", password = :password";
        $params[':password'] = $hashedPassword;
    }

    $sql .= " WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    // Izpilda vaicājumu un atgriež rezultātu
    if ($stmt->execute($params)) {
        echo json_encode(["success" => true, "message" => "Informācija atjaunota veiksmīgi."]);
    } else {
        echo json_encode(["success" => false, "message" => "Notikusi kļūda, mēģiniet vēlreiz."]);
    }
} else {
    // Ja pieprasījuma metode nav POST, atgriež kļūdu
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
