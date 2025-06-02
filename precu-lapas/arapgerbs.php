<?php
  include '../header.php';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Augstas redzamības Apģērbi | Darba Apģērbi</title>
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
            width: 300px;
            height: 300px;
            object-fit: contain;
            border-radius: 8px;
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
    </style>
</head>
<body>
    <div class="shop-container">
        <aside class="filters-sidebar">
             <button id="filter-close-btn" aria-label="Close Filters" style="display:none;">Aizvērt</button>
            <div class="filter-section">
                <h3>Cenas filtrs</h3>
                <input type="range" class="price-range" min="0" max="300" step="10">
                <div class="price-values">
                    <span>€0</span> - <span>€300</span>
                </div>
            </div>
            
            <div class="filter-section">
                <h3>Izmēri</h3>
                <label><input type="checkbox" class="size-filter" value="XS"> XS</label><br>
                <label><input type="checkbox" class="size-filter" value="S"> S</label><br>
                <label><input type="checkbox" class="size-filter" value="M"> M</label><br>
                <label><input type="checkbox" class="size-filter" value="L"> L</label><br>
                <label><input type="checkbox" class="size-filter" value="XL"> XL</label><br>
                <label><input type="checkbox" class="size-filter" value="XXL"> XXL</label><br>
                <label><input type="checkbox" class="size-filter" value="XXXL"> XXXL</label>
            </div>
        </aside>

        <main class="products-section">
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Meklēt augstas redzamības apģērbus...">
             <button id="filter-toggle-btn" aria-label="Toggle Filters" style="display:none;">Filtri</button>
            </div>
            <div id="products-container" class="products-grid">
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let allProducts = [];
        let currentProduct = null;
        
        fetch('fetch_category_products.php?category=Arapgerbs')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allProducts = data.products;
                    displayProducts(allProducts);
                }
            });

        document.querySelector('.search-bar').addEventListener('input', (e) => {
            filterProducts();
        });

        document.querySelector('.price-range').addEventListener('input', (e) => {
            filterProducts();
        });

        document.querySelectorAll('.size-filter').forEach(checkbox => {
            checkbox.addEventListener('change', () => filterProducts());
        });

        function filterProducts() {
            const searchTerm = document.querySelector('.search-bar').value.toLowerCase();
            const maxPrice = parseFloat(document.querySelector('.price-range').value);
            const selectedSizes = Array.from(document.querySelectorAll('.size-filter:checked')).map(cb => cb.value);

            const filteredProducts = allProducts.filter(product => {
                const matchesSearch = product.nosaukums.toLowerCase().includes(searchTerm) ||
                                      product.apraksts.toLowerCase().includes(searchTerm);
                const matchesPrice = parseFloat(product.cena) <= maxPrice;
                const matchesSize = selectedSizes.length === 0 || 
                                    selectedSizes.some(size => product.sizes && product.sizes.split(',').includes(size));
                return matchesSearch && matchesPrice && matchesSize;
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
                const images = product.bilde.split(',');
                const firstImage = images.length > 0 ? images[0].trim() : 'images/placeholder.png';
                const isOutOfStock = parseInt(product.quantity) === 0;

                container.innerHTML += `
                    <div class="product-card">
                        <img src="../${firstImage}" alt="${product.nosaukums}" onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')})">
                        <div class="product-info">
                            <h3 onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')})">${product.nosaukums}</h3>
                            <p onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')})">${product.apraksts}</p>
                            <p class="price" onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')})">€${product.cena}</p>
                            ${isOutOfStock ? `<p style="color: red; font-weight: bold;">Izpārdots</p>` : ''}
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
                            <img src="../${image.trim()}" class="carousel-image" style="display: ${index === 0 ? 'block' : 'none'}">
                        `).join('')}
                    </div>
                    ${images.length > 1 ? `<button class="carousel-btn next-btn" onclick="showNextModalImage()" aria-label="Nākamais attēls"></button>` : ''}
                </div>
                <div class="modal-product-info">
                    <h2>${product.nosaukums}</h2>
                    <p class="modal-description">${product.apraksts}</p>
                    <p class="modal-price">€${product.cena}</p>
                    ${isOutOfStock ? `<p style="color: red; font-weight: bold;">Izpārdots</p>` : ''}
                    <p><strong>Pieejamie izmēri:</strong> ${product.sizes ? product.sizes.split(',').map(size => size.trim()).join(', ') : 'Nav pieejami'}</p>
                    <p><strong>Pieejamais daudzums:</strong> ${product.quantity}</p>
                    <div>
                        <label for="size-select">Izvēlieties izmēru:</label>
                        <select id="size-select" ${isOutOfStock ? 'disabled' : ''}>
                            ${product.sizes ? product.sizes.split(',').map(size => `<option value="${size.trim()}">${size.trim()}</option>`).join('') : '<option disabled>Nav pieejami</option>'}
                        </select>
                    </div>
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

    function showPrevModalImage() {
        const carousel = document.querySelector('.modal-carousel .carousel-images');
        const images = carousel.querySelectorAll('.carousel-image');
        let currentIndex = Array.from(images).findIndex(img => img.style.display === 'block');
        images[currentIndex].style.display = 'none';
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        images[currentIndex].style.display = 'block';
    }

    function showNextModalImage() {
        const carousel = document.querySelector('.modal-carousel .carousel-images');
        const images = carousel.querySelectorAll('.carousel-image');
        let currentIndex = Array.from(images).findIndex(img => img.style.display === 'block');
        images[currentIndex].style.display = 'none';
        currentIndex = (currentIndex + 1) % images.length;
        images[currentIndex].style.display = 'block';
    }

    document.addEventListener('click', function(event) {
        const modal = document.getElementById('product-modal');
        if (event.target.classList.contains('close-modal') || event.target === modal) {
            modal.style.display = 'none';
        }
    });

    function addToCart() {
        if (!currentProduct) return;
        
        const selectedSize = document.getElementById('size-select')?.value || 'Nav norādīts';
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
                size: selectedSize, 
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

    function buyNow() {
        if (!currentProduct) return;
        
        const selectedSize = document.getElementById('size-select')?.value || 'Nav norādīts';
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
                size: selectedSize, 
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

    document.addEventListener('DOMContentLoaded', function() {
        const filterToggleBtn = document.getElementById('filter-toggle-btn');
        const filtersSidebar = document.querySelector('.filters-sidebar');

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


    document.addEventListener('DOMContentLoaded', function() {
        const filterCloseBtn = document.getElementById('filter-close-btn');
        const filtersSidebar = document.querySelector('.filters-sidebar');

        filterCloseBtn.addEventListener('click', () => {
            filtersSidebar.style.display = 'none';
        });

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
