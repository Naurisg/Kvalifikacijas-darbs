<?php
require_once 'auth_check.php';
require_once '../db_connect.php';

// Apstrādā POST pieprasījumu (formas iesniegšanu)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Iegūst un apstrādā ievadītos datus
        $name = trim($_POST['name']);
        $password = $_POST['password'];
        $email = trim($_POST['email']);
        $role = $_POST['role'];

        // Pārbauda paroles garumu
        if (strlen($password) < 8) {
            $error_message = "Parolei jābūt vismaz 8 simbolus garai.";
        } else {
            // Šifrē paroli
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Pārbauda, vai e-pasts jau eksistē
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM admin_signup WHERE email = :email");
            $checkStmt->execute(['email' => $email]);
            $emailExists = $checkStmt->fetchColumn();

            if ($emailExists) {
                // Ja e-pasts jau reģistrēts, izvada kļūdu
                $error_message = "Šis e-pasts jau ir reģistrēts.";
            } else {
                // Saglabā jauno administratoru datubāzē
                $stmt = $pdo->prepare("INSERT INTO admin_signup (name, password, email, role) 
                    VALUES (:name, :password, :email, :role)");
                $stmt->execute([
                    'name' => $name,
                    'password' => $passwordHash,
                    'email' => $email,
                    'role' => $role
                ]);
                // Veiksmīgas pievienošanas ziņojums
                $success_message = "Administrators veiksmīgi pievienots!";
            }
        }
    } catch(PDOException $e) {
        // Kļūdas apstrāde
        $error_message = "Kļūda: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pievienot administratoru</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background-color: #f7f7f7;
            font-family: 'Arial', sans-serif;
            padding: 40px 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input, select {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .toggle-password {
            position: absolute;
            top: 38px;
            right: 12px;
            cursor: pointer;
            color: #888;
        }

        .message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .success {
            background-color: #e7f5e7;
            color: #2e7d32;
            border-left: 5px solid #2e7d32;
        }

        .error {
            background-color: #fcebea;
            color: #c62828;
            border-left: 5px solid #c62828;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: space-between;
        }

        button {
            padding: 12px;
            width: 100%;
            font-weight: bold;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        button[type="submit"] {
            background-color: #333;
            color: white;
        }

        button[type="submit"]:hover {
            background-color: #444;
        }

        button[type="button"] {
            background-color: #999;
            color: white;
        }

        button[type="button"]:hover {
            background-color: #777;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Pievienot jaunu administratoru</h2>

        <?php if (isset($success_message)): ?>
            <!-- Veiksmīgas pievienošanas paziņojums ar automātisku pāradresāciju -->
            <div class="message success">
                <?php echo $success_message; ?>
                <br>
                <span id="redirect-countdown">Pārvirzīšana pēc <span id="countdown">2</span> sekundēm...</span>
            </div>
            <script>
                // Atpakaļskaitīšana un pāradresācija uz admin paneli
                let seconds = 2;
                const countdownSpan = document.getElementById('countdown');
                const interval = setInterval(function() {
                    seconds--;
                    countdownSpan.textContent = seconds;
                    if (seconds <= 0) {
                        clearInterval(interval);
                        window.location.href = 'admin-panelis.php';
                    }
                }, 1000);
            </script>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <!-- Kļūdas paziņojums -->
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="name">Lietotājvārds:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="password">Parole:</label>
                <input type="password" id="password" name="password" required>
                <!-- Paroles redzamības pārslēgšanas ikona -->
                <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
            </div>

            <div class="form-group">
                <label for="email">E-pasts:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="role">Loma:</label>
                <select id="role" name="role" required>
                    <option value="" disabled selected>Izvēlieties lomu</option>
                    <option value="Admin">Admin</option>
                    <option value="Mod">Mod</option>
                </select>
            </div>

            <div class="button-group">
                <button type="submit">Saglabāt</button>
                <button type="button" onclick="window.location.href='admin-panelis.php'">Atpakaļ</button>
            </div>
        </form>
    </div>

    <script>
        // Paroles redzamības pārslēgšana
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const icon = document.querySelector('.toggle-password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>
