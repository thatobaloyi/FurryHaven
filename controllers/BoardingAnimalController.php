<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
    include_once __DIR__ . '/../models/BoardingAnimals.php';
    include_once __DIR__ . '/../core/functions.php';

    class BoardingAnimalController
    {
        private $animal;

        public function __construct()
        {
            $this->animal = new BoardingAnimals();
        }

        public function create()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->animal->setBoardingAnimalID($this->animal->generateID());
                $this->animal->setOwnerID(sanitizeInput($_POST['owner_id']));
                $this->animal->setName(sanitizeInput($_POST['name']));
                $this->animal->setBreed(sanitizeInput($_POST['breed']));
                $this->animal->setAgeGroup(sanitizeInput($_POST['age_group']));
                $this->animal->setAnimalType(sanitizeInput($_POST['animal_type']));

                // Handle image upload
                $imagePath = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                    $targetFile = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                        $imagePath = 'uploads/' . $fileName; // Save relative path
                    }
                }
                $this->animal->setBoardAnimalPhoto($imagePath);

                $this->animal->setEmergencyFirstName(sanitizeInput($_POST['emergency_contact_fname']));
                $this->animal->setEmergencyLastName(sanitizeInput($_POST['emergency_contact_lname']));
                $this->animal->setEmergencyPhone(sanitizeInput($_POST['emergency_contact_phone']));
                $this->animal->setEmergencyEmail(sanitizeInput($_POST['emergency_contact_email']));
                $this->animal->setPrimaryVetName(sanitizeInput($_POST['primary_vet_name']));
                $this->animal->setPrimaryVetPhone(sanitizeInput($_POST['primary_vet_phone']));
                $this->animal->setMedicalConditions(sanitizeInput($_POST['medical_conditions']));
                $this->animal->setBehaviouralNotes(sanitizeInput($_POST['behavioural_notes']));
                $this->animal->setAllergies(sanitizeInput($_POST['allergies']));
                $this->animal->setDietaryRequirements(sanitizeInput($_POST['dietary_requirements']));
                $this->animal->setIsDeleted(0);

                if ($this->animal->createBoardingAnimal()) {
                    $_SESSION['notification'] = ['message' => 'Boarding animal added successfully.', 'type' => 'success'];
                    redirectTo("../userAnimal.php");
                } else {
                    $_SESSION['notification'] = ['message' => 'Failed to add boarding animal.', 'type' => 'error'];
                    redirectTo("../userAnimal.php");
                }
            }
        }

        public function update()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $animalID = sanitizeInput($_POST['boardAnimalID']);

                // Set all properties from the form
                $this->animal->setName(sanitizeInput($_POST['name']));
                $this->animal->setBreed(sanitizeInput($_POST['breed']));
                $this->animal->setAgeGroup(sanitizeInput($_POST['age_group']));
                $this->animal->setAnimalType(sanitizeInput($_POST['animal_type']));
                $this->animal->setEmergencyFirstName(sanitizeInput($_POST['emergency_contact_fname']));
                $this->animal->setEmergencyLastName(sanitizeInput($_POST['emergency_contact_lname']));
                $this->animal->setEmergencyPhone(sanitizeInput($_POST['emergency_contact_phone']));
                $this->animal->setEmergencyEmail(sanitizeInput($_POST['emergency_contact_email']));
                $this->animal->setPrimaryVetName(sanitizeInput($_POST['primary_vet_name']));
                $this->animal->setPrimaryVetPhone(sanitizeInput($_POST['primary_vet_phone']));
                $this->animal->setMedicalConditions(sanitizeInput($_POST['medical_conditions']));
                $this->animal->setBehaviouralNotes(sanitizeInput($_POST['behavioural_notes']));
                $this->animal->setAllergies(sanitizeInput($_POST['allergies']));
                $this->animal->setDietaryRequirements(sanitizeInput($_POST['dietary_requirements']));

                // Handle image upload (optional)
                $imagePath = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                    $targetFile = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                        $imagePath = 'uploads/' . $fileName; // Save relative path
                    }
                }

                // If a new image was uploaded, update the photo
                if ($imagePath !== '') {
                    // Update the photo in the DB
                    $conn = $this->animal->getDb();
                    $stmt = $conn->prepare("UPDATE boarding_animals SET board_animal_photo = ? WHERE boardAnimalID = ?");
                    $stmt->bind_param("ss", $imagePath, $animalID);
                    $stmt->execute();
                }

                if ($this->animal->update($animalID)) {
                    $_SESSION['notification'] = ['message' => 'Animal details updated successfully.', 'type' => 'success'];
                } else {
                    $_SESSION['notification'] = ['message' => 'Failed to update animal details.', 'type' => 'error'];
                }
                redirectTo("../userAnimal.php");
            }
        }

        public function delete()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $animalID = sanitizeInput($_POST['boardAnimalID']);

                if ($this->animal->softDelete($animalID)) {
                    $_SESSION['notification'] = ['message' => 'Animal profile deleted successfully.', 'type' => 'success'];
                } else {
                    $_SESSION['notification'] = ['message' => 'Failed to delete animal profile.', 'type' => 'error'];
                }
                redirectTo("../userAnimal.php");
            }
        }


        public function showAllAnimals($ownerID)
        {
            $animals = [];
            $conn = $this->animal->getDb(); // Use a getter for $db
            $stmt = $conn->prepare("SELECT * FROM boarding_animals WHERE ownerID = ? AND isDeleted = 0");
            $stmt->bind_param("s", $ownerID);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $animals[] = $row;
            }
            return $animals;
        }

    }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $boardingAnimalController = new BoardingAnimalController();
    switch ($_POST['action']) {
        case 'create':
            $boardingAnimalController->create();
            break;
        case 'update':
            $boardingAnimalController->update();
            break;
        case 'delete':
            $boardingAnimalController->delete();
            break;
        default:
            // Optional: handle unknown action
            break;
    }
}
