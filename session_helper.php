<?php
// Funkcija, kas pārbauda, vai sesijas lietotāja ID eksistē datubāzē

// Drošas sesijas startēšanas funkcija
function secure_session_start() {
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    $httponly = true;

// Izmanto stingro režīmu, lai novērstu neinicializētu sesijas ID izmantošanu
    ini_set('session.use_strict_mode', 1);

//sesijas sīkdatnes parametri
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => 'Lax' 
    ]);

    session_start();
}

// Aizstāj session_start() izsaukumus šajā failā ar secure_session_start()
function validate_session_user() {
    secure_session_start();
    // Ja sesijas lietotāja ID nav iestatīts, pāradresē uz pieteikšanās lapu
    if (!isset($_SESSION['user_id'])) {
        header("Location: log-in.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    try {
        // Izveido savienojumu ar SQLite datubāzi
        $db = new SQLite3('Datubazes/client_signup.db');
        // Sagatavo vaicājumu, lai pārbaudītu, vai lietotāja ID eksistē
        $stmt = $db->prepare('SELECT id FROM clients WHERE id = :id');
        $stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

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
