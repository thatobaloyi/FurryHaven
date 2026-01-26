<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Volunteer - FurryHaven</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #003366; /* Dark Blue */
      --secondary-color: #FF8C00; /* Orange */
      --background-color: #f8f4e9; /* Light Beige */
      --text-color: #333333; /* Dark Gray */
      --light-text: #f8f8f8; /* Off-White */
    }

    * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    body { background-color: var(--background-color); color: var(--text-color); line-height:1.6; }

    
    /* Header */
    .main-header { position: fixed; top:0; left:0; right:0; height:75px; display:flex; align-items:center; justify-content:space-between; padding:8px 24px; z-index:9999; background: rgba(255,248,240,0.0); border-bottom:none; transition: all 0.3s ease; }
    .main-header.transparent { background: rgba(255,248,240,0.45); backdrop-filter: blur(10px) saturate(1.05); border-bottom: 1px solid rgba(0,0,0,0.03); }
    .main-header.solid { backdrop-filter: none; box-shadow:0 3px 12px rgba(0,0,0,0.06); border-bottom:1px solid rgba(0,0,0,0.06); }
    .logo img { height:70px; display:block; }
    .nav-links { list-style:none; display:flex; gap:180px; }
    .nav-links li { position:relative; }
    .nav-links a { text-decoration:none; font-weight:600; color:#18436e; font-size:18px; }
    .nav-links a:hover { color:#df7100; }
    .dropdown-content { display:none; position:absolute; top:120%; left:0; background:#FFF8F0; min-width:180px; box-shadow:0 2px 8px rgba(0,0,0,0.1); padding:5px 0; list-style:none; z-index:1000; }
    .dropdown-content li a { display:block; padding:10px 16px; font-weight:500; color:#18436e; }
    .dropdown-content li a:hover { background-color:#df7100; color:white; }
    .dropdown:hover .dropdown-content { display:block; }

    .profile-icon { position: relative; cursor:pointer; }
    .profile-icon svg { width:32px; height:32px; fill:#18436e; transition: all 0.3s ease; }
    .profile-icon.active svg { fill:#df7100; transform:scale(1.1); }
    .profile-dropdown { display:none; position:absolute; right:0; top:120%; background:#FFF8F0; min-width:120px; box-shadow:0 2px 8px rgba(0,0,0,0.1); padding:5px 0; list-style:none; z-index:1000; }
    .profile-dropdown li a { display:block; padding:10px 16px; color:#18436e; font-weight:500; text-decoration:none; }
    .profile-dropdown li a:hover { background:#df7100; color:#fff; }
    .profile-icon.active .profile-dropdown { display:block; }

    /* Content Sections */
    .content-section { border-radius:12px; padding:40px; margin-bottom:40px; }
    .section-title { color: var(--primary-color); font-size:2.2rem; margin-bottom:25px; padding-bottom:15px; border-bottom:3px solid var(--secondary-color); display:inline-block; }

    /* Volunteer Today Section */
    .volunteer-today { display:grid; grid-template-columns:repeat(auto-fit, minmax(250px,1fr)); gap:30px; margin-top:30px; }
    .volunteer-role { background:#f9f9f9; padding:25px; border-radius:10px; text-align:center; transition:transform 0.3s ease; }
    .volunteer-role:hover { transform:translateY(-5px); }
    .volunteer-role h4 { color:var(--primary-color); font-size:1.4rem; margin-bottom:15px; }
    .volunteer-role p { color:#666; }

    /* Kennels Section */
    .kennels-section { display:flex; gap:40px; align-items:flex-start; flex-wrap:wrap; margin-top: 80px; /* add this line to push it down */ }
    .kennels-image { flex:1 1 400px; position:relative; height:400px; }

.volunteer-bubbles {
  position: relative;
  width: 800px;   /* wider to fit 3 large circles */
  height: 450px;  /* taller to allow vertical spacing */
}

.volunteer-bubbles .circle {
  position: absolute;
  width: 400px;
  height: 400px;
  border-radius: 50%;
  background-size: cover;
  background-position: center;
  border: 4px solid white;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  transition: transform 0.3s ease;
}

/* Triangle layout */
.volunteer-bubbles .circle:nth-child(1) {
  top: 0;
  left: 225px;   /* top center */
}

.volunteer-bubbles .circle:nth-child(2) {
  top: 300px;    /* lower */
  left: 10px;       /* left side */
}

.volunteer-bubbles .circle:nth-child(3) {
  top: 300px;    /* lower */
  left: 420px;   /* right side */
}

.volunteer-bubbles .circle:hover {
  transform: scale(1.1);
}




    .kennels-content { flex:1 1 400px; }
    .kennels-content h3 { color:var(--primary-color); font-size:1.8rem; margin-bottom:20px; }
    .kennels-content p { margin-bottom:20px; line-height:1.8; }

    /* Key Points Table */
    .key-points { width:100%; border-collapse:collapse; margin-top:30px; }
    .key-points th { background-color:var(--primary-color); color:white; padding:15px; text-align:left; }
    .key-points td { padding:15px; border-bottom:1px solid #eee; }
    .key-points tr:nth-child(even) { background-color:#f9f9f9; }

    /* Call to Action */
    .cta-section { text-align:center; padding:60px 20px; background:linear-gradient(135deg,var(--primary-color) 0%,#004080 100%); color:white; border-radius:12px; margin-bottom:40px; }
    .cta-section h2 { font-size:2.5rem; margin-bottom:20px; }
    .cta-section p { font-size:1.2rem; max-width:800px; margin:0 auto 30px; }
    .btn-primary { display:inline-block; background:var(--secondary-color); color:white; padding:15px 30px; border-radius:6px; text-decoration:none; font-weight:600; font-size:1.1rem; transition:background 0.3s ease; }
    .btn-primary:hover { background:#e67e00; }

        .section-title {
      text-align: center;
      font-size: 2em;
      color: var(--primary-color);
      margin-top: 2rem;
      margin-bottom: 2rem;
    }

     .steps-list {
      display: flex;
      justify-content: space-around;
      gap: 2rem;
      text-align: center;
      flex-wrap: wrap;
    }

    .step {
      flex: 1;
      min-width: 250px;
      background-color: white;
      padding: 1.5rem;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .step-number {
      font-size: 2.5rem;
      font-weight: bold;
      color: var(--secondary-color);
      margin-bottom: 0.5rem;
    }
      .faq-item {
      background-color: white;
      padding: 1.5rem;
      margin-bottom: 1rem;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .faq-item h3 {
        margin-top: 0;
        color: var(--primary-color);
    }

    .volunteer-opportunities {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
      margin-top: 2rem;
    }
    .opportunity-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        overflow: hidden;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }
    .opportunity-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .opportunity-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .card-content {
        padding: 1.5rem;
    }
    .card-content h3 {
        color: var(--primary-color);
        margin-top: 0;
    }

    /* Footer */
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



.kennels-content h2 {
  color: #003366; /* dark blue text */
  border-bottom: 4px solid #FF8C00; /* orange underline */
  display: inline-block; /* so the border only goes under the text */
  padding-bottom: 6px; /* spacing between text and border */
  margin-bottom: 20px; /* spacing below heading */
  margin-top: 60px; /* pushes content lower */
}

.kennels-content p {
  color: #003366; /* blue text for paragraphs */
  margin-bottom: 15px; /* spacing between paragraphs */
  margin-top: 40px; /* pushes content lower */
  font-size: 18px;  /* adjust to your preference */
  line-height: 1.8; /* keeps text readable */
}


    @media (max-width:992px) { .kennels-section { flex-direction:column; } footer { grid-template-columns:1fr; } .footer-logo, .contact-info { grid-column:span 1; text-align:center; } }
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
    <div class="profile-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24">
        <circle cx="12" cy="7" r="5"/>
        <path d="M2 21c0-5.5 4.5-10 10-10s10 4.5 10 10"/>
      </svg>
      <ul class="profile-dropdown">
        <li><a href="login.php">Sign In</a></li>
      </ul>
    </div>
  </header>

  <div class="main-content">

    <!-- Kennels Section with Triangle Bubbles -->
    <section class="content-section">
    
      <div class="kennels-section">
        <!-- Left: Volunteer Bubbles -->
        <div class="kennels-image">
          <div class="volunteer-bubbles">
            <div class="circle" style="background-image: url('vol1.jpeg');"></div>
            <div class="circle" style="background-image: url('vol2.jpeg');"></div>
            <div class="circle" style="background-image: url('vol3.jpeg');"></div>
          </div>
        </div>
        <!-- Right: Kennels content -->
        <div class="kennels-content">
          <h2>Join Our Volunteering Community </h2>
        <p>Volunteering at the Grahamstown SPCA is a rewarding way to make a difference in the lives of animals. Our volunteers help care for dogs, cats, and other animals, ensuring they receive love, attention, and support while waiting for their forever homes.</p>
<p>Whether you enjoy animal care, assisting at adoption events, or supporting our daily operations, your contribution is invaluable. By joining our volunteer community, you become part of a team dedicated to compassion, care, and improving the welfare of animals in Grahamstown.</p>
        </div>
      </div><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

      <!--Application Process-->
      <section class="how-to-start">
      <h2 class="section-title">Your First Step is Simple</h2>
      <div class="steps-list">
        <div class="step">
          <span class="step-number">1</span>
          <h3>Fill out the form</h3>
          <p>Complete our online application to tell us about your interests and availability.</p>
        </div>
        <div class="step">
          <span class="step-number">2</span>
          <h3>Attend an orientation</h3>
          <p>We'll invite you to a brief session to learn about our mission and policies.</p>
        </div>
        <div class="step">
          <span class="step-number">3</span>
          <h3>Find your fit</h3>
          <p>We'll help you find a volunteer role that matches your skills and passion.</p>
        </div>
      </div>
    </section>

 <section class="content-section">
      <h2 class="section-title">Important Documents</h2>
      <div class="attached-documents">
        <p>You can download the required Application Documents here:</p>
        <ul>
  
          <li><a href="doc/Indemnityform.pdf" target="_blank">Indemnity Form (PDF)</a></li>
          <li><a href="doc/Affidavitdocument.pdf" target="_blank">Affidavit Form (PDF)</a></li>
        
        
        </ul>
      </div>
    </section>





    <!-- Volunteer Today Section -->
  

    <section class="what-you-can-do">
        <h2 class="section-title">Explore Our Volunteer Opportunities</h2>
        <div class="volunteer-opportunities">
            <div class="opportunity-card">
                <img src="images/PHOTO-2025-08-02-13-11-15.jpg" alt="Animal Care">
                <div class="card-content">
                    <h3>Animal Care</h3>
                    <p>Help with feeding, grooming, and providing comfort to the animals at our shelter.</p>
                </div>
            </div>
            <div class="opportunity-card">
                <img src="images/abhijeet-singh-xTjYNR0FRPE-unsplash.jpg" alt="Dog Walking">
                <div class="card-content">
                    <h3>Dog Walking & Socialisation</h3>
                    <p>Give our dogs the exercise and human interaction they need to thrive.</p>
                </div>
            </div>
            <div class="opportunity-card">
                <img src="images/jamie-street-wFbkj9ilGnQ-unsplash.jpg" alt="Foster Care">
                <div class="card-content">
                    <h3>Foster Care</h3>
                    <p>Open your home to an animal in need of temporary care before they find their forever home.</p>
                </div>
            </div>
           
            <div class="opportunity-card">
                <img src="images/victor-g-N04FIfHhv_k-unsplash.jpg" alt="Special Events">
                <div class="card-content">
                    <h3>Special Events</h3>
                    <p>Help us with fundraising, adoption days, and other community outreach programs.</p>
                </div>
            </div>
        </div>
    </section>
    
<div class="kennels-content">
      <section class="faq">
      <h2 class="section-title">Frequently Asked Questions</h2>
      <div class="faq-item">
        <h3>What is the minimum age to volunteer?</h3>
        <p>You must be at least 18 years old.</p>
      </div>
      <div class="faq-item">
        <h3>Do I need previous experience?</h3>
        <p>No, you do not need previous experience. We provide all the necessary training for your role.</p>
      </div>
      <div class="faq-item">
        <h3>What is the time commitment?</h3>
        <p>It is beneficial if you are able to commit and come regularly, either Wednesdays or Saturdays or both, whatever suits you, as you get to know the animals and can assist with suitable adoptions and their well-being.  
    If you wish to only gain your community service hours, it is suggested that you apply for the Community Service Programme.</p>
      </div>

    </section>
  <p style="color:#003366;">
    Once you have joined, you will also learn of other exciting opportunities to get involved. We look forward to you joining the team and promise you many rewarding hours making a difference in the lives of the animals in our care.
  </p><br>
  
 
  <p style="color:#003366;">
    ✅ <strong>Volunteer here if:</strong>
    <ul style="color:#003366;">
      <li>You understand this is an unpaid position with no stipend or travel reimbursement—your time is a generous gift.</li>
      <li>You enjoy reading or have a genuine interest in books.</li>
      <li>You are comfortable being on your feet for several hours.</li>
      <li>You are friendly, approachable, and enjoy interacting with customers.</li>
      <li>You can handle transactions, including using a card machine and managing cash.</li>
      <li>You are reliable, enthusiastic, and committed to making a positive impact.</li>
</ul>
<p style="color:#003366;">
  Your time and effort can help us change lives, one book, one item sold at a time. 🐾
</p>
</div>




    <!-- Call to Action -->
    <section class="cta-section">
      <h2>Ready to Make a Difference?</h2>
      <p>Join our volunteer team today and help us provide care and love to animals in need. Your time and effort can transform lives.</p>
      <a href="volunteering.php" class="btn-primary">Apply to Volunteer</a>
    </section>

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
      <div class="social-icons">
        <a href="#"><img src="facebook-icon.png" alt="Facebook"></a>
        <a href="#"><img src="instagram-icon.jpg" alt="Instagram"></a>
      </div>
    </div>
  </div>

  <!-- Copyright -->
  <div class="footer-bottom">
    <p>© 2025 FurryHaven | Website design & hosting sponsored by Ezteck</p>
  </div>
</footer>


    <script>
        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.main-header');
            if (window.scrollY > 50) {
                header.classList.add('solid');
                header.classList.remove('transparent');
            } else {
                header.classList.add
('transparent');
                header.classList.remove('solid');
            }
        });
    </script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const profileIcon = document.querySelector('.profile-icon');
  if (!profileIcon) return;

  // ensure keyboard focusable
  if (!profileIcon.hasAttribute('tabindex')) profileIcon.setAttribute('tabindex', '0');

  // Toggle dropdown on click
  profileIcon.addEventListener('click', function (e) {
    e.stopPropagation();
    this.classList.toggle('active');
  });

  // Support keyboard toggle (Enter / Space)
  profileIcon.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      this.classList.toggle('active');
    }
  });

  // Close when clicking anywhere else
  document.addEventListener('click', function (e) {
    if (!profileIcon.contains(e.target)) {
      profileIcon.classList.remove('active');
    }
  });

  // Optional: close on Escape
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') profileIcon.classList.remove('active');
  });
});
</script>
<?php include_once __DIR__ . '/includes/profile_dropdown.php'; ?>
</body>
</html>