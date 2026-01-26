<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//     // $serverName = "is3-dev.ict.ru.ac.za";
//     // $user = "EzTeck";
//     // $password = "Ezt3ck!25";
//     // $database = "ezteck";  

//     // $conn = new mysqli($serverName, $user. $password, $database);

//     include_once __DIR__ . '/./config/databaseconnection.php';

//     global $conn;


// // CREATE
// if (isset($_POST['add'])) {
//     $medicalID = $_POST['medicalID'];
//     $animalID = $_POST['animalID'];
//     $vetID = $_POST['vetID'];
//     $procedureType = $_POST['procedureType'];
//     $procedureOutcome = $_POST['procedureOutcome'];
//     $procedureDate = $_POST['procedureDate'];
//     $details = $_POST['details'];

//     $sql = "INSERT INTO medicalprocedure 
//             (medicalID, animalID, vetID, procedureType, procedureOutcome, procedureDate, details)
//             VALUES ('$medicalID', '$animalID', '$vetID', '$procedureType', '$procedureOutcome', '$procedureDate', '$details')";
//     if($conn->query($sql)){
//         echo "added";
//     }else{
//         echo "not added";
//     }
// }

// // UPDATE
// if (isset($_POST['update'])) {
//     $medicalID = $_POST['medicalID'];
//     $procedureOutcome = $_POST['procedureOutcome'];
//     $details = $_POST['details'];

//     $sql = "UPDATE medicalprocedure 
//             SET procedureOutcome='$procedureOutcome', details='$details'
//             WHERE medicalID='$medicalID'";
//     $conn->query($sql);
// }

// // READ
// $result = $conn->query("SELECT * FROM medicalprocedure ORDER BY procedureDate DESC");
?>

<h2>Add New Medical Record</h2>
<form action="/medical/submit" method="POST">
    <!-- Medical ID: <input type="text" name="medicalID" required><br> -->
    Animal ID: <input type="text" name="animalID" required><br>
    <!-- Vet ID: <input type="text" name="vetID" required><br> -->

    Procedure Type:
    <select name="procedureType" required>
        <option value="">-- Select --</option>
        <option value="Vaccination">Vaccination</option>
        <option value="Surgery">Surgery</option>
        <option value="Dental">Dental</option>
        <option value="Sterilisation">Sterilisation</option>
        <option value="Check-up">Check-up</option>
    </select><br>

    Procedure Outcome:
    <select name="procedureOutcome" required>
        <option value="">-- Select --</option>
        <option value="Successful">Successful</option>
        <option value="Ongoing">Ongoing</option>
        <option value="Failed">Failed</option>
        <option value="Follow-up Required">Follow-up Required</option>
    </select><br>

    Procedure Date: <input type="datetime-local" name="procedureDate" required><br>
    Details: <textarea name="details"></textarea><br>

    <button type="submit" name="add">Add Record</button>
</form>

<h2>Medical History</h2>
<table border="1" cellpadding="5">
    <tr>
        <th>Medical ID</th>
        <th>Animal ID</th>
        <th>Vet ID</th>
        <th>Procedure Type</th>
        <th>Outcome</th>
        <th>Date</th>
        <th>Details</th>
        <th>Update</th>
        <th>Delete</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <form action="/medical/update" method="POST">
            <td>
                <?php echo $row['medicalID']; ?>
                <input type="hidden" name="medicalID" value="<?php echo $row['medicalID']; ?>">
            </td>
            <td><?php echo $row['animalID']; ?></td>
            <td><?php echo $row['vetID']; ?></td>
            <td><?php echo $row['procedureType']; ?></td>
            <td>
                <select name="procedureOutcome">
                    <option value="Successful" <?php if($row['procedureOutcome']=="Successful") echo "selected"; ?>>Successful</option>
                    <option value="Ongoing" <?php if($row['procedureOutcome']=="Ongoing") echo "selected"; ?>>Ongoing</option>
                    <option value="Failed" <?php if($row['procedureOutcome']=="Failed") echo "selected"; ?>>Failed</option>
                    <option value="Follow-up Required" <?php if($row['procedureOutcome']=="Follow-up Required") echo "selected"; ?>>Follow-up Required</option>
                </select>
            </td>
            <td><?php echo $row['procedureDate']; ?></td>
            <td><textarea name="details"><?php echo $row['details']; ?></textarea></td>
            <td><button type="submit" name="update">Save</button></td>
        </form>
        <form action="/medical/delete" method="POST">
            <input type="hidden" name="medicalID" value="<?php echo $row['medicalID']; ?>">
            <td><button type="submit" name="delete">Delete</button></td>
        </form>
    </tr>
    <?php endwhile; ?>
</table>