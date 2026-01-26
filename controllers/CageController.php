<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include_once __DIR__ . '/../models/Cage.php';
include_once __DIR__ . '/../config/databaseconnection.php';
include_once __DIR__ . '/../core/functions.php';

class CageController
{
    private $cage;

    public function __construct()
    {
        $this->cage = new Cage();
    }

    public function index()
    {
        include __DIR__ . '/../create_cage.php';
    }

    public function create()
    {
        global $conn;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->cage->setKennelId(sanitizeInput($_POST['kennel_id']));
            $this->cage->setOccupancyStatus(sanitizeInput($_POST['occupancy_status']));
            $this->cage->setId($this->cage->generateID());

            if ($this->cage->create()) {
                $_SESSION['notification'] = [
                    'message' => "Cage created successfully!",
                    'type' => 'success'
                ];
                // redirectTo("../create_cage.php");
            } else {
                $_SESSION['notification'] = [
                    'message' => "Failed to create cage.",
                    'type' => 'error'
                ];
                // redirectTo("../create_cage.php");
            }
        }
    }

    public function createForKennel($kennelId, $occupancyStatus = 0, $assignedBy = null)
    {
        $this->cage->setKennelId(sanitizeInput($kennelId));
        $this->cage->setOccupancyStatus($occupancyStatus);
        $this->cage->setAssignedBy($assignedBy);
        $this->cage->setId($this->cage->generateID());
        return $this->cage->create();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $cageController = new CageController();
    switch ($_POST['action']) {
        case 'create':
            $cageController->create();
            break;
        default:
            // Optional: handle unknown action
            break;
    }
}
