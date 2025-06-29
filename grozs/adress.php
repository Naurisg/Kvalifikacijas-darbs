<?php
session_start();
include '../header.php';

// Ja lietotājs nav autorizējies, pāradresē uz login lapu
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Iegūst bagātināto grozu no sesijas
$cart = $_SESSION['cart'] ?? [];

// Ja grozs ir tukšs — pāradresē atpakaļ uz grozu
if (empty($cart)) {
    header('Location: grozs.php');
    exit();
}

// Lietotāja dati no sesijas vai pēc vajadzības no datubāzes
require '../db_connect.php';

// Pievieno katram groza produktam informāciju no produktu tabulas
$enrichedCart = [];
$totalPrice = 0;
foreach ($cart as $item) {
    // Iegūst produkta detaļas no datubāzes
    $stmtProd = $pdo->prepare('SELECT nosaukums, cena, bilde FROM products WHERE id = :id');
    $stmtProd->execute([':id' => $item['id']]);
    $productDetails = $stmtProd->fetch(PDO::FETCH_ASSOC);

    if ($productDetails) {
        $mergedItem = array_merge($item, $productDetails);
        $enrichedCart[] = $mergedItem;
        $quantity = isset($mergedItem['quantity']) ? (int)$mergedItem['quantity'] : 1;
        $price = isset($mergedItem['cena']) ? (float)$mergedItem['cena'] : 0;
        $totalPrice += $price * $quantity;
    }
}
$cart = $enrichedCart;

// Pievieno lietotāja datus no datubāzes
try {
    $stmt = $pdo->prepare('SELECT name, email, phone FROM clients WHERE id = :user_id');
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $userData = [
        'name' => $result['name'] ?? '',
        'email' => $result['email'] ?? '',
        'phone' => $result['phone'] ?? ''
    ];
} catch (Exception $e) {
    $userData = ['name' => '', 'email' => '', 'phone' => ''];
}
?>


<!DOCTYPE html>
<html lang="lv" data-wf-page="66f12005df0203b01c953ebe" data-wf-site="66f12005df0203b01c953e53">
<head>
  <meta charset="utf-8">
  <title>Piegādes Adrese</title>
  <meta content="Piegādes Adrese" property="og:title">
  <meta content="Piegādes Adrese" property="twitter:title">
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
    .checkout-container {
      max-width: 1200px;
      margin: 50px auto;
      padding: 20px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .checkout-header {
      text-align: center;
      margin-bottom: 20px;
    }

    .checkout-header h2 {
      font-size: 28px;
      font-weight: bold;
      color: #333;
    }

    .checkout-steps {
      display: flex;
      justify-content: space-between;
      margin-bottom: 30px;
      position: relative;
    }

    .checkout-steps:before {
      content: '';
      position: absolute;
      top: 15px;
      left: 0;
      right: 0;
      height: 2px;
      background-color: #ddd;
      z-index: 1;
    }

    .step {
      text-align: center;
      position: relative;
      z-index: 2;
    }

    .step-number {
      width: 30px;
      height: 30px;
      background-color: #ddd;
      color: #777;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 5px;
      font-weight: bold;
    }

    .step.active .step-number {
      background-color: #4CAF50;
      color: white;
    }

    .step.completed .step-number {
      background-color: #4CAF50;
      color: white;
    }

    .step-title {
      font-size: 14px;
      color: #777;
    }

    .step.active .step-title {
      color: #4CAF50;
      font-weight: bold;
    }

    .checkout-content {
      display: flex;
      gap: 30px;
    }

    .address-form {
      flex: 2;
    }

    .order-summary {
      flex: 1;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
    }

    input, select, textarea {
      width: 100%;
      padding: 10px 15px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 16px;
    }

    .form-row {
      display: flex;
      gap: 15px;
    }

    .form-row .form-group {
      flex: 1;
    }

    .checkout-button {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 24px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s;
      width: 100%;
    }

    .checkout-button:hover {
      background-color: #45a049;
    }

    .back-button {
      display: inline-block;
      margin-top: 20px;
      margin-right: 10px;
      padding: 12px 24px;
      background-color: #FFFFFF;
      color: #000000;
      border: 2px solid #000000;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s, color 0.3s;
      width: 100%;
      text-align: center;
    }

    .back-button:hover {
      background-color: #F0F0F0;
      color: #333333;
    }

    .cart-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 15px;
      border-bottom: 1px solid #ddd;
    }

    .cart-item img {
      width: 80px;
      height: 80px;
      object-fit: contain;
      border-radius: 8px;
      background: #f5f5f5;
    }

    .cart-item-details {
      flex: 1;
      margin-left: 15px;
    }

    .cart-item-details h3 {
      font-size: 16px;
      font-weight: bold;
      margin: 0;
      color: #333;
    }

    .cart-item-details p {
      margin: 5px 0;
      font-size: 14px;
      color: #555;
    }

    .order-total {
      text-align: right;
      font-size: 18px;
      font-weight: bold;
      margin-top: 20px;
      color: #333;
    }

    .secure-checkout {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: 15px;
      color: #777;
      font-size: 14px;
    }

    .secure-checkout img {
      margin-right: 10px;
      height: 20px;
    }

    .button-group {
      margin-top: 30px;
    }

    /* Responsivitāte */
    @media (max-width: 600px) {
      .checkout-container {
        margin: 0;
        padding: 5px;
        border-radius: 0;
        max-width: 100vw;
        min-width: 0;
        box-shadow: none;
      }

      .checkout-header {
        margin-bottom: 10px;
        padding: 0 5px;
      }

      .checkout-header h2 {
        font-size: 18px;
        margin: 0;
        padding: 0;
      }

      .checkout-steps {
        flex-direction: row;
        gap: 0;
        margin-bottom: 15px;
        align-items: flex-start;
        overflow-x: auto;
        padding-bottom: 8px;
        scrollbar-width: thin;
      }

      .checkout-steps::-webkit-scrollbar {
        height: 4px;
      }

      .step {
        min-width: 90px;
        flex: 0 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        padding: 0 6px;
      }

      .step-number {
        width: 22px;
        height: 22px;
        font-size: 13px;
        margin-bottom: 2px;
      }

      .step-title {
        font-size: 12px;
        text-align: center;
        line-height: 1.1;
      }

      .checkout-content {
        flex-direction: column;
        gap: 10px;
      }

      .address-form, .order-summary {
        flex: none;
        width: 100%;
        padding: 0;
      }

      .address-form {
        display: flex;
        flex-direction: column;
      }
      .order-summary {
        order: 2;
        margin-bottom: 10px;
      }
      .button-group {
        order: 3;
        margin-top: 0;
      }

      @media (max-width: 600px) {
        .checkout-content {
          display: block;
        }
        .address-form {
          display: block;
        }
        .order-summary {
          margin-top: 0;
          margin-bottom: 10px;
        }
      }
    }
  </style>
</head>
<body>
  <div class="checkout-container">
    <div class="checkout-header">
      <h2>Piegādes informācija</h2>
    </div>
    
    <div class="checkout-steps">
      <div class="step completed">
        <div class="step-number">1</div>
        <div class="step-title">Pirkumu grozs</div>
      </div>
      <div class="step active">
        <div class="step-number">2</div>
        <div class="step-title">Piegādes informācija</div>
      </div>
      <div class="step">
        <div class="step-number">3</div>
        <div class="step-title">Maksājums</div>
      </div>
      <div class="step">
        <div class="step-number">4</div>
        <div class="step-title">Apstiprinājums</div>
      </div>
    </div>
    
    <div class="checkout-content">
      <div class="address-form">
        <form id="addressForm" action="success.php" method="POST">
          <div class="form-group">
            <label for="name">Vārds un uzvārds *</label>
            <input type="text" id="name" name="name" required>
          </div>
          <div class="form-group">
            <label for="email">E-pasts *</label>
            <input type="email" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="phone">Telefona numurs *</label>
            <input type="tel" id="phone" name="phone" required>
          </div>
          <div class="form-group">
            <label for="address">Adrese *</label>
            <input type="text" id="address" name="address" required>
          </div>
          <div class="form-group">
            <label for="address2">Dzīvoklis, nummurs. (nav obligāts)</label>
            <input type="text" id="address2" name="address2">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="city">Pilsēta *</label>
              <input type="text" id="city" name="city" required>
            </div>
            <div class="form-group">
              <label for="postal_code">Pasta indekss *</label>
              <input type="text" id="postal_code" name="postal_code" required>
            </div>
          </div>
          <div class="form-group">
            <label for="country">Valsts *</label>
            <input type="text" id="country" name="country" required>
          </div>
          <div class="form-group">
            <label for="notes">Piezīmes pie pasūtījuma (nav obligāts)</label>
            <textarea id="notes" name="notes" rows="3"></textarea>
          </div>

          <div class="order-summary order-summary-mobile">

            <h3>Jūsu pasūtījums</h3>
            <ul>
              <?php foreach ($cart as $product): ?>
                <?php 
                  $images = isset($product['bilde']) ? explode(',', $product['bilde']) : []; 
                  $firstImage = !empty($images) ? trim($images[0]) : 'images/placeholder.png';
                ?>
                <li class="cart-item">
                  <img src="../<?php echo htmlspecialchars($firstImage); ?>" alt="<?php echo htmlspecialchars($product['nosaukums']); ?>">
                  <div class="cart-item-details">
                    <h3><?php echo htmlspecialchars($product['nosaukums']); ?></h3>
                    <p>Cena: €<?php echo htmlspecialchars($product['cena']); ?></p>
                    <p>Izmērs: <?php echo htmlspecialchars($product['size'] ?? 'Nav norādīts'); ?></p>
                    <p>Daudzums: <?php echo htmlspecialchars($product['quantity'] ?? 1); ?></p>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
            <?php
              $pvn = $totalPrice * 0.21;
              $delivery = ($totalPrice >= 100) ? 0 : 10;
              $finalTotal = $totalPrice + $pvn + $delivery;
            ?>
            <div class="order-total">Preču summa: €<?php echo number_format($totalPrice, 2); ?></div>
            <div class="order-total">PVN (21%): €<?php echo number_format($pvn, 2); ?></div>
            <div class="order-total">
              Piegādes cena: 
              <?php if ($delivery == 0): ?>
                <span style="color:green;font-weight:bold;">Bezmaksas</span>
              <?php else: ?>
                €<?php echo number_format($delivery, 2); ?>
              <?php endif; ?>
            </div>
            <div class="order-total" style="margin-top:10px;">
              <strong>Kopējā cena: €<?php echo number_format($finalTotal, 2); ?></strong>
            </div>
          </div>
          <div class="button-group">
            <button type="button" class="back-button" onclick="window.location.href='grozs.php'">Atpakaļ uz grozu</button>
            <button type="submit" class="checkout-button">Turpināt uz maksājumu</button>
          </div>
        </form>
      </div>
      <div class="order-summary order-summary-desktop">

        <h3>Jūsu pasūtījums</h3>
        <ul>
          <?php foreach ($cart as $product): ?>
            <?php 
              $images = isset($product['bilde']) ? explode(',', $product['bilde']) : []; 
              $firstImage = !empty($images) ? trim($images[0]) : 'images/placeholder.png';
            ?>
            <li class="cart-item">
              <img src="../<?php echo htmlspecialchars($firstImage); ?>" alt="<?php echo htmlspecialchars($product['nosaukums']); ?>">
              <div class="cart-item-details">
                <h3><?php echo htmlspecialchars($product['nosaukums']); ?></h3>
                <p>Cena: €<?php echo htmlspecialchars($product['cena']); ?></p>
                <p>Izmērs: <?php echo htmlspecialchars($product['size'] ?? 'Nav norādīts'); ?></p>
                <p>Daudzums: <?php echo htmlspecialchars($product['quantity'] ?? 1); ?></p>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
        <?php
          $pvn = $totalPrice * 0.21;
          $delivery = ($totalPrice >= 100) ? 0 : 10;
          $finalTotal = $totalPrice + $pvn + $delivery;
        ?>
        <div class="order-total">Preču summa: €<?php echo number_format($totalPrice, 2); ?></div>
        <div class="order-total">PVN (21%): €<?php echo number_format($pvn, 2); ?></div>
        <div class="order-total">
          Piegādes cena: 
          <?php if ($delivery == 0): ?>
            <span style="color:green;font-weight:bold;">Bezmaksas</span>
          <?php else: ?>
            €<?php echo number_format($delivery, 2); ?>
          <?php endif; ?>
        </div>
        <div class="order-total" style="margin-top:10px;">
          <strong>Kopējā cena: €<?php echo number_format($finalTotal, 2); ?></strong>
        </div>
      </div>
    </div>
  </div>
  
  <div class="footer">
    <div class="footer-container w-container">
      <div class="w-layout-grid footer-grid">
        <div id="w-node-b8d7be4a-ce45-83ab-5947-02d204c8bff0-cf3fcb86" class="footerlogobloks">
          <a data-ix="logo" href="../index.html" class="footer-logo w-nav-brand">
            <img src="../images/Logo.png" width="130" sizes="130px" srcset="../images/Logo-p-500.png 500w, ../images/Logo-p-800.png 800w, ../images/Logo.png 960w" alt="">
          </a>
          <p class="text small">
            <strong>Piedāvājam piegādi tajā pašā dienā</strong><br>
            <strong>Tālruņa numurs:</strong> 29 702 132<br>
            <strong>Epasts:</strong> vissdarbam@gmail.com<br>
            <strong>Adrese:</strong> Brīvības iela 56, Liepāja, LV-3401<br><br>
          </p>
        </div>
        <div class="footer-links-container">
          <h5 class="footer-header">Mājas lapa</h5>
          <a href="../index.html" class="footer-link">Sākums</a>
          <a href="../precu-katalogs.html" class="footer-link">Preču Katalogs</a>
          <a href="../par-mums.html" class="footer-link">Logo uzdruka</a>
          <a href="../logo-uzdruka.html" class="footer-link">Par mums</a>
          <a href="../kontakti.html" class="footer-link">Kontakti</a>
        </div>
        <div class="footer-links-container">
          <h5 class="footer-header">Darba Laiki</h5>
          <p class="paragraph-14">
            Pirmdiena 09–17<br>
            Otrdiena 9-17<br>
            Trešdiena 09–17<br>
            Ceturtdiena 09–17<br>
            Piektdiena 09–17<br>
            Sestdiena Slēgts<br>
            Svētdiena Slēgts
          </p>
        </div>
      </div>
    </div>
    <section>
      <div class="text-block-2">© 2024 Majors-J. All Rights Reserved.</div>
    </section>
  </div>
  
  <script src="https://js.stripe.com/v3/"></script>
  <script>
    // Kad lapa ir ielādēta, aizpilda formas laukus ar lietotāja datiem
    document.addEventListener('DOMContentLoaded', function() {
      const userData = {
        name: "<?php echo htmlspecialchars($userData['name']); ?>",
        email: "<?php echo htmlspecialchars($userData['email']); ?>",
        phone: "<?php echo htmlspecialchars($userData['phone']); ?>"
      };

      const nameField = document.getElementById('name');
      const emailField = document.getElementById('email');
      const phoneField = document.getElementById('phone');

      if (nameField && userData.name) nameField.value = userData.name;
      if (emailField && userData.email) emailField.value = userData.email;
      if (phoneField && userData.phone) phoneField.value = userData.phone;
    });

    // Apstrādā piegādes adreses formas iesniegšanu un pāriet uz Stripe maksājumu
    document.getElementById('addressForm').addEventListener('submit', function (e) {
        e.preventDefault();

        // Disable button and show loading
        const submitBtn = document.querySelector('.checkout-button');
        submitBtn.disabled = true;
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Notiek apstrāde...';
        submitBtn.style.opacity = '0.7';
        submitBtn.style.cursor = 'not-allowed';

        // Iegūst formu datus
        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            address: document.getElementById('address').value,
            address2: document.getElementById('address2').value,
            city: document.getElementById('city').value,
            postal_code: document.getElementById('postal_code').value,
            country: document.getElementById('country').value,
            notes: document.getElementById('notes').value
        };

        // Nosūta datus uz serveri, lai izveidotu Stripe checkout sesiju
        fetch('create_checkout_session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            // Ja saņemts Stripe sesijas ID, pāradresē uz maksājumu
            if (data.id) {
                const stripe = Stripe('pk_test_51QP0wYHs6AycTP1yY0zaKnaw3dgfxaiKEX5OWQSuRo4IQzobUkCd3d347FksWLIrzASGinvz1Sdp4VjnWYfDTwW900N5fxwZIx'); //Stripe public key
                stripe.redirectToCheckout({ sessionId: data.id });
            } else {
                alert(data.error || 'Kļūda izveidojot maksājumu.');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                submitBtn.style.opacity = '';
                submitBtn.style.cursor = '';
            }
        })
        .catch(error => {
            // Apstrādā kļūdu gadījumā, ja neizdodas izveidot maksājumu
            console.error('Error:', error);
            alert('Kļūda izveidojot maksājumu.');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            submitBtn.style.opacity = '';
            submitBtn.style.cursor = '';
        });
    });
  </script>
  <style>
    /* Paslēpj arī mobilo izvēlni */
    .order-summary-mobile { display: none; }
    .order-summary-desktop { display: block; }
    @media (max-width: 600px) {
      .order-summary-mobile { display: block; }
      .order-summary-desktop { display: none; }
    }
  </style>
</body>
</html>