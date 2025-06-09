<?php
header('Content-Type: application/json');
// Sāk sesiju, lai pārbaudītu autorizāciju
session_start();

// Pārbauda, vai lietotājs ir autorizēts (ir user_id sesijā)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

// Iekļauj datubāzes pieslēguma failu
require_once '../db_connect.php';

try {
    // Iegūst formas laukus no POST datiem
    $id = $_POST['id'];
    $email = $_POST['email'];
    $name = $_POST['name'];
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];

    // Ja tiek mainīta parole, pārbauda vai vecā parole ir pareiza
    if (!empty($newPassword)) {
        $stmt = $pdo->prepare('SELECT password FROM admin_signup WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!password_verify($oldPassword, $admin['password'])) {
            echo json_encode(['success' => false, 'message' => 'Nepareiza pašreizējā parole']);
            exit();
        }
    }

    // Ja ir jauna parole, atjaunina arī paroli
    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE admin_signup SET email = :email, name = :name, password = :password WHERE id = :id');
        $stmt->execute([
            'email' => $email,
            'name' => $name,
            'password' => $hashedPassword,
            'id' => $id
        ]);
    } else {
        // Ja parole netiek mainīta, atjaunina tikai e-pastu un vārdu
        $stmt = $pdo->prepare('UPDATE admin_signup SET email = :email, name = :name WHERE id = :id');
        $stmt->execute([
            'email' => $email,
            'name' => $name,
            'id' => $id
        ]);
    }

    // Atgriež veiksmīgu atbildi
    echo json_encode(['success' => true, 'message' => 'Admin info updated successfully']);
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
