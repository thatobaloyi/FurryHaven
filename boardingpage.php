<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pet Boarding — Furry Friends Shelter</title>
  <header class="site-header">
  <a href="homepage.php" class="back-btn">← Back</a>
  <h1>Back</h1>
</header>

<style>
.back-btn {
  text-decoration: none;
  color: white;
  font-size: 18px;
  font-weight: bold;
  margin-right: 15px;
}

.back-btn:hover {
  text-decoration: underline;
}
    :root {
      --primary-color: #003366;
      --secondary-color: #FF8C00;
      --background-color: #f8f4e9;
      --text-color: #333333;
      --card-color: #ffffff;
      --muted-color: #555555;
    }

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

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--background-color);
      color: var(--text-color);
      margin: 0;
      padding: 0;
      line-height: 1.6;
    }

    .container {
      max-width: 900px;
      margin: 40px auto;
      padding: 20px;
    }

    .card {
      background-color: var(--card-color);
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      margin-bottom: 24px;
    }

    .hero {
      text-align: center;
      padding: 20px;
    }

    h1 {
      font-size: 36px;
      color: var(--primary-color);
      margin-top: 0;
    }

    h2 {
      color: var(--primary-color);
    }

    p {
      color: var(--muted-color);
      line-height: 1.6;
    }

    .buttons {
      display: flex;
      justify-content: center;
      gap: 16px;
      margin-top: 24px;
    }

    .button {
      padding: 12px 24px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: background-color 0.3s;
      cursor: pointer;
    }

    .primary {
      background-color: var(--secondary-color);
      color: white;
    }

    .primary:hover {
      background-color: #e67e00;
    }

    .secondary {
      background-color: var(--card-color);
      color: var(--secondary-color);
      border: 2px solid var(--secondary-color);
    }

    .secondary:hover {
      background-color: #fff3e0;
    }

    .features {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 24px;
    }

    .feature-item {
      flex: 1 1 200px;
      background-color: #fff8f0;
      border-radius: 10px;
      padding: 16px;
      text-align: center;
    }

    .feature-item h3 {
      margin-top: 0;
      font-size: 18px;
      color: var(--secondary-color);
    }

    .testimonial {
      background-color: #fff8f0;
      border-left: 4px solid var(--secondary-color);
      padding: 16px;
      border-radius: 8px;
      margin-bottom: 12px;
    }

    .testimonial p {
      margin: 0;
      font-style: italic;
    }

    .testimonial span {
      display: block;
      margin-top: 8px;
      font-weight: 600;
      color: var(--text-color);
    }

    .steps {
      list-style-type: none;
      padding: 0;
    }

    .step {
      display: flex;
      align-items: center;
      margin-bottom: 16px;
    }

    .step-number {
      background-color: var(--secondary-color);
      color: white;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      margin-right: 12px;
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
      content: "•";
      color: var(--secondary-color);
      font-weight: bold;
      position: absolute;
      left: 0;
    }

    @media (max-width: 768px) {
      .features { flex-direction: column; }
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

  </style>
  
  <!-- Header -->
  <header class="main-header transparent">
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
            <li><a href="donate.php">Donate</a></li>
            <li><a href="boarding.php">Boarding</a></li>
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
</head>
<body>
  <div class="container">
    <div class="card hero">
      <h1>Safe & Happy Pet Boarding</h1>
      <p>When you're away, your furry friend can stay in a loving, safe place. We provide daily care, play, and peace of mind for you.</p>
      <div class="buttons">
        <a href="#book-now" class="button primary">Book Now</a>
        <a href="#about" class="button secondary">Learn More</a>
      </div>
    </div>

    <div class="card">
      <h2>Why Choose Us?</h2>
      <p>We believe every pet deserves a vacation, too. Our trained staff ensure your pet's routine is maintained, with plenty of love and attention.</p>
      <div class="features">
        <div class="feature-item">
          <h3>Expert Care</h3>
          <p>Trained staff for feeding, medication, and safety.</p>
        </div>
        <div class="feature-item">
          <h3>Stress-Free Stay</h3>
          <p>Calm environment with toys and social time.</p>
        </div>
        <div class="feature-item">
          <h3>Daily Updates</h3>
          <p>Get photo updates sent right to your phone.</p>
        </div>
      </div>
    </div>

    <div class="card" id="about">
      <h2>How It Works</h2>
      <p>Our process is simple and transparent. We'll make sure your pet's stay is comfortable from start to finish.</p>
      <ul class="steps">
        <li class="step">
          <div class="step-number">1</div>
          <div>
            <strong>Request a Stay</strong><br>
            Fill out our simple form with your dates and pet details.
          </div>
        </li>
        <li class="step">
          <div class="step-number">2</div>
          <div>
            <strong>Drop Off</strong><br>
            Bring your pet for a quick health check and a warm welcome.
          </div>
        </li>
        <li class="step">
          <div class="step-number">3</div>
          <div>
            <strong>Enjoy Your Trip</strong><br>
            Relax knowing your pet is in great hands and get daily updates.
          </div>
        </li>
      </ul>
    </div>

    <div class="card">
      <h2>Happy Owners</h2>
      <div class="testimonial">
        <p>"The team accepted Bella on short notice and sent us daily photos. We're so grateful!"</p>
        <span>— Sarah</span>
      </div>
      <div class="testimonial">
        <p>"They followed Rocky's medication schedule perfectly. He came home calm and happy."</p>
        <span>— Mark</span>
      </div>
    </div>

    <div class="card">
      <h2 id="book-now">Ready to Book?</h2>
      <p>Spaces fill up fast, so book your pet's stay with us today.</p>
      <div class="buttons">
        <a href="boarding.php" class="button primary">Book Boarding</a>
        <a href="contact.html" class="button secondary">Contact Us</a>
      </div>
    </div>
  </div>
</body>

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
</html>
