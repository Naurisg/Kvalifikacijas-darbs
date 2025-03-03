
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Apavi | Darba Apģērbi</title>
    <link rel="stylesheet" href="../css/style.css">
    <header>
    <div data-collapse="small" data-animation="default" data-duration="400" data-easing="ease" data-easing2="ease" role="banner" class="nav-bar w-nav">
        <div class="nav-container w-container">
            <div class="logo-div">
                <a href="../index.html" class="nav-logo w-inline-block">
                    <img src="../images/Logo.png" width="125" sizes="(max-width: 479px) 50vw, 125px" srcset="../images/Logo-p-500.png 500w, ../images/Logo-p-800.png 800w, ../images/Logo.png 960w" alt="" class="logo">
                </a>
            </div>
            <nav role="navigation" class="navbar w-nav-menu">
                <div class="search-banner"></div>
                <div class="nav-menu">
                    <a href="../index.html" class="nav-link w-nav-link">Sākums</a>
                    <a href="../precu-katalogs.html" class="nav-link w-nav-link w--current">Preču Katalogs</a>
                    <a href="../logo-uzdruka.html" class="nav-link w-nav-link">Logo uzdruka</a>
                    <a href="../par-mums.html" class="nav-link w-nav-link">Par mums</a>
                    <a href="../kontakti.html" class="nav-link w-nav-link">Kontakti</a>
                </div>
            </nav>
            <a href="../grozs.html" class="w-inline-block">
                <img src="../images/Grozs.png" loading="eager" width="40" height="40" alt="">
            </a>
        </div>
    </div>
</header>
    <style>
        .shop-container {
            display: flex;
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .filters-sidebar {
            width: 250px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .products-section {
            flex: 1;
            padding: 0 20px;
        }

        .search-container {
            width: 100%;
            padding: 20px 0;
            margin-bottom: 20px;
        }

        .search-bar {
        width: 100%;
        padding: 12px 32px;  
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 16px;
        text-indent: 10px; 
        background-position: 10px center; 
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }

        .product-info {
            padding: 15px;
        }

        .filter-section {
            margin-bottom: 20px;
        }

        .filter-section h3 {
            margin-bottom: 10px;
        }

        .price-range {
            width: 100%;
        }

        .product-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .add-to-cart, .buy-now {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .add-to-cart {
            background-color: #4CAF50;
            color: white;
        }

        .buy-now {
            background-color: #2196F3;
            color: white;
        }

        .add-to-cart:hover, .buy-now:hover {
            opacity: 0.9;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="shop-container">
        <aside class="filters-sidebar">
            <div class="filter-section">
                <h3>Cenas filtrs</h3>
                <input type="range" class="price-range" min="0" max="200" step="10">
                <div class="price-values">
                    <span>€0</span> - <span>€200</span>
                </div>
            </div>
            
            <div class="filter-section">
                <h3>Izmēri</h3>
                <label><input type="checkbox" value="36"> 36</label><br>
                <label><input type="checkbox" value="37"> 37</label><br>
                <label><input type="checkbox" value="38"> 38</label><br>
                <label><input type="checkbox" value="39"> 39</label><br>
                <label><input type="checkbox" value="40"> 40</label><br>
                <label><input type="checkbox" value="41"> 41</label><br>
                <label><input type="checkbox" value="42"> 42</label>
            </div>
        </aside>

        <main class="products-section">
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Meklēt apavus...">
            </div>
            <div id="products-container" class="products-grid">
                <!-- Products will be loaded here -->
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    let allProducts = [];
    
    fetch('fetch_category_products.php?category=Apavi')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allProducts = data.products;
                displayProducts(allProducts);
            }
        });

    // Search filter
    document.querySelector('.search-bar').addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        filterProducts();
    });

    // Price filter
    document.querySelector('.price-range').addEventListener('input', (e) => {
        filterProducts();
    });

    function filterProducts() {
        const searchTerm = document.querySelector('.search-bar').value.toLowerCase();
        const maxPrice = parseFloat(document.querySelector('.price-range').value);

        const filteredProducts = allProducts.filter(product => {
            const matchesSearch = product.nosaukums.toLowerCase().includes(searchTerm) ||
                                product.apraksts.toLowerCase().includes(searchTerm);
            const matchesPrice = parseFloat(product.cena) <= maxPrice;
            return matchesSearch && matchesPrice;
        });

        displayProducts(filteredProducts);
        document.querySelector('.price-values').innerHTML = 
            `<span>€0</span> - <span>€${maxPrice}</span>`;
    }


            function displayProducts(products) {
                const container = document.getElementById('products-container');
                container.innerHTML = '';
                products.forEach(product => {
                    container.innerHTML += `
                        <div class="product-card">
                            <img src="../${product.bilde}" alt="${product.nosaukums}">
                            <div class="product-info">
                                <h3>${product.nosaukums}</h3>
                                <p>${product.apraksts}</p>
                                <p class="price">€${product.cena}</p>
                                <div class="product-buttons">
                                    <button class="add-to-cart" onclick="addToCart(${product.id})">Pievienot grozam</button>
                                    <button class="buy-now" onclick="buyNow(${product.id})">Pirkt tagad</button>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
        });

        function addToCart(productId) {
            console.log('Adding product to cart:', productId);
            // Add your cart logic here
        }

        function buyNow(productId) {
            console.log('Buying product:', productId);
            // Add your purchase logic here
        }
    </script>
</body>
</html>
