<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include_once __DIR__ . '/../models/Kennel.php';
include_once __DIR__ . '/../controllers/CageController.php';
include_once __DIR__ . '/../config/databaseconnection.php';
include_once __DIR__ . '/../core/functions.php';

class KennelController
{
    private $kennel;

    public function __construct()
    {
        $this->kennel = new Kennel();
    }

    public function index(){
        include __DIR__ . '/../kennel2.php';
    }

    public function create()
    {
        global $conn;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->kennel->setName(sanitizeInput($_POST['kennel_name']));
            $this->kennel->setAddress(sanitizeInput($_POST['kennel_address']));
            $this->kennel->setCapacity(sanitizeInput($_POST['kennel_capacity']));
            $this->kennel->setOccupancy(sanitizeInput($_POST['kennel_occupancy']));
            $this->kennel->setContactDetails(sanitizeInput($_POST['kennel_contact_details']));
            $this->kennel->setType(sanitizeInput($_POST['kennel_type']));
            $this->kennel->setFullCapacity(sanitizeInput($_POST['full_capacity']));
            $this->kennel->setId($this->kennel->generateID());

            if ($this->kennel->create()) {
                $cageController = new CageController();
                $startingCageNumber = 0;
                $kennelId = $this->kennel->getId();
                $capacity = $this->kennel->getCapacity();
                while ($startingCageNumber < $capacity) {
                    $cageController->createForKennel($kennelId, 0, null);
                    $startingCageNumber++;
                }
                $_SESSION['notification'] = [
                    'message' => "Kennel created successfully!",
                    'type' => 'success'
                ];
                redirectTo("../kennel2.php");
            } else {
                $_SESSION['notification'] = [
                    'message' => "Failed to create kennel.",
                    'type' => 'error'
                ];
                redirectTo("../kennel2.php");
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $kennelController = new KennelController();
    switch ($_POST['action']) {
        case 'create':
            $kennelController->create();
            break;
        default:
            // Optional: handle unknown action
            break;
    }
}
