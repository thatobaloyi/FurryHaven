<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once './models/Campaign.php';

$campaign = new Campaign();
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
/* === Base Styles === */
body {
  font-family: 'Inter', sans-serif;
  background-color: #f7fafc;
  color: #4b3d30;
  overflow-x: hidden;
  margin: 0;
  padding: 0;
  line-height: 1.6;
}

/* === Hero Section === */
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
  from { background-position: 0 0; }
  to { background-position: -200% 200%; }
}

/* === Header === */
.main-header {
  position: fixed;
  top: 0; left: 0; right: 0;
  height: 75px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  z-index: 9999;
  background: rgba(255, 248, 240, 0.0);
  transition: all 0.3s ease;
}
.main-header .logo img {
  height: 80px;
}
.nav-links {
  list-style: none;
  display: flex;
  gap: 180px;
  margin: 0;
  padding: 0;
}
.nav-links a {
  text-decoration: none;
  font-weight: 600;
  color: #18436e;
  font-size: 22px;
  transition: color 0.3s;
}
.nav-links a:hover {
  color: #df7100;
}

/* === Dropdowns === */
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
  transform: translateY(-10px);
  transition: all 0.3s ease;
}
.dropdown-content li a {
  display: block;
  padding: 10px 16px;
  font-weight: 500;
  color: #18436e;
}
.dropdown-content li a:hover {
  background-color: #df7100;
  color: #fff;
}
.nav-links .dropdown:hover .dropdown-content {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

/* === Profile Dropdown === */
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

/* === Card Base === */
.card {
  background-color: white;
  border-radius: 1rem;
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 20px rgba(0, 0, 0, 0.12);
}

/* === IMPROVED FUNDRAISING CARD STYLING === */
.fundraising-card {
  background: linear-gradient(to bottom right, #ffffff, #fffaf5);
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
  transition: all 0.35s ease;
  position: relative;
  overflow: hidden;
}
.fundraising-card::before {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at top right, rgba(255, 140, 0, 0.12), transparent 60%);
  opacity: 0;
  transition: opacity 0.35s ease;
}
.fundraising-card:hover::before {
  opacity: 1;
}
.fundraising-card:hover {
  transform: translateY(-6px) scale(1.02);
  box-shadow: 0 16px 30px rgba(0, 0, 0, 0.12);
}
.fundraising-card h3 {
  font-size: 1.4rem;
  color: #1f3c74;
  margin-bottom: 0.75rem;
  font-weight: 700;
}
.fundraising-card p {
  color: #5a5247;
  font-size: 0.95rem;
  line-height: 1.6;
}
.fundraising-card .progress-container {
  width: 100%;
  background-color: #e5e7eb;
  border-radius: 9999px;
  height: 0.75rem;
  overflow: hidden;
  margin: 1rem 0 0.5rem;
  position: relative;
}
.fundraising-card .progress-fill {
  height: 100%;
  background: linear-gradient(to right, #ff8c00, #ffb347);
  border-radius: 9999px;
  box-shadow: 0 0 8px rgba(255, 140, 0, 0.5);
  transition: width 0.8s ease-in-out;
}
.fundraising-card .donate-btn {
  background: #ff8c00;
  color: white;
  font-weight: 600;
  padding: 0.6rem 1.5rem;
  border-radius: 9999px;
  margin-top: 1rem;
  transition: all 0.3s ease;
}
.fundraising-card .donate-btn:hover {
  background: #ff9f1c;
  transform: scale(1.05);
  box-shadow: 0 0 10px rgba(255, 140, 0, 0.5);
}

/* === Section Titles === */
h2 {
  font-weight: 700;
  letter-spacing: -0.5px;
}

/* === Subtle Animations === */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
.fundraising-card {
  animation: fadeInUp 0.7s ease both;
}

/* === “Ways You Can Help” Styling === */
.card .w-12 {
  box-shadow: 0 0 8px rgba(239, 68, 68, 0.5);
}

/* === Footer Join Section === */
#join {
  background: linear-gradient(to right, #ff8c00, #ffb347);
  color: white;
}
#join a:hover {
  background-color: #fff7ed;
}

/* === Misc === */
.text-orange-500 {
  color: #ff8c00 !important;
}
</style>
</head>

<body class="antialiased">

<!-- ==== HEADER ==== -->
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
  </div>
</header>

<!-- ==== HERO ==== -->
<div class="hero">
  <div class="relative z-10 p-8 rounded-xl bg-black bg-opacity-30 mx-auto max-w-4xl">
    <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold mb-4">
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

<!-- ==== FUNDRAISING CAMPAIGNS ==== -->
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
            <span class="text-orange-600"><?php echo $row['amountRaised'] ?></span> / 
            <span class="text-gray-600"><?php echo $row['TargetAmount'];?></span>
          </p>
          <?php 
            $n = (float)$row['amountRaised'];
            $d = (float)$row['TargetAmount'];
            $percentage = ($n/$d)*100;
          ?>
          <div class="w-full bg-gray-300 rounded-full h-4 relative overflow-hidden mb-2">
            <div class="bg-orange-500 h-4 rounded-full transition-all duration-700 ease-in-out" style="width:<?php echo $percentage?>%"></div>
          </div>
          <p class="text-sm text-gray-700 font-medium"><?php echo $percentage?>%</p>
          <button class="donate-btn mt-4">Donate Now</button>
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
</body>
</html>
