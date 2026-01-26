<?php
//session_start();

// --- Database Connection ---
require('config/databaseconnection.php');



include_once __DIR__ . '/models/Donation.php';
include_once __DIR__ . '/models/Donor.php';



// dashboard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not authenticated
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}



$donor = new Donor();
$username = $_SESSION["username"];
//
// Get donor data
$result = $donor->findAll();
$donorData = $result->fetch_assoc();

// Check if donor data exists
if (!$donorData) {
    die("Donor data not found");
}

// Get volunteer activities
//$activitiesResult = $volunteerActivity->getActivityType($username);



/*$currentUserID = $_SESSION['username']; // Assuming userID is stored in session upon login
$notification = new Notification();
$unreadNotifications = $notification->getUnread($currentUserID);
$unreadCount = $unreadNotifications->num_rows;
*/



?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Donor Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    body {
      display: flex;
      margin: 0;
      font-family: Arial, sans-serif;
    }

    .sidebar {
      width: 220px;
      background: #333;
      color: white;
      padding: 20px;
      min-height: 100vh;
    }

    .sidebar h2 {
      margin-bottom: 20px;
    }

    .sidebar a {
      display: block;
      color: white;
      padding: 10px;
      text-decoration: none;
      cursor: pointer;
      border-radius: 5px;
      margin-bottom: 5px;
      transition: background 0.3s;
    }

    .sidebar a:hover {
      background: #444;
    }

    /* Highlight for active link */
    .sidebar a.active-link {
      background: #555;
      font-weight: bold;
    }

    .main-content {
      flex: 1;
      padding: 20px;
    }

    .content-section {
      display: none;
    }

    .content-section.active {
      display: block;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <nav class="sidebar">
    <h2>🐾 Donor Dashboard</h2>
    <a onclick="showSection('Dashboard', this)" class="active-link"><i class="fas fa-home"></i> Dashboard</a>
    <a onclick="showSection('Campaigns', this)"><i class="fas fa-bullhorn"></i> Campaigns</a>
    <a onclick="showSection('Leaderboard', this)"><i class="fas fa-trophy"></i> Leaderboard</a>
    <a onclick="showSection('History', this)"><i class="fas fa-history"></i> History</a>
    <a onclick="showSection('Settings', this)"><i class="fas fa-cog"></i> Settings</a>
    <a onclick="showSection('Logout', this)"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </nav>

  <!-- Main content -->
  <section class="main-content">

    <!-- Dashboard -->
    <div id="Dashboard" class="content-section active">
      <h1>Welcome to the Donor Dashboard</h1>
      <p>Here you can get an overview of everything.</p>
    </div>

    <!-- Campaigns -->
    <div id="Campaigns" class="content-section">
      <h1>Campaigns</h1>
      <p>View active campaigns and support animals in need 🐕🐾.</p>
    </div>

    <!-- Leaderboard -->
    <div id="Leaderboard" class="content-section">
      <h1>Leaderboard</h1>
      <p>See top donors and their amazing contributions 🌟.</p>
    </div>

    <!-- History -->

    <div id="History" class="content-section">
      <h1>Donation History</h1>
      <hr>
      <p><?php echo ['DonationAmount'] ?> to furryhaven: Campaign Animal life<p>
      <p> Date: 9 December 2025 <p> Download Receipt
      <hr>
      <p>$50 to furryhaven: Campaign Animal Initiative<p>
      <p> Date: 9 December 2025 <p> Download Receipt
    </div>

    <!-- Settings -->
    <div id="Settings" class="content-section">
      <h1>Settings</h1>
      <p>Update your preferences and account info ⚙️.</p>
    </div>

    <!-- Logout -->
    <div id="Logout" class="content-section">
      <h1>Logout</h1>
      <p>You have been logged out successfully. 👋</p>
    </div>

  </section>

  <script>
    function showSection(sectionId, element) {
      // Hide all sections
      document.querySelectorAll('.content-section').forEach(sec => {
        sec.classList.remove('active');
      });

      // Show selected section
      document.getElementById(sectionId).classList.add('active');

      // Remove highlight from all sidebar links
      document.querySelectorAll('.sidebar a').forEach(link => {
        link.classList.remove('active-link');
      });

      // Highlight clicked link
      element.classList.add('active-link');
    }
  </script>

</body>
</html>
