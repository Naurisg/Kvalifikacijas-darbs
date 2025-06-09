<?php
// Iekļauj failu, kas pārbauda, vai lietotājs ir autentificēts (autorizācijas pārbaude)
require_once 'auth_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rediģēt Klientu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fafafa;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #666;
            background: white;
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
        <h2>Rediģēt Klienta Datus</h2>
        <!-- Forma klienta datu rediģēšanai -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">E-pasts:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="name">Lietotājvārds:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="oldPassword">Pašreizējā parole</label>
                <input type="password" id="oldPassword" name="oldPassword">
            </div>
            <div class="form-group">
                <label for="password">Jaunā parole</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="button-group">
                <button type="submit">Saglabāt izmaiņas</button>
                <button type="button" onclick="window.location.href='admin-panelis.php'">Atgriezties uz administrācijas paneli</button>
            </div>
        </form>
    </div>

    <script>
        // Kad lapa ir ielādēta, aizpilda formas laukus ar esošajiem klienta datiem
        document.addEventListener('DOMContentLoaded', function() {
            // Iegūst klienta ID no URL parametriem
            const urlParams = new URLSearchParams(window.location.search);
            const clientId = urlParams.get('id');

            // Pieprasa klienta datus no servera un aizpilda formu
            fetch(`get_client_details.php?id=${clientId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('email').value = data.client.email;
                        document.getElementById('name').value = data.client.name;
                    }
                });

            // Apstrādā formas iesniegšanu, nosūtot izmaiņas serverim
            document.querySelector('form').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append('id', clientId);

                // Nosūta datus uz serveri, lai saglabātu izmaiņas
                fetch('update_client.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Izmaiņas veiksmīgi saglabātas!');
                        window.location.href = 'admin-panelis.php';
                    } else {
                        alert(data.message || 'Kļūda saglabājot izmaiņas.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Kļūda saglabājot izmaiņas.');
                });
            });
        });
    </script>
</body>
</html>
