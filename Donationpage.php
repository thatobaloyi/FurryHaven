<?php
// dashboard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once './models/VolunteerActivity.php';
include_once './core/functions.php';
include_once './notification.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$volunteerActivity = new VolunteerActivity();
$username = $_SESSION["username"];

// Handle delete
if (isset($_POST['del'])) {
    try {
        if (!$volunteerActivity->softDelete($_POST['ActivityID'])) {
            throw new Exception("Cannot delete!");
        } else {
            $_SESSION['notification'] = [
                'message' => "Activity Successfully deleted",
                'type' => 'success'
            ];
            // After deletion, recalculate stats below
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

$activities_result = $volunteerActivity->findByUsername($username);
$activities = [];
while ($row = $activities_result->fetch_assoc()) {
    $activities[] = $row;
}

// Calculate stats
$total_activities = count($activities);
$animals_helped = [];
foreach ($activities as $act) {
    $animals_helped[] = $act['AnimalID'];
}
$unique_animals_helped = count(array_unique($animals_helped));

// Calculate total duration using SQL for accuracy
$sql = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`duration`))) AS total_duration
        FROM `volunteeractivity` WHERE VolunteerID = '$username' AND isDeleted = 0";
$display_hours = $conn->query($sql)->fetch_assoc();

// Get all activities for the table
$ActivityQuery = "SELECT * FROM volunteeractivity WHERE VolunteerID = '$username' AND isDeleted = 0 ORDER BY Date";
$result01 = $conn->query($ActivityQuery);

// Check if volunteer data exists
if ($activities === false) {
    die("Volunteer data not found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
/* ===== GLOBAL ===== */
:root{
  --primary-color:#da7422; 
}

.dashboard-container {
  display: flex;
  min-height: 100vh;
}

/* ===== SIDEBAR (Glass + Blur + Spacing) ===== */
.sidebar {
  width: 80px;
  background: rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(12px);
  box-shadow: 2px 8px 24px rgba(0, 0, 0, 0.1);
  border-right: 1px solid rgba(31, 60, 116, 0.15);
  display: flex;
  flex-direction: column;
  justify-content: center;
  transition: width 0.4s ease;
  overflow: hidden;
  border-radius: 0 1rem 1rem 0;
  padding: 1rem 0;
}

.sidebar-content {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  overflow-y: hidden;
}

.top-links {
  flex-grow: 1;
  overflow-y: auto;
  list-style: none;
  padding: 0;
  margin: 0;
}

.bottom-links {
  flex-shrink: 0;
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar.expanded {
  width: 300px;
}

/* Logo */
.sidebar-header {
  flex-shrink: 0;
  text-align: center;
  padding: 1.5rem 0;
}

.sidebar-logo {
  width: 0;
  opacity: 0;
  transition: width 0.4s ease, opacity 0.4s ease;
}

.sidebar.expanded .sidebar-logo {
  width: 200px;
  opacity: 1;
  margin: 0 auto;
}

/* Separator line */
.sidebar-separator {
  height: 1px;
  background-color: rgba(31, 60, 116, 0.2);
  margin: 1rem 0;
}

/* Nav Links */
.nav-links li,
.bottom-links li {
  margin: 0.8rem 0;
  position: relative;
}

.nav-links a,
.bottom-links a {
  display: flex;
  align-items: center;
  padding: 0.8rem 1.5rem;
  text-decoration: none;
  color: #1f3c74;
  font-weight: 500;
  border-radius: 0.8rem;
  transition: background 0.3s, color 0.3s;
  position: relative;
}

.nav-links a:hover,
.bottom-links a:hover {
  background-color: rgba(218, 116, 34, 0.1);
  color: #da7422;
}

.nav-links a.active,
.bottom-links a.active {
  background-color: rgba(218, 116, 34, 0.15);
  color: #da7422;
  font-weight: bold;
}

/* Hover indicator line */
.nav-links a::after,
.bottom-links a::after {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  width: 4px;
  height: 100%;
  background-color: #da7422;
  opacity: 0;
  border-radius: 0 4px 4px 0;
  transition: opacity 0.3s;
}

.nav-links a:hover::after,
.nav-links a.active::after,
.bottom-links a:hover::after,
.bottom-links a.active::after {
  opacity: 1;
}

/* Icons */
.nav-links .icon,
.bottom-links .icon {
  font-size: 1.6rem;
  width: 36px;
  text-align: center;
  transition: transform 0.3s;
}

.nav-links a:hover .icon,
.bottom-links a:hover .icon {
  transform: translateX(4px);
}

/* Link text */
.nav-links .link-text,
.bottom-links .link-text {
  display: none;
  margin-left: 0.8rem;
  font-weight: 600;
}

.sidebar.expanded .link-text {
  display: inline;
}


/* ===== MAIN CONTENT ===== */
.main-content {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  background-color: #FFF8F0;
}
.foster > .main-content {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background-color: #FFF8F0;
}

.container{
  width: 100%;
}

/* ===== CARDS ===== */
.card {
  background: linear-gradient(135deg, #ffffff, #f1f6f2);
  border-radius: 1.2rem;
  padding: 2rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 16px 36px rgba(0, 0, 0, 0.12);
}

/* ===== BUTTONS ===== */
.btn-primary {
  background: transparent;
  border: 2px solid #da7422;
  color: #da7422;
  padding: 0.75rem 1.8rem;
  border-radius: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background: #da7422;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(218, 116, 34, 0.3);
}

/* ===== FOOTER ===== */
.footer {
  background-color: #114c8d;
  color: white;
  text-align: center;
  padding: 1.5rem;
  font-size: 0.95rem;
  letter-spacing: 0.5px;
  border-radius: 1rem 1rem 0 0;
}

.footer::before {
  content: '';
  display: block;
  height: 3px;
  width: 60px;
  margin: 0.4rem auto 0.6rem auto;
  background-color: #98b06f;
  border-radius: 2px;
}

.main-content> *:not(.dashboard-banner, .notification, #overlay, #animalPopup) {
  margin: 0em 5em 1em 5em;
}

.main-content{
  padding: 0em 0em 10em 0em;
}

table {
  margin: auto;
  border-collapse: collapse;
  border-spacing: 1px 5px;
  background-color: #FFF8F0;
  table-layout: fixed;
  font-family: 'Lexend', sans-serif;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 6px rgba(0, 0, 0, 0.1);
}

th {
  background-color: #003366;
  color: #FFF8F0;
  width: 14.28%;
  padding: 20px 35px;
  text-align: center;
  font-weight: 600;
  font-size: 0.95rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

td {
  background-color: white;
  padding: 20px 35px;
  color: #333;
  border-bottom: 1px solid #ddd;
  aspect-ratio: 1 / 1;
}

.event {
  background-color: #003366;
  color: #fff;
  font-size: 0.75rem;
  padding: 2px 6px;
  border-radius: 6px;
  margin-top: 4px;
  display: inline-block;
}

tr {
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

tr:hover {
  transform: translateY(-2px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

td img {
  border-radius: 6px;
}

a {
  color: #FF8C00;
  text-decoration: none;
  font-weight: bold;
}

a:hover {
  text-decoration: underline;
}

@media (max-width: 768px) {
  table,
  th,
  td {
    font-size: 0.9rem;
  }
}

.nav-links {
  height: 100vh;
  padding: 0;
  display: flex;
  list-style: none;
  flex-direction: column;
}

.alert {
  padding: 10px;
  margin-bottom: 20px;
  border: 1px solid transparent;
  border-radius: 4px;
}

.success {
  color: #3c763d;
  background-color: #dff0d8;
  border-color: #d6e9c6;
}

.error {
  color: #a94442;
  background-color: #f2dede;
  border-color: #ebccd1;
}

/* Animal Popup */
/* (Styles are commented out in original) */

/* Analytics Charts */
.analytics-charts {
  display: flex;
  justify-content: space-around;
  gap: 20px;
  margin: 20px 0;
  flex-wrap: wrap;
}

.analytics-charts canvas {
  max-width: 200px;
  max-height: 300px;
  margin: 20px auto;
}

.chart-box canvas {
  width: 100% !important;
  height: 100% !important;
}

.view-all-container {
  text-align: center;
  margin-top: 20px;
}

.view-all-btn {
  background: #2c3e50;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  font-size: 16px;
  cursor: pointer;
  transition: 0.3s;
}

.view-all-btn:hover {
  background: #34495e;
  transform: scale(1.05);
}

/* Sidebar */
#animalSearchInput {
  width: 100%;
  padding: 6px 10px;
  margin-bottom: 5px;
  border-radius: 4px;
  border: 1px solid #ccc;
  box-sizing: border-box;
}

#searchResults ul li:hover {
  background-color: #f1f1f1;
  cursor: pointer;
}

/* Highlight today's date */
table td.today b {
  background-color: #FF8C00;
  color: white;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  font-weight: bold;
  transition: transform 0.2s;
}

table td.today b:hover {
  transform: scale(1.2);
}

a {
  text-decoration: none !important;
}

/* MAIN CONTENT */
h1 {
  color: #003366;
  text-align: center;
  border-bottom: 4px solid #FF8C00;
  padding-bottom: 0.5rem;
  font-size: 2.0rem;
  font-family: 'Lexend', sans-serif;
}

blockquote {
  font-style: italic;
  text-align: center;
  color: #003366;
  font-size: 1.1rem;
  margin: 1.5rem 0;
}

body {
  margin: 0;
  font-family: 'Lexend', Arial, sans-serif;
  padding: 0;
  height: 100vh;
  color: #1f3c74;
  overflow-x: hidden;
  background: #f7f8f2;
}

p {
  margin-bottom: 1.5rem;
  line-height: 1.6;
}

section {
  margin-bottom: 100px;
}

/* GENERAL DASHBOARD STYLING */
.dashboard {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  padding: 2rem;
  margin-bottom: 3rem;
}

/* BANNER STYLING */
.dashboard-banner {
  background-image: url("./bannerdog.jpg");
  background-position: center center;
  background-repeat: no-repeat;
  background-size: cover;
  position: relative;
  width: 100%;
  height: 30vh;
  overflow: hidden;
  margin: 0;
  border-radius: 0;
}

/* Text Overlay */
.dashboard-banner .dashboard-welcome {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #f8f9fa;
  font-family: 'Lexend', sans-serif;
  font-size: 2rem;
  text-align: left;
  text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.5);
  line-height: 1.2;
}

.banner-text {
  position: absolute;
  top: 50%;
  left: 20px;
  transform: translateY(-50%);
  color: rgb(255, 255, 255);
  font-family: 'Lexend', sans-serif;
  font-size: 28px;
  font-weight: bold;
  text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.5);
}

/* NOTIFICATION BELL STYLING */
#notificationBell {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 1000;
  cursor: pointer;
}

#notificationBell i {
  font-size: 28px;
  color: rgb(230, 78, 31);
}

#notificationBell .badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: red;
  color: white;
  font-size: 12px;
  padding: 2px 6px;
  border-radius: 50%;
}

#notificationsDropdown {
  display: none;
  position: absolute;
  top: 40px;
  right: 0;
  background: white;
  border: 1px solid #ddd;
  width: 250px;
  max-height: 250px;
  overflow-y: auto;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  border-radius: 8px;
}

.notification-item {
  padding: 10px;
  border-bottom: 1px solid #eee;
}

.notification-item a {
  color: #2c3e50;
  text-decoration: none;
}

.notification-item.empty {
  text-align: center;
  color: #999;
}

/* ALL BUTTONS STYLING */
/* Button Container */
.view-all-container {
  text-align: center;
  margin-top: 1.5rem;
}

/* View All Button */
.view-all-btn {
  background: linear-gradient(135deg, rgb(233, 179, 119), rgb(42, 114, 197));
  color: #fff;
  font-size: 1rem;
  font-family: 'Lexend', sans-serif;
  padding: 12px 25px;
  border: none;
  border-radius: 25px;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.view-all-btn:hover {
  background: linear-gradient(135deg, #2e6eb5, rgb(243, 193, 128));
  transform: scale(1.05);
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
  border: none;
  transition: all 0.3s ease;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.action-btn:hover,
.search-filter-btn:hover {
  background-color: #0f2c4d;
}

.search-filter-btn:hover {
  background-color: rgb(152, 176, 111);
  transform: scale(1.05);
}

/* Inputs */
.search-box {
  padding: 10px 14px;
  border: 1px solid #18436e;
  border-radius: 12px;
  font-size: 14px;
  font-family: 'Lexend', sans-serif;
  color: #18436e;
  cursor: pointer;
  transition: 0.3s;
  background-color: #FFF8F0
}

.search-box:hover {
  border-color: #df7100;
}

.form-inline {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  margin: 1.5rem 0;
  flex-wrap: wrap;
}

.filter-select {
  padding: 10px 14px;
  border: 1px solid #18436e;
  border-radius: 12px;
  font-size: 14px;
  font-family: 'Lexend', sans-serif;
  background-color: #FFF8F0;
  color: #18436e;
  cursor: pointer;
  transition: 0.3s;
}

.filter-select:hover {
  border-color: #df7100;
}

.filter-select:focus {
  outline: none;
  border-color: #df7100;
  box-shadow: 0 0 5px #df7100;
}

.filter-select option {
  background-color: #f9f9f9;
  color: #18436e;
  font-family: 'Lexend', sans-serif;
  padding: 8px 10px;
}

.search-filter-btn {
  background-color: #98b06f;
  color: #fff;
  padding: 10px 20px;
  font-size: 1rem;
  font-family: 'Lexend', sans-serif;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.search-filter-btn:hover {
  background-color: rgb(152, 176, 111);
  transform: scale(1.05);
}

/* ADOPTION STATUS IN ANIMAL DATABASE */
/* status cell baseline */
.animals-section td.status {
  font-weight: 600;
}

/* pill-style badges */
.animals-section .status-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 0.9rem;
  line-height: 1;
  vertical-align: middle;
}

/* available */
.animals-section td.status.available .status-badge {
  color: rgb(38, 141, 79);
  background: #ecfdf5;
  border: 1px solidrgb(28, 255, 108);
}

/* adopted */
.animals-section td.status.adopted .status-badge {
  color: rgb(192, 62, 62);
  background: #fff1f2;
  border: 1px solidrgb(235, 25, 25);
}

/* CALENDER STYLING */
.calendar-section {
  margin-bottom: 20px;
}

/* Calendar Header */
.calendar-header {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 15px;
  padding: 15px 25px;
  border-radius: 16px;
  margin-bottom: 10px;
  font-weight: 600;
  color: #18436e;
}

/* Month + Year */
.calendar-header .month-year {
  font-size: 2.0rem;
  font-family: 'Lexend', sans-serif;
  font-weight: bold;
  color: #18436e;
  text-align: center;
  margin: 0;
  padding: 5px 0;
  border-bottom: 4px solid #FF8C00;
}

/* Navigation Arrows */
.calendar-header a {
  color: white;
  font-size: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 42px;
  height: 50px;
  border-radius: 50%;
  transition: all 0.25s ease;
  background: #ebaa61;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.calendar-header a:hover {
  background: linear-gradient(135deg, #FFB347, #DF7100);
  transform: scale(1.1) rotate(5deg);
}

/* Calendar Table */
.calendar-section table {
  border-collapse: collapse;
  table-layout: fixed;
  background: #f8f9fa;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  margin-top: 5px;
}

/* Weekday Headers */
.calendar-section th {
  background: #18436e;
  color: white;
  padding: 10px 0;
  font-weight: 600;
}

/* Table Cells */
.calendar-section td {
  width: 14.28%;
  aspect-ratio: 1 / 1;
  vertical-align: top;
  padding: 5px;
  text-align: left;
  cursor: pointer;
  position: relative;
  transition: background 0.2s;
  box-sizing: border-box;
  border-radius: 6px;
}

/* Hover Effect for Days */
.calendar-section td:hover {
  background: #FFE5B4;
}

/* Highlight Today */
.calendar-section td.today {
  color: white;
  font-weight: bold;
}

/* Events */
.calendar-section .event {
  color: white;
  font-size: 0.75rem;
  padding: 25px 25px;
  border-radius: 4px;
  margin-top: 2px;
  display: inline-block;
  max-width: 100%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Table spacing */
.calendar-section table {
  margin-top: 5px;
}

/* ANALYTICS STYLING */
.analytics-card {
  background: white;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.analytics-card:hover {
  transform: translateY(-4px);
  transition: 0.2s ease-in-out;
}

.analytics-section {
  position: relative;
}

.analytics-section::before {
  content: "";
  position: absolute;
  inset: 0;
  mix-blend-mode: multiply;
  border-radius: 12px;
}

.analytics-section * {
  position: relative;
}

/* POP UP STYLING */
/* (Styles are commented out in original) */

/* ALL FORMS STYLING */
/* (Styles are commented out in original) */

/* RESPONSIVE STYLING */
@media (max-width: 768px) {
  .dashboard-banner .dashboard-welcome {
    font-size: 1.5rem;
  }
}

@media only screen and (min-width: 970px) {
  .dashboard-banner {
    height: 60vh;
  }
}

.table-container {
  max-width: 950px;
  margin: 32px auto 40px auto;
  padding: 0 10px;
}

table {
  border-collapse: separate;
  border-spacing: 0;
  border-radius: 16px;
  overflow: hidden;
  margin: 24px auto 32px auto;
  box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}

/* Round the corners of thead and tbody */
thead tr:first-child th:first-child {
  border-top-left-radius: 16px;
}
thead tr:first-child th:last-child {
  border-top-right-radius: 16px;
}
tbody tr:last-child td:first-child {
  border-bottom-left-radius: 16px;
}
tbody tr:last-child td:last-child {
  border-bottom-right-radius: 16px;
}

th, td {
  text-align: left;
}

tr:hover {
  background: #f9f9f9;
  cursor: pointer;
}

button {
  margin-right: 6px;
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
}

.btn-view {
  background: #007bff;
  color: white;
}

/* Animal registration styles */
.registration-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.registration-form fieldset {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1.5rem;
    background-color: #f9f9f9;
}

.registration-form legend {
    font-weight: bold;
    color: #333;
    padding: 0 0.5rem;
}

.form-row {
    display: flex;
    gap: 1.5rem;
}

.form-column {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.registration-form label {
    font-weight: 500;
}

.registration-form input[type="text"],
.registration-form input[type="date"],
.registration-form select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

.cage-options {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.cage-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.registration-form button {
    align-self: center;
}
</style>
   
 <div class="main-content" id="mainContent">
</head>

<body>
    <div class="dashboard-container">

   
        <div class="main-content">
            <div class="header">
                <h1>Welcome, <br><?php echo htmlspecialchars($username); ?>!</h1>
                <div class="user-info">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($username); ?>" alt="User Avatar">
                    <span><?php echo htmlspecialchars($username); ?></span>
                </div>
            </div>

            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-header">
                        <h3>Total Hours</h3>
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat"><?php echo $display_hours['total_duration']; ?></div>
                    <p class="stat-desc">Total time volunteered</p>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Activities</h3>
                        <i class="fas fa-list-check"></i>
                    </div>
                  <div class="stat"><?php echo $total_activities; ?></div>
                    <p class="stat-desc">Activities participated in</p>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Animals Helped</h3>
                        <i class="fas fa-paw"></i>
                    </div>
                  <div class="stat"><?php echo $unique_animals_helped; ?></div>
                    <p class="stat-desc">Animals you've assisted</p>
                </div>
                
            </div>
            
            <div class="activity-section">
                <div class="section-header">
                    <h2>Recent Activities</h2>
                    <a href="new-activity.php" class="btn btn-accent">Log New Activity</a>
                </div>
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Search activities...">
                    <button class="btn">Search</button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Activity ID</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Duration</th>
                            <th>Animal ID</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result01->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ActivityID']); ?></td>
                                <td><?php echo htmlspecialchars($row['Date']); ?></td>
                                <td><?php echo htmlspecialchars($row['Description']); ?></td>
                                <td><?php echo htmlspecialchars($row['Duration']); ?></td>
                                <td><?php echo htmlspecialchars($row['AnimalID']); ?></td>
                                <td>
                                    <form method="post" action="dashboard.php" style="display:inline;">
                                        <input type="hidden" name="ActivityID" value="<?php echo htmlspecialchars($row['ActivityID']); ?>">
                                        <button type="submit" name="del" class="btn" style="background-color: #e74c3c;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>