<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class AnimalApplication
{
    private $db;
    private $table = 'animalapplication';

    // Attributes
    private $animalappID;
    private $username;
    private $applicationDate;
    private $IDnumber;
    private $fosterDuration;
    private $passportNumber;
    private $age;
    private $city;
    private $province;
    private $postalCode;
    private $country;
    private $housingType;
    private $homeOwnershipStatus;
    private $permissionFromLandlord;
    private $hasFencedYard;
    private $allergicHousehold;
    private $hasOtherPets;
    private $numberOfPets;
    private $whyFosterOrAdopt;
    private $houseInspectionDate;
    private $houseInspectionNotes;
    private $houseInspectionStatus;
    private $screeningNotes;
    private $applicationStatus;
    private $isDeleted;

    private $status;
    private $applicationType;
    private $animalID;
    private $inspectedBy;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    public function setStatus($status)
    {
        $this->status =  $status;
    }
    // Setters
    public function setAnimalAppID($animalappID)
    {
        $this->animalappID = $animalappID;
    }
    public function setUsername($username)
    {
        $this->username = $username;
    }
    public function setApplicationDate($applicationDate)
    {
        $this->applicationDate = $applicationDate;
    }
    public function setIDnumber($IDnumber)
    {
        $this->IDnumber = $IDnumber;
    }
    public function setFosterDuration($fosterDuration)
    {
        $this->fosterDuration = $fosterDuration;
    }
    public function setPassportNumber($passportNumber)
    {
        $this->passportNumber = $passportNumber;
    }
    public function setAge($age)
    {
        $this->age = $age;
    }
    public function setCity($city)
    {
        $this->city = $city;
    }
    public function setProvince($province)
    {
        $this->province = $province;
    }
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }
    public function setCountry($country)
    {
        $this->country = $country;
    }
    public function setHousingType($housingType)
    {
        $this->housingType = $housingType;
    }
    public function setHomeOwnershipStatus($homeOwnershipStatus)
    {
        $this->homeOwnershipStatus = $homeOwnershipStatus;
    }
    public function setPermissionFromLandlord($permissionFromLandlord)
    {
        $this->permissionFromLandlord = $permissionFromLandlord;
    }
    public function setHasFencedYard($hasFencedYard)
    {
        $this->hasFencedYard = $hasFencedYard;
    }
    public function setAllergicHousehold($allergicHousehold)
    {
        $this->allergicHousehold = $allergicHousehold;
    }
    public function setHasOtherPets($hasOtherPets)
    {
        $this->hasOtherPets = $hasOtherPets;
    }
    public function setNumberOfPets($numberOfPets)
    {
        $this->numberOfPets = $numberOfPets;
    }
    public function setWhyFosterOrAdopt($whyFosterOrAdopt)
    {
        $this->whyFosterOrAdopt = $whyFosterOrAdopt;
    }
    public function setHouseInspectionDate($houseInspectionDate)
    {
        $this->houseInspectionDate = $houseInspectionDate;
    }
    public function setHouseInspectionNotes($houseInspectionNotes)
    {
        $this->houseInspectionNotes = $houseInspectionNotes;
    }
    public function setHouseInspectionStatus($houseInspectionStatus)
    {
        $this->houseInspectionStatus = $houseInspectionStatus;
    }
    public function setScreeningNotes($screeningNotes)
    {
        $this->screeningNotes = $screeningNotes;
    }
    public function setApplicationStatus($applicationStatus)
    {
        $this->applicationStatus = $applicationStatus;
    }
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }
    public function setAnimalID($animalID)
    {
        $this->animalID = $animalID;
    }

    public function setApplicationType($appType)
    {
        $this->applicationType = $appType;
    }
    public function setInspectedBy($inspectedBy)
    {
        $this->inspectedBy = $inspectedBy;
    }

    // Getters
    public function getAnimalAppID()
    {
        return $this->animalappID;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function getApplicationDate()
    {
        return $this->applicationDate;
    }
    public function getIDnumber()
    {
        return $this->IDnumber;
    }
    public function getFosterDuration()
    {
        return $this->fosterDuration;
    }
    public function getPassportNumber()
    {
        return $this->passportNumber;
    }
    public function getAge()
    {
        return $this->age;
    }
    public function getCity()
    {
        return $this->city;
    }
    public function getProvince()
    {
        return $this->province;
    }
    public function getPostalCode()
    {
        return $this->postalCode;
    }
    public function getCountry()
    {
        return $this->country;
    }
    public function getHousingType()
    {
        return $this->housingType;
    }
    public function getHomeOwnershipStatus()
    {
        return $this->homeOwnershipStatus;
    }
    public function getPermissionFromLandlord()
    {
        return $this->permissionFromLandlord;
    }
    public function getHasFencedYard()
    {
        return $this->hasFencedYard;
    }
    public function getAllergicHousehold()
    {
        return $this->allergicHousehold;
    }
    public function getHasOtherPets()
    {
        return $this->hasOtherPets;
    }
    public function getNumberOfPets()
    {
        return $this->numberOfPets;
    }
    public function getWhyFosterOrAdopt()
    {
        return $this->whyFosterOrAdopt;
    }
    public function getHouseInspectionDate()
    {
        return $this->houseInspectionDate;
    }
    public function getHouseInspectionNotes()
    {
        return $this->houseInspectionNotes;
    }
    public function getHouseInspectionStatus()
    {
        return $this->houseInspectionStatus;
    }
    public function getScreeningNotes()
    {
        return $this->screeningNotes;
    }
    public function getApplicationStatus()
    {
        return $this->applicationStatus;
    }
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }
    public function getAnimalID()
    {
        return $this->animalID;
    }
    public function getInspectedBy()
    {
        return $this->inspectedBy;
    }

    // CRUD Methods
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " (animalappID, username, applicationDate, IDnumber, fosterDuration, passportNumber, age, city, province, postalCode, country, housingType, homeOwnershipStatus, permissionFromLandlord, hasFencedYard, allergicHousehold, hasOtherPets, numberOfPets, whyFosterOrAdopt, houseInspectionDate, houseInspectionNotes, houseInspectionStatus, screeningNotes, applicationStatus, isDeleted, animalID, inspectedBy, animalAppType) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssssissssssiiissssssssisss", $this->animalappID, $this->username, $this->applicationDate, $this->IDnumber, $this->fosterDuration, $this->passportNumber, $this->age, $this->city, $this->province, $this->postalCode, $this->country, $this->housingType, $this->homeOwnershipStatus, $this->permissionFromLandlord, $this->hasFencedYard, $this->allergicHousehold, $this->hasOtherPets, $this->numberOfPets, $this->whyFosterOrAdopt, $this->houseInspectionDate, $this->houseInspectionNotes, $this->houseInspectionStatus, $this->screeningNotes, $this->applicationStatus, $this->isDeleted, $this->animalID, $this->inspectedBy, $this->applicationType);
        if(!$stmt->execute()){
            throw new Exception($stmt->error);
        }
        return true;
    }

    public function findOne($username, $animalID)
    {
        $query = "SELECT * FROM $this->table WHERE animalID = ? and username = ? and isDeleted = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $animalID, $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findApplicationsByUsername($username, $animalAppType = null)
    {
        $query = "SELECT * FROM $this->table WHERE username = ? AND isDeleted = 0";

        if ($animalAppType !== null) {
            $query .= " AND animalAppType = ?";
        }
        $stmt = $this->db->prepare($query);

        if ($animalAppType !== null) {

            $stmt->bind_param("ss", $username, $animalAppType);
        } else {

            $stmt->bind_param("s", $username);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    public function updateStatus($id)
    {
        $stmt = $this->db->prepare("UPDATE $this->table SET applicationStatus = ? WHERE animalAppID = ?");
        $stmt->bind_param("ss", $this->status, $id);
        if (!$stmt->execute()) {
           throw new Exception($stmt->error);
        }
        return true;
    }


    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(animalappID, 11) AS UNSIGNED)) as max_id FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'ANIMALAPP-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }
}
