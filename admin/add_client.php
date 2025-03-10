<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: adminlogin.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = new PDO('sqlite:../Datubazes/client_signup.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $email = $_POST['email'];
        $name = $_POST['name'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $accept_privacy_policy = $_POST['accept_privacy_policy'];

        $stmt = $db->prepare("INSERT INTO clients (email, name, password, accept_privacy_policy) VALUES (:email, :name, :password, :accept_privacy_policy)");
        $stmt->execute([
            'email' => $email,
            'name' => $name,
            'password' => $password,
            'accept_privacy_policy' => $accept_privacy_policy
        ]);

        $success_message = "Klients veiksmīgi pievienots!";
    } catch(PDOException $e) {
        $error_message = "Kļūda: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pievienot jaunu klientu</title>
    <style>
        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .success {
            background: #dff0d8;
            color: #3c763d;
        }
        .error {
            background: #f2dede;
            color: #a94442;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Pievienot jaunu klientu</h2>
        
        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Epasts:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="name">Vārds:</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="password">Parole:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="accept_privacy_policy">Piekrītu privātuma politikai:</label>
                <select id="accept_privacy_policy" name="accept_privacy_policy" required>
                    <option value="1">Jā</option>
                    <option value="0">Nē</option>
                </select>
            </div>
            
            <div class="form-group">
                <button type="submit">Pievienot klientu</button>
                <button type="button" onclick="window.location.href='admin-panelis.php'">Atpakaļ uz Admin Paneli</button>
            </div>
        </form>
    </div>
</body>
</html>
