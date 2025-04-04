<?php
try {
    // Izveido (pievienojas) SQLite datubāzi failā
    $pdo = new PDO('sqlite:Datubazes/kontakti.db');
    // Uzstāda kļūdu režīmu uz izņēmumiem
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Izveido tabulu, ja tā neeksistē
    $pdo->exec("CREATE TABLE IF NOT EXISTS contacts (
        id INTEGER PRIMARY KEY,
        vards TEXT,
        uzvards TEXT,
        epasts TEXT,
        zina TEXT
    )");

    // Sagatavo INSERT komandu SQLite3 datubāzei
    $stmt = $pdo->prepare("INSERT INTO contacts (vards, uzvards, epasts, zina) VALUES (:first_name, :last_name, :email, :message)");

    // Piesaista parametrus pie komandas mainīgajiem
    $stmt->bindParam(':first_name', $_POST['First-Name']);
    $stmt->bindParam(':last_name', $_POST['Last-Name']);
    $stmt->bindParam(':email', $_POST['Email']);
    $stmt->bindParam(':message', $_POST['Message']);

    // Izpilda komandu
    $stmt->execute();

    // Pārvirza uz veiksmes lapu
    header("Location: kontakti.html?success=1");
} catch (PDOException $e) {
    // Pārvirza uz kļūdas lapu
    header("Location: kontakti.html?error=1");
    // Izvada PDOException kļūdas ziņojumu
    echo $e->getMessage();
}
?>