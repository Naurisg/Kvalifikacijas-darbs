<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo '<h1>Unauthorized</h1>';
    echo '<p>Please log in to complete your purchase.</p>';
    exit();
}

try {
    $db = new PDO('sqlite:../Datubazes/client_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // iegust lietotaja datus
    $stmt = $db->prepare('SELECT cart, orders FROM clients WHERE id = :user_id');
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('User not found.');
    }

    $cart = $user['cart'] ? json_decode($user['cart'], true) : [];
    if (empty($cart)) {
        throw new Exception('Cart is empty.');
    }

    $orders = $user['orders'] ? json_decode($user['orders'], true) : [];

    // apreķinaja kopejo summu
    $totalAmount = 0;
    foreach ($cart as $product) {
        $price = isset($product['cena']) ? floatval($product['cena']) : 0;
        $quantity = isset($product['quantity']) ? intval($product['quantity']) : 1;
        $totalAmount += $price * $quantity;
    }

    // ģenere unikālu pasūtijuma id
    $orderId = uniqid('order_');

    // izveido jaunu pasūtijumu
    $newOrder = [
        'order_id' => $orderId,
        'items' => $cart,
        'total_amount' => $totalAmount,
        'created_at' => date('Y-m-d H:i:s'),
        'status' => 'Pending'
    ];

    $orders[] = $newOrder;

    // Atjaunina pasūtijumus un notīra grozu datubāzē
    $updateStmt = $db->prepare('UPDATE clients SET orders = :orders, cart = :cart WHERE id = :user_id');
    $updateStmt->execute([
        ':orders' => json_encode($orders),
        ':cart' => json_encode([]),
        ':user_id' => $_SESSION['user_id']
    ]);

    echo '<h1>Maksājums veiksmīgs!</h1>';
    echo '<p>Paldies par pirkumu. Jūsu pasūtījums tiks apstrādāts.</p>';
} catch (Exception $e) {
    echo '<h1>Kļūda</h1>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>
