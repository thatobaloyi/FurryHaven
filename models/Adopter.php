<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class Adopter
{
    private $db;
    private $table = "adoption";

    // Adopter Properties
    public $adopterID;
    public $FirstName;
    public $LastName;
    public $NationalID;
    public $DateOfBirth;
    public $Email;
    public $PhoneNumber;
    public $AddressLine1;
    public $City;
    public $Province;
    public $PostalCode;
    public $Country;
    public $HousingType;
    public $HomeOwnershipStatus;
    public $HasFencedYard;
    public $HasOtherPets;
    public $NumberOfPets;
    public $AllergicHousehold;
    public $WhyAdopt;
    public $ExperienceWithAnimals;
    public $AgreedToTerms;
    public $ConsentDate;
    public $homeInspected;
    public $inspectionDate;
    public $inspectionResult;
    public $inspectorNotes;
    public $inspectedBy;
    public $passportNumber;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // Getters
    public function getAdopterID()
    {
        return $this->adopterID;
    }

    public function getFirstName()
    {
        return $this->FirstName;
    }

    public function getLastName()
    {
        return $this->LastName;
    }

    public function getNationalID()
    {
        return $this->NationalID;
    }

    public function getDateOfBirth()
    {
        return $this->DateOfBirth;
    }

    public function getEmail()
    {
        return $this->Email;
    }

    public function getPhoneNumber()
    {
        return $this->PhoneNumber;
    }

    public function getAddressLine1()
    {
        return $this->AddressLine1;
    }

    public function getCity()
    {
        return $this->City;
    }

    public function getProvince()
    {
        return $this->Province;
    }

    public function getPostalCode()
    {
        return $this->PostalCode;
    }

    public function getCountry()
    {
        return $this->Country;
    }

    public function getHousingType()
    {
        return $this->HousingType;
    }

    public function getHomeOwnershipStatus()
    {
        return $this->HomeOwnershipStatus;
    }

    public function getHasFencedYard()
    {
        return $this->HasFencedYard;
    }

    public function getHasOtherPets()
    {
        return $this->HasOtherPets;
    }

    public function getNumberOfPets()
    {
        return $this->NumberOfPets;
    }

    public function getAllergicHousehold()
    {
        return $this->AllergicHousehold;
    }

    public function getWhyAdopt()
    {
        return $this->WhyAdopt;
    }

    public function getExperienceWithAnimals()
    {
        return $this->ExperienceWithAnimals;
    }

    public function getAgreedToTerms()
    {
        return $this->AgreedToTerms;
    }

    public function getConsentDate()
    {
        return $this->ConsentDate;
    }

    public function getHomeInspected()
    {
        return $this->homeInspected;
    }

    public function getInspectionDate()
    {
        return $this->inspectionDate;
    }

    public function getInspectionResult()
    {
        return $this->inspectionResult;
    }

    public function getInspectorNotes()
    {
        return $this->inspectorNotes;
    }

    public function getInspectedBy()
    {
        return $this->inspectedBy;
    }

    public function getPassportNumber()
    {
        return $this->passportNumber;
    }

    // Setters
    public function setAdopterID($adopterID)
    {
        $this->adopterID = $adopterID;
    }

    public function setFirstName($FirstName)
    {
        $this->FirstName = $FirstName;
    }

    public function setLastName($LastName)
    {
        $this->LastName = $LastName;
    }

    public function setNationalID($NationalID)
    {
        $this->NationalID = $NationalID;
    }

    public function setDateOfBirth($DateOfBirth)
    {
        $this->DateOfBirth = $DateOfBirth;
    }

    public function setEmail($Email)
    {
        $this->Email = $Email;
    }

    public function setPhoneNumber($PhoneNumber)
    {
        $this->PhoneNumber = $PhoneNumber;
    }

    public function setAddressLine1($AddressLine1)
    {
        $this->AddressLine1 = $AddressLine1;
    }

    public function setCity($City)
    {
        $this->City = $City;
    }

    public function setProvince($Province)
    {
        $this->Province = $Province;
    }

    public function setPostalCode($PostalCode)
    {
        $this->PostalCode = $PostalCode;
    }

    public function setCountry($Country)
    {
        $this->Country = $Country;
    }

    public function setHousingType($HousingType)
    {
        $this->HousingType = $HousingType;
    }

    public function setHomeOwnershipStatus($HomeOwnershipStatus)
    {
        $this->HomeOwnershipStatus = $HomeOwnershipStatus;
    }

    public function setHasFencedYard($HasFencedYard)
    {
        $this->HasFencedYard = $HasFencedYard;
    }

    public function setHasOtherPets($HasOtherPets)
    {
        $this->HasOtherPets = $HasOtherPets;
    }

    public function setNumberOfPets($NumberOfPets)
    {
        $this->NumberOfPets = $NumberOfPets;
    }

    public function setAllergicHousehold($AllergicHousehold)
    {
        $this->AllergicHousehold = $AllergicHousehold;
    }

    public function setWhyAdopt($WhyAdopt)
    {
        $this->WhyAdopt = $WhyAdopt;
    }

    public function setExperienceWithAnimals($ExperienceWithAnimals)
    {
        $this->ExperienceWithAnimals = $ExperienceWithAnimals;
    }

    public function setAgreedToTerms($AgreedToTerms)
    {
        $this->AgreedToTerms = $AgreedToTerms;
    }

    public function setConsentDate($ConsentDate)
    {
        $this->ConsentDate = $ConsentDate;
    }

    public function setHomeInspected($homeInspected)
    {
        $this->homeInspected = $homeInspected;
    }

    public function setInspectionDate($inspectionDate)
    {
        $this->inspectionDate = $inspectionDate;
    }

    public function setInspectionResult($inspectionResult)
    {
        $this->inspectionResult = $inspectionResult;
    }

    public function setInspectorNotes($inspectorNotes)
    {
        $this->inspectorNotes = $inspectorNotes;
    }

    public function setInspectedBy($inspectedBy)
    {
        $this->inspectedBy = $inspectedBy;
    }

    public function setPassportNumber($passportNumber)
    {
        $this->passportNumber = $passportNumber;
    }

    // CRUD Methods
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " (adopterID, FirstName, LastName, NationalID, DateOfBirth, Email, PhoneNumber, AddressLine1, City, Province, PostalCode, Country, HousingType, HomeOwnershipStatus, HasFencedYard, HasOtherPets, NumberOfPets, AllergicHousehold, WhyAdopt, ExperienceWithAnimals, AgreedToTerms, ConsentDate, homeInspected, inspectionDate, inspectionResult, inspectorNotes, inspectedBy, passportNumber) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssssssssssssssiisssssssssss", $this->adopterID, $this->FirstName, $this->LastName, $this->NationalID, $this->DateOfBirth, $this->Email, $this->PhoneNumber, $this->AddressLine1, $this->City, $this->Province, $this->PostalCode, $this->Country, $this->HousingType, $this->HomeOwnershipStatus, $this->HasFencedYard, $this->HasOtherPets, $this->NumberOfPets, $this->AllergicHousehold, $this->WhyAdopt, $this->ExperienceWithAnimals, $this->AgreedToTerms, $this->ConsentDate, $this->homeInspected, $this->inspectionDate, $this->inspectionResult, $this->inspectorNotes, $this->inspectedBy, $this->passportNumber);
        return $stmt->execute();
    }

    public function readAll()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE adopterID = ? LIMIT 0,1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $this->adopterID);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . " SET FirstName = ?, LastName = ?, NationalID = ?, DateOfBirth = ?, Email = ?, PhoneNumber = ?, AddressLine1 = ?, City = ?, Province = ?, PostalCode = ?, Country = ?, HousingType = ?, HomeOwnershipStatus = ?, HasFencedYard = ?, HasOtherPets = ?, NumberOfPets = ?, AllergicHousehold = ?, WhyAdopt = ?, ExperienceWithAnimals = ?, AgreedToTerms = ?, ConsentDate = ?, homeInspected = ?, inspectionDate = ?, inspectionResult = ?, inspectorNotes = ?, inspectedBy = ?, passportNumber = ? WHERE adopterID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssssssssssssssiisssssssssss", $this->FirstName, $this->LastName, $this->NationalID, $this->DateOfBirth, $this->Email, $this->PhoneNumber, $this->AddressLine1, $this->City, $this->Province, $this->PostalCode, $this->Country, $this->HousingType, $this->HomeOwnershipStatus, $this->HasFencedYard, $this->HasOtherPets, $this->NumberOfPets, $this->AllergicHousehold, $this->WhyAdopt, $this->ExperienceWithAnimals, $this->AgreedToTerms, $this->ConsentDate, $this->homeInspected, $this->inspectionDate, $this->inspectionResult, $this->inspectorNotes, $this->inspectedBy, $this->passportNumber, $this->adopterID);
        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table . " WHERE adopterID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $this->adopterID);
        return $stmt->execute();
    }

    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(adopterID, 5) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'ADR-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }
}
