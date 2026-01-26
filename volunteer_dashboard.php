<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
VET DASHBOARD

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vet Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
    }

    /* Sidebar */
    .sidebar {
      width: 250px;
      background: #2C3E50;
      color: #fff;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      padding-top: 20px;
      display: flex;
      flex-direction: column;
    }

    .sidebar h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 22px;
      letter-spacing: 1px;
      border-bottom: 1px solid #34495E;
      padding-bottom: 10px;
    }

    .sidebar a {
      padding: 12px 20px;
      color: #ecf0f1;
      text-decoration: none;
      display: flex;
      align-items: center;
      transition: background 0.3s;
      font-size: 15px;
      cursor: pointer;
    }

    .sidebar a i {
      margin-right: 12px;
      width: 20px;
      text-align: center;
    }

    .sidebar a:hover {
      background: #34495e;
      color: #fff;
    }

    /* Main content */
    .main-content {
      margin-left: 250px;
      padding: 20px;
      flex: 1;
    }

    .content-section {
      display: none;
    }

    .content-section.active {
      display: block;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    table,
    th,
    td {
      border: 1px solid #ddd;
      padding: 8px;
    }

    th {
      background: #34495e;
      color: white;
    }

    #sidebar {
      position: fixed;
      left: 0;
      top: 0;
      width: 220px;
      height: 100%;
      background: #2c3e50;
      color: white;
      transition: 0.3s;
      overflow-y: auto;
      padding-top: 20px;
    }

    #sidebar a {
      display: block;
      color: white;
      padding: 12px 20px;
      text-decoration: none;
    }

    #sidebar a:hover {
      background: #34495e;
    }

    #sidebar .bottom {
      position: absolute;
      bottom: 0;
      width: 100%;
    }

    #main {
      margin-left: 220px;
      padding: 20px;
      transition: 0.3s;
    }

    .toggleBtn {
      position: fixed;
      top: 10px;
      left: 230px;
      font-size: 20px;
      cursor: pointer;
      color: #2c3e50;
      z-index: 1001;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      margin-bottom: 20px;
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 8px;
      vertical-align: top;
    }

    td {
      cursor: pointer;
      position: relative;
    }

    td:hover {
      background: #f0f0f0;
    }

    .event {
      background: #e0f7fa;
      margin: 2px 0;
      padding: 2px;
      border-radius: 3px;
    }

    #animalPopup {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 1000;
      background: white;
      border: 1px solid black;
      padding: 15px;
      width: 80%;
      max-width: 600px;
      max-height: 80vh;
      overflow-y: auto;
    }

    #popupForm {
      display: none;
      position: absolute;
      z-index: 1000;
      background: white;
      border: 1px solid black;
      padding: 15px;
    }

    #overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 999;
    }

    .calendar-nav {
      margin-bottom: 10px;
    }
  </style>
</head>

<body>

  <nav class="sidebar">
    <h2>🐾 Vet Dashboard</h2>
    <a onclick="showSection('Dashboard')"><i class="fas fa-home"></i> Dashboard</a>
    <a onclick="showSection('RegisteredAnimals')"><i class="fas fa-home"></i> Registered Animals</a>
    <a onclick="showSection('Medicalconditions')"><i class="fas fa-home"></i> Medical conditions</a>
    <a onclick="showSection('Alerts')"><i class="fas fa-user"></i>Alerts</a>
    <a onclick="showSection('Analytics')"><i class="fas fa-file-invoice-dollar"></i> Analytics</a>
    <a onclick="showSection('Settings')"><i class="fas fa-file-invoice-dollar"></i> Settings</a>
    <a onclick="showSection('Logout')"><i class="fas fa-boxes"></i>Logout</a>
  </nav>
  <!-- Main content -->
  <section class="main-content">
    <!-- Dashboard -->
 <div id="Dashboard" class="content-section active">
      <h1>Welcome to the Vet Dashboard</h1>
      <p>Quick stats for today:</p>
      <ul>
        <li>Appointments today: <b>6</b></li>
        <li>Total number of Animals : <b>4</b></li>
        <li>Analytics : <b>4</b></li>
        <li>New pets: <b>$500</b></li>
      </ul>
    </div> 


    <!-- AllAnimals-->
    <div id="RegisteredAnimals" class="content-section">
      <h2>Registered Animal</h2>
      <button onclick="location.href='/animals/register'">+ New</button>
      <form method="GET">
        <input type="text" name="search" placeholder="Search by name..." value="<?php echo $search; ?>">
        <select name="filter">
          <option value="">-- Filter By --</option>
          <option value="Adult" <?php echo $filter == 'Adult' ? 'selected' : ''; ?>>Adult</option>
          <option value="Juvenile" <?php echo $filter == 'Juvenile' ? 'selected' : ''; ?>>Juvenile</option>
          <option value="Senior" <?php echo $filter == 'Senior' ? 'selected' : ''; ?>>Senior</option>

        </select>
        <button type="submit">Search / Filter</button>
      </form>

      <table>
        <tr>
          <th></th>
          <th>Name</th>
          <th>Adoption Status</th>
          <th>Applications</th>
          <th>Species</th>
          <th>Gender</th>
          <th>Intake Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr onclick="showDetails('<?php echo $row['Animal_ID']; ?>')">
            <td><img src="images/animals/<?php echo $row['filePath']; ?>" width="50"></td>
            <td><?php echo $row['Animal_Name']; ?></td>
            <td><?php echo $row['outtakeType'] ? 'Adopted' : 'Available'; ?></td>
            <td><?php echo rand(0, 5); ?></td>
            <td><?php echo $row['Animal_Type']; ?></td>
            <td><?php echo $row['Animal_Gender']; ?></td>
            <td><?php echo date("d-M-Y", strtotime($row['Animal_RescueDate'])); ?></td>
          </tr>
        <?php endwhile; ?>
      </table>
    </div>
      <!-- Animal Popup -->
      <div id="animalPopup">
        <div id="popupContent"></div>
        <button onclick="closeAnimalPopup()">Close</button>
      </div>

      <!--Medical conditions-->
     <div id="Medicalconditions" class="content-section">
   <?php
include_once __DIR__ . '/./config/databaseconnection.php';
global $conn;

// CREATE
if (isset($_POST['add'])) {
    $animalID = $_POST['animalID'];
    $procedureType = $_POST['procedureType'];
    $procedureOutcome = $_POST['procedureOutcome'];
    $procedureDate = $_POST['procedureDate'];
    $details = $_POST['details'];

    $sql = "INSERT INTO medicalprocedure 
            (animalID, procedureType, procedureOutcome, procedureDate, details)
            VALUES ('$animalID', '$procedureType', '$procedureOutcome', '$procedureDate', '$details')";
    if($conn->query($sql)){
        echo "<script>alert('Record added successfully');</script>";
    }else{
        echo "<script>alert('Failed to add record');</script>";
    }
}

// READ
$result = $conn->query("SELECT * FROM medicalprocedure ORDER BY procedureDate DESC");
?>

<!-- Button to open modal -->
<button id="openModalBtn">Add Record</button>

<!-- The Modal -->
<div id="recordModal" class="modal">
  <div class="modal-content">
    <span class="closeBtn">&times;</span>
    <h2>Add New Medical Record</h2>
    <form action="" method="POST">
        Animal ID: <input type="text" name="animalID" required><br>

        Procedure Type:
        <select name="procedureType" required>
            <option value="">-- Select --</option>
            <option value="Vaccination">Vaccination</option>
            <option value="Surgery">Surgery</option>
            <option value="Dental">Dental</option>
            <option value="Sterilisation">Sterilisation</option>
            <option value="Check-up">Check-up</option>
        </select><br><br>

        Procedure Outcome:
        <select name="procedureOutcome" required>
            <option value="">-- Select --</option>
            <option value="Successful">Successful</option>
            <option value="Ongoing">Ongoing</option>
            <option value="Failed">Failed</option>
            <option value="Follow-up Required">Follow-up Required</option>
        </select><br><br>

        Procedure Date: <input type="datetime-local" name="procedureDate" required><br>
        Details: <textarea name="details"></textarea><br><br>

        <button type="submit" name="add">Save Record</button>
    </form>
  </div>
</div>

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

<style>
/* Modal styles */
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0; top: 0;
  width: 100%; height: 100%;
  background-color: rgba(0,0,0,0.6);
}

.modal-content {
  background: #fff;
  margin: 10% auto;
  padding: 20px;
  width: 40%;
  border-radius: 8px;
}

.closeBtn {
  float: right;
  font-size: 22px;
  cursor: pointer;
}
</style>

<script>
// Get modal elements
var modal = document.getElementById("recordModal");
var btn = document.getElementById("openModalBtn");
var span = document.getElementsByClassName("closeBtn")[0];

// Open modal
btn.onclick = function() {
  modal.style.display = "block";
}

// Close modal
span.onclick = function() {
  modal.style.display = "none";
}

// Close when clicking outside modal
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>




      </div>

    <!--Alerts-->
       <div id="Alerts" class="content-section">
        <h1>Analyticss</h1>
        <table>
          <tr>
            <th>Invoice ID</th>
            <th>Owner</th>
            <th>Amount</th>
            <th>Status</th>
          </tr>
          <tr>
            <td>Ziyanda</td>
            <td>John Smith</td>
            <td>$80</td>
            <td>Paid</td>
          </tr>
          <tr>
            <td>Elamii</td>
            <td>Sarah Lee</td>
            <td>$120</td>
            <td>Pending</td>
          </tr>
          <tr>
            <td>Karabo</td>
            <td>Mike Brown</td>
            <td>$300</td>
            <td>Paid</td>
          </tr>
        </table>
      </div>

      <!-- Analytics -->
      <div id="Analytics" class="content-section">
        <h1>Analyticss</h1>
        <table>
          <tr>
            <th>Invoice ID</th>
            <th>Owner</th>
            <th>Amount</th>
            <th>Status</th>
          </tr>
          <tr>
            <td>INV1001</td>
            <td>John Smith</td>
            <td>$80</td>
            <td>Paid</td>
          </tr>
          <tr>
            <td>INV1002</td>
            <td>Sarah Lee</td>
            <td>$120</td>
            <td>Pending</td>
          </tr>
          <tr>
            <td>INV1003</td>
            <td>Mike Brown</td>
            <td>$300</td>
            <td>Paid</td>
          </tr>
        </table>
      </div>
      
      <!--Settings-->

<div id="Settings" class="content-section">
  <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Settings</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
    }

    .settings-wrapper {
      margin-top: 40px; /* pushes it slightly down */
      background: #fff;
      border-radius: 12px;
      padding: 30px;
      width: 100%;
      max-width: 700px; /* keeps it narrow and centered */
      box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
      color: #060f17ff;
    }

    .settings-section {
      margin-bottom: 25px;
    }

    h2 {
      margin-bottom: 10px;
      border-bottom: 1px solid #eee;
      padding-bottom: 5px;
      font-size: 18px;
      color: #2c3e50;
    }

    label {
      display: block;
      margin: 8px 0 4px;
      font-weight: bold;
      font-size: 14px;
    }

    input, select, textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }

    .toggle {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 15px;
    }

    button {
      background: #34495e;
      color: #fff;
      padding: 10px 15px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
    }

    button:hover {
      background: #2c3e50;
    }
  </style>
</head>
<body>

  <div class="settings-wrapper">
    <h1>Settings</h1>

    <!-- Profile Settings -->
    <div class="settings-section">
      <h2>Profile Settings</h2>
      <form>
        <label for="username">Username</label>
        <input type="text" id="username" placeholder="Your username">

        <label for="email">Email</label>
        <input type="email" id="email" placeholder="Your email">

        <label for="password">Change Password</label>
        <input type="password" id="password" placeholder="New password">

        <button type="submit">Save Profile</button>
      </form>
    </div>

    <!-- Preferences -->
    <div class="settings-section">
      <h2>Preferences</h2>
      <form>
        <label for="theme">Theme</label>
        <select id="theme">
          <option>Light</option>
          <option>Dark</option>
        </select>


        <label for="language">Language</label>
        <select id="language">
          <option>English</option>
          <option>French</option>
          <option>Spanish</option>
        </select>

        <button type="submit">Save Preferences</button>
      </form>
    </div>

  
  </div>

</body>
</html>

        </div> 

      <!-- Logout -->
      <div id="Logout" class="content-section">
       
<head>
 
  <style>
   

    .logout-con {
      text-align: center;
      background: #fff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 100%;
    }

  

    a:hover {
      background: #2c3e50;
    }
  </style>
</head>
<body>

  <div class="logout-con">
    <h5>👋 You’ve Logged Out</h5>
    <p>Thank you for using the Vet Dashboard. See you again soon!</p>
    <a href="login.php">Back to Login</a>
  </div>
</div>
 

  <script>
    function showSection(sectionId) {
      document.querySelectorAll('.content-section').forEach(sec => {
        sec.classList.remove('active');
      });
      document.getElementById(sectionId).classList.add('active');
    }

    function showDetails(animalID) {
      fetch('?details=' + animalID)
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

</body>

</html>
