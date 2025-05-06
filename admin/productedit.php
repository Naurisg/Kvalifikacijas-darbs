<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: adminlogin.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labot Produktu</title>
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
        }

        .container, .form-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        h2 {
            color: #333;
            margin-bottom: 30px;
            font-size: 24px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #444;
            font-weight: 600;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fafafa;
            transition: border-color 0.3s ease;
            font-size: 16px;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #666;
            background: white;
        }

        textarea {
            height: 150px;
            resize: vertical;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        button, .submit-btn, .back-btn {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .submit-btn {
            background-color: #333;
            color: white;
        }

        .submit-btn:hover {
            background-color: #444;
        }

        .back-btn {
            background-color: #666;
            color: white;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .back-btn:hover {
            background-color: #555;
        }

        .current-image {
            max-width: 200px;
            margin: 10px 0;
        }

        #sizes-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        #sizes-container div {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 4px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fafafa;
            font-size: 14px;
            cursor: pointer;
            user-select: none;
        }

        #sizes-container input[type="checkbox"] {
            margin: 0;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Labot Produktu</h2>
        <form id="editProductForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nosaukums">Nosaukums:</label>
                <input type="text" id="nosaukums" name="nosaukums" required>
            </div>

            <div class="form-group">
                <label for="apraksts">Apraksts:</label>
                <textarea id="apraksts" name="apraksts" required></textarea>
            </div>

            <div class="form-group">
                <label for="kategorija">Kategorija:</label>
                <select id="kategorija" name="kategorija" required>
                    <option value="">Izvēlieties kategoriju</option>
                    <option value="Cimdi">Cimdi</option>
                    <option value="Apavi">Apavi</option>
                    <option value="Apgerbs">Apģērbs</option>
                    <option value="Drosibas-sistemas">Drošības sistēmas</option>
                    <option value="Gazmaskas">Gazmaskas</option>
                    <option value="Arapgerbs">Augstas redzamības apgerbs</option>
                    <option value="Austinas_kiveres_brilles">Austinas,kiveres,brilles</option>
                    <option value="KrasosanasApgerbs">Krāsošanas apģērbs</option>
                    <option value="Jakas">Jakas</option>
                    <option value="Kimijas">Ķīmijas</option>
                    <option value="Aksesuari">Aksesuāri</option>
                    <option value="Instrumenti">Instrumenti</option>
                </select>
            </div>

            <div class="form-group">
                <label for="cena">Cena (€):</label>
                <input type="number" id="cena" name="cena" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="quantity">Daudzums:</label>
                <input type="number" id="quantity" name="quantity" min="1" required>
            </div>

            <div class="form-group" id="sizes-section" style="display: none;">
                <label>Izmēri:</label>
                <div id="sizes-container">
                    
                </div>
            </div>

            <div class="form-group">
                <label>Pašreizējā bilde:</label>
                <img id="currentImage" class="current-image" src="" alt="Current product image">
                <label for="bilde">Mainīt bildi:</label>
                <input type="file" id="bilde" name="bilde" accept="image/*">
            </div>

            <div class="button-group">
                <a href="admin-panelis.php" class="back-btn">Atpakaļ</a>
                <button type="submit" class="submit-btn">Saglabāt izmaiņas</button>
            </div>
        </form>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('id');

        const categoryElement = document.getElementById('kategorija');
        const sizesSection = document.getElementById('sizes-section');
        const sizesContainer = document.getElementById('sizes-container');

        const sizesForCategories = {
            cimdi: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'],
            apavi: ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
            apgerbs: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'],
            drosibas_sistemas: ['S', 'M', 'L', 'XL', '2XL'],
            gazmaskas: ['Standarta', 'Liela', 'Maza'],
            arapgerbs: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'],
            jakas: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'],
            krasosanasapgerbs: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL']
        };

        categoryElement.addEventListener('change', function () {
            const selectedCategory = this.value.toLowerCase();
            sizesContainer.innerHTML = ''; // Notīra iepriekš atzīmētās izvēles rūtiņas

            if (sizesForCategories[selectedCategory]) {
                sizesSection.style.display = 'block';
                sizesForCategories[selectedCategory].forEach(size => {
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.name = 'sizes[]';
                    checkbox.value = size;
                    checkbox.id = `size-${size}`;

                    const label = document.createElement('label');
                    label.htmlFor = `size-${size}`;
                    label.textContent = size;

                    const wrapper = document.createElement('div');
                    wrapper.appendChild(checkbox);
                    wrapper.appendChild(label);

                    sizesContainer.appendChild(wrapper);
                });
            } else {
                sizesSection.style.display = 'none';
            }
        });

        fetch(`get_product.php?id=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('nosaukums').value = data.product.nosaukums;
                    document.getElementById('apraksts').value = data.product.apraksts;
                    document.getElementById('kategorija').value = data.product.kategorija;
                    document.getElementById('cena').value = data.product.cena;
                    document.getElementById('quantity').value = data.product.quantity;

                    // Pareizi iestata vecā attēla ceļu
                    if (data.product.bilde) {
                        document.getElementById('currentImage').src = '../' + data.product.bilde;
                        document.getElementById('currentImage').alt = data.product.nosaukums;
                    } else {
                        document.getElementById('currentImage').src = '';
                        document.getElementById('currentImage').alt = 'Nav pieejama bilde';
                    }

                    const selectedCategory = data.product.kategorija.toLowerCase();
                    if (sizesForCategories[selectedCategory]) {
                        sizesSection.style.display = 'block';
                        sizesForCategories[selectedCategory].forEach(size => {
                            const checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.name = 'sizes[]';
                            checkbox.value = size;
                            checkbox.id = `size-${size}`;
                            if (data.product.sizes && data.product.sizes.split(',').includes(size)) {
                                checkbox.checked = true;
                            }

                            const label = document.createElement('label');
                            label.htmlFor = `size-${size}`;
                            label.textContent = size;

                            const wrapper = document.createElement('div');
                            wrapper.appendChild(checkbox);
                            wrapper.appendChild(label);

                            // Uztaisa kad visu kasti var klikšķinat
                            wrapper.style.cursor = 'pointer';
                            wrapper.addEventListener('click', function(e) {
                                if (e.target !== checkbox && e.target.tagName.toLowerCase() !== 'label') {
                                    checkbox.checked = !checkbox.checked;
                                }
                            });

                            sizesContainer.appendChild(wrapper);
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching product data:', error);
                alert('Kļūda ielādējot produkta datus.');
            });

        document.getElementById('editProductForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('id', productId);

            fetch('update_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Produkts veiksmīgi atjaunināts!');
                    window.location.href = 'admin-panelis.php';
                } else {
                    alert('Kļūda: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Kļūda atjauninot produktu');
            });
        });
    </script>
</body>
</html>
