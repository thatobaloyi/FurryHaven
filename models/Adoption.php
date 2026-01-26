<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class Adoption
{
    private $db;
    private $table = 'adoption';

    // Attributes
    private $id; // AdoptionID
    private $animalId; // AnimalID
    private $adopterId; // AdopterID
    private $adoptionFee; // AdoptionFee
    private $adoptionDate; // AdoptionDate
    private $adoptionStatus; // AdoptionStatus
    private $screeningNotes; // ScreeningNotes
    private $followUpDate; // FollowUpDate
    private $followUpNotes; // FollowUpNotes
    private $screenedBy; // ScreenedBy
    private $approvedBy; // ApprovedBy
    private $isDeleted; // isDeleted

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // Setters
    public function setId($id){
        $this->id = $id;
    }
    public function setAnimalId($animalId)
    {
        $this->animalId = $animalId;
    }
    public function setAdopterId($adopterId)
    {
        $this->adopterId = $adopterId;
    }
    public function setAdoptionFee($adoptionFee)
    {
        $this->adoptionFee = $adoptionFee;
    }
    public function setAdoptionDate($adoptionDate)
    {
        $this->adoptionDate = $adoptionDate;
    }
    public function setAdoptionStatus($adoptionStatus)
    {
        $this->adoptionStatus = $adoptionStatus;
    }
    public function setScreeningNotes($screeningNotes)
    {
        $this->screeningNotes = $screeningNotes;
    }
    public function setFollowUpDate($followUpDate)
    {
        $this->followUpDate = $followUpDate;
    }
    public function setFollowUpNotes($followUpNotes)
    {
        $this->followUpNotes = $followUpNotes;
    }
    public function setScreenedBy($screenedBy)
    {
        $this->screenedBy = $screenedBy;
    }
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }
    public function getAnimalId()
    {
        return $this->animalId;
    }
    public function getAdopterId()
    {
        return $this->adopterId;
    }
    public function getAdoptionFee()
    {
        return $this->adoptionFee;
    }
    public function getAdoptionDate()
    {
        return $this->adoptionDate;
    }
    public function getAdoptionStatus()
    {
        return $this->adoptionStatus;
    }
    public function getScreeningNotes()
    {
        return $this->screeningNotes;
    }
    public function getFollowUpDate()
    {
        return $this->followUpDate;
    }
    public function getFollowUpNotes()
    {
        return $this->followUpNotes;
    }
    public function getScreenedBy()
    {
        return $this->screenedBy;
    }
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    // CRUD Methods
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " (AdoptionID, AnimalID, AdopterID, AdoptionDate, ApprovedBy, isDeleted) VALUES (?, ?, ?, ?,?, 0)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssss", $this->id, $this->animalId, $this->adopterId, $this->adoptionDate, $this->approvedBy);
        if (!$stmt->execute()){
            throw new Exception($stmt->error);
        }

        return true;
    }

    public function findAll()
    {
        $query = "SELECT * FROM adoption join users on adoption.AdopterID = users.username"; 
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function findOne($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE AdoptionID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

  

    public function findOneByAnimalID($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE AnimalID = ? AND isDeleted = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function update($id)
    {
        $query = "UPDATE " . $this->table . " SET AnimalID = ?, AdopterID = ?, AdoptionFee = ?, AdoptionDate = ?, AdoptionStatus = ?, ScreeningNotes = ?, FollowUpDate = ?, FollowUpNotes = ?, ScreenedBy = ?, ApprovedBy = ? WHERE AdoptionID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssdsssssssi", $this->animalId, $this->adopterId, $this->adoptionFee, $this->adoptionDate, $this->adoptionStatus, $this->screeningNotes, $this->followUpDate, $this->followUpNotes, $this->screenedBy, $this->approvedBy, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE AdoptionID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(AdoptionID, 10) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'ADOPTION-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }
}
