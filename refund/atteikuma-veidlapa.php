<?php include '../header.php'; ?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Atteikuma veidlapa</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8f8f8; margin:0; padding:0; color: #222; }
        .form-container { max-width: 800px; margin: 40px auto; background: #fff; border-radius:10px; box-shadow:0 4px 20px rgba(0,0,0,0.08); padding: 40px 30px; }
        h1 { text-align: center; color: #2c3e50; margin-bottom: 30px; font-size: 2rem; }
        label { display: block; margin-top: 20px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="date"], textarea { width: 100%; padding: 10px; margin-top: 8px; border:1px solid #ccc; border-radius:4px; font-size:1rem; }
        textarea { resize: vertical; height: 120px; }
        .note { font-size: 0.9rem; color: #555; margin-top:5px; }
        @media (max-width: 600px) { .form-container { padding:20px; } h1 { font-size:1.5rem; } }
    </style>
</head>
<body>
<div class="form-container">
    <h1>Atteikuma veidlapa</h1>
    <p>Pēc Patērētāju tiesību aizsardzības likuma, lūdzu aizpildiet zemāk esošo formu, ja vēlaties atteikties no pirkuma.</p>

    <a href="refund/atteikuma-veidlapa.pdf" download class="download-btn" style="display:inline-block;margin-bottom:18px;padding:10px 22px;background:#2c3e50;color:#fff;border-radius:5px;text-decoration:none;font-weight:bold;">Lejupielādēt PDF veidlapu</a>

    <form action="#" method="post">
        <label for="order_number">1. Pasūtījuma numurs</label>
        <input type="text" id="order_number" name="order_number" placeholder="Ievadiet pasūtījuma numuru">

        <label for="order_date">2. Pasūtījuma datums</label>
        <input type="date" id="order_date" name="order_date">

        <label for="received_date">3. Saņemšanas datums</label>
        <input type="date" id="received_date" name="received_date">

        <label for="full_name">4. Patērētāja vārds, uzvārds</label>
        <input type="text" id="full_name" name="full_name" placeholder="Ievadiet vārdu un uzvārdu">

        <label for="address">5. Adrese</label>
        <input type="text" id="address" name="address" placeholder="Ievadiet adresi">

        <label for="phone">6. Telefona numurs</label>
        <input type="text" id="phone" name="phone" placeholder="Ievadiet telefona numuru">

        <label for="email">7. E-pasta adrese</label>
        <input type="email" id="email" name="email" placeholder="Ievadiet e-pasta adresi">

        <label for="reason">8. Atteikuma iemesls <span class="note">(nav obligāti)</span></label>
        <textarea id="reason" name="reason" placeholder="Ja vēlaties, norādiet iemeslu"></textarea>

        <label for="signature">9. Paraksts <span class="note">(ja drukātā formā)</span></label>
        <input type="text" id="signature" name="signature" placeholder="Patērētāja paraksts">

        <label for="date">10. Datums</label>
        <input type="date" id="date" name="date">
    </form>
    <p class="note">Aizpildīto veidlapu lūdzu nosūtīt uz e-pastu <a href="mailto:vissdarbam@gmail.com">vissdarbam@gmail.com</a> vai pa pastu uz uzņēmuma juridisko adresi.</p>
</div>

<?php include '../footer.php'; ?>
</body>
</html>
