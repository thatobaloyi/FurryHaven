<?php
// dashboard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once './notification.php';

include_once __DIR__ . '/./config/databaseconnection.php';
global $conn;

// Redirect to login if not authentica


$vetQuery = "SELECT * FROM medicalprocedure WHERE isDeleted = 0 ORDER BY procedureDate";
$result = $conn->query($vetQuery);

if (!$result) {
    die("Query failed: " . $conn->error);
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Dashboard</title>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style2.css">




</head>

<body>
    <div class="dashboard-container">
        <?php include_once './sidebar2.php'?>
        <!-- Recent Activities Section -->
        <div class="main-content" id="mainContent">
            <div class="section-header">
                <h2>All Animal Procedure Details</h2>
            </div>
            <table class="volunteeractivity-table">
                <thead>
                    <tr>
                        <th>Medical ID</th>
                        <th>Vet ID</th>
                        <th>Procedure Type</th>
                        <th>Outcome</th>
                        <th>Date</th>
                        <th>Details</th>
                        <th>Update</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <form action="./controllers/MedicalProcedureController.php" method="POST">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="medicalID" value="<?php echo htmlspecialchars($row['medicalID']); ?>">
                                <td><?php echo htmlspecialchars($row['medicalID']); ?></td>
                                <td><?php echo htmlspecialchars($row['vetID']); ?></td>
                                <td><?php echo htmlspecialchars($row['procedureType']); ?></td>
                                <td>
                                    <select name="procedureOutcome">
                                        <option value="Successful" <?php if ($row['procedureOutcome'] == "Successful") echo "selected"; ?>>Successful</option>
                                        <option value="Ongoing" <?php if ($row['procedureOutcome'] == "Ongoing") echo "selected"; ?>>Ongoing</option>
                                        <option value="Failed" <?php if ($row['procedureOutcome'] == "Failed") echo "selected"; ?>>Failed</option>
                                        <option value="Follow-up Required" <?php if ($row['procedureOutcome'] == "Follow-up Required") echo "selected"; ?>>Follow-up Required</option>
                                    </select>
                                </td>
                                <td><?php echo htmlspecialchars(date("d-M-Y", strtotime($row['procedureDate']))); ?></td>
                                <td><textarea name="details"><?php echo htmlspecialchars($row['details']); ?></textarea></td>
                                <td>
                                    <button type="submit" class="update-btn">Update</button>
                                </td>
                                <td>
                                    <button type="button" class="delete-btn" onclick="document.getElementById('delete-form-<?php echo $row['medicalID']; ?>').submit();">Delete</button>
                                </td>
                            </form>
                        </tr>
                        <tr style="display: none;">
                            <td>
                                <form id="delete-form-<?php echo $row['medicalID']; ?>" action="./controllers/MedicalProcedureController.php" method="POST">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="medicalID" value="<?php echo htmlspecialchars($row['medicalID']); ?>">
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

<script src="sidebar2.js"></script>
    <script>
        function showDetails(medicalID) {
            // This function would show more details about a volunteer application
            // Use a custom modal instead of alert to handle this.
            console.log('Showing details for application: ' + medicalID);
            // Example of a custom modal (requires more HTML/CSS)
            // const modal = document.getElementById('details-modal');
            // modal.style.display = 'block';
            // document.getElementById('modal-content').innerText = 'Details for ' + volunteerID;
        }

        function confirmDelete(form) {
            // Use a custom modal instead of a browser-based confirm.
            const userConfirmed = window.prompt("Are you sure you want to delete this activity? Type 'DELETE' to confirm.");
            if (userConfirmed === 'DELETE') {
                return true;
            }
            return false;
        }
    </script>
</body>

</html>