<?php
header('Content-Type: application/json');

try {
    // Iekļauj datubāzes savienojumu no db_connect.php
    require_once '../db_connect.php';

    // Atlasa visus klientus, kuriem ir pasūtījumi (orders nav NULL)
    $stmt = $pdo->query('SELECT id, name AS client_name, orders FROM clients WHERE orders IS NOT NULL');
    $orders = [];

    // Caur katru klientu apstrādā viņa pasūtījumus
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $clientOrders = json_decode($row['orders'], true);

        if (is_array($clientOrders)) {
            foreach ($clientOrders as $order) {
                $items = [];
                // Apstrādā preču sarakstu katram pasūtījumam
                if (isset($order['items'])) {
                    if (is_string($order['items'])) {
                        $items = json_decode($order['items'], true) ?: [];
                    } else if (is_array($order['items'])) {
                        $items = $order['items'];
                    }
                }

                // Sagatavo pasūtījuma informāciju gala masīvam
                $orders[] = [
                    'id' => $order['order_id'] ?? 'N/A',
                    'client_name' => $row['client_name'],
                    'products' => $items, 
                    'total_price' => $order['total_amount'] ?? 0,
                    'date' => $order['created_at'] ?? 'N/A',
                    'status' => $order['status'] ?? 'Pending',
                    'address' => $order['address'] ?? null
                ];
            }
        }
    }

    // Kārto pasūtījumus pēc datuma dilstošā secībā (jaunākie vispirms)
    usort($orders, function($a, $b) {
        return strtotime($b['date']) <=> strtotime($a['date']);
    });

    // Atgriež pasūtījumu sarakstu JSON formātā
    echo json_encode(['success' => true, 'orders' => $orders]);
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
