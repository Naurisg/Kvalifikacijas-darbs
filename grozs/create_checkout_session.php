<?php
require '../vendor/autoload.php';
require_once '../db_connect.php';
session_start();

header('Content-Type: application/json');

// Autorizācijas pārbaude
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Neautorizēts pieprasījums.']);
    exit();
}

// Stripe slepenā atslēga (izmanto savu)
\Stripe\Stripe::setApiKey('sk_test_51QP0wYHs6AycTP1yyPSwfq6pYdkUGT9w6yLf2gsZdEsgfIxnsTqkwRJnqZZoF1H4f42axHvNyqHIj7enkqtMEp1100Zzk0WPsE');

try {
    // Groza dati no sesijas
    $cart = $_SESSION['cart'] ?? [];

    if (empty($cart)) {
        throw new Exception('Grozs ir tukšs.');
    }

    // Saņem adreses informāciju no fetch POST
    $rawData = file_get_contents('php://input');
    $addressData = json_decode($rawData, true);

    if (!$addressData) {
        throw new Exception('Nederīga adreses informācija.');
    }

    // Stripe line_items sagatavošana
    $lineItems = [];
    $totalPrice = 0;

    foreach ($cart as $product) {
        $name = $product['nosaukums'] ?? 'Nezināms produkts';
        $cena = isset($product['cena']) ? (float)$product['cena'] : 0.0;
        $quantity = isset($product['quantity']) ? (int)$product['quantity'] : 1;

        if ($cena <= 0 || $quantity <= 0) {
            throw new Exception("Nederīga cena vai daudzums priekš: $name");
        }

        $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $name,
                ],
                'unit_amount' => (int) round($cena * 100),
            ],
            'quantity' => $quantity,
        ];

        $totalPrice += $cena * $quantity;
    }

    // PVN un piegāde
    $pvn = $totalPrice * 0.21;
    $delivery = ($totalPrice >= 100) ? 0 : 10;
    $finalTotal = $totalPrice + $pvn + $delivery;

    if ($pvn > 0) {
        $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => ['name' => 'PVN (21%)'],
                'unit_amount' => (int) round($pvn * 100),
            ],
            'quantity' => 1,
        ];
    }

    if ($delivery > 0) {
        $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => ['name' => 'Piegādes maksa'],
                'unit_amount' => (int) round($delivery * 100),
            ],
            'quantity' => 1,
        ];
    }

    // Stripe Checkout sesijas izveide
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => 'http://localhost/Vissdarbam/grozs/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost/Vissdarbam/grozs/adress.php',
        'metadata' => [
            'name' => $addressData['name'] ?? '',
            'email' => $addressData['email'] ?? '',
            'phone' => $addressData['phone'] ?? '',
            'address' => $addressData['address'] ?? '',
            'address2' => $addressData['address2'] ?? '',
            'city' => $addressData['city'] ?? '',
            'postal_code' => $addressData['postal_code'] ?? '',
            'country' => $addressData['country'] ?? '',
            'notes' => $addressData['notes'] ?? '',
            'kopēja_cena' => number_format($finalTotal, 2)
        ]
    ]);

    echo json_encode(['id' => $session->id]);
} catch (Exception $e) {
    // Logē kļūdu uz failu (lokālai izstrādei)
    file_put_contents('stripe_error_log.txt', $e->getMessage() . PHP_EOL, FILE_APPEND);
    echo json_encode(['error' => 'Kļūda izveidojot maksājumu.']);
}
?>
