<?php
header('Content-Type: application/json');

require_once '../db_connect.php';

try {
    // Pābauda vai default admins jau eksistē
    $defaultAdminEmail = "admin@admin.com";
    $checkAdmin = $pdo->prepare("SELECT COUNT(*) FROM admin_signup WHERE email = :email");
    $checkAdmin->execute(['email' => $defaultAdminEmail]);

    if ($checkAdmin->fetchColumn() == 0) {
        $defaultAdmin = $pdo->prepare("
            INSERT INTO admin_signup (email, name, password, role, approved) 
            VALUES (:email, :name, :password, 'Admin', 1)
        ");
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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['Email'] ?? null;
    $name = $_POST['wf-user-field-name'] ?? null;
    $password = $_POST['Password'] ?? null;

    if (empty($email) || empty($name) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Visi lauki ir obligāti."]);
        exit();
    }

    // Paroles minimālais garums 8 simboli
    if (strlen($password) < 8) {
        echo json_encode(["success" => false, "message" => "Parolei jābūt vismaz 8 simbolus garai."]);
        exit();
    }

    // Pārbauda vai e-pasts jau eksistē
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM admin_signup WHERE email = :email");
    $checkStmt->execute(['email' => $email]);
    if ($checkStmt->fetchColumn() > 0) {
        echo json_encode(["success" => false, "message" => "Šāds e-pasts jau eksistē!"]);
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO admin_signup (email, name, password, role, approved) 
            VALUES (:email, :name, :password, 'Mod', 0)";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':password', $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Reģistrācija ir veiksmīga, gaidiet apstiprinājumu!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Reģistrācijas laikā radās kļūda."]);
    }
}
?>
