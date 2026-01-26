<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../models/Animal.php';
include_once __DIR__ . '/../models/AnimalMedia.php';
include_once __DIR__ . '/../models/Cage.php';
include_once __DIR__ . '/../models/Kennel.php';
include_once __DIR__ . '/../core/functions.php';

class AnimalController
{
    private $animal;
    private $cage;
    private $kennel;

    private $animalmedia;

    public function __construct()
    {
        $this->animal = new Animal();
        $this->cage = new Cage();
        $this->kennel = new Kennel();
        $this->animalmedia = new AnimalMedia();
    }

    public function showAnimals()
    {
        include __DIR__ . '/..../animaldatabase2.php';
    }


    public function showAnimalDetails()
    {
        if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['details'])) {
            $row = $this->animal->findOne($_GET['details']);
            include __DIR__ . '/../showAnimalDetails.php';
            // exit();
        }
    }

    public function index()
    {
        $filter = '';
        $search = '';

        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            $filter = isset($_GET["filter"]) ? sanitizeInput($_GET["filter"]) : "";
            $search = isset($_GET["search"]) ? sanitizeInput($_GET["search"]) : "";
            if (!empty($filter) && !empty($search)) {
                $result = $this->animal->searchAndFilter($search, $filter);
            } else if (!empty($filter) && empty($search)) {
                $result = $this->animal->filter($filter);
                // redirectTo("/");
            } else if (empty($filter) && !empty($search)) {
                $result = $this->animal->search($search);
                // redirectTo("/");
            } else {
                $result = $this->animal->findAll(5);
            }
        }

        return [$result, $search, $filter];
        // include __DIR__ . '/../admindisplay.php';
    }

    public function showRegistrationForm()
    {

        $availableCages = null;
        if (isset($_GET['animalType']) && !empty($_GET['animalType'])) {
            $animalType = $_GET['animalType'];

            $availableCages = $this->cage->findByAnimalType($animalType); // This method from a previous response is perfect
        }

        include __DIR__ . '/../registration2.php';
    }
    public function showUpdateForm()
    {
        $animalID = $_POST['animalId'];

        $result = $this->animal->findOne($animalID);

        // var_dump($result);

        include __DIR__ . '/../animalupdate.php';
    }

    public function processRegistration()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $foundCage = $this->cage->findOne($_POST['cageId']);
            //var_dump($_GET);
            // Set animal properties from the form data
            $this->animal->setID($this->animal->generateID());
            $this->animal->setName(sanitizeInput($_POST['animalName']));
            $this->animal->setType(sanitizeInput($_POST['animalType']));
            $this->animal->setAgeGroup(sanitizeInput($_POST['age']));
            $this->animal->setGender(sanitizeInput($_POST['gender']));
            $this->animal->setBreed(sanitizeInput($_POST['breed']));
            $this->animal->setRescueDate(sanitizeInput($_POST['rescueDate']));

            $location = sanitizeInput($_POST['rescueStreet']) . ', ' . sanitizeInput($_POST['rescueCity']) . ', ' . sanitizeInput($_POST['rescueProvince']) . ', ' . sanitizeInput($_POST['rescuePostal']);

            $this->animal->setRescueLocation($location);
            $this->animal->setHealthStatus(sanitizeInput($_POST['healthStatus']));
            $this->animal->setVaccinationStatus(sanitizeInput($_POST['vaccinationStatus']));
            $this->animal->setIntakeType(sanitizeInput($_POST['intakeType']));


            if ($_POST['isSpayed'] == "Yes") {
                $this->animal->setSpayNeutered(1);
            } else {
                $this->animal->setSpayNeutered(0);
            }

            $this->animal->setcageID(sanitizeInput($foundCage['CageID']));
            $this->animal->setregisteredBy(sanitizeInput($_SESSION['admin_username']));


            if ($this->animal->create()) { // remember to update the kennel and cage tables
                $this->cage->updateOccupancy($this->animal->getId(),  $foundCage['CageID'], 1, $_SESSION['admin_username']);
                $this->kennel->incrementOccupancy($foundCage['Kennel_ID']);

                if (isset($_FILES['animalImages'])) {
                    // redirectTo("/test");
                    $totalFiles = count($_FILES['animalImages']['name']);
                    for ($i = 0; $i < $totalFiles; $i++) {
                        // Check for upload errors for the current file
                        if ($_FILES['animalImages']['error'][$i] === UPLOAD_ERR_OK) {
                            $tmp_name = $_FILES['animalImages']['tmp_name'][$i];
                            $file_name = $_FILES['animalImages']['name'][$i];
                            $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                            // Generate a unique name to prevent conflicts
                            $uniqueFileName = $this->animal->getId() . '_' . uniqid() . '.' . $imageFileType;
                            $targetDir = __DIR__ . "/../images/animals/";
                            $targetFile = $targetDir . $uniqueFileName;

                            if (move_uploaded_file($tmp_name, $targetFile)) { // this adds the path of the image to the table
                                $mediaID = $this->animalmedia->generateID();
                                if ($this->animalmedia->create($mediaID, $this->animal->getID(), 'image', $uniqueFileName, 'default caption', $_SESSION['admin_username'])) {
                                    echo "the create worked somehow";
                                } else {
                                    echo "can't create media";
                                }
                            } else {
                                echo "Error: Failed to move uploaded file. Check your `images/animals` folder permissions.";
                            }
                        }
                    }
                }

                $_SESSION['notification'] = [
                    'message' => "Animal Registered Succesfully",
                    'type' => 'success'
                ];
                redirectTo("../animaldatabase2.php");
            } else {
                $_SESSION['notification'] = [
                    'message' => "Failed To Register Animal",
                    'type' => 'error'
                ];
                redirectTo("../animaldatabase2.php");
            }
        }
    }



    public function processUpdate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $animalId = $_POST['Animal_ID'];
            $newCageId = $_POST['CageID'];

            // Get the original animal data to find the old cage
            $originalAnimalData = $this->animal->findOne($animalId);
            $oldCageId = $originalAnimalData['CageID'];

            // If the cage has been changed, update occupancy
            if ($oldCageId !== $newCageId) {
                // Decrement occupancy of the old kennel
                $oldCageData = $this->cage->findOne($oldCageId);
                if ($oldCageData) {
                    $this->kennel->decrementOccupancy($oldCageData['Kennel_ID']);
                    $this->cage->updateOccupancy(null, $oldCageId, 0, $_SESSION['admin_username']);
                }

                // Increment occupancy of the new kennel
                $newCageData = $this->cage->findOne($newCageId);
                if ($newCageData) {
                    $this->kennel->incrementOccupancy($newCageData['Kennel_ID']);
                    $this->cage->updateOccupancy($animalId, $newCageId, 1, $_SESSION['admin_username']);
                }
            }

            $this->animal->setID($animalId);
            $this->animal->setName(sanitizeInput($_POST['Animal_Name']));
            $this->animal->setType(sanitizeInput($_POST['Animal_Type']));
            $this->animal->setAgeGroup(sanitizeInput($_POST['Animal_AgeGroup']));
            $this->animal->setGender(sanitizeInput($_POST['Animal_Gender']));
            $this->animal->setBreed(sanitizeInput($_POST['Animal_Breed']));
            $this->animal->setRescueDate(sanitizeInput($_POST['Animal_RescueDate']));

            $this->animal->setRescueLocation($_POST['Animal_RescueLocation']);
            $this->animal->setHealthStatus(sanitizeInput($_POST['Animal_HealthStatus']));
            $this->animal->setVaccinationStatus(sanitizeInput($_POST['Animal_Vacc_Status']));
            $this->animal->setIntakeType(sanitizeInput($_POST['intakeType']));
            $this->animal->setOutTakeType(isset($_POST['outtakeType']) && !empty($_POST['outtakeType']) ? sanitizeInput($_POST['outtakeType']) : null);

            if ($_POST['IsSpayNeutered'] == "Yes") {
                $this->animal->setSpayNeutered(1);
            } else {
                $this->animal->setSpayNeutered(0);
            }

            $this->animal->setcageID(sanitizeInput($newCageId));
            $this->animal->setregisteredBy(sanitizeInput($_POST['RegisteredBy']));

            if ($this->animal->update($this->animal->getID())) {
                $_SESSION['notification'] = [
                    'message' => "Animal Profile Updated Succesfully",
                    'type' => 'success'
                ];
                if (isset($_SERVER['HTTP_REFERER'])) {
                    // Redirect the user back to the page they came from
                    redirectTo("$_SERVER[HTTP_REFERER]");

                    exit;
                } else {
                    // Fallback: if Referer is not available, redirect to a default page
                    // For example, a dashboard or the form's main page
                    redirectTo("/");

                    exit;
                }
            } else {
                $_SESSION['notification'] = [
                    'message' => "Failed To Update Animal Profile",
                    'type' => 'error'
                ];
            }
        }
    }

    // public function animalsFilterSearch(){
    //     $animal = new Animal();

    //     $filter = isset($_POST["filter"]) ? sanitizeInput($_POST["filter"]) :"";
    //     $search = isset($_POST["filter"]) ? sanitizeInput($_POST["filter"]) :"";

    //     $result = $animal->filterSearch($search, $filter);

    //     include __DIR__ . '/../admindisplay.php';
    //     redirectTo('../animaldatabase2.php');
    // }
    public function animalDelete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $animalId = sanitizeInput($_POST['Animal_ID']);
            $animalData = $this->animal->findOne($animalId);
            // echo "mew mew";
            if ($animalData) {
                $cageId = $animalData['CageID'];
                $cageData = $this->cage->findOne($cageId);

                if ($cageData) {
                    $this->cage->updateOccupancy(null, $cageId, 0, $_SESSION['admin_username']);
                    $this->kennel->decrementOccupancy($cageData['Kennel_ID']);
                }
            }


            // var_dump($animal);
            if ($this->animal->delete($animalId)) {
                $_SESSION['notification'] = [
                    'message' => "Animal Deleted Succesfully",
                    'type' => 'success'
                ];
                redirectTo("../deleted_records.php");
            } else {
                $_SESSION['notification'] = [
                    'message' => "Failed To Delete Animal",
                    'type' => 'error'
                ];
                redirectTo("../deleted_records.php");
            }
        }
    }

    public function animalSoftDelete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $animalId = sanitizeInput($_POST['Animal_ID']);
            // var_dump($animal);
            if ($this->animal->softdelete($animalId)) {
                $_SESSION['notification'] = [
                    'message' => "Animal Deleted Succesfully",
                    'type' => 'success'
                ];
                redirectTo("../animaldatabase2.php");
            } else {
                $_SESSION['notification'] = [
                    'message' => "Failed To Delete Animal",
                    'type' => 'error'
                ];
                redirectTo("../animaldatabase2.php");
            }
        }
    }

    public function animalRestore()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $animalId = sanitizeInput($_POST['Animal_ID']);
            // var_dump($animal);
            if ($this->animal->restore($animalId)) {
                $_SESSION['notification'] = [
                    'message' => "Animal Restored Succesfully",
                    'type' => 'success'
                ];
                redirectTo("../animaldatabase2.php");
            } else {
                $_SESSION['notification'] = [
                    'message' => "Failed To Restore Animal",
                    'type' => 'error'
                ];
                redirectTo("../animaldatabase2.php");
            }
        }
    }

    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (isset($_FILES['animalImages'])) {
                // redirectTo("/test");
                $totalFiles = count($_FILES['animalImages']['name']);
                for ($i = 0; $i < $totalFiles; $i++) {
                    // Check for upload errors for the current file
                    if ($_FILES['animalImages']['error'][$i] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['animalImages']['tmp_name'][$i];
                        $file_name = $_FILES['animalImages']['name'][$i];
                        $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                        // Generate a unique name to prevent conflicts
                        $uniqueFileName = $_POST['animalID'] . '_' . uniqid() . '.' . $imageFileType;
                        $targetDir = __DIR__ . "/../images/animals/";
                        $targetFile = $targetDir . $uniqueFileName;

                        if (move_uploaded_file($tmp_name, $targetFile)) { // this adds the path of the image to the table
                            $mediaID = $this->animalmedia->generateID();
                            if ($this->animalmedia->create($mediaID, $_POST['animalID'], 'image', $uniqueFileName, 'default caption', $_SESSION['admin_username'])) {
                                $_SESSION['notification'] = [
                                    'message' => "Image Succesfully Added",
                                    'type' => 'success'
                                ];
                                redirectTo('../animaldatabase2.php');
                            } else {
                                echo "can't create media";
                            }
                        } else {
                            echo "Error: Failed to move uploaded file. Check your `images/animals` folder permissions.";
                        }
                    }
                }
            }
        }
    }

    public function deleteImage()
    {
                $animalID = $_POST['animalID'];
                $image_filename = $_POST['image_filename'];

                // Instantiate model
                $media = new AnimalMedia();

                // 1. Delete the record from the database
                $db_deleted = $media->deleteImageByFilename($animalID, $image_filename);

                if($db_deleted){
                     $_SESSION['notification'] = [
                                    'message' => "Image Successfully Deleted",
                                    'type' => 'success'
                                ];
                                redirectTo('../animaldatabase2.php');
                }

            //     if ($db_deleted) {
            //         // 2. Delete the actual file from the server
            //         $file_path = __DIR__ . '/../images/animals/' . $image_filename;
            //         if (file_exists($file_path) && unlink($file_path)) {
            //             // Success: file and DB record deleted
            //             header("Location: ../animal_details.php?details=" . $animalID . "&status=imageDeleted");
            //             exit;
            //         } else {
            //             // File deletion failed (DB record may still be gone)
            //             // Handle error
            //             header("Location: ../animal_details.php?details=" . $animalID . "&status=fileDeleteError");
            //             exit;
            //         }
            //     } else {
            //         // Database deletion failed
            //         header("Location: ../animal_details.php?details=" . $animalID . "&status=dbDeleteError");
            //         exit;
            //     }
            // }

    }

}


$animal = new AnimalController();


if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'register':
            $animal->processRegistration();
            break;
        case 'update':
            $animal->processUpdate();
            break;
        case 'deleteImage':
            $animal->deleteImage();
            break;
        case 'updateProfile':
            $animal->updateProfile();
            break;
        case 'delete':
            $animal->animalDelete();
            break;
        case 'soft_delete':
            $animal->animalSoftDelete();
            break;
        case 'restore':
            $animal->animalRestore();
            break;
    }
}
