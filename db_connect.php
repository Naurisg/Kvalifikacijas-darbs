<?php
// db_connect.php
// Savienojums ar datubāzi

$host = 'localhost';
$dbname = 'vissdarbam';
$username = 'root';
$password = '';

try {
    // Izveido PDO savienojumu ar MySQL datubāzi
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Uzstāda PDO kļūdu režīmu uz izņēmumiem
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Ja savienojums neizdodas, izvada kļūdas ziņu un pārtrauc izpildi
    die("Database connection failed: " . $e->getMessage());
}
?> 