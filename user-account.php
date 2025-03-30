<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: log-in.php");
    exit();
}

include 'header.php'; // Pievieno header.php failu

$user_id = $_SESSION['user_id'];
$db = new SQLite3('Datubazes/client_signup.db');

$query = $db->prepare('SELECT email, name, password FROM clients WHERE id = :id');
$query->bindValue(':id', $user_id, SQLITE3_INTEGER);
$result = $query->execute()->fetchArray(SQLITE3_ASSOC);

$email = $result['email'];
$name = $result['name'];
$hashed_password = $result['password'];

$popup_message = null; 
$popup_type = null; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $_POST['name'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    if (!empty($new_password)) {
        if (strlen($new_password) < 8) {
            $popup_message = "Jaunā parole nevar būt īsāka par 8 burtiem vai cipariem!";
            $popup_type = "error";
        } elseif (password_verify($old_password, $hashed_password)) {
            if (password_verify($new_password, $hashed_password)) {
                $popup_message = "Jaunā parole nevar būt tāda pati kā vecā parole!";
                $popup_type = "error";
            } else {
                $update_query = $db->prepare('UPDATE clients SET name = :name, password = :password WHERE id = :id');
                $update_query->bindValue(':name', $new_name, SQLITE3_TEXT);
                $update_query->bindValue(':password', password_hash($new_password, PASSWORD_DEFAULT), SQLITE3_TEXT);
                $update_query->bindValue(':id', $user_id, SQLITE3_INTEGER);
                $update_query->execute();
                $popup_message = "Jūsu parole un vārds ir veiksmīgi atjaunināti.";
                $popup_type = "success";
            }
        } else {
            $popup_message = "Vecā parole ir nepareiza!";
            $popup_type = "error";
        }
    } else {
        $update_query = $db->prepare('UPDATE clients SET name = :name WHERE id = :id');
        $update_query->bindValue(':name', $new_name, SQLITE3_TEXT);
        $update_query->bindValue(':id', $user_id, SQLITE3_INTEGER);
        $update_query->execute();
        $popup_message = "Jūsu vārds ir veiksmīgi atjaunināts.";
        $popup_type = "success";
    }

    // Atjauno jauna lietotāja datus
    $query = $db->prepare('SELECT email, name, password FROM clients WHERE id = :id');
    $query->bindValue(':id', $user_id, SQLITE3_INTEGER);
    $result = $query->execute()->fetchArray(SQLITE3_ASSOC);

    $email = $result['email'];
    $name = $result['name'];
    $hashed_password = $result['password'];
}
?>
<body>
<?php if ($popup_message): ?>
<div class="popup-notification <?php echo $popup_type; ?>">
    <p><?php echo $popup_message; ?></p>
</div>
<?php endif; ?>
  <div class="w-users-useraccountwrapper account-page-wrapper" data-wf-user-account="true" data-wf-collection="site.siteUser">
    <div class="w-users-blockcontent account-card">
      <div class="w-users-blockheader account-header">
        <h2 class="heading h3">Lietotāja informācija:</h2>
      </div>
      <div>
        <?php if (isset($success_message)): ?>
          <div class="w-users-userformsuccessstate w-form-success">
            <p><?php echo $success_message; ?></p>
          </div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
          <div class="w-users-userformerrorstate w-form-fail">
            <div class="user-form-error-msg"><?php echo $error_message; ?></div>
          </div>
        <?php endif; ?>
        <form class="account-info-wrapper" method="post" data-wf-user-form-type="userAccount">
          <label for="" class="field-label">E-pasts</label>
          <input placeholder="" id="wf-user-account-email" disabled="" name="Email" class="text-field w-input w-input-disabled" type="email" autocomplete="username" required="" data-wf-user-form-input-type="email" value="<?php echo htmlspecialchars($email); ?>">
          <label for="wf-user-account-name" class="field-label">Vārds</label>
          <input class="text-field w-input" maxlength="256" name="name" data-name="field" data-wf-user-field="wf-user-field-name" placeholder="" fieldtype="" type="text" id="wf-user-account-name" required="" value="<?php echo htmlspecialchars($name); ?>">
          <div class="spacer _16"></div>
          <h3 class="field-label">Paroles iestatījumi:</h3>
          <label for="old_password" class="field-label">Vecā parole:</label>
          <div style="position: relative;">
              <input class="text-field w-input" maxlength="256" name="old_password" placeholder="" type="password" id="old_password">
              <span id="toggle-old-password" style="cursor: pointer; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                  <img src="images/eye-icon.png" alt="Show Password" id="eye-icon-old" style="width: 20px; height: 20px;">
              </span>
          </div>
          <label for="new_password" class="field-label">Jaunā parole:</label>
          <div style="position: relative;">
              <input class="text-field w-input" maxlength="256" name="new_password" placeholder="" type="password" id="new_password">
              <span id="toggle-new-password" style="cursor: pointer; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                  <img src="images/eye-icon.png" alt="Show Password" id="eye-icon-new" style="width: 20px; height: 20px;">
              </span>
          </div>
          <div class="spacer _24"></div>
          <input data-wait="Saglabā..." type="submit" class="w-users-useraccountformsavebutton small-button w-button" value="Saglabāt">
          <a href="index.php" id="w-node-_62b23e3845c1a6d0f6be4e49000000000020-1c953ed5" class="w-users-useraccountformcancelbutton small-button light w-button">Atpakaļ</a>
        </form>
      </div>
    </div>
  </div>
  <div class="footer">
    <div class="footer-container w-container">
      <div class="w-layout-grid footer-grid">
        <div id="w-node-b8d7be4a-ce45-83ab-5947-02d204c8bff0-cf3fcb86" class="footerlogobloks">
          <a data-ix="logo" href="index.php" class="footer-logo w-nav-brand"><img src="images/Logo.png" width="130" sizes="130px" srcset="images/Logo-p-500.png 500w, images/Logo-p-800.png 800w, images/Logo.png 960w" alt=""></a>
          <p class="text small"><strong>Piedāvājam piegādi tajā pašā dienā </strong><br><strong>Tālruņa numurs: </strong>29 702 132<br><strong>Epasts:</strong> vissdarbam@gmail.com<br><strong>Adrese:</strong> Brīvības iela 56, Liepāja, LV-3401<br><br></p>
        </div>
        <div class="footer-links-container">
          <h5 class="footer-header">Mājas lapa</h5>
          <a href="index.php" class="footer-link">Sākums</a>
          <a href="precu-katalogs.html" class="footer-link">Preču Katalogs</a>
          <a href="par-mums.html" class="footer-link">Logo uzdruka</a>
          <a href="logo-uzdruka.html" class="footer-link">Par mums</a>
          <a href="kontakti.html" class="footer-link">Kontakti</a>
        </div>
        <div class="footer-links-container">
          <h5 class="footer-header">Darba Laiki</h5>
          <p class="paragraph-14">Pirmdiena 09–17<br>Otrdiena 9-17<br>Trešdiena 09–17<br>Ceturtdiena 09–17<br>Piektdiena 09–17<br>Sestdiena Slēgts<br>Svētdiena Slēgts</p>
        </div>
      </div>
    </div>
    <section>
      <div class="text-block-2">© 2022 Majors-J. All Rights Reserved.</div>
    </section>
  </div>
  <script src="https://d3e54v103j8qbb.cloudfront.net/js/jquery-3.5.1.min.dc5e7f18c8.js?site=66f12005df0203b01c953e53" type="text/javascript" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script src="js/script.js" type="text/javascript"></script>
  <script>
      document.getElementById('toggle-old-password').addEventListener('click', function() {
          const passwordField = document.getElementById('old_password');
          const eyeIcon = document.getElementById('eye-icon-old');
          if (passwordField.type === 'password') {
              passwordField.type = 'text';
              eyeIcon.src = 'images/eye-close.png'; 
          } else {
              passwordField.type = 'password';
              eyeIcon.src = 'images/eye-icon.png'; 
          }
      });

      document.getElementById('toggle-new-password').addEventListener('click', function() {
          const passwordField = document.getElementById('new_password');
          const eyeIcon = document.getElementById('eye-icon-new');
          if (passwordField.type === 'password') {
              passwordField.type = 'text';
              eyeIcon.src = 'images/eye-close.png'; // Change to "eye-slash" icon
          } else {
              passwordField.type = 'password';
              eyeIcon.src = 'images/eye-icon.png'; // Change back to "eye" icon
          }
      });
  </script>
</body>
<style>
/* Add styles for the popup notification */
.popup-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    animation: fadeOut 5s forwards;
    color: white;
}

.popup-notification.success {
    background-color: #4caf50;
}

.popup-notification.error {
    background-color: #f44336;
}

@keyframes fadeOut {
    0% { opacity: 1; }
    90% { opacity: 1; }
    100% { opacity: 0; display: none; }
}
</style>
</html>