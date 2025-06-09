<?php
session_name('admin_session');
session_start();
// Pārbauda, vai lietotājs ir pieteicies sistēmā
if (!isset($_SESSION['user_id'])) {
    header("Location: adminlogin.html"); // Ja nav pieteicies, novirza uz pieteikšanās lapu
    exit();
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Labot Produktu</title>
    <style>
        /* Lapas un formu stila iestatījumi */
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
    <div class="form-container">
        <h2>Labot Produktu</h2>
        <!-- Forma produkta datu labojumiem -->
        <form id="editProductForm" enctype="multipart/form-data">
            <!-- Lauks produkta nosaukuma ievadei -->
            <div class="form-group">
                <label for="nosaukums">Nosaukums:</label>
                <input type="text" id="nosaukums" name="nosaukums" required>
            </div>

            <!-- Lauks produkta apraksta ievadei -->
            <div class="form-group">
                <label for="apraksts">Apraksts:</label>
                <textarea id="apraksts" name="apraksts" required></textarea>
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
                    <option value="Austinas_kiveres_brilles">Austiņas,kiveres,brilles</option>
                    <option value="KrasosanasApgerbs">Krāsošanas apģērbs</option>
                    <option value="Jakas">Jakas</option>
                    <option value="Kimijas">Ķīmijas</option>
                    <option value="Aksesuari">Aksesuāri</option>
                    <option value="Instrumenti">Instrumenti</option>
                </select>
            </div>

            <!-- Lauks produkta cenas ievadei -->
            <div class="form-group">
                <label for="cena">Cena (€):</label>
                <input type="number" id="cena" name="cena" step="0.01" required>
            </div>

            <!-- Lauks produkta daudzuma ievadei -->
            <div class="form-group">
                <label for="quantity">Daudzums:</label>
                <input type="number" id="quantity" name="quantity" min="1" required>
            </div>

            <!-- Produkta izmēru izvēles sadaļa -->
            <div class="form-group" id="sizes-section" style="display: none;">
                <label>Izmēri:</label>
                <div id="sizes-container"></div>
            </div>

            <!-- Produkta attēlu augšupielādes sadaļa -->
            <div class="form-group">
                <label>Bildes:</label>
                <div class="image-upload-container" id="imageUploadContainer">
<span id="addImageBtn" style="display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; width: 120px; height: 120px; border: 2px dashed #999; border-radius: 8px; background-color: #f0f0f0; color: #666; margin-top: 15px;">
    <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 6px;">
        <circle cx="19" cy="19" r="18" stroke="#666" stroke-width="2" fill="none"/>
        <rect x="17" y="9" width="4" height="20" rx="2" fill="#666"/>
        <rect x="9" y="17" width="20" height="4" rx="2" fill="#666"/>
    </svg>
    <span style="font-size: 11px; color: #999; font-weight: 400; letter-spacing: 0.5px;">Pievienot bildi</span>
</span>
                </div>
                <input type="file" id="bildeInput" name="bilde[]" accept="image/*" multiple class="hidden-file-input" />
            </div>

            <!-- Pogas izmaiņu saglabāšanai un atgriešanai -->
            <div class="button-group">
                <a href="admin-panelis.php" class="back-btn">Atpakaļ</a>
                <button type="submit" class="submit-btn">Saglabāt izmaiņas</button>
            </div>
        </form>
    </div>

    <script>
        // --- Svarīgo mainīgo definēšana ---
        const addImageBtn = document.getElementById('addImageBtn');
        const bildeInput = document.getElementById('bildeInput');
        const imageUploadContainer = document.getElementById('imageUploadContainer');
        const form = document.getElementById('editProductForm');

        // Masīvi, kuros glabāt jaunos failus un esošos attēlus
        let selectedFiles = [];
        let existingImages = [];
        let removedImages = [];

        // Iegūst produkta ID no URL parametriem
        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('id');

        const categoryElement = document.getElementById('kategorija');
        const sizesSection = document.getElementById('sizes-section');
        const sizesContainer = document.getElementById('sizes-container');

        // Pieejamie izmēri katrai kategorijai
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

        // Kategorijas izvēles izmaiņu apstrāde
        categoryElement.addEventListener('change', function () {
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
                    wrapper.addEventListener('click', function (e) {
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

        // Iegūst produkta datus un aizpilda formu
        fetch(`get_product.php?id=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Aizpilda formas laukus ar produkta datiem
                    document.getElementById('nosaukums').value = data.product.nosaukums;
                    document.getElementById('apraksts').value = data.product.apraksts;
                    document.getElementById('kategorija').value = data.product.kategorija;
                    document.getElementById('cena').value = data.product.cena;
                    document.getElementById('quantity').value = data.product.quantity;

                    // Iestata izmēru izvēles rūtiņas
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

                            wrapper.style.cursor = 'pointer';
                            wrapper.addEventListener('click', function (e) {
                                if (e.target !== checkbox && e.target.tagName.toLowerCase() !== 'label') {
                                    checkbox.checked = !checkbox.checked;
                                }
                            });

                            sizesContainer.appendChild(wrapper);
                        });
                    }

                    // Ielādē esošās produkta bildes
                    if (data.product.bilde) {
                        existingImages = data.product.bilde.split(',').map(img => img.trim());
                        existingImages.forEach((imagePath, index) => {
                            addImagePreview(imagePath, true, index);
                        });
                    }
                } else {
                    alert('Neizdevās ielādēt produkta datus.');
                }
            })
            .catch(error => {
                console.error('Kļūda ielādējot produkta datus:', error);
                alert('Radās kļūda ielādējot produkta datus.');
            });

        // Pievienot bildes pogas klikšķa apstrāde
        addImageBtn.addEventListener('click', () => {
            bildeInput.click();
        });

        // Jaunu failu izvēles apstrāde
        bildeInput.addEventListener('change', (e) => {
            // Pievieno izvēlētos failus masīvam
            const files = Array.from(e.target.files);
            selectedFiles = [...selectedFiles, ...files];
            updateImagePreviews();
            bildeInput.value = '';
        });

        // Atjaunina attēlu priekšskatījumus
        function updateImagePreviews() {
            // Noņem visus priekšskatījumus, izņemot pievienošanas pogu
            const previews = Array.from(imageUploadContainer.querySelectorAll('.image-preview-wrapper'));
            previews.forEach(preview => imageUploadContainer.removeChild(preview));

            // Pievieno priekšskatījumus esošajiem attēliem (kas nav noņemti)
            existingImages.forEach((imagePath, index) => {
                addImagePreview(imagePath, true, index);
            });

            // Pievieno priekšskatījumus jaunajiem failiem
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    addImagePreview(e.target.result, false, index);
                };
                reader.readAsDataURL(file);
            });

            // Pārliecinās, ka pievienošanas poga paliek pēdējā
            imageUploadContainer.appendChild(addImageBtn);
        }

        // Pievieno viena attēla priekšskatījumu
        // isExisting: true, ja attēls ir no esošajiem, false, ja jauns fails
        // index: indekss attiecīgajā masīvā
        function addImagePreview(src, isExisting, index) {
            const previewWrapper = document.createElement('div');
            previewWrapper.className = 'image-preview-wrapper';
            previewWrapper.dataset.index = index;
            previewWrapper.dataset.existing = isExisting ? 'true' : 'false';

            const img = document.createElement('img');
            img.className = 'image-preview';
            img.src = isExisting ? '../' + src : src;

            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-image-btn';
            removeBtn.innerHTML = '×';
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (isExisting) {
                    // Noņem no esošajiem un pievieno noņemtajiem
                    removedImages.push(existingImages[index]);
                    existingImages.splice(index, 1);
                } else {
                    // Noņem no jaunajiem failiem
                    selectedFiles.splice(index, 1);
                }
                updateImagePreviews();
            });

            previewWrapper.appendChild(img);
            previewWrapper.appendChild(removeBtn);
            imageUploadContainer.insertBefore(previewWrapper, addImageBtn);
        }

        // Formas iesniegšanas apstrāde
        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append('id', productId);

            // Pievieno jaunos failus
            selectedFiles.forEach((file, index) => {
                formData.append(`bilde[${index}]`, file);
            });

            // Pievieno noņemtos attēlus JSON formātā
            formData.append('removedImages', JSON.stringify(removedImages));

            // Nosūta datus serverim, lai atjauninātu produktu
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
                console.error('Kļūda:', error);
                alert('Radās kļūda atjauninot produktu');
            });
        });
    </script>
</body>
</html>