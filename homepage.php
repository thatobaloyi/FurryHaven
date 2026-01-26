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
          WHERE a.isDeleted = 0 and (a.Animal_Vacc_Status = 'Vaccinated' or a.isSpayNeutered = 1)";
$result = mysqli_query($conn, $query);

if (!$result) {
  die("Database query failed: " . mysqli_error($conn));
}

$boardingHref = isset($_SESSION['username'])
  ? 'boarding.php'
  : 'login.php?redirect=boarding.php';

$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
$preferredName = $isLoggedIn ? $_SESSION['preferredName'] : '';


include_once __DIR__ . '/models/Donation.php';
$donationModel = new Donation();
$topDonations = $donationModel->leaderboard(10); // Top 10 donations
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FurryHaven - Home</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <style>
    /*General Styles and CSS Variables*/
    :root {
      --primary-color: #003366;
      /* Dark Blue */
      --secondary-color: #FF8C00;
      /* Orange */
      --background-color: #f8f4e9;
      /* Light Beige */
      --light-text: #f8f8f8;
      /* Off-White */
    }

    h1 {
      text-align: center;
      color: var(--primary-color);
      /* system blue */
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
      height: 75px;
      /* <-- shorter (was 72px) */
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 8px 24px;
      /* <-- reduced padding */
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
      border-bottom: 1px solid rgba(0, 0, 0, 0.03);
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


    .nav-links>li>a:hover {
      color: #df7100;
      background: none;
    }


    .dropdown-content {
      position: absolute;
      top: 120%;
      left: 0;
      background: #FFF8F0;
      min-width: 180px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
      box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
      border-bottom: 1px solid rgba(0, 0, 0, 0.06);
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
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
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
      margin-bottom: 0;
      /* remove any bottom margin */
      padding-bottom: 0;
      /* remove any bottom padding */
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
      top: 40%;
      /* Vertical center */
      left: 30%;
      /* Horizontal center */
      transform: translate(-50%, -50%);
      /* Perfect centering */
      text-align: center;
      /* Center text */
      max-width: 80%;
      /* Prevent it from being too wide */
      z-index: 5;
      color: #fff;
      /* Make sure text is readable */
      font-family: 'Segoe UI', sans-serif;
    }

    /* Heading */
    .carousel-caption.caption-slide1 h2 {
      font-size: clamp(36px, 5vw, 60px);
      /* Responsive size */
      font-weight: 800;
      margin-bottom: 12px;
      line-height: 1.2;
      border-bottom: 4px solid #FF8C00;
      /* Orange line */
      display: inline-block;
      /* So the line matches the text width */
      padding-bottom: 6px;
    }

    /* Subtext */
    .carousel-caption.caption-slide1 p {
      font-size: clamp(18px, 2.5vw, 24px);
      /* Responsive size */
      line-height: 1.4;
    }

    .slide3-caption {
      position: absolute;
      top: 50%;
      /* Vertical center */
      left: 20%;
      /* Horizontal center */
      transform: translate(-50%, -300%);
      /* Perfect centering */
      text-align: center;
      z-index: 5;
      max-width: 80%;
      /* Prevent text from being too wide */
      font-family: 'Segoe UI', sans-serif;
      color: #fff;
    }

    .slide3-caption .carousel-quote {
      font-size: clamp(36px, 5vw, 60px);
      /* Responsive heading size */
      font-weight: 800;
      display: inline-block;
      /* Keeps text width compact */
      margin-bottom: 20px;
      /* Space between quote and button */
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
      width: 280px;
      /* sidebar width */
      height: auto;
      background-color: #FFF8F0;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
      box-shadow: 4px 0 12px rgba(0, 0, 0, 0.3);
      z-index: 1000;
    }

    /* Logo */
    .dropdown-menu-logo {
      text-align: center;
      padding: 20px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
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
      color: #18436e;
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
      background-color: rgba(255, 140, 0, 0.7);
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
    /* --- Adopt, Volunteer, and Donate Cards --- */

    /* Base styling */
    body {
      font-family: Arial, sans-serif;
      background-color: #fdf4e6;
      /* beige background */
      margin: 0;
      padding: 0;
    }

    /* Container for the three cards */
    .cards {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 200px;
      /* space between cards */
      margin: 80px auto;
      max-width: 1600px;
      flex-wrap: nowrap;
      padding: 20px;
      box-sizing: border-box;
    }

    /* Individual card container */
    .card {
      background: none;
      width: 400px;
      height: 350px;
      perspective: 1000px;
      flex-shrink: 0;
      box-sizing: border-box;
      border: none;
      outline: none;
    }

    /* Inner container for flip effect */
    .card-inner {
      position: relative;
      width: 100%;
      height: 100%;
      transition: transform 0.8s ease;
      transform-style: preserve-3d;
      background-color: transparent;
      will-change: transform;
      border: none;
    }

    /* Flip animation on hover */
    .card:hover .card-inner {
      transform: rotateY(180deg);
    }

    /* Front and back sides */
    .card-front,
    .card-back {
      position: absolute;
      width: 100%;
      height: 100%;
      border-radius: 15px;
      color: #fff;
      backface-visibility: hidden;
      -webkit-backface-visibility: hidden;
      /* Safari/Chrome fix */
      padding: 25px;
      text-align: center;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      display: flex;
      flex-direction: column;
      justify-content: center;
      border: none;
      outline: none;
    }

    /* Back side rotation */
    .card-back {
      transform: rotateY(180deg);
      font-size: 0.95em;
      line-height: 1.5;
      background-color: rgb(228, 178, 109);
    }

    /* Card color themes */
    .adopt .card-front,
    .adopt .card-back {
      background-color: #1b3a61;
    }

    .donate .card-front,
    .donate .card-back {
      background-color: #e79e36;
    }

    .volunteer .card-front,
    .volunteer .card-back {
      background-color: #7b925d;
    }

    /* Icons */
    .card i {
      margin-bottom: 15px;
      font-size: 5em;
    }

    .icon1 {
      color: #e79e36;
    }

    .icon2 {
      color: rgb(238, 207, 163);
    }

    .icon3 {
      color: rgb(89, 116, 53);
    }

    /* Text styling */
    .card h2 {
      font-size: 3em;
      margin-bottom: 10px;
      font-weight: bold;
      color: white;
    }

    .card p {
      color: #f8f8f8;
      font-size: 2em;
      line-height: 1.5;
    }

    /* Responsive layout */
    @media (max-width: 900px) {
      .cards {
        flex-wrap: wrap;
      }

      .card {
        width: 90%;
        margin-bottom: 20px;
      }
    }

    /* --- 3R's of Pawsitivity Section --- */

    .hope-cards {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 200px;
      margin: 80px auto;
      max-width: 1600px;
      flex-wrap: nowrap;
      padding: 20px;
      box-sizing: border-box;
    }

    .card-front {
      font-family: 'Segoe UI', sans-serif;
    }

    /* Colors for 3R's section */
    .card.rescued .card-front,
    .card.rescued .card-back {
      background-color: #1b3a61;
    }

    .card.rehabilitated .card-front,
    .card.rehabilitated .card-back {
      background-color: #e79e36;
    }

    .card.rehomed .card-front,
    .card.rehomed .card-back {
      background-color: #7b925d;
    }

    /* Counter text */
    .counter-value {
      font-size: 6em;
      font-weight: 700;
      margin-top: 10px;
    }

    /* Remove outlines globally (fix for black borders) */
    .card,
    .card * {
      outline: none;
    }









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
      background: rgba(0, 0, 0, 0.5);
      /* dark overlay so text pops */
    }

    /* Hero content */
    .hero-content {
      position: relative;
      z-index: 1;
      max-width: 700px;
      padding: 2rem;
      color: white;
      /* makes all text inside white */
    }

    /* Headings */
    .hero h1 {
      font-size: 3rem;
      margin-bottom: 1rem;
      color: white;
      /* ensures H1 is white */
    }

    /* Paragraph */
    .hero p {
      font-size: 1.7 rem;
      margin-bottom: 2rem;
      color: white;
      /* ensures paragraph is white */
    }

    /* Button */
    .btn-primary {
      display: inline-block;
      padding: 12px 30px;
      background-color: #df7100;
      /* orange */
      color: white;
      /* button text white */
      font-weight: bold;
      border-radius: 8px;
      transition: background 0.3s;
      text-decoration: none;
    }

    .btn-primary:hover {
      background-color: #b85500;
      /* darker orange on hover */
      text-decoration: none;
      /* ensures no underline on hover */
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

      .r-of-hope,
      .breaking-the-chain,
      .champions,
      footer {
        grid-template-columns: 1fr;
      }

      .footer-logo,
      .contact-info,
      .copyright {
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

    .adoptable-section {
      padding: 60px 20px;
    }

    .section-header h1 {
      text-align: center !important;
      font-size: clamp(36px, 5vw, 60px) !important;
      font-weight: 900 !important;
      margin-bottom: 5px !important;
      letter-spacing: 1px !important;
      color: #1f3c74 !important;
    }

    /* --- Slider Styling --- */
    .slider-container {
      position: relative !important;
      overflow: hidden !important;
      padding: 0 20px !important;
      border: none !important;

    }

    .animal-slider {
      display: flex;
      gap: 25px;
      transition: transform 0.5s ease-in-out;
      border: none;
    }

    .animal-btn {
      border: none;
      border-radius: 20px;
      overflow: hidden;
      cursor: pointer;
      width: 100%;
      transition: transform 0.3s ease, box-shadow 0.3s ease, backdrop-filter 0.3s ease;
      display: flex;
      flex-direction: column;
      background-color: #fff8f0;

    }


    /* --- View More --- */
    .btn-secondary {
      display: inline-block !important;
      background: #da7422 !important;
      color: white !important;
      padding: 12px 30px !important;
      border-radius: 10px !important;
      font-weight: 600 !important;
      font-size: 1.1rem !important;
      transition: transform 0.3s ease, background 0.3s ease !important;
      text-decoration: none !important;
    }

    .view-more {
      display: flex !important;
      justify-content: center !important;
      /* centers the button horizontally */
      margin-top: 50px !important;
    }

    .btn-secondary:hover {
      background: #1f3c74 !important;
      transform: scale(1.05) !important;
    }


    /* --- Slider Controls --- */
 /* ==== SLIDER STYLING ==== */
  .slider-container {
    position: relative;
    width: 100%;
    overflow: hidden;
    margin: 40px auto;
    max-width: 1200px;
  }

  .animal-slider {
    display: flex;
    transition: transform 1s ease-in-out;
  }

  .slide {
    flex: 0 0 25%; /* 4 per row desktop */
    box-sizing: border-box;
    padding: 10px;
    text-align: center;
  }

  @media (max-width: 1024px) {
    .slide { flex: 0 0 33.33%; } /* 3 per row */
  }
  @media (max-width: 768px) {
    .slide { flex: 0 0 50%; } /* 2 per row */
  }
  @media (max-width: 480px) {
    .slide { flex: 0 0 100%; } /* 1 per row */
  }

  .animal-btn {
    background: #fff;
    border: none;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    width: 100%;
  }

  .animal-btn:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
  }

  .animal-image img {
    width: 100%;
    height: 220px;
    object-fit: cover;
  }

  .animal-info {
    padding: 10px 0;
  }

  .animal-name {
      font-size: 2rem !important;
      font-weight: 700 !important;
      color: #1f3c74 !important;
      margin-bottom: 5px !important;
    }

.animal-breed {
      font-size: 1.5rem !important;
      color: #1f3c74 !important;
    }

  .slider-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 15px;
  }

  .slider-dots {
    display: flex;
    gap: 8px;
  }

  .dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #ccc;
    transition: background-color 0.3s;
  }

   .dot.active {
      background: #da7422;
      transform: scale(1.3);
    }

   .btn-secondary {
      display: inline-block !important;
      background: #da7422 !important;
      color: white !important;
      padding: 12px 30px !important;
      border-radius: 10px !important;
      font-weight: 600 !important;
      font-size: 1.1rem !important;
      transition: transform 0.3s ease, background 0.3s ease !important;
      text-decoration: none !important;
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
      color: #ff8c00;
      /* highlight username */
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

    main {
      margin-top: 0;
      /* remove top margin */
      padding-top: 0;
      /* remove top padding */
    }

    /* Sidebar Styling (as provided previously, for context) */
    .sidebar {
      display: none;
      /* Hidden by default */
      position: fixed;
      top: 0;
      right: 0;
      height: 100%;
      width: 250px;
      background-color: var(--primary-color);
      color: var(--light-text);
      box-shadow: -4px 0 12px rgba(0, 0, 0, 0.2);
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
        display: none;
        /* Hide main nav on small screens */
      }

      .hamburger {
        display: block;
        /* Show hamburger */
      }

      .user-profile {
        margin-left: 15px;
        /* Reduce margin */
      }

      .sidebar {
        display: block;
        /* Show sidebar when toggled by JS */
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
      text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.6);
    }



    .icon-circle {
      width: 150px;
      height: 130px;
      border-radius: 50%;
      background: var(--secondary-color);
      /* white circle */
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
      box-shadow: 0 6px 14px rgba(0, 0, 0, 0.1);
    }

    .boarding-image-container img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .boarding-caption {
      position: absolute;
      top: 50%;
      /* vertically center */
      left: 5%;
      /* move to left side */
      transform: translateY(-50%);
      /* only adjust vertical */
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
      width: 450px;
      /* adjust size */
      height: auto;
      border-radius: 10px;
      /* keeps corners slightly soft */
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .volunteer-text {
      flex: 1;
    }

    .volunteer-text h2 {
      font-size: 2rem;
      color: #003366;
      /* dark blue similar to your example */
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
      height: 400px;
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
      color: white;
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

      .banner-left,
      .banner-right {
        flex: none;
        width: 100%;
        height: 300px;
      }

      .banner-right {
        padding: 20px;
      }
    }



    .leaderboard-wrapper {
      width: 100%;
      max-width: 1200px;
      margin: 2em auto 3em auto;
      border-radius: 18px;
      padding: 32px 24px 24px 24px;
      overflow-x: auto;
      margin-right: -325px;
      /* moves it further right */
    }

    .leaderboard-header {
      text-align: center;
      margin-bottom: 9em;
    }

    .leaderboard-text {
      margin-left: -350px;
      /* moves it further left */
    }

    .leaderboard-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 0.5em;
      font-size: 1.1em;
      background: transparent;
    }

    .leaderboard-table th {
      background: #18436e;
      color: white;
      font-weight: 700;
      padding: 14px 10px;
      text-align: left;
      font-size: 1.5em;
      letter-spacing: 0.5px;
    }

    .leaderboard-row {
      background: #fff;
      transition: box-shadow 0.2s, transform 0.2s;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .leaderboard-row:hover {
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.10);
      transform: scale(1.01);
    }

    .leaderboard-row td {
      padding: 16px 12px;
      vertical-align: middle;
      border: none;
      font-size: 1.08em;
    }

    .leaderboard-row.gold {
      background: linear-gradient(90deg, #fffbe6 60%, #ffe082 100%);
      font-weight: 900;
    }

    .leaderboard-row.silver {
      background: linear-gradient(90deg, #f8fafd 60%, #b0bec5 100%);
      font-weight: 900;
    }

    .leaderboard-row.bronze {
      background: linear-gradient(90deg, #fff3e0 60%, #ffb74d 100%);
      font-weight: 900;
    }

    .medal {
      font-size: 1.6em;
      vertical-align: middle;
    }

    .rank-num {
      font-size: 1.5em;
      color: #18436e;
      font-weight: 600;
    }

    .donor-name {
      color: #18436e;
      font-weight: 900;
      letter-spacing: 0.5px;
    }

    .donation-amount {
      color: #da7422;
      font-weight: 900;
      font-size: 1.1em;
      letter-spacing: 0.5px;
    }

    .donation-date {
      color: #18436e;
      font-size: 1em;
    }

    .donation-leaderboard-section {
      padding: 80px 20px;
      background: #fdf6ed;
    }

    .leaderboard-container {
      display: flex;
      /* max-width: 1200px; */
      margin: 0 45em;
      gap: 40px;
      flex-wrap: wrap;
    }

    /* Left side text */
    .leaderboard-text {
      flex: 1;
      min-width: 280px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 40px;
    }

    .leaderboard-text h2 {
      font-size: clamp(25px, 4vw, 50px) !important;
      color: #DA7422;
      font-weight: 900;
    }


    .leaderboard-text p {
      font-size: 2.5em;
      color: #1F3C74;
      line-height: 1.6;
    }

    p{
      font-size: 2em !important;
    }

    .donate-btn {

      background: #DA7422;
      color: #fff;
      padding: 20px 70px;
      font-size: 2em;
      font-weight: 600;
      border-radius: 30px;
      text-decoration: none;
      transition: all 0.3s ease;
      margin: 0 auto;
      display: block;
    }

    .donate-btn:hover {
      background: #1F3C74;
      color: #fff;
      transform: scale(1.05);
      text-decoration: none;
    }

    /* Right side table (keep your current styling mostly) */
    .leaderboard-wrapper {
      flex: 1;
      min-width: 320px;
      background: linear-gradient(90deg, #fff8f0 60%, #ffe0b2 100%);
      border-radius: 18px;
      box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
      padding: 32px 24px 24px 24px;
      overflow-x: auto;
    }

    .leaderboard-header {
      text-align: center;
      margin-bottom: 1.5em;
    }

    .leaderboard-header h2 {
      color: #18436e;
      font-size: 2.2em;
      font-weight: 800;
      margin-bottom: 0.2em;
      letter-spacing: 1px;
    }




    /* Responsive */
    @media (max-width: 1024px) {
      .leaderboard-container {
        flex-direction: column;
        gap: 40px;
      }
    }










    .report-cruelty-slant {
      width: 100%;
      overflow: hidden;
      padding: 0;
      display: flex;
      justify-content: center;
      height: 600px;
      /* set your desired height */
    }

    .slant-card {
      display: flex;
      width: 100%;
      margin: 0;
      background: transparent;
      /* no solid color */
      clip-path: polygon(0 5%, 100% 0, 100% 95%, 0% 100%);
      overflow: visible;
      /* allow transparency to show outside card if needed */
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }





    .slant-card:hover {
      transform: translateY(-5px);
    }

    .slant-card .text {
      flex: 1;
      padding: 50px;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: #DA7422;
      /* only behind text, not behind image */
    }


    .slant-card .text h2 {
      font-size: 4em;
      margin-bottom: 40px;
    }

    .slant-card .text p {
      font-size: 1.5em;
      line-height: 1.6;
      margin-bottom: 30px;
    }

    .report-btn {
      /* display: inline; */
      background: #fff;
      color: #DA7422;
      padding: 15px 20px;
      border-radius: 50px;
      font-weight: 600;
      width: 200px;
      text-decoration: none !important;
      transition: all 0.3s ease;


    }

    .report-btn:hover {
      background: #1F3C74;
      color: white;
      transform: scale(1.05);
    }

    .slant-card .image {
      flex: 1;
      background: #DA7422;
      display: flex;
      justify-content: flex-end;
      align-items: center;
      order: 2;
      /* moves to right */
    }

    .slant-card .image img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      filter: brightness(60%);
    }


    .slant-card .image:hover img {
      transform: scale(1.05);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .slant-card {
        flex-direction: column;
        clip-path: none;
      }


      .text{
        display: flex !important;
        flex-direction: column !important;;
        justify-content: center !important;;
      }
      
      .slant-card, .text
      .slant-card .image {
        align-items: center;
        width: 100%;
        padding: 30px;
      }
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
        <circle cx="12" cy="7" r="5" />
        <path d="M2 21c0-5.5 4.5-10 10-10s10 4.5 10 10" />
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
              <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] !== 'Guest'): ?>
                <a href="dashboard2.php">
                  <img src="./logo.png" alt="FurryHaven Logo">
                </a>
              <?php else: ?>
                <img src="./logo.png" alt="FurryHaven Logo">
              <?php endif; ?>
            </div>

            <!-- User Info -->
            <div class="dropdown-menu-header">
              <h3>Welcome Back,</h3>
              <p> <?php echo isset($preferredName) ? htmlspecialchars($preferredName) : htmlspecialchars($username); ?>!</p>
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
        window.addEventListener('scroll', updateHeaderState, {
          passive: true
        });
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
      <div class="carousel-item active" style="height: 700px;"> <!-- adjust height -->
        <img src="bannerdog.jpg"
          class="d-block w-100 h-100"
          style="object-fit: cover; object-position: center;"
          alt="Dog 1">
        <div class="carousel-caption caption-slide1">
          <h2>Welcome to FurryHaven!</h2>
          <p>Supporting Makhanda’s animals, one paw at a time.</p>
        </div>
      </div>


      <!-- Slide 3 with quote + button -->
      <div class="carousel-item" style="height: 700px;"> <!-- adjust height -->>
        <img src="image7.jpg"
          class="d-block w-100 h-100"
          style="object-fit:cover; object-position: center;"
          alt="Slide 3">
        <div class="carousel-caption slide3-caption">

          <span class="carousel-quote">Help Us, Help Our Furry Friends</span><br>
          <a href="cruelty.php" class="report-btn mt-3">Report Cruelty</a>
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

      <!-- Cards Section -->


      <section class="cards">

        <a href="adoptable.php">
          <div class="card adopt">
            <div class="card-inner">
              <div class="card-front">
                <i class="fa-solid fa-paw icon1"></i>
                <h2>ADOPT</h2>
              </div>
              <div class="card-back">
                <p>Meet our adorable, adoptable furry friends</p>
              </div>
            </div>
          </div>
        </a>

        <a href="campaignpage.php">
          <div class="card donate">
            <div class="card-inner">
              <div class="card-front">
                <i class="fa-solid fa-gift icon2"></i>
                <h2>DONATE</h2>
              </div>
              <div class="card-back">
                <p>Support our mission and help animals in need. Every contribution saves lives and spreads pawsitivity!</p>
              </div>
            </div>
          </div>
        </a>

        <a href="volunteerpage.php">
          <div class="card volunteer">
            <div class="card-inner">
              <div class="card-front">
                <i class="fa-solid fa-hand-holding-heart icon3"></i>
                <h2>VOLUNTEER</h2>
              </div>
              <div class="card-back">
                <p>Join our team for a few hours or a longer project and make a difference in the lives of the animals.</p>
              </div>
            </div>
          </div>
        </a>
      </section>


      <section class="adoptable-section">
        <div class="section-header">
          <div class="line"></div>
          <h1>Find Your Fur-ever Friend</h1>
          <div class="line"></div>
          <br><br>
        </div>


        <div class="slider-container">
          <div class="animal-slider">
            <?php
            $count = 0;
            while ($row = mysqli_fetch_assoc($result)) {
              if ($count >= 20) break;
            ?>
              <div class="slide">
                <form action="adoptable2.php" method="POST">
                  <input type="hidden" name="Animal_ID" value="<?php echo $row['Animal_ID']; ?>">
                  <button type="submit" class="animal-btn">
                    <div class="animal-image">
                      <img src="./images/animals/<?php echo htmlspecialchars($row['filePath']); ?>" alt="<?php echo htmlspecialchars($row['Animal_Name']); ?>">
                    </div>
                    <div class="animal-info">
                      <h2 class="animal-name"><?php echo htmlspecialchars($row['Animal_Name']); ?></h2>
                      <p class="animal-breed"><?php echo htmlspecialchars($row['Animal_Breed']); ?></p>
                    </div>
                  </button>
                </form>
              </div>
            <?php
              $count++;
            }
            ?>
          </div>
        </div>

        <div class="slider-controls">

          <div class="slider-dots" id="slider-dots"></div>

        </div>

        <div class="view-more">
          <a href="adoptable.php" class="btn-secondary">See All Animals</a>
        </div>
      </section>

      <!-- Volunteer Section End -->
      <section class="report-cruelty-slant">
        <div class="slant-card">
          <div class="text">
            <h2>Report Animal Cruelty</h2>
            <p>
              Every voice matters. Don’t stay silent — help us protect innocent lives and give hope to animals in need.
            </p>
            <a href="cruelty.html" class="report-btn">Report Now</a>
          </div>
          <div class="image">
            <img src="crueltyimage2.png" alt="Sad dog">
          </div>
        </div>
      </section><br><br>





      <!-- Analyzing the 3 R's of Hope section -->


      <?php
      $sql1 = "SELECT COUNT(*) AS total FROM animal WHERE isDeleted = 0";
      $sql2 = "SELECT COUNT(*) AS total FROM animal WHERE isDeleted = 0 AND Animal_Vacc_Status = 'Vaccinated'";
      $sql3 = "SELECT COUNT(*) AS total FROM adoption"; #WHERE isDeleted = 0 AND outtakeType = 'Adoption'

      $result1 = mysqli_query($conn, $sql1);
      $result2 = mysqli_query($conn, $sql2);
      $result3 = mysqli_query($conn, $sql3);

      $row1 = mysqli_fetch_assoc($result1);
      $row2 = mysqli_fetch_assoc($result2);
      $row3 = mysqli_fetch_assoc($result3);
      ?>
      <section class="hope-section">
        <div class="hope-header" style="text-align: center; margin-bottom: 60px;">



          <h1 style="font-size:clamp(36px, 5vw, 60px); color: #1f3c74; font-weight: 900; letter-spacing: 1px;">
            Spreading Pawsitivity
          </h1><br>
          <p style="font-size: 3em; color: #1F3C74; margin-top: 10px;">
            One paw at a time
          </p>
        </div>
        <section class="cards hope-cards">
          <!-- Rescued -->
          <div class="card rescued">
            <div class="card-inner">
              <div class="card-front">
                <i class="fa-solid fa-paw icon1"></i>
                <h2>RESCUED</h2>
                <div class="counter-value" data-target="<?php echo $row1['total']; ?>">0</div>
              </div>
              <div class="card-back">
                <p>All the animals we’ve rescued from harm and neglect.</p>
              </div>
            </div>
          </div>

          <!-- Rehabilitated -->
          <div class="card rehabilitated">
            <div class="card-inner">
              <div class="card-front">
                <i class="fa-solid fa-stethoscope icon2"></i>
                <h2>REHABILITATED</h2>
                <div class="counter-value" data-target="<?php echo $row2['total']; ?>">0</div>
              </div>
              <div class="card-back">
                <p>Animals that have been treated, healed, and cared for by our team.</p>
              </div>
            </div>
          </div>

          <!-- Rehomed -->
          <div class="card rehomed">
            <div class="card-inner">
              <div class="card-front">
                <i class="fa-solid fa-house icon3"></i>
                <h2>REHOMED</h2>
                <div class="counter-value" data-target="<?php echo $row3['total']; ?>">0</div>
              </div>
              <div class="card-back">
                <p>Animals that have found loving, permanent homes through our efforts.</p>
              </div>
            </div>
          </div>
        </section>







        <!-- End of 3 R's of Hope section -->




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





        <script>
          const counters = document.querySelectorAll('.counter-value');

          counters.forEach(counter => {
            counter.innerText = '0';

            const updateCounter = () => {
              const target = +counter.getAttribute('data-target');
              const c = +counter.innerText;

              const duration = 3000; // total animation duration in ms
              const stepTime = 20; // how often to update (ms)
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




      <!--Volunteer Section Start -->
      <section class="hero">
        <div class="hero-content">
          <h1>Support Our Cause</h1>
          <p>Together, we can give every animal a second chance at a loving home.</p>
          <a href="volunteerpage.php" class="btn-primary">Volunteer Now</a>
        </div>
      </section>
    </main>



    <!--Volunteer Section End -->

    <section class="donation-leaderboard-section">
      <div class="leaderboard-container">

        <!-- Left side: text + button -->
        <div class="leaderboard-text">
          <h2>Congratulations to Our Top Donors!</h2>
          <p>
            These amazing supporters are spreading pawsitivity and making a difference every day.
            Join the race to help our furry friends and leave your paw print on their lives.
          </p>
          <a href="my_donations.php" class="donate-btn">Donate Now</a>
        </div>

        <!-- Right side: leaderboard table -->
        <div class="leaderboard-wrapper">
          <div class="leaderboard-header">
            <h2>Donation Leaderboard</h2>
          </div>
          <table class="leaderboard-table">
            <thead>
              <tr>
                <th style="width:80px;">#</th>
                <th>Donor</th>
                <th>Amount</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php $rank = 1; ?>
              <?php while ($donation = $topDonations->fetch_assoc()): ?>
                <tr class="leaderboard-row <?php echo $rank == 1 ? 'gold' : ($rank == 2 ? 'silver' : ($rank == 3 ? 'bronze' : '')); ?>">
                  <td>
                    <?php if ($rank == 1): ?>
                      <span class="medal gold">🥇</span>
                    <?php elseif ($rank == 2): ?>
                      <span class="medal silver">🥈</span>
                    <?php elseif ($rank == 3): ?>
                      <span class="medal bronze">🥉</span>
                    <?php else: ?>
                      <span class="rank-num"><?php echo $rank; ?></span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="donor-name"><?php echo htmlspecialchars($donation['DonorID']); ?></span>
                  </td>
                  <td>
                    <span class="donation-amount">R<?php echo number_format($donation['TotalDonated'], 2); ?></span>
                  </td>
                  <td>
                    <span class="donation-date"><?php echo date('d M Y', strtotime($donation['LastDonation'])); ?></span>
                  </td>
                </tr>
                <?php $rank++; ?>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

      </div>
    </section>







    <!-- Footer -->
    <footer>
      <div class="footer-container">

        <!-- Contact -->
        <div class="footer-links">
          <h4>Stay in touch</h4>
          <p>Tel: 082 770 2667</p>
          <p>Email: furryhavendonations@gmail.com</p>
        </div>

        <!-- Support -->
        <div class="footer-links">
          <h4>Support</h4>
          <ul>
            <li><a href="reportcruelty2.php">Report abuse</a></li>
            <li><a href="campaignpage.php">Donate</a></li>
            <li><a href="volunteerpage.php">Volunteer</a></li>
            <li><a href="#">Privacy policy</a></li>
          </ul>
        </div>

        <!-- Company -->
        <div class="footer-links">
          <h4>Company</h4>
          <ul>
            <li><a href="adoptable.php">Adopt a dog</a></li>
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

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Footer -->

  <script>
    var myCarousel = document.querySelector('#furryhavenBanner');
    var carousel = new bootstrap.Carousel(myCarousel, {
      interval: 3000, // 3 seconds
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



    const profileIcon = document.querySelector
      profileIcon.classList.toggle('active');
    });

    // Optional: close dropdown if you click outside
    document.addEventListener('click', (e) => {
      if (!profileIcon.contains(e.target)) {
        profileIcon.classList.remove('active');
      }
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
    window.addEventListener('scroll', updateHeaderState, {
      passive: true
    });
  </script>
  <script src="/assets/profile-toggle.js"></script>
</body>

</html>