<?php
    include_once __DIR__ . '/../config/databaseconnection.php';


    class BoardingAnimals {

        private $db;    
        private $table = 'boarding_animals';

        private $boardingAnimalID;
        private $ownerID;
        private $name;
        private $breed;
        private $ageGroup;
        private $animalType;
        private $board_animal_photo;
        private $emergency_first_name;
        private $emergency_last_name;
        private $emergency_phone;
        private $emergency_email;
        private $primary_vet_name;
        private $primary_vet_phone;
        private $behavioural_notes;
        private $medical_conditions;
        private $allergies;
        private $dietary_requirements;
        private $isDeleted;





        public function __construct()
        {
            global $conn;
            $this->db = $conn;
        }

        // Getters
        public function getBoardingAnimalID() {
            return $this->boardingAnimalID;
        }

        public function getOwnerID() {
            return $this->ownerID;
        }

        public function getName() {
            return $this->name;
        }

        public function getBreed() {
            return $this->breed;
        }

        public function getAgeGroup() {
            return $this->ageGroup;
        }

        public function getAnimalType() {
            return $this->animalType;
        }

        public function getBoardAnimalPhoto() {
            return $this->board_animal_photo;
        }

        public function getEmergencyFirstName() {
            return $this->emergency_first_name;
        }

        public function getEmergencyLastName() {
            return $this->emergency_last_name;
        }

        public function getEmergencyPhone() {
            return $this->emergency_phone;
        }

        public function getEmergencyEmail() {
            return $this->emergency_email;
        }

        public function getPrimaryVetName() {
            return $this->primary_vet_name;
        }

        public function getPrimaryVetPhone() {
            return $this->primary_vet_phone;
        }

        public function getBehaviouralNotes() {
            return $this->behavioural_notes;
        }

        public function getMedicalConditions() {
            return $this->medical_conditions;
        }

        public function getAllergies() {
            return $this->allergies;
        }

        public function getDietaryRequirements() {
            return $this->dietary_requirements;
        }

        public function getIsDeleted() {
            return $this->isDeleted;
        }

        // Setters
        public function setBoardingAnimalID($boardingAnimalID) {
            $this->boardingAnimalID = $boardingAnimalID;
        }

        public function setOwnerID($ownerID) {
            $this->ownerID = $ownerID;
        }

        public function setName($name) {
            $this->name = $name;
        }

        public function setBreed($breed) {
            $this->breed = $breed;
        }

        public function setAgeGroup($ageGroup) {
            $this->ageGroup = $ageGroup;
        }

        public function setAnimalType($animalType) {
            $this->animalType = $animalType;
        }

        public function setBoardAnimalPhoto($board_animal_photo) {
            $this->board_animal_photo = $board_animal_photo;
        }

        public function setEmergencyFirstName($emergency_first_name) {
            $this->emergency_first_name = $emergency_first_name;
        }

        public function setEmergencyLastName($emergency_last_name) {
            $this->emergency_last_name = $emergency_last_name;
        }

        public function setEmergencyPhone($emergency_phone) {
            $this->emergency_phone = $emergency_phone;
        }

        public function setEmergencyEmail($emergency_email) {
            $this->emergency_email = $emergency_email;
        }

        public function setPrimaryVetName($primary_vet_name) {
            $this->primary_vet_name = $primary_vet_name;
        }

        public function setPrimaryVetPhone($primary_vet_phone) {
            $this->primary_vet_phone = $primary_vet_phone;
        }

        public function setBehaviouralNotes($behavioural_notes) {
            $this->behavioural_notes = $behavioural_notes;
        }

        public function setMedicalConditions($medical_conditions) {
            $this->medical_conditions = $medical_conditions;
        }

        public function setAllergies($allergies) {
            $this->allergies = $allergies;
        }

        public function setDietaryRequirements($dietary_requirements) {
            $this->dietary_requirements = $dietary_requirements;
        }

        public function setIsDeleted($isDeleted) {
            $this->isDeleted = $isDeleted;
        }



        public function createBoardingAnimal() {
            $query = "INSERT INTO " . $this->table . " (
                boardAnimalID, ownerID, name, breed, ageGroup, animalType, board_animal_photo, emergency_first_name, emergency_last_name, emergency_phone, emergency_email, primary_vet_name, primary_vet_phone, behavioural_notes, medical_conditions, allergies, dietary_requirements, isDeleted
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($query);

            $stmt->bind_param(
                "sssssssssssssssssi",
                $this->boardingAnimalID,
                $this->ownerID,
                $this->name,
                $this->breed,
                $this->ageGroup,
                $this->animalType,
                $this->board_animal_photo,
                $this->emergency_first_name,
                $this->emergency_last_name,
                $this->emergency_phone,
                $this->emergency_email,
                $this->primary_vet_name,
                $this->primary_vet_phone,
                $this->behavioural_notes,
                $this->medical_conditions,
                $this->allergies,
                $this->dietary_requirements,
                $this->isDeleted
            );

            return $stmt->execute();
        }


        public function update($id) {
            $query = "UPDATE " . $this->table . " SET 
                        name = ?, breed = ?, ageGroup = ?, animalType = ?, 
                        emergency_first_name = ?, emergency_last_name = ?, 
                        emergency_phone = ?, emergency_email = ?, primary_vet_name = ?, 
                        primary_vet_phone = ?, behavioural_notes = ?, medical_conditions = ?, 
                        allergies = ?, dietary_requirements = ? 
                      WHERE boardAnimalID = ?";
            
            $stmt = $this->db->prepare($query);
            
            // Bind parameters
            $stmt->bind_param(
                "sssssssssssssss",
                $this->name,
                $this->breed,
                $this->ageGroup,
                $this->animalType,
                $this->emergency_first_name,
                $this->emergency_last_name,
                $this->emergency_phone,
                $this->emergency_email,
                $this->primary_vet_name,
                $this->primary_vet_phone,
                $this->behavioural_notes,
                $this->medical_conditions,
                $this->allergies,
                $this->dietary_requirements,
                $id
            );

            return $stmt->execute();
        }

        public function softDelete($id) {
            $query = "UPDATE " . $this->table . " SET isDeleted = 1 WHERE boardAnimalID = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('s', $id);
            return $stmt->execute();
        }

        public function generateID() {
            // The prefix 'Board-An' is 8 characters long, so we start extracting the number from position 9.
            $query = "SELECT MAX(CAST(SUBSTRING(boardAnimalID, 9) AS UNSIGNED)) as max_id FROM " . $this->table;
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
            return 'Board-An' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
        }

        public function getDb() {
            return $this->db;
        }


    }


?>