<!DOCTYPE html>
<html data-wf-page="66f12005df0203b01c953ecb" data-wf-site="66f12005df0203b01c953e53">
<head>
  <meta charset="utf-8">
  <title>Reģistrācija</title>
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="css/normalize.css" rel="stylesheet" type="text/css">
  <link href="css/main.css" rel="stylesheet" type="text/css">
  <link href="css/style.css" rel="stylesheet" type="text/css">
  <link href="images/favicon.png" rel="shortcut icon" type="image/x-icon">
  <link href="images/webclip.png" rel="apple-touch-icon">
  <style>
    #notification {
      display: none; 
      position: fixed; 
      top: 20px; 
      left: 50%; 
      transform: translateX(-50%); 
      background-color: #4CAF50; 
      color: white; 
      padding: 15px; 
      border-radius: 5px; 
      z-index: 1000; 
    }
  </style>
  <!-- Google reCAPTCHA scripts -->
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
  <!-- Notifikācijas bloks, kurā parādās kļūdas vai veiksmes ziņojumi -->
  <div id="notification"></div>
  
  <div class="w-users-userformpagewrap full-page-wrapper">
    <div class="w-users-usersignupformwrapper admin-form-card">
      <!-- Reģistrācijas forma -->
      <form id="registrationForm" method="post" action="register.php">
        <div class="w-users-userformheader form-card-header">
          <h2 class="heading h3">Reģistrācija</h2>
        </div>
        <input placeholder="Jūsu e-pasts" id="wf-sign-up-email" maxlength="256" name="Email" class="text-field w-input" type="email" autocomplete="username" required="">
        <input class="text-field w-input" maxlength="256" name="field" data-name="field" placeholder="Vārds" type="text" id="wf-sign-up-name" required="">
        <div style="position: relative;">
          <input placeholder="Your password" id="wf-sign-up-password" maxlength="256" name="Password" class="text-field w-input" type="password" required="">
          <span id="toggle-password" style="cursor: pointer; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
            <img src="images/eye-icon.png" alt="Show Password" id="eye-icon" style="width: 20px; height: 20px;">
          </span>
        </div>
        <label class="w-checkbox checkbox-field">
          <input class="w-checkbox-input check-box" name="Checkbox" type="checkbox" id="wf-sign-up-accept-privacy" required="">
          <span class="checkbox-label w-form-label" for="Checkbox">Piekrītu lietošanas noteikumiem!</span>
        </label>
        <!-- Google reCAPTCHA -->
        <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI" style="margin: 15px 0;"></div>
        <input data-wait="Mazliet uzgaidiet.." type="submit" class="w-users-userformbutton button w-button" value="Reģistrēties">
        <div class="w-users-userformfooter form-card-footer">
          <span>Vai jums jau ir konts?</span>
          <a href="log-in">Log In</a>
        </div>
      </form>
    </div>
  </div>

  <script src="https://d3e54v103j8qbb.cloudfront.net/js/jquery-3.5.1.min.dc5e7f18c8.js" type="text/javascript" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script>
    // Apstrādā reģistrācijas formas iesniegšanu ar AJAX
    const form = document.getElementById('registrationForm');
    const notification = document.getElementById('notification');

    form.addEventListener('submit', function(event) {
      event.preventDefault();

      // Pārbauda paroles garumu
      const passwordField = document.getElementById('wf-sign-up-password');
      if (passwordField.value.length < 8) {
        notification.style.backgroundColor = '#f44336'; // Kļūdas krāsa
        notification.textContent = 'Parolei jābūt vismaz 8 cipariem vai burtiem garai!';
        notification.style.display = 'block';
        return; // Apstādina formu, ja parole ir pārāk īsa
      }

      // Pārbauda, vai reCAPTCHA ir apstiprināta
      if (grecaptcha.getResponse() === "") {
        notification.style.backgroundColor = '#f44336'; // Kļūdas krāsa
        notification.textContent = 'Lūdzu, apstipriniet, ka neesat robots!';
        notification.style.display = 'block';
        return; // Apstādina formu, ja reCAPTCHA nav apstiprināta
      }

      const formData = new FormData(form);

      // Nosūta reģistrācijas datus uz serveri ar AJAX
      fetch('register.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Veiksmīgas reģistrācijas gadījumā rāda atpakaļskaitīšanu un pāradresē uz log-in.php
          let countdown = 3; // Sāk skaitīt no 3
          notification.style.backgroundColor = '#4CAF50';
          notification.textContent = `Reģistrācija veiksmīga! Jūs tiksiet pārvirzīts pēc ${countdown} sekundēm.`;
          notification.style.display = 'block';

          const interval = setInterval(() => {
            countdown--;
            if (countdown > 0) {
              notification.textContent = `Reģistrācija veiksmīga! Jūs tiksiet pārvirzīts pēc ${countdown} sekundēm.`;
            } else {
              clearInterval(interval);
              window.location.href = 'log-in.php'; 
            }
          }, 1000); 
        } else {
          // Ja kļūda, parāda kļūdas ziņu
          notification.style.backgroundColor = '#f44336'; 
          notification.textContent = data.message;
          notification.style.display = 'block';
        }
      })
      .catch(error => {
        // Ja servera kļūda, rāda kļūdas ziņu
        notification.style.backgroundColor = '#f44336'; 
        notification.textContent = 'Lietotājs ar šādu e-pastu jau eksistē!';
        notification.style.display = 'block';
      });
    });

    // Parāda vai paslēpj paroli, kad lietotājs noklikšķina uz acs ikonas
    document.getElementById('toggle-password').addEventListener('click', function() {
      const passwordField = document.getElementById('wf-sign-up-password');
      const eyeIcon = document.getElementById('eye-icon');
      if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.src = 'images/eye-close.png'; // Nomaina uz "aizvērtā acs" ikonu
      } else {
        passwordField.type = 'password';
        eyeIcon.src = 'images/eye-icon.png'; // Nomaina uz "atvērtā acs" ikonu
      }
    });
  </script>
</body>
</html>
