<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class VolunteerActivity
{
    private $db;

    private $table = "volunteeractivity";
    private $ActivityID;
    private $VolunteerID;
    private $AnimalID;
    private $ActivityType;
    private $Date;
    private $Duration;
    private $AssignedBy;
    private $isDeleted;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // Setters
    public function setActivityID($ActivityID)
    {
        $this->ActivityID = $ActivityID;
    }

    public function setVolunteerID($VolunteerID)
    {
        $this->VolunteerID = $VolunteerID;
    }

    public function setAnimalID($AnimalID)
    {
        $this->AnimalID = $AnimalID;
    }

    public function setActivityType($ActivityType)
    {
        $this->ActivityType = $ActivityType;
    }

    public function setDate($Date)
    {
        $this->Date = $Date;
    }

    public function setDuration($Duration)
    {
        $this->Duration = $Duration;
    }

    public function setAssignedBy($AssignedBy)
    {
        $this->AssignedBy = $AssignedBy;
    }

    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    // Getters
    public function getActivityID()
    {
        return $this->ActivityID;
    }

    public function getVolunteerID()
    {
        return $this->VolunteerID;
    }

    public function getAnimalID()
    {
        return $this->AnimalID;
    }

    public function getActivityType()
    {
        return $this->ActivityType;
    }

    public function getDate()
    {
        return $this->Date;
    }

    public function getDuration()
    {
        return $this->Duration;
    }

    public function getAssignedBy()
    {
        return $this->AssignedBy;
    }

    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    // CRUD Operations
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " (ActivityID, VolunteerID, AnimalID, ActivityType, Date, Duration, AssignedBy) VALUES (?,?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssssss", $this->ActivityID, $this->VolunteerID, $this->AnimalID, $this->ActivityType, $this->Date, $this->Duration, $this->AssignedBy);
        return $stmt->execute();
    }

    public function findOne($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE ActivityID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }



    public function findByUsername($username)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE VolunteerID = ? AND isDeleted = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function findAll()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE isDeleted = 0";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function update($id)
    {
        $query = "UPDATE " . $this->table . " SET VolunteerID = ?, AnimalID = ?, ActivityType = ?, Date = ?, Duration = ?, AssignedBy = ? WHERE ActivityID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssssss", $this->VolunteerID, $this->AnimalID, $this->ActivityType, $this->Date, $this->Duration, $this->AssignedBy, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE ActivityID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }


    public function getStats($id){
        $hoursQuery = "SELECT count(*) FROM $this->table WHERE VolunteerID = ?";
        $stmt = $this->db->prepare($hoursQuery);
        $stmt->bind_param("s", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
    public function softDelete($id)
    {
        $query = "UPDATE " . $this->table . " SET isDeleted = 1 WHERE ActivityID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        if(!$stmt->execute()){
            throw new Exception($stmt->error);
        }
        return true;
    }

    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(ActivityID, 13) AS UNSIGNED)) as max_id FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'VOLACTIVITY-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }
}
