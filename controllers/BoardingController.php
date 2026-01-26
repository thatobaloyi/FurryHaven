<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include_once __DIR__ . '/../models/BoardingBooking.php';
include_once __DIR__ . '/../models/BoardingAnimals.php';
include_once __DIR__ . '/../models/BoardingPayments.php';
include_once __DIR__ . '/../core/functions.php';

class BoardingController
{
    private $booking;

    public function __construct()
    {
        $this->booking = new BoardingBooking();
    }

    // Accept optional params OR fallback to POST fields (boardBookID / boardingAnimalID / cageNumber)
    public function checkIn($id = null, $anid = null, $cage = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id    = $id    ?? (isset($_POST['boardBookID']) ? sanitizeInput($_POST['boardBookID']) : (isset($_POST['booking_id']) ? sanitizeInput($_POST['booking_id']) : ''));
            $anid  = $anid  ?? (isset($_POST['boardingAnimalID']) ? sanitizeInput($_POST['boardingAnimalID']) : (isset($_POST['animal_id']) ? sanitizeInput($_POST['animal_id']) : ''));
            $cage  = $cage  ?? (isset($_POST['cageNumber']) ? sanitizeInput($_POST['cageNumber']) : (isset($_POST['cage_id']) ? sanitizeInput($_POST['cage_id']) : ''));

            if ($this->booking->checkIn($id, $anid, $cage)) {
                $_SESSION['notification'] = ['message' => 'Animal checked in successfully.', 'type' => 'success'];
            } else {
                $_SESSION['notification'] = ['message' => 'Error checking in animal.', 'type' => 'error'];
            }
            redirectTo("./approvedboarding2.php");
        }
    }

    public function checkOut($id = null, $anid = null, $cage = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id    = $id    ?? (isset($_POST['boardBookID']) ? sanitizeInput($_POST['boardBookID']) : (isset($_POST['booking_id']) ? sanitizeInput($_POST['booking_id']) : ''));
            $anid  = $anid  ?? (isset($_POST['boardingAnimalID']) ? sanitizeInput($_POST['boardingAnimalID']) : (isset($_POST['animal_id']) ? sanitizeInput($_POST['animal_id']) : ''));
            $cage  = $cage  ?? (isset($_POST['cageNumber']) ? sanitizeInput($_POST['cageNumber']) : (isset($_POST['cage_id']) ? sanitizeInput($_POST['cage_id']) : ''));

            if ($this->booking->checkOut($id, $anid, $cage)) {
                $_SESSION['notification'] = ['message' => 'Animal checked out successfully.', 'type' => 'success'];
            } else {
                $_SESSION['notification'] = ['message' => 'Error checking out animal.', 'type' => 'error'];
            }
            redirectTo("./approvedboarding2.php");
        }
    }

    public function displayAllBookings()
    {
        return $this->booking->showAllBookings();
    }

    public function displayActiveBookings()
    {
        return $this->booking->showActiveBookings();
    }

    public function displayUpcomingBookings() {
        return $this->booking->getUpcomingBookings();
    }

    public function displayUserActiveBookings($username) {
        return $this->booking->getUserActiveBookings($username);
    }

    public function displayUserUpcomingBookings($username) {
        return $this->booking->getUserUpcomingBookings($username);
    }

    // new: completed bookings (Checked Out)
    public function displayCompletedBookings() {
        return $this->booking->getCompletedBookings();
    }

    public function displayUserCompletedBookings($username) {
        return $this->booking->getUserCompletedBookings($username);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $boardingController = new BoardingController();
    switch ($_POST['action']) {
        case 'checkIn':
            $boardingController->checkIn();
            break;
        case 'checkOut':
            $boardingController->checkOut();
            break;
        default:
            // Optional: handle unknown action
            break;
    }
}
