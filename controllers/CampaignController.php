<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include_once __DIR__ . '/../models/Campaign.php';
include_once __DIR__ . '/../config/databaseconnection.php';
include_once __DIR__ . '/../core/functions.php';

class CampaignController
{
    private $campaign;

    public function __construct()
    {
        $this->campaign = new Campaign();
    }

    public function index(){
        include __DIR__ . '/../campaigns.php';
    }

    public function create()
    {
        global $conn;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->campaign->setId($this->campaign->generateID());
            $this->campaign->setName(sanitizeInput($_POST['campaign_name']));
            $this->campaign->setDescription(sanitizeInput($_POST['campaign_description']));
            $this->campaign->setStartDate(sanitizeInput($_POST['campaign_start_date']));
            $this->campaign->setEndDate(sanitizeInput($_POST['campaign_end_date']));
            $this->campaign->setGoalAmount(sanitizeInput($_POST['target_amount']));
            $this->campaign->setCreatedBy(sanitizeInput($_SESSION['admin_username']));

            if ($this->campaign->create()) {
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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getAllCampaigns') {
    $campaign = new Campaign();
    $result = $campaign->findAll();
    $campaigns = [];
    while ($row = $result->fetch_assoc()) {
        $campaigns[] = [
            'CampaignID' => $row['CampaignID'],
            'CampaignName' => $row['CampaignName']
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($campaigns);
    exit;
}