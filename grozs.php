<?php
session_start();
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    echo '<p>Lūdzu, piesakieties, lai apskatītu savu grozu.</p>';
    exit();
}

try {
    // Load cart from clients table
    $clientDb = new PDO('sqlite:Datubazes/client_signup.db');
    $clientDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $clientDb->prepare('SELECT cart FROM clients WHERE id = :user_id');
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $cart = $result['cart'] ? json_decode($result['cart'], true) : [];
    $_SESSION['cart'] = $cart; // Sync session cart with database
} catch (PDOException $e) {
    echo '<p>Kļūda ielādējot grozu: ' . htmlspecialchars($e->getMessage()) . '</p>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="lv" data-wf-page="66f12005df0203b01c953ebe" data-wf-site="66f12005df0203b01c953e53">
<head>
  <meta charset="utf-8">
  <title>Jūsu Grozs</title>
  <meta content="Jūsu Grozs" property="og:title">
  <meta content="Jūsu Grozs" property="twitter:title">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="css/normalize.css" rel="stylesheet" type="text/css">
  <link href="css/main.css" rel="stylesheet" type="text/css">
  <link href="css/style.css" rel="stylesheet" type="text/css">
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
  <link href="images/favicon.png" rel="shortcut icon" type="image/x-icon">
  <link href="images/webclip.png" rel="apple-touch-icon">
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

    .cart-empty {
      text-align: center;
      font-size: 18px;
      color: #777;
      margin: 50px 0;
    }

    .remove-button {
      background-color: #f44336;
      color: white;
      border: none;
      padding: 6px 10px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 12px;
      transition: background-color 0.3s;
    }

    .remove-button:hover {
      background-color: #d32f2f;
    }
  </style>
</head>
<body>
  <div class="cart-container">
    <div class="cart-header">
      <h2>Jūsu Grozs</h2>
    </div>
    <?php if (!empty($cart)): ?>
      <ul class="cart-list">
        <?php foreach ($cart as $index => $product): ?>
          <li class="cart-item">
            <img src="/Vissdarbam/<?php echo htmlspecialchars($product['bilde']); ?>" alt="<?php echo htmlspecialchars($product['nosaukums']); ?>" width="100">
            <div class="cart-item-details">
              <h3><?php echo htmlspecialchars($product['nosaukums']); ?></h3>
              <p>Cena: €<?php echo htmlspecialchars($product['cena']); ?></p>
            </div>
            <button class="remove-button" onclick="removeFromCart(<?php echo $index; ?>)">Noņemt</button>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="cart-empty">Grozs ir tukšs.</p>
    <?php endif; ?>
  </div>
  <div class="footer">
    <div class="footer-container w-container">
      <div class="w-layout-grid footer-grid">
        <div id="w-node-b8d7be4a-ce45-83ab-5947-02d204c8bff0-cf3fcb86" class="footerlogobloks">
          <a data-ix="logo" href="index.html" class="footer-logo w-nav-brand">
            <img src="images/Logo.png" width="130" sizes="130px" srcset="images/Logo-p-500.png 500w, images/Logo-p-800.png 800w, images/Logo.png 960w" alt="">
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
          <a href="index.html" class="footer-link">Sākums</a>
          <a href="precu-katalogs.html" class="footer-link">Preču Katalogs</a>
          <a href="par-mums.html" class="footer-link">Logo uzdruka</a>
          <a href="logo-uzdruka.html" class="footer-link">Par mums</a>
          <a href="kontakti.html" class="footer-link">Kontakti</a>
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
  <script src="https://d3e54v103j8qbb.cloudfront.net/js/jquery-3.5.1.min.dc5e7f18c8.js?site=66f12005df0203b01c953e53" type="text/javascript" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script src="js/script.js" type="text/javascript"></script>
  <script>
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
          location.reload(); // Refresh the page to update the cart
        } else {
          alert(data.message || 'Kļūda noņemot produktu no groza.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Kļūda noņemot produktu no groza.');
      });
    }
  </script>
</body>
</html>