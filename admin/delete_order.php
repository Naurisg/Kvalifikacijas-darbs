<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$orderId = $_POST['id'] ?? null;

if (!$orderId) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit;
}

try {
    // Iekļauj datubāzes savienojumu no db_connect.php
    require_once '../db_connect.php';

    $stmt = $pdo->query('SELECT id, orders FROM clients WHERE orders IS NOT NULL');
    $clientFound = false;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $orders = json_decode($row['orders'], true);
        if (!is_array($orders)) {
            continue;
        }

        $orderIndex = null;
        foreach ($orders as $index => $order) {
            if (isset($order['order_id']) && $order['order_id'] == $orderId) {
                $orderIndex = $index;
                break;
            }
        }

        if ($orderIndex !== null) {
            // Noņem pasūtijumu
            array_splice($orders, $orderIndex, 1);

            // Atjaunina clienta pasūtijuma JSON
            $updateStmt = $pdo->prepare('UPDATE clients SET orders = :orders WHERE id = :id');
            $updateStmt->execute([
                ':orders' => json_encode($orders),
                ':id' => $row['id']
            ]);

            $clientFound = true;
            break;
        }
    }

    if ($clientFound) {
        echo json_encode(['success' => true, 'message' => 'Pasūtījums veiksmīgi dzēsts']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Pasūtījums nav atrasts']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datubāzes kļūda: ' . $e->getMessage()]);
}
?>
