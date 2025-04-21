<?php
session_start();
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// iegūst sūtijumus no datubāzes
try {
    $clientDb = new PDO('sqlite:Datubazes/client_signup.db');
    $clientDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $clientDb->prepare('SELECT orders FROM clients WHERE id = :user_id');
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $orders = $stmt->fetchColumn();
    $orders = $orders ? json_decode($orders, true) : [];
    
    foreach ($orders as &$order) {
        $order['total_price'] = 0;
        $order['status'] = $order['status'] ?? 'Gaida apstiprinājumu'; 
        if (isset($order['items'])) {
            $items = json_decode($order['items'], true);
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (isset($item['cena']) && isset($item['quantity'])) {
                        $order['total_price'] += floatval($item['cena']) * intval($item['quantity']);
                    }
                }
            }
        }
    }
    unset($order); 
    
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

        /* Status colors */
        .status-Gaida-apstiprinājumu {
            background-color: #fff3cd;
            color: #856404;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }
        .status-Apstiprināts {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }
        .status-Sagatavo {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }
        .status-Nosūtīts {
            background-color: #fd7e14;
            color: white;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }
        .status-Piegādāts {
            background-color: #155724;
            color: white;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }
        .status-Atcelts {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }

        #orderModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow-y: auto;
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 25px;
            width: 80%;
            max-width: 900px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .modal-close {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
            transition: color 0.3s;
        }

        .modal-close:hover {
            color: #333;
        }

        .modal-body {
            max-height: 60vh;
            overflow-y: auto;
        }

        .modal-summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-total {
            font-size: 18px;
            font-weight: bold;
        }

        .order-details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-details-table th {
            position: sticky;
            top: 0;
            background: #f4f4f4;
            z-index: 10;
        }

        .order-details-table img {
            max-width: 60px;
            max-height: 60px;
            object-fit: contain;
        }

        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 2% auto;
                padding: 15px;
            }
            
            .modal-body {
                max-height: 70vh;
            }
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
                        <td><?= number_format($order['total_price'] ?? 0, 2) ?> EUR</td>
                        <td><span class="status-<?= htmlspecialchars(str_replace(' ', '-', $order['status'])) ?>"><?= htmlspecialchars($order['status']) ?></span></td>
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

    <div id="orderModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Pasūtījuma Informācija</h2>
                <span class="modal-close" onclick="closeOrderModal()">&times;</span>
            </div>
            
            <div class="modal-summary">
                <div>
                    <strong>Pasūtījuma ID:</strong> <span id="modalOrderId"></span><br>
                    <strong>Datums:</strong> <span id="modalOrderDate"></span><br>
                    <strong>Statuss:</strong> <span id="modalOrderStatus"></span>
                </div>
                <div class="modal-total">
                    Kopējā summa: <span id="modalTotalPrice">0.00</span> EUR
                </div>
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
                        <!-- Šeit dinamiski tiks ievietota informācija par produktu -->
                    </tbody>
                </table>
            </div>
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
            orderDetailsTable.innerHTML = '';
            let totalPrice = 0;

            document.getElementById('modalOrderId').textContent = order.order_id;
            document.getElementById('modalOrderDate').textContent = order.created_at;
            document.getElementById('modalOrderStatus').textContent = order.status;

            try {
                const products = JSON.parse(order.items);
                products.forEach(product => {
                    const itemPrice = parseFloat(product.cena) || 0;
                    const quantity = parseInt(product.quantity) || 0;
                    const itemTotal = itemPrice * quantity;
                    totalPrice += itemTotal;
                    
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><img src="${product.bilde || 'placeholder.jpg'}" alt="${product.nosaukums}"></td>
                        <td>${product.nosaukums}</td>
                        <td>${quantity}</td>
                        <td>${product.size || 'N/A'}</td>
                        <td>${itemPrice.toFixed(2)} EUR</td>
                        <td>${itemTotal.toFixed(2)} EUR</td>
                    `;
                    orderDetailsTable.appendChild(row);
                });

                document.getElementById('modalTotalPrice').textContent = totalPrice.toFixed(2);
                document.getElementById('orderModal').style.display = 'block';
                document.body.style.overflow = 'hidden'; 
            } catch (error) {
                console.error('Error parsing order items:', error);
                alert('Kļūda ielādējot pasūtījuma detaļas.');
            }
        }

        function closeOrderModal() {
            document.getElementById('orderModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('orderModal');
            if (event.target === modal) {
                closeOrderModal();
            }
        }
    </script>
</body>
</html>