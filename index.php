<!DOCTYPE html>
<html data-wf-page="66f12005df0203b01c953eb0" data-wf-site="66f12005df0203b01c953e53">
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

    .product-buttons button {
      background-color: transparent;
      border: 1px solid black; 
      color: black;
      padding: 5px 10px;
      font-size: 12px; 
      cursor: pointer;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .product-buttons button:hover {
      background-color: black;
      color: white;
    }

    .product-buttons .buy-now {
      background-color: black; 
      color: white;
      border: 1px solid black;
      padding: 5px 10px;
      font-size: 12px;
      cursor: pointer;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .product-buttons .buy-now:hover {
      background-color: white; 
      color: black;
    }

    .product-buttons .add-to-cart {
      background-color: transparent;
      border: none;
      color: black;
      font-size: 16px;
      cursor: pointer;
      padding: 5px;
      transition: color 0.3s ease;
    }

    .product-buttons .add-to-cart:hover {
      color: gray;
    }
  </style>
</head>
<body>
  <?php
  include 'header.php';
  ?>
  <div class="page-wrapper">
    <div id="" class="sakumssection light-color-gradient">
      <div class="container2">
        <div class="w-layout-grid sakumsgrid">
          <div id="w-node-accfd924-78f6-ecf1-4abb-37edf78b5e65-1c953eb0" class="text-box">
            <h1 class="heading h1">VissDarbam</h1>
            <p class="text large"><strong class="bold-text-5">Jūsu azsardzība- mūsu rūpes</strong></p>
            <div class="spacer _16"></div>
            <a href="precu-katalogs.php" class="button w-button">Preču Katalogs</a>
          </div>
        </div>
      </div>
    </div>
    <div class="w-layout-blockcontainer container-5 w-container">

      <section class="toppreces">
        <h1 class="heading-8"><strong class="bold-text-10">Top kategorijas:</strong></h1>
        <div class="w-layout-grid grid-6">
            <div class="topprecesdiv">
                <a href="precu-lapas/cimdi.php" class="image-container">
                    <img src="images/cimdi2.png" loading="lazy" alt="" class="image-73">
                    <span class="overlay-text">Cimdi</span>
                </a>
            </div>
            <div class="topprecesdiv">
                <a href="precu-lapas/apavi.php" class="image-container">
                    <img src="images/apavi.webp" loading="lazy" alt="" class="image-74">
                    <span class="overlay-text">Apavi</span>
                </a>
            </div>
            <div class="topprecesdiv">
                <a href="precu-lapas/apgerbi.php" class="image-container">
                    <img src="images/artwork.png" loading="lazy" alt="" class="image-75">
                    <span class="overlay-text">Apģērbi</span>
                </a>
            </div>
            <div class="topprecesdiv">
                <a href="precu-lapas/jakas.php" class="image-container">
                    <img src="images/fleecejakas.kategorias.png" loading="lazy" alt="" class="image-76">
                    <span class="overlay-text">Jakas</span>
                </a>
            </div>
        </div>
    </section>
    </div>

    
    <div class="jaunumisection">
      <div class="container2">
          <div class="section-top">
              <h2 class="heading h3"><strong>Preču Jaunumi:</strong></h2>
              <a href="precu-katalogs.php" class="button light mobile-hidden w-button">Apskatīt visus</a>
          </div>
          <div class="w-layout-grid grid" id="latest-products-container">
              <!-- Produkti tiks ielādēti šeit dynamically -->
          </div>
      </div>
  </div>
  


    <section class="kapecmussection">
      <div class="div-block-10">
        <h1><strong class="bold-text-9">Kāpēc izvēlēties tieši mūs?</strong></h1>
      </div>
      <div class="w-layout-blockcontainer w-container">
        <p class="paragraph-15">Majors-J ir uzticams partneris, kas jau vairāk nekā 13 gadus piedāvā darba apģērbu, apavus, cimdus, drošības sistēmas un citus aizsardzības līdzekļus. Mūsu ilggadējā pieredze garantē, ka mēs saprotam jūsu darba drošības vajadzības un varam piedāvāt risinājumus, kas nodrošina optimālu komfortu un aizsardzību jebkurā darba vidē.<br>‍<br>‍<strong>Kvalitāte un drošība ir mūsu prioritāte</strong> — mēs piedāvājam tikai tādus produktus, kas atbilst visaugstākajiem kvalitātes standartiem. Katra prece tiek rūpīgi izvēlēta, lai tā spētu izturēt intensīvas darba slodzes un atbilstu industrijas normām.<br>‍<br>Mēs lepojamies ar savu <strong>personisko pieeju katram klientam</strong>. Majors-J komanda ir apņēmīga palīdzēt jums atrast vislabākos risinājumus, kas atbilst jūsu specifiskajām vajadzībām. Uzņēmuma vadība personīgi rūpējas par profesionālu un uzticamu sadarbību ar katru klientu.<br><br>Turklāt, <strong>mūsu veikals Liepājā</strong> ir vieta, kur jūs varat personīgi apskatīt un iegādāties mūsu plašo preču klāstu. Mēs vienmēr esam gatavi sniegt padomu un palīdzēt izvēlēties jums piemērotākos produktus.<br><br>Izvēloties Majors-J, jūs izvēlaties <strong>uzticamību, kvalitāti un individuālu pieeju</strong>, kas palīdzēs jums veidot drošu un komfortablu darba vidi.</p>
      </div>
    </section>
    <section class="sadarbibassection">
      <h1 id="w-node-_6e3de817-c101-b5db-b63a-35ee90ffa410-1c953eb0" class="heading-3"><strong class="bold-text-7">Sadarbības partneri</strong></h1>
      <div class="w-layout-blockcontainer container-4 w-container">
        <div id="w-node-c4358a7d-c283-ce3c-8531-b91f787f2e5b-1c953eb0" class="w-layout-layout quick-stack wf-layout-layout">
          <div id="w-node-c4358a7d-c283-ce3c-8531-b91f787f2e5c-1c953eb0" class="w-layout-cell cell">
            <div class="partneridiv">
              <a href="https://www.upb.lv/" target="_blank" class="w-inline-block"><img src="images/UPB.png" loading="lazy" alt="" class="image-19"></a>
            </div>
          </div>
          <div id="w-node-c4358a7d-c283-ce3c-8531-b91f787f2e5d-1c953eb0" class="w-layout-cell cell">
            <div class="partneridiv">
              <a href="https://www.mpbuvserviss.lv/" target="_blank" class="w-inline-block"><img src="images/mpbūvserviss.png" loading="lazy" sizes="(max-width: 767px) 160px, (max-width: 991px) 22vw, (max-width: 1279px) 220px, 260px" srcset="images/mpbūvserviss-p-500.png 500w, images/mpbūvserviss-p-800.png 800w, images/mpbūvserviss-p-1080.png 1080w, images/mpbūvserviss-p-1600.png 1600w, images/mpbūvserviss.png 2048w" alt="" class="image-20"></a>
            </div>
          </div>
          <div id="w-node-_862fb62b-c996-96d1-30f8-0b55975fc870-1c953eb0" class="w-layout-cell cell-9">
            <div class="partneridiv">
              <a href="https://www.medzescomponents.lv/" target="_blank" class="w-inline-block"><img src="images/Medzes-Components.webp" loading="lazy" sizes="(max-width: 767px) 160px, (max-width: 991px) 22vw, (max-width: 1279px) 220px, 260px" srcset="images/Medzes-Components-p-500.webp 500w, images/Medzes-Components.webp 894w" alt="" class="image-21"></a>
            </div>
          </div>
          <div id="w-node-_782acdc5-d71e-4475-8ab5-a1a893141d80-1c953eb0" class="w-layout-cell cell-10">
            <div class="partneridiv">
              <a href="https://terrabalt.lv/en/home/" target="_blank" class="w-inline-block"><img src="images/Terrabalt.png" loading="lazy" alt="" class="image-22"></a>
            </div>
          </div>
          <div id="w-node-b3097266-47bc-2a23-9ed9-8111f8992abc-1c953eb0" class="w-layout-cell cell-14">
            <div class="partneridiv">
              <a href="https://www.stiklucentrs.lv/lv" target="_blank" class="w-inline-block"><img src="images/stiklucentrs.png" loading="lazy" sizes="(max-width: 767px) 160px, (max-width: 991px) 22vw, (max-width: 1279px) 220px, 260px" srcset="images/stiklucentrs-p-500.png 500w, images/stiklucentrs.png 600w" alt="" class="image-23"></a>
            </div>
          </div>
          <div id="w-node-c2ebe490-ae09-0ffa-ea62-75917d4e689e-1c953eb0" class="w-layout-cell cell-13">
            <div class="partneridiv">
              <a href="https://kohsel.dk/" target="_blank" class="w-inline-block"><img src="images/kohsel.png" loading="lazy" alt="" class="image-65"></a>
            </div>
          </div>
          <div id="w-node-_37204bb5-74fe-7061-97ca-d11f5dcc97d6-1c953eb0" class="w-layout-cell cell-12">
            <div class="partneridiv">
              <a href="https://www.jensenmetal.lv/lv" target="_blank" class="w-inline-block"><img src="images/sadarbibaspartneris3.jpeg" loading="lazy" alt="" class="image-66"></a>
            </div>
          </div>
          <div id="w-node-_073258ef-8810-ad26-defd-c0ca1d94d223-1c953eb0" class="w-layout-cell cell-11">
            <div class="partneridiv">
              <a href="https://www.brabantia.com/int_en/" target="_blank" class="w-inline-block"><img src="images/sadarbibaspartneris1.png" loading="lazy" sizes="(max-width: 767px) 160px, (max-width: 991px) 22vw, (max-width: 1279px) 220px, 260px" srcset="images/sadarbibaspartneris1-p-500.png 500w, images/sadarbibaspartneris1.png 590w" alt="" class="image-64"></a>
            </div>
          </div>
        </div>
      </div>
    </section>
    <div class="sakumssection black-gradient">
      <div class="container2 w-container">
        <div class="text-box _550px center-align">
          <h2 class="heading h2">Iegūstiet jaunumus e-pastā</h2>
          <p class="text large">Sekojiet jaunumiem par produktiem un saņemiet jaunākos piedāvājumus!</p>
          <div class="spacer _16"></div>
          <div class="email-form center-align w-form">
            <form id="subscribe-form" name="wf-form-Subscribe-Form" data-name="Subscribe Form" method="get" class="email-form" data-wf-page-id="66f12005df0203b01c953eb0" data-wf-element-id="4b7eae9c-8abc-9b14-e6a2-0d470afd457b">
              <div class="email-subscribe">
                  <input class="text-field no-margin w-input" maxlength="256" name="Email-2" data-name="Email 2" placeholder="Ievadiet savu e-pastu" type="email" id="subscribe-email" required="">
                  <input type="submit" data-wait="Lūdzu uzgaidiet..." class="button dark w-button" value="Abonēt">
              </div>
          </form>
          
            <div class="form-success dark w-form-done">
              <div>You&#x27;re all signed up.</div>
            </div>
            <div class="w-form-fail">
              <div>Oops! Something went wrong while submitting the form.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Atpakaļ uz augšu poga -->
  <button id="backToTop" style="display: none;">↑</button>
  <?php include 'footer.php'; ?>
  <script src="https://d3e54v103j8qbb.cloudfront.net/js/jquery-3.5.1.min.dc5e7f18c8.js?site=66f12005df0203b01c953e53" type="text/javascript" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script src="js/script.js" type="text/javascript"></script>
  <script>
    const nightModeToggle = document.getElementById('nightModeToggle');
  
    if (localStorage.getItem('darkMode') === 'true') {
      document.body.classList.add('dark-mode');
      document.querySelectorAll('.nav-bar, .sakumssection, .jaunumisection, .kapecmussection, .sadarbibassection').forEach(section => {
        section.classList.add('dark-mode');
      });
      document.querySelectorAll('.button').forEach(button => {
        button.classList.add('dark-mode');
      });
      document.querySelectorAll('a').forEach(link => {
        link.classList.add('dark-mode');
      });
    }
  
    nightModeToggle.addEventListener('click', (event) => {
      event.preventDefault(); 
      document.body.classList.toggle('dark-mode');
      document.querySelectorAll('.nav-bar, .sakumssection, .jaunumisection, .kapecmussection, .sadarbibassection').forEach(section => {
        section.classList.toggle('dark-mode');
      });
      document.querySelectorAll('.button').forEach(button => {
        button.classList.toggle('dark-mode');
      });
      document.querySelectorAll('a').forEach(link => {
        link.classList.toggle('dark-mode');
      });
  
      // saglaba darkmode local storage
      localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
    });

    // Abonentu js
    document.getElementById('subscribe-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const email = document.getElementById('subscribe-email').value;
    
    fetch('subscribe.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'email=' + encodeURIComponent(email)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Paldies par abonēšanu!');
            document.getElementById('subscribe-email').value = '';
        } else {
            alert('Kļūda: ' + data.message);
        }
    });
});



    document.addEventListener('DOMContentLoaded', function() {
    fetch('fetch_latest_products.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const container = document.getElementById('latest-products-container');
                data.products.forEach(product => {
                    container.innerHTML += `
                        <div class="product-card">
                            <img src="${product.bilde}" loading="lazy" alt="${product.nosaukums}">
                            <div class="product-info">
                                <h3>${product.nosaukums}</h3>
                                <p>${product.apraksts}</p>
                                <p class="price">€${product.cena}</p>
                                <div class="product-buttons">
                                    <button class="add-to-cart" onclick="event.stopPropagation(); addToCart(${product.id})">
                                      <i class="fas fa-shopping-cart"></i>
                                    </button>
                                    <button class="buy-now" onclick="event.stopPropagation(); buyNow(${product.id})">Pirkt tagad</button>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
        })
        .catch(error => console.error('Error:', error));
});


    // Back to Top Button functionality
    const backToTopButton = document.getElementById('backToTop');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 200) {
        backToTopButton.style.display = 'block';
      } else {
        backToTopButton.style.display = 'none';
      }
    });

    backToTopButton.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  </script>
  <style>
    #backToTop {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 50px;
      height: 50px;
      background-color: white;
      border: 2px solid black;
      border-radius: 50%;
      font-size: 24px;
      color: black;
      text-align: center;
      line-height: 46px;
      cursor: pointer;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: opacity 0.3s ease, transform 0.3s ease;
      z-index: 1000;
    }

    #backToTop:hover {
      transform: scale(1.1);
      opacity: 0.8;
    }
  </style>
</body>
</html>