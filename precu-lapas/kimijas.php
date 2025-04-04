<?php
  include '../header.php';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Ķīmijas | Darba Apģērbi</title>
    <link rel="stylesheet" href="../css/style.css">
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
    flex-wrap: wrap;
    gap: 20px;
    align-items: flex-start;
}

.modal-product-details img {
    width: 300px;
    height: 300px;
    object-fit: contain;
    border-radius: 8px;
    background: #f5f5f5;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.modal-product-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.modal-product-info h2 {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
}

.modal-product-info p {
    font-size: 16px;
    line-height: 1.5;
    color: #555;
}

.modal-product-info .modal-price {
    font-size: 20px;
    font-weight: bold;
    color: #27ae60;
}

.modal-product-info label {
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
}

.modal-product-info select,
.modal-product-info input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.modal-buttons {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.modal-buttons button {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    transition: background-color 0.3s, transform 0.2s;
}

.modal-buttons .add-to-cart {
    background-color: #4CAF50;
    color: white;
}

.modal-buttons .buy-now {
    background-color: #2196F3;
    color: white;
}

.modal-buttons button:hover {
    transform: scale(1.05);
    opacity: 0.9;
}
    </style>
</head>
<body>
    <div class="shop-container">
        <aside class="filters-sidebar">
            <div class="filter-section">
                <h3>Cenas filtrs</h3>
                <input type="range" class="price-range" min="0" max="300" step="10">
                <div class="price-values">
                    <span>€0</span> - <span>€300</span>
                </div>
            </div>
            
            <div class="filter-section">
                <h3>Izmēri</h3>
                <label><input type="checkbox" class="size-filter" value="1L"> 1L</label><br>
                <label><input type="checkbox" class="size-filter" value="5L"> 5L</label><br>
                <label><input type="checkbox" class="size-filter" value="10L"> 10L</label><br>
                <label><input type="checkbox" class="size-filter" value="20L"> 20L</label>
            </div>
        </aside>

        <main class="products-section">
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Meklēt ķīmiskos līdzekļus...">
            </div>
            <div id="products-container" class="products-grid">
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let allProducts = [];
        
        fetch('fetch_category_products.php?category=Kimijas')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allProducts = data.products;
                    displayProducts(allProducts);
                }
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