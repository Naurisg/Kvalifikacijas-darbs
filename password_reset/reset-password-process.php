<?php
/*
reset-password-process.php
Apstrādā lietotāja paroles atiestatīšanas saiti, pārbauda tokenu, ļauj ievadīt jaunu paroli, atjaunina paroli
*/

session_start();
require_once '../db_connect.php';

// Ja pieprasījums ir GET, pārbauda tokenu un parāda paroles maiņas formu
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = $_GET['token'] ?? '';
    if (!$token) {
        echo "Nederīgs vai trūkstošs tokens.";
        exit;
    }

    try {
        // Pārbauda, vai tokens eksistē un nav beidzies
        $stmt = $pdo->prepare('SELECT email, expires_at FROM password_resets WHERE token = :token');
        $stmt->execute([':token' => $token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            echo "Nederīgs vai beidzies derīguma termiņš tokenam.";
            exit;
        }

        $expires_at = new DateTime($result['expires_at']);
        $now = new DateTime();

        if ($now > $expires_at) {
            echo "Tokens ir beidzies.";
            exit;
        }

        // Saglabā e-pastu un tokenu sesijā, lai izmantotu POST pieprasījumā
        $_SESSION['reset_email'] = $result['email'];
        $_SESSION['reset_token'] = $token;

        // Parāda formu jaunās paroles ievadei ar apstiprinājumu un acu ikonas pārslēgšanu
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Paroles atiestatīšana</title>
            <link href="../css/normalize.css" rel="stylesheet" type="text/css">
            <link href="../css/main.css" rel="stylesheet" type="text/css">
            <link href="../css/style.css" rel="stylesheet" type="text/css">
        </head>
        <body>
            <div class="w-users-userformpagewrap full-page-wrapper">
                <div class="w-users-userresetpasswordformwrapper admin-form-card center-align">
                    <div class="w-users-userformheader form-card-header">
                        <h2 class="heading h3">Atiestatiet savu paroli</h2>
                    </div>
                    <form method="post" action="reset-password-process.php">
                        <div class="form-field" style="margin-bottom: 5px;">
                            <div style="position: relative;">
                                <input type="password" id="new_password" name="new_password" class="text-field w-input" placeholder="Ievadiet jauno paroli" required minlength="8">
                                <span id="toggle-new-password" style="cursor: pointer; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                                    <img src="../images/eye-icon.png" alt="Show Password" id="eye-icon-new" style="width: 20px; height: 20px;">
                                </span>
                            </div>
                        </div>
                        <div class="form-field" style="margin-bottom: 5px;">
                            <div style="position: relative;">
                                <input type="password" id="confirm_password" name="confirm_password" class="text-field w-input" placeholder="Apstipriniet jauno paroli" required minlength="8">
                                <span id="toggle-confirm-password" style="cursor: pointer; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                                    <img src="../images/eye-icon.png" alt="Show Password" id="eye-icon-confirm" style="width: 20px; height: 20px;">
                                </span>
                            </div>
                        </div>
                        <input type="submit" value="Atiestatīt paroli" class="w-users-userformbutton button w-button">
                    </form>
                </div>
            </div>
            <script>
                document.getElementById('toggle-new-password').addEventListener('click', function() {
                    const passwordField = document.getElementById('new_password');
                    const eyeIcon = document.getElementById('eye-icon-new');
                    if (passwordField.type === 'password') {
                        passwordField.type = 'text';
                        eyeIcon.src = '../images/eye-close.png';
                    } else {
                        passwordField.type = 'password';
                        eyeIcon.src = '../images/eye-icon.png';
                    }
                });
                document.getElementById('toggle-confirm-password').addEventListener('click', function() {
                    const passwordField = document.getElementById('confirm_password');
                    const eyeIcon = document.getElementById('eye-icon-confirm');
                    if (passwordField.type === 'password') {
                        passwordField.type = 'text';
                        eyeIcon.src = '../images/eye-close.png';
                    } else {
                        passwordField.type = 'password';
                        eyeIcon.src = '../images/eye-icon.png';
                    }
                });
            </script>
        </body>
        </html>
        <?php
        exit;
    } catch (Exception $e) {
        echo "Servera kļūda: " . $e->getMessage();
        exit;
    }
// Ja pieprasījums ir POST, apstrādā paroles maiņu
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pārbauda, vai sesijā ir saglabāts e-pasts un tokens
    if (!isset($_SESSION['reset_email'], $_SESSION['reset_token'])) {
        echo "Sesija ir beigusies vai piekļuve nav derīga.";
        exit;
    }

    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Pārbauda paroles garumu
    if (strlen($new_password) < 8) {
        echo "Parolei jābūt vismaz 8 rakstzīmju garai.";
        exit;
    }

    // Pārbauda, vai paroles sakrīt
    if ($new_password !== $confirm_password) {
        $error_message = "Paroles nesakrīt. Lūdzu, mēģiniet vēlreiz.";
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Paroles atiestatīšana</title>
            <link href="../css/normalize.css" rel="stylesheet" type="text/css">
            <link href="../css/main.css" rel="stylesheet" type="text/css">
            <link href="../css/style.css" rel="stylesheet" type="text/css">
            <style>
                .error-message {
                    color: red;
                    font-weight: bold;
                    margin-bottom: 15px;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class="w-users-userformpagewrap full-page-wrapper">
                <div class="w-users-userresetpasswordformwrapper admin-form-card center-align">
                    <div class="w-users-userformheader form-card-header">
                        <h2 class="heading h3">Atiestatiet savu paroli</h2>
                    </div>
                    <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
                    <form method="post" action="reset-password-process.php">
                        <div class="form-field" style="margin-bottom: 5px;">
                            <div style="position: relative;">
                                <input type="password" id="new_password" name="new_password" class="text-field w-input" placeholder="Ievadiet jauno paroli" required minlength="8">
                                <span id="toggle-new-password" style="cursor: pointer; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                                    <img src="../images/eye-icon.png" alt="Show Password" id="eye-icon-new" style="width: 20px; height: 20px;">
                                </span>
                            </div>
                        </div>
                        <div class="form-field" style="margin-bottom: 5px;">
                            <div style="position: relative;">
                                <input type="password" id="confirm_password" name="confirm_password" class="text-field w-input" placeholder="Apstipriniet jauno paroli" required minlength="8">
                                <span id="toggle-confirm-password" style="cursor: pointer; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                                    <img src="../images/eye-icon.png" alt="Show Password" id="eye-icon-confirm" style="width: 20px; height: 20px;">
                                </span>
                            </div>
                        </div>
                        <input type="submit" value="Atiestatīt paroli" class="w-users-userformbutton button w-button">
                    </form>
                </div>
            </div>
            <script>
                document.getElementById('toggle-new-password').addEventListener('click', function() {
                    const passwordField = document.getElementById('new_password');
                    const eyeIcon = document.getElementById('eye-icon-new');
                    if (passwordField.type === 'password') {
                        passwordField.type = 'text';
                        eyeIcon.src = '../images/eye-close.png';
                    } else {
                        passwordField.type = 'password';
                        eyeIcon.src = '../images/eye-icon.png';
                    }
                });
                document.getElementById('toggle-confirm-password').addEventListener('click', function() {
                    const passwordField = document.getElementById('confirm_password');
                    const eyeIcon = document.getElementById('eye-icon-confirm');
                    if (passwordField.type === 'password') {
                        passwordField.type = 'text';
                        eyeIcon.src = '../images/eye-close.png';
                    } else {
                        passwordField.type = 'password';
                        eyeIcon.src = '../images/eye-icon.png';
                    }
                });
            </script>
        </body>
        </html>
        <?php
        exit;
    }

    $email = $_SESSION['reset_email'];
    $token = $_SESSION['reset_token'];

    try {
        // Pārbauda, vai tokens vēl ir derīgs un vai parole nav tāda pati kā iepriekš
        $stmt = $pdo->prepare('SELECT expires_at, password FROM password_resets INNER JOIN clients ON password_resets.email = clients.email WHERE password_resets.token = :token AND password_resets.email = :email');
        $stmt->execute([':token' => $token, ':email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            echo "Nederīgs vai beidzies derīguma termiņš tokenam.";
            exit;
        }

        $expires_at = new DateTime($result['expires_at']);
        $now = new DateTime();

        if ($now > $expires_at) {
            echo "Tokens ir beidzies.";
            exit;
        }

        // Pārbauda, vai jaunā parole nav tāda pati kā vecā
        if (password_verify($new_password, $result['password'])) {
            $error_message = "Jaunā parole nevar būt tāda pati kā pašreizējā parole.";
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Paroles atiestatīšana</title>
                <link href="../css/normalize.css" rel="stylesheet" type="text/css">
                <link href="../css/main.css" rel="stylesheet" type="text/css">
                <link href="../css/style.css" rel="stylesheet" type="text/css">
                <style>
                    .error-message {
                        color: red;
                        font-weight: bold;
                        margin-bottom: 15px;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <div class="w-users-userformpagewrap full-page-wrapper">
                    <div class="w-users-userresetpasswordformwrapper admin-form-card center-align">
                        <div class="w-users-userformheader form-card-header">
                            <h2 class="heading h3">Atiestatiet savu paroli</h2>
                        </div>
                        <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
                        <form method="post" action="reset-password-process.php">
                            <div class="form-field" style="margin-bottom: 5px;">
                                <div style="position: relative;">
                                    <input type="password" id="new_password" name="new_password" class="text-field w-input" placeholder="Ievadiet jauno paroli" required minlength="8">
                                    <span id="toggle-new-password" style="cursor: pointer; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                                        <img src="../images/eye-icon.png" alt="Show Password" id="eye-icon-new" style="width: 20px; height: 20px;">
                                    </span>
                                </div>
                            </div>
                            <div class="form-field" style="margin-bottom: 5px;">
                                <div style="position: relative;">
                                    <input type="password" id="confirm_password" name="confirm_password" class="text-field w-input" placeholder="Apstipriniet jauno paroli" required minlength="8">
                                    <span id="toggle-confirm-password" style="cursor: pointer; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                                        <img src="../images/eye-icon.png" alt="Show Password" id="eye-icon-confirm" style="width: 20px; height: 20px;">
                                    </span>
                                </div>
                            </div>
                            <input type="submit" value="Atiestatīt paroli" class="w-users-userformbutton button w-button">
                        </form>
                    </div>
                </div>
                <script>
                    document.getElementById('toggle-new-password').addEventListener('click', function() {
                        const passwordField = document.getElementById('new_password');
                        const eyeIcon = document.getElementById('eye-icon-new');
                        if (passwordField.type === 'password') {
                            passwordField.type = 'text';
                            eyeIcon.src = '../images/eye-close.png';
                        } else {
                            passwordField.type = 'password';
                            eyeIcon.src = '../images/eye-icon.png';
                        }
                    });
                    document.getElementById('toggle-confirm-password').addEventListener('click', function() {
                        const passwordField = document.getElementById('confirm_password');
                        const eyeIcon = document.getElementById('eye-icon-confirm');
                        if (passwordField.type === 'password') {
                            passwordField.type = 'text';
                            eyeIcon.src = '../images/eye-close.png';
                        } else {
                            passwordField.type = 'password';
                            eyeIcon.src = '../images/eye-icon.png';
                        }
                    });
                </script>
            </body>
            </html>
            <?php
            exit;
        }

        // Šifrē jauno paroli un atjaunina klienta paroli datubāzē
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $pdo->prepare('UPDATE clients SET password = :password WHERE email = :email');
        $update->execute([':password' => $hashed_password, ':email' => $email]);

        // Dzēš izmantoto tokenu no datubāzes
        $delete = $pdo->prepare('DELETE FROM password_resets WHERE token = :token');
        $delete->execute([':token' => $token]);

        // Notīra sesiju
        unset($_SESSION['reset_email'], $_SESSION['reset_token']);
        session_destroy();

        // Parāda veiksmīgas paroles atiestatīšanas ziņu
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Paroles atiestatīšana veiksmīga</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f6f8;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                }
                .success-message {
                    background-color: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                    padding: 20px 30px;
                    border-radius: 8px;
                    font-size: 18px;
                    text-align: center;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                }
                a {
                    color: #155724;
                    text-decoration: underline;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class="success-message">
                Jūsu parole ir veiksmīgi atiestatīta. Tagad varat <a href="../log-in.php">pieteikties</a>.
            </div>
        </body>
        </html>
        <?php
    } catch (Exception $e) {
        echo "Servera kļūda: " . $e->getMessage();
        exit;
    }
} else {
    // Ja metode nav atļauta
    http_response_code(405);
    echo "Metode nav atļauta.";
}
?>
