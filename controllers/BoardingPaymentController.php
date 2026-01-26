<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include_once __DIR__ . '/../models/BoardingBooking.php';
include_once __DIR__ . '/../models/BoardingPayments.php';
include_once __DIR__ . '/../core/functions.php';

class BoardingPaymentController
{
    private $booking;
    private $payment;

    public function __construct()
    {
        $this->booking = new BoardingBooking();
        $this->payment = new BoardingPayments();
    }

    

    public function process()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectTo("/boarding.php");
        }

        $db = $this->booking->getDb();
        $db->begin_transaction();

        try {
            // Sanitize inputs first
            $animalID = sanitizeInput($_POST['animalID']);
            $startDate = sanitizeInput($_POST['StartDate']);
            $endDate = sanitizeInput($_POST['EndDate']);

            // Check for duplicate booking before proceeding
            if ($this->booking->doesBookingExist($animalID, $startDate, $endDate)) {
                throw new Exception("This animal is already booked for these exact dates or overlapping dates. Please choose different dates.");
            }

            // 1. Create Booking Record
            $this->booking->setBoardBookID($this->booking->generateID());
            $this->booking->setBoardingAnimalID($animalID);
            $this->booking->setBookingStartDate($startDate);
            $this->booking->setBookingEndDate($endDate);
            $this->booking->setStatus('Pending'); // Booking is pending until payment is confirmed
            $this->booking->setIsDeleted(0);

            $availableCages = $this->booking->getAvailableCages($startDate, $endDate);
            if (count($availableCages) <= 0) {
                throw new Exception("No cages available for the selected dates. Please choose different dates.");
            }
            
            $this->booking->setCageNumber($this->booking->assignCage($startDate, $endDate));

            if (!$this->booking->create()) {
                throw new Exception("Failed to create the booking record.");
            }

            // 2. Create Payment Record
            $this->payment->setBoardPaymentID($this->payment->generateID());
            $this->payment->setBookingID($this->booking->getBoardBookID());
            $this->payment->setDailyRate((float) sanitizeInput($_POST['dailyRate']));
            $this->payment->setDaysStayed((int) sanitizeInput($_POST['daysStayed']));
            $this->payment->setPaymentMethod(sanitizeInput($_POST['paymentMethod']));
            $this->payment->setPaymentStatus('pending');
            $this->payment->setIsDeleted(0);

            if (!$this->payment->createBoardingPayment()) {
                throw new Exception("Failed to create the payment record.");
            }

            // 3. If all successful, commit the transaction
            $db->commit();

            $_SESSION['notification'] = ['message' => 'Booking successful! Your request is pending confirmation.', 'type' => 'success'];
            redirectTo("../userAnimal.php");

        } catch (Exception $e) {
            $db->rollback();
            // Store the actual error message in the session to be displayed
            $_SESSION['notification'] = ['message' => 'Booking failed: ' . $e->getMessage(), 'type' => 'error'];
            redirectTo("../boarding.php"); // Redirect back to the form
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $boardingPaymentController = new BoardingPaymentController();
    switch ($_POST['action']) {
        case 'process':
            $boardingPaymentController->process();
            break;
        default:
            // Optional: handle unknown action
            break;
    }
}