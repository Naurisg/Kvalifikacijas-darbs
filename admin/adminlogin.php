<?php
header('Content-Type: application/json');
session_start();

try {
    $db = new PDO('sqlite:../Datubazes/admin_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $email = $_POST['Email'] ?? null;
    $password = $_POST['Password'] ?? null;

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Lūdzu, aizpildiet visus laukus.']);
        exit();
    }

    $stmt = $db->prepare('SELECT id, password, role FROM admin_signup WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['user_role'] = $admin['role'];
        echo json_encode(['success' => true, 'redirect' => 'admin-panelis.php']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nepareizs e-pasts vai parole.']);
    }
} catch (Exception $e) {
    error_log('Login error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Servera kļūda. Lūdzu, mēģiniet vēlreiz vēlāk.']);
}
?>