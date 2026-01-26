<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal Medical Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: orange;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .action-btns {
            display: flex;
            gap: 5px;
        }
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 14px;
        }
        .update-btn {
            background-color: #4CAF50;
            color: white;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php
    // require_once __DIR__ "/./core/databaseconnection.php";
 
    
    // // Connect query to database 
    // $sql = "SELECT * FROM animal";
    // $result = $conn->query($sql);
    
    // Check if connection is successful
    if($result->num_rows > 0) {
        echo "<h2>Animal Medical Records</h2>";
        echo "<table>
                <tr>
                    <th>Animal_ID</th>
                    <th>Animal_Name</th>
                    <th>Animal_Gender</th>
                    <th>Animal_Type</th>
                    <th>Animal_Breed</th>
   
                    <th>Animal_AgeGroup</th>
                    <th>Animal_HealthStatus</th>
                    <th>Spay/Neutered</th>
                    <th>Vacc_Status</th>
                    <th>Intake</th>
                    <th>RescueLocation</th>
                    <th>Actions</th>
                </tr>";
    
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".htmlspecialchars($row['Animal_ID'])."</td>
                    <td>".htmlspecialchars($row['Animal_Name'])."</td>
                    <td>".htmlspecialchars($row['Animal_Gender'])."</td>
                    <td>".htmlspecialchars($row['Animal_Type'])."</td>
                    <td>".htmlspecialchars($row['Animal_Breed'])."</td>

                    <td>".htmlspecialchars($row['Animal_AgeGroup'])."</td>
                    <td>".htmlspecialchars($row['Animal_HealthStatus'])."</td>
                    <td>".htmlspecialchars($row['IsSpayNeutered'])."</td>
                    <td>".htmlspecialchars($row['Animal_Vacc_Status'])."</td>
                    <td>".htmlspecialchars($row['intakeType'])."</td>
                    <td>".htmlspecialchars($row['Animal_RescueLocation'])."</td>
                    <td class='action-btns'>
                                             
                        <a href='controllers/AnimalController.php?action=update&id=<?php echo $row[Animal_ID]; ?>' class='btn update-btn'>Update</a>
                        <a href='controllers/AnimalController.php?action=delete&id=<?php echo $row[Animal_ID]; ?>' class='btn delete-btn'>Delete</a>
                    </td>
                </tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No animal records found in the database.</p>";
    }

    ?>
</body>
</html> // Fix the linking part of the controllers and the php