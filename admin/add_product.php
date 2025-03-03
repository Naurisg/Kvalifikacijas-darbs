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
    <title>Pievienot Produktu</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #2c3e50, #bdc3c7);
            margin: 0;
            padding: 20px;
        }

        .form-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #4A4A4A;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #007bff;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 10px;
        }

        textarea {
            height: 150px;
            resize: vertical;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .submit-btn, .back-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .submit-btn {
            background-color: #28a745;
            color: white;
        }

        .back-btn {
            background-color: #6c757d;
            color: white;
            text-decoration: none;
        }

        .submit-btn:hover, .back-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Pievienot Jaunu Produktu</h2>
        <form id="addProductForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nosaukums">Nosaukums:</label>
                <input type="text" id="nosaukums" name="nosaukums" required>
            </div>

            <div class="form-group">
                <label for="apraksts">Apraksts:</label>
                <textarea id="apraksts" name="apraksts" required></textarea>
            </div>

            <div class="form-group">
                <label for="bilde">Bilde:</label>
                <input type="file" id="bilde" name="bilde" accept="image/*" required>
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
                    <option value="ArApgerbs">Augstas redzamības apgerbs</option>
                    <option value="Austinas_kiveres_brilles">Austinas,kiveres,brilles</option>
                    <option value="KrasoApgerbs">Krāsošanas apģērbs</option>
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

            <div class="button-group">
                <a href="admin-panelis.php" class="back-btn">Atpakaļ</a>
                <button type="submit" class="submit-btn">Pievienot Produktu</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
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
    </script>
</body>
</html>
