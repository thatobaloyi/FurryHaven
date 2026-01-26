<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require('config/databaseconnection.php');

require_once "./core/functions.php";

// Get all animals from the database
$query = "SELECT a.*, b.filePath
          FROM animal a
          LEFT JOIN (
              SELECT animalID, MIN(filePath) AS filePath
              FROM animalmedia
              GROUP BY animalID
          ) b ON a.Animal_ID = b.animalID
          WHERE a.isDeleted = 0";
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
    <title>FurryHaven - Home</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        :root {
            --primary-color: #003366;
            --secondary-color: #FF8C00;
            --background-color: #f8f4e9;
            --text-color: #333333;
            --light-text: #f8f8f8;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        header {
            background-color: var(--primary-color);
            color: var(--light-text);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative; /* Needed for absolute positioning of sub-menu */
        }

        .logo {
            font-size: 1.8rem; /* Slightly larger */
            font-weight: bold;
            color: var(--light-text);
            text-decoration: none;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 2rem;
        }

        nav a {
            color: var(--light-text);
            text-decoration: none;
            font-weight: bold;
            padding: 0.5rem;
            display: block;
            transition: color 0.3s ease;
        }

        nav ul li {
            position: relative;
        }

        nav a:hover {
            color: var(--secondary-color);
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: white;
            min-width: 160px;
            border-radius: 6px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            z-index: 999;
        }

        .dropdown-content li a {
            color: var(--text-color);
            padding: 0.75rem 1rem;
        }

        .dropdown-content li a:hover {
            background-color: var(--background-color);
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* User profile dropdown */
        .user-profile {
            position: relative;
            margin-left: 30px; /* Space from nav links */
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid var(--secondary-color); /* Highlight border */
        }

        .sub-menu-wrap {
            position: absolute;
            top: 100%;
            right: 0; /* Align to the right edge of the user-profile div */
            width: 320px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease-in-out;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
            z-index: 1001; /* Ensure it's above other content */
            margin-top: 10px; /* Space below the profile picture */
        }

        .sub-menu-wrap.open-menu {
            max-height: 400px; /* Adjust as needed */
        }

        .sub-menu {
            padding: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .user-info img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
            border: none; /* Remove border from here as it's on the profile icon */
        }

        .user-info h3 {
            font-weight: 500;
            margin: 0;
        }

        .sub-menu hr {
            border: 0;
            height: 1px;
            width: 100%;
            background: #ccc;
            margin: 15px 0 10px;
        }

        .sub-menu-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #525252;
            margin: 12px 0;
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .sub-menu-link:hover {
            background-color: var(--background-color);
        }

        .sub-menu-link img {
            width: 40px;
            background: #e5e5e5;
            border-radius: 50%;
            padding: 8px;
            margin-right: 15px;
        }

        .sub-menu-link span {
            font-size: 22px;
            margin-left: auto; /* Pushes the arrow to the right */
            transition: transform 0.3s ease;
        }

        .sub-menu-link:hover span {
            transform: translateX(5px);
        }

        .sub-menu-link:hover p {
            font-weight: 600;
        }

        /* Hamburger Menu for smaller screens */
        .hamburger {
            display: none; /* Hidden by default */
            font-size: 2rem;
            color: var(--light-text);
            background: none;
            border: none;
            cursor: pointer;
            margin-left: 20px; /* Space from user profile */
        }

        /* Sidebar Styling */
        .sidebar {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            right: 0;
            height: 100%;
            width: 250px;
            background-color: var(--primary-color);
            color: var(--light-text);
            box-shadow: -4px 0 12px rgba(0,0,0,0.2);
            padding-top: 60px;
            z-index: 1000;
            overflow-y: auto; /* Enable scrolling if content exceeds height */
        }

        .sidebar a {
            display: block;
            padding: 15px 20px;
            color: var(--light-text);
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s, padding-left 0.3s;
        }

        .sidebar a:hover {
            background-color: var(--secondary-color);
            padding-left: 30px;
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: var(--light-text);
            cursor: pointer;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            nav ul {
                display: none; /* Hide main nav on small screens */
            }

            .hamburger {
                display: block; /* Show hamburger */
            }

            .user-profile {
                margin-left: 15px; /* Reduce margin */
            }

            .sidebar {
                display: block; /* Show sidebar when toggled by JS */
            }

            /* Adjust header for mobile */
            header {
                padding: 1rem;
            }
        }

        /* Removed the redundant CSS from the original code */
        .herox { display: none; } /* Hide the redundant herox element */
        .carousel-inner .item img { /* This was for an older Bootstrap version, using .carousel-item img now */
            height: 300px; /* Adjust as needed */
            object-fit: cover;
            object-position: center;
        }
        .banner-img {
            height: 60vh;
            object-fit: cover;
        }
        .carousel-caption {
            top: 50%;
            transform: translateY(-50%);
            bottom: auto;
            text-shadow: 1px 1px 5px rgba(0,0,0,0.6);
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .card {
            border: none;
            width: 100%;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.2s;
            text-decoration: none;
            color: #000;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .card-info {
            padding: 15px;
            text-align: center;
        }
        .card-info h3 {
            margin: 5px 0;
            font-size: 18px;
        }
        .card-info p {
            margin: 0;
            color: #555;
        }

        /* Added styles for sections that were present but not fully styled */
        .opportunities {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            text-align: center;
            margin-bottom: 2rem; /* Add some spacing */
        }
        .opportunity-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            text-decoration: none;
            color: var(--text-color);
            display: flex;
            flex-direction: column;
        }
        .opportunity-card:hover {
            transform: translateY(-5px);
        }
        .opportunity-card h3 {
            padding: 1rem;
            margin: 0;
            background-color: var(--primary-color);
            color: var(--light-text);
            font-size: 1.2rem;
            text-align: center;
        }
        .opportunity-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            display: block;
        }

        .hope-section {
            display: grid;
            grid-template-columns: 1fr 1fr; /* image left, text right */
            gap: 2rem;
            align-items: center;
            margin-bottom: 2rem;
        }
        .hope-image img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .hope-content {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .r-of-hope {
            display: flex;
            justify-content: space-around;
            gap: 2rem;
            text-align: center;
        }
        .r-of-hope div {
            flex: 1;
        }
        .r-of-hope .icon {
            font-size: 3rem;
            color: var(--secondary-color);
        }
        .animal-gallery {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }
        .animal-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
            padding-bottom: 1rem;
        }
        .animal-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .breaking-the-chain {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: center;
        }
        .breaking-the-chain img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .champions {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            align-items: start;
        }
        .donor-leaderboard {
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .donor-leaderboard table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        .donor-leaderboard th, .donor-leaderboard td {
            text-align: left;
            padding: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        .donor-leaderboard th {
            font-weight: bold;
        }
        .call-to-action {
            text-align: center;
            background-color: #e6e6e6;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 3rem;
        }
        .btn {
            display: inline-block;
            background-color: var(--secondary-color);
            color: white;
            padding: 0.7rem 1.5rem;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #e67e00;
        }
        .btn-secondary {
            background-color: #777;
        }
        .btn-secondary:hover {
            background-color: #555;
        }
        footer {
            background-color: var(--primary-color);
            color: var(--light-text);
            padding: 2rem 5%;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
        }
        footer h4 {
            margin-top: 0;
            color: var(--secondary-color);
        }
        footer ul {
            list-style: none;
            padding: 0;
        }
        footer ul li a {
            color: var(--light-text);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        footer ul li a:hover {
            color: var(--secondary-color);
        }
        .footer-logo {
            grid-column: span 1;
        }
        .contact-info {
            grid-column: span 2;
            text-align: right;
        }
        .copyright {
            grid-column: span 4;
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 1rem;
            margin-top: 1rem;
        }
        .social-icons a {
            color: var(--light-text);
            font-size: 1.5rem;
            margin-left: 1rem;
        }

        @media (max-width: 768px) {
            header {
                flex-direction: row; /* Keep in a row for hamburger */
                padding: 1rem;
            }

            nav ul {
                display: none; /* Hide main nav on small screens */
            }

            .hamburger {
                display: block; /* Show hamburger */
            }

            .user-profile {
                margin-left: 15px; /* Adjust spacing */
            }

            .sidebar {
                display: block; /* Show sidebar when toggled by JS */
                right: 0; /* Ensure it slides from the right */
                transform: translateX(100%); /* Hidden off-screen */
                transition: transform 0.3s ease-in-out;
            }

            .sidebar.open {
                transform: translateX(0); /* Slide in */
            }

            .r-of-hope, .breaking-the-chain, .champions, footer {
                grid-template-columns: 1fr;
            }

            .footer-logo, .contact-info, .copyright {
                grid-column: span 1;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="homepage.php" class="logo">🐾 FurryHaven</a>

        <nav>
            <ul>
                <li><a href="aboutus.html">About Us</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Get Involved ▾</a>
                    <ul class="dropdown-content">
                        <li><a href="adoptable.php">Adopt</a></li>
                        <li><a href="adoptable.php">Foster</a></li>
                        <li><a href="donate.php">Donate</a></li>
                        <li><a href="volunteering.php">Volunteer</a></li>
                    </ul>
                </li>
                <li><a href="#">Search</a></li>
            </ul>
        </nav>

        <div class="user-profile">
            <img src="images/cHJpdmF0ZS9sci9pbWFnZXMvd2Vic2l0ZS8yMDIzLTAxL3JtNjA5LXNvbGlkaWNvbi13LTAwMi1wLnBuZw.webp" alt="User Avatar" onclick="toggleUserMenu()">
            <div class="sub-menu-wrap" id="subMenu">
                <div class="sub-menu">
                    <div class="user-info">
                        <img src="images/cHJpdmF0ZS9sci9pbWFnZXMvd2Vic2l0ZS8yMDIzLTAxL3JtNjA5LXNvbGlkaWNvbi13LTAwMi1wLnBuZw.webp" alt="User Avatar">
                        <h3>Clinton Nz</h3>
                    </div>
                    <hr>
                    <a href="login.php" class="sub-menu-link">
                        <img src="images/profile-icon.png" alt="Profile Icon"> <p>Edit Profile</p>
                        <span>&gt;</span>
                    </a>
                    <a href="#" class="sub-menu-link">
                        <img src="images/settings-icon.png" alt="Settings Icon"> <p>Settings</p>
                        <span>&gt;</span>
                    </a>
                    <a href="#" class="sub-menu-link">
                        <img src="images/donation-icon.png" alt="Donations Icon"> <p>Donations</p>
                        <span>&gt;</span>
                    </a>
                    <a href="#" class="sub-menu-link">
                        <img src="images/boarding-icon.png" alt="Boarding Icon"> <p>Boarding</p>
                        <span>&gt;</span>
                    </a>
                    <hr>
                    <a href="#" class="sub-menu-link">
                        <img src="images/logout-icon.png" alt="Logout Icon"> <p>Logout</p>
                        <span>&gt;</span>
                    </a>
                </div>
            </div>
        </div>

        <button id="hamburgerBtn" class="hamburger" onclick="toggleSidebar()">&#9776;</button>
    </header>

    <div id="mySidebar" class="sidebar">
        <button class="close-btn" onclick="toggleSidebar()">&times;</button>
        <a href="aboutus.html">About Us</a>
        <a href="contact.html">Contact</a>
        <a href="adoptable.php">Adopt</a>
        <a href="adoptable.php">Foster</a>
        <a href="donate.php">Donate</a>
        <a href="volunteering.php">Volunteer</a>
        <a href="#">Search</a>
        <?php if (isset($_SESSION['username'])): ?>
            <a href="#">My Profile</a>
            <a href="#">Account Settings</a>
            <a href="logout.php">Logout</a> <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>

    <main>
        <div id="furryhavenBanner" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="./bannerdog.jpg" class="d-block w-100 banner-img" alt="Cat 1">
                    <div class="carousel-caption text-start">
                        <h1>Welcome to FurryHaven!</h1>
                        <p>Making a difference together.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="images/jamie-street-Lax3oDPJuYY-unsplash.jpg" class="d-block w-100 banner-img" alt="Cat 2">
                    <div class="carousel-caption text-start">
                        <h1>Adopt, Don’t Shop</h1>
                        <p>Give a loving home to animals in need.</p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#furryhavenBanner" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#furryhavenBanner" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <section>
            <main>
                <section class="opportunities">
                    <a href="adoptable.php" class="opportunity-card">
                        <img src="anoir-chafik-2_3c4dIFYFU-unsplash.jpg" alt="Adopt">
                        <h3>Adopt</h3>
                    </a>
                    <a href="donate.php" class="opportunity-card">
                        <img src="anoir-chafik-2_3c4dIFYFU-unsplash.jpg" alt="Donate">
                        <h3>Donate</h3>
                    </a>
                    <a href="volunteering.php" class="opportunity-card">
                        <img src="anoir-chafik-2_3c4dIFYFU-unsplash.jpg" alt="Volunteer">
                        <h3>Volunteer</h3>
                    </a>
                </section>

                <section class="hope-section">
                    <div class="hope-image">
                        <img src="anoir-chafik-2_3c4dIFYFU-unsplash.jpg" alt="Hopeful animals">
                    </div>
                    <div class="hope-content">
                        <div class="intro">
                            <h2>The 3R's of Hope</h2>
                            <p>Spay, neuter, and rescue together can help end animal homelessness and suffering.</p>
                        </div>
                        <div class="r-of-hope">
                            <div>
                                <span class="icon">🐶</span>
                                <h4>Rescued</h4>
                            </div>
                            <div>
                                <span class="icon">💉</span>
                                <h4>Rehabilitated</h4>
                            </div>
                            <div>
                                <span class="icon">🏡</span>
                                <h4>Rehomed</h4>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <h2>Meet Your Furry Friends</h2>
                    <div class="grid">
                        <?php
                        $count = 0;
                        while ($row = mysqli_fetch_assoc($result)) {
                            if ($count >= 4) break; // Limit to 4 animals
                        ?>
                            <form action="/adoptable2.php" method="POST">
                                <input type="hidden" name="Animal_ID" value="<?php echo $row['Animal_ID']; ?>">
                                <button type="submit" class="card">
                                    <img src="./images/animals/<?php echo htmlspecialchars($row['filePath']); ?>" alt="<?php echo htmlspecialchars($row['Animal_Name']); ?>">
                                    <div class="card-info">
                                        <h3><?php echo htmlspecialchars($row['Animal_Name']); ?></h3>
                                        <p><?php echo htmlspecialchars($row['Animal_AgeGroup']); ?></p>
                                    </div>
                                </button>
                            </form>
                        <?php
                            $count++;
                        }
                        ?>
                    </div>
                    <div style="text-align: center; margin-top: 2rem;">
                        <a href="adoptable.php" class="btn btn-secondary">View More</a>
                    </div>
                </section>

                <section>
                    <h2>Don't have a home away from home? Come Board with us</h2>
                    <div class="boarding">
                        <div>
                            <p>Amazing stays for up to 2 weeks come and leave your loved one with us</p>
                        </div>
                        <div>
                            <img src="anoir-chafik-2_3c4dIFYFU-unsplash.jpg" alt="Boarding" width="50%">
                        </div>
                    </div>
                    <button id="boarding-button" class="btn">Boarding</button> <br><br>
                </section>

                <script>
                    document.getElementById("boarding-button").addEventListener("click", function () {
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            alert("Please log in or register to access the boarding form.");
                            window.location.href = 'login.php?redirect=boarding.php';
                        <?php else: ?>
                            window.location.href = 'boarding.php';
                        <?php endif; ?>
                    });
                </script>

                <section>
                    <h2>Together, We Can Break the Chain of Cruelty</h2>
                    <div class="breaking-the-chain">
                        <div>
                            <p>Every animal deserves to be loved and cared for. Our mission is to provide a safe haven and a second chance for abandoned and abused animals. With your help, we can create a world where every paw has a home.</p>
                            <a href="cruelty.php" class="btn">Report Cruelty</a>
                        </div>
                        <div>
                            <img src="anoir-chafik-2_3c4dIFYFU-unsplash.jpg" alt="Chain of cruelty">
                        </div>
                    </div>
                </section>

                <section class="champions">
                    <div>
                        <h2>FurryHaven Heroes: Champions of Compassion</h2>
                        <p>Recognizing the hearts behind the help.</p>
                        <p>Every donation, no matter the size, helps us provide food, shelter, and medical care for the animals in our care. We are incredibly grateful to our generous donors who make our work possible.</p>
                    </div>
                    <div class="donor-leaderboard">
                        <h3>Donor Leaderboard</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>J. Hurlow</td><td>R600</td></tr>
                                <tr><td>B. Jacobs</td><td>R500</td></tr>
                                <tr><td>S. Ford</td><td>R400</td></tr>
                                <tr><td>A. Smith</td><td>R250</td></tr>
                                <tr><td>E. Motsamai</td><td>R150</td></tr>
                                <tr><td>M. Mokoena</td><td>R100</td></tr>
                                <tr><td>Anonymous</td><td>R50</td></tr>
                            </tbody>
                        </table>
                        <a href="donate.php" class="btn">Donate Now</a>
                    </div>
                </section>

                <section>
                    <h2>Join us in creating a Haven of hope for animals in need.</h2>
                    <div class="breaking-the-chain">
                        <div>
                            <p>Our campaign is dedicated to giving every animal the love, care, and safety they deserve. By supporting us, you’re helping provide food, shelter, medical care, and the chance for abandoned and mistreated animals to find a forever home. Together, we can make a difference ,one paw at a time.</p>
                            <a href="campaignpage.php" class="btn">Campaign</a>
                        </div>
                        <div>
                            <img src="images.jpg" alt="Campaign image">
                        </div>
                    </div>
                </section>

                <section class="call-to-action">
                    <h3>Be part of the journey and support the cause.</h3>
                    <p>Together, we can make a difference in the lives of animals.</p>
                    <a href="volunteering.php" class="btn">Volunteer Now</a>
                </section>
            </main>
        </section>

        <footer>
            <div class="footer-logo">
                <a href="#" class="logo">🐾 FurryHaven</a>
                <p>&copy; 2024 FurryHaven. All Rights Reserved.</p>
            </div>
            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Get Involved</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Support</h4>
                <ul>
                    <li><a href="#">Donate</a></li>
                    <li><a href="#">Volunteer</a></li>
                    <li><a href="#">Adopt</a></li>
                </ul>
            </div>
            <div class="contact-info">
                <p>We'd love to hear from you.</p>
                <p><a href="mailto:SPCA@FurryHaven.org" style="color: var(--secondary-color);">SPCA@FurryHaven.org</a></p>
                <div class="social-icons">
                    <a href="#">Facebook</a>
                    <a href="#">Twitter</a>
                    <a href="#">Instagram</a>
                </div>
            </div>
            <div class="copyright">
                <p>All images used are for illustrative purposes.</p>
            </div>
        </footer>

        <script>
            let subMenu = document.getElementById("subMenu");
            let sidebar = document.getElementById("mySidebar");
            let hamburgerBtn = document.getElementById("hamburgerBtn");

            function toggleUserMenu() {
                subMenu.classList.toggle("open-menu");
            }

            function toggleSidebar() {
                if (sidebar.style.display === "block") {
                    sidebar.style.display = "none";
                } else {
                    sidebar.style.display = "block";
                }
                // Alternatively, use a class for smoother transitions:
                // sidebar.classList.toggle("open");
            }

            // Close user menu if clicking outside of it
            document.addEventListener('click', function(event) {
                const isClickInsideUserMenu = subMenu.contains(event.target);
                const isClickOnUserIcon = event.target.classList.contains('user-profile') || event.target.tagName === 'IMG'; // Check if the click is on the user icon itself
                
                if (!isClickInsideUserMenu && !isClickOnUserIcon) {
                    subMenu.classList.remove("open-menu");
                }
            });
            
            // Close sidebar if clicking outside of it
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnHamburger = event.target === hamburgerBtn;

                if (!isClickInsideSidebar && !isClickOnHamburger && sidebar.style.display === "block") {
                    sidebar.style.display = "none";
                    // sidebar.classList.remove("open");
                }
            });
        </script>
</body>
</html>