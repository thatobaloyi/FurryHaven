<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once './models/Campaign.php';

$campaign = new Campaign();

$raised = 67;

$result = $campaign->findAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pawsitively Making a Difference.</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7fafc;
            color: #4b3d30;
            overflow-x: hidden;
        }

        .hero {
            background-color: #1f3c74;
            color: white;
            padding: 4rem 1rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 10%, transparent 10.1%);
            background-size: 20px 20px;
            animation: background-pan 60s linear infinite;
        }

        @keyframes background-pan {
            from {
                background-position: 0 0;
            }

            to {
                background-position: -200% 200%;
            }
        }

        .section-container {
            max-width: 1200px;
        }

        .card {
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: #1f3c74;
        }

        .fundraising-card {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
            :root {
            --primary-color: #003366; /* Dark Blue */
            --secondary-color: #FF8C00; /* Orange */
            --background-color: #f8f4e9; /* Light Beige */
            --light-text: #f8f8f8; /* Off-White */
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7fafc;
            color: #4b3d30;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            line-height: 1.6;
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

        /* END OF NAV LINKS STYING  */

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

    </style>
</head>

<body class="antialiased">

    
    <!-- Header -->
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
                            <p><?php echo htmlspecialchars($username); ?></p>
                        </div>

                        <!-- Menu Options -->
                        <div class="dropdown-menu-content">
                            <a href="my_applications.php" class="dropdown-menu-option">
                                <i class="fas fa-paw"></i>
                                <span>My Applications</span>
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

    <div class="hero">
        <div class="relative z-10 p-8 rounded-xl bg-black bg-opacity-30 mx-auto max-w-4xl">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold mb-4 animate-fade-in-down">
                Every Paw, Every Heart, Deserves a Home.
            </h1>
            <p class="text-lg sm:text-xl md:text-2xl font-light mb-8">
                Join us in creating a Haven of hope for animals in need.
            </p>
            <a href="donate.php" class="bg-white text-orange-500 font-bold py-3 px-8 rounded-full shadow-lg hover:bg-gray-100 transition duration-300 transform hover:scale-105">
                Help a Friend Today
            </a>
        </div>
    </div>

    <div class="py-12 bg-gray-50">
        <div class="container mx-auto px-4 section-container">
            <h2 class="text-2xl sm:text-3xl font-bold mb-8 text-center text-orange-500">
                Fundraising Campaigns
            </h2>
            <div id="campaigns-container" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="fundraising-card flex flex-col items-center text-center">
                        <h3 class="text-xl font-bold mb-2 text-gray-800"><?php echo $row['CampaignName'];?></h3>
                        <p class="text-gray-600 mb-4"><?php echo $row['CampaignDescription'];?></p>
                        <p class="text-lg font-semibold mb-4">
                            <span class="text-orange-600"><?php echo $row['amountRaised'] ?></span> / <span class="text-gray-600"><?php echo $row['TargetAmount'];?></span>
                        </p>

                        <?php $n = (float)$row['amountRaised'];
                                $d =  (float)$row['TargetAmount'];
                                $percentage = ($n/$d)*100;
                        ?>
                        <div class="w-full bg-gray-300 rounded-full h-4 relative overflow-hidden mb-2"> 
                            <div class="bg-orange-500 h-4 rounded-full transition-all duration-700 ease-in-out" style="width:<?php echo $percentage?>%"></div>
                        </div> 
                        <p class="text-sm text-gray-700 font-medium"><?php echo $percentage?>%</p> 
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <div class="py-16 bg-white">
        <div class="container mx-auto px-4 section-container">
            <h2 class="text-3xl sm:text-4xl font-bold text-center mb-12">
                How Your Support Makes a Difference
            </h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 text-center">
                <div class="card p-6 rounded-xl">
                    <img src="images/NelsonMandela.jpg" alt="An icon representing animal shelter and care." class="mx-auto mb-4 rounded-xl">
                    <h3 class="text-xl font-bold mb-2">Nelson Mandela Day of service</h3>
                    <p class="text-gray-600">Honour Madiba's legacy by spending time with our animals, cleaning kennels, playing with donkeys, or donating wishlist items. Every minute makes an impact!</p>
                </div>
                <div class="card p-6 rounded-xl">
                    <img src="images/GolfDay.jpg" alt="An icon representing medical treatment for animals." class="mx-auto mb-4 rounded-xl">
                    <h3 class="text-xl font-bold mb-2"> "Purr-fect" Golf Day Fundraiser!</h3>
                    <p class="text-gray-600"> Swing into action for a cause! Join our Golf Day, proudly presented by MB Life / Investments. R2500 Purr Four Ball. Hole sponsorships available.</p>
                </div>
                <div class="card p-6 rounded-xl">
                    <img src="images/Knight.jpg" alt="An icon representing finding forever homes for animals." class="mx-auto mb-4 rounded-xl">
                    <h3 class="text-xl font-bold mb-2">Karaoke Night</h3>
                    <p class="text-gray-600">Sing your heart out for our furry friends! Join us for a night of fun, music, a photo booth, food, and drinks. Tickets R50.</p>
                </div>
                <div class="card p-6 rounded-xl">
                    <img src="images/OpenGarden.jpg" alt="An icon representing spreading compassion and love." class="mx-auto mb-4 rounded-xl">
                    <h3 class="text-xl font-bold mb-2">Open Gardens</h3>
                    <p class="text-gray-600">Explore stunning local gardens and support the SPCA! R100 for all gardens ticket. Interact with local beauty for a beautiful cause.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="py-16 bg-red-50">
        <div class="container mx-auto px-4 section-container">
            <h2 class="text-3xl sm:text-4xl font-bold text-center mb-12">
                Ways You Can Help
            </h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="card p-6 rounded-xl text-center">
                    <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-2xl font-bold">1</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Donate</h3>
                    <p class="text-gray-600">Every donation, big or small, helps us provide essential resources and care for our animals.</p>
                </div>
                <div class="card p-6 rounded-xl text-center">
                    <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-2xl font-bold">2</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Volunteer</h3>
                    <p class="text-gray-600">Your time and effort are invaluable. Help with daily tasks, events, and animal care.</p>
                </div>
                <div class="card p-6 rounded-xl text-center">
                    <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-2xl font-bold">3</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Adopt or Foster</h3>
                    <p class="text-gray-600">Open your heart and home to an animal in need and give them a second chance at life.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="join" class="py-16 bg-orange-500 text-white text-center">
        <h2 class="text-3xl sm:text-4xl font-bold mb-4">
            Join Our Mission of Compassion.
        </h2>
        <p class="text-lg sm:text-xl mb-8">
            Together, we can ensure a loving future for every animal.
        </p>
        <a href="volunteering.php" class="bg-white text-orange-500 font-bold py-3 px-8 rounded-full shadow-lg hover:bg-gray-100 transition duration-300 transform hover:scale-105">
            Get Involved Now
        </a>
    </div>

    <script>
        function renderCampaigns() {
            const container = document.getElementById('campaigns-container');
            let htmlContent = '';

            campaigns.forEach(campaign => {
                const percentage = Math.min((campaign.raised / campaign.goal) * 100, 100).toFixed(0);
                htmlContent += `
                <div class="fundraising-card flex flex-col items-center text-center">
                    <h3 class="text-xl font-bold mb-2 text-gray-800">${campaign.name}</h3>
                    <p class="text-gray-600 mb-4">${campaign.description}</p>
                    <p class="text-lg font-semibold mb-4">
                        <span class="text-orange-600">R${campaign.raised.toLocaleString()}</span> / <span class="text-gray-600">R${campaign.goal.toLocaleString()}</span>
                    </p>
                    <div class="w-full bg-gray-300 rounded-full h-4 relative overflow-hidden mb-2">
                        <div class="bg-orange-500 h-4 rounded-full transition-all duration-700 ease-in-out" style="width: ${percentage}%;"></div>
                    </div>
                    <p class="text-sm text-gray-700 font-medium">${percentage}% of the goal</p>
                </div>
            `;
            });

            container.innerHTML = htmlContent;
        }

        // window.onload = renderCampaigns;
    </script>

</body>

</html>