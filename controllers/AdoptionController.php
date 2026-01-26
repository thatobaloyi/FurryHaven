<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include_once __DIR__ . '/../models/Adopter.php';
include_once __DIR__ . '/../models/Adoption.php';
include_once __DIR__ . '/../config/databaseconnection.php';
include_once __DIR__ . '/../core/functions.php';

class AdoptionController
{
    private $adopter;
    private $adoption;

    public function __construct()
    {
        $this->adopter = new Adopter();
        $this->adoption = new Adoption();
    }

    public function index()
    {
        include __DIR__ . '/../adopt.php';
    }

    public function create()
    {
        global $conn;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adopter->setAdopterID($this->adopter->generateID());
            $this->adopter->setFirstName(sanitizeInput($_POST['first_name']));
            $this->adopter->setLastName(sanitizeInput($_POST['last_name']));
            $this->adopter->setNationalID(isset($_POST['nationalID']) ? sanitizeInput($_POST['nationalID']) : null);
            $this->adopter->setPassportNumber(isset($_POST['passportNumber']) ? sanitizeInput($_POST['passportNumber']) : null);
            $this->adopter->setDateOfBirth(sanitizeInput($_POST['dob']));
            $this->adopter->setEmail(sanitizeInput($_POST['adoptEmail']));
            $this->adopter->setPhoneNumber(sanitizeInput($_POST['phoneNumber']));
            $this->adopter->setAddressLine1(sanitizeInput($_POST['addressLine1']));
            $this->adopter->setCity(sanitizeInput($_POST['city']));
            $this->adopter->setProvince(sanitizeInput($_POST['province']));
            $this->adopter->setPostalCode(sanitizeInput($_POST['postalCode']));
            $this->adopter->setCountry(sanitizeInput($_POST['country']));
            $this->adopter->setHousingType(sanitizeInput($_POST['homeType']));
            $this->adopter->setHomeOwnershipStatus(sanitizeInput($_POST['homeOwnership']));

            if($_POST['yard'] === "Yes"){
                $this->adopter->setHasFencedYard(1);
            }else{
                $this->adopter->setHasFencedYard(0);
            }

            if($_POST["otherPets"] === "Yes"){
                $this->adopter->setHasOtherPets(1);
            }else{
                $this->adopter->setHasOtherPets(1);
            }
            $this->adopter->setNumberOfPets(isset($_POST['numberOfPets']) ? sanitizeInput($_POST['numberOfPets']) : null);

            if($_POST['allergicHousehold'] === 'Yes'){
                $this->adopter->setAllergicHousehold(1);
            }else{
                $this->adopter->setAllergicHousehold(0);
            }
            // $this->adopter->setAllergicHousehold(sanitizeInput($_POST['allergicHousehold']));
            $this->adopter->setExperienceWithAnimals(sanitizeInput($_POST['experience']));
            $this->adopter->setWhyAdopt(sanitizeInput($_POST['whyAdopt']));
            $this->adopter->setAgreedToTerms(isset($_POST['agreedToTerms']) ? 1 : 0);
            $this->adopter->setConsentDate(date('Y-m-d'));

            $conn->begin_transaction();
            try {
                if (!$this->adopter->create()) {
                    throw new Exception('Failed to create Adopter');
                }

                $adopterId = $this->adopter->getAdopterID();

                $this->adoption->setId($this->adoption->generateID());
                $this->adoption->setAdopterId($adopterId);
                $this->adoption->setAnimalId(sanitizeInput($_POST['animalId']));
                $this->adoption->setAdoptionFee(sanitizeInput($_POST['adoptionFee']));
                $this->adoption->setAdoptionDate(date('Y-m-d'));
                $this->adoption->setAdoptionStatus('pending');
                $this->adoption->setScreeningNotes(isset($_POST['screeningNotes']) ? sanitizeInput($_POST['screeningNotes']) : null);

                // These would likely be set by staff later in the process
                $this->adoption->setFollowUpDate(null);
                $this->adoption->setFollowUpNotes(null);
                $this->adoption->setScreenedBy(null);
                $this->adoption->setApprovedBy(null);

                if (!$this->adoption->create()) {
                    throw new Exception('Failed to create Adoption');
                }

                $conn->commit();
                $_SESSION['notification'] = [
                    'message' => "Adoption Successful!",
                    'type' => 'success'
                ];
                redirectTo("../adopt.php");
            } catch (Exception $e) {
                $conn->rollback();
                $_SESSION['notification'] = [
                    'message' => "Cannot Create An Adoption " . $e->getMessage(),
                    'type' => 'error'
                ];
                 redirectTo("../adopt.php");
            }

            $conn->close();
        }
    }
    public function addFollowUp()
    {
        global $conn;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adopterId = sanitizeInput($_POST['adopter_id']);
            $note = sanitizeInput($_POST['note']);
            $date = date('Y-m-d H:i:s');

            // Find the latest adoption for this adopter
            $adoptionResult = $conn->prepare("SELECT AdoptionID FROM adoption WHERE AdopterID = ? AND isDeleted = 0 ORDER BY AdoptionDate DESC LIMIT 1");
            $adoptionResult->bind_param("s", $adopterId);
            $adoptionResult->execute();
            $adoptionRow = $adoptionResult->get_result()->fetch_assoc();

            if ($adoptionRow) {
                $adoptionId = $adoptionRow['AdoptionID'];
                $update = $conn->prepare("UPDATE adoption SET FollowUpNotes = ? WHERE AdoptionID = ?");
                $update->bind_param("ss", $note, $adoptionId);
                if ($update->execute()) {
                    $_SESSION['notification'] = [
                        'message' => "Follow-up note added successfully!",
                        'type' => 'success'
                    ];
                } else {
                    $_SESSION['notification'] = [
                        'message' => "Failed to add follow-up note.",
                        'type' => 'error'
                    ];
                }
            } else {
                $_SESSION['notification'] = [
                    'message' => "No adoption record found for this adopter.",
                    'type' => 'error'
                ];
            }
            redirectTo("../approvedadopters2.php");
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $adoptionController = new AdoptionController();
    switch ($_POST['action']) {
        case 'create':
            $adoptionController->create();
            break;
        case 'followup':
            $adoptionController->addFollowUp();
            break;
        default:
            // Optional: handle unknown action
            break;
    }
}