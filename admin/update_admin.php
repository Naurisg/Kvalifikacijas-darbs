<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

try {
    $db = new PDO('sqlite:../Datubazes/admin_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $id = $_POST['id'];
    $email = $_POST['email'];
    $name = $_POST['name'];
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];

    if (!empty($newPassword)) {
        $stmt = $db->prepare('SELECT password FROM admin_signup WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!password_verify($oldPassword, $admin['password'])) {
            echo json_encode(['success' => false, 'message' => 'Nepareiza pašreizējā parole']);
            exit();
        }
    }

    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare('UPDATE admin_signup SET email = :email, name = :name, password = :password WHERE id = :id');
        $stmt->execute([
            'email' => $email,
            'name' => $name,
            'password' => $hashedPassword,
            'id' => $id
        ]);
    } else {
        $stmt = $db->prepare('UPDATE admin_signup SET email = :email, name = :name WHERE id = :id');
        $stmt->execute([
            'email' => $email,
            'name' => $name,
            'id' => $id
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Admin info updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
