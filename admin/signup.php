<?php
header('Content-Type: application/json'); 

try {
    $db = new PDO('sqlite:../Datubazes/admin_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS admin_signup (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL,
            name TEXT NOT NULL,
            password TEXT NOT NULL,
            role TEXT DEFAULT 'Moderators',
            approved INTEGER DEFAULT 0 CHECK (approved IN (0, 1)),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ";
    $db->exec($createTableQuery);

    $defaultAdminEmail = "admin@admin.com";
    $checkAdmin = $db->prepare("SELECT COUNT(*) FROM admin_signup WHERE email = :email");
    $checkAdmin->execute(['email' => $defaultAdminEmail]);

    if ($checkAdmin->fetchColumn() == 0) {
        $defaultAdmin = $db->prepare("INSERT INTO admin_signup (email, name, password, role, approved) VALUES (:email, :name, :password, 'Admin', 1)");
        
        $defaultAdmin->execute([
            'email' => $defaultAdminEmail,
            'name' => 'Admin',
            'password' => password_hash('admin123', PASSWORD_BCRYPT)
        ]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Savienojums neizdevās: " . $e->getMessage()]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['Email'] ?? null;
    $name = $_POST['wf-user-field-name'] ?? null;
    $password = $_POST['Password'] ?? null;

    if (empty($email) || empty($name) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Visi lauki ir obligāti."]);
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO admin_signup (email, name, password, role, approved) VALUES (:email, :name, :password, 'Moderators', 0)";
    $stmt = $db->prepare($sql);

    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':password', $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Reģistrācija ir veiksmīga, gaida apstiprinājumu!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Reģistrācijas laikā radās kļūda."]);
    }
}
?>
