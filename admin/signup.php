<?php
header('Content-Type: application/json'); 

// Savienojums ar SQLite Datubāzi
try {
    $db = new PDO('sqlite:admin_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Izveido tabulu
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
    echo json_encode(["success" => false, "message" => "Savienojums neizdevās: " . $e->getMessage()]);
    exit();
}

// Parbauda vai pietiekums ir iesniegts
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['Email'] ?? null;
    $name = $_POST['wf-user-field-name'] ?? null;
    $password = $_POST['Password'] ?? null;
    $accept_privacy_policy = isset($_POST['Checkbox']) ? 1 : 0;

    // Pārbaude
    if (empty($email) || empty($name) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Visi lauki ir obligāti."]);
        exit();
    }

    // Šifrē paroli
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO admin_signup (email, name, password, accept_privacy_policy, approved) VALUES (:email, :name, :password, :accept_privacy_policy, 0)";
    $stmt = $db->prepare($sql);

    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':accept_privacy_policy', $accept_privacy_policy);

    // Izvada paziņojumu
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Reģistrācija ir veiksmīga, gaida apstiprinājumu!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Reģistrācijas laikā radās kļūda."]);
    }
}
?>
