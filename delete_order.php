<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['order_id'] ?? null;

if (!$orderId) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit();
}

try {
    $clientDb = new PDO('sqlite:Datubazes/client_signup.db');
    $clientDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $clientDb->prepare('SELECT orders FROM clients WHERE id = :user_id');
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $orders = $stmt->fetchColumn();
    $orders = $orders ? json_decode($orders, true) : [];

    $updatedOrders = array_filter($orders, function ($order) use ($orderId) {
        return $order['order_id'] !== $orderId;
    });

    $updateStmt = $clientDb->prepare('UPDATE clients SET orders = :orders WHERE id = :user_id');
    $updateStmt->execute([
        ':orders' => json_encode(array_values($updatedOrders)),
        ':user_id' => $_SESSION['user_id']
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
