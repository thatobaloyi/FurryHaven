<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/config/databaseconnection.php';
include_once __DIR__ . '/models/Animal.php';
include_once __DIR__ . '/models/AnimalApplication.php';

// Instantiate models
$animalModel = new Animal();
$applicationModel = new AnimalApplication();

// Get all animals
$result = $animalModel->findAll();

// Get recent applications
$recentApplications = $applicationModel->getRecentApplications(5);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style2.css">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Lexend', sans-serif;
            background-color: #FFF8F0;
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
            color: #18436e;
            margin-bottom: 20px;
        }

        .dashboard-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .dashboard-section {
            border: 2px solid #df7100;
            padding: 20px;
            border-radius: 8px;
            background-color: #fff;
        }

        .dashboard-section h2 {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .action-links a {
            color: #df7100;
            text-decoration: none;
        }

        .action-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <h1>Welcome, <?php echo "$_SESSION[first_name] $_SESSION[last_name]" ?></h1>

    <div class="dashboard-container">
        <div class="dashboard-section">
            <h2>Recent Animal Applications</h2>
            <table>
                <thead>
                    <tr>
                        <th>Applicant Name</th>
                        <th>Animal Name</th>
                        <th>Application Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($application = $recentApplications->fetch_assoc()) : ?>
                        <tr>
                            <td><?= htmlspecialchars($application['username']) ?></td>
                            <td><?= htmlspecialchars($application['Animal_Name']) ?></td>
                            <td><?= htmlspecialchars($application['applicationDate']) ?></td>
                            <td class="action-links"><a href="animal_application_edit.php?id=<?= $application['animalappID'] ?>">View/Edit</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="dashboard-section">
            <h2>Registered Animals</h2>
            <button onclick="location.href='registration.php'">+ New</button>

            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by name..." value="">
                <select name="filter">
                    <option value="">-- Filter By --</option>
                    <option value="Adult">Adult</option>
                    <option value="Juvenile">Juvenile</option>
                    <option value="Senior">Senior</option>
                    <option value="Dog">Dog</option>
                    <option value="Cat">Cat</option>
                </select>
                <button type="submit">Search / Filter</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th></th> <!-- Image -->
                        <th>Name</th>
                        <th>Adoption Status</th>
                        <th>Applications</th>
                        <th>Species</th>
                        <th>Gender</th>
                        <th>Intake Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <?php
                        $applicationCount = 0;
                        $countStmt = $conn->prepare("SELECT COUNT(*) as count FROM animalapplication WHERE animalID = ?");
                        $countStmt->bind_param("s", $row['Animal_ID']);
                        $countStmt->execute();
                        $countResult = $countStmt->get_result()->fetch_assoc();
                        if ($countResult) {
                            $applicationCount = $countResult['count'];
                        }
                        $countStmt->close();
                        ?>
                        <tr onclick="showDetails('<?= $row['Animal_ID']; ?>')">
                            <td><img src="./images/animals/<?= $row['Animal_ID']; ?>.jpg" width="50"></td>
                            <td><?= htmlspecialchars($row['Animal_Name']); ?></td>
                            <td><?= htmlspecialchars($row['outtakeType'] ? 'Adopted' : 'Available'); ?></td>
                            <td><?= $applicationCount ?></td>
                            <td><?= htmlspecialchars($row['Animal_Type']); ?></td>
                            <td><?= htmlspecialchars($row['Animal_Gender']); ?></td>
                            <td><?= date("d-M-Y", strtotime($row['Animal_RescueDate'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Popup -->
    <div id="animalPopup" style="display:none; position:fixed; top:10%; left:20%; background:white; border:1px solid black; padding:20px; z-index:1000;">
        <div id="popupContent"></div>
        <button onclick="closePopup()">Close</button>
    </div>

    <script>
        function showDetails(animalID) {
            fetch('showAnimalDetails.php?details=' + animalID)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('popupContent').innerHTML = data;
                    document.getElementById('animalPopup').style.display = 'block';
                });
        }

        function closePopup() {
            document.getElementById('animalPopup').style.display = 'none';
        }
    </script>
</body>

</html>