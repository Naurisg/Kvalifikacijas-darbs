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
    cursor: pointer;
    height: 450px;
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-card img {
    width: 100%;
    height: 250px;
    object-fit: contain;
    border-radius: 8px 8px 0 0;
    background: #f5f5f5;
}

.product-info {
    padding: 15px;
    flex: 1;

    flex-direction: column;
}

.product-info h3 {
    font-size: 16px;
    line-height: 1.2;
    height: 40px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    font-weight: bold;
    color: #333;

}

.product-info p {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 5px;
    font-size: 14px;
}

.product-info .price {
    font-size: 18px;
    font-weight: bold;
    margin: 8px 0;
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
    gap: 5px;
    margin-top: auto;
    margin-bottom: 15px;
}

.add-to-cart, .buy-now {
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
    font-size: 12px;
    white-space: nowrap;
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

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 1000;
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    max-width: 80%;
    max-height: 90vh;
    overflow-y: auto;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.close-modal {
    position: absolute;
    right: 20px;
    top: 10px;
    font-size: 30px;
    cursor: pointer;
}

.modal-product-details {
    display: flex;
    gap: 30px;
}

.modal-product-details img {
    width: 400px;
    height: 400px;
    object-fit: contain;
    background: #f5f5f5;
}

.modal-product-info {
    flex: 1;
}

.modal-description {
    margin: 20px 0;
    font-size: 16px;
    line-height: 1.6;
    white-space: pre-line;
}

.modal-price {
    font-size: 24px;
    font-weight: bold;
    margin: 20px 0;
}

.modal-buttons {
    display: flex;
    gap: 15px;
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
                <label><input type="checkbox" value="42"> 42</label><br>
                <label><input type="checkbox" value="43"> 43</label><br>
                <label><input type="checkbox" value="44"> 44</label><br>
                <label><input type="checkbox" value="45"> 45</label><br>
                <label><input type="checkbox" value="46"> 46</label>
            </div>
        </aside>

        <main class="products-section">
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Meklēt apavus...">
            </div>
            <div id="products-container" class="products-grid">
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

        document.querySelector('.search-bar').addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            filterProducts();
        });

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
            
            if (!document.getElementById('product-modal')) {
                document.body.insertAdjacentHTML('beforeend', `
                    <div id="product-modal" class="modal" style="display: none;">
                        <div class="modal-content">
                            <span class="close-modal">&times;</span>
                            <div class="modal-body"></div>
                        </div>
                    </div>
                `);
            }

            products.forEach(product => {
                container.innerHTML += `
                    <div class="product-card" onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')})">
                        <img src="../${product.bilde}" alt="${product.nosaukums}">
                        <div class="product-info">
                            <h3>${product.nosaukums}</h3>
                            <p>${product.apraksts}</p>
                            <p class="price">€${product.cena}</p>
                            <div class="product-buttons">
                                <button class="add-to-cart" onclick="event.stopPropagation(); addToCart(${product.id})">Pievienot grozam</button>
                                <button class="buy-now" onclick="event.stopPropagation(); buyNow(${product.id})">Pirkt tagad</button>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
    });

    function showProductModal(product) {
        const modal = document.getElementById('product-modal');
        const modalBody = modal.querySelector('.modal-body');
        
        modalBody.innerHTML = `
            <div class="modal-product-details">
                <img src="../${product.bilde}" alt="${product.nosaukums}">
                <div class="modal-product-info">
                    <h2>${product.nosaukums}</h2>
                    <p class="modal-description">${product.apraksts}</p>
                    <p class="modal-price">€${product.cena}</p>
                    <div class="modal-buttons">
                        <button class="add-to-cart" onclick="addToCart(${product.id})">Pievienot grozam</button>
                        <button class="buy-now" onclick="buyNow(${product.id})">Pirkt tagad</button>
                    </div>
                </div>
            </div>
        `;
        
        modal.style.display = 'block';
    }

    document.addEventListener('click', function(event) {
        const modal = document.getElementById('product-modal');
        if (event.target.classList.contains('close-modal') || event.target === modal) {
            modal.style.display = 'none';
        }
    });

    function addToCart(productId) {
        console.log('Adding product to cart:', productId);
    }

    function buyNow(productId) {
        console.log('Buying product:', productId);
    }
    </script>
</body>
</html>
