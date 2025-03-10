<?php
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
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --success-color: #059669;
            --danger-color: #dc2626;
            --background-color: #f3f4f6;
            --card-background: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--background-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .edit-form {
            width: 100%;
            max-width: 600px;
            background: var(--card-background);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .edit-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        h2 {
            color: var(--text-primary);
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        label {
            display: block;
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }

        input {
            width: 100%;
            padding: 14px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            color: var(--text-primary);
            background: #f9fafb;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: var(--primary-color);
            background: var(--card-background);
            outline: none;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.2);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .error-message {
            color: var(--danger-color);
            font-size: 14px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .success-message {
            color: var(--success-color);
            font-size: 14px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        @media (max-width: 640px) {
            .edit-form {
                padding: 30px 20px;
            }

            h2 {
                font-size: 24px;
            }

            input {
                padding: 12px 16px;
            }
        }

        .back-btn {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 6px 12px;
        background: #f3f4f6;
        border: none;
        border-radius: 6px;
        color: var(--text-secondary);
        font-size: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
        }

        .back-btn:hover {
            background: #e5e7eb;
            color: var(--text-primary);
        }
    </style>
</head>
<body>
    <div class="edit-form">
        <button class="back-btn" onclick="window.location.href='admin-panelis.php'">← Atpakaļ</button>
        <h2>Rediģēt Klienta Datus</h2>
        <form id="editForm">
            <div class="form-group">
                <label for="email">E-pasta Adrese</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="name">Vārds</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="oldPassword">Pašreizējā Parole</label>
                <input type="password" id="oldPassword" name="oldPassword">
                <div class="password-info">* Nepieciešama tikai paroles maiņai</div>
            </div>
            <div class="form-group">
                <label for="password">Jaunā Parole</label>
                <input type="password" id="password" name="password">
                <div class="password-info">* Atstāt tukšu, ja nevēlaties mainīt paroli</div>
            </div>
            <button type="submit" class="submit-btn">Saglabāt Izmaiņas</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const clientId = urlParams.get('id');

            fetch(`get_client_details.php?id=${clientId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('email').value = data.client.email;
                        document.getElementById('name').value = data.client.name;
                    }
                });

            document.getElementById('editForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData();
                formData.append('id', clientId);
                formData.append('email', document.getElementById('email').value);
                formData.append('name', document.getElementById('name').value);
                formData.append('oldPassword', document.getElementById('oldPassword').value);
                formData.append('password', document.getElementById('password').value);

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
