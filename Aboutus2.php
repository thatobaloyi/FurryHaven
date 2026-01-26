<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
:root {
   --primary-color: #003366;
   --secondary-color: #FF8C00;
   --background-color: #f8f4e9;
   --light-text: #f8f8f8;
   --text-color:#003366;
}

main h1, main p, .what-we-do p, .team-member p {
    color: var(--text-color);
}

h1, h2, h3, h4, h5, h6, p, ul, li, a {
   color: var(--text-color);
}

    body {
  margin: 0;
  padding: 0;
}

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--background-color);
      margin: 0;
      padding: 0;
      line-height: 1.6;
      overflow-x: hidden;
    }

/* START OF HEADER STYLING */
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

/* Logo */
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

/* Nav Links */
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

/* Dropdown */
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

/* Solid state when scrolled */
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
/* END OF HEADER STYLING */

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

/* Sidebar for logged in users */
.dropdown-sidebar {
  width: 100%;
  background-color: #FFF8F0;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

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
  text-decoration: none;
}
/* END OF PROFILE STYLING */

/* Main Content */
main {
  padding-top: 120px;
}

#furryhavenBanner {
  margin-top: 0;
}

.what-we-do .content-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 2rem;
  margin-bottom: 2rem;
}

.what-we-do .content-row.reverse {
  flex-direction: row-reverse;
}

.what-we-do .text-block {
  flex: 1;
}

.what-we-do .image-block {
  flex: 0 0 500px;
}

.what-we-do .image-block img {
  width: 100%;
  height: auto;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Responsive stack on smaller screens */
@media (max-width: 768px) {
  .what-we-do .content-row {
    flex-direction: column;
    text-align: center;
  }

  .what-we-do .image-block {
    flex: 1;
    max-width: 500px;
    margin: 0 auto;
  }
  
  .nav-links {
    gap: 80px;
  }
}

.what-we-do {
  padding: 3rem 5%;
  background-color: var(--background-color);
}
    
.what-we-do h1 {
  margin-top: 2rem;
  color: var(--primary-color);
  text-align: center;
}

.what-we-do .banner {
  width: 100%;
  height: auto;
  margin: 1rem 0 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.team-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 30px;
  margin-top: 40px;
  text-align: center;
}

.team-member {
  background-color: white;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  transition: transform 0.3s ease;
}

.team-member:hover {
  transform: translateY(-5px);
}

.team-member img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 15px;
  border: 3px solid var(--secondary-color);
  box-shadow: none;
}

.footer-links {
    color: #ffffff;
}

.footer-links h4 {
    color: #ffffff;
}

.footer-links p {
    color: #ffffff;
}

.team-member h3 {
  margin: 0 0 5px 0;
  color: var(--primary-color);
  font-size: 1.3em;
}

.team-member p {
  margin: 0;
  color: var(--text-color);
  font-size: 0.95em;
}

.call-to-action-banner {
  background-color: var(--primary-color);
  color: var(--light-text);
  text-align: center;
  padding: 40px 20px;
  margin: 40px 0;
  border-radius: 8px;
}

.call-to-action-banner h2 {
  color: var(--light-text);
  font-size: 2em;
  margin-bottom: 10px;
}

.call-to-action-banner p {
  font-size: 1.1em;
  margin-bottom: 20px;
  max-width: 700px;
  margin-left: auto;
  margin-right: auto;
}
    
ul {
    list-style: none;
    padding-left: 0;
}

li {
    margin-bottom: 10px;
    position: relative;
    padding-left: 25px;
}

li::before {
    color: var(--secondary-color);
    font-weight: bold;
    display: inline-block;
    width: 1em;
    margin-left: -1em;
    position: absolute;
    left: 0;
}

.quote-box {
    background-color: var(--primary-color);
    color: var(--light-text);
    border-radius: 12px;
    text-align: center;
    font-style: italic;
    max-width: 800px;
    margin: 0 auto 40px auto;
    padding: 30px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}
    
.quote-box .quote-text {
    font-size: 1.8em;
    line-height: 1.5;
    margin-bottom: 15px;
}

.quote-box .quote-author {
    font-size: 1.2em;
    font-weight: bold;
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

button {
  display: inline-block;
  background-color: var(--secondary-color);
  color: white;
  padding: 0.7rem 1.5rem;
  border: none;
  border-radius: 4px;
  margin: 0.5rem;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #e67e00;
}

.content-row .text-block h1 {
  font-size: 3rem;
  color: var(--primary-color);
  font-weight: bold;
  text-align: left;
  position: relative;
  margin-bottom: 20px;
}

.content-row .text-block h1::after {
  content: "";
  position: absolute;
  width: 80%;
  height: 3px;
  background-color: var(--secondary-color);
  bottom: -10px;
  left: 0;
}

/* Carousel styling */
.carousel-item {
  position: relative;
  width: 100%;
  height: 100vh;
  overflow: hidden;
}

.carousel-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center center;
}

.carousel-caption {
  padding-top: 24px;
}

.carousel-control-prev,
.carousel-control-next {
  top: 40%;
  transform: translateY(-50%);
}

.carousel-button-container {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 5;
}

.carousel-caption {
  position: absolute;
  top: 50%;
  max-width: 46%;
  z-index: 3;
  transform: translateY(-50%);
  font-family: 'Segoe UI', sans-serif;
}

.caption-slide1,
.caption-slide2 {
  left: 200px;
  text-align: left;
  max-width: 600px;
}

.caption-slide1 h2,
.caption-slide2 h2 {
  font-size: clamp(36px, 5vw, 60px);
  font-weight: 800;
}

.caption-slide1 p,
.caption-slide2 p {
  font-size: clamp(18px, 2.5vw, 24px);
}

.caption-slide1 h2 {
  border-bottom: 4px solid #FF8C00;
  display: inline-block;
  padding-bottom: 5px;
}

.caption-slide1 h2 {
    color: rgb(253, 249, 249);
}

.caption-slide1 p {
    color: #ffffff;
}
  </style>
</head>
<body>
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
    <?php
    // Check if user is logged in (you would need to implement proper session handling)
    $isLoggedIn = false; // Default to false - you would check session here
    $username = ''; // Default empty - you would get from session
    
    // Example of how you might check login status
    // session_start();
    // $isLoggedIn = isset($_SESSION['username']);
    // $username = $isLoggedIn ? $_SESSION['username'] : '';
    ?>
    
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
    
    // Check for login status and reload the page
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('login') && urlParams.get('login') === 'success') {
        // Remove the parameter from the URL to avoid continuous reloads
        window.history.replaceState({}, document.title, window.location.pathname);
        // Force a page reload to ensure the PHP script runs again
        window.location.reload();
    }
  });
});
</script>

  <!-- Carousel -->
<div id="furryhavenBanner" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
  <div class="carousel-inner">
    <!-- Slide 1 -->
    <div class="carousel-item active">
      <img src="aboutusimage.jpeg" class="d-block w-100" style="height:700px; object-fit:cover; object-position: 70% 50%;" alt="Dog 1">
      <div class="carousel-caption caption-slide1">
      </div>
    </div>
  </div>
</div>

  <main>
   <section class="what-we-do">
  <div class="quote-box">
    <div class="quote-text">"Together we give every animal a second chance at a loving home."</div>
    <div class="quote-author">- FurryHaven</div>
  </div>

  <div class="content-row">
    <div class="text-block">
      <h1>Who We Are</h1>
      <p>We are <strong>SPCA Grahamstown</strong>, a dedicated organisation committed to the protection, welfare, and dignity of all animals. Our goal is to ensure that every animal, regardless of species or background, receives compassion, care, and the legal protection they deserve. As a registered public benefit organisation, we're authorised to issue Section 18A Tax Receipts for your generous donations, making your contribution even more impactful!</p>
    </div>
    <div class="image-block">
      <img src="aboutus3.jpeg" alt="Smiling woman with a dog">
    </div>
  </div>

  <div class="content-row reverse">
    <div class="text-block">
      <h1>Our Vision & Mission</h1>
      <h3>Our Vision</h3>
      <p>Our Mission is to support the operations and mission of the Society for the Prevention of Cruelty to Animals (SPCA). The system is designed to enhance the efficiency of SPCA facilities by managing animal welfare, preventing cruelty, promoting adoption, and engaging with the community to foster responsible pet ownership.</p>
      <h3>Our Mission</h3>
      <ul>
        <li>To protect animals from abuse, neglect, and cruelty.</li>
        <li>To promote and embrace all animals through volunteering.</li>
        <li>To educate and engage the public about responsible animal care.</li>
        <li>To provide rescue, rehabilitation, and, where possible, rehoming of animals in need.</li>
      </ul>
    </div>
    <div class="image-block">
      <img src="aboutus5.jpeg" alt="Animals being cared for">
    </div>
  </div>

  <div class="content-row">
    <div class="text-block">
      <h1>What We Do</h1>
      <p>Our work involves:</p>
      <ul>
        <li><strong>Investigation & Enforcement:</strong> We respond to cruelty complaints, conduct inspections of farms, shelters, transport, and holding facilities to ensure compliance with standards.</li>
        <li><strong>Rescue & Rehabilitation:</strong> We rescue animals in distress, provide medical care, shelter, and where possible, prepare them for adoption.</li>
        <li><strong>Wildlife Welfare:</strong> Monitoring and protecting wildlife in captivity and in the wild, ensuring ethical treatment, humane housing, rescue, and rehabilitation when needed.</li>
        <li><strong>Public Advocacy & Legal Action:</strong> We work to strengthen animal protection laws, support prosecutions of offences, and where necessary, pursue legal action to ensure animals are defended under the law.</li>
        <li><strong>Education & Awareness:</strong> Running campaigns, workshops, community outreach, and educational programmes to foster empathy, knowledge, and better practices among animal owners and the public.</li>
      </ul>
    </div>
    <div class="image-block">
      <img src="aboutus2.jpeg" alt="Animal rescue in progress">
    </div>
  </div>
</section>

<section class="call-to-action-banner">
  <h2>Join Our Team and Make a Difference!</h2>
  <p>Come volunteer and be a voice for the voiceless— together, we can create a better world for animals in need.</p>
  <button onclick="window.location.href='volunteerpage.php'">Volunteer Today</button>
</section>

  </main>
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

  <!-- Copyright -->
  <div class="footer-bottom">
    <p>© 2025 FurryHaven | Website design & hosting sponsored by Ezteck</p>
  </div>
</footer>

</body>
</html>