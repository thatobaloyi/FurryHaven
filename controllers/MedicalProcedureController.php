<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include_once __DIR__ . '/../models/MedicalProcedure.php';
include_once __DIR__ . '/../core/functions.php';

class MedicalProcedureController
{
    private $medicalProcedure;

    public function __construct()
    {
        $this->medicalProcedure = new MedicalProcedure();
    }

    public function index()
    {
      include __DIR__ . "/../dashboard2.php";

    }
    
    public function addMedicalProcedure()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->medicalProcedure->setMedicalID($this->medicalProcedure->generateID());
            $this->medicalProcedure->setAnimalID(sanitizeInput($_POST['Animal_ID']));
            $this->medicalProcedure->setVetID(sanitizeInput($_SESSION['vet_username']));
            $this->medicalProcedure->setProcedureType(sanitizeInput($_POST['procedureType']));
            $this->medicalProcedure->setProcedureOutcome(sanitizeInput($_POST['procedureOutcome']));
            $this->medicalProcedure->setProcedureDate(sanitizeInput($_POST['procedureDate']));
            $this->medicalProcedure->setDetails(sanitizeInput($_POST['details']));

            if ($this->medicalProcedure->create()) {
                $_SESSION['notification'] = [
                    'message' => "Medical procedure added successfully!",
                    'type' => 'success'
                ];
                redirectTo("../dashboard2.php");
            } else {
                $_SESSION['notification'] = [
                    'message' => "Failed to add medical procedure.",
                    'type' => 'error'
                ];
                redirectTo("../dashboard2.php");
            }
        }
    }

    public function updateMedicalProcedure()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $medicalID = sanitizeInput($_POST['medicalID']);
            $this->medicalProcedure->setProcedureOutcome(sanitizeInput($_POST['procedureOutcome']));
            $this->medicalProcedure->setDetails(sanitizeInput($_POST['details']));

            if ($this->medicalProcedure->update($medicalID)) {
                $_SESSION['notification'] = [
                    'message' => "Medical procedure updated successfully!",
                    'type' => 'success'
                ];
                redirectTo("../vet_history.php");

            } else {
                $_SESSION['notification'] = [
                    'message' => "Failed to update medical procedure.",
                    'type' => 'error'
                ];
                 redirectTo("../vet_history.php");
            }
        }
    }

    public function updateMedicalProcedureBatch()
    {
       
       
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $success = true;
            foreach ($_POST['medicalID'] as $medicalID){
                $this->medicalProcedure->setProcedureOutcome(sanitizeInput($_POST['procedureOutcome'][$medicalID]));
                $this->medicalProcedure->setDetails(sanitizeInput($_POST['details'][$medicalID]));

                try{
                    if($this->medicalProcedure->update($medicalID)){
                        
                    }
                }catch (Exception $e){
                    throw new Exception($e);
                }
            }

            if ($this->medicalProcedure->update($medicalID)) {
                $_SESSION['notification'] = [
                    'message' => "Medical procedure updated successfully!",
                    'type' => 'success'
                ];
                redirectTo("../vet_history.php");

            } else {
                $_SESSION['notification'] = [
                    'message' => "Failed to update medical procedure.",
                    'type' => 'error'
                ];
                redirectTo("../vet_history.php");
            }
        }
    }
    public function deleteMedicalProcedure()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $medicalID = sanitizeInput($_POST['medicalID']);

            if ($this->medicalProcedure->delete($medicalID)) {
                $_SESSION['notification'] = [
                    'message' => "Medical procedure deleted successfully!",
                    'type' => 'success'
                ];
                redirectTo("../vet_history.php");

            } else {
                $_SESSION['notification'] = [
                    'message' => "Failed to update medical procedure.",
                    'type' => 'error'
                ];
                redirectTo("../vet_history.php");
            }
        }
    }
    public function getProceduresForCalendar()
    {
        header('Content-Type: application/json');
        $vetID = $_SESSION['vet_username'] ?? null;
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;

        $result = $this->medicalProcedure->getNumberOfProceduresForVetForRange($vetID, $start, $end);

        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = [
                'title' => $row['num_procedures'] . ' procedures',
                'start' => $row['day'],
                'allDay' => true
            ];
        }
        echo json_encode($events);
        exit;
    }
    public function getProceduresForDay()
    {
        header('Content-Type: application/json');
        $vetID = $_SESSION['vet_username'] ?? null;
        $date = $_GET['date'] ?? null;

        $result = $this->medicalProcedure->getProceduresForVetForDay($vetID, $date);

        $procedures = [];
        while ($row = $result->fetch_assoc()) {
            $procedures[] = [
                'time' => date('H:i', strtotime($row['procedureDate'])),
                'procedureType' => $row['procedureType'],
                'animalName' => $row['animalName'],
                'status' => $row['procedureOutcome']
            ];
        }
        echo json_encode($procedures);
        exit;
    }
}

if (isset($_REQUEST['action'])) {
    $medicalProcedureController = new MedicalProcedureController();
    switch ($_REQUEST['action']) {
        case 'add':
            $medicalProcedureController->addMedicalProcedure();
            break;
        case 'update':
            $medicalProcedureController->updateMedicalProcedure();
            break;
        case 'delete':
            $medicalProcedureController->deleteMedicalProcedure();
            break;
        case 'updateBatch':
            $medicalProcedureController->updateMedicalProcedureBatch();
            break;
        case 'getProceduresForCalendar':
            $medicalProcedureController->getProceduresForCalendar();
            break;
        case 'getProceduresForDay':
            $medicalProcedureController->getProceduresForDay();
            break;
        default:
            // Optional: handle unknown action
            break;
    }
}