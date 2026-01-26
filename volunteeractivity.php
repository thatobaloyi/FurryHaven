<?php
// dashboard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once './models/VolunteerActivity.php';
include_once './core/functions.php';
include_once './notification.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$volunteerActivity = new VolunteerActivity();
$username = $_SESSION["username"];

// Handle delete
if (isset($_POST['del'])) {
    try {
        if (!$volunteerActivity->softDelete($_POST['ActivityID'])) {
            throw new Exception("Cannot delete!");
        } else {
            $_SESSION['notification'] = [
                'message' => "Activity Successfully deleted",
                'type' => 'success'
            ];
            // After deletion, recalculate stats below
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

$activities_result = $volunteerActivity->findByUsername($username);
$activities = [];
while ($row = $activities_result->fetch_assoc()) {
    $activities[] = $row;
}

// Calculate stats
$total_activities = count($activities);
$animals_helped = [];
foreach ($activities as $act) {
    $animals_helped[] = $act['AnimalID'];
}
$unique_animals_helped = count(array_unique($animals_helped));

// Calculate total duration using SQL for accuracy
$sql = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`duration`))) AS total_duration
        FROM `volunteeractivity` WHERE VolunteerID = '$username' AND isDeleted = 0";
$display_hours = $conn->query($sql)->fetch_assoc();

// Get all activities for the table
$ActivityQuery = "SELECT * FROM volunteeractivity WHERE VolunteerID = '$username' AND isDeleted = 0 ORDER BY Date";
$result01 = $conn->query($ActivityQuery);

// Check if volunteer data exists
if ($activities === false) {
    die("Volunteer data not found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="style2.css">
    <style>
     

       * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        } 

        body {
            background-color: #f0f2f5;
            color: #1f3c74;
            background-image: rgba(31, 60, 116, 0.2);
        } 

       .dashboard-container {
            display: flex;
            min-height: 100vh;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            
        }
        
        /* Main Content Area */
     /* .main-content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
            background-color: #ffffff;
        } */
 
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        } 

        .header h1 {
            font-size: 2.2rem;
            color: var(--primary-high);
        } 

        .user-info {
            display: flex;
            align-items: center;
            background-color:  rgba(31, 60, 116, 0.2);
            padding: 8px 15px;
            border-radius: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: flex;
            justify-content: center;

            /* padding-left:28px; */
            /* grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); */
            gap: 25px;
            margin-bottom: 40px;
        } 

        .card2 {
            display: grid;
            /* text-align: center; */
            background-color: white;
            border-radius: 15px;
            padding: 3em;
    
          
            /* padding-left:28px; */
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            /* position: relative; */
            overflow: hidden;
            z-index: 1;
        }

        .card2 i{
            font-size: 2em;
        }
        
        /* .card2::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.2);
            z-index: -1;
            transition: opacity 0.3s ease;
            opacity: 0;
        } */
        
        .card2:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        
        .card2:hover::before {
            opacity: 1;
        }

        .card2-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .card2-header h3 {
            font-size: 1.3rem;
            color: var(--primary-high);
        }

        .card2-header i {
            color: var(--primary-middle);
            font-size: 2rem;
        }

        .stat {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--accent);
            margin-bottom: 5px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .stat-desc {
            font-size: 0.9rem;
            color: #777;
        }

         .footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  padding-top: 1rem;
  text-align: center;
  font-size: 0.9rem;
  opacity: 0.8;
}


footer {
  background-color: #18436e;
  color: #f1f1f1;
  padding: 3rem 5% 1rem;
  font-family: Arial, sans-serif;
}

.footer-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

    .footer-links {
    color: #ffffff; /* makes all text inside white */
}

/* Optional: if you want to style only headings or paragraphs differently */
.footer-links h4 {
    color: #ffffff;
}

.footer-links p {
    color: #ffffff;
}

.footer-links h4 {
  margin-bottom: 1rem;
  font-size: 1.2rem;
  border-bottom: 2px solid #df7100;
  display: inline-block;
  padding-bottom: 5px;
}

.footer-links ul {
  list-style: none;
  padding: 0;
}

.footer-links ul li {
  margin: 0.5rem 0;
}

.footer-links ul li a {
  color: #f1f1f1;
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-links ul li a:hover {
  color: #df7100;
}
   .footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  padding-top: 1rem;
  text-align: center;
  font-size: 0.9rem;
  opacity: 0.8;
}


footer {
  background-color: #18436e;
  color: #f1f1f1;
  padding: 3rem 5% 1rem;
  font-family: Arial, sans-serif;
}

.footer-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

.footer-links h4 {
  margin-bottom: 1rem;
  font-size: 1.2rem;
  border-bottom: 2px solid #df7100;
  display: inline-block;
  padding-bottom: 5px;
}

.footer-links ul {
  list-style: none;
  padding: 0;
}

.footer-links ul li {
  margin: 0.5rem 0;
}

.footer-links ul li a {
  color: #f1f1f1;
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-links ul li a:hover {
  color: #df7100;
}


        /* Activity Section */
        .activity-section {
            background-color: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        } */

        .section-header h2 {
            font-size: 1.8rem;
            color: var(--primary-high);
        } 
        
        .search-container {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
      .search-container input[type="text"] {
            flex-grow: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 25px;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s;
        } 

        .search-container input[type="text"]:focus {
            border-color: var(--primary-middle);
            box-shadow: 0 0 5px rgba(155, 89, 182, 0.5);
        }

    h1 {
  color: #003366;
  text-align: center;
  border-bottom: 4px solid #FF8C00;
  padding-bottom: 0.5rem;
  font-size: 2.0rem;
  font-family: 'Lexend', sans-serif;
}

blockquote {
  font-style: italic;
  text-align: center;
  color: #003366;
  font-size: 1.1rem;
  margin: 1.5rem 0;
}
    </style>
   
</head>

<body>
     <div class="dashboard-container">
        <!-- Main Content -->
        <div class="main-content" id="mainContent">
                  <h1>My Volunteering Activities</h1>
      <blockquote>"Spreading pawsitivity- every minute"</blockquote>

            <!-- Stats Cards -->
            <div class="dashboard-cards">
                <div class="card2">
                    <div class="card-header">
                        <h3 grid-area="hours">Total Hours</h3>
                        <i class="fas fa-clock" grid-area="icon"></i>
                    </div>
                    <div class="stat"><?php echo $display_hours['total_duration']; ?></div>
                    <p class="stat-desc">Total time volunteered</p>
                </div>
                
                <div class="card2">
                    <div class="card-header">
                        <h3>Activities</h3>
                        <i class="fas fa-list-check"></i>
                    </div>
                  <div class="stat"><?php echo $total_activities; ?></div>
                    <p class="stat-desc">Activities participated in</p>
                </div>

                <div class="card2">
                    <div class="card-header">
                        <h3>Animals Helped</h3>
                        <i class="fas fa-paw"></i>
                    </div>
                  <div class="stat"><?php echo $unique_animals_helped; ?></div>
                    <p class="stat-desc">Animals you've assisted</p>
                </div>
            </div>

            <!-- Recent Activities Section -->
            <div class="activity-section">
                <div class="section-header">
                    <h2>Your Volunteer Activities</h2>
                </div>
                
                <form method="GET" class="search-container">
                    <input type="text" name="search" placeholder="Search Activity name"
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <table class="card">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>AnimalID</th>
                            <th>Activity Type</th>
                            <th>Date</th>
                            <th>Duration</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result01->fetch_assoc()): ?>
                            <tr onclick="showDetails('<?php echo htmlspecialchars($row['ActivityID']); ?>')">
                                <td><?php echo htmlspecialchars($row['VolunteerID']); ?></td>
                                <td><?php echo htmlspecialchars($row['AnimalID']); ?></td>
                                <td><?php echo htmlspecialchars($row['ActivityType']); ?></td>
                                <td><?php echo htmlspecialchars(date("d-M-Y", strtotime($row['Date']))); ?></td>
                                <td><?php echo htmlspecialchars($row['Duration']); ?></td>
                                <td>
                                    <form method='POST' action="./volunteeractivity.php" class="delete-form" onsubmit="return confirmDelete(this);">
                                        <input type='hidden' name='ActivityID' value="<?php echo $row['ActivityID'] ?>">
                                        <button type='submit' class="delete-btn" name="del">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
 </div>


<script src="./sidebar2.js"></script>
    <script>
        function showDetails(volunteerID) {
            // Show more details about a volunteer application (custom modal can be implemented)
            console.log('Showing details for application: ' + volunteerID);
        }
        function confirmDelete(form) {
            return confirm("Are you sure you want to delete this activity?");
        }
    </script>
<footer>
  <div class="footer-container">

    <!-- Contact -->
    <div class="footer-links">
      <h4>Stay in touch</h4>
      <p>Tel: 082 770 2667</p>
      <p>Email: info@example.org</p>
    </div>

    <!-- Support -->
    <div class="footer-links">
      <h4>Support</h4>
      <ul>
        <li><a href="#">Report abuse</a></li>
        <li><a href="#">Donate</a></li>
        <li><a href="#">Volunteer</a></li>
        <li><a href="#">Foster an animal</a></li>
        <li><a href="#">Privacy policy</a></li>
      </ul>
    </div>

    <!-- Company -->
    <div class="footer-links">
      <h4>Company</h4>
      <ul>
        <li><a href="#">Adopt a dog</a></li>
        <li><a href="#">Adopt a cat</a></li>
        <li><a href="#">Success stories</a></li>
        <li><a href="#">About us</a></li>
        <li><a href="#">Contact us</a></li>
      </ul>
    </div>

    <!-- Newsletter + Social -->
    <div class="footer-links">
      <h4>Stay connected</h4>
      <form class="newsletter">
        <input type="email" placeholder="Sign up for updates">
        <button type="submit">Subscribe</button>
      </form>

      </div>
    </div>
  </div>

  <!-- Copyright -->
  <div class="footer-bottom">
    <p>© 2025 FurryHaven | Website design & hosting sponsored by Ezteck</p>
  </div>
</footer>


</body>
</html> 