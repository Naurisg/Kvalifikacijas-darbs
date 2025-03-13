<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: adminlogin.html");
    exit();
}

$user_role = $_SESSION['user_role'] ?? '';
$admin_id = $_SESSION['user_id'] ?? '';

try {
    $pdo = new PDO('sqlite:../Datubazes/admin_signup.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #2c3e50, #bdc3c7);
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
            color: #4A4A4A; 
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15); 
            border-radius: 8px; 
            overflow: hidden; 
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff; 
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tr:hover {
            background-color: rgba(0, 123, 255, 0.1); 
        }

        .logout-button, .toggle-button {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px;
            background-color: #dc3545; 
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
            font-weight: bold;
        }

        .logout-button:hover, .toggle-button:hover {
            background-color: #c82333; 
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); 
        }

        .toggle-button {
            background-color: #007bff; 
        }

        .toggle-button:hover {
            background-color: #0056b3; 
        }

        .edit-button, .approval-button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11px;
            font-weight: bold;
            background-color: #17a2b8; 
            color: white;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        .edit-button:hover, .approval-button:hover {
            background-color: #138496; 
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
            border: 2px solid #007bff;
            border-radius: 25px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
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
            font-size: 16px;
            transition: all 0.3s ease;
            margin-left: 10px;
        }

        .add-button:hover {
            background-color: #218838;
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
    border-color: #007bff;
}

.delete-btn {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 5px 10px;
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
        }
    </style>
</head>
<body>
<section>
    <div class="header-container">
        <img src="../images/Logo.png" alt="Logo" class="logo">
        <h2>Sveiki, <?php echo htmlspecialchars($admin_name); ?>!</h2>
    </div>
    <?php if ($user_role !== 'Moderators'): ?>
    <button class="toggle-button" onclick="showTable('admin')">Admini</button>
    <?php endif; ?>
    <button class="toggle-button" onclick="showTable('client')">Klienti</button>
    <button class="toggle-button" onclick="showTable('product')">Produkti</button>
    <button class="toggle-button" onclick="showTable('subscriber')">Abonenti</button>
    <button class="toggle-button" onclick="showTable('contact')">Kontakti</button>
    
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
            <th>Role</th>
            <th>Apstiprināts</th>
            <th>Izveidots</th>
            <th>Darbības</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
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
        <th>Apstiprināts</th>
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
            <option value="DrosibasSistemas">Drošības sistēmas</option>
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
</section>

<script>
   document.addEventListener('DOMContentLoaded', function() {
    const lastTable = localStorage.getItem('lastTable') || 'admin';
    showTable(lastTable);
});

function showTable(table) {
    localStorage.setItem('lastTable', table);
    const adminTable = document.getElementById("admin-table");
    const clientTable = document.getElementById("client-table");
    const productTable = document.getElementById("product-table");
    const subscriberTable = document.getElementById("subscriber-table");
    const contactTable = document.getElementById("contact-table");
    const adminHeader = document.getElementById("admin-header");
    const clientHeader = document.getElementById("client-header");
    const productHeader = document.getElementById("product-header");
    const subscriberHeader = document.getElementById("subscriber-header");
    const contactHeader = document.getElementById("contact-header");
    const productSearch = document.getElementById("product-search");
    const adminActions = document.getElementById("admin-actions");
    const clientActions = document.getElementById("client-actions");
    const subscriberActions = document.getElementById("subscriber-actions");
    const contactActions = document.getElementById("contact-actions");

    [adminTable, clientTable, productTable, subscriberTable, contactTable].forEach(t => t.style.display = 'none');
    [adminHeader, clientHeader, productHeader, subscriberHeader, contactHeader].forEach(h => h.style.display = 'none');
    productSearch.style.display = 'none';
    adminActions.style.display = 'none';
    clientActions.style.display = 'none';
    subscriberActions.style.display = 'none';
    contactActions.style.display = 'none';

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
    } else {
        productTable.style.display = 'table';
        productHeader.style.display = 'block';
        productSearch.style.display = 'block';
    }
}

    function addNewAdmin() {
        window.location.href = 'add_admin.php';
    }

    function addNewClient() {
        window.location.href = 'add_client.php';
    }

    function addNewProduct() {
        window.location.href = 'add_product.php';
    }

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
                    location.reload();
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

            for (let j = 1; j < 4; j++) { // Search in name, surname, and email columns
                const cellText = cells[j].textContent.toLowerCase();
                if (cellText.includes(searchValue)) {
                    found = true;
                    break;
                }
            }
            row.style.display = found ? '' : 'none';
        }
    }

    // Admin ieladejas
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
            <option value="admin" ${admin.role === 'admin' ? 'selected' : ''}>Admin</option>
            <option value="moderator" ${admin.role === 'moderator' ? 'selected' : ''}>Moderator</option>
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



    fetch('get_clients.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const clients = data.clients;
            const tbody = document.querySelector("#client-table tbody");
            clients.forEach(client => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${client.id}</td>
                    <td>${client.email}</td>
                    <td>${client.name}</td>
                    <td>${client.approved ? 'Jā' : 'Nē'}</td>
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

    fetch('get_products.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const products = data.products;
                const tbody = document.querySelector("#product-table tbody");
                products.forEach(product => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${product.id}</td>
                        <td>${product.nosaukums}</td>
                        <td>${product.apraksts}</td>
                        <td><img src="../${product.bilde}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;"></td>
                        <td>${product.kategorija}</td>
                        <td>${product.cena}€</td>
                        <td>
                            <a href="productedit.html?id=${product.id}" class="edit-button">Labot</a>
                            <button class="edit-button" onclick="deleteProduct(${product.id})">Dzēst</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }
        });

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
                            <button class="edit-button" onclick="deleteSubscriber(${subscriber.id})">Dzēst</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }
        });

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
                        <button class="edit-button" onclick="deleteContact(${contact.id})">Dzēst</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
    });

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
</script>
</body>
</html>

