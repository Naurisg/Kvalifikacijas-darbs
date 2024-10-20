<?php
header('Content-Type: application/json'); // Set response type to JSON

// Connection to SQLite Database
try {
    $db = new PDO('sqlite:admin_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create the table if it doesn't exist
    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS admin_signup (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL,
            name TEXT NOT NULL,
            password TEXT NOT NULL,
            accept_privacy_policy INTEGER NOT NULL CHECK (accept_privacy_policy IN (0, 1)),
            approved INTEGER DEFAULT 0 CHECK (approved IN (0, 1)),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ";
    $db->exec($createTableQuery);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $e->getMessage()]);
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['Email'] ?? null;
    $name = $_POST['wf-user-field-name'] ?? null;
    $password = $_POST['Password'] ?? null;
    $accept_privacy_policy = isset($_POST['Checkbox']) ? 1 : 0;

    // Basic validation
    if (empty($email) || empty($name) || empty($password)) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit();
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepare SQL statement to insert the data
    $sql = "INSERT INTO admin_signup (email, name, password, accept_privacy_policy, approved) VALUES (:email, :name, :password, :accept_privacy_policy, 0)";
    $stmt = $db->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':accept_privacy_policy', $accept_privacy_policy);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registration successful, awaiting approval!"]);
    } else {
        echo json_encode(["success" => false, "message" => "There was an error during registration."]);
    }
}
?>
