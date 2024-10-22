<?php
session_start(); 

header('Content-Type: application/json'); 

// Savienojums ar SQLite Datubāzi
try {
    $db = new PDO('sqlite:admin_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $e->getMessage()]);
    exit();
}

// Pārbauda vai login form ir iesniegta
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['Email'] ?? null;
    $password = $_POST['Password'] ?? null;

    // Pārbaude
    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Email and Password are required."]);
        exit();
    }

    $sql = "SELECT * FROM admin_signup WHERE email = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Pārbauda vai lietotājs ir apstiprināts
        if ($user['approved'] == 1) {
            // Ja ir apstiprināts parvirza uz admin paneli
            $_SESSION['user_id'] = $user['id']; 
            echo json_encode(["success" => true, "message" => "Login successful!", "redirect" => "adminpanel.html"]);
            header("Location: admin-panelis.html");
            exit();
        } else {
            echo json_encode(["success" => false, "message" => "Your account is not approved yet."]);
            exit();
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid email or password."]);
        exit();
    }
}
?>
