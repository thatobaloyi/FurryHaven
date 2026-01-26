<?php

include_once __DIR__ . '/../config/databaseconnection.php';
class Animal
{

    private $db;

    private $table = "animal";
    private $joinTable = "animalmedia";

    private $medicals = "medicalprocedure";
    private $joinTable2 = "kennel";
    private $id;
    private $name;
    private $type;
    private $breed;
    private $gender;
    private $ageGroup;
    private $healthStatus;
    public $isSpayNeutered;
    private $vaccinationStatus;
    private $rescueDate;
    private $animalRescueLocation;
    private $cageID;
    private $intakeType;
    private $outTakeType;
    private $registeredBy;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // setters

    public function setIntakeType($intakeType)
    {
        $this->intakeType = $intakeType;
    }
    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function setBreed(string $breed)
    {
        $this->breed = $breed;
    }

    public function setID(string $id)
    {
        $this->id = $id;
    }

    public function setAgeGroup(string $ageGroup)
    {
        $this->ageGroup = $ageGroup;
    }

    public function setHealthStatus(string $healthStatus)
    {

        $this->healthStatus = $healthStatus;
    }

    public function setSpayNeutered($num)
    {
        $this->isSpayNeutered = $num;
    }

    public function setGender(string $gender)
    {
        $this->gender = $gender;
    }

    public function setVaccinationStatus(string $vaccinationStatus)
    {
        $this->vaccinationStatus = $vaccinationStatus;
    }

    public function setRescueDate(string $rescueDate)
    {
        $this->rescueDate = $rescueDate;
    }

    public function setAnimalRescueLocation(string $animalRescueLocation)
    {
        $this->animalRescueLocation = $animalRescueLocation;
    }

    public function setRescueLocation(string $rescueLocation)
    {
        $this->animalRescueLocation = $rescueLocation;
    }

    public function setcageID($cageID)
    {
        $this->cageID = $cageID;
    }

    public function setregisteredBy(string $name)
    {
        $this->registeredBy = $name;
    }

    public function setOutTakeType($ot)
    {
        $this->outTakeType = $ot;
    }


    //getters
    public function getID()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getBreed()
    {
        return $this->breed;
    }

    public function getAgeGroup()
    {
        return $this->ageGroup;
    }

    public function getHealthStatus()
    {
        return $this->healthStatus;
    }

    public function getSpayNeutered()
    {
        return $this->isSpayNeutered;
    }

    public function getVaccinationStatus()
    {
        return $this->vaccinationStatus;
    }

    public function getRescueDate()
    {
        return $this->rescueDate;
    }

    public function getAnimalRescueLocation()
    {
        return $this->animalRescueLocation;
    }

    public function getRescueLocation()
    {
        return $this->animalRescueLocation;
    }

    public function getcageID()
    {
        return $this->cageID;
    }

    public function getregisteredBy()
    {
        return $this->registeredBy;
    }


    public function create()
    {
        $query = "INSERT INTO $this->table (Animal_ID, Animal_Name, Animal_Type, Animal_Breed, Animal_Gender, Animal_AgeGroup, Animal_HealthStatus, IsSpayNeutered, Animal_Vacc_Status, Animal_RescueDate, Animal_RescueLocation, CageID, RegisteredBy, intakeType) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssssssssssss", $this->id, $this->name, $this->type, $this->breed, $this->gender, $this->ageGroup, $this->healthStatus, $this->isSpayNeutered, $this->vaccinationStatus, $this->rescueDate, $this->animalRescueLocation, $this->cageID, $this->registeredBy, $this->intakeType);
        return $stmt->execute();
    }

    public function findAll($limit = null)
    {
        $query = "SELECT a.*, b.filePath 
              FROM animal a 
              LEFT JOIN (
                  SELECT animalID, MIN(filePath) AS filePath
                  FROM animalmedia
                  GROUP BY animalID
              ) b ON a.Animal_ID = b.animalID 
              WHERE a.isDeleted = 0";

        if ($limit !== null && is_int($limit)) {
            $query .= " LIMIT ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $limit);
        } else {
            $stmt = $this->db->prepare($query);
        }

        $stmt->execute();
        return $stmt->get_result();
    }
    public function findDeleted($limit = null)
    {
        $query = "SELECT a.*, b.filePath 
              FROM animal a 
              LEFT JOIN (
                  SELECT animalID, MIN(filePath) AS filePath
                  FROM animalmedia
                  GROUP BY animalID
              ) b ON a.Animal_ID = b.animalID 
              WHERE a.isDeleted = 1";

        if ($limit !== null && is_int($limit)) {
            $query .= " LIMIT ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $limit);
        } else {
            $stmt = $this->db->prepare($query);
        }

        $stmt->execute();
        return $stmt->get_result();
    }

    public function findAdoptable($limit = null)
    {
        $query = "SELECT a.*, b.filePath
              FROM animal a 
              LEFT JOIN (
                  SELECT animalID, MIN(filePath) AS filePath
                  FROM animalmedia
                  GROUP BY animalID
              ) b ON a.Animal_ID = b.animalID LEFT JOIN medicalprocedure m on a.Animal_ID = m.animalID
              WHERE a.isDeleted = 0 and m.procedureType in ('Vaccination') and m.procedureOutcome = 'Successful' group by a.Animal_ID";

        if ($limit !== null && is_int($limit)) {
            $query .= " LIMIT ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $limit);
        } else {
            $stmt = $this->db->prepare($query);
        }

        $stmt->execute();
        return $stmt->get_result();
    }


    public function findOne($id)
    {
        $query = "SELECT * FROM $this->table a LEFT Join $this->joinTable b on a.Animal_ID = b.animalID WHERE Animal_ID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id)
    {
        $query = "UPDATE $this->table SET Animal_Name = ?, Animal_Type = ?, Animal_Breed = ?, Animal_Gender = ?, Animal_AgeGroup = ?, Animal_HealthStatus = ?, IsSpayNeutered = ?, Animal_Vacc_Status = ?, Animal_RescueLocation = ?, CageID = ?, RegisteredBy = ?, intakeType = ?, outtakeType = ? WHERE Animal_ID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssssisssssss", $this->name,$this->type,$this->breed,$this->gender, $this->ageGroup,$this->healthStatus, $this->isSpayNeutered, $this->vaccinationStatus, $this->animalRescueLocation, $this->cageID, $this->registeredBy, $this->intakeType, $this->outTakeType, $id);
        return $stmt->execute();
    }

    public function filter($filter)
    {
        $query = "SELECT * FROM $this->table a LEFT Join $this->joinTable b on a.Animal_ID = b.animalID WHERE Animal_AgeGroup = '$filter'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function search($search)
    {
        $query = "SELECT * FROM $this->table a LEFT Join $this->joinTable b on a.Animal_ID = b.animalID WHERE Animal_Name LIKE '%$search%'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function searchAndFilter($search, $filter)
    {
        $query = "SELECT * FROM $this->table a LEFT Join $this->joinTable b on a.Animal_ID = b.animalID WHERE Animal_Name LIKE '%$search%' AND Animal_AgeGroup = '$filter'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }


    public function delete($id)
    {
        $query = "DELETE FROM $this->table  WHERE Animal_ID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function softdelete(string $id)
    {
        $query = "UPDATE $this->table SET isDeleted = 1 WHERE Animal_ID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function restore(string $id)
    {
        $query = "UPDATE $this->table SET isDeleted = 0 WHERE Animal_ID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(Animal_ID, 4) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'AN-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }

    public function getTotalAnimals()
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }

   

    public function getAdoptedAnimals()
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE outtakeType='Adoption'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }

    public function getAvailableAnimals()
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE outtakeType IS NULL";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }

    public function getMonthlyIntakes()
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE MONTH(Animal_RescueDate) = MONTH(CURDATE()) AND YEAR(Animal_RescueDate) = YEAR(CURDATE())";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }

    public function getMonthlyAdoptions($currentMonth, $currentYear)
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE outtakeType='Adoption' AND MONTH(outtakeDate)=? AND YEAR(outtakeDate)=?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $currentMonth, $currentYear);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }

    public function getHealthyAnimals()
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE Animal_HealthStatus='Healthy'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }

    public function getInTreatmentAnimals()
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE Animal_HealthStatus IN ('Sick','Injured','Recovering','Under Observation')";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }
}
