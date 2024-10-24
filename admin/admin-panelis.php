<?php
session_start();

// funkcija kas parbauda vai lietotājs ir ielogojies pretēja gadijumā pārmet uz login page
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

        .approval-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            color: white; 
        }

        .approval-button.approve {
            background: linear-gradient(45deg, #28a745, #6dd5ed); 
        }

        .approval-button.approve:hover {
            background: linear-gradient(45deg, #218838, #5bc0de); 
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .approval-button.revoke {
            background: linear-gradient(45deg, #dc3545, #ff6b81); 
        }

        .approval-button.revoke:hover {
            background: linear-gradient(45deg, #c82333, #ff4c4f); 
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .edit-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            background-color: #17a2b8; 
            color: white;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        .edit-button:hover {
            background-color: #138496; 
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>

<section>
    <button class="toggle-button" onclick="showTable('admin')">Admini</button>
    <button class="toggle-button" onclick="showTable('client')">Klienti</button>
    <a href="logout.php" class="logout-button">Iziet</a>
    <h2 id="admin-header" style="display: none;">Admini</h2>
    <table id="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Epasts</th>
                <th>Vārds</th>
                <th>Piekritis noteikumiem</th>
                <th>Statuss</th>
                <th>Izveidots</th>
                <th>Statuss</th> 
            </tr>
        </thead>
        <tbody>
            <!-- Admin dati -->
        </tbody>
    </table>

    <h2 id="client-header" style="display: none;">Reģistrētie klienti</h2>
    <table id="client-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Epasts</th>
                <th>Vārds</th>
                <th>Piekritis noteikumiem</th>
                <th>Izveidots</th>
            </tr>
        </thead>
        <tbody>
            <!-- klienta dati -->
        </tbody>
    </table>
</section>

<script>
    function showTable(table) {
        const adminTable = document.getElementById("admin-table");
        const clientTable = document.getElementById("client-table");
        const adminHeader = document.getElementById("admin-header");
        const clientHeader = document.getElementById("client-header");

        if (table === 'admin') {
            adminTable.style.display = 'table';
            clientTable.style.display = 'none';
            adminHeader.style.display = 'block'; 
            clientHeader.style.display = 'none'; 
        } else {
            adminTable.style.display = 'none';
            clientTable.style.display = 'table';
            adminHeader.style.display = 'none'; 
            clientHeader.style.display = 'block'; 
        }
    }

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
                        <td>${admin.accept_privacy_policy == 1 ? 'Jā' : 'Nē'}</td>
                        <td id="approved-status-${admin.id}">${admin.approved == 1 ? 'Apstiprināts' : 'Gaida apstiprinājumu'}</td>
                        <td>${admin.created_at}</td>
                        <td>
                            <button class="approval-button ${admin.approved == 1 ? 'revoke' : 'approve'}" 
                                    onclick="toggleApproved(${admin.id}, ${admin.approved})">
                                ${admin.approved == 1 ? 'Noņemt piekļuvi' : 'Apstiprināt'}
                            </button>
                            <a href="adminedit.html?id=${admin.id}" class="edit-button">Labot</a>
                        </td>
                    `;

                    tbody.appendChild(row);
                });
                showTable('admin'); 
            } else {
                alert("Failed to load admin data.");
            }
        })
        .catch(error => {
            console.error('Error fetching admin data:', error);
        });

    function editAdmin(adminId) {
        
        alert(`Admins ar ID: ${adminId} tiks rediģēts.`);
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
                        <td>${client.accept_privacy_policy == 1 ? 'Yes' : 'No'}</td>
                        <td>${client.created_at}</td>
                    `;

                    tbody.appendChild(row);
                });
            } else {
                alert("Failed to load client data.");
            }
        })
        .catch(error => {
            console.error('Error fetching client data:', error);
        });

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
</script>

</body>
</html>
