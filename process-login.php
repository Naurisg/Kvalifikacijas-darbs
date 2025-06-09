<?php
require_once 'session_helper.php';
// Uzsāk drošu sesiju
secure_session_start();
header('Content-Type: application/json');

require_once 'db_connect.php';

// Pārbauda, vai reCAPTCHA ir aizpildīta
if (!isset($_POST['g-recaptcha-response'])) {
    echo json_encode(['success' => false, 'message' => 'Lūdzu, apstipriniet, ka neesat robots.']);
    exit;
}

// Sagatavo datus reCAPTCHA pārbaudei
$recaptchaResponse = $_POST['g-recaptcha-response'];
$secretKey = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';

$verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
$data = [
    'secret' => $secretKey,
    'response' => $recaptchaResponse,
    'remoteip' => $_SERVER['REMOTE_ADDR']
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ],
];

$context  = stream_context_create($options);
$verifyResponse = file_get_contents($verifyUrl, false, $context);
$responseData = json_decode($verifyResponse);

// Ja reCAPTCHA nav veiksmīga, pārtrauc izpildi
if (!$responseData->success) {
    echo json_encode(['success' => false, 'message' => 'reCAPTCHA verifikācija neizdevās. Lūdzu, mēģiniet vēlreiz.']);
    exit;
}

try {
    // Iegūst lietotāja ievadīto e-pastu un paroli
    $email = $_POST['Email'];
    $password = $_POST['Password'];

    // Meklē lietotāju pēc e-pasta
    $stmt = $pdo->prepare('SELECT * FROM clients WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Ja lietotājs atrasts un parole sakrīt
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true); 
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        echo json_encode(['success' => true, 'redirect' => 'index.html']);
    } else {
        // Nepareizs e-pasts vai parole
        echo json_encode(['success' => false, 'message' => 'Nepareizs e-pasts vai parole']);
    }
} catch (PDOException $e) {
    // Apstrādā datubāzes kļūdu
    echo json_encode(['success' => false, 'message' => 'Datubāzes kļūda: ' . $e->getMessage()]);
}
?>
