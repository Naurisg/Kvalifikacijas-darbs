<?php
require_once 'db_connect.php';

// Drošas sesijas startēšanas funkcija
function secure_session_start() {
    // Nosaka, vai izmantot drošu (HTTPS) savienojumu
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    $httponly = true;

    // Aktivizē stingro režīmu, lai novērstu neinicializētu sesijas ID izmantošanu
    ini_set('session.use_strict_mode', 1);

    // Uzstāda sesijas sīkdatnes parametrus
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => 'Lax' 
    ]);

    session_start();

    // Sasaista sesiju ar lietotāja pārlūka aģentu un IP adresi
    if (!isset($_SESSION['user_agent'])) {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
    } else {
        // Ja mainās pārlūka aģents vai IP, iespējams sesijas nolaupīšanas mēģinājums
        if ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '') ||
            $_SESSION['user_ip'] !== ($_SERVER['REMOTE_ADDR'] ?? '')) {
            // Iespējama sesijas nolaupīšanas mēģinājums
            $_SESSION = array();
            session_destroy();
            header("Location: log-in.php");
            exit();
        }
    }
}

// Funkcija, kas pārbauda, vai lietotājs ir ielogojies un eksistē datubāzē
function validate_session_user() {
    global $pdo;
    secure_session_start();
    // Ja sesijas lietotāja ID nav iestatīts, pāradresē uz pieteikšanās lapu
    if (!isset($_SESSION['user_id'])) {
        header("Location: log-in.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    try {
        // Sagatavo vaicājumu, lai pārbaudītu, vai lietotāja ID eksistē datubāzē
        $stmt = $pdo->prepare('SELECT id FROM clients WHERE id = :id');
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Ja lietotāja ID nav atrasts, iztīra sesiju un pāradresē uz pieteikšanās lapu
        if (!$result) {
            $_SESSION = array();
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
            header("Location: log-in.php");
            exit();
        }
    } catch (Exception $e) {
        // Kļūdas gadījumā iztīra sesiju un pāradresē uz pieteikšanās lapu
        $_SESSION = array();
        session_destroy();
        header("Location: log-in.php");
        exit();
    }
}
?>
