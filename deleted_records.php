<?php

include_once './models/Animal.php';


include_once './notification.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require('config/databaseconnection.php');

$animal = new Animal();
// Fetch deleted animals
$deletedAnimals = $animal->findDeleted();

if (!$deletedAnimals) {
    die("Error fetching animals: " . $conn->error);
}

// Fetch deleted cruelty reports
$deletedCruelty = $conn->query("SELECT * FROM crueltyreport WHERE isDeleted = 1");
if (!$deletedCruelty) {
    die("Error fetching cruelty reports: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Deleted Records</title>
<link rel="stylesheet" href="style2.css">

</head>
<body>
 <div class="dashboard-container">
    <!-- Sidebar -->
    <?php include 'sidebar2.php'; ?>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">

      <h1>Deleted Records</h1>
      <blockquote>"Record every rescue — every life matters."</blockquote>

<table>
        <tr>
            <th></th>
            <th>Animal Name</th>
            <th>Outtake Type</th>
            <th>Applications</th>
            <th>Animal Type</th>
            <th>Animal Gender</th>
            <th>Rescue Date</th>
            <th>Action</th>
            <th>Action</th>
        </tr>
        </tr>

        
<?php if ($deletedAnimals->num_rows > 0): ?>
<?php while($row = $deletedAnimals->fetch_assoc()): ?>
<tr onclick="showDetails('<?php echo $row['Animal_ID']; ?>')">
    <td>
        <!-- <?php var_dump($row['filePath']) ?> -->
        <img src="./images/animals/<?php echo $row['filePath']; ?>" width="50" alt="<?php echo $row['Animal_Name']; ?>">
    </td>
    <td><?php echo $row['Animal_Name']; ?></td>
    <td><?php echo $row['outtakeType'] ? 'Adopted' : 'Available'; ?></td>
    <td><?php echo rand(0,5); ?></td>
    <td><?php echo $row['Animal_Type']; ?></td>
    <td><?php echo $row['Animal_Gender']; ?></td>
    <td><?php echo date("d-M-Y", strtotime($row['Animal_RescueDate'])); ?></td>
    <td>
    <form method="POST" action="./controllers/AnimalController.php">
        <input type="hidden" name="action" value="restore">
        <input type="hidden" name="Animal_ID" value="<?php echo htmlspecialchars($row['Animal_ID']); ?>">
        <button type="submit">Reinstate</button>
    </form> 
</td>
<td>
    <form method="POST" action="./controllers/AnimalController.php" onsubmit="return confirm('Are you sure?');">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="Animal_ID" value="<?php echo htmlspecialchars($row['Animal_ID']); ?>">
        <button type="submit">Delete Forever!!</button>
    </form> 
    </td>


</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="7">No deleted animal reports found.</td></tr>
<?php endif; ?>
</table>


<h2>Deleted Cruelty Reports</h2>

<table>
 <tr>
    <th>Evidence</th>
    <th>Report Date</th>
    <th>Description</th>
    <th>Animal Street Address</th>
    <th>Incident Type</th>
    <th>Action</th>
    <th>Action</th>
</tr>

<?php if ($deletedCruelty->num_rows > 0): ?>
<?php while($row2 = $deletedCruelty->fetch_assoc()): ?>
<tr onclick="showDetails('<?php echo $row2['crueltyID']; ?>')">
    <td>
        <img src="./images/cruelty/<?php echo $row2['evidence']; ?>" width="50" alt="Evidence">
    </td>
    <td><?php echo date("d-M-Y", strtotime($row2['reportDate'])); ?></td>
    <td><?php echo htmlspecialchars($row2['description']); ?></td>
    <td><?php echo htmlspecialchars($row2['animalStreetAddress']); ?></td>
    <td><?php echo htmlspecialchars($row2['incidentType']); ?></td>
    <td>
        <form method="POST" action="./controllers/CrueltyReportController.php">
            <input type="hidden" name="action" value="restore">
            <input type="hidden" name="crueltyID" value="<?php echo htmlspecialchars($row2['crueltyID']); ?>">
            <button type="submit">Reinstate</button>
        </form>
    </td>
    <td>
        <form method="POST" action="./controllers/CrueltyReportController.php" onsubmit="return confirm('Are you sure you want to permanently delete this report?');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="crueltyID" value="<?php echo htmlspecialchars($row2['crueltyID']); ?>">
            <button type="submit">Delete Forever!!</button>
        </form>
    </td>

</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="7">No deleted cruelty reports found.</td></tr>
<?php endif; ?>
</table>



</div>

<script>
function toggleSidebar(){
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');

    if(sidebar.style.left === '0px' || sidebar.style.left === ''){
        sidebar.style.left = '-220px';
        main.style.marginLeft = '0';
    } else {
        sidebar.style.left = '0';
        main.style.marginLeft = '220px';
    }
}
</script>

<script src="sidebar2.js"></script>

</body>
</html>
