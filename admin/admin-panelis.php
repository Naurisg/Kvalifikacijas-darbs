<?php
// Sāk sesiju ar īpašu sesijas nosaukumu priekš admina
session_name('admin_session');
session_start();
// Pārbauda, vai lietotājs ir ielogojies, ja nav - pāradresē uz admina login lapu
if (!isset($_SESSION['user_id'])) {
    header("Location: adminlogin.html");
    exit();
}

// Iegūst lietotāja lomu un admina ID no sesijas
$user_role = $_SESSION['user_role'] ?? '';
$admin_id = $_SESSION['user_id'] ?? '';

require_once '../db_connect.php';

try {
    // Iegūst admina vārdu no datubāzes pēc ID
    $stmt = $pdo->prepare('SELECT name FROM admin_signup WHERE id = :id');
    $stmt->execute(['id' => $admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    $admin_name = $admin['name'] ?? 'Admin';
} catch (PDOException $e) {
    $admin_name = 'Admin';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panelis</title>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #f8f8f8;
            margin: 0;
            padding: 0;
            color: #333;
        }

        section {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #2c3e50;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tr:hover {
            background-color: #f0f0f0;
        }

.logout-button, .toggle-button {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
            font-weight: bold;
        }

        .logout-button {
            background-color: #dc3545; 
        }

        .logout-button:hover {
            background-color: #c82333; 
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .toggle-button:hover {
            background-color: #555;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .edit-button, .approval-button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11px;
            font-weight: bold;
            background-color: #2c3e50;
            color: white;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none; 
        }

        .edit-button:hover, .approval-button:hover {
            background-color: #1a252f;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .search-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
        }

        .search-input {
            width: 50%;
            padding: 12px 20px;
            border: 2px solid #2c3e50;
            border-radius: 25px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 0 8px rgba(44, 62, 80, 0.5);
            width: 60%;
        }

        .add-button {
            padding: 12px 24px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 13px;
            transition: all 0.3s ease;
            margin-left: 10px;
        }

        .add-button:hover {
            background-color: #218838;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .soldout-button {
            padding: 12px 24px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 13px;
            transition: all 0.3s ease;
            margin-left: 10px;
        }

        .soldout-button:hover,
        .soldout-button.active {
            background-color: #c82333;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .role-select {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            cursor: pointer;
        }

        .role-select:hover {
            border-color: #2c3e50;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            margin-top: 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .logo {
            position: absolute;
            left: 0;
            width: 100px;
            height: auto;
        }

        .header-container {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        #orderModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            z-index: 10000;
            overflow-y: auto;
            transition: opacity 0.3s ease;
        }

        .modal-content {
            background: #fff;
            margin: 2% auto;
            padding: 20px 25px;
            width: 85%;
            max-width: 700px;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            position: relative;
            font-family: 'Arial', sans-serif;
            color: #222;
            font-size: 14px;
            line-height: 1.4;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #ccc;
        }

        .modal-header h2 {
            font-weight: 700;
            font-size: 1.4rem;
            color: #000;
            margin: 0;
            letter-spacing: 0.02em;
        }

        .modal-close {
            font-size: 28px;
            font-weight: 700;
            cursor: pointer;
            color: #555;
            transition: color 0.3s ease, transform 0.3s ease;
            user-select: none;
        }

        .modal-close:hover {
            color: #000;
            transform: rotate(90deg);
        }

        .modal-body {
            max-height: 50vh;
            overflow-y: auto;
            padding-right: 8px;
            scrollbar-width: thin;
            scrollbar-color: #888 transparent;
        }

        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 3px;
        }

        .modal-summary {
            background: #f5f5f5;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.05);
            color: #333;
            font-size: 13px;
        }

        .modal-summary > div:first-child {
            line-height: 1.3;
        }

        .modal-summary strong {
            color: #000;
            font-weight: 600;
        }

        .modal-total {
            font-size: 16px;
            font-weight: 700;
            color: #000;
            text-align: right;
            min-width: 140px;
            line-height: 1.3;
        }

        .modal-total div {
            margin-bottom: 4px;
        }

        .modal-address {
            background: #fafafa;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.03);
            color: #333;
            font-size: 13px;
            line-height: 1.4;
        }

        .modal-address h3 {
            margin-top: 0;
            margin-bottom: 8px;
            font-weight: 700;
            color: #000;
            font-size: 1.1rem;
            letter-spacing: 0.02em;
        }

        .order-details-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 6px;
            font-size: 13px;
            color: #222;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.07);
            border-radius: 6px;
            overflow: hidden;
        }

        .order-details-table thead tr {
            background: #333;
            color: #fff;
            font-weight: 600;
            font-size: 13px;
            letter-spacing: 0.04em;
        }

        .order-details-table th {
            padding: 10px 12px;
            text-align: left;
            border-bottom: none;
        }

        .order-details-table tbody tr {
            background: #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
            border-radius: 6px;
            transition: background-color 0.25s ease;
        }

        .order-details-table tbody tr:hover {
            background: #f0f0f0;
        }

        .order-details-table td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: none;
            vertical-align: middle;
        }

        .order-details-table img {
            max-width: 50px;
            max-height: 50px;
            object-fit: cover;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            section {
                margin: 20px;
                padding: 15px;
            }

            h2 {
                font-size: 1.5rem;
            }

            .logout-button, .toggle-button {
                padding: 10px 20px;
            }

            .modal-content {
                width: 95%;
                margin: 2% auto;
                padding: 15px;
            }
            
            .modal-body {
                max-height: 70vh;
            }
        }

        /* ---Responsivitāte --- */
        @media (max-width: 1200px) {
            section {
                margin: 10px;
                padding: 10px;
                border-radius: 6px;
            }
            .header-container {
                flex-direction: column;
                align-items: flex-start;
            }
            .header-container h2 {
                text-align: center;
                width: 100%;
            }
            .logout-button {
                text-align: center;
            }
            .logo {
                width: 80px;
                margin-bottom: 10px;
            }
            h2 {
                font-size: 1.2rem;
                margin-bottom: 12px;
            }
            .search-container {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
                width: 100%;
                padding: 0 0 6px 0;
                box-sizing: border-box;
            }
            .search-input, #categoryFilter {
                width: 98vw !important;
                max-width: 100%;
                min-width: 0;
                font-size: 15px;
                padding: 10px 12px;
                margin: 0 auto;
                box-sizing: border-box;
                display: block;
            }
            #categoryFilter {
                display: block;
                margin: 12px auto 12px auto !important;
                text-align: center;
            }
            .add-button, .soldout-button, .logout-button, .toggle-button {
                width: 100%;
                margin: 6px 0;
                font-size: 15px;
                padding: 12px 0;
            }
            table, thead, tbody, th, td, tr {
                display: block;
                width: 100%;
            }
            thead {
                display: none;
            }
            tr {
                margin-bottom: 18px;
                border-bottom: 2px solid #eee;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.03);
                padding: 10px 0;
            }
            td {
                padding: 10px 12px;
                text-align: left;
                border: none;
                position: relative;
                font-size: 15px;
            }
            td:before {
                content: attr(data-label);
                font-weight: bold;
                display: block;
                margin-bottom: 2px;
                color: #2c3e50;
                font-size: 13px;
            }
            .edit-button, .delete-btn, .approval-button {
                width: 100%;
                margin: 4px 0 0 0;
                font-size: 14px;
                padding: 10px 0;
                box-sizing: border-box;
                display: block;
                text-align: center;
            }
            #admin-table td .edit-button,
            #admin-table td .approval-button,
            #admin-table td .delete-btn {
                width: 100%;
                margin: 4px 0 0 0;
                font-size: 14px;
                padding: 10px 0;
                box-sizing: border-box;
                display: block;
            }
            #admin-table td .edit-button:first-child,
            #admin-table td .approval-button:first-child,
            #admin-table td .delete-btn:first-child {
                margin-top: 0;
            }
            .size-badge {
                font-size: 12px;
                padding: 3px 7px;
            }
            .order-details-table, .order-details-table thead, .order-details-table tbody, .order-details-table tr, .order-details-table td, .order-details-table th {
                display: block;
                width: 100%;
            }
            .order-details-table tr {
                margin-bottom: 12px;
                border-bottom: 1px solid #eee;
            }
            .order-details-table td, .order-details-table th {
                padding: 8px 10px;
                font-size: 14px;
            }
            .order-details-table td:before {
                content: attr(data-label);
                font-weight: bold;
                display: block;
                margin-bottom: 2px;
                color: #2c3e50;
                font-size: 12px;
            }
            .order-details-table img {
                max-width: 45px;
                max-height: 45px;
            }
            .modal-content {
                width: 99%;
                min-width: unset;
                padding: 8px;
            }
            .modal-summary, .modal-address {
                flex-direction: column;
                align-items: flex-start;
                font-size: 14px;
            }
        }
        @media (max-width: 600px) {
            section {
                margin: 5px;
                padding: 5px;
                border-radius: 0;
            }
            .header-container {
                flex-direction: column;
                align-items: flex-start;
            }
            .header-container h2 {
                text-align: center;
                width: 100%;
            }
            .logout-button {
                text-align: center;
            }
            .logo {
                width: 70px;
                margin-bottom: 10px;
            }
            h2 {
                font-size: 1.1rem;
                margin-bottom: 10px;
            }
            .search-container {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
                width: 100%;
                padding: 0 0 4px 0;
                box-sizing: border-box;
            }
            .search-input, #categoryFilter {
                width: 96vw !important;
                max-width: 100%;
                min-width: 0;
                font-size: 14px;
                padding: 8px 10px;
                margin: 0 auto;
                box-sizing: border-box;
                display: block;
            }
            #categoryFilter {
                display: block;
                margin: 10px auto 10px auto !important;
                text-align: center;
            }
            .add-button, .soldout-button, .logout-button, .toggle-button {
                width: 100%;
                margin: 5px 0;
                font-size: 14px;
                padding: 10px 0;
            }
            table, thead, tbody, th, td, tr {
                display: block;
                width: 100%;
            }
            thead {
                display: none;
            }
            tr {
                margin-bottom: 15px;
                border-bottom: 2px solid #eee;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.03);
                padding: 8px 0;
            }
            td {
                padding: 8px 10px;
                text-align: left;
                border: none;
                position: relative;
                font-size: 14px;
            }
            td:before {
                content: attr(data-label);
                font-weight: bold;
                display: block;
                margin-bottom: 2px;
                color: #2c3e50;
                font-size: 12px;
            }
            .edit-button, .delete-btn, .approval-button {
                width: 100%;
                margin: 3px 0 0 0;
                font-size: 13px;
                padding: 8px 0;
                box-sizing: border-box;
                display: block;
                text-align: center;
            }
            #admin-table td .edit-button,
            #admin-table td .approval-button,
            #admin-table td .delete-btn {
                width: 100%;
                margin: 3px 0 0 0;
                font-size: 13px;
                padding: 8px 0;
                box-sizing: border-box;
                display: block;
            }
            #admin-table td .edit-button:first-child,
            #admin-table td .approval-button:first-child,
            #admin-table td .delete-btn:first-child {
                margin-top: 0;
            }
            .size-badge {
                font-size: 11px;
                padding: 2px 6px;
            }
            .order-details-table, .order-details-table thead, .order-details-table tbody, .order-details-table tr, .order-details-table td, .order-details-table th {
                display: block;
                width: 100%;
            }
            .order-details-table tr {
                margin-bottom: 10px;
                border-bottom: 1px solid #eee;
            }
            .order-details-table td, .order-details-table th {
                padding: 6px 8px;
                font-size: 13px;
            }
            .order-details-table td:before {
                content: attr(data-label);
                font-weight: bold;
                display: block;
                margin-bottom: 2px;
                color: #2c3e50;
                font-size: 11px;
            }
            .order-details-table img {
                max-width: 40px;
                max-height: 40px;
            }
            .modal-content {
                width: 99%;
                min-width: unset;
                padding: 5px;
            }
            .modal-summary, .modal-address {
                flex-direction: column;
                align-items: flex-start;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
<section>
    <div class="header-container" style="position: relative;">
        <img src="../images/Logo.png" alt="Logo" class="logo">
        <h2>Sveiki, <?php echo htmlspecialchars($admin_name); ?>!</h2>
        <button id="burger-button" aria-label="Menu" aria-expanded="false" aria-controls="burger-menu" onclick="toggleBurgerMenu()" style="display:none; position: absolute; top: 10px; right: 10px; background: none; border: none; cursor: pointer; z-index: 1001;">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="5" width="30" height="3" fill="#333"/>
                <rect y="13.5" width="30" height="3" fill="#333"/>
                <rect y="22" width="30" height="3" fill="#333"/>
            </svg>
        </button>
        <div id="burger-menu" style="display:none; position: absolute; top: 45px; right: 10px; background: white; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); z-index: 1000; min-width: 150px;">
        <?php if ($user_role !== 'Mod'): ?>
        <button class="burger-menu-item" onclick="showTable('admin'); toggleBurgerMenu();">Admini</button>
        <?php endif; ?>
            <button class="burger-menu-item" onclick="showTable('client'); toggleBurgerMenu();">Klienti</button>
            <button class="burger-menu-item" onclick="showTable('product'); toggleBurgerMenu();">Produkti</button>
            <button class="burger-menu-item" onclick="showTable('subscriber'); toggleBurgerMenu();">Abonenti</button>
            <button class="burger-menu-item" onclick="showTable('contact'); toggleBurgerMenu();">Kontakti</button>
            <button class="burger-menu-item" onclick="showTable('orders'); toggleBurgerMenu();">Pasūtījumi</button>
            <button class="burger-menu-item" onclick="showTable('reviews'); toggleBurgerMenu();">Atsauksmes</button>
        </div>
    </div>
        <?php if ($user_role !== 'Mod'): ?>
        <button class="toggle-button" onclick="showTable('admin')">Admini</button>
        <?php endif; ?>

        <?php if ($user_role === 'Mod'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showTable('client');
            });
        </script>
        <?php endif; ?>
    <button class="toggle-button" onclick="showTable('client')">Klienti</button>
    <button class="toggle-button" onclick="showTable('product')">Produkti</button>
    <button class="toggle-button" onclick="showTable('subscriber')">Abonenti</button>
    <button class="toggle-button" onclick="showTable('contact')">Kontakti</button>
    <button class="toggle-button" onclick="showTable('orders')">Pasūtījumi</button>
    <button class="toggle-button" onclick="showTable('reviews')">Atsauksmes</button>
    
    <a href="logout.php" class="logout-button">Iziet</a>

        <?php if ($user_role !== 'Moderators'): ?>
        <h2 id="admin-header" style="display: none;">Admini</h2>
        <div class="search-container" id="admin-actions" style="display: none;">
            <input type="text" id="adminSearchInput" class="search-input" placeholder="Meklēt pēc Epasta, Vārda vai Lomas...">
            <button class="add-button" onclick="addNewAdmin()">+Pievienot adminu</button>
        </div>
        <table id="admin-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Epasts</th>
                <th>Vārds</th>
                <th>Pakāpe</th>
                <th>Apstiprināts</th>
                <th>Izveidots</th>
                <th>Darbības</th>
            </tr>
            </thead>
<tbody>
            </tbody>

<style>
.size-badge {
    display: inline-block;
    background-color: #2c3e50;
    color: white;
    padding: 3px 8px;
    margin: 2px 3px;
    border-radius: 12px;
    font-size: 12px;
    white-space: nowrap;
}
#product-table td:nth-child(8) {
    max-width: 110px;
    white-space: normal;
    word-wrap: break-word;
}
</style>

<script>
fetch('get_products.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const products = data.products.sort((a, b) => new Date(b.created_at) - new Date(a.created_at)); // Sort by newest
            const tbody = document.querySelector("#product-table tbody");
            tbody.innerHTML = ''; 
            products.forEach(product => {
                const sizes = product.sizes || '';
                const sizeArray = sizes.split(/[, ]+/).filter(s => s.trim() !== '');
                const sizeBadges = sizeArray.map(s => `<span class="size-badge">${s}</span>`).join(' ');
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.nosaukums}</td>
                    <td>${product.apraksts}</td>
            <td>
                ${(function() {
                    try {
                        const images = product.bilde.split(',');
                        return images.map(image => `<img src="../${image}" style="width: 50px; height: 50px; object-fit: cover; margin-right: 5px; border-radius: 4px;">`).join('');
                    } catch (e) {
                        return `<img src="../${product.bilde}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">`;
                    }
                })()}
            </td>
                    <td>${product.kategorija}</td>
                    <td>${product.cena}€</td>
                    <td>${product.quantity}</td>
                    <td>${sizeBadges || 'Nav norādīts'}</td>
                    <td>
                        <a href="productedit.php?id=${product.id}" class="edit-button">Labot</a>
                        <button class="delete-btn" onclick="deleteProduct(${product.id})">Dzēst</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
    });
</script>
    </table>
    <?php endif; ?>

    <h2 id="client-header" style="display: none;">Reģistrētie klienti</h2>
    <div class="search-container" id="client-actions" style="display: none;">
        <input type="text" id="clientSearchInput" class="search-input" placeholder="Meklēt pēc Vārda vai Epasta...">
        <button class="add-button" onclick="addNewClient()">+Pievienot klientu</button>
    </div>
    <table id="client-table">
        <thead>
        <tr>
        <th>ID</th>
        <th>Epasts</th>
        <th>Vārds</th>
        <!-- <th>Apstiprināts</th>  Noņemts -->
        <th>Izveidots</th>
        <th>Darbības</th>
    </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <h2 id="product-header" style="display: none;">Produkti</h2>
    <div class="search-container" id="product-search" style="display: none;">
        <input type="text" id="productSearchInput" class="search-input" placeholder="Meklēt pēc ID, Nosaukuma, Apraksta vai Cenas...">
        <select id="categoryFilter" class="search-input" style="width: auto; margin-left: 10px;">
            <option value="">Visas kategorijas</option>
            <option value="Cimdi">Cimdi</option>
            <option value="Apavi">Apavi</option>
            <option value="Apgerbs">Apģērbs</option>
            <option value="Drosibas-sistemas">Drošības sistēmas</option>
            <option value="Gazmaskas">Gazmaskas</option>
            <option value="Arapgerbs">Augstas redzamības apgerbs</option>
            <option value="Austinas_kiveres_brilles">Austinas, kiveres, brilles</option>
            <option value="KrasosanasApgerbs">Krāsošanas apģērbs</option>
            <option value="Jakas">Jakas</option>
            <option value="Kimijas">Ķīmijas</option>
            <option value="Aksesuari">Aksesuāri</option>
            <option value="Instrumenti">Instrumenti</option>
        </select>
        <button class="add-button" onclick="addNewProduct()">Pievienot +</button>
        <button id="soldOutBtn" class="soldout-button" onclick="toggleSoldOut()">Izpārdotie</button>
    </div>
    <table id="product-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nosaukums</th>
                <th>Apraksts</th>
                <th>Bilde</th>
                <th>Kategorija</th>
                <th>Cena</th>
                <th>Skaits</th>
                <th>Izmēri</th>
                <th>Darbības</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <h2 id="subscriber-header" style="display: none;">Abonenti</h2>
    <div class="search-container" id="subscriber-actions" style="display: none;">
        <input type="text" id="subscriberSearchInput" class="search-input" placeholder="Meklēt pēc Epasta...">
    </div>
    <table id="subscriber-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Epasts</th>
                <th>Pievienošanās datums</th>
                <th>Darbības</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <h2 id="contact-header" style="display: none;">Kontakti</h2>
    <div class="search-container" id="contact-actions" style="display: none;">
        <input type="text" id="contactSearchInput" class="search-input" placeholder="Meklēt pēc Vārda, Uzvārda vai Epasta...">
    </div>
    <table id="contact-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Vārds</th>
                <th>Uzvārds</th>
                <th>Epasts</th>
                <th>Ziņa</th>
                <th>Darbības</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <h2 id="orders-header" style="display: none;">Pasūtījumi</h2>
    <div class="search-container" id="orders-actions" style="display: none;">
        <input type="text" id="ordersSearchInput" class="search-input" placeholder="Meklēt pēc ID, Klienta Vārda vai Statusa...">
    </div>
    <table id="orders-table" style="display: none;">
        <thead>
            <tr>
                <th>Pasūtījuma ID</th>
                <th>Klienta Vārds</th>
                <th>Datums</th>
                <th>Produkti</th>
                <th>Kopējā Cena</th>
                <th>Statuss</th>
                <th>Darbības</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <h2 id="reviews-header" style="display: none;">Atsauksmes</h2>
    <div class="search-container" id="reviews-actions" style="display: none;">
        <input type="text" id="reviewsSearchInput" class="search-input" placeholder="Meklēt pēc Lietotāja Vārda, Epasta vai Teksta...">
    </div>
    <table id="reviews-table" style="display: none; width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Lietotājs</th>
                <th>Epasts</th>
                <th>Izveidots</th>
                <th>Novērtējums</th>
                <th>Atsauksmes teksts</th>
                <th>Attēli</th>
                <th>Darbības</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</section>

<!-- Pasūtijumu Modal -->
<div id="orderModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Pasūtījuma Informācija</h2>
            <span class="modal-close" onclick="closeOrderModal()">&times;</span>
        </div>
        
        <div class="modal-summary">
            <div>
                <strong>Pasūtījuma ID:</strong> <span id="modalOrderId"></span><br>
                <strong>Klienta Vārds:</strong> <span id="modalClientName"></span><br>
                <strong>Datums:</strong> <span id="modalOrderDate"></span><br>
                <strong>Statuss:</strong> 
                <select id="modalOrderStatus" onchange="updateOrderStatus()">
                    <option value="Gaida apstiprinājumu">Gaida apstiprinājumu</option>
                    <option value="Apstiprināts">Apstiprināts</option>
                    <option value="Sagatavo">Sagatavo</option>
                    <option value="Nosūtīts">Nosūtīts</option>
                    <option value="Piegādāts">Piegādāts</option>
                    <option value="Atcelts">Atcelts</option>
                </select>
            </div>
            <div class="modal-total" style="line-height: 1.6;">
            <div>Preču summa: <span id="modalItemsPrice">0.00</span> EUR</div>
            <div>PVN (21%): <span id="modalVatAmount">0.00</span> EUR</div>
            <div>Piegādes cena: <span id="modalShippingPrice">0.00</span></div>
            <div>Kopējā summa: <span id="modalTotalPrice">0.00</span> EUR</div>
        </div>
        </div>

        <div class="modal-address" style="margin-bottom: 20px;">
            <h3>Adrese</h3>
            <div id="modalAddress" style="line-height: 1.5;"></div>
        </div>
        
        <div class="modal-body">
            <table class="order-details-table">
                <thead>
                    <tr>
                        <th>Attēls</th>
                        <th>Nosaukums</th>
                        <th>Daudzums</th>
                        <th>Izmērs</th>
                        <th>Cena</th>
                        <th>Summa</th>
                    </tr>
                </thead>
                <tbody id="orderDetailsTable">
                    <!-- Šeit tiks ievietotas pasūtījuma preces -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Kad lapa ielādējas, parāda pēdējo skatīto tabulu
document.addEventListener('DOMContentLoaded', function() {
    const lastTable = localStorage.getItem('lastTable') || 'admin';
    showTable(lastTable);
});

// Funkcija, kas parāda izvēlēto tabulu un slēpj pārējās
function showTable(table) {
    localStorage.setItem('lastTable', table);
    const adminTable = document.getElementById("admin-table");
    const clientTable = document.getElementById("client-table");
    const productTable = document.getElementById("product-table");
    const subscriberTable = document.getElementById("subscriber-table");
    const contactTable = document.getElementById("contact-table");
    const ordersTable = document.getElementById("orders-table");
    const reviewsTable = document.getElementById("reviews-table");
    const adminHeader = document.getElementById("admin-header");
    const clientHeader = document.getElementById("client-header");
    const productHeader = document.getElementById("product-header");
    const subscriberHeader = document.getElementById("subscriber-header");
    const contactHeader = document.getElementById("contact-header");
    const ordersHeader = document.getElementById("orders-header");
    const reviewsHeader = document.getElementById("reviews-header");
    const productSearch = document.getElementById("product-search");
    const adminActions = document.getElementById("admin-actions");
    const clientActions = document.getElementById("client-actions");
    const subscriberActions = document.getElementById("subscriber-actions");
    const contactActions = document.getElementById("contact-actions");
    const ordersActions = document.getElementById("orders-actions");
    const reviewsActions = document.getElementById("reviews-actions");

    [adminTable, clientTable, productTable, subscriberTable, contactTable, ordersTable, reviewsTable].forEach(t => t.style.display = 'none');
    [adminHeader, clientHeader, productHeader, subscriberHeader, contactHeader, ordersHeader, reviewsHeader].forEach(h => h.style.display = 'none');
    productSearch.style.display = 'none';
    adminActions.style.display = 'none';
    clientActions.style.display = 'none';
    subscriberActions.style.display = 'none';
    contactActions.style.display = 'none';
    ordersActions.style.display = 'none';
    reviewsActions.style.display = 'none';

    if (table === 'admin' && '<?php echo $user_role; ?>' !== 'Mod') {
        adminTable.style.display = 'table';
        adminHeader.style.display = 'block';
        adminActions.style.display = 'flex';
    } else if (table === 'client') {
        clientTable.style.display = 'table';
        clientHeader.style.display = 'block';
        clientActions.style.display = 'flex';
    } else if (table === 'subscriber') {
        subscriberTable.style.display = 'table';
        subscriberHeader.style.display = 'block';
        subscriberActions.style.display = 'flex';
    } else if (table === 'contact') {
        contactTable.style.display = 'table';
        contactHeader.style.display = 'block';
        contactActions.style.display = 'flex';
    } else if (table === 'orders') {
        ordersTable.style.display = 'table';
        ordersHeader.style.display = 'block';
        ordersActions.style.display = 'flex';
        loadOrders();
    } else if (table === 'reviews') {
        reviewsTable.style.display = 'table';
        reviewsHeader.style.display = 'block';
        reviewsActions.style.display = 'flex';
        loadReviews();
    } else {
        productTable.style.display = 'table';
        productHeader.style.display = 'block';
        productSearch.style.display = 'block';
    }
}

// Funkcija, kas pāradresē uz admin pievienošanas lapu
function addNewAdmin() {
    window.location.href = 'add_admin.php';
}

// Funkcija, kas pāradresē uz klienta pievienošanas lapu
function addNewClient() {
    window.location.href = 'add_client.php';
}

// Funkcija, kas pāradresē uz produkta pievienošanas lapu
function addNewProduct() {
    window.location.href = 'add_product.php';
}

// Funkcija, kas dzēš produktu pēc ID
function deleteProduct(id) {
    if (confirm('Vai tiešām vēlaties dzēst šo produktu?')) {
        fetch('delete_product.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                id: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Produkts veiksmīgi dzēsts');
                //Noņem rindu no tabulas bez lapas pārlādēšanas
                const table = document.getElementById('product-table');
                const rows = table.getElementsByTagName('tr');
                for (let i = 1; i < rows.length; i++) {
                    const row = rows[i];

                    if (row.cells[0] && row.cells[0].textContent == id) {
                        row.remove();
                        break;
                    }
                }
                // Nav jāatsvaidzina lapa, jo rinda ir noņemta
            } else {
                alert('Kļūda dzēšot produktu: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kļūda dzēšot produktu');
        });
    }
}

// Funkcija, kas dzēš abonentu pēc ID
function deleteSubscriber(id) {
    if (confirm('Vai tiešām vēlaties dzēst šo abonentu?')) {
        fetch('delete_subscriber.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Abonents veiksmīgi dzēsts');
                location.reload();
            } else {
                alert('Kļūda dzēšot abonentu: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kļūda dzēšot abonentu');
        });
    }
}

// Funkcija, kas pārslēdz admina apstiprinājuma statusu
function toggleApproved(id, currentStatus) {
    const newStatus = currentStatus === 1 ? 0 : 1;
    fetch('update_approved.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            id: id,
            approved: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const statusCell = document.getElementById(`approved-status-${id}`);
                const button = statusCell.parentElement.querySelector("button.approval-button");
                statusCell.textContent = newStatus === 1 ? 'Apstiprināts' : 'Gaidīts';
                button.textContent = newStatus === 1 ? 'Noņemt piekļuvi' : 'Apstiprināt';
                button.setAttribute("onclick", `toggleApproved(${id}, ${newStatus})`);
            } else {
                alert("Failed to update approved status.");
            }
        })
        .catch(error => {
            console.error('Error updating approved status:', error);
        });
}

document.getElementById('productSearchInput').addEventListener('keyup', filterProducts);
document.getElementById('categoryFilter').addEventListener('change', filterProducts);

function filterProducts() {
    const searchValue = document.getElementById('productSearchInput').value.toLowerCase();
    const categoryValue = document.getElementById('categoryFilter').value.toLowerCase();
    const table = document.getElementById('product-table');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;

        // Pārbauda, vai soldOutActive ir ieslēgts un daudzums ir 0
        const quantity = cells[6] ? parseInt(cells[6].textContent) : null;
        if (soldOutActive && quantity !== 0) {
            row.style.display = 'none';
            continue;
        }

        for (let j = 0; j < cells.length - 1; j++) {
            const cellText = cells[j].textContent.toLowerCase();
            if (cellText.includes(searchValue) && (categoryValue === "" || cells[4].textContent.toLowerCase() === categoryValue)) {
                found = true;
                break;
            }
        }
        row.style.display = found ? '' : 'none';
    }
}

document.getElementById('clientSearchInput').addEventListener('keyup', filterClients);

function filterClients() {
    const searchValue = document.getElementById('clientSearchInput').value.toLowerCase();
    const table = document.getElementById('client-table');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;

        for (let j = 1; j < 3; j++) { 
            const cellText = cells[j].textContent.toLowerCase();
            if (cellText.includes(searchValue)) {
                found = true;
                break;
            }
        }
        row.style.display = found ? '' : 'none';
    }
}

document.getElementById('adminSearchInput').addEventListener('keyup', filterAdmins);

function filterAdmins() {
    const searchValue = document.getElementById('adminSearchInput').value.toLowerCase();
    const table = document.getElementById('admin-table');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;

        for (let j = 1; j < 4; j++) { 
            const cellText = cells[j].textContent.toLowerCase();
            if (cellText.includes(searchValue)) {
                found = true;
                break;
            }
        }
        row.style.display = found ? '' : 'none';
    }
}

document.getElementById('subscriberSearchInput').addEventListener('keyup', filterSubscribers);

function filterSubscribers() {
    const searchValue = document.getElementById('subscriberSearchInput').value.toLowerCase();
    const table = document.getElementById('subscriber-table');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;

        for (let j = 1; j < 2; j++) { 
            const cellText = cells[j].textContent.toLowerCase();
            if (cellText.includes(searchValue)) {
                found = true;
                break;
            }
        }
        row.style.display = found ? '' : 'none';
    }
}

document.getElementById('contactSearchInput').addEventListener('keyup', filterContacts);

function filterContacts() {
    const searchValue = document.getElementById('contactSearchInput').value.toLowerCase();
    const table = document.getElementById('contact-table');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;

        for (let j = 1; j < 4; j++) { 
            const cellText = cells[j].textContent.toLowerCase();
            if (cellText.includes(searchValue)) {
                found = true;
                break;
            }
        }
        row.style.display = found ? '' : 'none';
    }
}

document.getElementById('ordersSearchInput').addEventListener('keyup', filterOrders);

function filterOrders() {
    const searchValue = document.getElementById('ordersSearchInput').value.toLowerCase();
    const table = document.getElementById('orders-table');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;

        for (let j = 0; j < cells.length - 1; j++) {
            const cellText = cells[j].textContent.toLowerCase();
            if (cellText.includes(searchValue)) {
                found = true;
                break;
            }
        }
        row.style.display = found ? '' : 'none';
    }
}

// Ielādē pasūtījumus no servera un attēlo tabulā
function loadOrders() {
    fetch('get_orders.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const orders = data.orders;
            const tbody = document.querySelector("#orders-table tbody");
            tbody.innerHTML = "";
            
            orders.forEach(order => {
    let totalPrice = order.total_price || 0;
    if (!totalPrice && order.products) {
        totalPrice = order.products.reduce((sum, product) => {
            return sum + (parseFloat(product.cena || 0) * parseInt(product.quantity || 1));
        }, 0);
    }
                
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${order.id}</td>
                    <td>${order.client_name}</td>
                    <td>${order.date}</td>
                    <td>
                        <button class="edit-button" onclick="showOrderDetails(${JSON.stringify(order).replace(/"/g, '&quot;')})">
                            Apskatīt (${order.products ? order.products.length : 0} produkti)
                        </button>
                    </td>
                    <td>${totalPrice.toFixed(2)} EUR</td>
                    <td>${order.status || 'Gaida apstiprinājumu'}</td>
                    <td>
                        <button class="delete-btn" onclick="deleteOrder('${order.id}', '${order.client_name}')">Dzēst</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            alert('Kļūda ielādējot pasūtījumus: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error fetching orders:', error);
        alert('Kļūda ielādējot pasūtījumus.');
    });
}

let currentOrderId = null;

// Parāda izvēlētā pasūtījuma detaļas modālajā logā
function showOrderDetails(order) {
    currentOrderId = order.id;
    const orderDetailsTable = document.getElementById('orderDetailsTable');
    orderDetailsTable.innerHTML = '';
    let itemsPrice = 0;

    document.getElementById('modalOrderId').textContent = order.id;
    document.getElementById('modalClientName').textContent = order.client_name;
    document.getElementById('modalOrderDate').textContent = order.date;
    document.getElementById('modalOrderStatus').value = order.status || 'Gaida apstiprinājumu';

    // Rāda adreses informāciju
    const addressInfo = order.address || {};
    document.getElementById('modalAddress').innerHTML = `
        <strong>Vārds:</strong> ${addressInfo.name || 'Nav norādīts'}<br>
        <strong>E-pasts:</strong> ${addressInfo.email || 'Nav norādīts'}<br>
        <strong>Telefons:</strong> ${addressInfo.phone || 'Nav norādīts'}<br>
        <strong>Adrese:</strong> ${addressInfo.address || 'Nav norādīts'}<br>
        ${addressInfo.address2 ? `<strong>Dzīvoklis:</strong> ${addressInfo.address2}<br>` : ''}
        <strong>Pilsēta:</strong> ${addressInfo.city || 'Nav norādīts'}<br>
        <strong>Pasta indekss:</strong> ${addressInfo.postal_code || 'Nav norādīts'}<br>
        <strong>Valsts:</strong> ${addressInfo.country || 'Nav norādīts'}<br>
        ${addressInfo.notes ? `<strong>Piezīmes:</strong> ${addressInfo.notes}` : ''}
    `;

    try {
        const products = Array.isArray(order.products) ? order.products : [];
        products.forEach(product => {
            const images = product.bilde ? product.bilde.split(',') : [];
            const firstImage = images.length > 0 ? images[0].trim() : 'placeholder.jpg';

            const itemPrice = parseFloat(product.cena || 0);
            const quantity = parseInt(product.quantity || 0);
            const itemTotal = itemPrice * quantity;
            itemsPrice += itemTotal;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td><img src="../${firstImage}" alt="${product.nosaukums}" style="max-width: 60px; max-height: 60px; object-fit: contain;"></td>
                <td>${product.nosaukums || 'N/A'}</td>
                <td>${quantity}</td>
                <td>${product.size || 'N/A'}</td>
                <td>${itemPrice.toFixed(2)} EUR</td>
                <td>${itemTotal.toFixed(2)} EUR</td>
            `;
            orderDetailsTable.appendChild(row);
        });

        // Aprēķina PVN un piegādes cenu pēc preču summas
        const vatAmount = itemsPrice * 0.21;
        let shippingPrice = 10;
        let shippingDisplay = "10.00 EUR";
        if (itemsPrice >= 100) {
            shippingPrice = 0;
            shippingDisplay = "Bezmaksas";
        }
        // Ja ir saglabāta total_amount, izmanto to kā kopējo summu modālī
        let modalTotalPrice = itemsPrice + vatAmount + shippingPrice;
        if (order.total_amount) {
            modalTotalPrice = parseFloat(order.total_amount);
        }

        // Uzstāda vērtības modāla laukiem
        document.getElementById('modalItemsPrice').textContent = itemsPrice.toFixed(2);
        document.getElementById('modalVatAmount').textContent = vatAmount.toFixed(2);
        document.getElementById('modalShippingPrice').textContent = shippingDisplay;
        document.getElementById('modalTotalPrice').textContent = modalTotalPrice.toFixed(2);

        document.getElementById('orderModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
    } catch (error) {
        // Kļūda ielādējot pasūtījuma preces
        console.error('Kļūda ielādējot pasūtījuma preces:', error);
        alert('Kļūda ielādējot pasūtījuma detaļas.');
    }
}

// Aizver pasūtījuma modālo logu
function closeOrderModal() {
    document.getElementById('orderModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Atjaunina pasūtījuma statusu datubāzē
function updateOrderStatus() {
    const newStatus = document.getElementById('modalOrderStatus').value;
    
    fetch('update_order_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            order_id: currentOrderId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atjaunina statusu tabula
            const rows = document.querySelectorAll('#orders-table tbody tr');
            rows.forEach(row => {
                if (row.cells[0].textContent === currentOrderId) {
                    row.cells[5].textContent = newStatus;
                }
            });
            alert('Pasūtījuma statuss veiksmīgi atjaunināts!');
        } else {
            alert('Kļūda atjauninot statusu: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Kļūda atjauninot statusu');
    });
}

// Dzēš pasūtījumu pēc ID
function deleteOrder(orderId, clientName) {
    if (confirm(`Vai tiešām vēlaties dzēst pasūtījumu #${orderId} no klienta ${clientName}?`)) {
        fetch('delete_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + orderId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                const rows = document.querySelectorAll('#orders-table tbody tr');
                rows.forEach(row => {
                    if (row.cells[0].textContent === orderId) {
                        row.remove();
                    }
                });
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kļūda dzēšot pasūtījumu');
        });
    }
}

// Ielādē adminus no servera un attēlo tabulā
fetch('get_admins.php')
.then(response => response.json())
.then(data => {
    if (data.success) {
        const admins = data.admins;
        const tbody = document.querySelector("#admin-table tbody");
        admins.forEach(admin => {
            const row = document.createElement("tr");
            row.innerHTML = `
<td>${admin.id}</td>
<td>${admin.email}</td>
<td>${admin.name}</td>
<td>
            <select class="role-select" onchange="updateRole(${admin.id}, this.value)">
                <option value="Admin" ${admin.role === 'Admin' ? 'selected' : ''}>Admin</option>
                <option value="Mod" ${admin.role === 'Mod' ? 'selected' : ''}>Mod</option>
            </select>
</td>
<td>${admin.approved ? 'Jā' : 'Nē'}</td>
<td>${admin.created_at}</td>
<td>
    <button 
        onclick="updateApproved(${admin.id}, ${admin.approved ? 0 : 1})"
        class="edit-button"
    >
        ${admin.approved ? 'Atsaukt apstiprinājumu' : 'Apstiprināt'}
    </button>
    <a href="adminedit.php?id=${admin.id}" class="edit-button">Rediģēt</a>
    <button class="delete-btn" onclick="deleteAdmin(${admin.id})">Dzēst</button>
</td>
`;
            tbody.appendChild(row);
        });
    }
});

// Funkcija, kas atjaunina admina lomu
function updateRole(id, newRole) {
fetch('update_role.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        id: id,
        role: newRole
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        alert('Veiksmīgi atjaunināts Role');
    } else {
        alert('Neizdevās atjaunināt');
    }
});
}

// Funkcija, kas atjaunina admina apstiprinājuma statusu
function updateApproved(adminId, approved) {
    const formData = new FormData();
    formData.append('id', adminId);
    formData.append('approved', approved);

    fetch('update_approved.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Refresho admin tabulu
            location.reload();
        } else {
            alert('Kļūda: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Kļūda atjaunojot statusu');
    });
}

// Funkcija, kas dzēš administratoru pēc ID
function deleteAdmin(adminId) {
    if (confirm('Vai tiešām vēlaties dzēst šo administratoru?')) {
        fetch('delete_admin.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + adminId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kļūda dzēšot administratoru');
        });
    }
}

// Ielādē klientus no servera un attēlo tabulā
fetch('get_clients.php')
.then(response => response.json())
.then(data => {
    if (data.success) {
        // kārto client tabulu jaunākie augšā
        const clients = data.clients.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        const tbody = document.querySelector("#client-table tbody");
        clients.forEach(client => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${client.id}</td>
                <td>${client.email}</td>
                <td>${client.name}</td>
                <td>${client.created_at}</td>
                <td>
                    <a href="useredit.php?id=${client.id}" class="edit-button">Rediģēt</a>
                    <form method='POST' action='delete_client.php' style='display:inline;' onsubmit='return confirm("Vai esi drošs, ka vēlies dzēst šo klientu?");'>
                        <input type='hidden' name='client_id' value='${client.id}'>
                        <button type='submit' class='delete-btn'>Dzēst</button>
                    </form>
                </td>
            `;
            tbody.appendChild(row);
        });
    } else {
        alert('Kļūda ielādējot klientus: ' + data.message);
    }
})
.catch(error => {
    console.error('Error:', error);
    alert('Kļūda ielādējot klientus');
});

// Ielādē produktus no servera un attēlo tabulā
fetch('get_products.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const products = data.products.sort((a, b) => new Date(b.created_at) - new Date(a.created_at)); // Sort by newest
            const tbody = document.querySelector("#product-table tbody");
            tbody.innerHTML = ''; 
            products.forEach(product => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.nosaukums}</td>
                    <td>${product.apraksts}</td>
            <td>
                ${(function() {
                    try {
                        const images = product.bilde.split(',');

                        return images.map(image => `<img src="../${image}" style="width: 50px; height: 50px; object-fit: cover; margin-right: 5px; border-radius: 4px;">`).join('');
                    } catch (e) {
                        return `<img src="../${product.bilde}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">`;
                    }
                })()}

            </td>
                    <td>${product.kategorija}</td>
                    <td>${product.cena}€</td>
                    <td>${product.quantity}</td>
                    <td>${product.sizes || 'Nav norādīts'}</td>
                    <td>
<a href="productedit.php?id=${product.id}" class="edit-button">Labot</a>
                        <button class="delete-btn" onclick="deleteProduct(${product.id})">Dzēst</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
    });

// Ielādē abonentus no servera un attēlo tabulā
fetch('get_subscribers.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const subscribers = data.subscribers;
            const tbody = document.querySelector("#subscriber-table tbody");
subscribers.forEach(subscriber => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${subscriber.id}</td>
                    <td>${subscriber.email}</td>
                    <td>${subscriber.created_at}</td>
                    <td>
                        <button class="delete-btn" onclick="deleteSubscriber(${subscriber.id})">Dzēst</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
    });

// Ielādē kontaktus no servera un attēlo tabulā
fetch('get_contacts.php')
.then(response => response.json())
.then(data => {
    if (data.success) {
        const contacts = data.contacts;
        const tbody = document.querySelector("#contact-table tbody");
contacts.forEach(contact => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${contact.id}</td>
                <td>${contact.name}</td>
                <td>${contact.surname}</td>
                <td>${contact.email}</td>
                <td>${contact.message}</td>
                <td>
                    <button class="delete-btn" onclick="deleteContact(${contact.id})">Dzēst</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
});

// Funkcija, kas dzēš kontaktu pēc ID
function deleteContact(contactId) {
    if (confirm('Vai tiešām vēlaties dzēst šo kontaktu?')) {
        fetch('delete_contact.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + contactId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kļūda dzēšot kontaktu');
        });
    }
}

// Aizver pasūtījuma modālo logu, ja tiek uzspiests ārpus tā
window.onclick = function(event) {
    const modal = document.getElementById('orderModal');
    if (event.target === modal) {
        closeOrderModal();
    }
}

let soldOutActive = false;

// Funkcija, kas pārslēdz izpārdoto produktu skatījumu
function toggleSoldOut() {
    soldOutActive = !soldOutActive;
    const btn = document.getElementById('soldOutBtn');
    if (soldOutActive) {
        btn.classList.add('active');
    } else {
        btn.classList.remove('active');
    }
    filterProducts();
}
</script>

<script>
// Pievieno event listeneri atsauksmju meklēšanas laukam
document.getElementById('reviewsSearchInput').addEventListener('keyup', filterReviews);

// Funkcija, kas parāda izvēlēto tabulu un slēpj citas (atsauksmju sadaļai)
function showTable(table) {
    localStorage.setItem('lastTable', table);
    const adminTable = document.getElementById("admin-table");
    const clientTable = document.getElementById("client-table");
    const productTable = document.getElementById("product-table");
    const subscriberTable = document.getElementById("subscriber-table");
    const contactTable = document.getElementById("contact-table");
    const ordersTable = document.getElementById("orders-table");
    const reviewsTable = document.getElementById("reviews-table");
    const adminHeader = document.getElementById("admin-header");
    const clientHeader = document.getElementById("client-header");
    const productHeader = document.getElementById("product-header");
    const subscriberHeader = document.getElementById("subscriber-header");
    const contactHeader = document.getElementById("contact-header");
    const ordersHeader = document.getElementById("orders-header");
    const reviewsHeader = document.getElementById("reviews-header");
    const productSearch = document.getElementById("product-search");
    const adminActions = document.getElementById("admin-actions");
    const clientActions = document.getElementById("client-actions");
    const subscriberActions = document.getElementById("subscriber-actions");
    const contactActions = document.getElementById("contact-actions");
    const ordersActions = document.getElementById("orders-actions");
    const reviewsActions = document.getElementById("reviews-actions");

    [adminTable, clientTable, productTable, subscriberTable, contactTable, ordersTable, reviewsTable].forEach(t => t.style.display = 'none');
    [adminHeader, clientHeader, productHeader, subscriberHeader, contactHeader, ordersHeader, reviewsHeader].forEach(h => h.style.display = 'none');
    productSearch.style.display = 'none';
    adminActions.style.display = 'none';
    clientActions.style.display = 'none';
    subscriberActions.style.display = 'none';
    contactActions.style.display = 'none';
    ordersActions.style.display = 'none';
    reviewsActions.style.display = 'none';

    if (table === 'admin' && '<?php echo $user_role; ?>' !== 'Moderators') {
        adminTable.style.display = 'table';
        adminHeader.style.display = 'block';
        adminActions.style.display = 'flex';
    } else if (table === 'client') {
        clientTable.style.display = 'table';
        clientHeader.style.display = 'block';
        clientActions.style.display = 'flex';
    } else if (table === 'subscriber') {
        subscriberTable.style.display = 'table';
        subscriberHeader.style.display = 'block';
        subscriberActions.style.display = 'flex';
    } else if (table === 'contact') {
        contactTable.style.display = 'table';
        contactHeader.style.display = 'block';
        contactActions.style.display = 'flex';
    } else if (table === 'orders') {
        ordersTable.style.display = 'table';
        ordersHeader.style.display = 'block';
        ordersActions.style.display = 'flex';
        loadOrders();
    } else if (table === 'reviews') {
        reviewsTable.style.display = 'table';
        reviewsHeader.style.display = 'block';
        reviewsActions.style.display = 'flex';
        loadReviews();
    } else {
        productTable.style.display = 'table';
        productHeader.style.display = 'block';
        productSearch.style.display = 'block';
    }
}

// Ielādē atsauksmes no servera un attēlo tabulā
function loadReviews() {
    fetch('get_reviews.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const reviews = data.reviews;
            const tbody = document.querySelector("#reviews-table tbody");
            tbody.innerHTML = "";

            reviews.forEach(review => {
                const row = document.createElement("tr");

                // Sagatavo attēlu HTML
                let imagesHtml = "";
                if (review.images && review.images.length > 0) {
                    review.images.forEach(imgPath => {
                        imagesHtml += `<img src="../${imgPath}" style="max-width: 50px; max-height: 50px; object-fit: cover; margin-right: 2px; border-radius: 4px;">`;
                    });
                }

                row.innerHTML = `
                    <td>${review.id}</td>
                    <td>${review.user_name || 'Nezināms'}</td>
                    <td>${review.user_email || 'Nezināms'}</td>
                    <td>${review.created_at || 'Nezināms'}</td>
                    <td>${review.rating ? review.rating.toFixed(1) + ' / 5' : 'Nav novērtējuma'}</td>
                    <td>${review.review_text || ''}</td>
                    <td>${imagesHtml}</td>
                    <td>
                        <button class="delete-btn" onclick="deleteReview('${review.user_id}', '${review.order_id}')">Dzēst</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            alert('Kļūda ielādējot atsauksmes: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Kļūda ielādējot atsauksmes:', error);
        alert('Kļūda ielādējot atsauksmes.');
    });
}

// Filtrē atsauksmes pēc meklēšanas ievades
function filterReviews() {
    const searchValue = document.getElementById('reviewsSearchInput').value.toLowerCase();
    const table = document.getElementById('reviews-table');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;

        for (let j = 1; j < cells.length - 1; j++) {
            const cellText = cells[j].textContent.toLowerCase();
            if (cellText.includes(searchValue)) {
                found = true;
                break;
            }
        }
        row.style.display = found ? '' : 'none';
    }
}

// Dzēš atsauksmi pēc lietotāja ID un pasūtījuma ID
function deleteReview(userId, orderId) {
    if (confirm('Vai tiešām vēlaties dzēst šo atsauksmi?')) {
        fetch('delete_reviews.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                user_id: userId,
                order_id: orderId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Atsauksme veiksmīgi dzēsta');
                // Atjauno atsauksmes pēc dzēšanas
                loadReviews();
            } else {
                alert('Kļūda dzēšot atsauksmi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Kļūda dzēšot atsauksmi:', error);
            alert('Kļūda dzēšot atsauksmi');
        });
    }
}
</script>
</body>
</html>