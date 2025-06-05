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

        .image-upload-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }

        .image-preview-wrapper {
            position: relative;
            width: 120px;
            height: 120px;
            border: 1px dashed #ccc;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f9f9f9;
        }

        .image-preview {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .remove-image-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255,0,0,0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 14px;
            transition: all 0.2s;
        }

        .remove-image-btn:hover {
            background: rgba(255,0,0,0.9);
            transform: scale(1.1);
        }

        .add-image-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 120px;
            height: 120px;
            border: 2px dashed #999;
            border-radius: 8px;
            background-color: #f0f0f0;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 24px;
            color: #666;
        }

        .add-image-btn:hover {
            background-color: #e0e0e0;
            border-color: #666;
        }

        .hidden-file-input {
            display: none;
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
                <label>Bildes:</label>
                <div class="image-upload-container" id="imageUploadContainer">
                    <div class="add-image-btn" id="addImageBtn">
                        + Pievienot bildi
                    </div>
                </div>
                <input type="file" id="bildeInput" name="bilde[]" accept="image/*" multiple class="hidden-file-input" />
            </div>

            <!-- Produkta kategorijas izvēles lauks -->
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
        // Attēlu augšupielādes funkcionalitāte
        const addImageBtn = document.getElementById('addImageBtn');
        const bildeInput = document.getElementById('bildeInput');
        const imageUploadContainer = document.getElementById('imageUploadContainer');
        const form = document.getElementById('addProductForm');
        
        // Masīvs, lai saglabātu visus atlasītos failus
        let selectedFiles = [];
        
        // Izsauc faila ievadi, kad tiek noklikšķināts uz pievienošanas pogas
        addImageBtn.addEventListener('click', () => {
            bildeInput.click();
        });
        
        // Apstrādā faila atlasi
        bildeInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            
            // Pievieno jaunus failus mūsu selectedFiles masīvam
            selectedFiles = [...selectedFiles, ...files];
            
            // Update the preview
            updateImagePreviews();
            
            // Atjauno faila ievadi, lai ļautu atkārtoti atlasīt tos pašus failus
            bildeInput.value = '';
        });
        
        // Funkcija attēlu priekšskatījumu atjaunināšanai
        function updateImagePreviews() {
            // Notīra esošos priekšskatījumus (izņemot pievienošanas pogu)
            while (imageUploadContainer.firstChild) {
                imageUploadContainer.removeChild(imageUploadContainer.firstChild);
            }
            
            // Pievieno priekšskatījumu katram atlasītajam failam
            selectedFiles.forEach((file, index) => {
                if (!file.type.match('image.*')) return;
                
                const reader = new FileReader();
                
                reader.onload = (e) => {
                    const previewWrapper = document.createElement('div');
                    previewWrapper.className = 'image-preview-wrapper';
                    previewWrapper.dataset.index = index;
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'image-preview';
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'remove-image-btn';
                    removeBtn.innerHTML = '×';
                    removeBtn.addEventListener('click', (event) => {
                        event.stopPropagation();
                        removeImage(index);
                    });
                    
                    previewWrapper.appendChild(img);
                    previewWrapper.appendChild(removeBtn);
                    imageUploadContainer.appendChild(previewWrapper);
                };
                
                reader.readAsDataURL(file);
            });
            
            // Pievieno pievienošanas pogu atpakaļ beigās
            imageUploadContainer.appendChild(addImageBtn);
        }
        
        // Funkcija attēla dzēšanai
        function removeImage(index) {
            selectedFiles.splice(index, 1);
            updateImagePreviews();
        }
        
        // Apstrādā formas iesniegšanu
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Pārbauda, vai ir izvēlēts vismaz viens attēls
            if (selectedFiles.length === 0) {
                alert('Lūdzu, pievienojiet vismaz vienu bildi!');
                return;
            }
            
            // Izveido FormData un pievieno visus laukus
            const formData = new FormData(form);
            
            // Pievieno visus atlasītos failus
            selectedFiles.forEach((file, index) => {
                formData.append('bilde[]', file);
            });
            
            //Nosūta uz serveri
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

        // Izmēra izvēles funkcionalitāte (no sākotnējā koda)
        const sizesSection = document.getElementById('sizes-section');
        const sizesContainer = document.getElementById('sizes-container');
        const categoryElement = document.getElementById('kategorija');

        const sizesForCategories = {
            cimdi: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'],
            apavi: ['35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48'],
            apgerbs: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'],
            'drosibas-sistemas': ['S', 'M', 'L', 'XL', '2XL'],
            gazmaskas: ['S', 'M', 'L', 'XL'],
            arapgerbs: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'],
            jakas: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'],
            krasosanasapgerbs: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL']
        };

        categoryElement.addEventListener('change', function() {
            const selectedCategory = this.value.toLowerCase();
            sizesContainer.innerHTML = '';

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

                    wrapper.style.cursor = 'pointer';
                    wrapper.addEventListener('click', function(e) {
                        if (e.target !== checkbox && e.target.tagName.toLowerCase() !== 'label') {
                            checkbox.checked = !checkbox.checked;
                        }
                    });

                    sizesContainer.appendChild(wrapper);
                });
            } else {
                sizesSection.style.display = 'none';
            }
        });
    </script>
</body>
</html>