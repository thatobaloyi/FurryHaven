<?php


include_once __DIR__ . '/../config/databaseconnection.php';

class CrueltyReport
{
    private $db;
    private $table = 'crueltyreport';

    // Attributes
    private $crueltyID;
    private $reportDate;
    private $description;
    private $evidence;
    private $animalStreetAddress;
    private $animalCity;
    private $reporterFirstName;
    private $reporterLastName;
    private $assignedTo;
    private $status;
    private $reporterEmail;
    private $reporterPhone;
    private $animalType;
    private $incidentType;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // Setters
    public function setCrueltyID()
    {
        $this->crueltyID = $this->generateID();
    }
    public function setReportDate($reportDate)
    {
        $this->reportDate = $reportDate;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function setEvidence($evidence)
    {
        $this->evidence = $evidence;
    }
    public function setAnimalStreetAddress($animalStreetAddress)
    {
        $this->animalStreetAddress = $animalStreetAddress;
    }
    public function setAnimalCity($animalCity)
    {
        $this->animalCity = $animalCity;
    }
    public function setReporterFirstName($reporterFirstName)
    {
        $this->reporterFirstName = $reporterFirstName;
    }
    public function setReporterLastName($reporterLastName)
    {
        $this->reporterLastName = $reporterLastName;
    }
    public function setAssignedTo($assignedTo)
    {
        $this->assignedTo = $assignedTo;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }
    public function setReporterEmail($reporterEmail)
    {
        $this->reporterEmail = $reporterEmail;
    }
    public function setReporterPhone($reporterPhone)
    {
        $this->reporterPhone = $reporterPhone;
    }
    public function setAnimalType($animalType)
    {
        $this->animalType = $animalType;
    }
    public function setIncidentType($incidentType)
    {
        $this->incidentType = $incidentType;
    }

    // Getters
    public function getCrueltyID()
    {
        return $this->crueltyID;
    }
    public function getReportDate()
    {
        return $this->reportDate;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getEvidence()
    {
        return $this->evidence;
    }
    public function getAnimalStreetAddress()
    {
        return $this->animalStreetAddress;
    }
    public function getAnimalCity()
    {
        return $this->animalCity;
    }
    public function getReporterFirstName()
    {
        return $this->reporterFirstName;
    }
    public function getReporterLastName()
    {
        return $this->reporterLastName;
    }
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getReporterEmail()
    {
        return $this->reporterEmail;
    }
    public function getReporterPhone()
    {
        return $this->reporterPhone;
    }
    public function getAnimalType()
    {
        return $this->animalType;
    }
    public function getIncidentType()
    {
        return $this->incidentType;
    }

    // CRUD Methods
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " (crueltyID, reportDate, description, evidence, animalStreetAddress, animalCity, reporterFirstName, reporterLastName, assignedTo, status, reporterEmail, reporterPhone, animalType, incidentType) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssssssssssss", $this->crueltyID, $this->reportDate, $this->description, $this->evidence, $this->animalStreetAddress, $this->animalCity, $this->reporterFirstName, $this->reporterLastName, $this->assignedTo, $this->status, $this->reporterEmail, $this->reporterPhone, $this->animalType, $this->incidentType);
        return $stmt->execute();
    }

    public function findAll()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function findOne($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE crueltyID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id)
    {
        $query = "UPDATE " . $this->table . " SET reportDate = ?, description = ?, evidence = ?, animalStreetAddress = ?, animalCity = ?, reporterFirstName = ?, reporterLastName = ?, assignedTo = ?, status = ?, reporterEmail = ?, reporterPhone = ?, animalType = ?, incidentType = ? WHERE crueltyID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssssssssssss", $this->reportDate, $this->description, $this->evidence, $this->animalStreetAddress, $this->animalCity, $this->reporterFirstName, $this->reporterLastName, $this->assignedTo, $this->status, $this->reporterEmail, $this->reporterPhone, $this->animalType, $this->incidentType, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE crueltyID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function softDelete($id)
    {
        $query = "UPDATE " . $this->table . " SET isDeleted = 1 WHERE crueltyID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }


    public function restore(string $id)
    {
        $query = "UPDATE $this->table SET isDeleted = 0 WHERE crueltyID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(crueltyID, 7) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'CRUEL-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }
} ?>