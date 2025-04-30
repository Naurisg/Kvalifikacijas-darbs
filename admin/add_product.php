<?php
require_once 'auth_check.php'; // Pārbauda, vai lietotājs ir autorizēts
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pievienot Produktu</title>
    <style>
        /* Stila noteikumi lapas izkārtojumam un dizainam */
        body {
            background-color: #f5f5f5;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
        }

        .container {
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

        input, select, textarea {
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

        button {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        button[type="submit"] {
            background-color: #333;
            color: white;
        }

        button[type="submit"]:hover {
            background-color: #444;
        }

        button[type="button"] {
            background-color: #666;
            color: white;
        }

        button[type="button"]:hover {
            background-color: #555;
        }

        .message {
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
            border-left: 4px solid;
        }

        .success {
            background-color: #f0f0f0;
            border-color: #2d2d2d;
            color: #2d2d2d;
        }

        .error {
            background-color: #f0f0f0;
            border-color: #4a4a4a;
            color: #4a4a4a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Pievienot Jaunu Produktu</h2>
        <form id="addProductForm" enctype="multipart/form-data">
            <!-- Produkta nosaukuma ievades lauks -->
            <div class="form-group">
                <label for="nosaukums">Nosaukums:</label>
                <input type="text" id="nosaukums" name="nosaukums" required />
            </div>

            <!-- Produkta apraksta ievades lauks -->
            <div class="form-group">
                <label for="apraksts">Apraksts:</label>
                <textarea id="apraksts" name="apraksts" required></textarea>
            </div>

            <!-- Produkta attēlu augšupielādes sadaļa -->
            <div class="form-group">
                <label for="bilde">Bildes:</label>
                <div id="image-upload-container" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <label class="image-upload-label" style="cursor: pointer; font-size: 24px; font-weight: bold; color: #333; border: 1px solid #ddd; width: 100px; height: 100px; display: flex; justify-content: center; align-items: center; background: #fafafa;">
                        +
                        <input type="file" name="bilde[]" accept="image/*" onchange="addImage(event)" style="display: none;" />
                    </label>
                </div>
            </div>

            <!-- Produkta kategorijas izvēles lauks -->
            <div class="form-group">
                <label for="kategorija">Kategorija:</label>
                <select id="kategorija" name="kategorija" required>
                    <option value="">Izvēlieties kategoriju</option>
                    <!-- Kategoriju saraksts -->
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

            <!-- Produkta cenas ievades lauks -->
            <div class="form-group">
                <label for="cena">Cena (€):</label>
                <input type="number" id="cena" name="cena" step="0.01" required />
            </div>

            <!-- Produkta daudzuma ievades lauks -->
            <div class="form-group">
                <label for="quantity">Daudzums:</label>
                <input type="number" id="quantity" name="quantity" min="1" required />
            </div>

            <!-- Produkta izmēru izvēles sadaļa -->
            <div class="form-group" id="sizes-section" style="display: none;">
                <label>Izmēri:</label>
                <div id="sizes-container">
                    <!-- Izmēru izvēles iespējas tiks ģenerētas dinamiski -->
                </div>
            </div>

            <!-- Pogas formas iesniegšanai vai atgriešanai uz administrācijas paneli -->
            <div class="button-group">
                <button type="button" onclick="window.location.href='admin-panelis.php'">Atgriezties uz administrācijas paneli</button>
                <button type="submit">Pievienot produktu</button>
            </div>
        </form>
    </div>

    <script>
        // Izmēru sadaļas un kategoriju izvēles funkcionalitāte
        const sizesSection = document.getElementById('sizes-section');
        const sizesContainer = document.getElementById('sizes-container');
        const categoryElement = document.getElementById('kategorija');

        // Izmēru saraksts dažādām kategorijām
        const sizesForCategories = {
            cimdi: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'],
            apavi: ['35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48'],
            apgerbs: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'],
            drosibas_sistemas: ['S', 'M', 'L', 'XL', '2XL'],
            gazmaskas: ['Standarta', 'Liela', 'Maza'],
            arapgerbs: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'],
            jakas: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'],
            krasosanasapgerbs: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL']
        };

        // Mainot kategoriju, tiek ģenerēti atbilstošie izmēri
        categoryElement.addEventListener('change', function () {
            const selectedCategory = this.value.toLowerCase();
            sizesContainer.innerHTML = ''; // Notīra iepriekšējos izmērus

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

        // Forma tiek iesniegta, izmantojot AJAX pieprasījumu
        document.getElementById('addProductForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('save_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Produkts veiksmīgi pievienots!');
                    window.location.href = 'admin-panelis.php';
                } else {
                    alert('Kļūda: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Kļūda pievienojot produktu');
            });
        });

        // Funkcija attēlu pievienošanai un priekšskatīšanai
        function addImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const container = document.getElementById('image-upload-container');

                    // Izveido jaunu attēla priekšskatījuma kvadrātu
                    const imageWrapper = document.createElement('div');
                    imageWrapper.style.width = '100px';
                    imageWrapper.style.height = '100px';
                    imageWrapper.style.border = '1px solid #ddd';
                    imageWrapper.style.borderRadius = '4px';
                    imageWrapper.style.overflow = 'hidden';
                    imageWrapper.style.position = 'relative';
                    imageWrapper.style.display = 'flex';
                    imageWrapper.style.justifyContent = 'center';
                    imageWrapper.style.alignItems = 'center';
                    imageWrapper.style.background = '#fafafa';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';

                    // Pievieno pogu attēla noņemšanai
                    const removeBtn = document.createElement('button');
                    removeBtn.textContent = '×';
                    removeBtn.style.position = 'absolute';
                    removeBtn.style.top = '5px';
                    removeBtn.style.right = '5px';
                    removeBtn.style.background = 'rgba(0,0,0,0.5)';
                    removeBtn.style.color = 'white';
                    removeBtn.style.border = 'none';
                    removeBtn.style.borderRadius = '50%';
                    removeBtn.style.width = '25px';
                    removeBtn.style.height = '25px';
                    removeBtn.style.cursor = 'pointer';
                    removeBtn.style.display = 'none'; // Sākotnēji paslēpta
                    removeBtn.style.justifyContent = 'center';
                    removeBtn.style.alignItems = 'center';
                    removeBtn.onclick = function() {
                        container.removeChild(imageWrapper);
                    };

                    // Parāda noņemšanas pogu, kad pele ir virs attēla
                    imageWrapper.onmouseover = function() {
                        removeBtn.style.display = 'flex';
                    };
                    imageWrapper.onmouseout = function() {
                        removeBtn.style.display = 'none';
                    };

                    imageWrapper.appendChild(img);
                    imageWrapper.appendChild(removeBtn);
                    container.insertBefore(imageWrapper, container.lastElementChild);
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
