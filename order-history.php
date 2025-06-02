<?php
session_start();

require_once 'db_connect.php';

if (isset($_GET['fetch_review']) && $_GET['fetch_review'] == '1' && isset($_GET['order_id'])) {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }
    try {
        global $pdo;

        $stmt = $pdo->prepare('SELECT review_text, images, rating FROM reviews WHERE user_id = :user_id AND order_id = :order_id');
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':order_id' => $_GET['order_id']
        ]);
        $review = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($review) {
            $review['images'] = json_decode($review['images'], true);
            header('Content-Type: application/json');
            echo json_encode($review);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Review not found']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['review_text'], $_POST['rating'])) {
    try {
        global $pdo;

        $uploadedImages = [];
        if (!empty($_FILES['review_images']['name'][0])) {
            $uploadDir = 'review_images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            foreach ($_FILES['review_images']['tmp_name'] as $key => $tmpName) {
                $originalName = basename($_FILES['review_images']['name'][$key]);
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $newFileName = uniqid('review_img_') . '.' . $extension;
                $destination = $uploadDir . $newFileName;
                if (move_uploaded_file($tmpName, $destination)) {
                    $uploadedImages[] = $destination;
                }
            }
        }
        $imagesJson = json_encode($uploadedImages);

        $insertStmt = $pdo->prepare('INSERT INTO reviews (user_id, order_id, review_text, images, rating) VALUES (:user_id, :order_id, :review_text, :images, :rating)');
        $insertStmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':order_id' => $_POST['order_id'],
            ':review_text' => $_POST['review_text'],
            ':images' => $imagesJson,
            ':rating' => floatval($_POST['rating'])
        ]);
        $reviewMessage = "Atsauksme veiksmīgi saglabāta.";

        // Pēc veiksmīgas atsauksmes saglabāšanas pāradresē uz to pašu lapu, lai novērstu POST atkārtotu iesniegšanu
        header("Location: " . strtok($_SERVER['REQUEST_URI'], '?') . "?review_saved=1");
        exit();

    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
            $reviewMessage = "Jau esat atstājis atsauksmi par šo pasūtījumu.";
        } else {
            $reviewMessage = "Kļūda saglabājot atsauksmi: " . $e->getMessage();
        }
    }
}

include 'header.php';

require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Iegūst sūtījumus no datubāzes
try {
    global $pdo;

    $stmt = $pdo->prepare('SELECT orders FROM clients WHERE id = :user_id');
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $orders = $stmt->fetchColumn();
    $orders = $orders ? json_decode($orders, true) : [];

    // Sort orders by created_at descending (newest first)
    usort($orders, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });

    // Pieslēdzas atsauksmju datubāzei, lai pārbaudītu esošās atsauksmes
    $reviewCheckStmt = $pdo->prepare('SELECT COUNT(*) FROM reviews WHERE user_id = :user_id AND order_id = :order_id');
    
foreach ($orders as &$order) {
    // Debug: Log the address data to verify
    error_log(print_r($order['address'] ?? null, true));

    // Ensure address data is displayed correctly
    if (!isset($order['address']) || !is_array($order['address'])) {
        $order['address'] = [
            'name' => 'Nav norādīts',
            'email' => 'Nav norādīts',
            'phone' => 'Nav norādīts',
            'address' => 'Nav norādīts',
            'city' => 'Nav norādīts',
            'postal_code' => 'Nav norādīts',
            'country' => 'Nav norādīts',
            'notes' => ''
        ];
    }

    $order['total_price'] = 0;

    // Use saved total_amount if available
    if (isset($order['total_amount'])) {
        $order['total_price'] = floatval($order['total_amount']);
    } else if (isset($order['items'])) {
        if (is_string($order['items'])) {
            $items = json_decode($order['items'], true);
        } else {
            $items = $order['items'];
        }
        if (is_array($items)) {
            foreach ($items as $item) {
                if (isset($item['cena']) && isset($item['quantity'])) {
                    $order['total_price'] += floatval($item['cena']) * intval($item['quantity']);
                }
            }
        }
    }
    // Pārbauda, vai šim pasūtījumam jau ir atsauksme
    $reviewCheckStmt->execute([':user_id' => $_SESSION['user_id'], ':order_id' => $order['order_id']]);
    $order['has_review'] = $reviewCheckStmt->fetchColumn() > 0;
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
            gap: 10px;
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
            max-height: none;
            overflow-y: visible;
        }

        .modal-summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
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

        /* --- RESPONSIVE STYLES --- */
        @media (max-width: 1000px) {
            .container {
                padding: 10px;
            }
            .modal-content {
                width: 98%;
                padding: 10px;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 5px;
            }
            .modal-content {
                width: 99%;
                margin: 2% auto;
                padding: 8px;
            }
            .modal-body {
                max-height: 70vh;
            }
            .modal-summary {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .modal-total {
                font-size: 16px;
            }
        }

        @media (max-width: 600px) {
            .container {
                max-width: 100vw;
                padding: 2vw 2vw 10vw 2vw;
                border-radius: 0;
                box-shadow: none;
            }
            h1 {
                font-size: 1.2em;
                margin-bottom: 12px;
            }
            .search-container {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
                margin-bottom: 10px;
            }
            .search-input {
                width: 100%;
                font-size: 15px;
                padding: 8px;
            }
            table, thead, tbody, th, td, tr {
                display: block;
                width: 100%;
            }
            thead {
                display: none;
            }
            #orderTable tr {
                margin-bottom: 18px;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.07);
                background: #fff;
                border: 1px solid #eee;
                padding: 10px 0;
            }
            #orderTable td {
                border: none;
                padding: 6px 10px;
                font-size: 15px;
                position: relative;
            }
            #orderTable td:before {
                content: attr(data-label);
                font-weight: bold;
                color: #555;
                display: block;
                margin-bottom: 2px;
                font-size: 13px;
            }
            #orderTable td:last-child {
                margin-bottom: 0;
            }
            .no-orders {
                font-size: 15px;
                margin: 10px 0;
            }
            .modal-content {
                width: 99vw;
                max-width: 99vw;
                min-width: 0;
                padding: 4vw 2vw;
            }
            .modal-header h2 {
                font-size: 1.1em;
            }
            .modal-summary {
                font-size: 14px;
                padding: 8px;
            }
            .modal-total {
                font-size: 15px;
            }
            /* Modal order details table as cards for mobile */
            .order-details-table {
                display: block;
                width: 100%;
            }
            .order-details-table thead {
                display: none;
            }
            .order-details-table tbody {
                display: block;
                width: 100%;
            }
            .order-details-table tr {
                display: flex;
                flex-direction: column;
                background: #fafafa;
                border-radius: 8px;
                box-shadow: 0 1px 4px rgba(0,0,0,0.04);
                margin-bottom: 12px;
                border: 1px solid #eee;
                padding: 8px 0;
            }
            .order-details-table td {
                display: flex;
                align-items: center;
                padding: 6px 10px;
                font-size: 14px;
                border: none;
                width: 100%;
                position: relative;
                background: none;
            }
            .order-details-table td[data-label="Attēls"] {
                justify-content: center;
                padding-top: 0;
                padding-bottom: 0;
            }
            .order-details-table td[data-label="Attēls"] img {
                margin: 0 auto;
                display: block;
                max-width: 48px;
                max-height: 48px;
            }
            .order-details-table td:before {
                content: attr(data-label) ": ";
                font-weight: bold;
                color: #555;
                min-width: 90px;
                display: inline-block;
                margin-right: 8px;
                font-size: 13px;
            }
            .order-details-table td[data-label="Attēls"]:before {
                content: "";
                display: none;
            }
        }
    </style>
    <script>
        // Add data-labels for mobile table
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth <= 600) {
                const labels = ["Pasūtījuma ID", "Datums", "Produkti", "Kopējā Cena", "Statuss", "Atsauksme"];
                document.querySelectorAll("#orderTable tbody tr").forEach(function(row) {
                    row.querySelectorAll("td").forEach(function(td, i) {
                        td.setAttribute("data-label", labels[i]);
                    });
                });
            }
        });
        // Add data-labels for modal order-details-table for mobile
        function addModalDataLabels() {
            if (window.innerWidth <= 600) {
                const modalLabels = ["Attēls", "Nosaukums", "Daudzums", "Izmērs", "Cena", "Summa"];
                document.querySelectorAll(".order-details-table tbody tr").forEach(function(row) {
                    row.querySelectorAll("td").forEach(function(td, i) {
                        td.setAttribute("data-label", modalLabels[i]);
                    });
                });
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Pasūtījumu Vēsture</h1>
        <div class="search-container" style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 20px;">
            <input type="text" class="search-input" placeholder="Meklēt pēc ID vai datuma..." onkeyup="filterOrders()" style="padding: 8px; font-size: 16px; border: 1px solid #ddd; border-radius: 4px; width: 300px;">
            <button id="sortPriceBtn" onclick="sortByPrice()" style="padding: 8px 16px; font-size: 16px; cursor: pointer; border: 1px solid #333; background-color: #333; color: white; border-radius: 4px; transition: background-color 0.3s;">Kārtot pēc cenas ↑</button>
            <button id="sortDateBtn" onclick="sortByDate()" style="padding: 8px 16px; font-size: 16px; cursor: pointer; border: 1px solid #333; background-color: #333; color: white; border-radius: 4px; transition: background-color 0.3s;">Kārtot pēc datuma ↑</button>
        </div>
        <table id="orderTable">
            <thead>
                <tr>
                    <th>Pasūtījuma ID</th>
                    <th>Datums</th>
                    <th>Produkti</th>
                    <th>Kopējā Cena</th>
                    <th>Statuss</th>
                    <th>Atsauksme</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $index => $order): ?>
                    <tr id="orderRow-<?= htmlspecialchars($order['order_id']) ?>">
                        <td><?= htmlspecialchars($order['order_id']) ?></td>
                        <td><?= htmlspecialchars($order['created_at']) ?></td>
                        <td>
                            <button onclick="showOrderDetails(<?= htmlspecialchars(json_encode($order)) ?>)" style="padding: 4px 10px; font-size: 14px; cursor: pointer; border: 1px solid #333; background-color: #333; color: white; border-radius: 4px; transition: background-color 0.3s;">Apskatīt</button>
                        </td>
                        <td><?= number_format($order['total_price'] ?? 0, 2) ?> EUR</td>
                        <td><span class="status-<?= htmlspecialchars(str_replace(' ', '-', $order['status'])) ?>"><?= htmlspecialchars($order['status']) ?></span></td>
                        <td>
                        <?php if (!empty($order['has_review'])): ?>
                            <button onclick="openViewReviewModal('<?= htmlspecialchars($order['order_id']) ?>')" style="padding: 4px 10px; font-size: 14px; cursor: pointer; border: 1px solid #333; background-color: #333; color: white; border-radius: 4px; transition: background-color 0.3s;">Skatīt atsauksmi</button>
                        <?php elseif ($order['status'] === 'Piegādāts'): ?>
                            <button onclick="openReviewModal('<?= htmlspecialchars($order['order_id']) ?>')" style="padding: 4px 10px; font-size: 14px; cursor: pointer; border: 1px solid #333; background-color: #333; color: white; border-radius: 4px; transition: background-color 0.3s;">Atstāt atsauksmi</button>
                        <?php else: ?>
                            <span style="color: #777; font-size: 0.8em; white-space: nowrap;">Atsauksmi būs iespējams atstāt, kad sūtījums būs piegādāts</span>
                        <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-orders">Nav atrasti pasūtījumi.</td>
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
                <div class="modal-total" style="line-height: 1.6;">
                    <div><strong>Preču summa:</strong> <span id="modalItemsPrice">0.00</span> EUR</div>
                    <div><strong>PVN (21%):</strong> <span id="modalVatAmount">0.00</span> EUR</div>
                    <div><strong>Piegādes cena:</strong> <span id="modalShippingPrice">0.00</span></div>
                    <div><strong>Kopējā summa:</strong> <span id="modalTotalPrice">0.00</span> EUR</div>
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
                        <!-- Šeit dinamiski tiks ievietota informācija par produktu -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Atsauksmes Modal -->
    <div id="reviewModal" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index: 1001; overflow-y: auto;">
        <div class="modal-content" style="max-width: 600px; margin: 5% auto; padding: 20px; border-radius: 8px; background: white; position: relative;">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2>Atstāt atsauksmi</h2>
                <span class="modal-close" style="font-size: 28px; font-weight: bold; cursor: pointer; color: #aaa;" onclick="closeReviewModal()">&times;</span>
            </div>
            <form id="reviewForm" method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="order_id" id="reviewOrderId" value="">
                <label for="starRating" style="display: block; margin-bottom: 5px;">Novērtējums:</label>
                <div id="starRating" style="font-size: 24px; color: #ccc; margin-bottom: 10px; cursor: pointer;">
                    <i class="fas fa-star" data-value="1"></i>
                    <i class="fas fa-star" data-value="2"></i>
                    <i class="fas fa-star" data-value="3"></i>
                    <i class="fas fa-star" data-value="4"></i>
                    <i class="fas fa-star" data-value="5"></i>
                </div>
                <input type="hidden" name="rating" id="ratingInput" required>
                <textarea name="review_text" id="reviewText" rows="5" style="width: 100%; padding: 10px; font-size: 16px;" placeholder="Rakstiet savu atsauksmi šeit..." required></textarea>
                <label for="reviewImages" style="display: block; margin-top: 10px;">Pievienot attēlus (var būt vairāki):</label>
                <input type="file" name="review_images[]" id="reviewImages" multiple accept="image/*" style="display: none;">
                <div id="imagePreviewContainer" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                    <div id="addImageBox" style="width: 80px; height: 80px; border: 2px dashed #ccc; display: flex; justify-content: center; align-items: center; cursor: pointer; font-size: 36px; color: #ccc;">
                        +
                    </div>
                </div>
                <div style="margin-top: 15px; text-align: right;">
                    <button type="submit" style="padding: 10px 20px; font-size: 16px; cursor: pointer; border: 1px solid #333; background-color: #333; color: white; border-radius: 4px; transition: background-color 0.3s;">Iesniegt</button>
                    <button type="button" style="padding: 10px 20px; font-size: 16px; cursor: pointer; margin-left: 10px; border: 1px solid #333; background-color: #fff; color: #333; border-radius: 4px; transition: background-color 0.3s;">Atcelt</button>
                </div>
            </form>
            <script>
                const stars = document.querySelectorAll('#starRating i');
                const ratingInput = document.getElementById('ratingInput');
                stars.forEach(star => {
                    star.addEventListener('click', () => {
                        const rating = star.getAttribute('data-value');
                        ratingInput.value = rating;
                        stars.forEach(s => {
                            if (s.getAttribute('data-value') <= rating) {
                                s.style.color = '#ffc107';
                            } else {
                                s.style.color = '#ccc';
                            }
                        });
                    });
                });

                const reviewImagesInput = document.getElementById('reviewImages');
                const imagePreviewContainer = document.getElementById('imagePreviewContainer');
                const addImageBox = document.getElementById('addImageBox');

                let selectedFiles = [];

                addImageBox.addEventListener('click', () => {
                    reviewImagesInput.click();
                });

                reviewImagesInput.addEventListener('change', () => {
                    selectedFiles = selectedFiles.concat(Array.from(reviewImagesInput.files));
                    updateImagePreviews();
                    reviewImagesInput.value = '';
                });

                function updateImagePreviews() {
                    const previews = imagePreviewContainer.querySelectorAll('.image-preview');
                    previews.forEach(preview => preview.remove());

                    selectedFiles.forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = e => {
                            const imgDiv = document.createElement('div');
                            imgDiv.classList.add('image-preview');
                            imgDiv.style.position = 'relative';
                            imgDiv.style.width = '80px';
                            imgDiv.style.height = '80px';
                            imgDiv.style.border = '1px solid #ccc';
                            imgDiv.style.borderRadius = '4px';
                            imgDiv.style.overflow = 'hidden';

                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.width = '100%';
                            img.style.height = '100%';
                            img.style.objectFit = 'cover';
                            imgDiv.appendChild(img);

                            const removeBtn = document.createElement('button');
                            removeBtn.textContent = '×';
                            removeBtn.style.position = 'absolute';
                            removeBtn.style.top = '2px';
                            removeBtn.style.right = '2px';
                            removeBtn.style.background = 'rgba(0,0,0,0.5)';
                            removeBtn.style.color = 'white';
                            removeBtn.style.border = 'none';
                            removeBtn.style.borderRadius = '50%';
                            removeBtn.style.width = '20px';
                            removeBtn.style.height = '20px';
                            removeBtn.style.cursor = 'pointer';
                            removeBtn.addEventListener('click', () => {
                                selectedFiles.splice(index, 1);
                                updateImagePreviews();
                            });
                            imgDiv.appendChild(removeBtn);

                            imagePreviewContainer.insertBefore(imgDiv, addImageBox);
                        };
                        reader.readAsDataURL(file);
                    });
                }

                const reviewForm = document.getElementById('reviewForm');
                reviewForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const formData = new FormData(reviewForm);
                    formData.delete('review_images[]');

                    // --- Vienmēr izmantot failus no ievades, ja selectedFiles ir tukšs. ---
                    let filesToUpload = selectedFiles.length > 0 ? selectedFiles : Array.from(reviewImagesInput.files);
                    filesToUpload.forEach(file => {
                        formData.append('review_images[]', file);
                    });

                    fetch(reviewForm.action, {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    }).then(response => {
                        if (response.redirected) {
                            window.location.href = response.url;
                        } else {
                            return response.text().then(text => {
                                alert('Kļūda iesniedzot atsauksmi.');
                            });
                        }
                    }).catch(() => {
                        alert('Kļūda iesniedzot atsauksmi.');
                    });
                });
            </script>
        </div>
    </div>

    <!-- Apskatīt Review Modal -->
    <div id="viewReviewModal" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index: 1002; overflow-y: auto;">
        <div class="modal-content" style="max-width: 600px; margin: 5% auto; padding: 20px; border-radius: 8px; background: white; position: relative;">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2>Jūsu Atsauksme</h2>
                <span class="modal-close" style="font-size: 28px; font-weight: bold; cursor: pointer; color: #aaa;" onclick="closeViewReviewModal()">&times;</span>
            </div>
            <div id="viewReviewContent" style="font-size: 16px; line-height: 1.5;">
                <p><strong>Novērtējums:</strong> <span id="viewReviewRating"></span></p>
                <p><strong>Atsauksme:</strong></p>
                <p id="viewReviewText"></p>
                <div id="viewReviewImages" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;"></div>
            </div>
        </div>
    </div>

    <script>
        function openViewReviewModal(orderId) {
            fetch(`?fetch_review=1&order_id=${orderId}`, { credentials: 'same-origin' })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Atsauksmes ielāde neizdevās.');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('viewReviewRating').textContent = data.rating ? data.rating.toFixed(1) + ' / 5' : 'Nav novērtējuma';
                    document.getElementById('viewReviewText').textContent = data.review_text || 'Nav atsauksmes teksta.';
                    const imagesContainer = document.getElementById('viewReviewImages');
                    imagesContainer.innerHTML = '';
                    if (data.images && data.images.length > 0) {
                        data.images.forEach(imgPath => {
                            const img = document.createElement('img');
                            img.src = imgPath;
                            img.style.maxWidth = '100px';
                            img.style.maxHeight = '100px';
                            img.style.objectFit = 'cover';
                            img.style.borderRadius = '4px';
                            imagesContainer.appendChild(img);
                        });
                    }
                    document.getElementById('viewReviewModal').style.display = 'block';
                    document.body.style.overflow = 'hidden';
                })
                .catch(error => {
                    alert(error.message);
                });
        }

        function closeViewReviewModal() {
            document.getElementById('viewReviewModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        window.onclick = function(event) {
            const viewReviewModal = document.getElementById('viewReviewModal');
            if (event.target === viewReviewModal) {
                closeViewReviewModal();
            }
        }
    </script>

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

        let priceSortAsc = true;
        function sortByPrice() {
            const table = document.getElementById('orderTable');
            const tbody = table.tBodies[0];
            const rows = Array.from(tbody.rows);

            rows.sort((a, b) => {
                const priceA = parseFloat(a.cells[3].textContent.replace(' EUR', '').replace(',', '.')) || 0;
                const priceB = parseFloat(b.cells[3].textContent.replace(' EUR', '').replace(',', '.')) || 0;
                return priceSortAsc ? priceA - priceB : priceB - priceA;
            });

            rows.forEach(row => tbody.appendChild(row));
            priceSortAsc = !priceSortAsc;

            const btn = document.getElementById('sortPriceBtn');
            btn.textContent = `Kārtot pēc cenas ${priceSortAsc ? '↑' : '↓'}`;
            btn.style.backgroundColor = priceSortAsc ? '#333' : '#000';
        }

        let dateSortAsc = false;
        function sortByDate() {
            const table = document.getElementById('orderTable');
            const tbody = table.tBodies[0];
            const rows = Array.from(tbody.rows);

            rows.sort((a, b) => {
                const dateA = new Date(a.cells[1].textContent);
                const dateB = new Date(b.cells[1].textContent);
                return dateSortAsc ? dateA - dateB : dateB - dateA;
            });

            rows.forEach(row => tbody.appendChild(row));
            dateSortAsc = !dateSortAsc;

            const btn = document.getElementById('sortDateBtn');
            btn.textContent = `Kārtot pēc datuma ${dateSortAsc ? '↑' : '↓'}`;
            btn.style.backgroundColor = dateSortAsc ? '#333' : '#000';
        }

        function showOrderDetails(order) {
            const orderDetailsTable = document.getElementById('orderDetailsTable');
            orderDetailsTable.innerHTML = '';
            let itemsPrice = 0;

            document.getElementById('modalOrderId').textContent = order.order_id;
            document.getElementById('modalOrderDate').textContent = order.created_at;
            document.getElementById('modalOrderStatus').textContent = order.status;

            // Parādīt pasūtījuma adresi
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
                let products = order.items;
                if (typeof products === 'string') {
                    products = JSON.parse(products);
                }
                products.forEach(product => {
                    const images = product.bilde ? product.bilde.split(',') : [];
                    const firstImage = images.length > 0 ? images[0].trim() : 'placeholder.jpg';

                    const itemPrice = parseFloat(product.cena) || 0;
                    const quantity = parseInt(product.quantity) || 0;
                    const itemTotal = itemPrice * quantity;
                    itemsPrice += itemTotal;

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><img src="${firstImage}" alt="${product.nosaukums}" style="max-width: 60px; max-height: 60px; object-fit: contain;"></td>
                        <td>${product.nosaukums}</td>
                        <td>${quantity}</td>
                        <td>${product.size || 'N/A'}</td>
                        <td>${itemPrice.toFixed(2)} EUR</td>
                        <td>${itemTotal.toFixed(2)} EUR</td>
                    `;
                    orderDetailsTable.appendChild(row);
                });

                // Aprēķināt PVN un piegādes maksu
                const vatAmount = itemsPrice * 0.21;
                let shippingPrice = 10;
                let shippingDisplay = "10.00 EUR";
                if (itemsPrice >= 100) {
                    shippingPrice = 0;
                    shippingDisplay = "Bezmaksas";
                }
                // Izmanto saglabāto kopējo summu, ja tā ir pieejama
                let modalTotalPrice = itemsPrice + vatAmount + shippingPrice;
                if (order.total_amount) {
                    modalTotalPrice = parseFloat(order.total_amount);
                }

                document.getElementById('modalItemsPrice').textContent = itemsPrice.toFixed(2);
                document.getElementById('modalVatAmount').textContent = vatAmount.toFixed(2);
                document.getElementById('modalShippingPrice').textContent = shippingDisplay;
                document.getElementById('modalTotalPrice').textContent = modalTotalPrice.toFixed(2);

                document.getElementById('orderModal').style.display = 'block';
                document.body.style.overflow = 'hidden'; 
                addModalDataLabels();
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

        function openReviewModal(orderId) {
            document.getElementById('reviewOrderId').value = orderId;
            document.getElementById('reviewModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        window.onclick = function(event) {
            const reviewModal = document.getElementById('reviewModal');
            if (event.target === reviewModal) {
                closeReviewModal();
            }
        };

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