<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include_once __DIR__ . '/../models/Volunteeractivity.php';
include_once __DIR__ . '/../config/databaseconnection.php';
include_once __DIR__ . '/../core/functions.php';

class VolunteerController
{
    private $volunteer;

    public function __construct()
    {
        $this->volunteer = new VolunteerActivity();
    }

    public function index(){
        include __DIR__ . '/../volunteeractvity.php';
    }

    public function create()
    {
        global $conn;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->volunteer->setId($this->volunteer->generateID());
            $this->volunteer->setVolunteerID(sanitizeInput($_POST['VolunteerID']));
            $this->volunteer->setAnimalID(sanitizeInput($_POST['AnimalID']));
            $this->volunteer->setActivityType(sanitizeInput($_POST['ActivtyType']));
            $this->volunteer->setDate(sanitizeInput($_POST['Date']));
            $this->volunteer->setDuration(sanitizeInput($_POST['Duration']));
            $this->volunteer->setAssignedBy(sanitizeInput($_SESSION['AssignedBy']));

            if ($this->volunteer->create()) {
                $_SESSION['notification'] = [
                    'message' => "Campaign created successfully!",
                    'type' => 'success'
                ];
                redirectTo("../campaigns.php");
            } else {
                $_SESSION['notification'] = [
                    'message' => "Failed to create campaign.",
                    'type' => 'error'
                ];
                redirectTo("../campaigns.php");
            }
        }
    }

    public function restore(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $id = sanitizeInput($_POST['CampignID']);
            if($this->campaign->restore($id)){
                $_SESSION['notification'] = [
                    'message' => "Report Restored",
                    'type' => 'success'
                ];
                redirectTo("../campaigns.php");
            } else {
                $_SESSION['notification'] = [
                    'message' => "Error Restoring Report",
                    'type' => 'error'
                ];
                redirectTo("../campaigns.php");
            }
        }
    }

    public function delete(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $id = sanitizeInput($_POST['CampaignID']);
            if($this->campaign->delete($id)){
                $_SESSION['notification'] = [
                    'message' => "Campaign Deleted",
                    'type' => 'success'
                ];
                redirectTo("../campaigns.php");
            } else {
                $_SESSION['notification'] = [
                    'message' => "Error Deleting Campaign",
                    'type' => 'error'
                ];
                redirectTo("../campaigns.php");
            }
        }
    }

    public function softDelete(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $id = sanitizeInput($_POST['CampaignID']);
            if($this->campaign->softDelete($id)){
                $_SESSION['notification'] = [
                    'message' => "Campaign Deleted",
                    'type' => 'success'
                ];
                redirectTo("../campaigns.php");
            } else {
                $_SESSION['notification'] = [
                    'message' => "Error Deleting Campaign",
                    'type' => 'error'
                ];
                redirectTo("../campaigns.php");
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $campaignController = new CampaignController();
    switch ($_POST['action']) {
        case 'create':
            $campaignController->create();
            break;
        case 'delete':
            $campaignController->delete();
            break;
        case 'softDelete':
            $campaignController->softDelete();
            break;
        case 'restore':
            $campaignController->restore();
            break;
        default:
            // Optional: handle unknown action
            break;
    }
}