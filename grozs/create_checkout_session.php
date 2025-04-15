<?php
require '../vendor/autoload.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

//\Stripe\Stripe::setApiKey('sk_test_51QP0wYHs6AycTP1yyPSwfq6pYdkUGT9w6yLf2gsZdEsgfIxnsTqkwRJnqZZoF1H4f42axHvNyqHIj7enkqtMEp1100Zzk0WPsE'); // Replace with your secret key

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

    echo json_encode(['id' => $session->id]);
} catch (Exception $e) {
    error_log('Stripe Checkout Error: ' . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
?>
