<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once "./models/Adoption.php";
include_once "./models/Foster.php";

$foster = new Foster();
$adoption = new Adoption();



include_once './notification.php';

$page = "animaldatabase2.php"; // current page
require('config/databaseconnection.php');

// --- Handle popup request (AJAX) ---
if (isset($_GET['details'])) {
    $animalID = intval($_GET['details']); // safe cast to int

    $sql = "SELECT a.*, b.filePath 
            FROM animal a 
            LEFT JOIN (
                SELECT animalID, MIN(filePath) AS filePath
                FROM animalmedia
                GROUP BY animalID
            ) b ON a.Animal_ID = b.animalID
            WHERE a.Animal_ID = $animalID AND a.isDeleted = 0";

    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        echo "<h3>" . htmlspecialchars($row['Animal_Name']) . "</h3>";
        if ($row['filePath']) {
            echo "<img src='images/animals/" . htmlspecialchars($row['filePath']) . "' width='150'><br>";
        }
        echo "<p><b>Species:</b> " . htmlspecialchars($row['Animal_Type']) . "</p>";
        echo "<p><b>Gender:</b> " . htmlspecialchars($row['Animal_Gender']) . "</p>";
        echo "<p><b>Age Group:</b> " . htmlspecialchars($row['Animal_AgeGroup']) . "</p>";
        echo "<p><b>Intake Date:</b> " . date("d-M-Y", strtotime($row['Animal_RescueDate'])) . "</p>";
        echo "<p><b>Status:</b> " . ($row['outtakeType'] === 'Adoption' ? "Adopted" : "Available") . "</p>";
    } else {
        echo "Animal not found.";
    }
    exit;
}

// --- Normal page load (table + search/filter) ---
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

$Query = "SELECT a.*, b.filePath 
          FROM animal a 
          LEFT JOIN (
              SELECT animalID, MIN(filePath) AS filePath
              FROM animalmedia
              GROUP BY animalID
          ) b ON a.Animal_ID = b.animalID
          WHERE a.isDeleted = 0";

if ($search) {
    $Query .= " AND a.Animal_Name LIKE '%" . $conn->real_escape_string($search) . "%'";
}

if ($filter) {
    $Query .= " AND (a.Animal_AgeGroup = '" . $conn->real_escape_string($filter) . "' 
                 OR a.Animal_Type = '" . $conn->real_escape_string($filter) . "')";
}

$result = $conn->query($Query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>FurryHaven Admin Dashboard</title>
    <link rel="stylesheet" href="style2.css">
    <style>
        @media (max-width: 600px) {
            body {
                padding: 1rem;
            }
        }

        /* Status badges */
        .status.available .status-badge {
            background: #ecfdf5;
            color: #267d4b;
            border-radius: 999px;
            padding: 4px 10px;
            font-weight: 600;
        }

        .status.adopted .status-badge {
            background: #fff1f2;
            color: #c03e3e;
            border-radius: 999px;
            padding: 4px 10px;
            font-weight: 600;
        }

        /* Popup styles */
        #overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        /* #animalPopup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 12px;
            z-index: 1001;
            max-width: 90%;
            max-height: 80%;
            overflow-y: auto;
        }

        #animalPopup img {
            margin-bottom: 10px;
        } */
    </style>
</head>

<body>
    <div class="dashboard-container">
        <?php include 'sidebar2.php'; ?>

        <div class="main-content" id="mainContent">
            <h1>Registered Animals</h1>
            <blockquote>"Record every rescue — every life matters."</blockquote>
            <div class="card">
                <?php if ($_SESSION['user_role'] === 'Admin') :
                ?>
                    <button class="action-btn" onclick="location.href='./registration2.php'">+ New</button>
                <?php endif;
                ?>
                <form method="GET" class="form-inline">
                    <input type="text" class="search-box" name="search" placeholder="Search by name..." value="<?php echo htmlspecialchars($search); ?>">
                    <select class="filter-select" name="filter">
                        <option value="">-- Filter By --</option>
                        <option value="Adult" <?php echo $filter == 'Adult' ? 'selected' : ''; ?>>Adult</option>
                        <option value="Juvenile" <?php echo $filter == 'Juvenile' ? 'selected' : ''; ?>>Juvenile</option>
                        <option value="Senior" <?php echo $filter == 'Senior' ? 'selected' : ''; ?>>Senior</option>
                        <!-- <option value="Dog" <?php echo $filter == 'Dog' ? 'selected' : ''; ?>>Dog</option>
                        <option value="Cat" <?php echo $filter == 'Cat' ? 'selected' : ''; ?>>Cat</option> -->
                    </select>
                    <button type="submit" class="search-filter-btn">Search / Filter</button>
                </form>

                <table>
                    <tr>
                        <th>Thumbnail</th>
                        <th>Name</th>
                        <th>Adoption Status</th>
                        <?php if ($_SESSION['user_role'] === "Admin"): ?>
                            <th>Applications</th>
                        <?php endif; ?>
                        <th>Species</th>
                        <th>Gender</th>
                        <th>Intake Date</th>
                    </tr>

                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr onclick="showDetails('<?php echo $row['Animal_ID']; ?>')">
                                <td><?php if ($row['filePath']): ?><img src="images/animals/<?php echo htmlspecialchars($row['filePath']); ?>" width="50"><?php endif; ?></td>
                                <td><?php echo htmlspecialchars($row['Animal_Name']); ?></td>
                                <td class="status <?= $foster->getFosterByAnimalId($row["Animal_ID"])->num_rows>0 || $adoption->findOneByAnimalID($row["Animal_ID"])->num_rows>0 ? 'adopted' : 'available'; ?>">
                                    <span class="status-badge"><?= $foster->getFosterByAnimalId($row["Animal_ID"])->num_rows>0 || $adoption->findOneByAnimalID($row["Animal_ID"])->num_rows>0 ? 'Not Available' : 'Available'; ?></span>
                                </td>
                                <?php if ($_SESSION['user_role'] === "Admin"): ?>
                                    <td><a href="animal_applications.php?animal_id=<?php echo $row['Animal_ID']; ?>">View Applications</a></td>
                                <?php endif; ?>
                                <td><?php echo htmlspecialchars($row['Animal_Type']); ?></td>
                                <td><?php echo htmlspecialchars($row['Animal_Gender']); ?></td>
                                <td><?php echo date("d-M-Y", strtotime($row['Animal_RescueDate'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No animals found.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
            <!-- Overlay & Popup -->
            <div id="overlay" onclick="closeAnimalPopup()"></div>
            <div id="animalPopup" style="display: none;">
                <div id="popupContent"></div>
                <button onclick="closeAnimalPopup()">close</button>
            </div>
        </div>
    </div>


    <script>
        function showDetails(animalID) {
            fetch('showAnimalDetails.php?details=' + animalID)
                .then(res => res.text())
                .then(data => {
                    document.getElementById('popupContent').innerHTML = data;
                    document.getElementById('animalPopup').style.display = 'block';
                    document.getElementById('overlay').style.display = 'block';
                });
        }

        function closeAnimalPopup() {
            document.getElementById('animalPopup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }
    </script>

    <?php include 'footer2.php'; ?>
    <script src="sidebar2.js"></script>
</body>

</html>