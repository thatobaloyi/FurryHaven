<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../models/CrueltyReport.php';
include_once __DIR__ . '/../models/Notification.php';
include_once __DIR__ . '/./NotificationController.php';
include_once __DIR__ . '/../core/functions.php';


class CrueltyReportController
{
    private $crueltyReport;
    private $notificationController;

    public function __construct()
    {
        $this->crueltyReport = new CrueltyReport();
        $this->notificationController = new NotificationController();
    }

    public function index()
    {
        include __DIR__ . '/../reportCruelty2.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            global $conn;
            $conn->begin_transaction();
            try {
                $this->crueltyReport->setCrueltyID();
                $this->crueltyReport->setReportDate(date('Y-m-d H:i:s')); // Let the backend handle this for accuracy
                $this->crueltyReport->setDescription(sanitizeInput($_POST['description']));
                $this->crueltyReport->setEvidence(isset($_POST['evidence']) ? sanitizeInput($_POST['evidence']) : null); // Note: sanitizing might affect file paths if you are uploading files.
                $this->crueltyReport->setAnimalStreetAddress(sanitizeInput($_POST['street_address']));
                $this->crueltyReport->setAnimalCity(sanitizeInput($_POST['city']));
                $this->crueltyReport->setReporterFirstName(isset($_POST['firstname']) ? sanitizeInput($_POST['firstname']) : null);
                $this->crueltyReport->setReporterLastName(isset($_POST['lastname']) ? sanitizeInput($_POST['lastname']) : null);
                $this->crueltyReport->setAssignedTo(isset($_POST['assignedTo']) ? sanitizeInput($_POST['assignedTo']) : null);
                $this->crueltyReport->setStatus(isset($_POST['status']) ? sanitizeInput($_POST['status']) : 'Open');
                $this->crueltyReport->setReporterEmail(isset($_POST['email']) ? sanitizeInput($_POST['email']) : null);
                $this->crueltyReport->setReporterPhone(isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : null);
                $this->crueltyReport->setAnimalType(sanitizeInput($_POST['animal_type']));
                $this->crueltyReport->setIncidentType(sanitizeInput($_POST['incident_type']));

                if (isset($_FILES['evidence'])) {
                    // redirectTo("/test");
                    // Check for upload errors for the current file
                    if ($_FILES['evidence']['error'] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['evidence']['tmp_name'];
                        $file_name = $_FILES['evidence']['name'];
                        $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                        // Generate a unique name to prevent conflicts
                        $uniqueFileName = $this->crueltyReport->getCrueltyID() . '_' . uniqid() . '.' . $imageFileType;
                        $targetDir = __DIR__ . "/../images/reports/";
                        $targetFile = $targetDir . $uniqueFileName;

                        if (move_uploaded_file($tmp_name, $targetFile)) {
                            $this->crueltyReport->setEvidence($uniqueFileName);
                        } else {
                            echo "Error: Failed to move uploaded file. Check your `images/reports` folder permissions.";
                        }
                    }
                }


                if ($this->crueltyReport->create()) {
                    if (!$this->notificationController->createCrueltyNotification($this->crueltyReport->getCrueltyID())) {
                        throw new Exception("Could not create a notification for the report");
                    }
                    $_SESSION['notification'] = [
                        'message' => "Report Filed Successfully!",
                        'type' => 'success'
                    ];
                    $conn->commit();
                    redirectTo("../cruelty.php");
                } else {
                    throw new Exception("Counlt not create a cruelty report!.");
                }
            } catch (Exception $e) {
                $_SESSION['notification'] = [
                    'message' => $e->getMessage(),
                    'type' => 'error'
                ];
                $conn->rollback();
                
                redirectTo("../cruelty.php");
            }
        }
    }

    public function restore()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = sanitizeInput($_POST['crueltyID']);
            if ($this->crueltyReport->restore($id)) {
                $_SESSION['notification'] = [
                    'message' => "Report Restored",
                    'type' => 'success'
                ];
                redirectTo("../reportcruelty2.php");
            } else {
                $_SESSION['notification'] = [
                    'message' => "Error Restoring Report",
                    'type' => 'error'
                ];
                redirectTo("../reportcruelty2.php");
            }
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = sanitizeInput($_POST['crueltyID']);
            if ($this->crueltyReport->delete($id)) {
                $_SESSION['notification'] = [
                    'message' => "Report Deleted",
                    'type' => 'success'
                ];
                redirectTo("../reportcruelty2.php");
            } else {
                $_SESSION['notification'] = [
                    'message' => "Error Deleting Report",
                    'type' => 'error'
                ];
                redirectTo("../reportcruelty2.php");
            }
        }
    }

    public function softDelete()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = sanitizeInput($_POST['crueltyID']);
            if ($this->crueltyReport->softDelete($id)) {
                $_SESSION['notification'] = [
                    'message' => "Report Deleted",
                    'type' => 'success'
                ];
                redirectTo("../reportcruelty2.php");
            } else {
                $_SESSION['notification'] = [
                    'message' => "Error Deleting Report",
                    'type' => 'error'
                ];
                redirectTo("../reportcruelty2.php");
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $crueltyReportController = new CrueltyReportController();
    switch ($_POST['action']) {
        case 'create':
            $crueltyReportController->create();
            break;
        case 'delete':
            $crueltyReportController->delete();
            break;
        case 'softDelete':
            $crueltyReportController->softDelete();
            break;
        case 'restore':
            $crueltyReportController->restore();
            break;
        default:
            // Optional: handle unknown action
            break;
    }
}
