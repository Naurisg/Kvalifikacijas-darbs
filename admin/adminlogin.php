<?php
header('Content-Type: application/json');
// Uzstāda pielāgotu sesijas nosaukumu priekš admina
session_name('admin_session');
session_start();

// Iekļauj datubāzes pieslēguma failu
include '../db_connect.php';

try {
    // Izmanto $pdo no db_connect.php kā MySQL pieslēgumu
    $db = $pdo;
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Iegūst formas ievadi (e-pasts un parole)
    $email = $_POST['Email'] ?? null;
    $password = $_POST['Password'] ?? null;

    // Pārbauda, vai visi lauki ir aizpildīti
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Lūdzu, aizpildiet visus laukus.']);
        exit();
    }

    // Vaicā MySQL datubāzi pēc admina ar norādīto e-pastu
    $stmt = $db->prepare('SELECT id, password, role, approved FROM admin_signup WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Pārbauda, vai lietotājs eksistē un parole ir pareiza
    if ($admin && password_verify($password, $admin['password'])) {
        // Pārbauda, vai konts ir apstiprināts
        if ($admin['approved'] == 1) {
            // Saglabā lietotāja ID un lomu sesijā
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_role'] = $admin['role'];
            echo json_encode(['success' => true, 'redirect' => 'admin-panelis.php']);
        } else {
            // Ja konts nav apstiprināts
            echo json_encode(['success' => false, 'message' => 'Jūsu konts vēl nav apstiprināts.']);
        }
    } else {
        // Nepareizs e-pasts vai parole
        echo json_encode(['success' => false, 'message' => 'Nepareizs e-pasts vai parole.']);
    }

} catch (Exception $e) {
    // Apstrādā izņēmumu un izvada kļūdas ziņojumu
    error_log('Login error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Servera kļūda. Lūdzu, mēģiniet vēlreiz vēlāk.']);
}
?>
