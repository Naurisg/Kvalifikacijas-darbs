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

try {
    global $pdo;

    $stmt = $pdo->prepare('SELECT orders FROM clients WHERE id = :user_id');
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $orders = $stmt->fetchColumn();
    $orders = $orders ? json_decode($orders, true) : [];

    usort($orders, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });

    $reviewCheckStmt = $pdo->prepare('SELECT COUNT(*) FROM reviews WHERE user_id = :user_id AND order_id = :order_id');
    
    foreach ($orders as &$order) {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --black: #000000;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius-sm: 0.125rem;
            --radius: 0.25rem;
            --radius-md: 0.375rem;
            --radius-lg: 0.5rem;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.5;
        }

        .container {
            max-width: 1200px;
            margin: 1rem auto;
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--black);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .search-container {
            position: relative;
            flex-grow: 1;
            max-width: 400px;
        }

        .search-container i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
        }

        .search-input {
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            width: 100%;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            transition: all 0.2s;
            background-color: var(--white);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--gray-400);
            box-shadow: 0 0 0 3px rgba(156, 163, 175, 0.1);
        }

        .sort-buttons {
            display: flex;
            gap: 0.75rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--black);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--gray-800);
        }

        .btn-outline {
            background-color: transparent;
            border-color: var(--gray-300);
            color: var(--gray-800);
        }

        .btn-outline:hover {
            background-color: var(--gray-100);
        }

        .btn-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.8125rem;
        }

        .table-container {
            overflow-x: auto;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            background: var(--white);
            border: 1px solid var(--gray-200);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th {
            background-color: var(--gray-50);
            color: var(--gray-600);
            font-weight: 600;
            text-align: left;
            padding: 0.75rem 1rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--gray-200);
        }

        td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--gray-200);
            font-size: 0.875rem;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: var(--gray-50);
        }

        .status {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-Gaida-apstiprinājumu {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-Apstiprināts {
            background-color: #d4edda;
            color: #155724;
        }

        .status-Sagatavo {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-Nosūtīts {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-Piegādāts {
            background-color: #d4edda;
            color: #155724;
        }

        .status-Atcelts {
            background-color: #f8d7da;
            color: #721c24;
        }

        .no-orders {
            text-align: center;
            padding: 2rem;
            color: var(--gray-400);
        }

        /* Modal Stils */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s;
            padding: 1rem;
        }

        .modal.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background-color: var(--white);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 800px;
            max-height: calc(100vh - 2rem);
            display: flex;
            flex-direction: column;
            transform: translateY(10px);
            transition: transform 0.2s;
        }

        .modal.active .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .modal-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--black);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--gray-500);
            cursor: pointer;
            transition: color 0.2s;
            line-height: 1;
        }

        .modal-close:hover {
            color: var(--black);
        }

        .modal-body {
            padding: 1.5rem;
            overflow-y: auto;
            flex-grow: 1;
        }

        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .info-card {
            background-color: var(--gray-50);
            border-radius: var(--radius-sm);
            padding: 1rem;
            border: 1px solid var(--gray-200);
        }

        .info-label {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .info-value {
            font-size: 0.9375rem;
            font-weight: 500;
            color: var(--black);
        }

        .section-title {
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--black);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: var(--gray-400);
        }

        .order-items {
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-sm);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .order-item {
            display: grid;
            grid-template-columns: 60px 1fr 80px 80px 80px;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--gray-200);
            gap: 1rem;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item-image {
            width: 50px;
            height: 50px;
            object-fit: contain;
            border-radius: var(--radius-sm);
            border: 1px solid var(--gray-200);
        }

        .order-item-name {
            font-weight: 500;
            color: var(--black);
            font-size: 0.875rem;
        }

        .order-item-price, 
        .order-item-total {
            font-weight: 500;
            font-size: 0.875rem;
        }

        .order-item-quantity {
            font-size: 0.875rem;
            color: var(--gray-600);
        }

        .order-totals {
            background-color: var(--gray-50);
            border-radius: var(--radius-sm);
            padding: 1rem;
            border: 1px solid var(--gray-200);
        }

        .totals-grid {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 0.75rem;
        }

        .total-row {
            display: contents;
        }

        .total-label {
            font-size: 0.875rem;
            color: var(--gray-600);
            text-align: right;
        }

        .total-value {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--black);
            text-align: right;
        }

        .total-row:last-child .total-label,
        .total-row:last-child .total-value {
            font-weight: 600;
            font-size: 0.9375rem;
            padding-top: 0.25rem;
            border-top: 1px solid var(--gray-200);
        }

        .review-modal {
            max-width: 500px;
        }

        .rating-container {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            justify-content: center;
        }

        .rating-star {
            font-size: 1.5rem;
            color: var(--gray-300);
            cursor: pointer;
            transition: color 0.2s;
        }

        .rating-star.active {
            color: var(--gray-700);
        }

        .review-textarea {
            width: 100%;
            padding: 0.875rem;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-sm);
            min-height: 120px;
            margin-bottom: 1.5rem;
            resize: vertical;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .review-textarea:focus {
            outline: none;
            border-color: var(--gray-400);
            box-shadow: 0 0 0 3px rgba(156, 163, 175, 0.1);
        }

        .image-upload {
            margin-bottom: 1.5rem;
        }

        .image-upload-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            color: var(--gray-700);
        }

        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 0.75rem;
        }

        .image-preview {
            position: relative;
            width: 80px;
            height: 80px;
            border-radius: var(--radius-sm);
            overflow: hidden;
            border: 1px solid var(--gray-200);
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-preview-remove {
            position: absolute;
            top: 0.25rem;
            right: 0.25rem;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            font-size: 0.75rem;
        }

        .upload-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1rem;
            background-color: var(--black);
            color: var(--white);
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-size: 0.875rem;
            transition: background-color 0.2s;
        }

        .upload-btn:hover {
            background-color: var(--gray-800);
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--gray-200);
        }

        .review-content {
            margin-bottom: 1.5rem;
        }

        .review-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            justify-content: center;
        }

        .review-rating-value {
            font-weight: 600;
            color: var(--black);
            font-size: 0.875rem;
        }

        .review-text {
            background-color: var(--gray-50);
            padding: 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            line-height: 1.6;
            border: 1px solid var(--gray-200);
        }

        /* Responsivitāte */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-container {
                max-width: 100%;
            }

            .sort-buttons {
                justify-content: flex-start;
            }

            .order-item {
                grid-template-columns: 50px 1fr;
                grid-template-rows: auto auto auto;
                gap: 0.5rem;
                padding: 0.75rem;
            }

            .order-item-image {
                grid-row: span 3;
                width: 40px;
                height: 40px;
            }

            .order-item-name {
                grid-column: 2;
            }

            .order-item-quantity, 
            .order-item-price, 
            .order-item-total {
                grid-column: 2;
                font-size: 0.8125rem;
            }

            .modal-body {
                padding: 1rem;
            }

            .order-info-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.8125rem;
            }

            .modal-header {
                padding: 0.75rem 1rem;
            }

            .order-item {
                grid-template-columns: 40px 1fr;
                padding: 0.5rem;
            }

            .order-item-name,
            .order-item-quantity,
            .order-item-price,
            .order-item-total {
                font-size: 0.8125rem;
            }

            .totals-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .total-label {
                text-align: left;
            }

            .total-value {
                text-align: left;
            }

            .modal-footer {
                padding: 0.75rem 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Pasūtījumu Vēsture</h1>
        
        <div class="toolbar">
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" class="search-input" placeholder="Meklēt pēc ID vai datuma..." onkeyup="filterOrders()">
            </div>
            <div class="sort-buttons">
                <button id="sortDateBtn" onclick="sortByDate()" class="btn btn-outline">
                    <i class="fas fa-calendar-alt"></i>
                    Datums
                </button>
                <button id="sortPriceBtn" onclick="sortByPrice()" class="btn btn-outline">
                    <i class="fas fa-euro-sign"></i>
                    Cena
                </button>
            </div>
        </div>

        <div class="table-container">
            <table id="orderTable">
                <thead>
                    <tr>
                        <th>Pasūtījuma ID</th>
                        <th>Datums</th>
                        <th>Produkti</th>
                        <th>Summa</th>
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
                                <button onclick="showOrderDetails(<?= htmlspecialchars(json_encode($order)) ?>)" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i>
                                    Skatīt
                                </button>
                            </td>
                            <td><?= number_format($order['total_price'] ?? 0, 2) ?> EUR</td>
                            <td>
                                <span class="status status-<?= htmlspecialchars(str_replace(' ', '-', $order['status'])) ?>">
                                    <?= htmlspecialchars($order['status']) ?>
                                </span>
                            </td>
                            <td>
                            <?php if (!empty($order['has_review'])): ?>
                                <button onclick="openViewReviewModal('<?= htmlspecialchars($order['order_id']) ?>')" class="btn btn-outline btn-sm">
                                    <i class="fas fa-star"></i>
                                    Atsauksme
                                </button>
                            <?php elseif ($order['status'] === 'Piegādāts'): ?>
                                <button onclick="openReviewModal('<?= htmlspecialchars($order['order_id']) ?>')" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                    Atstāt
                                </button>
                            <?php else: ?>
                                <span style="color: var(--gray-400); font-size: 0.75rem;">Pēc piegādes</span>
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
        </div>
    </div>

    <!-- Pasūtijus Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Pasūtījuma detaļas</h2>
                <button class="modal-close" onclick="closeOrderModal()">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="order-info-grid">
                    <div class="info-card">
                        <div class="info-label">Pasūtījuma ID</div>
                        <div class="info-value" id="modalOrderId"></div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Datums</div>
                        <div class="info-value" id="modalOrderDate"></div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Statuss</div>
                        <div class="info-value" id="modalOrderStatus"></div>
                    </div>
                </div>

                <div class="section-title">
                    <i class="fas fa-truck"></i>
                    <span>Piegādes informācija</span>
                </div>
                <div class="info-card" id="modalAddress" style="margin-bottom: 1.5rem;">
                    <!-- Adrese -->
                </div>
                
                <div class="section-title">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Pasūtījuma saturs</span>
                </div>
                <div class="order-items">
                    <div id="orderDetailsTable">
                        <!-- Pasūtījuma detaļas -->
                    </div>
                </div>
                
                <div class="order-totals">
                    <div class="totals-grid">
                        <div class="total-row">
                            <div class="total-label">Preču summa:</div>
                            <div class="total-value" id="modalItemsPrice">0.00 EUR</div>
                        </div>
                        <div class="total-row">
                            <div class="total-label">PVN (21%):</div>
                            <div class="total-value" id="modalVatAmount">0.00 EUR</div>
                        </div>
                        <div class="total-row">
                            <div class="total-label">Piegādes cena:</div>
                            <div class="total-value" id="modalShippingPrice">0.00 EUR</div>
                        </div>
                        <div class="total-row">
                            <div class="total-label">Kopējā summa:</div>
                            <div class="total-value" id="modalTotalPrice">0.00 EUR</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Atsauksmes modal -->
    <div id="reviewModal" class="modal">
        <div class="modal-content review-modal">
            <div class="modal-header">
                <h2 class="modal-title">Atstāt atsauksmi</h2>
                <button class="modal-close" onclick="closeReviewModal()">&times;</button>
            </div>
            
            <div class="modal-body">
                <form id="reviewForm" method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="order_id" id="reviewOrderId" value="">
                    
                    <div class="rating-container">
                        <div class="rating-star" data-value="1"><i class="fas fa-star"></i></div>
                        <div class="rating-star" data-value="2"><i class="fas fa-star"></i></div>
                        <div class="rating-star" data-value="3"><i class="fas fa-star"></i></div>
                        <div class="rating-star" data-value="4"><i class="fas fa-star"></i></div>
                        <div class="rating-star" data-value="5"><i class="fas fa-star"></i></div>
                    </div>
                    <input type="hidden" name="rating" id="ratingInput" required>
                    
                    <textarea class="review-textarea" name="review_text" id="reviewText" placeholder="Rakstiet savu atsauksmi šeit..." required></textarea>
                    
                    <div class="image-upload">
                        <label class="image-upload-label">Pievienot attēlu:</label>
                        <input type="file" name="review_images[]" id="reviewImages" multiple accept="image/*" style="display: none;">
                        <label for="reviewImages" class="upload-btn">
                            <i class="fas fa-upload"></i>
                            Augšupielādēt attēlus
                        </label>
                        
                        <div class="image-preview-container" id="imagePreviewContainer">
                            <!-- Bildes tiks pievienotas seit -->
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" onclick="closeReviewModal()">Atcelt</button>
                        <button type="submit" class="btn btn-primary">Saglabāt</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Apskatīt atsauksmes modal -->
    <div id="viewReviewModal" class="modal">
        <div class="modal-content review-modal">
            <div class="modal-header">
                <h2 class="modal-title">Jūsu atsauksme</h2>
                <button class="modal-close" onclick="closeViewReviewModal()">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="review-content">
                    <div class="review-rating">
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <span class="review-rating-value" id="viewReviewRating"></span>
                    </div>
                    
                    <div class="review-text" id="viewReviewText"></div>
                    
                    <div class="image-preview-container" id="viewReviewImages">
                        <!-- Atsauksme attēli -->
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="closeViewReviewModal()">Aizvērt</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const stars = document.querySelectorAll('.rating-star');
            const ratingInput = document.getElementById('ratingInput');
            
            stars.forEach(star => {
                star.addEventListener('click', () => {
                    const rating = star.getAttribute('data-value');
                    ratingInput.value = rating;
                    
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                });
            });
            
            // Inicialize pasūtijuma modal
            const reviewImagesInput = document.getElementById('reviewImages');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            
            reviewImagesInput.addEventListener('change', function() {
                imagePreviewContainer.innerHTML = '';
                
                if (this.files.length > 5) {
                    alert('Var augšupielādēt maksimāli 5 attēlus!');
                    this.value = '';
                    return;
                }
                
                Array.from(this.files).forEach(file => {
                    if (!file.type.match('image.*')) {
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imagePreview = document.createElement('div');
                        imagePreview.className = 'image-preview';
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        
                        const removeBtn = document.createElement('button');
                        removeBtn.className = 'image-preview-remove';
                        removeBtn.innerHTML = '×';
                        removeBtn.addEventListener('click', function() {
                            imagePreview.remove();

                            const dataTransfer = new DataTransfer();
                            Array.from(reviewImagesInput.files)
                                .filter(f => f !== file)
                                .forEach(f => dataTransfer.items.add(f));
                            reviewImagesInput.files = dataTransfer.files;
                        });
                        
                        imagePreview.appendChild(img);
                        imagePreview.appendChild(removeBtn);
                        imagePreviewContainer.appendChild(imagePreview);
                    };
                    reader.readAsDataURL(file);
                });
            });
        });
        
        // Pasūtījumu tabulas funkcijas
        function showOrderDetails(order) {
            const orderDetailsTable = document.getElementById('orderDetailsTable');
            orderDetailsTable.innerHTML = '';
            let itemsPrice = 0;

            document.getElementById('modalOrderId').textContent = order.order_id;
            document.getElementById('modalOrderDate').textContent = order.created_at;
            document.getElementById('modalOrderStatus').textContent = order.status;

            // Parāda adreses informāciju
            const addressInfo = order.address || {};
            const addressHTML = `
                <div><strong>Vārds:</strong> ${addressInfo.name || 'Nav norādīts'}</div>
                <div><strong>E-pasts:</strong> ${addressInfo.email || 'Nav norādīts'}</div>
                <div><strong>Telefons:</strong> ${addressInfo.phone || 'Nav norādīts'}</div>
                <div><strong>Adrese:</strong> ${addressInfo.address || 'Nav norādīts'}</div>
                ${addressInfo.address2 ? `<div><strong>Dzīvoklis:</strong> ${addressInfo.address2}</div>` : ''}
                <div><strong>Pilsēta:</strong> ${addressInfo.city || 'Nav norādīts'}</div>
                <div><strong>Pasta indekss:</strong> ${addressInfo.postal_code || 'Nav norādīts'}</div>
                <div><strong>Valsts:</strong> ${addressInfo.country || 'Nav norādīts'}</div>
                ${addressInfo.notes ? `<div><strong>Piezīmes:</strong> ${addressInfo.notes}</div>` : ''}
            `;
            document.getElementById('modalAddress').innerHTML = addressHTML;

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

                    const itemElement = document.createElement('div');
                    itemElement.className = 'order-item';
                    itemElement.innerHTML = `
                        <img src="${firstImage}" alt="${product.nosaukums}" class="order-item-image">
                        <div class="order-item-name">${product.nosaukums}</div>
                        <div class="order-item-quantity">${quantity} gab.</div>
                        <div class="order-item-price">${itemPrice.toFixed(2)} EUR</div>
                        <div class="order-item-total">${itemTotal.toFixed(2)} EUR</div>
                    `;
                    orderDetailsTable.appendChild(itemElement);
                });

                // APrēķina PVN un piegādes maksu
                const vatAmount = itemsPrice * 0.21;
                let shippingPrice = 10;
                let shippingDisplay = "10.00 EUR";
                if (itemsPrice >= 100) {
                    shippingPrice = 0;
                    shippingDisplay = "Bezmaksas";
                }
                
                // Izmanto order.total_amount, ja tas ir pieejams
                let modalTotalPrice = itemsPrice + vatAmount + shippingPrice;
                if (order.total_amount) {
                    modalTotalPrice = parseFloat(order.total_amount);
                }

                document.getElementById('modalItemsPrice').textContent = itemsPrice.toFixed(2) + ' EUR';
                document.getElementById('modalVatAmount').textContent = vatAmount.toFixed(2) + ' EUR';
                document.getElementById('modalShippingPrice').textContent = shippingDisplay;
                document.getElementById('modalTotalPrice').textContent = modalTotalPrice.toFixed(2) + ' EUR';

                // Parāda modālu
                document.getElementById('orderModal').classList.add('active');
                document.body.style.overflow = 'hidden';
            } catch (error) {
                console.error('Error parsing order items:', error);
                alert('Kļūda ielādējot pasūtījuma detaļas.');
            }
        }

        function closeOrderModal() {
            document.getElementById('orderModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Atsauksmes modāļa funkcijas
        function openReviewModal(orderId) {
            document.getElementById('reviewOrderId').value = orderId;
            document.getElementById('reviewModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            // Atiestata formu
            document.getElementById('reviewForm').reset();
            document.getElementById('imagePreviewContainer').innerHTML = '';
            // Atiestata zvaigznes
            document.querySelectorAll('.rating-star').forEach(star => {
                star.classList.remove('active');
            });
        }

        // Atsauksmes skatīšanas modāļa funkcijas
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
                    
                    // Uzstāda zvaigžņu vērtējuma attēlojumu
                    const stars = document.querySelectorAll('#viewReviewModal .rating-stars i');
                    const rating = data.rating ? Math.round(parseFloat(data.rating)) : 0;
                    stars.forEach((star, index) => {
                        if (index < rating) {
                            star.style.color = '#111827';
                        } else {
                            star.style.color = '#e5e7eb';
                        }
                    });
                    
                    // parāda atsauksmes attēlus
                    const imagesContainer = document.getElementById('viewReviewImages');
                    imagesContainer.innerHTML = '';
                    if (data.images && data.images.length > 0) {
                        data.images.forEach(imgPath => {
                            const imagePreview = document.createElement('div');
                            imagePreview.className = 'image-preview';
                            
                            const img = document.createElement('img');
                            img.src = imgPath;
                            
                            imagePreview.appendChild(img);
                            imagesContainer.appendChild(imagePreview);
                        });
                    }
                    
                    // parāda modāl
                    document.getElementById('viewReviewModal').classList.add('active');
                    document.body.style.overflow = 'hidden';
                })
                .catch(error => {
                    alert(error.message);
                });
        }

        function closeViewReviewModal() {
            document.getElementById('viewReviewModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // tabula filtrēšanas un kārtošanas funkcijas
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

            const noOrdersMsg = document.querySelector('.no-orders');
            if (noOrdersMsg) {
                noOrdersMsg.style.display = hasVisibleRows ? 'none' : 'table-cell';
            }
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
            btn.innerHTML = priceSortAsc ? 
                '<i class="fas fa-euro-sign"></i> Cena ↑' : 
                '<i class="fas fa-euro-sign"></i> Cena ↓';
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
            btn.innerHTML = dateSortAsc ? 
                '<i class="fas fa-calendar-alt"></i> Datums ↑' : 
                '<i class="fas fa-calendar-alt"></i> Datums ↓';
        }

        // Aizvērt modālus, ja noklikšķina ārpus tiem
        window.onclick = function(event) {
            const orderModal = document.getElementById('orderModal');
            if (event.target === orderModal) {
                closeOrderModal();
            }
            
            const reviewModal = document.getElementById('reviewModal');
            if (event.target === reviewModal) {
                closeReviewModal();
            }
            
            const viewReviewModal = document.getElementById('viewReviewModal');
            if (event.target === viewReviewModal) {
                closeViewReviewModal();
            }
        };

        // formas iesniegšanas apstrāde
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!document.getElementById('ratingInput').value) {
                alert('Lūdzu, novērtējiet produktu!');
                return;
            }
            
            // Iesniegšanas datu sagatavošana
            const formData = new FormData(this);
            
            // Noņem atsauksmes attēlus no formas datiem
            formData.delete('review_images[]');
            
            const fileInput = document.getElementById('reviewImages');
            const files = fileInput.files;
            
            for (let i = 0; i < files.length; i++) {
                formData.append('review_images[]', files[i]);
            }
            
            // Nosūta atsauksmes formu ar AJAX uz serveri
            fetch(this.action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            }).then(response => {
                // Ja serveris pāradresē (veiksmīgi saglabāta atsauksme), pāriet uz jauno URL
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    // Ja nav pāradresācijas, parāda kļūdas paziņojumu
                    return response.text().then(text => {
                        alert('Kļūda iesniedzot atsauksmi.');
                    });
                }
            }).catch(() => {
                // Apstrādā tīkla vai servera kļūdu
                alert('Kļūda iesniedzot atsauksmi.');
            });
        });
    </script>
</body>
</html>