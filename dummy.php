<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Database Connection ---
require('config/databaseconnection.php');


include_once __DIR__ . '/models/Donor.php';
include_once __DIR__ . '/models/Dononation.php';



// dashboard.php

// Redirect to login if not authenticated
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}



$donor = new Donor();
$username = $_SESSION["username"];

// Get donor data for the logged-in user
$result = $donor->findAll();
$donorData = $result->fetch_assoc();

if (!$donorData) {
    die("No donor data found for this user.");
}

// Example: show donor info
echo "Welcome, " . htmlspecialchars($donorData['FullName']);
echo "<br>Email: " . htmlspecialchars($donorData['Email']);
echo "<br>Total Donations: " . htmlspecialchars($donorData['TotalDonations']);
