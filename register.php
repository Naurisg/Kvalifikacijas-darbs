<?php
header('Content-Type: application/json'); 

require_once 'db_connect.php';

// Pārbauda, vai pieprasījums ir POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Iegūst ievadītos datus no formas
    $email = $_POST['Email'] ?? null;
    $name = $_POST['field'] ?? null; 
    $password = $_POST['Password'] ?? null;
    $accept_privacy_policy = isset($_POST['Checkbox']) ? 1 : 0;

    // Pārbauda, vai visi nepieciešamie lauki ir aizpildīti
    if (empty($email) || empty($name) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Aizpildiet visus laukus."]);
        exit();
    }

    // Hasho lietotāja paroli
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Pārbauda, vai e-pasts jau eksistē datubāzē
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clients WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $emailExists = $stmt->fetchColumn();

        if ($emailExists) {
            echo json_encode(["success" => false, "message" => "Lietotājs ar šādu e-pastu jau eksistē!"]);
            exit();
        }

        // Sagatavo SQL vaicājumu, lai ievietotu jaunu klientu datubāzē
        $sql = "INSERT INTO clients (email, name, password, accept_privacy_policy, cart) VALUES (:email, :name, :password, :accept_privacy_policy, :cart)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':accept_privacy_policy', $accept_privacy_policy);
        $stmt->bindValue(':cart', json_encode([])); 


        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Reģistrācija veiksmīga!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Notikusi kļūda, mēģiniet vēlreiz."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Kļūda: " . $e->getMessage()]);
    }
}
?>
