<?php
try {
    // Create (connect to) SQLite database in file
    $pdo = new PDO('sqlite:Datubazes/kontakti.db');
    // Set error mode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS contacts (
        id INTEGER PRIMARY KEY,
        vards TEXT,
        uzvards TEXT,
        epasts TEXT,
        zina TEXT
    )");

    // Prepare INSERT statement to SQLite3 file db
    $stmt = $pdo->prepare("INSERT INTO contacts (vards, uzvards, epasts, zina) VALUES (:first_name, :last_name, :email, :message)");

    // Bind parameters to statement variables
    $stmt->bindParam(':first_name', $_POST['First-Name']);
    $stmt->bindParam(':last_name', $_POST['Last-Name']);
    $stmt->bindParam(':email', $_POST['Email']);
    $stmt->bindParam(':message', $_POST['Message']);

    // Execute statement
    $stmt->execute();

    // Redirect to a success page
    header("Location: kontakti.html?success=1");
} catch (PDOException $e) {
    // Redirect to an error page
    header("Location: kontakti.html?error=1");
    // Print PDOException message
    echo $e->getMessage();
}
?>
