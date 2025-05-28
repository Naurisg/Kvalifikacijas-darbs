<!DOCTYPE html>
<html data-wf-page="66f12005df0203b01c953eb7" data-wf-site="66f12005df0203b01c953e53">
<?php include 'header.php'; ?>
<body>
  <div class="page-wrapper">
    <div class="sakumssection black-gradient">
      <div class="container2">
        <div id="w-node-b49c48ad-b3b4-f319-67e3-9d04be5835dc-1c953eb7" class="text-box _550px center-align">
          <div class="title-tag">KONTAKTI</div>
          <h1 class="heading h1">Kā mēs varam palīdzēt?</h1>
          <p class="text medium">Sazinieties ar mums, lai uzzinātu par neskaidriem jautājumiem, iegūtu jaunākos jaunumus vai uzdotu jebkuru citu jautājumu, kas jums varētu rasties.</p>
          <div class="spacer _32"></div>
          <div class="form-card">
            <div class="form w-form">
              <form id="email-form" name="email-form" data-name="Email Form" method="post" action="save_contact.php" class="form" data-wf-page-id="66f12005df0203b01c953eb7" data-wf-element-id="8d34d8cc-5d0c-97cb-e06c-d4d71db618a2">
                <div class="w-layout-grid form-2-grid">
                  <div class="field-block"><label for="First-Name">Vārds*</label><input class="text-field w-input" maxlength="256" name="First-Name" data-name="First Name" placeholder="Vārds" type="text" id="First-Name" required=""></div>
                  <div class="field-block"><label for="Last-Name">Uzvārds*</label><input class="text-field w-input" maxlength="256" name="Last-Name" data-name="Last Name" placeholder="Uzvārds" type="text" id="Last-Name" required=""></div>
                </div>
                <div class="field-block"><label for="Email">E-pasts*</label><input class="text-field w-input" maxlength="256" name="Email" data-name="Email" placeholder="vissdarbam@email.com" type="email" id="Email" required=""></div>
                <div class="field-block"><label for="Message">Tava ziņa</label><textarea placeholder="Raksti šeit....." maxlength="5000" data-name="Message" name="Message" id="Message" required="" class="text-area w-input"></textarea></div><input type="submit" data-wait="Lūdzu uzgaidiet.." class="button no-margin w-button" value="Nosūtīt!">
              </form>
              <div id="contact-message" style="margin-top:10px; font-weight:bold;"></div>
              <script>
(function() {
  function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  }

  const messageDiv = document.getElementById('contact-message');
  const success = getQueryParam('success');
  const error = getQueryParam('error');

  if (success === '1') {
    messageDiv.style.color = 'green';
    messageDiv.textContent = 'Jūsu ziņa ir veiksmīgi nosūtīta! Mēs ar jums sazināsimies drīz.';
  } else if (error === '1') {
    messageDiv.style.color = 'red';
    messageDiv.textContent = 'Radās kļūda, lūdzu mēģiniet vēlreiz.';
  }

  // Paslēpj ziņu pēc 5 sekundēm
  if (success === '1' || error === '1') {
    setTimeout(() => {
      messageDiv.style.transition = "opacity 0.5s ease";
      messageDiv.style.opacity = 0;

      // Pilnībā izņem no DOM pēc 0.5s (kad izbalē)
      setTimeout(() => {
        messageDiv.remove();
      }, 500);
    }, 3000); // 5 sekundes
  }
})();
</script>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include 'footer.php'; ?>
  <script src="https://d3e54v103j8qbb.cloudfront.net/js/jquery-3.5.1.min.dc5e7f18c8.js?site=66f12005df0203b01c953e53" type="text/javascript" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script src="js/script.js" type="text/javascript"></script>
</body>
</html>