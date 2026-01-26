<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class Cage
{
    private $db;
    private $table = 'cage';

    private $linkTable = 'kennel';

    // Attributes
    private $id; // CageID
    private $animalId; // Animal_ID
    private $kennelId; // Kennel_ID
    private $occupancyStatus; // Occupancy_Status
    private $assignedBy; // AssignedBy

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
    public function setKennelId($kennelId)
    {
        $this->kennelId = $kennelId;
    }
    public function setOccupancyStatus($occupancyStatus)
    {
        $this->occupancyStatus = $occupancyStatus;
    }
    public function setAssignedBy($assignedBy)
    {
        $this->assignedBy = $assignedBy;
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
    public function getKennelId()
    {
        return $this->kennelId;
    }
    public function getOccupancyStatus()
    {
        return $this->occupancyStatus;
    }
    public function getAssignedBy()
    {
        return $this->assignedBy;
    }

    // CRUD Methods
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " (CageID, Kennel_ID, Occupancy_Status, AssignedBy) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssss", $this->id, $this->kennelId, $this->occupancyStatus, $this->assignedBy);
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
        $query = "SELECT * FROM " . $this->table . " WHERE CageID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function findByAnimalType($type)
    {
        $query = "SELECT * FROM $this->table c join $this->linkTable l on c.Kennel_ID = l.Kennel_ID WHERE l.Kennel_Type = ? and c.Occupancy_Status = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $type);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function update($id)
    {
        $query = "UPDATE " . $this->table . " SET Animal_ID = ?, Kennel_ID = ?, Occupancy_Status = ?, AssignedBy = ? WHERE CageID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssss", $this->animalId, $this->kennelId, $this->occupancyStatus, $this->assignedBy, $id);
        return $stmt->execute();
    }

    public function updateOccupancy($animalID , $CageId, $status, $adminID){
        $query = "UPDATE $this->table SET Animal_ID = ?, Occupancy_Status = ?, AssignedBy = ?  WHERE CageID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("siss",$animalID, $status,$adminID, $CageId);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE CageID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(CageID, 6) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'CAGE-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }


    public function softDelete($id)
    {
        $query = "UPDATE " . $this->table . " SET isDeleted = 1 WHERE CageID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }


    public function findAvailableCagesByType($type)
    {
        $query = "SELECT c.* FROM $this->table c JOIN $this->linkTable k ON c.Kennel_ID = k.Kennel_ID WHERE k.Kennel_Type = ? AND c.Occupancy_Status = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $type);
        $stmt->execute();
        return $stmt->get_result();
    }
}
