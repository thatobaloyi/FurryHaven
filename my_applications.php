<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/models/AnimalApplication.php';
include_once __DIR__ . '/models/VolunteerApplication.php';

$animalApplication = new AnimalApplication();
$volunteerApplication = new VolunteerApplication();

$username = $_SESSION['username'] ?? '';
$userRole = $_SESSION['user_role'] ?? '';

$adoption_applications = $animalApplication->findApplicationsByUsername($username, 'Adoption');
$foster_applications = $animalApplication->findApplicationsByUsername($username, 'Foster');
$volunteer_applications = $volunteerApplication->findByUsername($username);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications</title>
    <link rel="stylesheet" href="style2.css">
    <style>
        td{
            text-align: center;
        }
        .status-accepted {
            color: green;
            font-weight: bold;
        }
        .status-rejected {
            color: red;
            font-weight: bold;
        }

        /* Back button styles */
     .page-header {
    position: relative;
    text-align: center;
    margin-bottom: 2rem;
}

.page-header h1 {
    color: #18436e;
    margin: 0;
    text-align: center;
    width: 100%;
}

#backButton {
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
}

        .back-btn:hover {
            background-color: #E67E00;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .back-btn:active {
            transform: translateY(0);
        }
        
        .back-icon {
            font-size: 1.2em;
            transition: transform 0.3s ease;
        }
        
        .back-btn:hover .back-icon {
            transform: translateX(-3px);
        }

        .page-header h1 {
            margin: 0;
            color: #18436e;
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

/* Responsive styles */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .page-title-section {
        width: 100%;
        justify-content: space-between;
    }
}
 .action-btn {
            background-color: #18436e;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Lexend', sans-serif;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .action-btn:hover {
            background-color: #0f2c4d;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">

        
        <div class="main-content" id="mainContent">
            <!-- Page Header with Back Button -->
            <div class="page-header">
                <div class="page-title-section">
                    <button id="backButton" class="action-btn">
                        <span class="action-icon">←</span> Back
                    </button>
                    <h1>My Applications</h1>
                </div>
            </div>

            <div class="card">
                <h2>My Adoption Applications</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Application ID</th>
                            <th>Animal ID</th>
                            <th>Application Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $adoption_applications->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['animalappID']); ?></td>
                                <td><?php echo htmlspecialchars($row['animalID']); ?></td>
                                <td><?php echo htmlspecialchars($row['applicationDate']); ?></td>
                                <td class="<?php
                                    switch (strtolower($row['applicationStatus'])) {
                                        case 'accepted':
                                            echo 'status-accepted';
                                            break;
                                        case 'rejected':
                                            echo 'status-rejected';
                                            break;
                                        default:
                                            echo '';
                                    }
                                ?>"><?php echo htmlspecialchars($row['applicationStatus']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if($_SESSION['user_role'] == "Volunteer"): ?>
            <div class="card">
                <h2>My Foster Applications</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Application ID</th>
                            <th>Animal ID</th>
                            <th>Application Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $foster_applications->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['animalappID']); ?></td>
                                <td><?php echo htmlspecialchars($row['animalID']); ?></td>
                                <td><?php echo htmlspecialchars($row['applicationDate']); ?></td>
                                <td class="<?php
                                    switch (strtolower($row['applicationStatus'])) {
                                        case 'accepted':
                                            echo 'status-accepted';
                                            break;
                                        case 'rejected':
                                            echo 'status-rejected';
                                            break;
                                        default:
                                            echo '';
                                    }
                                ?>"><?php echo htmlspecialchars($row['applicationStatus']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <?php endif; ?>


             <?php if($_SESSION['user_role'] == "Volunteer" || $_SESSION['user_role'] == "Guest"): ?>
            <div class="card">
                <h2>My Volunteer Applications</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Application ID</th>
                            <th>Application Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $volunteer_applications->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['volAppID']); ?></td>
                                <td><?php echo htmlspecialchars($row['applicationDate']); ?></td>
                                <td class="<?php
                                    switch (strtolower($row['status'])) {
                                        case 'accepted':
                                            echo 'status-accepted';
                                            break;
                                        case 'rejected':
                                            echo 'status-rejected';
                                            break;
                                        default:
                                            echo '';
                                    }
                                ?>"><?php echo htmlspecialchars($row['status']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php endif;?>
        </div>
    </div>
    
    <script>
        // Back button functionality
        document.getElementById('backButton').addEventListener('click', function() {
            // Check if there's a previous page in history
            if (document.referrer && document.referrer.includes(window.location.hostname)) {
                window.history.back();
            } else {
                // If no history or coming from external site, redirect to a default page
                window.location.href = './index.php'; // Change to your desired default page
            }
        });
    </script>
    
    <script src="sidebar2.js"></script>
    
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