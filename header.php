<?php
?>
<!DOCTYPE html>
<html lang="lv">
<head>
  <meta charset="utf-8">
  <title>Sākums</title>
  <meta content="Sākums" property="og:title">
  <meta content="Sākums" property="twitter:title">
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
  <style>
    ::-webkit-scrollbar {
      width: 12px;
    }

    ::-webkit-scrollbar-track {
      background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 6px;
      border: 3px solid #f1f1f1;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: #555;
    }
  </style>
</head>
<body>
  <div data-collapse="small" data-animation="default" data-duration="400" data-easing="ease" data-easing2="ease" role="banner" class="nav-bar w-nav">
    <div class="nav-container w-container">
      <div class="logo-div">
        <a href="index.html" aria-current="page" class="nav-logo w-inline-block w--current">
          <img src="images/Logo.png" width="125" sizes="(max-width: 479px) 50vw, 125px" srcset="images/Logo-p-500.png 500w, images/Logo-p-800.png 800w, images/Logo.png 960w" alt="" class="logo">
        </a>
      </div>
      <nav role="navigation" class="navbar w-nav-menu">
        <div class="search-banner"></div>
        <div class="nav-menu">
          <a href="precu-katalogs.html" class="nav-link w-nav-link">Preču Katalogs</a>
          <a href="logo-uzdruka.html" class="nav-link w-nav-link">Logo uzdruka</a>
          <a href="par-mums.html" class="nav-link w-nav-link">Par mums</a>
          <a href="kontakti.html" class="nav-link w-nav-link">Kontakti</a>
          <a href="log-in.php" class="nav-link w-nav-link header-login-link">
            <img src="images/login.png" alt="Login" style="width: 20px; height: 20px; vertical-align: middle; margin-right: 5px;">
            Login
          </a>
          <a href="user-account.php" class="nav-link w-nav-link profile-link" style="display: none;">
            <img src="images/profile-user.png" alt="Profile" style="width: 24px; height: 24px;">
          </a>
          <a id="nightModeToggle" class="nav-link w-nav-link">
            <img src="images/night-mode.png" alt="Night Mode" style="width: 24px; height: 24px;">
          </a>
        </div>
      </nav>
      <a href="grozs.php" class="w-inline-block cart-link" style="display: none;">
        <img src="images/Grozs.png" loading="eager" width="40" height="40" alt="">
      </a>
      <div class="menu-button w-nav-button">
        <img src="images/Menu-Icon.svg" loading="lazy" width="24" alt="" class="menu-icon">
      </div>
    </div>
  </div>
</body>
</html>
