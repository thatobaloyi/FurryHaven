<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Database Connection ---
$serverName = "is3-dev.ict.ru.ac.za";
$user = "EzTeck";
$password = "Ezt3ck!25";
$database = "ezteck";  

$conn = new mysqli($serverName, $user, $password, $database);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// --- Analytics ---
$totalAnimals = $conn->query("SELECT COUNT(*) as count FROM animal")->fetch_assoc()['count'];
$adopted = $conn->query("SELECT COUNT(*) as count FROM animal WHERE outtakeType='Adoption'")->fetch_assoc()['count'];
$available = $conn->query("SELECT COUNT(*) as count FROM animal WHERE outtakeType IS NULL")->fetch_assoc()['count'];
$monthlyIntakes = $conn->query("SELECT COUNT(*) as count FROM animal WHERE MONTH(Animal_RescueDate) = MONTH(CURDATE()) AND YEAR(Animal_RescueDate) = YEAR(CURDATE())")->fetch_assoc()['count'];

// --- Monthly Adoptions ---
$currentMonth = date('m');
$currentYear = date('Y');
$monthlyAdoptions = $conn->query("SELECT COUNT(*) as count FROM animal WHERE outtakeType='Adoption' AND MONTH(outtakeDate)=$currentMonth AND YEAR(outtakeDate)=$currentYear")->fetch_assoc()['count'];

$healthy = $conn->query("SELECT COUNT(*) as count FROM animal WHERE Animal_HealthStatus='Healthy'")->fetch_assoc()['count'];
$inTreatment = $conn->query("SELECT COUNT(*) as count FROM animal WHERE Animal_HealthStatus IN ('Sick','Injured','Recovering','Under Observation')")->fetch_assoc()['count'];

// --- Calendar Setup ---
$month = $_GET['month'] ?? $currentMonth;
$year = $_GET['year'] ?? $currentYear;
$month = (int)$month;
$year = (int)$year;

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDay = date("w", strtotime("$year-$month-01"));

$resultEvents = $conn->query("SELECT * FROM events WHERE MONTH(event_date) = $month AND YEAR(event_date) = $year");
$events = [];
while ($row = $resultEvents->fetch_assoc()) $events[$row['event_date']][] = $row;

// --- Registered Animals ---
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? '';
$sqlAnimals = "SELECT * FROM animal WHERE 1=1";
if ($search) $sqlAnimals .= " AND Animal_Name LIKE '%$search%'";
if ($filter) $sqlAnimals .= " AND (Animal_AgeGroup='$filter' OR Animal_Type='$filter')";
$resultAnimals = $conn->query($sqlAnimals);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial; margin:0; }
        /* Sidebar */
        #sidebar { position:fixed; left:0; top:0; width:220px; height:100%; background:#2c3e50; color:white; transition:0.3s; overflow-y:auto; padding-top:20px; }
        #sidebar a { display:block; color:white; padding:12px 20px; text-decoration:none; }
        #sidebar a:hover { background:#34495e; }
        #sidebar .bottom { position:absolute; bottom:0; width:100%; }
        #main { margin-left:220px; padding:20px; transition:0.3s; }
        .toggleBtn { position:fixed; top:10px; left:230px; font-size:20px; cursor:pointer; color:#2c3e50; z-index:1001; }
        table { border-collapse: collapse; width:100%; margin-bottom:20px; }
        th, td { border:1px solid #ddd; padding:8px; vertical-align: top; }
        td { cursor:pointer; position: relative; }
        td:hover { background:#f0f0f0; }
        .event { background:#e0f7fa; margin:2px 0; padding:2px; border-radius:3px; }
        #animalPopup, #popupForm, #overlay { display:none; position:absolute; z-index:1000; background:white; border:1px solid black; padding:15px; }
        #overlay { position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; }
        .calendar-nav { margin-bottom:10px; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div id="sidebar">
    <h3 style="text-align:center;">Dashboard</h3>
    <a href="dashboard.php">Dashboard</a>
    <a href="animals/register.php">Register Animal</a>

    <a href="#">Animal Database</a>
    <a href="#">Reports</a>
    <a href="#">Volunteers</a>
    <a href="#">Adopters</a>
    <a href="#">Donations</a>
    <div class="bottom">
        <a href="#">System Settings</a>
        <a href="#">Staff Profile</a>
        <a href="#">Search Animal</a>
        <a href="#">Help & Support</a>
        <a href="#">Profile</a>
    </div>
</div>

<!-- Toggle Button -->
<span class="toggleBtn" onclick="toggleSidebar()">☰</span>

<!-- Main Content -->
<div id="main">
    <h1>Welcome, <?php echo $_SESSION['first_name'].' '.$_SESSION['last_name']; ?></h1>

    <!-- Analytics -->
    <h2>Quick Analytics</h2>
    <table>
        <tr>
            <td><strong>Total Animals</strong><br><?php echo $totalAnimals; ?></td>
            <td><strong>Available</strong><br><?php echo $available; ?></td>
            <td><strong>Adopted</strong><br><?php echo $adopted; ?></td>
        </tr>
        <tr>
            <td><strong>Monthly Intakes</strong><br><?php echo $monthlyIntakes; ?></td>
            <td><strong>Current Month Adoptions</strong><br><?php echo $monthlyAdoptions; ?></td>
            <td><strong>Healthy</strong><br><?php echo $healthy; ?></td>
            <td><strong>In Treatment</strong><br><?php echo $inTreatment; ?></td>
        </tr>
    </table>

    <!-- Calendar -->
    <h2>Calendar - <?php echo date("F Y", strtotime("$year-$month-01")); ?></h2>
    <div class="calendar-nav">
        <a href="?month=<?php echo ($month==1?12:$month-1); ?>&year=<?php echo ($month==1?$year-1:$year); ?>">Prev</a> |
        <a href="?month=<?php echo ($month==12?1:$month+1); ?>&year=<?php echo ($month==12?$year+1:$year); ?>">Next</a>
    </div>
    <table>
        <tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr>
        <tr>
<?php
for($i=0;$i<$firstDay;$i++) echo "<td></td>";
for($day=1;$day<=$daysInMonth;$day++){
    $dateStr="$year-".str_pad($month,2,"0",STR_PAD_LEFT)."-".str_pad($day,2,"0",STR_PAD_LEFT);
    echo "<td onclick=\"openPopup(this,'$dateStr')\"><b>$day</b><br>";
    if(isset($events[$dateStr])){
        foreach($events[$dateStr] as $event) echo "<div class='event'>📌 ".$event['title']."</div>";
    }
    echo "</td>";
    if(($day+$firstDay)%7==0) echo "</tr><tr>";
}
?>
        </tr>
    </table>

    <!-- Popup for events -->
    <div id="overlay" onclick="closePopup()"></div>
    <div id="popupForm">
        <h3>Add Event</h3>
        <form method="POST" action="save_event.php">
            <input type="hidden" name="event_date" id="eventDate">
            <label>Title:</label><br><input type="text" name="title" required><br><br>
            <label>Details:</label><br><textarea name="details"></textarea><br><br>
            <button type="submit">Save Event</button>
            <button type="button" onclick="closePopup()">Cancel</button>
        </form>
    </div>

    <!-- Registered Animals -->
    <h2>Registered Animals</h2>
     <button onclick="location.href='/animals/register'">+ New</button>
    <form method="GET">
        <input type="text" name="search" placeholder="Search by name..." value="<?php echo $search; ?>">
        <select name="filter">
            <option value="">-- Filter By --</option>
            <option value="Adult" <?php echo $filter=='Adult'?'selected':''; ?>>Adult</option>
            <option value="Juvenile" <?php echo $filter=='Juvenile'?'selected':''; ?>>Juvenile</option>
            <option value="Senior" <?php echo $filter=='Senior'?'selected':''; ?>>Senior</option>
            <option value="Dog" <?php echo $filter=='Dog'?'selected':''; ?>>Dog</option>
            <option value="Cat" <?php echo $filter=='Cat'?'selected':''; ?>>Cat</option>
        </select>
        <button type="submit">Search / Filter</button>
    </form>

    <table>
        <tr>
            <th></th><th>Name</th><th>Adoption Status</th><th>Applications</th><th>Species</th><th>Gender</th><th>Intake Date</th>
        </tr>
        <?php while($row=$resultAnimals->fetch_assoc()): ?>
        <tr onclick="showDetails('<?php echo $row['Animal_ID']; ?>')">
            <td><img src="images/<?php echo $row['Animal_ID']; ?>.jpg" width="50"></td>
            <td><?php echo $row['Animal_Name']; ?></td>
            <td><?php echo $row['outtakeType']?'Adopted':'Available'; ?></td>
            <td><?php echo rand(0,5); ?></td>
            <td><?php echo $row['Animal_Type']; ?></td>
            <td><?php echo $row['Animal_Gender']; ?></td>
            <td><?php echo date("d-M-Y",strtotime($row['Animal_RescueDate'])); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <!-- See More Button -->
    <button onclick="location.href='animaldatabase.php'">See More</button>

    <!-- Animal Popup -->
    <div id="animalPopup">
        <div id="popupContent"></div>
        <button onclick="closeAnimalPopup()">Close</button>
    </div>
</div>

<script>
// Sidebar toggle
function toggleSidebar(){
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    if(sidebar.style.left==='-220px'){
        sidebar.style.left='0';
        main.style.marginLeft='220px';
    } else {
        sidebar.style.left='-220px';
        main.style.marginLeft='0';
    }
}

// Calendar popup
function openPopup(cell,date){
    const rect = cell.getBoundingClientRect();
    const popup = document.getElementById('popupForm');
    popup.style.top=(window.scrollY+rect.top+25)+'px';
    popup.style.left=(rect.left)+'px';
    document.getElementById('eventDate').value=date;
    popup.style.display='block';
    document.getElementById('overlay').style.display='block';
}
function closePopup(){
    document.getElementById('popupForm').style.display='none';
    document.getElementById('overlay').style.display='none';
}

// Animal popup
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



