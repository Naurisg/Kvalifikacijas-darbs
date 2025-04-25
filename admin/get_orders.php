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
                $items = [];
                if (isset($order['items'])) {
                    if (is_string($order['items'])) {
                        $items = json_decode($order['items'], true) ?: [];
                    } else if (is_array($order['items'])) {
                        $items = $order['items'];
                    }
                }

                $orders[] = [
                    'id' => $order['order_id'] ?? 'N/A',
                    'client_name' => $row['client_name'],
                    'products' => $items, 
                    'total_price' => $order['total_amount'] ?? 0,
                    'date' => $order['created_at'] ?? 'N/A',
                    'status' => $order['status'] ?? 'Pending'
                ];
            }
        }
    }

    // Kārto pasūtījumus pēc datuma dilstošā secībā (jaunākie vispirms)
    usort($orders, function($a, $b) {
        return strtotime($b['date']) <=> strtotime($a['date']);
    });

    echo json_encode(['success' => true, 'orders' => $orders]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>