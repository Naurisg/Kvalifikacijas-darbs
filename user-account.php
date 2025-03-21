<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$db = new SQLite3('Datubazes/client_signup.db');

$query = $db->prepare('SELECT email, name, password FROM clients WHERE id = :id');
$query->bindValue(':id', $user_id, SQLITE3_INTEGER);
$result = $query->execute()->fetchArray(SQLITE3_ASSOC);

$email = $result['email'];
$name = $result['name'];
$hashed_password = $result['password'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $_POST['name'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    if (!empty($new_password)) {
        if (password_verify($old_password, $hashed_password)) {
            $update_query = $db->prepare('UPDATE clients SET name = :name, password = :password WHERE id = :id');
            $update_query->bindValue(':name', $new_name, SQLITE3_TEXT);
            $update_query->bindValue(':password', password_hash($new_password, PASSWORD_DEFAULT), SQLITE3_TEXT);
            $update_query->bindValue(':id', $user_id, SQLITE3_INTEGER);
            $update_query->execute();
            $success_message = "Your account was updated successfully.";
        } else {
            $error_message = "Old password is incorrect.";
        }
    } else {
        $update_query = $db->prepare('UPDATE clients SET name = :name WHERE id = :id');
        $update_query->bindValue(':name', $new_name, SQLITE3_TEXT);
        $update_query->bindValue(':id', $user_id, SQLITE3_INTEGER);
        $update_query->execute();
        $success_message = "Your account was updated successfully.";
    }

    // Reload the updated user information
    $query = $db->prepare('SELECT email, name, password FROM clients WHERE id = :id');
    $query->bindValue(':id', $user_id, SQLITE3_INTEGER);
    $result = $query->execute()->fetchArray(SQLITE3_ASSOC);

    $email = $result['email'];
    $name = $result['name'];
    $hashed_password = $result['password'];
}
?>
<!DOCTYPE html>
<html data-wf-page="66f12005df0203b01c953ed5" data-wf-site="66f12005df0203b01c953e53">
<head>
  <meta charset="utf-8">
  <title>Projekta darbs</title>
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="css/normalize.css" rel="stylesheet" type="text/css">
  <link href="css/main.css" rel="stylesheet" type="text/css">
  <link href="css/style.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
  <script type="text/javascript">WebFont.load({  google: {    families: ["Inter:regular,500,600,700","Libre Baskerville:regular,italic,700","Volkhov:regular,italic,700,700italic","Noto Serif:regular,italic,700,700italic"]  }});</script>
  <script type="text/javascript">!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart"in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);</script>
  <link href="images/favicon.png" rel="shortcut icon" type="image/x-icon">
  <link href="images/webclip.png" rel="apple-touch-icon">
</head>
<body>
  <div data-collapse="small" data-animation="default" data-duration="400" data-easing="ease" data-easing2="ease" role="banner" class="nav-bar w-nav">
    <div class="nav-container w-container">
      <div class="logo-div">
        <a href="index.html" class="nav-logo w-inline-block"><img src="images/Logo.png" width="125" sizes="(max-width: 479px) 50vw, 125px" srcset="images/Logo-p-500.png 500w, images/Logo-p-800.png 800w, images/Logo.png 960w" alt="" class="logo"></a>
      </div>
      <nav role="navigation" class="navbar w-nav-menu">
        <div class="search-banner"></div>
        <div class="nav-menu">
          <a href="index.html" class="nav-link w-nav-link">Sākums</a>
          <a href="precu-katalogs.html" class="nav-link w-nav-link">Preču Katalogs</a>
          <a href="logo-uzdruka.html" class="nav-link w-nav-link">Logo uzdruka</a>
          <a href="par-mums.html" class="nav-link w-nav-link">Par mums</a>
          <a href="kontakti.html" class="nav-link w-nav-link">Kontakti</a>
        </div>
      </nav>
      <a href="grozs.html" class="w-inline-block"><img src="images/Grozs.png" loading="eager" width="40" height="40" alt=""></a>
      <div class="menu-button w-nav-button"><img src="images/Menu-Icon.svg" loading="lazy" width="24" alt="" class="menu-icon"></div>
    </div>
  </div>
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
          <h3 class="field-label">Paroles iestatījumi</h3>
          <label for="old_password" class="field-label">Vecā parole</label>
          <input class="text-field w-input" maxlength="256" name="old_password" placeholder="" type="password" id="old_password">
          <label for="new_password" class="field-label">Jaunā parole</label>
          <input class="text-field w-input" maxlength="256" name="new_password" placeholder="" type="password" id="new_password">
          <div class="spacer _24"></div>
          <input data-wait="Saglabā..." type="submit" class="w-users-useraccountformsavebutton small-button w-button" value="Saglabāt">
          <a href="index.html" id="w-node-_62b23e3845c1a6d0f6be4e49000000000020-1c953ed5" class="w-users-useraccountformcancelbutton small-button light w-button">Atpakaļ</a>
        </form>
      </div>
    </div>
  </div>
  <div class="footer">
    <div class="footer-container w-container">
      <div class="w-layout-grid footer-grid">
        <div id="w-node-b8d7be4a-ce45-83ab-5947-02d204c8bff0-cf3fcb86" class="footerlogobloks">
          <a data-ix="logo" href="index.html" class="footer-logo w-nav-brand"><img src="images/Logo.png" width="130" sizes="130px" srcset="images/Logo-p-500.png 500w, images/Logo-p-800.png 800w, images/Logo.png 960w" alt=""></a>
          <p class="text small"><strong>Piedāvājam piegādi tajā pašā dienā </strong><br><strong>Tālruņa numurs: </strong>29 702 132<br><strong>Epasts:</strong> vissdarbam@gmail.com<br><strong>Adrese:</strong> Brīvības iela 56, Liepāja, LV-3401<br><br></p>
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
</body>
</html>