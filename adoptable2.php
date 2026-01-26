<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require('config/databaseconnection.php');

$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';


if (!isset($_POST['Animal_ID'])) {
    die("No animal selected!");
}

// 1. Sanitize the input
// Use mysqli_real_escape_string to sanitize the string ID
$animal_id = mysqli_real_escape_string($conn, $_POST['Animal_ID']);

// 2. Prepare the SQL statement
// Use a placeholder (?) for the string ID
$query = "SELECT a.*, b.filePath 
          FROM animal a 
          LEFT JOIN (
              SELECT animalID, MIN(filePath) AS filePath
              FROM animalmedia
              GROUP BY animalID
          ) b ON a.Animal_ID = b.animalID 
          WHERE a.isDeleted = 0 AND a.Animal_ID = ?";
          
$stmt = mysqli_prepare($conn, $query);

if ($stmt === false) {
    die("Error preparing statement: " . mysqli_error($conn));
}

// 3. Bind the parameter as a string
// The "s" indicates that the data type is a string
mysqli_stmt_bind_param($stmt, "s", $animal_id);

// 4. Execute the statement
mysqli_stmt_execute($stmt);

// 5. Get the result
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    die("Animal not found.");
}

$row = mysqli_fetch_assoc($result);

// ... rest of your HTML code goes here ...


// 6. Close the statement and connection
mysqli_stmt_close($stmt);
mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <title><?php echo $row['Animal_Name']; ?> - Adopt Me</title>
  <style>
    :root {
        --primary-color: #003366; /* Dark Blue */
        --secondary-color: #FF8C00; /* Orange */
        --background-color: #f8f4e9; /* Light Beige */
        --card-bg-color: #ffffff;
        --text-color: #333;
        --light-text: #f8f8f8;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--background-color);
      margin: 0;
      padding: 40px 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .container {
      max-width: 900px;
      width: 100%;
      background: var(--card-bg-color);
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    @media (min-width: 768px) {
      .container {
        flex-direction: row;
      }
    }

    .image-container {
      flex: 1;
      height: 600px;
      min-width: 40%;
    }

    .animal-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .details {
      flex: 1;
      padding: 30px;
    }

    h1 {
      margin-top: 0;
      color: var(--primary-color);
      font-size: 2.5rem;
      font-weight: 700;
      border-bottom: 3px solid var(--secondary-color);
      padding-bottom: 10px;
      margin-bottom: 20px;
    }

    p {
      font-size: 1rem;
      color: var(--muted-color, #555);
      margin-bottom: 12px;
      line-height: 1.6;
    }

    p strong {
      color: var(--primary-color);
      font-weight: 600;
      min-width: 150px;
      display: inline-block;
    }

    .btn {
      display: inline-block;
      padding: 12px 25px;
      margin-top: 20px;
      background: var(--secondary-color);
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      border: none;
      font-size: 1.1rem;
      font-weight: bold;
      transition: background 0.3s;
      cursor: pointer;
    }

    .btn:hover {
      background: #e07a00;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* --- Start of Header --- */
    .main-header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 75px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 8px 24px;
      z-index: 9999;
      transition: background-color 300ms ease, box-shadow 300ms ease;
      background: #FFF8F0; /* Solid background for this page */
      box-shadow: 0 3px 12px rgba(0,0,0,0.06);
      border-bottom: 1px solid rgba(0,0,0,0.06);
    }

    .main-header .logo img {
      height: 80px;
      display: block;
    }

    .nav-links {
      list-style: none;
      display: flex;
      gap: 180px;
      margin: 0;
      padding: 0;
    }

    .nav-links li {
      position: relative;
    }

    .nav-links a {
      text-decoration: none;
      font-weight: 600;
      color: #18436e;
      font-size: 22px;
      transition: color 0.3s;
    }

    .nav-links > li > a:hover {
      color: #df7100;
      background: none;
    }

    .dropdown-content {
      position: absolute;
      top: 120%;
      left: 0;
      background: #FFF8F0;
      min-width: 180px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 5px 0;
      list-style: none;
      z-index: 1000;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s ease, transform 0.3s ease;
      transform: translateY(-10px);
    }

    .dropdown-content li a {
      display: block;
      padding: 10px 16px;
      font-weight: 500;
      color: #18436e;
      background: none;
      transition: all 0.3s;
    }

    .dropdown-content li a:hover {
      background-color: #df7100;
      color: #fff;
    }

    .nav-links .dropdown:hover .dropdown-content {
      display: block;
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    /* Profile Icon and Dropdown */
    .profile-icon {
      position: relative;
      cursor: pointer;
      display: inline-block;
      margin-right: 40px;
    }

    .profile-icon svg {
      width: 32px;
      height: 32px;
      fill: #18436e;
      transition: all 0.3s ease;
    }

    .profile-dropdown-menu {
      position: absolute;
      top: 50px;
      right: 0;
      width: 280px;
      background-color: #FFF8F0;
      border-radius: 8px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.15);
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.3s ease;
      z-index: 1000;
    }

    .profile-dropdown-menu.active {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .dropdown-sidebar {
        display: flex;
        flex-direction: column;
    }

    .dropdown-menu-logo {
        text-align: center;
        padding: 20px 0;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }

    .dropdown-menu-logo img {
        width: 150px;
    }

    .dropdown-menu-header {
        text-align: center;
        padding: 20px;
        border-bottom: 3px solid #98b06f;
    }

    .dropdown-menu-header h3 {
        margin: 0 0 6px 0;
        font-size: 1.5rem;
        color: #18436e;
    }

    .dropdown-menu-header p {
        margin: 0;
        font-size: 1.2rem;
        color: #df7100;
    }

    .dropdown-menu-content {
        padding: 10px 0;
    }

    .dropdown-menu-option {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        border-radius: 8px;
        margin: 4px 8px;
        color: #18436e;
        font-size: 1rem;
    }

    .dropdown-menu-option i {
        margin-right: 12px;
        font-size: 1.2rem;
        width: 20px;
        text-align: center;
    }

    .dropdown-menu-option:hover {
        background-color: #ff8c00;
        color: white;
        transform: translateX(5px);
        text-decoration: none;
    }

    .dropdown-btn-primary {
        width: calc(100% - 30px);
        margin: 15px;
        padding: 10px;
        border: none;
        border-radius: 5px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
        background-color: #df7100;
        color: white;
    }

    form{
      display: flex;
  justify-content: center;
  margin-top: 20px;

    }
  </style>
</head>
<body>

  <header class="main-header">
    <a href="homepage.php" class="logo">
      <img src="logo.png" alt="Furry Haven Logo">
    </a>
    <nav>
      <ul class="nav-links">
        <li><a href="Aboutus2.html">About Us</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li class="dropdown">
          <a href="#">Get Involved ▾</a>
          <ul class="dropdown-content">
            <li><a href="adoptable.php">Adopt</a></li>
            <li><a href="campaignpage.php">Donate</a></li>
            <li><a href="volunteerpage.php">Volunteer</a></li>
          </ul>
        </li>
      </ul>
    </nav>
    <div class="profile-icon" id="profileIcon">
      <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24">
        <circle cx="12" cy="7" r="5"/>
        <path d="M2 21c0-5.5 4.5-10 10-10s10 4.5 10 10"/>
      </svg>
      <div class="profile-dropdown-menu" id="profileDropdownMenu">
        <?php if (!$isLoggedIn): ?>
          <div class="dropdown-menu-header">
            <h3>Hello</h3>
            <p>Please Sign In!</p>
          </div>
          <div class="dropdown-menu-content">
            <button class="dropdown-btn-primary" onclick="window.location.href='login.php'">Sign In</button>
          </div>
        <?php else: ?>
          <div class="dropdown-sidebar">
            <div class="dropdown-menu-logo">
              <a href="dashboard2.php"><img src="./logo.png" alt="FurryHaven Logo"></a>
            </div>
            <div class="dropdown-menu-header">
              <h3>Hey There,</h3>
              <p><?php echo htmlspecialchars($username); ?></p>
            </div>
            <div class="dropdown-menu-content">
              <a href="my_applications.php" class="dropdown-menu-option"><i class="fas fa-paw"></i><span>My Applications</span></a>
              <a href="my_donations.php" class="dropdown-menu-option"><i class="fas fa-heart"></i><span>My Donations</span></a>
              <a href="userAnimal.php" class="dropdown-menu-option"><i class="fas fa-dog"></i><span>Boarding</span></a>
              <a href="logout.php" class="dropdown-menu-option"><i class="fas fa-sign-out-alt"></i><span>LogOut</span></a>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <div class="container">
    <div class="image-container">
      <img src="./images/animals/<?php echo $row['filePath']; ?>"  class="animal-img">
    </div>

    <div class="details">
      <h1><?php echo $row['Animal_Name']; ?></h1>
      <p><strong>Gender:</strong> <?php echo $row['Animal_Gender']; ?></p>
      <p><strong>Age:</strong> <?php echo $row['Animal_AgeGroup']; ?></p>
      <p><strong>Breed:</strong> <?php echo $row['Animal_Breed'] ?? 'Unknown'; ?></p>
      <p><strong>Vaccination Status:</strong> <?php echo $row['Animal_Vacc_Status'] ?? 'Unknown'; ?></p>
      <p><strong>Sterilisation:</strong> 
    <?php 
        if ($row['IsSpayNeutered'] == 1) {
            echo "Sterlized";
        } elseif ($row['IsSpayNeutered'] == 0) {
            echo "Not Sterlized";
        } else {
            echo "Unknown";
        }
    ?>
</p>
      <p><strong>Current Health:</strong> <?php echo $row['Animal_HealthStatus'] ?? 'Unknown'; ?></p>
      
       
      <form action="adopt.php" method="POST">
        <input type="hidden" name="Animal_ID" value=<?php echo $row['Animal_ID']?>>
        <button class="btn">🐾 Adopt Me</button>
        
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const profileIcon = document.getElementById('profileIcon');
      const dropdownMenu = document.getElementById('profileDropdownMenu');
      
      profileIcon.addEventListener('click', function(event) {
          event.stopPropagation();
          dropdownMenu.classList.toggle('active');
      });
      
      document.addEventListener('click', function(event) {
          if (!profileIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
              dropdownMenu.classList.remove('active');
          }
      });
    });
  </script>
</body>
</html>