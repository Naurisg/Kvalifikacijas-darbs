<?php
session_start();
header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:Datubazes/client_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $email = $_POST['Email'];
    $password = $_POST['Password'];

    $stmt = $db->prepare('SELECT * FROM clients WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true); // Regenerate session ID to prevent session fixation
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        echo json_encode(['success' => true, 'redirect' => 'index.html']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nepareizs e-pasts vai parole']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datubāzes kļūda: ' . $e->getMessage()]);
}
?>