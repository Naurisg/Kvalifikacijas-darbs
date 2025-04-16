<?php
require '../vendor/autoload.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

\Stripe\Stripe::setApiKey('sk_test_51QP0wYHs6AycTP1yyPSwfq6pYdkUGT9w6yLf2gsZdEsgfIxnsTqkwRJnqZZoF1H4f42axHvNyqHIj7enkqtMEp1100Zzk0WPsE'); // Replace with your secret key

try {
    $clientDb = new PDO('sqlite:../Datubazes/client_signup.db');
    $clientDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $clientDb->prepare('SELECT cart FROM clients WHERE id = :user_id');
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $cart = $result['cart'] ? json_decode($result['cart'], true) : [];
    if (empty($cart)) {
        throw new Exception('Cart is empty.');
    }

    $lineItems = [];
    foreach ($cart as $product) {
        if (!isset($product['nosaukums'], $product['cena'])) {
            throw new Exception('Invalid product data in cart.');
        }

        $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $product['nosaukums'],
                ],
                'unit_amount' => $product['cena'] * 100, // Pārkonvertē uz centiem
            ],
            'quantity' => $product['quantity'] ?? 1,
        ];
    }

    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => 'http://localhost/Vissdarbam/grozs/success.php',
        'cancel_url' => 'http://localhost/Vissdarbam/grozs/adress.php',
    ]);

    // Clear the cart after successful session creation
    $_SESSION['cart'] = [];
    $updateStmt = $clientDb->prepare('UPDATE clients SET cart = :cart WHERE id = :user_id');
    $updateStmt->execute([
        ':cart' => json_encode([]),
        ':user_id' => $_SESSION['user_id']
    ]);

    // Generate a unique order_id
    do {
        $orderId = bin2hex(random_bytes(8)); // Generate a random 16-character hexadecimal string
        $checkStmt = $clientDb->prepare('SELECT COUNT(*) FROM clients WHERE orders LIKE :order_id');
        $checkStmt->execute([':order_id' => '%' . $orderId . '%']);
        $exists = $checkStmt->fetchColumn() > 0;
    } while ($exists);

    // Save the order in the "orders" column
    $orderDetails = [
        'order_id' => $orderId,
        'items' => $result['cart'],
        'total_amount' => $session->amount_total / 100, // Convert from cents to EUR
        'created_at' => date('Y-m-d H:i:s')
    ];
    $ordersStmt = $clientDb->prepare('SELECT orders FROM clients WHERE id = :user_id');
    $ordersStmt->execute([':user_id' => $_SESSION['user_id']]);
    $existingOrders = $ordersStmt->fetchColumn();

    $orders = $existingOrders ? json_decode($existingOrders, true) : [];
    $orders[] = $orderDetails;

    $updateOrdersStmt = $clientDb->prepare('UPDATE clients SET orders = :orders WHERE id = :user_id');
    $updateOrdersStmt->execute([
        ':orders' => json_encode($orders),
        ':user_id' => $_SESSION['user_id']
    ]);

    echo json_encode(['id' => $session->id]);
} catch (Exception $e) {
    error_log('Stripe Checkout Error: ' . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
?>