<?php
header('Content-Type: application/json'); // Set response type to JSON

require_once 'db_connect.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['Email'] ?? null;
    $name = $_POST['field'] ?? null; // Adjust the name attribute as necessary
    $password = $_POST['Password'] ?? null;
    $accept_privacy_policy = isset($_POST['Checkbox']) ? 1 : 0;

    // Basic validation
    if (empty($email) || empty($name) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Aizpildiet visus laukus."]);
        exit();
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Check if the email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clients WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $emailExists = $stmt->fetchColumn();

        if ($emailExists) {
            echo json_encode(["success" => false, "message" => "Lietotājs ar šādu e-pastu jau eksistē!"]);
            exit();
        }

        // Prepare SQL statement to insert the data
        $sql = "INSERT INTO clients (email, name, password, accept_privacy_policy, cart) VALUES (:email, :name, :password, :accept_privacy_policy, :cart)";
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':accept_privacy_policy', $accept_privacy_policy);
        $stmt->bindValue(':cart', json_encode([])); // Initialize cart as an empty JSON array

        // Execute the statement
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
