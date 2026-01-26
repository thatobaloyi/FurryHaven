<?php
require('config/databaseconnection.php');  
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Handle popup request (AJAX) ---
if (isset($_GET['details'])) {
    $animalID = intval($_GET['details']); // safe cast to int
    $sql = "SELECT * FROM animal a LEFT Join (SELECT animalID, MIN(filePath) AS filePath
                  FROM animalmedia
                  GROUP BY animalID) b on a.Animal_ID = b.animalID WHERE isDeleted = 0
    ";
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        echo "<h3>".htmlspecialchars($row['Animal_Name'])."</h3>";
        if ($row['filePath']) {
            echo "<img src='images/animals/".htmlspecialchars($row['filePath'])."' width='150'><br>";
        }
        echo "<p><b>Species:</b> ".htmlspecialchars($row['Animal_Type'])."</p>";
        echo "<p><b>Gender:</b> ".htmlspecialchars($row['Animal_Gender'])."</p>";
        echo "<p><b>Age Group:</b> ".htmlspecialchars($row['Animal_AgeGroup'])."</p>";
        echo "<p><b>Intake Date:</b> ".date("d-M-Y", strtotime($row['Animal_RescueDate']))."</p>";
        echo "<p><b>Status:</b> ".($row['outtakeType'] ? "Adopted" : "Available")."</p>";
    } else {
        echo "Animal not found.";
    }
    exit; // stop here so the page doesn’t reload
}

// --- Normal page load (table + search/filter) ---
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

$Query = "
   SELECT * FROM animal a 
   LEFT JOIN (
       SELECT animalID, MIN(filePath) AS filePath
       FROM animalmedia
       GROUP BY animalID
   ) b on a.Animal_ID = b.animalID
   WHERE isDeleted = 0
";

if ($search) {
    $Query .= " AND a.Animal_Name LIKE '%" . $conn->real_escape_string($search) . "%'";
}

if ($filter) {
    $Query .= " AND a.Animal_AgeGroup = '" . $conn->real_escape_string($filter) . "'";
}

$result = $conn->query($Query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registered Animals</title>
        <link rel="stylesheet" href="style.css"> <!-- Global CSS -->
</head>
<body style="margin:0; padding:0;">

<h2>Registered Animals</h2>


<form method="GET">
    <input type="text" name="search" placeholder="Search by name..." value="<?php echo htmlspecialchars($search); ?>">
    <select name="filter">
        <option value="">-- Filter By --</option>
        <option value="Adult" <?php echo $filter=='Adult'?'selected':''; ?>>Adult</option>
        <option value="Juvenile" <?php echo $filter=='Juvenile'?'selected':''; ?>>Juvenile</option>
        <option value="Senior" <?php echo $filter=='Senior'?'selected':''; ?>>Senior</option>
    </select>
    <button type="submit">Search / Filter</button>
</form>

<table border="1" width="100%" cellspacing="0" cellpadding="5">
    <tr>
        <th></th><th>Name</th><th>Adoption Status</th><th>Applications</th><th>Species</th><th>Gender</th><th>Intake Date</th>
    </tr>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr onclick="showDetails('<?php echo $row['Animal_ID']; ?>')">
                <td>
                    <?php if($row['filePath']): ?>
                        <img src="images/animals/<?php echo $row['filePath']; ?>" width="50">
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['Animal_Name']); ?></td>
                <td><?php echo $row['outtakeType'] ? 'Adopted' : 'Available'; ?></td>
                <td><a href="animal_applications.php?animal_id=<?php echo $row['Animal_ID']; ?>">View Applications</a></td>
                <td><?php echo htmlspecialchars($row['Animal_Type']); ?></td>
                <td><?php echo htmlspecialchars($row['Animal_Gender']); ?></td>
                <td><?php echo date("d-M-Y", strtotime($row['Animal_RescueDate'])); ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="7">No animals found.</td></tr>
    <?php endif; ?>
</table>

<!-- Overlay -->
<div id="overlay" onclick="closeAnimalPopup()" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:999;"></div>

<!-- Animal Popup -->
<div id="animalPopup" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:1000;background:white;border:1px solid black;padding:15px;width:80%;max-width:600px;max-height:80vh;overflow-y:auto;">
    <div id="popupContent"></div>
    <button onclick="closeAnimalPopup()">Close</button>
</div>

<script>
function showDetails(animalID){
    fetch('showAnimalDetails.php?details='+animalID)
    .then(res=>res.text())
    .then(data=>{
        document.getElementById('popupContent').innerHTML=data;
        document.getElementById('animalPopup').style.display='block';
        document.getElementById('overlay').style.display='block';
    });
}
function closeAnimalPopup(){
    document.getElementById('animalPopup').style.display='none';
    document.getElementById('overlay').style.display='none';
}
</script>

</body>
</html>
