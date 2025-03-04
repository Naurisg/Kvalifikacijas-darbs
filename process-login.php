<?php
session_start();

try {
    $db = new PDO('sqlite:Datubazes/client_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $email = $_POST['Email'];
    $password = $_POST['Password'];

    $stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nepareizs e-pasts vai parole']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datubāzes kļūda: ' . $e->getMessage()]);
}
?>
