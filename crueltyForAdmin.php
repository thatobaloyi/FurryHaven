<?php
require('config/databaseconnection.php');  
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$crueltyReportQuery = "SELECT crueltyID, reportDate, description, animalStreetAddress, evidence, incidentType 
    FROM crueltyreport where isDeleted = 0 ";
$result = $conn->query($crueltyReportQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cruelty Reports</title>
<style>
    body { margin: 0; font-family: Arial, sans-serif; }

    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    tr:hover { background-color: #f9f9f9; cursor: pointer; }

    /* Sidebar */
    #sidebar { 
        position: fixed;
        left: 0;
        top: 0;
        width: 220px;
        height: 100%;
        background: #2c3e50;
        color: white;
        transition: 0.3s;
        overflow-y: auto;
        padding-top: 20px;
    }
    #sidebar a { display: block; color: white; padding: 12px 20px; text-decoration: none; }
    #sidebar a:hover { background: #34495e; }
    #sidebar .bottom { position: absolute; bottom: 0; width: 100%; }

    /* Main content */
    #main {
        margin-left: 220px;
        transition: margin-left 0.3s;
        padding: 20px;
    }

    /* Toggle button */
    .toggleBtn {
        position: fixed;
        top: 10px;
        left: 10px;
        font-size: 24px;
        background: #2c3e50;
        color: white;
        padding: 8px 12px;
        cursor: pointer;
        z-index: 1000;
        border-radius: 4px;
    }

    button {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        background-color: #98b06f;
        color: white;
        cursor: pointer;
    }
</style>
</head>
<body>

<span class="toggleBtn" onclick="toggleSidebar()">☰</span>

<div id="sidebar">
    <h3 style="text-align:center;">Dashboard</h3>
    <a href="#">Dashboard</a>
    <a href="/animals/register">Register Animal</a>
    <a href="/animals">Animal Database</a>
    <a href="#">Cruelty Reports</a>
    <a href="#">Volunteers</a>
    <a href="#">Adopters</a>
    <a href="#">Donations</a>

    <div class="bottom">
        <a href="deleted_records.php">🗑️</a>
        <a href="#">System Settings</a>
        <a href="#">Staff Profile</a>
        <a href="#">Search Animal</a>
        <a href="#">Help & Support</a>
        <a href="#">Profile</a>
    </div>
</div>

<div id="main">

    <h2 style="display: flex; justify-content: space-between; align-items: center;">
        Current Cruelty Reports 
    </h2>

    <a href="cruelty.php">
        <button style="margin-top: 10px; float: right;">New Report</button>
    </a>
    <br><br>

    <table>
        <tr>
            <th>Report Date</th>
            <th>Description</th>
            <th>Animal Street Address</th>
            <th>Evidence</th>
            <th>Incident Type</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['reportDate'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['description'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['animalStreetAddress'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['evidence'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['incidentType'] ?? '') . "</td>";
                echo "<td>
                        <form method='POST' action='/cruelty/softdelete' onsubmit='return confirm(\"Are you sure you want to delete this report?\");'>
                            <input type='hidden' name='crueltyID' value='" . htmlspecialchars($row['crueltyID']) . "'>
                            <button type='submit'>Delete</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No records found</td></tr>";
        }
        $conn->close();
        ?>
    </table>

</div>

<script>
function toggleSidebar(){
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');

    if (sidebar.style.left === '-220px') {
        sidebar.style.left = '0';
        main.style.marginLeft = '220px';
    } else {
        sidebar.style.left = '-220px';
        main.style.marginLeft = '0';
    }
}
</script>

</body>
</html>
