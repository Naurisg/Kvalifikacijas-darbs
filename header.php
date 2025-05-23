<?php
// Pārbauda, vai lietotājs ir ielogojies
echo '<script>
document.addEventListener("DOMContentLoaded", function() {
  fetch("/Vissdarbam/check-login-status.php")
    .then(response => response.json())
    .then(data => {
      if (data.loggedIn) {
        const loginLink = document.querySelector(".header-login-link");
        if (loginLink) {
          loginLink.innerHTML = \'<img src="/Vissdarbam/images/logout.png" alt="Izlogoties" class="header-icon"><span class="nav-text">Izlogoties</span>\';
          loginLink.href = "/Vissdarbam/log-out.php";
          loginLink.title = "Izlogoties";
        }
        document.querySelector(".profile-link").style.display = "inline-flex";
        document.querySelector(".cart-link").style.display = "inline-flex";
        // Paslēpt reģistrēties pogu, ja ielogojies
        var regLink = document.getElementById("registerLink");
        if (regLink) regLink.style.display = "none";
      } else {
        // Parādīt reģistrēties pogu, ja nav ielogojies
        var regLink = document.getElementById("registerLink");
        if (regLink) regLink.style.display = "inline-block";
      }
    })
    .catch(error => {
      console.error("Kļūda:", error);
    });
});
</script>';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="lv">
<head>
  <meta charset="utf-8">
  <title>Sākums</title>
  <meta content="Sākums" property="og:title">
  <meta content="Sākums" property="twitter:title">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="/Vissdarbam/css/normalize.css" rel="stylesheet" type="text/css">
  <link href="/Vissdarbam/css/main.css" rel="stylesheet" type="text/css">
  <link href="/Vissdarbam/css/style.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
  <script type="text/javascript">WebFont.load({  google: {    families: ["Inter:regular,500,600,700","Libre Baskerville:regular,italic,700","Volkhov:regular,italic,700,700italic","Noto Serif:regular,italic,700,700italic"]  }});</script>
  <script type="text/javascript">!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart"in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);</script>
  <link href="/Vissdarbam/images/favicon.png" rel="shortcut icon" type="image/x-icon">
  <link href="/Vissdarbam/images/webclip.png" rel="apple-touch-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
  <style>
    /* Uzlabotā ritjosla */
    ::-webkit-scrollbar {
      width: 10px;
      height: 10px;
    }
    ::-webkit-scrollbar-track {
      background: #f8f8f8;
      border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 10px;
      border: 2px solid #f8f8f8;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }
    
    /*Sidebar */
    #sidebar {
      display: none;
      position: fixed;
      top: 0;
      right: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(5px);
      z-index: 1000;
      overflow: hidden;
      transition: all 0.3s ease;
    }
    
    #sidebar.open {
      display: block;
      animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    #sidebar .content {
      background-color: #fff;
      width: 320px;
      height: 100%;
      padding: 30px;
      position: absolute;
      right: 0;
      transform: translateX(100%);
      transition: transform 0.3s ease;
      box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
    }
    
    #sidebar.open .content {
      transform: translateX(0);
    }
    
    #sidebar .close-btn {
      font-size: 28px;
      cursor: pointer;
      color: #333;
      margin-bottom: 30px;
      align-self: flex-end;
      transition: color 0.2s;
    }
    
    #sidebar .close-btn:hover {
      color: #000;
    }
    
    .sidebar-nav {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    
    .sidebar-link {
      display: flex;
      align-items: center;
      padding: 12px 15px;
      border-radius: 8px;
      color: #333;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.2s ease;
    }
    
    .sidebar-link:hover {
      background-color: #f5f5f5;
      transform: translateX(5px);
    }
    
    .sidebar-link.active {
      background-color: #f0f0f0;
      color: #000;
      font-weight: 600;
    }
    
    .sidebar-icon {
      width: 24px;
      height: 24px;
      margin-right: 15px;
      opacity: 0.8;
    }
    
    .header-icon {
      width: 20px;
      height: 20px;
      vertical-align: middle;
      margin-right: 8px;
      transition: transform 0.2s;
    }
    
    .nav-link:hover .header-icon {
      transform: scale(1.1);
    }
    
    .nav-text {
      vertical-align: middle;
    }
    
    /* Aktīvais lapas stils */
    .active-page {
      position: relative;
      color: #333;
      font-weight: 600;
    }

    .active-page:after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 100%;
      height: 2px;
      background-color: #333;
    }
    
    /* Menu pogas animācija */
    #menuButton {
      transition: all 0.3s ease;
    }
    
    #menuButton:hover {
      transform: rotate(90deg);
    }
    
    .nav-bar {
      background-color: #fff;
      box-shadow: 0 2px 15px rgba(0,0,0,0.1);
      padding: 10px 0;
      position: sticky;
      top: 0;
      z-index: 999;
    }
    
    .nav-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 20px;
    }
    
    .logo-div {
      flex: 0 0 auto;
    }
    
    .logo {
      height: 50px;
      width: auto;
      transition: transform 0.3s;
    }
    
    .logo:hover {
      transform: scale(1.05);
    }
    
    .navbar {
      display: flex;
      align-items: center;
    }
    
    .nav-menu {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .nav-link {
      display: flex;
      align-items: center;
      padding: 8px 10px;
      color: #555;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.2s;
      border-radius: 6px;
      font-size: 14px;
    }
    
    .nav-link:hover {
      color: #000;
      background-color: #f5f5f5;
    }
    
    .user-actions {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-left: 15px;
      padding-left: 15px;
      border-left: 1px solid #eee;
    }
    
    .user-action-link {
      display: flex;
      align-items: center;
      padding: 6px;
      border-radius: 50%;
      transition: all 0.2s;
    }
    
    .user-action-link:hover {
      background-color: #f5f5f5;
      transform: scale(1.1);
    }
    
    .user-action-link .nav-text {
      display: none;
    }
    
    /* Responsivitāte */
    @media (max-width: 1024px) {
      .nav-menu {
        gap: 8px;
      }
      
      .nav-link {
        padding: 6px 8px;
        font-size: 13px;
      }
      
      .logo {
        height: 45px;
      }
    }
    
    /* Mobilie stili (≤ 767px)*/
    @media (max-width: 767px) {
      #sidebar .content {
        width: 280px;
        padding: 20px;
      }
      .sidebar-link {
        padding: 10px 12px;
        font-size: 14px;
      }
      /* Paslēpt parasto navigāciju*/
      .nav-menu {
        display: none !important;
      }
      /* Pēc noklusējuma paslēpt mobilo izvēlni, rādīt tikai tad, kad ir .active */
      .mobile-menu {
        display: none !important;
      }
      .mobile-menu.active {
        display: flex !important;
        opacity: 1;
      }
      /* Burgera izvēlnes stili */
      .burger-menu {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 30px;
        height: 21px;
        cursor: pointer;
        margin-left: 15px;
      }
      
      .burger-line {
        width: 100%;
        height: 3px;
        background-color: #333;
        border-radius: 3px;
        transition: all 0.3s ease;
      }
      
      /* Burgera izvēlnes animācija */
      .burger-menu.active .burger-line:nth-child(1) {
        transform: translateY(9px) rotate(45deg);
      }
      
      .burger-menu.active .burger-line:nth-child(2) {
        opacity: 0;
      }
      
      .burger-menu.active .burger-line:nth-child(3) {
        transform: translateY(-9px) rotate(-45deg);
      }
      
      /* Mobilās izvēlnes pārklājums */
      .mobile-menu {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        z-index: 1000;
        display: none;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.3s ease;
      }
      
      .mobile-menu.active {
        display: flex;
        opacity: 1;
      }
      
      /* Mobilā izvēlnes saturs */
      .mobile-menu-content {
        background: white;
        width: 90%;
        max-width: 350px;
        border-radius: 10px;
        padding: 30px 20px;
        transform: translateY(20px);
        transition: transform 0.3s ease;
      }
      
      .mobile-menu.active .mobile-menu-content {
        transform: translateY(0);
      }
      
      /* Mobilās navigācijas saites */
      .mobile-nav {
        display: flex;
        flex-direction: column;
        gap: 15px;
      }
      
      .mobile-nav-link {
        padding: 12px 15px;
        color: #333;
        text-decoration: none;
        font-size: 18px;
        border-radius: 5px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
      }
      
      .mobile-nav-link:hover {
        background-color: #f5f5f5;
      }
      
      .mobile-nav-link.active {
        background-color: #f0f0f0;
        font-weight: 600;
      }
      
      .mobile-nav-icon {
        width: 20px;
        height: 20px;
        margin-right: 10px;
      }
      
      /* Izvēršanas poga */
      .mobile-menu-close {
        position: absolute;
        top: 20px;
        right: 20px;
        color: white;
        font-size: 30px;
        cursor: pointer;
      }
      
      /* Pielāgot galvenes izkārtojumu */
      .nav-container {
        padding: 0 15px !important;
      }
      
      .navbar {
        width: auto;
      }
      
      .user-actions {
        margin-left: auto;
        border-left: none !important;
        padding-left: 0 !important;
      }
      
      /* Slēpt tekstu lietotāja darbībās */
      .user-action-link .nav-text {
        display: none !important;
      }
      
      /* pataisa logo mazliet mazaku */
      .logo {
        height: 35px !important;
      }
    }
    @media (min-width: 768px) {
      .mobile-menu,
      .burger-menu {
        display: none !important;
      }
    }
  </style>
</head>
<body>
  <!-- Sānu izvēlnes -->
  <div id="sidebar">
    <div class="content">
      <span id="closeSidebar" class="close-btn" title="Aizvērt">&times;</span>
      <div class="sidebar-nav">
        <a href="/Vissdarbam/user-account" class="sidebar-link profile-link <?php echo $current_page == 'user-account.php' ? 'active' : ''; ?>" style="display: none;">
          <img src="/Vissdarbam/images/profile-user.png" alt="Profils" class="sidebar-icon">
          <span>Profils</span>
        </a>
        <a href="/Vissdarbam/grozs/grozs" class="sidebar-link <?php echo $current_page == 'grozs.php' ? 'active' : ''; ?>">
          <img src="/Vissdarbam/images/Grozs.png" alt="Grozs" class="sidebar-icon">
          <span>Grozs</span>
        </a>
        <a href="/Vissdarbam/order-history" class="sidebar-link <?php echo $current_page == 'order-history.php' ? 'active' : ''; ?>">
          <img src="/Vissdarbam/images/pasutijumi.png" alt="Pasūtījumu vēsture" class="sidebar-icon">
          <span>Pasūtījumu vēsture</span>
        </a>
      </div>
    </div>
  </div>
  
  <!-- Galvenes navigācija -->
  <div data-collapse="small" data-animation="default" data-duration="400" data-easing="ease" data-easing2="ease" role="banner" class="nav-bar w-nav">
    <div class="nav-container w-container">
      <div class="logo-div">
        <a href="/Vissdarbam/index" aria-current="page" class="nav-logo w-inline-block w--current">
          <img src="/Vissdarbam/images/Logo.png" width="125" sizes="(max-width: 479px) 50vw, 125px" srcset="/Vissdarbam/images/Logo-p-500.png 500w, /Vissdarbam/images/Logo-p-800.png 800w, /Vissdarbam/images/Logo.png 960w" alt="Logo" class="logo">
        </a>
      </div>
      <!-- Pievieno burgera izvēlnes pogu šeit, vienmēr DOM -->
      <div class="burger-menu" id="burgerMenuBtn" style="display:none;">
        <span class="burger-line"></span>
        <span class="burger-line"></span>
        <span class="burger-line"></span>
      </div>
      <nav role="navigation" class="navbar w-nav-menu">
        <div class="search-banner"></div>
        <div class="nav-menu">
          <a href="/Vissdarbam/index" class="nav-link w-nav-link <?php echo $current_page == 'index.php' ? 'active-page' : ''; ?>">
            <span class="nav-text">Sākums</span>
          </a>
          <a href="/Vissdarbam/precu-katalogs" class="nav-link w-nav-link <?php echo $current_page == 'precu-katalogs.php' ? 'active-page' : ''; ?>">
            <span class="nav-text">Preču Katalogs</span>
          </a>
          <a href="/Vissdarbam/logo-uzdruka" class="nav-link w-nav-link <?php echo $current_page == 'logo-uzdruka.php' ? 'active-page' : ''; ?>">
            <span class="nav-text">Logo uzdruka</span>
          </a>
          <a href="/Vissdarbam/par-mums" class="nav-link w-nav-link <?php echo $current_page == 'par-mums.php' ? 'active-page' : ''; ?>">
            <span class="nav-text">Par mums</span>
          </a>
          <a href="/Vissdarbam/kontakti" class="nav-link w-nav-link <?php echo $current_page == 'kontakti.php' ? 'active-page' : ''; ?>">
            <span class="nav-text">Kontakti</span>
          </a>
          <a href="/Vissdarbam/reviews" class="nav-link w-nav-link <?php echo $current_page == 'reviews.php' ? 'active-page' : ''; ?>">
            <span class="nav-text">Atsauksmes</span>
          </a>
        </div>
        
        <div class="user-actions">
          <a href="/Vissdarbam/log-in" class="user-action-link w-nav-link header-login-link <?php echo $current_page == 'log-in.php' ? 'active-page' : ''; ?>" title="Ienākt">
            <img src="/Vissdarbam/images/login.png" alt="Ienākt" class="header-icon">
            <span class="nav-text">Ienākt</span>
          </a>
          <a href="/Vissdarbam/sign-up" id="registerLink" class="user-action-link w-nav-link" style="display:none;" title="Reģistrēties">
            <img src="/Vissdarbam/images/register.png" alt="Reģistrēties" class="header-icon">
            <span class="nav-text">Reģistrēties</span>
          </a>
          <a id="menuButton" class="user-action-link w-nav-link" style="display: none;" title="Izvēlne">
            <img src="/Vissdarbam/images/menu.png" alt="Izvēlne" class="header-icon">
          </a>
        </div>
      </nav>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const menuButton = document.getElementById("menuButton");
      const sidebar = document.getElementById("sidebar");
      const closeBtn = document.getElementById("closeSidebar");
      
      // Sānu izvēlnes pārslēgšana
      function toggleSidebar() {
        sidebar.classList.toggle("open");
        document.body.style.overflow = sidebar.classList.contains("open") ? "hidden" : "";
      }
      
      menuButton.addEventListener("click", toggleSidebar);
      closeBtn.addEventListener("click", toggleSidebar);
      
      // Aizver sānu izvēlni, kad noklikšķina ārpus tās
      sidebar.addEventListener("click", function(e) {
        if (e.target === sidebar) {
          toggleSidebar();
        }
      });
      
      // Pārbauda ielogoties statusu izvēlnes pogai
      fetch("/Vissdarbam/check-login-status.php")
        .then(response => response.json())
        .then(data => {
          if (data.loggedIn) {
            menuButton.style.display = "inline-block";
          } else {
            menuButton.style.display = "none";
          }
        })
        .catch(error => {
          console.error("Kļūda:", error);
        });

      // Rāda burgera izvēlni tikai mobilajās ierīcēs
      function toggleBurgerVisibility() {
        var burger = document.getElementById('burgerMenuBtn');
        // Labojums: paslēpj arī mobilo izvēlni, ja maina uz darbvirsmu
        if (window.innerWidth <= 767) {
          burger.style.display = 'flex';
        } else {
          burger.style.display = 'none';
          // Aizver mobilo izvēlni, ja tā ir atvērta
          burger.classList.remove('active');
          var mobileMenu = document.querySelector('.mobile-menu');
          if (mobileMenu) {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = '';
          }
        }
      }
      toggleBurgerVisibility();
      window.addEventListener('resize', toggleBurgerVisibility);

      // Burgera izvēlnes atvēršanas/aizvēršanas loģika
      var burgerMenu = document.getElementById('burgerMenuBtn');
      var mobileMenu = document.querySelector('.mobile-menu');
      if (burgerMenu && mobileMenu) {
        burgerMenu.addEventListener('click', function() {
          burgerMenu.classList.toggle('active');
          mobileMenu.classList.toggle('active');
          document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
        });
        mobileMenu.querySelector('.mobile-menu-close').addEventListener('click', function() {
          burgerMenu.classList.remove('active');
          mobileMenu.classList.remove('active');
          document.body.style.overflow = '';
        });
        mobileMenu.querySelectorAll('.mobile-nav-link').forEach(link => {
          link.addEventListener('click', function() {
            burgerMenu.classList.remove('active');
            mobileMenu.classList.remove('active');
            document.body.style.overflow = '';
          });
        });
      }
    });
  </script>
  <!-- Pievieno mobilo izvēlni HTML dokumenta beigās -->
  <div class="mobile-menu">
    <span class="mobile-menu-close">&times;</span>
    <div class="mobile-menu-content">
      <nav class="mobile-nav" id="mobileNavLinks">
        <!-- Redzams tikai mobilajās ierīcēs, kad .mobile-menu.active ir iestatīts ar JS -->
        <a href="/Vissdarbam/index" class="mobile-nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
          Sākums
        </a>
        <a href="/Vissdarbam/precu-katalogs" class="mobile-nav-link <?php echo $current_page == 'precu-katalogs.php' ? 'active' : ''; ?>">
          Preču Katalogs
        </a>
        <a href="/Vissdarbam/logo-uzdruka" class="mobile-nav-link <?php echo $current_page == 'logo-uzdruka.php' ? 'active' : ''; ?>">
          Logo uzdruka
        </a>
        <a href="/Vissdarbam/par-mums" class="mobile-nav-link <?php echo $current_page == 'par-mums.php' ? 'active' : ''; ?>">
          Par mums
        </a>
        <a href="/Vissdarbam/kontakti" class="mobile-nav-link <?php echo $current_page == 'kontakti.php' ? 'active' : ''; ?>">
          Kontakti
        </a>
        <a href="/Vissdarbam/reviews" class="mobile-nav-link <?php echo $current_page == 'reviews.php' ? 'active' : ''; ?>">
          Atsauksmes
        </a>
        <hr style="border: none; border-top: 3px solid #eee; margin: 15px 0;">
        <!-- Autorizācijas/lietošanas saites tiks apstrādātas ar JS zemāk -->
        <a href="/Vissdarbam/log-in" id="mobileLoginLink" class="mobile-nav-link <?php echo $current_page == 'log-in.php' ? 'active' : ''; ?>">
          <img src="/Vissdarbam/images/login.png" alt="Ienākt" style="width:20px;height:20px;vertical-align:middle;margin-right:8px;"> Ienākt
        </a>
        <a href="/Vissdarbam/sign-up" id="mobileRegisterLink" class="mobile-nav-link <?php echo $current_page == 'sign-up.php' ? 'active' : ''; ?>">
          <img src="/Vissdarbam/images/register.png" alt="Reģistrēties" style="width:20px;height:20px;vertical-align:middle;margin-right:8px;"> Reģistrēties
        </a>
        <a href="/Vissdarbam/user-account" id="mobileProfileLink" class="mobile-nav-link <?php echo $current_page == 'user-account.php' ? 'active' : ''; ?>" style="display:none;">
          <img src="/Vissdarbam/images/profile-user.png" alt="Profils" style="width:20px;height:20px;vertical-align:middle;margin-right:8px;"> Profils
        </a>
        <a href="/Vissdarbam/grozs/grozs" id="mobileCartLink" class="mobile-nav-link <?php echo $current_page == 'grozs.php' ? 'active' : ''; ?>" style="display:none;">
          <img src="/Vissdarbam/images/Grozs.png" alt="Grozs" style="width:20px;height:20px;vertical-align:middle;margin-right:8px;"> Grozs
        </a>
        <a href="/Vissdarbam/order-history" id="mobileOrderHistoryLink" class="mobile-nav-link <?php echo $current_page == 'order-history.php' ? 'active' : ''; ?>" style="display:none;">
          <img src="/Vissdarbam/images/pasutijumi.png" alt="Pasūtījumu vēsture" style="width:20px;height:20px;vertical-align:middle;margin-right:8px;"> Pasūtījumu vēsture
        </a>
      </nav>
    </div>
  </div>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Mobilās navigācijas ielogoties/izlogoties/profils loģika
      fetch("/Vissdarbam/check-login-status.php")
        .then(response => response.json())
        .then(data => {
          // Mobilās navigācijas elementi
          var mobileLoginLink = document.getElementById("mobileLoginLink");
          var mobileRegisterLink = document.getElementById("mobileRegisterLink");
          var mobileProfileLink = document.getElementById("mobileProfileLink");
          var mobileCartLink = document.getElementById("mobileCartLink");
          var mobileOrderHistoryLink = document.getElementById("mobileOrderHistoryLink");
          if (data.loggedIn) {
            // Maina ielogoties uz izlogoties
            if (mobileLoginLink) {
              mobileLoginLink.innerHTML = '<img src="/Vissdarbam/images/logout.png" alt="Izlogoties" style="width:20px;height:20px;vertical-align:middle;margin-right:8px;"> Izlogoties';
              mobileLoginLink.href = "/Vissdarbam/log-out.php";
              mobileLoginLink.title = "Izlogoties";
            }
            // Paslēpj reģistrēties, rāda profilu/grozs/pasūtījumu vēsturi
            if (mobileRegisterLink) mobileRegisterLink.style.display = "none";
            if (mobileProfileLink) mobileProfileLink.style.display = "flex";
            if (mobileCartLink) mobileCartLink.style.display = "flex";
            if (mobileOrderHistoryLink) mobileOrderHistoryLink.style.display = "flex";
          } else {
            // Rāda ielogoties/reģistrēties, paslēpj profilu/grozs/pasūtījumu vēsturi
            if (mobileLoginLink) {
              mobileLoginLink.innerHTML = '<img src="/Vissdarbam/images/login.png" alt="Ienākt" style="width:20px;height:20px;vertical-align:middle;margin-right:8px;"> Ienākt';
              mobileLoginLink.href = "/Vissdarbam/log-in";
              mobileLoginLink.title = "Ienākt";
            }
            if (mobileRegisterLink) mobileRegisterLink.style.display = "flex";
            if (mobileProfileLink) mobileProfileLink.style.display = "none";
            if (mobileCartLink) mobileCartLink.style.display = "none";
            if (mobileOrderHistoryLink) mobileOrderHistoryLink.style.display = "none";
          }
        })
        .catch(error => {
          console.error("Kļūda:", error);
        });
    });
  </script>
</body>
</html>
