<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';
require_once '../../db_connect.php';

// Pieņem tikai POST pieprasījumus
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metode nav atļauta']);
    exit;
}

// Validē e-pasta adresi
$email = filter_var($_POST['Email'] ?? '', FILTER_VALIDATE_EMAIL);
if (!$email) {
    echo json_encode(['error' => 'Nederīga e-pasta adrese']);
    exit;
}

try {
    // Pārbauda, vai e-pasts eksistē adminu tabulā
    $stmt = $pdo->prepare('SELECT id FROM admin_signup WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Ja e-pasts nav atrasts, neatklāj informāciju par konta esamību
    if (!$result) {
        echo json_encode(['message' => 'Ja atradām kontu, kas saistīts ar šo e-pasta adresi, mēs nosūtījām saiti paroles atiestatīšanai.']);
        exit;
    }

    // Pārbauda, vai nesen jau nav sūtīts paroles atiestatīšanas pieprasījums
    $checkRecentToken = $pdo->prepare('SELECT expires_at FROM password_resets_admin WHERE email = :email ORDER BY expires_at DESC LIMIT 1');
    $checkRecentToken->execute([':email' => $email]);
    $recentTokenResult = $checkRecentToken->fetch(PDO::FETCH_ASSOC);

    if ($recentTokenResult) {
        $expiresAt = new DateTime($recentTokenResult['expires_at']);
        $now = new DateTime();

        // Aprēķina, cik laika pagājis kopš pēdējā tokena izveides
        $creationTime = clone $expiresAt;
        $creationTime->modify('-1 hour');

        $interval = $now->getTimestamp() - $creationTime->getTimestamp();

        // Ja pieprasījums veikts mazāk nekā pirms 2 minūtēm, neļauj atkārtoti sūtīt
        if ($interval < 120) {
            echo json_encode(['error' => 'Jau nesen tika nosūtīta paroles atiestatīšanas saite. Lūdzu, uzgaidiet dažas minūtes pirms mēģināt vēlreiz.']);
            exit;
        }
    }

    // Ģenerē jaunu tokenu un derīguma termiņu
    $token = bin2hex(random_bytes(16));
    $expires_at = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

    // Dzēš visus esošos tokenus šim e-pastam, lai atspējotu vecās paroles atiestatīšanas saites
    $deleteOldTokens = $pdo->prepare('DELETE FROM password_resets_admin WHERE email = :email');
    $deleteOldTokens->execute([':email' => $email]);

    // Ievieto jaunu tokenu paroles atiestatīšanai
    $insert = $pdo->prepare('INSERT INTO password_resets_admin (email, token, expires_at) VALUES (:email, :token, :expires_at)');
    $insert->execute([
        ':email' => $email,
        ':token' => $token,
        ':expires_at' => $expires_at
    ]);

    // Izveido paroles atiestatīšanas saiti
    $reset_link = sprintf(
        '%s/Vissdarbam/admin/password_reset_admin/reset-password-process.php?token=%s',
        (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'],
        $token
    );

    // Sagatavo un nosūta e-pastu ar PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 0; 
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'naurissgg@gmail.com';
        $mail->Password   = 'jbks qyqi mvxk gqth';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('naurissgg@gmail.com', 'Admin Paroles atiestatisana');
        $mail->addAddress($email);

        $mail->isHTML(false);
        $mail->Subject = 'Paroles atjaunosanas pieprasijums';
        $mail->Body    = "Sveiki,\n\nMēs saņēmām pieprasījumu atiestatīt jūsu paroli. Noklikšķiniet uz zemāk esošās saites, lai to atiestatītu:\n\n$reset_link\n\nJa jūs to neesat pieprasījis, lūdzu, ignorējiet šo e-pastu.\n\nPaldies.";

        $mail->send();
        echo json_encode(['message' => 'Ja eksistē konts, kas saistīts ar šo e-pasta adresi, mēs nosūtījām saiti paroles atiestatīšanai.']);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Pasta sūtīšanas kļūda: ' . $mail->ErrorInfo]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Servera kļūda: ' . $e->getMessage()]);
}
?>
