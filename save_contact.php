<?php
require 'db_connect.php';

try {
    // Validējam ievadi
    if (
        empty($_POST['First-Name']) ||
        empty($_POST['Last-Name']) ||
        empty($_POST['Email']) ||
        empty($_POST['Message'])
    ) {
        // Ja kāds lauks nav aizpildīts, pāradresē uz kļūdu
        header("Location: kontakti.php?error=1");
        exit();
    }

    // Izveido tabulu, ja tā neeksistē (pēc nepieciešamības)
    $pdo->exec("CREATE TABLE IF NOT EXISTS contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        vards VARCHAR(100) NOT NULL,
        uzvards VARCHAR(100) NOT NULL,
        epasts VARCHAR(255) NOT NULL,
        zina TEXT NOT NULL
    )");

    // Sagatavo ievades vaicājumu
    $stmt = $pdo->prepare("INSERT INTO contacts (vards, uzvards, epasts, zina) 
                           VALUES (:first_name, :last_name, :email, :message)");

    // Sasaita parametrus
    $stmt->bindParam(':first_name', $_POST['First-Name']);
    $stmt->bindParam(':last_name', $_POST['Last-Name']);
    $stmt->bindParam(':email', $_POST['Email']);
    $stmt->bindParam(':message', $_POST['Message']);

    // Izpilda vaicājumu
    $stmt->execute();

    // Pāradresē uz veiksmes ziņojumu
    header("Location: kontakti.php?success=1");
    exit();
} catch (Exception $e) {
    // Ja kaut kas noiet greizi, pāradresē uz kļūdas ziņojumu
    header("Location: kontakti.php?error=1");
    exit();
}
?>
