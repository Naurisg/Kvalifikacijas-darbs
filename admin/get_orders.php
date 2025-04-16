<?php
header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:../Datubazes/client_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->query('SELECT id, name AS client_name, orders FROM clients WHERE orders IS NOT NULL');
    $orders = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $clientOrders = json_decode($row['orders'], true);

        if (is_array($clientOrders)) {
            foreach ($clientOrders as $order) {
                $orders[] = [
                    'id' => $order['order_id'] ?? 'N/A',
                    'client_name' => $row['client_name'],
                    'products' => $order['items'] ?? [],
                    'total_price' => $order['total_amount'] ?? 0,
                    'date' => $order['created_at'] ?? 'N/A',
                    'status' => $order['status'] ?? 'Pending'
                ];
            }
        }
    }

    echo json_encode(['success' => true, 'orders' => $orders]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
