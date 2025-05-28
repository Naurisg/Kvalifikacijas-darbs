<?php
header('Content-Type: application/json');
session_start();

include '../db_connect.php';

try {
    // Use $pdo from db_connect.php for MySQL connection
    $db = $pdo;
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get form input
    $email = $_POST['Email'] ?? null;
    $password = $_POST['Password'] ?? null;

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Lūdzu, aizpildiet visus laukus.']);
        exit();
    }

    // Query the MySQL database
    $stmt = $db->prepare('SELECT id, password, role, approved FROM admin_signup WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check user and password
    if ($admin && password_verify($password, $admin['password'])) {
        if ($admin['approved'] == 1) {
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_role'] = $admin['role'];
            echo json_encode(['success' => true, 'redirect' => 'admin-panelis.php']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Jūsu konts vēl nav apstiprināts.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Nepareizs e-pasts vai parole.']);
    }

} catch (Exception $e) {
    error_log('Login error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Servera kļūda. Lūdzu, mēģiniet vēlreiz vēlāk.']);
}
?>
