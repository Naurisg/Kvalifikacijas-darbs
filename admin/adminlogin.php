<?php
session_start(); // Start the session for login

header('Content-Type: application/json'); // Set response type to JSON

// Connection to SQLite Database
try {
    $db = new PDO('sqlite:admin_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $e->getMessage()]);
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['Email'] ?? null;
    $password = $_POST['Password'] ?? null;

    // Basic validation
    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Email and Password are required."]);
        exit();
    }

    // Prepare SQL to check the user credentials
    $sql = "SELECT * FROM admin_signup WHERE email = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Check if the user is approved
        if ($user['approved'] == 1) {
            // If approved, redirect to the admin panel
            $_SESSION['user_id'] = $user['id']; // Set session variable
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
