<?php
/*
reset-password-process.php
Apstrādā paroles atiestatīšanas saiti, pārbauda tokenu, ļauj ievadīt jaunu paroli, atjaunina paroli
*/

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = $_GET['token'] ?? '';
    if (!$token) {
        echo "Nederīgs vai trūkstošs tokens.";
        exit;
    }

    try {
        $db = new SQLite3('../Datubazes/client_signup.db');
        $stmt = $db->prepare('SELECT email, expires_at FROM password_resets WHERE token = :token');
        $stmt->bindValue(':token', $token, SQLITE3_TEXT);
        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

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

        $_SESSION['reset_email'] = $result['email'];
        $_SESSION['reset_token'] = $token;

        // Parāda formu jaunas paroles ievadei
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Paroles atiestatīšana</title>
        </head>
        <body>
            <h2>Atiestatiet savu paroli</h2>
            <form method="post" action="reset-password-process.php">
                <label for="new_password">Jaunā parole:</label><br>
                <input type="password" id="new_password" name="new_password" required minlength="8"><br><br>
                <input type="submit" value="Atiestatīt paroli">
            </form>
        </body>
        </html>
        <?php
        exit;
    } catch (Exception $e) {
        echo "Servera kļūda: " . $e->getMessage();
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['reset_email'], $_SESSION['reset_token'])) {
        echo "Sesija ir beigusies vai piekļuve nav derīga.";
        exit;
    }

    $new_password = $_POST['new_password'] ?? '';
    if (strlen($new_password) < 8) {
        echo "Parolei jābūt vismaz 8 rakstzīmju garai.";
        exit;
    }

    $email = $_SESSION['reset_email'];
    $token = $_SESSION['reset_token'];

    try {
        $db = new SQLite3('../Datubazes/client_signup.db');

        // Pārbauda, vai tokens joprojām ir derīgs
        $stmt = $db->prepare('SELECT expires_at FROM password_resets WHERE token = :token AND email = :email');
        $stmt->bindValue(':token', $token, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

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

        // Atjaunina paroli klientu tabulā
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $db->prepare('UPDATE clients SET password = :password WHERE email = :email');
        $update->bindValue(':password', $hashed_password, SQLITE3_TEXT);
        $update->bindValue(':email', $email, SQLITE3_TEXT);
        $update->execute();

        // Dzēš tokenu no password_resets tabulas
        $delete = $db->prepare('DELETE FROM password_resets WHERE token = :token');
        $delete->bindValue(':token', $token, SQLITE3_TEXT);
        $delete->execute();

        // Notīra sesijas mainīgos
        unset($_SESSION['reset_email'], $_SESSION['reset_token']);

        echo "Jūsu parole ir veiksmīgi atiestatīta. Tagad varat <a href='../log-in.php'>pieteikties</a>.";
    } catch (Exception $e) {
        echo "Servera kļūda: " . $e->getMessage();
        exit;
    }
} else {
    http_response_code(405);
    echo "Metode nav atļauta.";
}
?>
