<?php
session_start();
include '../header.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

try {
    $clientDb = new PDO('sqlite:../Datubazes/client_signup.db'); 
    $clientDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $clientDb->prepare('SELECT cart FROM clients WHERE id = :user_id');
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $cart = $result['cart'] ? json_decode($result['cart'], true) : [];
    
    if (empty($cart)) {
        header('Location: grozs.php');
        exit();
    }
    
    $totalPrice = 0;
    foreach ($cart as $product) {
        $totalPrice += $product['cena'] * ($product['quantity'] ?? 1);
    }
} catch (PDOException $e) {
    echo '<p>Kļūda ielādējot grozu: ' . htmlspecialchars($e->getMessage()) . '</p>';
    exit();
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
        <form id="addressForm" action="payment.php" method="POST">
          <div class="form-row">
            <div class="form-group">
              <label for="firstname">Vārds *</label>
              <input type="text" id="firstname" name="firstname" required>
            </div>
            <div class="form-group">
              <label for="lastname">Uzvārds *</label>
              <input type="text" id="lastname" name="lastname" required>
            </div>
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
            <label for="address2">Dzīvoklis, istaba, utt. (nav obligāts)</label>
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
            <input type="text" id="country" name="country" required >
          </div>
          
          <div class="form-group">
            <label for="notes">Piezīmes pie pasūtījuma (nav obligāts)</label>
            <textarea id="notes" name="notes" rows="3"></textarea>
          </div>
          
          <div class="button-group">
            <button type="button" class="back-button" onclick="window.location.href='grozs.php'">Atpakaļ uz grozu</button>
            <button type="submit" class="checkout-button">Turpināt uz maksājumu</button>
            <div class="secure-checkout">
              <img src="../images/favicon.png" alt="Secure Payment">
              <span>Drošs maksājums</span>
            </div>
          </div>
        </form>
      </div>
      
      <div class="order-summary">
        <h3>Jūsu pasūtījums</h3>
        <ul>
          <?php foreach ($cart as $product): ?>
            <li class="cart-item">
              <img src="../<?php echo htmlspecialchars($product['bilde']); ?>" alt="<?php echo htmlspecialchars($product['nosaukums']); ?>">
              <div class="cart-item-details">
                <h3><?php echo htmlspecialchars($product['nosaukums']); ?></h3>
                <p>Cena: €<?php echo htmlspecialchars($product['cena']); ?></p>
                <p>Daudzums: <?php echo htmlspecialchars($product['quantity'] ?? 1); ?></p>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
        
        <div class="order-total">
          <p>Kopējā summa: €<?php echo number_format($totalPrice, 2); ?></p>
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
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const userData = {
        firstname: "<?php echo isset($_SESSION['user_firstname']) ? $_SESSION['user_firstname'] : ''; ?>",
        lastname: "<?php echo isset($_SESSION['user_lastname']) ? $_SESSION['user_lastname'] : ''; ?>",
        email: "<?php echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''; ?>",
        phone: "<?php echo isset($_SESSION['user_phone']) ? $_SESSION['user_phone'] : ''; ?>"
      };
      
      if (userData.firstname) document.getElementById('firstname').value = userData.firstname;
      if (userData.lastname) document.getElementById('lastname').value = userData.lastname;
      if (userData.email) document.getElementById('email').value = userData.email;
      if (userData.phone) document.getElementById('phone').value = userData.phone;
    });
  </script>
</body>
</html>