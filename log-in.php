<!DOCTYPE html>
<html data-wf-page="66f12005df0203b01c953ebf" data-wf-site="66f12005df0203b01c953e53">
<head>
  <meta charset="utf-8">
  <title>Autorizācija</title>
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
  <div class="w-users-userformpagewrap full-page-wrapper">
    <div class="w-users-userloginformwrapper admin-form-card">
      <form id="loginForm" method="post">
        <div class="w-users-userformheader form-card-header">
          <h2 class="heading h3">Autorizēties</h2>
        </div>
        <input maxlength="256" placeholder="Jūsu e-pasts" name="Email" id="wf-log-in-email" class="text-field w-input" type="email" autocomplete="username" required="" data-wf-user-form-input-type="email">
        <div style="position: relative;">
          <input maxlength="256" placeholder="Jūsu parole" name="Password" id="wf-log-in-password" class="text-field w-input" type="password" required="" data-wf-user-form-input-type="password">
          <span id="toggle-password" style="cursor: pointer; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
            <img src="images/eye-icon.png" alt="Show Password" id="eye-icon" style="width: 20px; height: 20px;">
          </span>
        </div>
        <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>
        <input type="submit" data-wait="Please wait..." class="w-users-userformbutton button w-button" value="Ienākt">
        <div class="w-users-userformfooter form-card-footer"><span>Nav izveidots konts?</span>
          <a href="sign-up.php">Reģistrēties</a>
        </div>
      </form>
      <div style="display:none" data-wf-user-form-error="true" class="w-users-userformerrorstate form-error w-form-fail">
        <div class="user-form-error-msg" wf-login-form-general-error-error="We&#x27;re having trouble logging you in. Please try again, or contact us if you continue to have problems." wf-login-form-invalid-email_or_password-error="Invalid email or password. Please try again.">We&#x27;re having trouble logging you in. Please try again, or contact us if you continue to have problems.</div>
      </div>
    </div>
    <a href="password_reset/reset-password.html" class="below-card-link">Aizmirsi paroli?</a>
  </div>
  <script src="https://d3e54v103j8qbb.cloudfront.net/js/jquery-3.5.1.min.dc5e7f18c8.js?site=66f12005df0203b01c953e53" type="text/javascript" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script src="js/script.js" type="text/javascript"></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <script>
    document.getElementById('loginForm').addEventListener('submit', function(event) {
      event.preventDefault(); //  Novērš formu no noklusējuma iesniegšanas

      const formData = new FormData(this); // Iegūst form datus

      // Iegūt reCAPTCHA atbildes tokenu
      const recaptchaResponse = grecaptcha.getResponse();
      if (!recaptchaResponse) {
        alert('Lūdzu, apstipriniet, ka neesat robots.');
        return;
      }
      formData.append('g-recaptcha-response', recaptchaResponse);

      fetch('process-login.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          window.location.href = 'index.php'; // Pārved atpakaļ uz index.php pec veiksmigas ielogošanas
        } else {
          const errorDiv = document.querySelector('.w-users-userformerrorstate');
          const errorMsg = document.querySelector('.user-form-error-msg');
          errorMsg.textContent = data.message;
          errorDiv.style.display = 'block';
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    });

    // pievieno notikumu, lai parādītu vai paslēptu paroli
    document.getElementById('toggle-password').addEventListener('click', function() {
      const passwordField = document.getElementById('wf-log-in-password');
      const eyeIcon = document.getElementById('eye-icon');
      if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.src = 'images/eye-close.png'; // Nomaina uz "aizvērtā acs" ikonu
      } else {
        passwordField.type = 'password';
        eyeIcon.src = 'images/eye-icon.png'; // Nomaina uz "atvērtā acs" ikonu
      }
    });

    // Pārbauda, vai lietotājs ir pieteicies
    document.addEventListener('DOMContentLoaded', function() {
      fetch('check-login-status.php')
        .then(response => response.json())
        .then(data => {
          if (data.loggedIn) {
            document.querySelector('.header-login-link').textContent = 'Log Out';
            document.querySelector('.header-login-link').href = 'log-out.php';
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    });
  </script>
</body>
</html>