<?php
// Check if user is logged in
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
      }
    })
    .catch(error => {
      console.error("Error:", error);
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
    /* Enhanced Scrollbar */
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
    
    /* Modern Sidebar */
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
    
    /* Header improvements */
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
    
    /* Active page indicator */
    .active-page {
      background-color: black;
      color: white;
      border-radius: 5px;
      padding: 5px 10px;
      border: 2px solid white;
    }

    .active-page:hover {
      background-color: white;
      color: black;
      border: 2px solid black;
    }
    
    /* Menu button animation */
    #menuButton {
      transition: all 0.3s ease;
    }
    
    #menuButton:hover {
      transform: rotate(90deg);
    }
    
    /* Night mode toggle */
    .night-mode-toggle {
      display: flex;
      align-items: center;
      cursor: pointer;
      margin-top: 30px;
      padding: 12px 15px;
      border-radius: 8px;
      background-color: #f8f8f8;
    }
    
    .night-mode-toggle:hover {
      background-color: #f0f0f0;
    }
    
    /* Language switcher */
    .language-switcher {
      margin-top: 20px;
      border-top: 1px solid #eee;
      padding-top: 20px;
    }
    
    .language-options {
      display: flex;
      gap: 10px;
      margin-top: 10px;
    }
    
    .language-option {
      padding: 8px 12px;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.2s;
      border: 1px solid #ddd;
      font-size: 14px;
    }
    
    .language-option:hover {
      background-color: #f0f0f0;
    }
    
    .language-option.active {
      background-color: #2c3e50;
      color: white;
      border-color: #2c3e50;
    }
    
    /* Responsive adjustments */
    @media (max-width: 767px) {
      #sidebar .content {
        width: 280px;
        padding: 20px;
      }
      
      .sidebar-link {
        padding: 10px 12px;
        font-size: 14px;
      }
    }
  </style>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const menuButton = document.getElementById("menuButton");
      const sidebar = document.getElementById("sidebar");
      const closeBtn = document.getElementById("closeSidebar");
      
      // Toggle sidebar
      function toggleSidebar() {
        sidebar.classList.toggle("open");
        document.body.style.overflow = sidebar.classList.contains("open") ? "hidden" : "";
      }
      
      menuButton.addEventListener("click", toggleSidebar);
      closeBtn.addEventListener("click", toggleSidebar);
      
      // Close sidebar when clicking outside
      sidebar.addEventListener("click", function(e) {
        if (e.target === sidebar) {
          toggleSidebar();
        }
      });
      
      // Check login status for menu button
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
          console.error("Error:", error);
        });
      
      // Night mode toggle functionality
      const nightModeToggle = document.getElementById("nightModeToggle");
      if (nightModeToggle) {
        nightModeToggle.addEventListener("click", function() {
          document.body.classList.toggle("night-mode");
          localStorage.setItem("nightMode", document.body.classList.contains("night-mode"));
        });
        
        // Check for saved preference
        if (localStorage.getItem("nightMode") === "true") {
          document.body.classList.add("night-mode");
        }
      }
      
      // Language switcher functionality
      const languageOptions = document.querySelectorAll('.language-option');
      languageOptions.forEach(option => {
        option.addEventListener('click', function() {
          // Remove active class from all options
          languageOptions.forEach(opt => opt.classList.remove('active'));
          
          // Add active class to clicked option
          this.classList.add('active');
          
          // Get selected language
          const lang = this.getAttribute('data-lang');
          
          // Save to localStorage
          localStorage.setItem('preferredLanguage', lang);
          
          // Here you would typically reload the page with the new language
          // or make an AJAX call to update content
          console.log('Language changed to:', lang);
          
          // For demo purposes, we'll just show an alert
          alert(`Language changed to ${lang.toUpperCase()}. In a real implementation, this would reload the page with the selected language.`);
        });
      });
      
      // Set initial language based on localStorage or browser language
      const preferredLanguage = localStorage.getItem('preferredLanguage') || 'lv';
      document.querySelector(`.language-option[data-lang="${preferredLanguage}"]`).classList.add('active');
    });
  </script>
</head>
<body>
  <!-- Sidebars -->
  <div id="sidebar">
    <div class="content">
      <span id="closeSidebar" class="close-btn" title="Aizvērt">&times;</span>
      <div class="sidebar-nav">
        <a href="/Vissdarbam/user-account.php" class="sidebar-link profile-link <?php echo $current_page == 'user-account.php' ? 'active' : ''; ?>" style="display: none;">
          <img src="/Vissdarbam/images/profile-user.png" alt="Profils" class="sidebar-icon">
          <span>Profils</span>
        </a>
        <a href="/Vissdarbam/grozs/grozs.php" class="sidebar-link <?php echo $current_page == 'grozs.php' ? 'active' : ''; ?>">
          <img src="/Vissdarbam/images/Grozs.png" alt="Grozs" class="sidebar-icon">
          <span>Grozs</span>
        </a>
        <a href="/Vissdarbam/order-history.php" class="sidebar-link <?php echo $current_page == 'order-history.php' ? 'active' : ''; ?>">
          <img src="/Vissdarbam/images/pasutijumi.png" alt="Pasūtījumu vēsture" class="sidebar-icon">
          <span>Pasūtījumu vēsture</span>
        </a>
        <a href="/Vissdarbam/settings.php" class="sidebar-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
          <img src="/Vissdarbam/images/settings.png" alt="Iestatījumi" class="sidebar-icon">
          <span>Iestatījumi</span>
        </a>
      </div>
      
      <div class="night-mode-toggle" id="nightModeToggle">
        <img src="/Vissdarbam/images/night-mode.png" alt="Tumšais režīms" class="sidebar-icon">
        <span>Tumšais režīms</span>
      </div>
      
      <!-- valodas maiņa -->
      <div class="language-switcher">
        <div style="font-weight: 500; margin-bottom: 8px;">Valodas maiņa</div>
        <div class="language-options">
          <div class="language-option" data-lang="lv" title="Latviešu">LV</div>
          <div class="language-option" data-lang="en" title="English">EN</div>
          <div class="language-option" data-lang="ru" title="Русский">RU</div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Header navigacija -->
  <div data-collapse="small" data-animation="default" data-duration="400" data-easing="ease" data-easing2="ease" role="banner" class="nav-bar w-nav">
    <div class="nav-container w-container">
      <div class="logo-div">
        <a href="/Vissdarbam/index.php" aria-current="page" class="nav-logo w-inline-block w--current">
          <img src="/Vissdarbam/images/Logo.png" width="125" sizes="(max-width: 479px) 50vw, 125px" srcset="/Vissdarbam/images/Logo-p-500.png 500w, /Vissdarbam/images/Logo-p-800.png 800w, /Vissdarbam/images/Logo.png 960w" alt="Logo" class="logo">
        </a>
      </div>
      <nav role="navigation" class="navbar w-nav-menu">
        <div class="search-banner"></div>
        <div class="nav-menu">
          <a href="/Vissdarbam/index.php" class="nav-link w-nav-link <?php echo $current_page == 'index.php' ? 'active-page' : ''; ?>">
            <span class="nav-text">Sākums</span>
          </a>
          <a href="/Vissdarbam/precu-katalogs.php" class="nav-link w-nav-link <?php echo $current_page == 'precu-katalogs.php' ? 'active-page' : ''; ?>">
            <span class="nav-text">Preču Katalogs</span>
          </a>
          <a href="/Vissdarbam/logo-uzdruka.php" class="nav-link w-nav-link <?php echo $current_page == 'logo-uzdruka.php' ? 'active-page' : ''; ?>">
            <span class="nav-text">Logo uzdruka</span>
          </a>
          <a href="/Vissdarbam/par-mums.php" class="nav-link w-nav-link <?php echo $current_page == 'par-mums.php' ? 'active-page' : ''; ?>">
            <span class="nav-text">Par mums</span>
          </a>
          <a href="/Vissdarbam/kontakti.php" class="nav-link w-nav-link <?php echo $current_page == 'kontakti.php' ? 'active-page' : ''; ?>">
            <span class="nav-text">Kontakti</span>
          </a>
          <a href="/Vissdarbam/reviews.php" class="nav-link w-nav-link <?php echo $current_page == 'reviews.php' ? 'active-page' : ''; ?>">
            <span class="nav-text">Atsauksmes</span>
          </a>
          <a href="/Vissdarbam/log-in.php" class="nav-link w-nav-link header-login-link <?php echo $current_page == 'log-in.php' ? 'active-page' : ''; ?>">
            <img src="/Vissdarbam/images/login.png" alt="Ienākt" class="header-icon">
            <span class="nav-text">Ienākt</span>
          </a>
          <a id="menuButton" class="nav-link w-nav-link" style="display: none;" title="Izvēlne">
            <img src="/Vissdarbam/images/menu.png" alt="Izvēlne" class="header-icon">
          </a>
        </div>
      </nav>
    </div>
  </div>
</body>
</html>