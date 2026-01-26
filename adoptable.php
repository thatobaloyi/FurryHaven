<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once './notification.php';
require('config/databaseconnection.php');
require_once "./core/functions.php";

// Check if user is logged in
$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

// Get all animals from the database
$query = "SELECT a.*, b.filePath 
          FROM animal a 
          LEFT JOIN (
              SELECT animalID, MIN(filePath) AS filePath
              FROM animalmedia
              GROUP BY animalID
          ) b ON a.Animal_ID = b.animalID 
          WHERE a.isDeleted = 0 AND (a.Animal_Vacc_Status = 'Vaccinated' OR a.IsSpayNeutered = 1)";
$result = mysqli_query($conn, $query);

if (!$result) {
  die("Database query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <title>Adoptable Animals</title>
  <style>
    /* button{
     border: none; 
    } */
    :root {
        --primary-color: #003366; /* Dark Blue */
        --secondary-color: #FF8C00; /* Orange */
        --background-color: #f8f4e9; /* Light Beige */
        --light-text: #f8f8f8; /* Off-White */
    }
    
    h1 {
      text-align: center;
      color: var(--primary-color);
      font-family: var(--font-family, Arial, Helvetica, sans-serif);
      font-size: 2.2rem;
      font-weight: bold;
      margin-top: 20px;
      margin-bottom: 0;
      font-size: clamp(36px, 5vw, 60px);
      font-weight: 800;
      display: block;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--background-color);
      color: var(--text-color);
      margin: 0;
      padding: 0;
      line-height: 1.6;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 30px;
      padding: 20px;
      max-width: 1200px;
      margin-left: auto;
      margin-right: auto;
    }

    .card {
      width: 100%;
      border: none;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      transition: transform 0.2s;
      text-decoration: none;
      color: #000;
      cursor: pointer;
    }

    .card:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }

    .card-info {
      padding: 15px;
      text-align: center;
      background: white;
    }

    .card-info h3 {
      margin: 5px 0;
      font-size: 18px;
      color: var(--primary-color);
    }

    .card-info p {
      margin: 0;
      color: #555;
    }

    .modal {
      display: none; 
      position: fixed; 
      z-index: 1000;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
    }
    
    .modal-content {
      background: #fff;
      margin: 5% auto;
      padding: 20px;
      border-radius: 12px;
      width: 80%;
      max-width: 800px;
      max-height: 90vh;
      overflow-y: auto;
      position: relative;
    }
    
    .close {
      position: absolute;
      top: 15px;
      right: 25px;
      font-size: 35px;
      font-weight: bold;
      cursor: pointer;
      color: #aaa;
    }
    
    .close:hover {
      color: #000;
    }

    .hero {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
      background-color: #fff;
      border-radius: 12px;
      margin: 100px auto 20px;
      max-width: 1200px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .hero-text {
      flex: 1;
      min-width: 300px;
      padding: 20px;
    }

    .hero-text h1 {
      font-size: clamp(32px, 5vw, 48px);
      margin-bottom: 10px;
      font-weight: 800;
    }

    .hero-text .highlight {
      color: var(--secondary-color); /* orange */
    }

    .hero-text .subtitle {
      font-weight: bold;
      margin-bottom: 15px;
      color: var(--primary-color);
    }

    .hero-text p {
      margin-bottom: 12px;
      color: #333;
    }

    .hero-text .note {
      font-size: 14px;
      color: #777;
    }

    .hero-buttons {
      margin-top: 20px;
    }

    .hero-buttons .btn {
      display: inline-block;
      margin-right: 15px;
      padding: 12px 20px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: bold;
      transition: 0.2s;
    }

    .btn.primary {
      background: var(--primary-color);
      color: #fff;
    }

    .btn.primary:hover {
      background: #002244;
    }

    .btn.secondary {
      background: var(--secondary-color);
      color: #fff;
    }

    .btn.secondary:hover {
      background: #e07a00;
    }

    .hero-image {
      flex: 1;
      min-width: 280px;
      padding: 20px;
      text-align: center;
      position: relative;
    }

    .hero-image img {
      width: 100%;
      max-width: 400px;
      border-radius: 12px;
    }

    .hero-image .image-caption {
      margin-top: 10px;
      background: var(--secondary-color);
      color: #fff;
      padding: 10px 15px;
      border-radius: 8px;
      display: inline-block;
      font-weight: bold;
    }

    /* START OF PROFILE STYLING */
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

    .profile-icon.active svg {
      fill: #df7100; 
      transform: scale(1.1);
    }

    .profile-dropdown-menu {
      position: absolute;
      top: 40px; 
      right: 0;
      width: 250px;
      background-color: #FFF8F0; 
      border-radius: 8px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.15);
      padding: 10px 0;
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

    .dropdown-menu-header {
      padding: 15px;
      text-align: center;
      color: #18436e;
    }

    .dropdown-menu-header h3 {
      margin-bottom: 6px;
    }

    .dropdown-menu-header p {
      margin: 0;
      font-size: 1.5rem;
      color: #18436e;
    }

    .dropdown-menu-content {
      padding: 10px 15px;
    }

    .dropdown-btn {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 5px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
    }

    .dropdown-btn-primary {
      background-color: #df7100; 
      color: white;
    }

    .dropdown-btn-primary:hover {
      background-color: #ff8c00; 
    }

    /* END OF PROFILE STYLING */

    /* Logo */
    .dropdown-menu-logo {
        text-align: center;
        padding: 20px 0;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    .dropdown-menu-logo img {
        width: 150px;
        height: auto;
        object-fit: contain;
    }

    /* User Info */
    .dropdown-menu-header {
        text-align: center;
        padding: 20px;
        border-bottom: 3px solid #98b06f;
        color: #fff;
    }

    .dropdown-menu-header h3 {
        margin: 0 0 6px 0;
        font-size: 3.5rem;
        color:#18436e;
    }

    .dropdown-menu-header p {
        margin: 0;
        font-size: 1.5rem;
        color: #df7100;
    }

    .dropdown-menu-content {
        display: flex;
        flex-direction: column;
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
        font-size: 1.5rem;
    }

    .dropdown-menu-option i {
        margin-right: 12px;
        font-size: 1.2rem;
        width: 20px;
        text-align: center;
    }

    .dropdown-menu-option:hover {
        background-color: #ff8c00;
        transform: translateX(5px);
    }

    .dropdown-sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .dropdown-sidebar::-webkit-scrollbar-thumb {
        background-color: rgba(255,140,0,0.7);
        border-radius: 3px;
    }

    .dropdown-menu-option:hover {
        text-decoration: none; 
        background-color: #ff8c00; 
        transform: translateX(5px); 
    }
    
    

    /* Adoptable.php sidebar fix */
    /* Sidebar container */
    .dropdown-sidebar {
        position: fixed;
        top: 0;
        right: 0;
        width: 280px; /* A more standard sidebar width */
        height: auto;
        background-color: #FFF8F0;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        box-shadow: 4px 0 12px rgba(0,0,0,0.3);
        z-index: 1000;
    }
  </style>
      <style>
/*General Styles and CSS Variables*/
        :root {
            --primary-color: #003366; /* Dark Blue */
            --secondary-color: #FF8C00; /* Orange */
            --background-color: #f8f4e9; /* Light Beige */
            --light-text: #f8f8f8; /* Off-White */
        }

     /* --- Start of Header --- */
.main-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 75px;                 /* <-- shorter (was 72px) */
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 24px;            /* <-- reduced padding */
  z-index: 9999;
  transition: background-color 300ms ease, box-shadow 300ms ease,
              backdrop-filter 300ms ease, border-bottom 300ms ease, color 200ms;
  background: rgba(255, 248, 240, 0.0);
  border-bottom: none;
  margin: 0;
  padding: 0;
  width: 100%;


}

  /* logo */
    .main-header .logo img {
      height: 80px;
      display: block;
    }

  /* Transparent state */
    .main-header.transparent {
      background: rgba(255, 248, 240, 0.45);
      -webkit-backdrop-filter: blur(10px) saturate(1.05);
      backdrop-filter: blur(10px) saturate(1.05);
      border-bottom: 1px solid rgba(0,0,0,0.03);
      box-shadow: none;
    }
  

/* START OF NAV LINKS STYING  */
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
  font-size: 18px;
  transition: color 0.3s;
   font-size: 22px;
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

.main-header.solid {
  background: #FFF8F0;
  box-shadow: 0 3px 12px rgba(0,0,0,0.06);
  border-bottom: 1px solid rgba(0,0,0,0.06);
}

.main-header.solid .nav-links a {
  color: #18436e; 
}

    .main-header.transparent .nav-links a:hover {
      color: #ffefcf;
      text-decoration: none;
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

/* END OF NAV LINKS STYING  */
</style>
</head>

<body>

  <header class="main-header transparent">
    <a href="homepage.php" class="logo">
      <img src="logo.png" alt="Furry Haven Logo">
    </a>
    <nav>
      <ul class="nav-links">
        <li><a href="Aboutus2.php">About Us</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li class="dropdown">
          <a href="#">Get Involved ▾</a>
          <ul class="dropdown-content">
            <li><a href="adoptable.php">Adopt</a></li>
            <li><a href="my_donations.php">Donate</a></li>
            <li><a href="campaignpage.php">Campaigns</a></li>
            <li><a href="userAnimal.php">Boarding</a></li>
            <li><a href="volunteerpage.php">Volunteer</a></li>
          </ul>
        </li>
      </ul>
    </nav>

    <!-- Profile Icon -->
    <div class="profile-icon" id="profileIcon">
      <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24">
        <circle cx="12" cy="7" r="5"/>
        <path d="M2 21c0-5.5 4.5-10 10-10s10 4.5 10 10"/>
      </svg>

      <!-- Profile Dropdown Menu -->
      <div class="profile-dropdown-menu" id="profileDropdownMenu">
        <?php if (!$isLoggedIn): ?>
          <!-- Sign In Button (shown when not authenticated) -->
          <div class="dropdown-menu-header">
            <h3>Hello</h3>
            <p>Please Sign In!</p>
          </div>
          <div class="dropdown-menu-content">
            <button class="dropdown-btn dropdown-btn-primary" onclick="window.location.href='login.php'">Sign In</button>

          </div>
        <?php else: ?>
          <!-- User Info and Menu Options (shown when authenticated) -->
          <div class="dropdown-sidebar">

    <!-- Logo on top -->
    <div class="dropdown-menu-logo">
        <a href="dashboard2.php">
            <img src="./logo.png" alt="FurryHaven Logo">
        </a>
    </div>

    <!-- User Info -->
    <div class="dropdown-menu-header">
        <h3>Hey There,</h3>
        <p> <?php echo htmlspecialchars($username); ?></p>
    </div>

    <!-- Menu Options -->
    <div class="dropdown-menu-content">
        <a href="my_applications.php" class="dropdown-menu-option">
            <i class="fas fa-paw"></i>
            <span>My Applications</span>
        </a>
        <a href="volunteeractivity.php" class="dropdown-menu-option">
            <i class="fas fa-hands-helping"></i>
            <span>Volunteer Activities</span>
        </a>
        <a href="my_donations.php" class="dropdown-menu-option">
            <i class="fas fa-heart"></i>
            <span>My Donations</span>
        </a>
        <a href="userAnimal.php" class="dropdown-menu-option">
            <i class="fas fa-heart"></i>
            <span>Boarding</span>
        </a>
        <a href="logout.php" class="dropdown-menu-option">
            <i class="fas fa-sign-out-alt"></i>
            <span>LogOut</span>
        </a>
    </div>
</div>

        <?php endif; ?>
        </div>
    </div>
</header>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileIcon = document.getElementById('profileIcon');
    const dropdownMenu = document.getElementById('profileDropdownMenu');
    
    // Toggle dropdown menu
    profileIcon.addEventListener('click', function(event) {
        event.stopPropagation();
        dropdownMenu.classList.toggle('active');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!profileIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove('active');
        }
    });
    
    // Header scroll effect
    const header = document.querySelector('.main-header');
    
    function updateHeaderState() {
        if (window.scrollY > 40) {
            header.classList.add('solid');
            header.classList.remove('transparent');
        } else {
            header.classList.remove('solid');
            header.classList.add('transparent');
        }
    }
    
    // Add null check for header to prevent errors on pages without it
    if (header) {
        updateHeaderState();
        window.addEventListener('scroll', updateHeaderState, { passive: true });
    }
    
    // This is the key change: Check for login status and reload the page
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('login') && urlParams.get('login') === 'success') {
        // Remove the parameter from the URL to avoid continuous reloads
        window.history.replaceState({}, document.title, window.location.pathname);
        // Force a page reload to ensure the PHP script runs again
        window.location.reload();
    }
});
</script>


<section class="hero">
    <div class="hero-text">
      <h1>Adopt a <span class="highlight">Friend</span></h1>
      <p class="subtitle">
        We have THE friend for you! Have a look and adopt below.
      </p>
      <p>
        Adopting a rescued animal won't only change your life in all the best ways, 
        it also frees up a space for another one to be rescued. 
        Most furry friends find their way here have been rescued from abuse, neglect, and abandonment. 
      </p>
      <p>
        All the friends below are ready for adoption and waiting for YOU to give them a new forever home.
      </p>
      <p class="note">
        Please note: adoptions are only available within the Eastern Cape.
      </p>
      <div class="hero-buttons">
        <a href="contact.html" class="btn secondary">💌 Get in Touch</a>
      </div>
    </div>
    <div class="hero-image">
      <img src="./images/—Pngtree—isolated cat on white background_9158356.png" alt="Adoptable cat">
      <div class="image-caption">Your purrfect furry friend awaits you.</div>
    </div>
  </section>

  <div class="grid">
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <form action="adoptable2.php" method="POST" style="margin: 0;">
        <input type="hidden" name="Animal_ID" value="<?php echo $row['Animal_ID']; ?>">
        <button type="submit" class="card">
          <img src="./images/animals/<?php echo $row['filePath']; ?>" alt="<?php echo $row['Animal_Name']; ?>">
          <div class="card-info">
            <h3><?php echo $row['Animal_Name']; ?></h3>
            <p><?php echo $row['Animal_Gender']; ?></p>
            <p><?php echo $row['Animal_Breed']; ?></p>
            <p><?php echo $row['Animal_AgeGroup']; ?></p>
          </div>
        </button>
      </form>
    <?php } ?>
  </div>
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
  <div id="animalModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <div id="modalBody">
        <!-- animal details will be loaded here -->
      </div>
    </div>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", function() {    
    // Header scroll effect
    const header = document.querySelector('.main-header');
    
    function updateHeaderState() {
      if (window.scrollY > 40) {
        header.classList.add('solid');
        header.classList.remove('transparent');
      } else {
        header.classList.remove('solid');
        header.classList.add('transparent');
      }
    }
    
    // Add null check for header to prevent errors on pages without it
    if (header) {
      updateHeaderState();
      window.addEventListener('scroll', updateHeaderState, { passive: true });
    }
    
    // Profile dropdown functionality
    const profileIcon = document.getElementById('profileIcon');
    const dropdownMenu = document.getElementById('profileDropdownMenu');
    
    // Toggle dropdown menu
    profileIcon.addEventListener('click', function(event) {
      event.stopPropagation();
      dropdownMenu.classList.toggle('active');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      if (!profileIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.classList.remove('active');
      }
    });
  });

  document.addEventListener('DOMContentLoaded', function() {
    const profileIcon = document.getElementById('profileIcon');
    const dropdownMenu = document.getElementById('profileDropdownMenu');
    
    // Toggle dropdown menu
    profileIcon.addEventListener('click', function(event) {
        event.stopPropagation();
        dropdownMenu.classList.toggle('active');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!profileIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove('active');
        }
    });
    
    // Header scroll effect
    const header = document.querySelector('.main-header');
    
    function updateHeaderState() {
        if (window.scrollY > 40) {
            header.classList.add('solid');
            header.classList.remove('transparent');
        } else {
            header.classList.remove('solid');
            header.classList.add('transparent');
        }
    }
    
    // Add null check for header to prevent errors on pages without it
    if (header) {
        updateHeaderState();
        window.addEventListener('scroll', updateHeaderState, { passive: true });
    }
});
  </script>

</body>
</html>