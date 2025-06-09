<?php
  // Iekļauj galvenes failu
  include '../header.php';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Darba Aksesuāri | Darba Apģērbi</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="precu.style.css">
    <style>
        .modal-carousel {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .carousel-images {
            display: flex;
            position: relative;
        }

        .carousel-image {
            transition: opacity 0.3s ease;
        }

        .carousel-btn {
            background-color: #333;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 12px;
        }

        .carousel-btn:hover {
            background-color: #555;
        }

        .out-of-stock-label {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: rgba(255, 0, 0, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="shop-container">
        <aside class="filters-sidebar">
                         <button id="filter-close-btn" aria-label="Close Filters" style="display:none;">Aizvērt</button>
            <div class="filter-section">
                <h3>Cenas filtrs</h3>
                <input type="range" class="price-range" min="0" max="100" step="5">
                <div class="price-values">
                    <span>€0</span> - <span>€100</span>
                </div>
            </div>
        </aside>

        <main class="products-section">
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Meklēt darba aksesuārus...">
                  <button id="filter-toggle-btn" aria-label="Toggle Filters" style="display:none;">Filtri</button>
            </div>
            <div id="products-container" class="products-grid">
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Saglabā visus produktus un pašreiz izvēlēto produktu
        let allProducts = [];
        let currentProduct = null;
        
        // Ielādē produktus no servera pēc kategorijas
        fetch('fetch_category_products.php?category=Aksesuari')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allProducts = data.products;
                    displayProducts(allProducts);
                }
            });

        // Meklēšanas un cenas filtra notikumu klausītāji
        document.querySelector('.search-bar').addEventListener('input', () => filterProducts());
        document.querySelector('.price-range').addEventListener('input', () => filterProducts());

        // Filtrē produktus pēc meklēšanas un cenas
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

        // Attēlo produktus lapā
        function displayProducts(products) {
            const container = document.getElementById('products-container');
            container.innerHTML = '';
            
            if (products.length === 0) {
                container.innerHTML = '<p>Nav pieejamu produktu šajā kategorijā.</p>';
                return;
            }

            // Izveido modālo logu, ja tāda vēl nav
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

            // Izvada katru produktu kā karti
            products.forEach(product => {
                const images = product.bilde.split(',');
                const firstImage = images.length > 0 ? images[0].trim() : 'images/placeholder.png';
                const isOutOfStock = parseInt(product.quantity) === 0;

                container.innerHTML += `
                    <div class="product-card">
                        ${isOutOfStock ? `<div class="out-of-stock-label">Izpārdots!</div>` : ''}
                        <img src="../${firstImage}" alt="${product.nosaukums}" onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')})">
                        <div class="product-info">
                            <h3 onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')})">${product.nosaukums}</h3>
                            <p onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')})">${product.apraksts}</p>
                            <p class="price" onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')})">€${product.cena} <span style="font-size: 0.75em; color: #888;">+PVN 21%</span></p>
                            <div class="product-buttons">
                                <button class="add-to-cart" onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')}, true)" ${isOutOfStock ? 'disabled' : ''}>
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                                <button class="buy-now" onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')}, true)" ${isOutOfStock ? 'disabled' : ''}>Pirkt tagad</button>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
    });

    // Parāda produkta modālo logu ar detalizētu informāciju
    function showProductModal(product, focusOnAddToCart = false) {
        const modal = document.getElementById('product-modal');
        const modalBody = modal.querySelector('.modal-body');
        currentProduct = product;

        const images = product.bilde.split(',');
        const isOutOfStock = parseInt(product.quantity) === 0;

        modalBody.innerHTML = `
            <div class="modal-product-details">
                <div class="modal-carousel">
                    ${images.length > 1 ? `<button class="carousel-btn prev-btn" onclick="showPrevModalImage()" aria-label="Iepriekšējais attēls"></button>` : ''}
                    <div class="carousel-images">
                        ${images.map((image, index) => `
                            <img src="../${image.trim()}" class="carousel-image" style="display: ${index === 0 ? 'block' : 'none'}; width: 300px; height: 300px; object-fit: contain; border-radius: 8px;">
                        `).join('')}
                    </div>
                    ${images.length > 1 ? `<button class="carousel-btn next-btn" onclick="showNextModalImage()" aria-label="Nākamais attēls"></button>` : ''}
                </div>
                <div class="modal-product-info">
                    <h2>${product.nosaukums}</h2>
                    <p class="modal-description">${product.apraksts}</p>
                    <p class="modal-price">€${product.cena} <span style="font-size: 0.75em; color: #888;">+PVN 21%</span></p>
                    ${isOutOfStock ? `<p style="color: red; font-weight: bold;">Izpārdots</p>` : ''}
                    <p><strong>Pieejamais daudzums:</strong> ${product.quantity}</p>
                    <div>
                        <label for="quantity-input">Daudzums:</label>
                        <input type="number" id="quantity-input" min="1" max="${product.quantity}" value="1" ${isOutOfStock ? 'disabled' : ''}>
                    </div>
                    <div class="modal-buttons">
                        <button class="add-to-cart" onclick="addToCart()" ${isOutOfStock ? 'disabled' : ''}>
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                        <button class="buy-now" onclick="buyNow()" ${isOutOfStock ? 'disabled' : ''}>Pirkt tagad</button>
                    </div>
                </div>
            </div>
        `;
        
        modal.style.display = 'block';
        
        if (focusOnAddToCart && !isOutOfStock) {
            const addToCartBtn = modal.querySelector('.add-to-cart');
            addToCartBtn.focus();
        }
    }

    // Parāda iepriekšējo attēlu modālajā logā
    function showPrevModalImage() {
        const carousel = document.querySelector('.modal-carousel .carousel-images');
        const images = carousel.querySelectorAll('.carousel-image');
        let currentIndex = Array.from(images).findIndex(img => img.style.display === 'block');
        images[currentIndex].style.display = 'none';
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        images[currentIndex].style.display = 'block';
    }

    // Parāda nākamo attēlu modālajā logā
    function showNextModalImage() {
        const carousel = document.querySelector('.modal-carousel .carousel-images');
        const images = carousel.querySelectorAll('.carousel-image');
        let currentIndex = Array.from(images).findIndex(img => img.style.display === 'block');
        images[currentIndex].style.display = 'none';
        currentIndex = (currentIndex + 1) % images.length;
        images[currentIndex].style.display = 'block';
    }

    // Aizver modālo logu, ja tiek uzspiests uz aizvēršanas pogas vai ārpus modāla
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('product-modal');
        if (event.target.classList.contains('close-modal') || event.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Pievieno produktu grozam no modāla
    function addToCart() {
        if (!currentProduct) return;
        
        const quantityInput = document.getElementById('quantity-input');
        const quantity = quantityInput ? parseInt(quantityInput.value, 10) || 1 : 1;
        const maxQuantity = quantityInput ? parseInt(quantityInput.max, 10) : Infinity;

        if (quantity > maxQuantity) {
            alert(`Maksimālais pieejamais daudzums ir ${maxQuantity}.`);
            return;
        }

        fetch('/Vissdarbam/grozs/add_to_cart.php', { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                id: currentProduct.id, 
                size: 'Viensize', 
                quantity: quantity 
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Produkts pievienots grozam!');
                document.getElementById('product-modal').style.display = 'none';
            } else {
                alert(data.message || 'Kļūda pievienojot produktu grozam.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kļūda pievienojot produktu grozam.');
        });
    }

    // Pievieno produktu grozam un pāriet uz pirkuma lapu
    function buyNow() {
        if (!currentProduct) return;
        
        const quantityInput = document.getElementById('quantity-input');
        const quantity = quantityInput ? parseInt(quantityInput.value, 10) || 1 : 1;
        const maxQuantity = quantityInput ? parseInt(quantityInput.max, 10) : Infinity;

        if (quantity > maxQuantity) {
            alert(`Maksimālais pieejamais daudzums ir ${maxQuantity}.`);
            return;
        }

        fetch('/Vissdarbam/grozs/add_to_cart.php', { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                id: currentProduct.id, 
                size: 'Viensize', 
                quantity: quantity 
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/Vissdarbam/grozs/adress.php';
            } else {
                alert(data.message || 'Kļūda pievienojot produktu grozam.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kļūda pievienojot produktu grozam.');
        });
    }

    // Atbild par filtru sānpaneli uz mobilajām ierīcēm
    document.addEventListener('DOMContentLoaded', function() {
        const filterToggleBtn = document.getElementById('filter-toggle-btn');
        const filtersSidebar = document.querySelector('.filters-sidebar');

        // Funkcija, kas atjaunina filtra pogas redzamību atkarībā no ekrāna platuma
        function updateFilterButtonVisibility() {
            if (window.innerWidth <= 600) {
                filterToggleBtn.style.display = 'inline-block';
                
                if (filtersSidebar.style.display !== 'block') {
                    filtersSidebar.style.display = 'none';
                }
            } else {
                filterToggleBtn.style.display = 'none';
                filtersSidebar.style.display = 'block';
            }
        }

        // Filtra pogas klikšķa apstrāde
        filterToggleBtn.addEventListener('click', () => {
            if (filtersSidebar.style.display === 'none') {
                filtersSidebar.style.display = 'block';
            } else {
                filtersSidebar.style.display = 'none';
            }
        });

        window.addEventListener('resize', updateFilterButtonVisibility);
        updateFilterButtonVisibility();
    });

    // Atbild par filtra aizvēršanas pogu uz mobilajām ierīcēm
    document.addEventListener('DOMContentLoaded', function() {
        const filterCloseBtn = document.getElementById('filter-close-btn');
        const filtersSidebar = document.querySelector('.filters-sidebar');

        // Aizver filtru sānpaneli
        filterCloseBtn.addEventListener('click', () => {
            filtersSidebar.style.display = 'none';
        });

        // Funkcija, kas atjaunina aizvēršanas pogas redzamību atkarībā no ekrāna platuma
        function updateCloseButtonVisibility() {
            if (window.innerWidth <= 600) {
                filterCloseBtn.style.display = 'inline-block';
            } else {
                filterCloseBtn.style.display = 'none';
            }
        }

        window.addEventListener('resize', updateCloseButtonVisibility);
        updateCloseButtonVisibility();
    });
    </script>
</body>
</html>