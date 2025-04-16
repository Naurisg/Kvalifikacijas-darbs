<?php
header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:../Datubazes/client_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->query('SELECT id, name AS client_name, orders FROM clients WHERE orders IS NOT NULL');
    $orders = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $clientOrders = json_decode($row['orders'], true);

        // Debugging: Log the raw and decoded orders
        error_log("Raw orders for client {$row['client_name']}: " . $row['orders']);
        error_log("Decoded orders: " . print_r($clientOrders, true));

        if (is_array($clientOrders)) {
            foreach ($clientOrders as $order) {
                $products = is_array($order['items']) ? $order['items'] : json_decode($order['items'], true);
                $orders[] = [
                    'id' => $order['order_id'],
                    'client_name' => $row['client_name'],
                    'products' => $products,
                    'total_price' => $order['total_amount'],
                    'date' => $order['created_at'],
                    'status' => $order['status'] ?? 'Pending'
                ];
            }
        }
    }

    echo json_encode(['success' => true, 'orders' => $orders]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
