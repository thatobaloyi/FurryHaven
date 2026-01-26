<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once './notification.php';

require('config/databaseconnection.php');


// --- Handle kennel details popup (AJAX) ---
if (isset($_GET['kennel_details'])) {
    $kennelID = $conn->real_escape_string($_GET['kennel_details']);

    // Get kennel info
    $sqlKennel = "SELECT * FROM kennel WHERE Kennel_ID='$kennelID'";
    $resKennel = $conn->query($sqlKennel);
    if ($resKennel && $resKennel->num_rows > 0) {
        $kennel = $resKennel->fetch_assoc();
        echo "<h3>" . htmlspecialchars($kennel['Kennel_Name']) . " - " . $kennel['Kennel_Type'] . "</h3>";
        echo "<p><b>Address:</b> " . htmlspecialchars($kennel['Kennel_Address']) . "</p>";
        echo "<p><b>Capacity:</b> " . $kennel['Kennel_Capacity'] . "</p>";
        echo "<p><b>Occupancy:</b> " . $kennel['Kennel_Occupancy'] . "</p>";
        // echo "<button class=\"action-btn\" onclick=\"openCreateCage('" . $kennel['Kennel_ID'] . "')\">Create New Cage</button>";

        // List cages in this kennel
        $sqlCages = "SELECT * FROM cage WHERE Kennel_ID='$kennelID' AND isDeleted=0";
        $resCages = $conn->query($sqlCages);
        if ($resCages->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>CageID</th><th>Animal Assigned</th><th>Status</th><th>Assigned By</th></tr>";
            while ($cage = $resCages->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($cage['CageID']) . "</td>";
                echo "<td>" . htmlspecialchars($cage['Animal_ID'] ?? '-') . "</td>";
                echo "<td>" . ($cage['Occupancy_Status'] ? 'Occupied' : 'Empty') . "</td>";
                echo "<td>" . htmlspecialchars($cage['AssignedBy'] ?? '-') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No cages in this kennel.</p>";
        }
    }
    exit; // stop here for AJAX
}

// --- Normal page load: list all kennels ---
$sqlAllKennels = "SELECT * FROM kennel";
$result = $conn->query($sqlAllKennels);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>FurryHaven Admin Dashboard</title>
    <link rel="stylesheet" href="style2.css"> <!-- Global CSS -->
    <style>
        h1 {
            color: #003366;
            text-align: center;
            border-bottom: 2px solid #FF8C00;
            padding-bottom: 0.5rem;
        }

        blockquote {
            font-style: italic;
            text-align: center;
            color: #003366;
            font-size: 1.1rem;
            margin: 1.5rem 0;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Lexend', sans-serif;
            cursor: pointer;
            transition: 0.3s;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'sidebar2.php'; ?>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <h1>Kennel assignment page</h1>
            <blockquote>"Record every rescue — every life matters."</blockquote>
            <div class="card">

                <div id="kennels">
                    <button onclick="openCreateKennel()" class="action-btn">Create New Kennel</button>
                    <br>
                    <br>
                    <table>
                        <tr>
                            <th>Kennel ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Capacity</th>
                            <th>Occupancy</th>
                            <th>Type</th>
                        </tr>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr onclick="showKennelDetails('<?php echo $row['Kennel_ID']; ?>')">
                                <td><?php echo htmlspecialchars($row['Kennel_ID']); ?></td>
                                <td><?php echo htmlspecialchars($row['Kennel_Name']); ?></td>
                                <td><?php echo htmlspecialchars($row['Kennel_Address']); ?></td>
                                <td><?php echo $row['Kennel_Capacity']; ?></td>
                                <td><?php echo $row['Kennel_Occupancy']; ?></td>
                                <td><?php echo htmlspecialchars($row['Kennel_Type']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>

                    <!-- Popup for kennel details -->
                    <div id="overlay" onclick="closePopup()" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);"></div>
                    <div id="kennelPopup" style="display:none;position:fixed;top:10%;left:50%;transform:translateX(-50%);background:#fff;padding:20px;border:1px solid #000;z-index:1001;max-height:80%;overflow:auto;">
                        <div id="kennelPopupContent"></div>
                        <button onclick="closePopup()" class="btn-secondary">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showKennelDetails(kennelID) {
            fetch('?kennel_details=' + kennelID)
                .then(res => res.text())
                .then(data => {
                    document.getElementById('kennelPopupContent').innerHTML = data;
                    document.getElementById('kennelPopup').style.display = 'block';
                    document.getElementById('overlay').style.display = 'block';
                });
        }

        function closePopup() {
            document.getElementById('kennelPopup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        function openCreateKennel() {
            const url = `./create_kennel.php`;
            window.location.href = url;
        }

        function openCreateCage(kennelID) {
            const url = `./create_cage.php?kennel=${kennelID}`;
            window.location.href = url;
        }

        function editCage(cageID) {
            const url = `edit_cage.php?cage=${cageID}`;
            window.location.href = url;
        }
    </script>

    <script src="sidebar2.js"></script>
    <?php include 'footer2.php'; ?>
</body>

</html>