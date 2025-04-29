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

    // Fetch cart from the database
    $stmt = $clientDb->prepare('SELECT cart FROM clients WHERE id = :user_id');
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $cart = $result['cart'] ? json_decode($result['cart'], true) : [];

    if (empty($cart)) {
        throw new Exception('Cart is empty.');
    }

    // Get raw POST data and decode JSON for address
    $rawData = file_get_contents('php://input');
    $addressData = json_decode($rawData, true);

    if (!$addressData) {
        throw new Exception('Invalid address data.');
    }

    // Prepare Stripe line items
    $lineItems = [];
    foreach ($cart as $product) {
        $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $product['nosaukums'],
                ],
                'unit_amount' => $product['cena'] * 100, // Convert to cents
            ],
            'quantity' => $product['quantity'] ?? 1,
        ];
    }

    // Create Stripe checkout session with address metadata
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => 'http://localhost/Vissdarbam/grozs/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost/Vissdarbam/grozs/adress.php',
        'metadata' => [
            'name' => $addressData['name'] ?? 'Nav norādīts',
            'email' => $addressData['email'] ?? 'Nav norādīts',
            'phone' => $addressData['phone'] ?? 'Nav norādīts',
            'address' => $addressData['address'] ?? 'Nav norādīts',
            'address2' => $addressData['address2'] ?? '',
            'city' => $addressData['city'] ?? 'Nav norādīts',
            'postal_code' => $addressData['postal_code'] ?? 'Nav norādīts',
            'country' => $addressData['country'] ?? 'Nav norādīts',
            'notes' => $addressData['notes'] ?? ''
        ]
    ]);

    echo json_encode(['id' => $session->id]);
} catch (Exception $e) {
    error_log('Stripe Checkout Error: ' . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
?>