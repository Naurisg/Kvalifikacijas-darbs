<?php
session_start();
include '../header.php';
require_once '../db_connect.php';

// Pārbauda, vai lietotājs ir autorizējies (ir user_id sesijā)
if (!isset($_SESSION['user_id'])) {
    echo '<p>Lūdzu, piesakieties, lai apskatītu savu grozu.</p>';
    exit();
}

try {
    // Iegūst lietotāja grozu no datubāzes
    $stmt = $pdo->prepare('SELECT cart FROM clients WHERE id = :user_id');
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Ja grozs nav tukšs, pārvērš to par masīvu
    $cart = $result['cart'] ? json_decode($result['cart'], true) : [];

    // Pievieno groza vienumiem informāciju no produktu tabulas
    $enrichedCart = [];
    $totalPrice = 0;

    $soldOutExists = false; // Pārbaude, vai kāds produkts ir izpārdots

    // Izrēķina katra produkta kopējo daudzumu grozā (ja ir vairākas rindas ar vienu produktu)
    $productTotals = [];
    foreach ($cart as $item) {
        $productTotals[$item['id']] = ($productTotals[$item['id']] ?? 0) + ($item['quantity'] ?? 1);
    }

    $enrichedCart = [];
    foreach ($cart as $index => $item) {
        // Iegūst produkta detaļas no datubāzes
        $stmtProd = $pdo->prepare('SELECT nosaukums, cena, bilde, sizes, quantity AS stock_quantity FROM products WHERE id = :id');
        $stmtProd->execute([':id' => $item['id']]);
        $productDetails = $stmtProd->fetch(PDO::FETCH_ASSOC);

        if ($productDetails) {
            $quantityInCart = $item['quantity'] ?? 1;
            $totalForThisProduct = $productTotals[$item['id']];
            $availableStock = $productDetails['stock_quantity'];

            // Izrēķina maksimālo daudzumu šai rindai, ņemot vērā pieejamo krājumu
            $otherLines = $totalForThisProduct - $quantityInCart;
            $maxForThisLine = max(1, $availableStock - $otherLines);

            // Izpārdots, ja pieejamais krājums ir mazāks vai vienāds ar 0, vai arī kopējais daudzums šim produktam pārsniedz pieejamo krājumu
            $isSoldOut = ($availableStock <= 0) || ($totalForThisProduct > $availableStock);

            if ($isSoldOut) {
                $soldOutExists = true;
            }

            // Apvieno groza datus ar produktu datiem
            $mergedItem = array_merge($item, $productDetails);
            $mergedItem['sold_out'] = $isSoldOut;
            $mergedItem['max_for_this_line'] = $maxForThisLine;
            $enrichedCart[] = $mergedItem;
            $totalPrice += $productDetails['cena'] * $quantityInCart;
        }
    }
    $cart = $enrichedCart;
    $_SESSION['cart'] = $cart; // Atjaunina grozu sesijā
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo '<p>Kļūda ielādējot grozu: ' . htmlspecialchars($e->getMessage()) . '</p>';
    exit();
}

$totalItems = count($cart);
?>
<!DOCTYPE html>
<html lang="lv">
<head>
  <meta charset="utf-8">
  <title>Jūsu Grozs</title>
  <meta content="Jūsu Grozs" property="og:title">
  <meta content="Jūsu Grozs" property="twitter:title">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="../css/normalize.css" rel="stylesheet" type="text/css"> 
  <link href="../css/main.css" rel="stylesheet" type="text/css">
  <link href="../css/style.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
  <script type="text/javascript">
    WebFont.load({
      google: {
        families: ["Inter:regular,500,600,700", "Libre Baskerville:regular,italic,700", "Volkhov:regular,italic,700,700italic", "Noto Serif:regular,italic,700,700italic"]
      }
    });
  </script>
  <link href="../images/favicon.png" rel="shortcut icon" type="image/x-icon"> 
  <link href="../images/webclip.png" rel="apple-touch-icon">
  <meta name="robots" content="noindex">
  <style>
    .cart-container {
      max-width: 1200px;
      margin: 50px auto;
      padding: 20px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .cart-header {
      text-align: center;
      margin-bottom: 20px;
    }

    .cart-header h2 {
      font-size: 28px;
      font-weight: bold;
      color: #333;
    }

    .cart-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .cart-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 15px;
      border-bottom: 1px solid #ddd;
    }

    .cart-item img {
      width: 100px;
      height: 100px;
      object-fit: contain;
      border-radius: 8px;
      background: #f5f5f5;
    }

    .cart-item-details {
      flex: 1;
      margin-left: 20px;
    }

    .cart-item-details h3 {
      font-size: 18px;
      font-weight: bold;
      margin: 0;
      color: #333;
    }

    .cart-item-details p {
      margin: 5px 0;
      font-size: 16px;
      color: #555;
    }

    .cart-item-details .size, .cart-item-details .quantity {
      font-size: 14px;
      color: #777;
    }

    .cart-empty {
      text-align: center;
      font-size: 18px;
      color: #777;
      margin: 50px 0;
    }

    .remove-button {
      background: url('../images/delete.png') no-repeat center center;
      background-size: contain;
      width: 24px;
      height: 24px;
      border: none;
      cursor: pointer;
      transition: transform 0.3s;
    }

    .remove-button:hover {
      transform: scale(1.2);
    }

    .cart-total {
      text-align: right;
      font-size: 18px;
      font-weight: bold;
      margin-top: 20px;
      color: #333;
    }

    .checkout-button {
      display: inline-block;
      margin-top: 10px;
      padding: 10px 20px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s;
    }

    .checkout-button:hover {
      background-color: #45a049;
    }

    .quantity-container {
      display: flex;
      align-items: center;
    }

    .quantity-container input[type="number"] {
      width: 60px;
      padding: 5px;
      font-size: 14px;
      text-align: center;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .clear-cart-button {
      margin-top: 10px;
      padding: 10px 20px;
      background-color: #FFFFFF;
      color: #000000;
      border: 2px solid #000000;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s, color 0.3s;
    }

    .clear-cart-button:hover {
      background-color: #F0F0F0;
      color: #333333;
    }
  </style>
</head>
<body>
  <div class="cart-container">
    <div class="cart-header">
      <h2>Jūsu Grozs</h2>
      <p>Kopējais preču skaits: <?php echo $totalItems; ?></p>
      <?php if ($totalItems > 0): ?>
        <button class="clear-cart-button" onclick="clearCart()">Iztukšot grozu</button>
      <?php endif; ?>
    </div>
    <?php if (!empty($cart)): ?>
      <ul class="cart-list">
        <?php foreach ($cart as $index => $product): ?>
          <?php 
            $images = isset($product['bilde']) ? explode(',', $product['bilde']) : []; 
            $firstImage = !empty($images) ? trim($images[0]) : 'images/placeholder.png';
          ?>
          <li class="cart-item">
            <img src="../<?php echo htmlspecialchars($firstImage); ?>" alt="<?php echo htmlspecialchars($product['nosaukums']); ?>" width="100">
            <div class="cart-item-details">
              <h3><?php echo htmlspecialchars($product['nosaukums']); ?></h3>
              <?php if (!empty($product['sold_out']) && $product['sold_out']): ?>
                <p style="color: red; font-weight: bold;">Izpārdots</p>
              <?php endif; ?>
              <p>Cena: €<?php echo htmlspecialchars($product['cena']); ?></p>
              <p class="size">Izmērs: <?php echo htmlspecialchars($product['size'] ?? 'Nav norādīts'); ?></p>
              <p class="quantity">
                Daudzums:
                <div class="quantity-container">
                  <input type="number"
                         value="<?php echo htmlspecialchars($product['quantity'] ?? 1); ?>"
                         min="1"
                         max="<?php echo htmlspecialchars($product['max_for_this_line']); ?>"
                         onchange="updateQuantity(<?php echo $index; ?>, this.value)"
                         <?php echo (!empty($product['sold_out']) && $product['sold_out']) ? 'disabled' : ''; ?>
                         <?php if (($product['quantity'] ?? 1) >= $product['max_for_this_line']) echo 'style=\"border:2px solid red;\"'; ?>
                  >
                  <?php if (($product['quantity'] ?? 1) >= $product['max_for_this_line']): ?>
                    <span style="color:red;font-size:12px;">Maksimālais daudzums sasniegts</span>
                  <?php endif; ?>
                </div>
              </p>
            </div>
            <button class="remove-button" onclick="removeFromCart(<?php echo $index; ?>)" title="Noņemt"></button>
          </li>
        <?php endforeach; ?>
      </ul>
      <div class="cart-total">
        <div class="cart-total">Preču summa: €<?php echo number_format($totalPrice, 2); ?></div>
        <?php
          // PVN (VAT) 21% no Preču summa
          $pvn = $totalPrice * 0.21;
          $delivery = ($totalPrice >= 100) ? 0 : 10;
          $finalTotal = $totalPrice + $pvn + $delivery;
        ?>
        <div class="cart-total">PVN (21%): €<?php echo number_format($pvn, 2); ?></div>
        <div class="cart-total">
          Piegādes cena: 
          <?php if ($delivery == 0): ?>
            <span style="color:green;font-weight:bold;">Bezmaksas</span>
          <?php else: ?>
            €<?php echo number_format($delivery, 2); ?>
          <?php endif; ?>
        </div>
        <div class="cart-total" style="margin-top:10px;">
          <strong>Kopējā cena: €<?php echo number_format($finalTotal, 2); ?></strong>
          <?php if ($soldOutExists): ?>
            <p style="color: red; font-weight: bold; margin-top: 10px;">Kāds no produktiem kas atrodas grozā ir izpārdots izņemiet to lai turpinātu maksājumu</p>
          <?php endif; ?>
        </div>
        <button class="checkout-button" id="checkoutButton" <?php echo $soldOutExists ? 'disabled style="background-color: grey; cursor: not-allowed;"' : ''; ?> onclick="window.location.href='adress'">Noformēt sūtījumu</button>
      </div>
    <?php else: ?>
      <p class="cart-empty">Grozs ir tukšs.</p>
    <?php endif; ?>
  </div>
  <?php include '../footer.php'; ?>
  <script>
    // Noņem produktu no groza pēc indeksa
    function removeFromCart(index) {
      fetch('remove_from_cart.php', { 
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ index: index }),
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Produkts noņemts no groza!');
          location.reload();
        } else {
          alert(data.message || 'Kļūda noņemot produktu no groza.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Kļūda noņemot produktu no groza.');
      });
    }

    // Pievieno notikumu, lai pārbaudītu daudzuma ievadi
    document.addEventListener('DOMContentLoaded', function() {
      const quantityInputs = document.querySelectorAll('.quantity-container input[type="number"]');
      quantityInputs.forEach(input => {
        input.addEventListener('input', function() {
          const max = parseInt(this.getAttribute('max'), 10);
          let value = parseInt(this.value, 10);
          if (isNaN(value) || value < 1) {
            value = 1;
          }
          if (value > max) {
            value = max;
            alert('Daudzums nevar pārsniegt pieejamo krājumu: ' + max);
          }
          this.value = value;
        });
      });
    });

    function proceedToCheckout() {
      alert('Noformēt sūtījumu funkcionalitāte vēl nav ieviesta.');
    }

    function updateQuantity(index, quantity) {
      fetch('update_quantity.php', { 
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ index: index, quantity: quantity }),
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Daudzums atjaunināts!');
          location.reload();
        } else {
          alert(data.message || 'Kļūda atjauninot daudzumu.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Kļūda atjauninot daudzumu.');
      });
    }

    // Notīra visu grozu
    function clearCart() {
      fetch('clear_cart.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Grozs ir notīrīts!');
          location.reload();
        } else {
          alert(data.message || 'Kļūda notīrot grozu.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Kļūda notīrot grozu.');
      });
    }
  </script>
</body>
</html>