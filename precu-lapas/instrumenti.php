<?php
  include '../header.php';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Darba Instrumenti | Darba Apģērbi</title>
    <link rel="stylesheet" href="/Vissdarbam/css/style.css">
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
    </style>
</head>
<body>
    <div class="shop-container">
        <aside class="filters-sidebar">
            <div class="filter-section">
                <h3>Cenas filtrs</h3>
                <input type="range" class="price-range" min="0" max="500" step="20">
                <div class="price-values">
                    <span>€0</span> - <span>€500</span>
                </div>
            </div>
            
            <div class="filter-section">
                <h3>Kategorija</h3>
                <label><input type="checkbox" class="category-filter" value="Rokas"> Rokas instrumenti</label><br>
                <label><input type="checkbox" class="category-filter" value="Elektriskie"> Elektriskie instrumenti</label><br>
                <label><input type="checkbox" class="category-filter" value="Mērinstrumenti"> Mērinstrumenti</label><br>
                <label><input type="checkbox" class="category-filter" value="Urbji"> Urbji un uzgaļi</label>
            </div>

            <div class="filter-section">
                <h3>Zīmols</h3>
                <label><input type="checkbox" class="brand-filter" value="Makita"> Makita</label><br>
                <label><input type="checkbox" class="brand-filter" value="Bosch"> Bosch</label><br>
                <label><input type="checkbox" class="brand-filter" value="DeWalt"> DeWalt</label><br>
                <label><input type="checkbox" class="brand-filter" value="Milwaukee"> Milwaukee</label>
            </div>

            <div class="filter-section">
                <h3>Pielietojums</h3>
                <label><input type="checkbox" class="application-filter" value="Būvniecība"> Būvniecība</label><br>
                <label><input type="checkbox" class="application-filter" value="Metālapstrāde"> Metālapstrāde</label><br>
                <label><input type="checkbox" class="application-filter" value="Kokapstrāde"> Kokapstrāde</label><br>
                <label><input type="checkbox" class="application-filter" value="Dārzkopība"> Dārzkopība</label>
            </div>
        </aside>

        <main class="products-section">
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Meklēt darba instrumentus...">
            </div>
            <div id="products-container" class="products-grid">
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let allProducts = [];
        let currentProduct = null;
        
        fetch('/Vissdarbam/precu-lapas/fetch_category_products.php?category=Instrumenti')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allProducts = data.products;
                    displayProducts(allProducts);
                }
            });

        document.querySelector('.search-bar').addEventListener('input', () => filterProducts());
        document.querySelector('.price-range').addEventListener('input', () => filterProducts());
        document.querySelectorAll('.category-filter, .brand-filter, .application-filter').forEach(checkbox => {
            checkbox.addEventListener('change', () => filterProducts());
        });

        function filterProducts() {
            const searchTerm = document.querySelector('.search-bar').value.toLowerCase();
            const maxPrice = parseFloat(document.querySelector('.price-range').value);
            const selectedCategories = Array.from(document.querySelectorAll('.category-filter:checked')).map(cb => cb.value);
            const selectedBrands = Array.from(document.querySelectorAll('.brand-filter:checked')).map(cb => cb.value);
            const selectedApplications = Array.from(document.querySelectorAll('.application-filter:checked')).map(cb => cb.value);

            const filteredProducts = allProducts.filter(product => {
                const matchesSearch = product.nosaukums.toLowerCase().includes(searchTerm) ||
                                      product.apraksts.toLowerCase().includes(searchTerm);
                const matchesPrice = parseFloat(product.cena) <= maxPrice;
                const matchesCategory = selectedCategories.length === 0 || 
                                      (product.category && selectedCategories.some(cat => product.category.includes(cat)));
                const matchesBrand = selectedBrands.length === 0 || 
                                   (product.brand && selectedBrands.some(brand => product.brand.includes(brand)));
                const matchesApplication = selectedApplications.length === 0 || 
                                        (product.application && selectedApplications.some(app => product.application.includes(app)));
                
                return matchesSearch && matchesPrice && matchesCategory && matchesBrand && matchesApplication;
            });

            displayProducts(filteredProducts);
            document.querySelector('.price-values').innerHTML = 
                `<span>€0</span> - <span>€${maxPrice}</span>`;
        }

        function displayProducts(products) {
            const container = document.getElementById('products-container');
            container.innerHTML = '';
            
            if (products.length === 0) {
                container.innerHTML = '<p>Nav pieejamu produktu šajā kategorijā.</p>';
                return;
            }

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
                const images = product.bilde.split(','); // Handle multiple images
                const firstImage = images.length > 0 ? images[0].trim() : 'images/placeholder.png'; // Use the first image or fallback

                container.innerHTML += `
                    <div class="product-card">
                        <img src="/Vissdarbam/${firstImage}" alt="${product.nosaukums}" onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')})">
                        <div class="product-info">
                            <h3 onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')})">${product.nosaukums}</h3>
                            <p onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')})">${product.apraksts}</p>
                            <p class="price" onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')})">€${product.cena}</p>
                            <div class="product-buttons">
                                <button class="add-to-cart" onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')}, true)">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                                <button class="buy-now" onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')}, true)">Pirkt tagad</button>
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

        modalBody.innerHTML = `
            <div class="modal-product-details">
                <div class="modal-carousel">
                    ${images.length > 1 ? `<button class="carousel-btn prev-btn" onclick="showPrevModalImage()">&#9664;</button>` : ''}
                    <div class="carousel-images">
                        ${images.map((image, index) => `
                            <img src="/Vissdarbam/${image.trim()}" class="carousel-image" style="display: ${index === 0 ? 'block' : 'none'}; width: 300px; height: 300px; object-fit: contain; border-radius: 8px;">
                        `).join('')}
                    </div>
                    ${images.length > 1 ? `<button class="carousel-btn next-btn" onclick="showNextModalImage()">&#9654;</button>` : ''}
                </div>
                <div class="modal-product-info">
                    <h2>${product.nosaukums}</h2>
                    <p class="modal-description">${product.apraksts}</p>
                    <p class="modal-price">€${product.cena}</p>
                    ${product.category ? `<p><strong>Kategorija:</strong> ${product.category}</p>` : ''}
                    ${product.brand ? `<p><strong>Zīmols:</strong> ${product.brand}</p>` : ''}
                    ${product.application ? `<p><strong>Pielietojums:</strong> ${product.application}</p>` : ''}
                    <p><strong>Pieejamais daudzums:</strong> ${product.quantity}</p>
                    <div>
                        <label for="quantity-input">Daudzums:</label>
                        <input type="number" id="quantity-input" min="1" max="${product.quantity}" value="1">
                    </div>
                    <div class="modal-buttons">
                        <button class="add-to-cart" onclick="addToCart()">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                        <button class="buy-now" onclick="buyNow()">Pirkt tagad</button>
                    </div>
                </div>
            </div>
        `;
        
        modal.style.display = 'block';
        
        if (focusOnAddToCart) {
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
        
        const quantityInput = document.getElementById('quantity-input');
        const quantity = quantityInput ? parseInt(quantityInput.value, 10) || 1 : 1;
        const maxQuantity = quantityInput ? parseInt(quantityInput.max, 10) : Infinity;

        if (quantity > maxQuantity) {
            alert(`Maksimālais pieejamais daudzums ir ${maxQuantity}.`);
            return;
        }

        // First add to cart, then redirect to checkout
        fetch('/Vissdarbam/grozs/add_to_cart.php', { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                id: currentProduct.id, 
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
    </script>
</body>
</html>