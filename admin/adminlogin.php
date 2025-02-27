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

    // SQL lai parbaudītu lietotāju
    $sql = "SELECT * FROM admin_signup WHERE email = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // parbauda vai parole ir pareiza
        if (password_verify($password, $user['password'])) {
            // Pārbauda vai lietotājs ir apstiprināts
            if ($user['approved'] == 1) {
                // ja ir apstiprināts tad novirza uz admin paneli
                $_SESSION['user_id'] = $user['id']; 
                echo json_encode(["success" => true, "message" => "Logins veiksmīgs!", "redirect" => "admin-panelis.php"]);
                exit(); 
            } else {
                // ja nav apstiprināts parāda error message
                echo json_encode(["success" => false, "message" => "Jūsu konts vēl nav apstiprināts."]);
                exit();
            }
        } else {
            // Ja nav pareiza parole parāda error message
            echo json_encode(["success" => false, "message" => "Nepareizi pieteikšanās dati. Mēģini vēlreiz."]);
            exit();
        }
    } else {
        // Ja lietotājs nav atrasts parāda error meassage
        echo json_encode(["success" => false, "message" => "Nav atrasts konts ar šo e-pastu.."]);
        exit();
    }
}
?>
