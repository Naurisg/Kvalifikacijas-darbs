<?php
require '../vendor/autoload.php';
require_once '../db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo '<div style="display:flex; flex-direction: column; justify-content: center; align-items: center; height: 100vh; text-align: center;">';
    echo '<h1>Unauthorized</h1>';
    echo '<p>Lūdzu, piesakieties, lai pabeigtu pirkumu.</p>';
    echo '</div>';
    exit();
}

if (!isset($_GET['session_id'])) {
    echo '<div style="display:flex; flex-direction: column; justify-content: center; align-items: center; height: 100vh; text-align: center;">';
    echo '<h1>Pieeja nav atļauta</h1>';
    echo '<p>Nav atrasta neviena maksājuma sesija. Lūdzu, pabeidziet pirkumu, izmantojot norēķinu procesu.</p>';
    echo '</div>';
    exit();
}

\Stripe\Stripe::setApiKey('sk_test_51QP0wYHs6AycTP1yyPSwfq6pYdkUGT9w6yLf2gsZdEsgfIxnsTqkwRJnqZZoF1H4f42axHvNyqHIj7enkqtMEp1100Zzk0WPsE'); // Aizstājiet ar savu slepeno atslēgu

try {
    $sessionId = $_GET['session_id'];
    $stripe = new \Stripe\StripeClient('sk_test_51QP0wYHs6AycTP1yyPSwfq6pYdkUGT9w6yLf2gsZdEsgfIxnsTqkwRJnqZZoF1H4f42axHvNyqHIj7enkqtMEp1100Zzk0WPsE');
    $session = $stripe->checkout->sessions->retrieve($sessionId);

    // Pārbauda, vai maksājums ir veikts
    if ($session->payment_status !== 'paid') {
        throw new Exception('Maksājums nav pabeigts. Lūdzu, aizpildiet maksājumu, lai turpinātu.');
    }

    // Iegūst lietotāja datus
    $stmt = $pdo->prepare('SELECT cart, orders FROM clients WHERE id = :user_id');
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('Lietotājs nav atrasts.');
    }

    $cart = $user['cart'] ? json_decode($user['cart'], true) : [];
    if (empty($cart)) {
        throw new Exception('Grozs ir tukšs.');
    }

    $orders = $user['orders'] ? json_decode($user['orders'], true) : [];

    // Pievieno groza vienumiem informāciju no produktu tabulas
    $enrichedCart = [];
    foreach ($cart as $item) {
        $stmtProd = $pdo->prepare('SELECT nosaukums, cena, bilde FROM products WHERE id = :id');
        $stmtProd->execute([':id' => $item['id']]);
        $productDetails = $stmtProd->fetch(PDO::FETCH_ASSOC);

        if ($productDetails) {
            $enrichedCart[] = array_merge($item, $productDetails);
        } else {
            $enrichedCart[] = $item; // Noklusējuma gadījums, kuram nevajadzētu rasties
        }
    }
    $cart = $enrichedCart;

    // Iegūst adreses datus no sesijas metadatiem
    $address = [
        'name' => $session->metadata->name,
        'email' => $session->metadata->email,
        'phone' => $session->metadata->phone,
        'address' => $session->metadata->address,
        'address2' => $session->metadata->address2,
        'city' => $session->metadata->city,
        'postal_code' => $session->metadata->postal_code,
        'country' => $session->metadata->country,
        'notes' => $session->metadata->notes
    ];

    // Aprēķina kopējo summu
    $totalAmount = 0;
    foreach ($cart as $product) {
        $price = isset($product['cena']) ? floatval($product['cena']) : 0;
        $quantity = isset($product['quantity']) ? intval($product['quantity']) : 1;
        $totalAmount += $price * $quantity;
    }

    // Izmanto faktiski samaksāto summu no Stripe sesijas (amount_total centos)
    $paidAmount = isset($session->amount_total) ? $session->amount_total / 100 : $totalAmount;

    // Ģenerē unikālu pasūtījuma ID
    $orderId = uniqid('order_');

    // Atjaunina produktu daudzumu datubāzē pēc veiksmīga maksājuma
    // Izmanto PDO, lai droši atjauninātu datubāzi
    foreach ($cart as $product) {
        $productId = isset($product['id']) ? $product['id'] : null;
        $quantityBought = isset($product['quantity']) ? intval($product['quantity']) : 0;

        if ($productId && $quantityBought > 0) {
            // Iegūst pašreizējo daudzumu
            $stmt = $pdo->prepare("SELECT quantity FROM products WHERE id = :id");
            $stmt->execute([':id' => $productId]);
            $currentProduct = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($currentProduct) {
                $currentQuantity = intval($currentProduct['quantity']);
                $newQuantity = max(0, $currentQuantity - $quantityBought);

                // Atjaunina daudzumu
                $updateStmt = $pdo->prepare("UPDATE products SET quantity = :quantity WHERE id = :id");
                $updateStmt->execute([
                    ':quantity' => $newQuantity,
                    ':id' => $productId
                ]);
            }
        }
    }

    // Izveido jaunu pasūtījumu
    $newOrder = [
        'order_id' => $orderId,
        'items' => $cart,
        'total_amount' => $paidAmount,
        'created_at' => date('Y-m-d H:i:s'),
        'status' => 'Pending',
        'address' => $address
    ];

    $orders[] = $newOrder;

    // Atjauno pasūtījumus un notīra grozu datubāzē
    $updateStmt = $pdo->prepare('UPDATE clients SET orders = :orders, cart = :cart WHERE id = :user_id');
    $updateStmt->execute([
        ':orders' => json_encode($orders),
        ':cart' => json_encode([]),
        ':user_id' => $_SESSION['user_id']
    ]);

    // Notīra sesijas grozu
    unset($_SESSION['cart']);

    echo '<!DOCTYPE html>
    <html lang="lv">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maksājums veiksmīgs</title>
    <style>
      body {
        margin: 0;
        padding: 0;
        background: #f8f8f8;
        font-family: Inter, Arial, sans-serif;
      }
      .success-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        text-align: center;
        background: #f8f8f8;
      }
      .success-box {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        padding: 40px 50px;
        max-width: 400px;
        width: 90vw;
        margin: 20px;
      }
      .success-box h1 {
        color: #4CAF50;
        font-size: 2.2em;
        margin-bottom: 10px;
      }
      .success-box p {
        color: #333;
        font-size: 1.1em;
        margin: 10px 0;
      }
      .success-box strong {
        color: #222;
      }
      @media (max-width: 600px) {
        .success-box {
          padding: 20px 10px;
          font-size: 1em;
        }
        .success-box h1 {
          font-size: 1.4em;
        }
        .success-box p {
          font-size: 1em;
        }
      }
    </style>
    </head>
    <body>
      <div class="success-container">
        <div class="success-box">
          <h1>Maksājums veiksmīgs!</h1>
          <p>Paldies par pirkumu. Jūsu pasūtījums tiks apstrādāts.</p>
          <p>Jūs tiksiet novirzīts pēc <span id="countdown">3</span> sekundēm.</p>
          <p><strong>Lūdzu neaizveriet šo lapu</strong></p>
        </div>
      </div>
      <script>
        var countdownElement = document.getElementById("countdown");
        var countdown = 3;
        var interval = setInterval(function() {
            countdown--;
            if (countdown <= 0) {
                clearInterval(interval);
                window.location.href = "../order-history.php";
            } else {
                countdownElement.textContent = countdown;
            }
        }, 1000);
      </script>
    </body>
    </html>';
} catch (Exception $e) {
    echo '<!DOCTYPE html>
    <html lang="lv">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kļūda</title>
    <style>
      body {
        margin: 0;
        padding: 0;
        background: #f8f8f8;
        font-family: Inter, Arial, sans-serif;
      }
      .success-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        text-align: center;
        background: #f8f8f8;
      }
      .success-box {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        padding: 40px 50px;
        max-width: 400px;
        width: 90vw;
        margin: 20px;
      }
      .success-box h1 {
        color: #d32f2f;
        font-size: 2.2em;
        margin-bottom: 10px;
      }
      .success-box p {
        color: #333;
        font-size: 1.1em;
        margin: 10px 0;
      }
      @media (max-width: 600px) {
        .success-box {
          padding: 20px 10px;
          font-size: 1em;
        }
        .success-box h1 {
          font-size: 1.4em;
        }
        .success-box p {
          font-size: 1em;
        }
      }
    </style>
    </head>
    <body>
      <div class="success-container">
        <div class="success-box">
          <h1>Kļūda</h1>
          <p>' . htmlspecialchars($e->getMessage()) . '</p>
        </div>
      </div>
    </body>
    </html>';
}
?>
