<?php
header('Content-Type: application/json');

// Apstrādā tikai POST pieprasījumus
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Iegūst pasūtījuma ID no POST datiem
$orderId = $_POST['id'] ?? null;

// Pārbauda, vai pasūtījuma ID ir norādīts
if (!$orderId) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit;
}

try {
    // Iekļauj datubāzes savienojumu no db_connect.php
    require_once '../db_connect.php';

    // Atlasa visus klientus, kuriem ir pasūtījumi
    $stmt = $pdo->query('SELECT id, orders FROM clients WHERE orders IS NOT NULL');
    $clientFound = false;

    // Caur katru klientu pārbauda, vai pasūtījumu sarakstā ir dzēšamais pasūtījums
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $orders = json_decode($row['orders'], true);
        if (!is_array($orders)) {
            continue;
        }

        $orderIndex = null;
        // Atrod pasūtījuma indeksu pēc order_id
        foreach ($orders as $index => $order) {
            if (isset($order['order_id']) && $order['order_id'] == $orderId) {
                $orderIndex = $index;
                break;
            }
        }

        if ($orderIndex !== null) {
            // Noņem pasūtījumu no masīva
            array_splice($orders, $orderIndex, 1);

            // Atjaunina klienta pasūtījumu JSON laukā datubāzē
            $updateStmt = $pdo->prepare('UPDATE clients SET orders = :orders WHERE id = :id');
            $updateStmt->execute([
                ':orders' => json_encode($orders),
                ':id' => $row['id']
            ]);

            $clientFound = true;
            break;
        }
    }

    // Ja pasūtījums tika atrasts un dzēsts
    if ($clientFound) {
        echo json_encode(['success' => true, 'message' => 'Pasūtījums veiksmīgi dzēsts']);
    } else {
        // Ja pasūtījums netika atrasts
        echo json_encode(['success' => false, 'message' => 'Pasūtījums nav atrasts']);
    }
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(['success' => false, 'message' => 'Datubāzes kļūda: ' . $e->getMessage()]);
}
?>
