<?php
require '../vendor/autoload.php';
require_once '../db_connect.php';
session_start();

header('Content-Type: application/json');

// Autorizācijas pārbaude - vai lietotājs ir ielogojies
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Neautorizēts pieprasījums.']);
    exit();
}

// Stripe slepenā atslēga
\Stripe\Stripe::setApiKey('sk_test_51QP0wYHs6AycTP1yyPSwfq6pYdkUGT9w6yLf2gsZdEsgfIxnsTqkwRJnqZZoF1H4f42axHvNyqHIj7enkqtMEp1100Zzk0WPsE');

try {
    // Dinamiski izveido bāzes URL priekš pāradresācijām
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseUrl = $scheme . '://' . $host . '/Vissdarbam';

    // Groza dati no sesijas
    $cart = $_SESSION['cart'] ?? [];

    // Ja grozs ir tukšs, izmet kļūdu
    if (empty($cart)) {
        throw new Exception('Grozs ir tukšs.');
    }

    // Papildina groza produktus ar datiem no datubāzes
    $enrichedCart = [];
    foreach ($cart as $item) {
        $stmtProd = $pdo->prepare('SELECT nosaukums, cena FROM products WHERE id = :id');
        $stmtProd->execute([':id' => $item['id']]);
        $productDetails = $stmtProd->fetch(PDO::FETCH_ASSOC);
        if ($productDetails) {
            $enrichedItem = array_merge($item, $productDetails);
            $enrichedCart[] = $enrichedItem;
        } else {
            // Ja produkts nav atrasts, pievieno ar noklusējuma vērtībām
            $enrichedCart[] = array_merge($item, ['nosaukums' => 'Nezināms produkts', 'cena' => 0]);
        }
    }
    $cart = $enrichedCart;

    // Saņem adreses informāciju no fetch POST (JSON formātā)
    $rawData = file_get_contents('php://input');
    $addressData = json_decode($rawData, true);

    // Pārbauda, vai adreses dati ir korekti
    if (!$addressData) {
        throw new Exception('Nederīga adreses informācija.');
    }

    // Stripe line_items sagatavošana (katram groza produktam)
    $lineItems = [];
    $totalPrice = 0;

    foreach ($cart as $product) {
        $name = $product['nosaukums'] ?? 'Nezināms produkts';
        $cena = isset($product['cena']) ? (float)$product['cena'] : 0.0;
        $quantity = isset($product['quantity']) ? (int)$product['quantity'] : 1;

        // Pārbauda, vai cena un daudzums ir derīgi
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

    // PVN un piegādes aprēķins
    $pvn = $totalPrice * 0.21;
    $delivery = ($totalPrice >= 100) ? 0 : 10;
    $finalTotal = $totalPrice + $pvn + $delivery;

    // Pievieno PVN kā atsevišķu rindu Stripe grozā
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

    // Pievieno piegādes maksu kā atsevišķu rindu Stripe grozā
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

    // Stripe Checkout sesijas izveide ar visiem datiem
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => $baseUrl . '/grozs/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $baseUrl . '/grozs/adress.php',
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

    // Atgriež Stripe sesijas ID klientam
    echo json_encode(['id' => $session->id]);
} catch (Exception $e) {
    // Logē kļūdu uz failu (lokālai izstrādei)
    file_put_contents('stripe_error_log.txt', $e->getMessage() . PHP_EOL, FILE_APPEND);
    echo json_encode(['error' => 'Kļūda izveidojot maksājumu.']);
}
?>
