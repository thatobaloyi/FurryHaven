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

  $boardingHref = isset($_SESSION['username'])
      ? 'boarding.php'
      : 'login.php?redirect=boarding.php';

$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

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
/*General Styles and CSS Variables*/
        :root {
            --primary-color: #003366; /* Dark Blue */
            --secondary-color: #FF8C00; /* Orange */
            --background-color: #f8f4e9; /* Light Beige */
            --light-text: #f8f8f8; /* Off-White */
        }
h1 {
  text-align: center;
  color: var(--primary-color);   /* system blue */
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

/* START OF CAROUSEL STYLING */
.carousel-item {
  position: relative;
  width: 100%;
  height: 100vh;     
  overflow: hidden;
}

#furryhavenBanner {
  width: 100%;
  margin: 0;
  padding: 0;
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

.carousel-caption.caption-slide1 {
  position: absolute;
  top: 40%;             /* Vertical center */
  left: 30%;            /* Horizontal center */
  transform: translate(-50%, -50%); /* Perfect centering */
  text-align: center;   /* Center text */
  max-width: 80%;       /* Prevent it from being too wide */
  z-index: 5;
  color: #fff;          /* Make sure text is readable */
  font-family: 'Segoe UI', sans-serif;
}

/* Heading */
.carousel-caption.caption-slide1 h2 {
  font-size: clamp(36px, 5vw, 60px); /* Responsive size */
  font-weight: 800;
  margin-bottom: 12px;
  line-height: 1.2;
  border-bottom: 4px solid #FF8C00; /* Orange line */
  display: inline-block;            /* So the line matches the text width */
  padding-bottom: 6px;   
}

/* Subtext */
.carousel-caption.caption-slide1 p {
  font-size: clamp(18px, 2.5vw, 24px); /* Responsive size */
  line-height: 1.4;
}

.slide3-caption {
  position: absolute;
  top: 50%;                  /* Vertical center */
  left: 20%;                 /* Horizontal center */
  transform: translate(-50%, -300%); /* Perfect centering */
  text-align: center;
  z-index: 5;
  max-width: 80%;            /* Prevent text from being too wide */
  font-family: 'Segoe UI', sans-serif;
  color: #fff;
}

.slide3-caption .carousel-quote {
  font-size: clamp(36px, 5vw, 60px); /* Responsive heading size */
  font-weight: 800;
  display: inline-block;             /* Keeps text width compact */
  margin-bottom: 20px;               /* Space between quote and button */
  line-height: 1.2;
}

/* Optional: Keep the button styling the same */
.slide3-caption .report-btn {
  display: inline-block;
  padding: 14px 28px;
  background-color: #df7100;
  color: #fff;
  font-weight: bold;
  text-decoration: none;
  border-radius: 6px;
  font-size: 18px;
  transition: background 0.3s ease, transform 0.2s ease;
}

.slide3-caption .report-btn:hover {
  background-color: #ff8c33;
  transform: scale(1.05);
}
/* END OF CAROUSEL STYLING */



/* Sidebar container */
.dropdown-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 280px; /* sidebar width */
    height: auto;
    background-color: #FFF8F0;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    box-shadow: 4px 0 12px rgba(0,0,0,0.3);
    z-index: 1000;
}

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







/* END OF HOMEPAGE STYLING */






/*CODE FOR THE ADOPT VOLUNTEER AND DONATE CARDS*/
/* Flip card container */
       /* Opportunities Section */
        .opportunities {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .opportunity-card {
            text-decoration: none;
            height: 380px;
            perspective: 1000px;
        }
        
        .flip-card {
            background-color: transparent;
            width: 100%;
            height: 100%;
            perspective: 1000px;
        }
        
        .flip-card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
            transition: transform 0.8s;
            transform-style: preserve-3d;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .opportunity-card:hover .flip-card-inner {
            transform: rotateY(180deg);
        }
        
        .flip-card-front, .flip-card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        
        .flip-card-front {
            background: linear-gradient(135deg, var(--primary-color) 0%, #004080 100%);
            color: var(--light-text);
        }
        
        .flip-card-back {
            background: white;
            color: var(--text-color);
            transform: rotateY(180deg);
        }
        
        .card-number {
            width: 60px;
            height: 60px;
            background: var(--secondary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 1.8rem;
            font-weight: bold;
            color: white;
        }
        
        .flip-card-front h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .flip-card-front img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 10px;
            margin-top: 15px;
        }
        
        .flip-card-back h1 {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .flip-card-back p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 10px;
            color: #555;
        }
        
        .flip-card-back .btn {
            margin-top: 20px;
            padding: 10px 20px;
            background: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .flip-card-back .btn:hover {
            background: #e67e00;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .opportunities {
                grid-template-columns: 1fr;
            }
            
            .opportunity-card {
                height: 350px;
            }
            
            .section-header h1 {
                font-size: 2.2rem;
            }
            
            .section-header p {
                font-size: 1rem;
            }
        }

/*Analyzing the 3 R's of Hope section*/
 .hope-section {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  gap: 2rem;
  margin: 3rem auto;
  max-width: 1000px;
}

.r-of-hope {
  display: flex;
  justify-content: space-between; /* pushes first to left, last to right */
  align-items: center;
  width: 100%;
  max-width: 1000px;
  margin: 0 auto;
}

.hope-card {
  flex: 0 0 auto; /* stop them from stretching */
  min-width: 200px;
  max-width: 280px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.counter-value {
  color: var(--primary-color);
  font-size: 2.5rem;
  font-weight: bold;
  margin: 0.5rem 0;
}

.hope-card p {
  font-size: 2rem;
  color: #333;
  margin: 0;
}


/*END OF HOPE SECTION*/

/*VOLUNTEER*/
.hero {
  background: url("spcaezteck.jpg") center top/cover no-repeat;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  color: white;
  position: relative
  
}


.hero::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.5); /* dark overlay so text pops */
}

/* Hero content */
.hero-content {
  position: relative;
  z-index: 1;
  max-width: 700px;
  padding: 2rem;
  color: white; /* makes all text inside white */
}

/* Headings */
.hero h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
  color: white; /* ensures H1 is white */
}

/* Paragraph */
.hero p {
  font-size: 1.2rem;
  margin-bottom: 2rem;
  color: white; /* ensures paragraph is white */
}

/* Button */
.btn-primary {
  display: inline-block;
  padding: 12px 30px;
  background-color: #df7100; /* orange */
  color: white; /* button text white */
  font-weight: bold;
  border-radius: 8px;
  transition: background 0.3s;
  text-decoration: none;
}

.btn-primary:hover {
  background-color: #b85500; /* darker orange on hover */
  text-decoration: none; /* ensures no underline on hover */
}

/*VOLUNTEER*/


/*Whatever this section is*/
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

/* Footer Styles */
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

.newsletter {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.newsletter input {
  flex: 1;
  padding: 0.6rem;
  border: none;
  border-radius: 4px;
}

.newsletter button {
  background-color: #98b06f;
  border: none;
  padding: 0.6rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  transition: background 0.3s ease;
}

.newsletter button:hover {
  background-color: #df7100;
}

.social-icons a {
  display: inline-block;
  margin-right: 10px;
}

.social-icons img {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  transition: transform 0.3s ease;
}

.social-icons img:hover {
  transform: scale(1.1);
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  padding-top: 1rem;
  text-align: center;
  font-size: 0.9rem;
  opacity: 0.8;
}



/* Footer End */
        
        @media (max-width: 768px) {
            header {
                flex-direction: row;
                padding: 1rem;
            }

            nav ul {
                display: none;
            }

            .hamburger {
                display: block;
            }

            .user-profile {
                margin-left: 15px;
            }

            .sidebar {
                display: block;
                right: 0;
                transform: translateX(100%);
                transition: transform 0.3s ease-in-out;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .r-of-hope, .breaking-the-chain, .champions, footer {
                grid-template-columns: 1fr;
            }

            .footer-logo, .contact-info, .copyright {
                grid-column: span 1;
                text-align: center;
            }
        }
/* New styles for the animal grid */
/* Section heading */
/* Section heading */
section h2 {
  text-align: center;
  margin-bottom: 1.5rem;
  font-size: 1.8rem;
  color: #333;
}


/* Grid container */
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));

  justify-items: center;
  margin-top: 2rem;
  padding: 0 1rem;
}

     /* Animal Gallery Styles */
        .animal-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2.5rem;
            justify-items: center;
            margin-top: 2rem;
            padding: 0 1rem;
        }
        
        .animal-item {
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .animal-item:hover {
            transform: translateY(-5px);
        }
        
        .animal-image {
            width: 100%;
            height: 300px;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease;
        }
        
        .animal-item:hover .animal-image {
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .animal-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .animal-item:hover .animal-image img {
            transform: scale(1.05);
        }
        
        .animal-name {
            margin-top: 15px;
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .animal-breed {
            color: var(--secondary-color);
            font-size: 1rem;
            margin-top: 5px;
        }
        
        .view-more {
            text-align: center;
            margin-top: 50px;
        }
        
        .btn-secondary {
            display: inline-block;
            background: var(--secondary-color);
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: background 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #e67e00;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .animal-gallery {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1.5rem;
            }
            
            .animal-image {
                height: 250px;
            }
            
            .section-header h1 {
                font-size: 2.2rem;
            }
            
            .section-header p {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .animal-gallery {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .animal-image {
                height: 280px;
                max-width: 300px;
            }
        }
        /* Slider Container */
        .slider-container {
            position: relative;
            overflow: hidden;
            margin: 30px 0;
            padding: 0 40px;
        }
        
        /* Animal Slider */
        .animal-slider {
            display: flex;
            transition: transform 0.5s ease;
        }
        
        .slide {
            flex: 0 0 25%;
            padding: 0 15px;
            text-align: center;
        }
        
        .animal-image {
            width: 100%;
            height: 300px;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.1);
        }
        
        .animal-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .animal-name {
            margin-top: 15px;
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .animal-breed {
            color: var(--secondary-color);
            font-size: 1rem;
            margin-top: 5px;
        }
        
        /* Slider Controls */
        .slider-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
        }
        
        .slider-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            transition: background 0.3s ease;
            margin: 0 10px;
        }
        
        .slider-btn:hover {
            background: var(--secondary-color);
        }
        
        .slider-dots {
            display: flex;
            justify-content: center;
            margin: 0 15px;
        }
        
        .dot {
            width: 12px;
            height: 12px;
            background: #ccc;
            border-radius: 50%;
            margin: 0 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .dot.active {
            background: var(--primary-color);
        }
        
        .view-more {
            text-align: center;
            margin-top: 50px;
        }
        
        .btn-secondary {
            display: inline-block;
            background: var(--secondary-color);
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: background 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #e67e00;
        }
        
        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .slide {
                flex: 0 0 33.33%;
            }
        }
        
        @media (max-width: 768px) {
            .slide {
                flex: 0 0 50%;
            }
            
            .animal-image {
                height: 250px;
            }
            
            .section-header h1 {
                font-size: 2.2rem;
            }
            
            .section-header p {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .slide {
                flex: 0 0 100%;
            }
            
            .slider-container {
                padding: 0 20px;
            }
            
            .animal-image {
                height: 280px;
            }
        }



     .sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 250px;
  height: 100vh;
  background-color: #003366;
  color: #fff;
  padding: 20px;
  overflow-y: auto;
}

.sidebar-header h3 {
  text-align: center;
  margin-bottom: 25px;
  color: #ff8c00; /* highlight username */
}

.sidebar-section h4 {
  font-size: 0.95rem;
  margin-bottom: 10px;
  color: #ff8c00;
}

.sidebar-section ul {
  list-style: none;
  padding: 0;
}

.sidebar-section ul li {
  margin-bottom: 12px;
}

.sidebar-section ul li a {
  color: #fff;
  text-decoration: none;
  padding: 5px 10px;
  display: block;
  border-radius: 5px;
}

.sidebar-section ul li a:hover {
  background-color: #ff8c00;
  color: #003366;
}

.main-content {
  margin-left: 250px;
  padding: 20px;
}
   
        /* Sidebar Styling (as provided previously, for context) */
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
            overflow-y: auto;
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

            header {
                padding: 1rem;
            }
        }

        /* Other styles from your original code */
      
        .carousel-inner .item img {
            height: 300px;
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



.icon-circle {
  width: 200px;
  height: 200px;
  border-radius: 50%;
  background: var(--secondary-color); /* white circle */
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.icon-circle img {
  width: 50px;
  height: 50px;
  object-fit: contain;
}
/* End of new styles */

/* Boardin */
.boarding-image-container {
  position: relative;
  width: 100%;
  max-height: 600px;
  overflow: hidden;
  box-shadow: 0 6px 14px rgba(0,0,0,0.1);
}

.boarding-image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.boarding-caption {
  position: absolute;
  top: 50%;         /* vertically center */
  left: 5%;         /* move to left side */
  transform: translateY(-50%); /* only adjust vertical */
  text-align: left;
  color: #fff;
}

.boarding-caption h2 {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.boarding-caption button {
  padding: 0.6rem 1.2rem;
  font-size: 1rem;
}
/* End of Boarding */

/*Volunteer Section*/
.volunteer-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 2rem;
  max-width: 1100px;
  margin: 50px auto;
  padding: 20px;
}

.volunteer-image img {
  width: 450px; /* adjust size */
  height: auto;
  border-radius: 10px; /* keeps corners slightly soft */
  box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
}

.volunteer-text {
  flex: 1;
}

.volunteer-text h2 {
  font-size: 2rem;
  color: #003366; /* dark blue similar to your example */
  margin-bottom: 1rem;
}

.volunteer-text p {
  font-size: 1rem;
  line-height: 1.6;
  margin-bottom: 1rem;
  color: #444;
}

/*THIS IS EXPEREIMENTAL SHIII*/

        

  
        .user-info {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        

        
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
      
      .banner {
    display: flex;
    height: 500px;
    width: 100%;
  }

  /* Left side with blurred image */
  .banner-left {
    flex: 1;
    position: relative;
    background: url('crueltyimage.jpg') center/cover no-repeat;
   
  }



  /* Right side with text */
  .banner-right {
    flex: 1;
    background-color: #18436e;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 0 40px;
  }

  .banner-right h2 {
    font-size: 2rem;
    margin-bottom: 15px;
    color:white;
  }

  .banner-right p {
    font-size: 1rem;
    margin-bottom: 25px;
  }

  .banner-right .donate-btn {
    background-color: #df7100;
    color: white;
    border: none;
    padding: 12px 30px;
    font-size: 1rem;
    cursor: pointer;
    border-radius: 5px;
    text-decoration: none;
  }

  @media (max-width: 768px) {
    .banner {
      flex-direction: column;
      height: auto;
    }
    .banner-left, .banner-right {
      flex: none;
      width: 100%;
      height: 300px;
    }
    .banner-right {
      padding: 20px;
    }
  }   

        

        


        

</style>
      </head>
      <body>

  <!-- Header -->
  <header class="main-header transparent">
    <a href="#" class="logo">
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
          <li><a href="volunteering.php">Volunteer</a></li>
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
  </header>

<div id="furryhavenBanner" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
  <div class="carousel-inner">

    <!-- Slide 1 -->
<div class="carousel-item active">
  <img src="bannerdog.jpg" class="d-block w-100" style="height:700px; object-fit:cover; object-position: 70% 50%;" alt="Dog 1">
  <div class="carousel-caption caption-slide1">
    <h2>Welcome to FurryHaven</h2>
    <p>Supporting Makhanda’s animals, one paw at a time.</p>
  </div>
</div>


    <!-- Slide 3 with quote + button -->
<div class="carousel-item">
  <img src="image7.jpg" class="d-block w-100" style="height:700px; object-fit:cover; object-position: 70% 50%;" alt="Slide 3">
  <div class="carousel-caption slide3-caption">
    <span class="carousel-quote">Help Us, Help Our Furry Friends</span><br>
    <a href="report.php" class="report-btn mt-3">Report Cruelty</a>
  </div>
</div>

  
  <!-- Arrows -->
  <button class="carousel-control-prev" type="button" data-bs-target="#furryhavenBanner" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#furryhavenBanner" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>









        
<main>
<h1 class="text-center ">Take the first step!</h1>

<br><section class="opportunities">
    <a href="adoptable.php" class="opportunity-card">
        <div class="flip-card">
            <div class="flip-card-inner">
                <div class="flip-card-front">
                    <div class="card-number">1</div>
                    <h3>ADOPTION</h3>
                    
                </div>
                <div class="flip-card-back">
                    <h1>ADOPTION</h1>
                    <p>Give a forever home to a loving animal in need of a second chance.</p>
                    <p>Our adoption process is designed to ensure the perfect match between you and your new companion.</p>
                    <button class="btn">Find Your Friend</button>
                </div>
            </div>
        </div>
    </a>

    <a href="campaignpage.php" class="opportunity-card">
        <div class="flip-card">
            <div class="flip-card-inner">
                <div class="flip-card-front">
                    <div class="card-number">2</div>
                    <h3>DONATION</h3>
                    
                </div>
                <div class="flip-card-back">
                    <h1>DONATION</h1>
                    <p>Every donation, big or small, helps us provide essential resources and care for our animals.</p>
                    <p>Your support enables us to continue our mission of rescuing and rehabilitating animals in need.</p>
                    <button class="btn">Make a Difference</button>
                </div>
            </div>
        </div>
    </a>

    <a href="volunteerpage.php" class="opportunity-card">
        <div class="flip-card">
            <div class="flip-card-inner">
                <div class="flip-card-front">
                    <div class="card-number">3</div>
                    <h3>VOLUNTEER</h3>
                    
                </div>
                <div class="flip-card-back">
                    <h1>VOLUNTEER</h1>
                    <p>Your time and effort are invaluable. Help with daily tasks, events, and animal care.</p>
                    <p>Join our dedicated team of volunteers and make a direct impact on the lives of our animals.</p>
                    <button class="btn">Join Our Team</button>
                </div>
            </div>
        </div>
    </a>
     <a href="#" class ="opportunity-card">
        <div class="flip-card">
            <div class="flip-card-inner">
                <div class="flip-card-front">
                    <div class="card-number">4</div>
                    <h3>BOARDING</h3>
                    
                </div>
                <div class="flip-card-back">
                    <h1>BOARDING</h1>
                    <p>Your pet's home away from home. Safe and comfortable boarding facilities.</p>
                    <p>Join our dedicated team of volunteers and make a direct impact on the lives of our animals.</p>
                    <button id="boarding-button"> Book Now</button>


                          <script>
                    document.getElementById("boarding-button").addEventListener("click", function () {
                        <?php if (!isset($_SESSION['username'])): ?>
                            alert("Please log in or register to access the boarding form.");
                            window.location.href = 'login.php?redirect=boarding.php';
                        <?php else: ?>
                            window.location.href = 'userAnimal.php';
                        <?php endif; ?>
                    });
                </script>
                </div>
            </div>
        </div>
    </a>
</section>
<br><br><br>


<!-- Analyzing the 3 R's of Hope section --> 
<?php
$sql1 = "SELECT COUNT(*) AS total FROM animal WHERE isDeleted = 0";
$sql2 = "SELECT COUNT(*) AS total FROM animal WHERE isDeleted = 0 AND Animal_Vacc_Status = 'Vaccinated'";
$sql3 = "SELECT COUNT(*) AS total FROM animal WHERE isDeleted = 0 AND outtakeType = 'Adoption'";

$result1 = mysqli_query($conn, $sql1);
$result2 = mysqli_query($conn, $sql2);
$result3 = mysqli_query($conn, $sql3);

$row1 = mysqli_fetch_assoc($result1);
$row2 = mysqli_fetch_assoc($result2);
$row3 = mysqli_fetch_assoc($result3);
?>
<section class="hope-section">
  <div class="hope-content">
    <div class="intro">
      <h1>The 3R's of Hope</h1><br><br><br>
    </div>
    <div class="r-of-hope">
      <!-- Rescued -->
      <div class="hope-card">
        <div class="icon-circle">
          <img src="images/icons/pawprint.png" alt="Rescued">
        </div>
        <div class="counter-value" data-target="9743">0</div>
        <p>Rescued</p>
      </div>

      <!-- Rehabilitated -->
      <div class="hope-card">
        <div class="icon-circle">
          <img src="images/icons/veterinary.png" alt="Rehabilitated">
        </div>
        <div class="counter-value" data-target="7485">0</div>
        <p>Rehabilitated</p>
      </div>

      <!-- Rehomed -->
      <div class="hope-card">
        <div class="icon-circle">
          <img src="images/icons/house.png" alt="Rehomed">
        </div>
        <div class="counter-value" data-target="8394">0</div>
        <p>Rehomed</p>
      </div>
    </div>
  </div>
</section>


<script>

const counters = document.querySelectorAll('.counter-value');

counters.forEach(counter => {
    counter.innerText = '0';

    const updateCounter = () => {
        const target = +counter.getAttribute('data-target');
        const c = +counter.innerText;

        const duration = 3000; // total animation duration in ms
        const stepTime = 20;   // how often to update (ms)
        const increment = target / (duration / stepTime);

        if (c < target) {
            counter.innerText = `${Math.ceil(c + increment)}`;
            setTimeout(updateCounter, stepTime);
        } else {
            counter.innerText = target;
        }
    };

    updateCounter();
});
</script>
</section>

<!-- End of 3 R's of Hope section -->

<!-- Adoptable Animals Section -->
                <br><br><section>
    <div class="section-header">
        <h1>Meet Your Furry Friends</h1>
    </div>
    
    <!-- Slider Container -->
    <div class="slider-container">
        <div class="animal-slider">
            <?php
            $count = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                if ($count >= 20) break; // Limit to 5 animals for the slider
            ?>
                <div class="slide">
                    <form action="adoptable2.php" method="POST">
                        <input type="hidden" name="Animal_ID" value="<?php echo $row['Animal_ID']; ?>">
                        <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0; width: 100%;">
                            <div class="animal-image">
                                <img src="./images/animals/<?php echo htmlspecialchars($row['filePath']); ?>" alt="<?php echo htmlspecialchars($row['Animal_Name']); ?>">
                            </div>
                            <h3 class="animal-name"><?php echo htmlspecialchars($row['Animal_Name']); ?></h3>
                            <p class="animal-breed"><?php echo htmlspecialchars($row['Animal_Breed']); ?></p>
                        </button>
                    </form>
                </div>
            <?php
                $count++;
            }
            ?>
        </div>
    </div>
    
   <!-- Slider Controls -->
            <div class="slider-controls">
                <button class="slider-btn" id="prev-slide"><i class="fas fa-chevron-left"></i></button>
                
                <div class="slider-dots" id="slider-dots">
                    <!-- Dots will be generated by JavaScript -->
                </div>
                
                <button class="slider-btn" id="next-slide"><i class="fas fa-chevron-right"></i></button>
            </div>
            
            <div class="view-more">
                <a href="adoptable.php" class="btn-secondary">View More Animals</a>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.querySelector('.animal-slider');
            const slides = document.querySelectorAll('.slide');
            const dotsContainer = document.getElementById('slider-dots');
            const prevBtn = document.getElementById('prev-slide');
            const nextBtn = document.getElementById('next-slide');
            
            let currentIndex = 0;
            let slidesToShow = 4; // Default number of slides to show
            let totalSlides = slides.length;
            let autoSlideInterval;
            
            // Create dots based on number of slides
            function createDots() {
                dotsContainer.innerHTML = '';
                const dotsNeeded = Math.ceil(totalSlides / slidesToShow);
                
                for (let i = 0; i < dotsNeeded; i++) {
                    const dot = document.createElement('div');
                    dot.classList.add('dot');
                    if (i === 0) dot.classList.add('active');
                    dot.addEventListener('click', () => goToSlide(i));
                    dotsContainer.appendChild(dot);
                }
            }
            
            // Determine how many slides to show based on screen width
            function updateSlidesToShow() {
                if (window.innerWidth >= 1024) {
                    slidesToShow = 4;
                } else if (window.innerWidth >= 768) {
                    slidesToShow = 3;
                } else if (window.innerWidth >= 480) {
                    slidesToShow = 2;
                } else {
                    slidesToShow = 1;
                }
                
                // Recreate dots when slidesToShow changes
                createDots();
                updateSliderPosition();
                updateDots();
            }
            
            // Initialize slider
            function initSlider() {
                updateSlidesToShow();
                updateSliderPosition();
                startAutoSlide();
                
                // Add event listeners
                prevBtn.addEventListener('click', prevSlide);
                nextBtn.addEventListener('click', nextSlide);
                
                // Update on window resize
                window.addEventListener('resize', function() {
                    updateSlidesToShow();
                });
            }
            
            // Update slider position
            function updateSliderPosition() {
                const slideWidth = 100 / slidesToShow;
                const translateX = -(currentIndex * slideWidth);
                slider.style.transform = `translateX(${translateX}%)`;
            }
            
            // Update dots
            function updateDots() {
                const dots = document.querySelectorAll('.dot');
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentIndex);
                });
            }
            
            // Go to next slide
            function nextSlide() {
                const maxIndex = Math.ceil(totalSlides / slidesToShow) - 1;
                
                if (currentIndex < maxIndex) {
                    currentIndex++;
                } else {
                    currentIndex = 0;
                }
                updateSliderPosition();
                updateDots();
            }
            
            // Go to previous slide
            function prevSlide() {
                const maxIndex = Math.ceil(totalSlides / slidesToShow) - 1;
                
                if (currentIndex > 0) {
                    currentIndex--;
                } else {
                    currentIndex = maxIndex;
                }
                updateSliderPosition();
                updateDots();
            }
            
            // Go to specific slide
            function goToSlide(index) {
                const maxIndex = Math.ceil(totalSlides / slidesToShow) - 1;
                
                if (index >= 0 && index <= maxIndex) {
                    currentIndex = index;
                    updateSliderPosition();
                    updateDots();
                }
            }
            
            // Start auto slide
            function startAutoSlide() {
                autoSlideInterval = setInterval(nextSlide, 4000);
            }
            
            // Initialize the slider
            initSlider();
        });
    </script><br>
<!-- End of Adoptable Animals Section -->


<!-- Volunteer Section Start -->




<!-- Volunteer Section End -->
  <section>
 <div class="banner">
  <div class="banner-left">
    <div class="content-warning">
    </div>
  </div>
  <div class="banner-right">
    <h2>LET’S PUT AN END TO ANIMAL CRUELTY</h2>
    <p>Every year thousands of animals suffer from neglect, cruelty and abuse. With your help, we can end their misery.</p>
    <a href="cruelty.html" class="donate-btn">Report Cruelty</a>
  </div>
</div>
</section><br>


<!--Volunteer Section Start -->
<section class="hero">
  <div class="hero-content">
    <h1>Support Our Cause</h1>
    <p>Together, we can give every animal a second chance at a loving home.</p>
    <a href="volunteerpage.php" class="btn-primary">Volunteer Now</a>
  </div>
</section>




<!--Volunteer Section End -->



<!-- Footer -->
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

 <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>       

<!-- Footer -->

        <script>


  var myCarousel = document.querySelector('#furryhavenBanner');
  var carousel = new bootstrap.Carousel(myCarousel, {
    interval: 3000,  // 3 seconds
    ride: 'carousel'
  });



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

   

            const profileIcon = document.querySelector('.profile-icon');

  profileIcon.addEventListener('click', () => {
    profileIcon.classList.toggle('active');
  });

  // Optional: close dropdown if you click outside
  document.addEventListener('click', (e) => {
    if (!profileIcon.contains(e.target)) {
      profileIcon.classList.remove('active');
    }
  });


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

    updateHeaderState();
    window.addEventListener('scroll', updateHeaderState, { passive: true });
            



        </script>
</body>
</html>