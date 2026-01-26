<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class Foster
{
    private $table = 'foster';
    private $conn;

    private $fosterID;
    private $animalID;

    private $fosterer;
    private $duration;
    private $status;
    private $startDate;
    private $endDate;
    private $screenedBy;
    private $approvedBy;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // Getters
    public function getFosterID()
    {
        return $this->fosterID;
    }

    public function getAnimalID()
    {
        return $this->animalID;
    }


    public function getStatus()
    {
        return $this->status;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getScreenedBy()
    {
        return $this->screenedBy;
    }

    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    // Setters
    public function setFosterID($fosterID)
    {
        $this->fosterID = $fosterID;
    }

    public function setAnimalID($animalID)
    {
        $this->animalID = $animalID;
    }

    public function setFosterer($name){
        $this->fosterer=$name;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    public function setScreenedBy($screenedBy)
    {
        $this->screenedBy = $screenedBy;
    }

    public function setDuration($duration){
        $this->duration=$duration;
    }

    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
    }

    // CRUD Methods

    // Create
    public function createFoster()
    {
        $query = "INSERT INTO " . $this->table . " (fosterID, animalID, fosterer, approvedBy, fosterDuration) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssss", $this->fosterID, $this->animalID, $this->fosterer, $this->approvedBy, $this->duration);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read
    public function getFosterById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE fosterID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function getFosterByAnimalId($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE animalID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function readAll()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Update
    public function updateFoster()
    {
        $query = "UPDATE " . $this->table . " SET animalID = ?, fosterFirstName = ?, fosterLastName = ?, fosterStreetAddress = ?, fosterPhone = ?, fosterEmail = ?, status = ?, startDate = ?, endDate = ?, screenedBy = ?, approvedBy = ? WHERE fosterID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssssssssi", $this->animalID, $this->fosterFirstName, $this->fosterLastName, $this->fosterStreetAddress, $this->fosterPhone, $this->fosterEmail, $this->status, $this->startDate, $this->endDate, $this->screenedBy, $this->approvedBy, $this->fosterID);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete
    public function deleteFoster($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE fosterID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(fosterID, 5) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'FOS-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }
}

