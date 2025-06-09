<?php
header('Content-Type: application/json');
// Sāk sesiju (var izmantot autorizācijas pārbaudei, ja nepieciešams)
session_start();

// Iekļauj datubāzes pieslēguma failu
require_once '../db_connect.php';

try {
    // Iegūst kategoriju no GET parametriem, ja tāda ir norādīta
    $category = $_GET['category'] ?? null;

    // Ja ir norādīta kategorija, atlasa tikai šīs kategorijas produktus
    if ($category) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE kategorija = :category");
        $stmt->execute(['category' => $category]);
    } else {
        // Ja kategorija nav norādīta, atlasa visus produktus
        $stmt = $pdo->query("SELECT * FROM products");
    }

    // Iegūst visus produktus masīvā
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Atgriež produktu sarakstu JSON formātā
    echo json_encode(['success' => true, 'products' => $products]);
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
