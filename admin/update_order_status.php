<?php
header('Content-Type: application/json');

try {
    // Nolasa JSON ievadi no pieprasījuma ķermeņa
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['order_id']) || !isset($input['status'])) {
        throw new Exception('Missing order_id or status');
    }

    $orderId = $input['order_id'];
    $newStatus = $input['status'];

    // Iekļauj datubāzes savienojumu no db_connect.php
    require_once '../db_connect.php';

    // Atrod klientu, kuram ir šis pasūtījums
    $stmt = $pdo->prepare('SELECT id, orders FROM clients');
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $found = false;
    foreach ($clients as $client) {
        // Pārvērš klienta pasūtījumus no JSON uz masīvu
        $orders = json_decode($client['orders'], true);
        if (!is_array($orders)) {
            continue;
        }
        $updated = false;
        // Atrod vajadzīgo pasūtījumu un atjauno tā statusu
        foreach ($orders as &$order) {
            if (isset($order['order_id']) && $order['order_id'] == $orderId) {
                $order['status'] = $newStatus;
                $updated = true;
                $found = true;
                break;
            }
        }
        unset($order);
        // Ja statuss tika atjaunināts, saglabā izmaiņas datubāzē
        if ($updated) {
            $ordersJson = json_encode($orders);
            $updateStmt = $pdo->prepare('UPDATE clients SET orders = :orders WHERE id = :id');
            $updateStmt->execute([':orders' => $ordersJson, ':id' => $client['id']]);
            break;
        }
    }

    // Atgriež rezultātu atkarībā no tā, vai pasūtījums tika atrasts un atjaunināts
    if ($found) {
        echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
    }
} catch (Exception $e) {
    // Apstrādā kļūdu un atgriež kļūdas ziņu
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
