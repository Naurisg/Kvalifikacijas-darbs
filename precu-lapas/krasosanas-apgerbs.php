<?php
  include '../header.php';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Krāsošanas Apģērbs | Darba Apģērbi</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="precu.style.css">
</head>
<body>
    <div class="shop-container">
        <aside class="filters-sidebar">
            <div class="filter-section">
                <h3>Cenas filtrs</h3>
                <input type="range" class="price-range" min="0" max="100" step="5">
                <div class="price-values">
                    <span>€0</span> - <span>€100</span>
                </div>
            </div>
            
            <div class="filter-section">
                <h3>Izmēri</h3>
                <label><input type="checkbox" class="size-filter" value="XS"> XS</label><br>
                <label><input type="checkbox" class="size-filter" value="S"> S</label><br>
                <label><input type="checkbox" class="size-filter" value="M"> M</label><br>
                <label><input type="checkbox" class="size-filter" value="L"> L</label><br>
                <label><input type="checkbox" class="size-filter" value="XL"> XL</label><br>
                <label><input type="checkbox" class="size-filter" value="XXL"> XXL</label>
            </div>

            <div class="filter-section">
                <h3>Tips</h3>
                <label><input type="checkbox" value="Kombinezoni"> Kombinezoni</label><br>
                <label><input type="checkbox" value="Halāti"> Halāti</label><br>
                <label><input type="checkbox" value="Priekšauti"> Priekšauti</label><br>
                <label><input type="checkbox" value="Cepures"> Cepures</label>
            </div>

            <div class="filter-section">
                <h3>Materiāls</h3>
                <label><input type="checkbox" value="Vienreizējie"> Vienreizējās lietošanas</label><br>
                <label><input type="checkbox" value="Polipropilēns"> Polipropilēns</label><br>
                <label><input type="checkbox" value="Kokvilna"> Kokvilna</label><br>
                <label><input type="checkbox" value="Poliesters"> Poliesters</label>
            </div>
        </aside>

        <main class="products-section">
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Meklēt krāsošanas apģērbu...">
            </div>
            <div id="products-container" class="products-grid">
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let allProducts = [];

        // Fetch products for the "Krāsošanas apģērbs" category
        fetch('../admin/get_products.php?category=KrasosanasApgerbs')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allProducts = data.products;
                    displayProducts(allProducts);
                } else {
                    console.error('Error fetching products:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });

        document.querySelector('.search-bar').addEventListener('input', () => filterProducts());
        document.querySelector('.price-range').addEventListener('input', () => filterProducts());
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
                    <p><strong>Pieejamie izmēri:</strong> ${product.sizes ? product.sizes.replace(/,/g, ', ') : 'Nav pieejami'}</p>
                    <p><strong>Pieejamais daudzums:</strong> ${product.quantity}</p>
                    <div>
                        <label for="size-select">Izvēlieties izmēru:</label>
                        <select id="size-select">
                            ${product.sizes ? product.sizes.split(',').map(size => `<option value="${size}">${size}</option>`).join('') : '<option disabled>Nav pieejami</option>'}
                        </select>
                    </div>
                    <div>
                        <label for="quantity-input">Daudzums:</label>
                        <input type="number" id="quantity-input" min="1" max="${product.quantity}" value="1">
                    </div>
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
        const selectedSize = document.getElementById('size-select').value || 'Nav norādīts';
        const quantityInput = document.getElementById('quantity-input');
        const quantity = parseInt(quantityInput.value, 10) || 1;
        const maxQuantity = parseInt(quantityInput.max, 10);

        if (quantity > maxQuantity) {
            alert(`Maksimālais pieejamais daudzums ir ${maxQuantity}.`);
            return;
        }

        fetch('/Vissdarbam/grozs/add_to_cart.php', { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: productId, size: selectedSize, quantity: quantity }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Produkts pievienots grozam!');
            } else {
                alert(data.message || 'Kļūda pievienojot produktu grozam.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kļūda pievienojot produktu grozam.');
        });
    }

    function buyNow(productId) {
        console.log('Buying product:', productId);
    }
    </script>
</body>
</html>