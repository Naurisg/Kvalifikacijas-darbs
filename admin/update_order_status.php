<?php
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['order_id']) || !isset($input['status'])) {
        throw new Exception('Missing order_id or status');
    }

    $orderId = $input['order_id'];
    $newStatus = $input['status'];

    // Iekļauj datubāzes savienojumu no db_connect.php
    require_once '../db_connect.php';

    // Find the client that has this order
    $stmt = $pdo->prepare('SELECT id, orders FROM clients');
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $found = false;
    foreach ($clients as $client) {
        $orders = json_decode($client['orders'], true);
        if (!is_array($orders)) {
            continue;
        }
        $updated = false;
        foreach ($orders as &$order) {
            if (isset($order['order_id']) && $order['order_id'] == $orderId) {
                $order['status'] = $newStatus;
                $updated = true;
                $found = true;
                break;
            }
        }
        unset($order);
        if ($updated) {
            $ordersJson = json_encode($orders);
            $updateStmt = $pdo->prepare('UPDATE clients SET orders = :orders WHERE id = :id');
            $updateStmt->execute([':orders' => $ordersJson, ':id' => $client['id']]);
            break;
        }
    }

    if ($found) {
        echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
