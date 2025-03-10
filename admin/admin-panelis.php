<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: adminlogin.html");
    exit();
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
    <button class="toggle-button" onclick="showTable('admin')">Admini</button>
    <button class="toggle-button" onclick="showTable('client')">Klienti</button>
    <button class="toggle-button" onclick="showTable('product')">Produkti</button>
    <button class="toggle-button" onclick="showTable('subscriber')">Abonenti</button>
    
    <a href="logout.php" class="logout-button">Iziet</a>

    <h2 id="admin-header" style="display: none;">Admini</h2>
    <div class="search-container" id="admin-actions" style="display: none;">
        <button class="add-button" onclick="addNewAdmin()">+Pievienot adminu</button>
    </div>
    <table id="admin-table">
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

    <h2 id="client-header" style="display: none;">Reģistrētie klienti</h2>
    <div class="search-container" id="client-actions" style="display: none;">
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
</section>

<script>
   document.addEventListener('DOMContentLoaded', function() {
        showTable('admin');
    });

    function showTable(table) {
        const adminTable = document.getElementById("admin-table");
        const clientTable = document.getElementById("client-table");
        const productTable = document.getElementById("product-table");
        const subscriberTable = document.getElementById("subscriber-table");
        const adminHeader = document.getElementById("admin-header");
        const clientHeader = document.getElementById("client-header");
        const productHeader = document.getElementById("product-header");
        const subscriberHeader = document.getElementById("subscriber-header");
        const productSearch = document.getElementById("product-search");
        const adminActions = document.getElementById("admin-actions");
        const clientActions = document.getElementById("client-actions");

        [adminTable, clientTable, productTable, subscriberTable].forEach(t => t.style.display = 'none');
        [adminHeader, clientHeader, productHeader, subscriberHeader].forEach(h => h.style.display = 'none');
        productSearch.style.display = 'none';
        adminActions.style.display = 'none';
        clientActions.style.display = 'none';

        if (table === 'admin') {
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

    document.getElementById('productSearchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const table = document.getElementById('product-table');
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
    });

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
    <td>${admin.name}</td>
    <td>${admin.email}</td>
    <td>${admin.accept_privacy_policy ? 'Jā' : 'Nē'}</td>
    <td>${admin.created_at}</td>
    <td>
        <button 
            onclick="updateApproved(${admin.id}, ${admin.accept_privacy_policy ? 0 : 1})"
            class="edit-button"
        >
            ${admin.accept_privacy_policy ? 'Atsaukt apstiprinājumu' : 'Apstiprināt'}
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
                    <td>${client.accept_privacy_policy == 1 ? 'Jā' : 'Nē'}</td>
                    <td>${client.created_at}</td>
                    <td>
                        <a href="adminedit.html?id=${client.id}" class="edit-button">Labot</a>
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
</script>
</body>
</html>

