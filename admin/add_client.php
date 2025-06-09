<?php
require_once 'auth_check.php';
require_once '../db_connect.php';

// Apstrādā POST pieprasījumu (formas iesniegšanu)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Iegūst ievadītos datus no formas
        $email = $_POST['email'];
        $name = $_POST['name'];
        $password = $_POST['password'];
        // Pārbauda, vai lietotājs ir piekritis privātuma politikai
        $accept_privacy_policy = isset($_POST['accept_privacy_policy']) && $_POST['accept_privacy_policy'] === '1' ? 1 : 0;

        // Pārbauda, vai e-pasts jau eksistē klientu tabulā
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM clients WHERE email = :email");
        $checkStmt->execute(['email' => $email]);
        if ($checkStmt->fetchColumn() > 0) {
            $error_message = "Šis e-pasts jau ir reģistrēts!";
        } elseif (strlen($password) < 8) {
            // Pārbauda paroles garumu
            $error_message = "Parolei jābūt vismaz 8 simbolus garai.";
        } elseif (!$accept_privacy_policy) {
            // Pārbauda, vai piekrišana privātuma politikai ir atzīmēta
            $error_message = "Lai pievienotu klientu, jāpiekrīt privātuma politikai.";
        } else {
            // Šifrē paroli un pievieno klientu datubāzē
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO clients (email, name, password, accept_privacy_policy) VALUES (:email, :name, :password, :accept_privacy_policy)");
            $stmt->execute([
                'email' => $email,
                'name' => $name,
                'password' => $passwordHash,
                'accept_privacy_policy' => $accept_privacy_policy
            ]);
            $success_message = "Klients veiksmīgi pievienots!";
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
    <title>Pievienot jaunu klientu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

        .checkbox-label {
            display: inline;
            font-weight: 400;
            margin-left: 6px;
        }

        input[type="checkbox"] {
            width: auto;
            margin-right: 6px;
            accent-color: #333;
            vertical-align: middle;
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

        .password-wrapper {
            position: relative;
        }

        .password-wrapper i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
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
        <h2>Pievienot jaunu klientu</h2>
        
        <?php if (isset($success_message)): ?>
            <div class="message success" style="background-color: #e7f5e7; color: #2e7d32; border-left: 5px solid #2e7d32;">
                <?php echo $success_message; ?>
                <br>
                <span id="redirect-countdown">Pāradresācija pēc <span id="countdown">2</span> sekundēm...</span>
            </div>
            <script>
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
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">E-pasts:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="name">Vārds:</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="password">Parole:</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required>
                    <i class="fas fa-eye" id="togglePassword"></i>
                </div>
            </div>
            
            <div class="form-group">
                <input type="checkbox" id="accept_privacy_policy" name="accept_privacy_policy" value="1" required>
                <label for="accept_privacy_policy" class="checkbox-label">Piekrītu privātuma politikai</label>
            </div>
            
            <div class="button-group">
                <button type="submit">Pievienot klientu</button>
                <button type="button" onclick="window.location.href='admin-panelis.php'">Atgriezties uz administrācijas paneli</button>
            </div>
        </form>
    </div>

    <script>
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");

        togglePassword.addEventListener("click", function () {
            const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
            passwordInput.setAttribute("type", type);
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    </script>
</body>
</html>
