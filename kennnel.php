<?php
require('config/databaseconnection.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Handle kennel details popup (AJAX) ---
if(isset($_GET['kennel_details'])){
    $kennelID = $conn->real_escape_string($_GET['kennel_details']);
    
    // Get kennel info
    $sqlKennel = "SELECT * FROM kennel WHERE Kennel_ID='$kennelID'";
    $resKennel = $conn->query($sqlKennel);
    if($resKennel && $resKennel->num_rows>0){
        $kennel = $resKennel->fetch_assoc();
        echo "<h3>".htmlspecialchars($kennel['Kennel_Name'])." - ".$kennel['Kennel_Type']."</h3>";
        echo "<p><b>Address:</b> ".htmlspecialchars($kennel['Kennel_Address'])."</p>";
        echo "<p><b>Capacity:</b> ".$kennel['Kennel_Capacity']."</p>";
        echo "<p><b>Occupancy:</b> ".$kennel['Kennel_Occupancy']."</p>";
        echo "<button onclick=\"openCreateCage('".$kennel['Kennel_ID']."')\">Create New Cage</button>";
        
        // List cages in this kennel
        $sqlCages = "SELECT * FROM cage WHERE Kennel_ID='$kennelID' AND isDeleted=0";
        $resCages = $conn->query($sqlCages);
        if($resCages->num_rows>0){
            echo "<table>";
            echo "<tr><th>CageID</th><th>Animal Assigned</th><th>Status</th><th>Assigned By</th></tr>";
            while($cage=$resCages->fetch_assoc()){
                echo "<tr>";
                echo "<td>".htmlspecialchars($cage['CageID'])."</td>";
                echo "<td>".htmlspecialchars($cage['Animal_ID'] ?? '-')."</td>";
                echo "<td>".($cage['Occupancy_Status'] ? 'Occupied':'Empty')."</td>";
                echo "<td>".htmlspecialchars($cage['AssignedBy'] ?? '-')."</td>";
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
<html>
<head>
    <title>Kennels</title>
     <link rel="stylesheet" href="style.css"> <!-- Global CSS -->
</head>
<body>

<div id="kennels" >
    <h2>Kennels</h2>
    <button onclick="openCreateKennel()">Create New Kennel</button>
    
    <table>
        <tr>
            <th>Kennel ID</th><th>Name</th><th>Address</th><th>Capacity</th><th>Occupancy</th><th>Type</th>
        </tr>
        <?php while($row=$result->fetch_assoc()): ?>
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
        <button onclick="closePopup()">Close</button>
    </div>
</div>

<script>
function showKennelDetails(kennelID){
    fetch('?kennel_details='+kennelID)
    .then(res=>res.text())
    .then(data=>{
        document.getElementById('kennelPopupContent').innerHTML = data;
        document.getElementById('kennelPopup').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
    });
}

function closePopup(){
    document.getElementById('kennelPopup').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
}

function openCreateKennel(){
    // open create_kennel.php in a popup (can also be AJAX or a simple window.open)
    window.open('create_kennel.php', 'Create Kennel', 'width=500,height=400');
}

function openCreateCage(kennelID){
    window.open('create_cage.php?kennel='+kennelID, 'Create Cage', 'width=500,height=400');
}

function editCage(cageID){
    window.open('edit_cage.php?cage='+cageID, 'Edit Cage', 'width=500,height=400');
}
</script>

</body>
</html>



