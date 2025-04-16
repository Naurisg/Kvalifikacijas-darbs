<?php
session_start();
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch orders from the database
try {
    $clientDb = new PDO('sqlite:Datubazes/client_signup.db');
    $clientDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $clientDb->prepare('SELECT orders FROM clients WHERE id = :user_id');
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $orders = $stmt->fetchColumn();
    $orders = $orders ? json_decode($orders, true) : [];
} catch (Exception $e) {
    $orders = [];
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasūtījumu Vēsture</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .search-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .search-input {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
            color: #333;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .no-orders {
            text-align: center;
            font-size: 18px;
            color: #777;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Pasūtījumu Vēsture</h1>
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Meklēt pēc ID vai datuma..." onkeyup="filterOrders()">
        </div>
        <table id="orderTable">
            <thead>
                <tr>
                    <th>Pasūtījuma ID</th>
                    <th>Datums</th>
                    <th>Produkti</th>
                    <th>Kopējā Cena</th>
                    <th>Statuss</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $index => $order): ?>
                        <tr id="orderRow-<?= htmlspecialchars($order['order_id']) ?>">
                            <td><?= htmlspecialchars($order['order_id']) ?></td>
                            <td><?= htmlspecialchars($order['created_at']) ?></td>
                            <td>
                                <button onclick="showOrderDetails(<?= htmlspecialchars(json_encode($order)) ?>)" style="cursor: pointer;">Apskatīt</button>
                            </td>
                            <td><?= htmlspecialchars(number_format($order['total_amount'], 2)) ?> EUR</td>
                            <td><?= 'Completed' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-orders">Nav atrasti pasūtījumi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <p class="no-orders" style="display: none;">Nav atrasti pasūtījumi.</p>
    </div>

    <!-- Modal for order details -->
    <div id="orderModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000;">
        <div style="background: white; margin: 10% auto; padding: 20px; width: 70%; border-radius: 8px; position: relative;">
            <span onclick="closeOrderModal()" style="position: absolute; top: 10px; right: 20px; cursor: pointer; font-size: 20px;">&times;</span>
            <h2>Pasūtījuma Detalizācija</h2>
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr>
                        <th>Attēls</th>
                        <th>Nosaukums</th>
                        <th>Daudzums</th>
                        <th>Izmērs</th>
                        <th>Cena</th>
                    </tr>
                </thead>
                <tbody id="orderDetailsTable">
                    <!-- Product details will be dynamically inserted here -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function filterOrders() {
            const searchValue = document.querySelector('.search-input').value.toLowerCase();
            const rows = document.querySelectorAll('#orderTable tbody tr');
            let hasVisibleRows = false;

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const matches = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(searchValue));
                row.style.display = matches ? '' : 'none';
                if (matches) hasVisibleRows = true;
            });

            document.querySelector('.no-orders').style.display = hasVisibleRows ? 'none' : 'block';
        }

        function showOrderDetails(order) {
            const orderDetailsTable = document.getElementById('orderDetailsTable');
            orderDetailsTable.innerHTML = ''; // Clear previous content

            try {
                const products = JSON.parse(order.items);
                products.forEach(product => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><img src="${product.bilde || 'placeholder.jpg'}" alt="Product Image" style="width: 50px; height: 50px;"></td>
                        <td>${product.nosaukums}</td>
                        <td>${product.quantity}</td>
                        <td>${product.size || 'N/A'}</td>
                        <td>${product.cena} EUR</td>
                    `;
                    orderDetailsTable.appendChild(row);
                });

                document.getElementById('orderModal').style.display = 'block';
            } catch (error) {
                console.error('Error parsing order items:', error);
                alert('Kļūda ielādējot pasūtījuma detaļas.');
            }
        }

        function closeOrderModal() {
            document.getElementById('orderModal').style.display = 'none';
        }
    </script>
</body>
</html>
